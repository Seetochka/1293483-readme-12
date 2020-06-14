<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';
require_once 'constants.php';

$user_data = $_SESSION['user'];
$profile_id = filter_input(INPUT_GET, 'id') ?? null;
$profile_data = get_sql_user($link, $profile_id);

if (empty($profile_data)) {
    $path = $_SERVER['HTTP_REFERER'];
    header("Location: $path");
    die();
}

$query_parameter = filter_input(INPUT_GET, 'query-parameter') ?? 'posts';
$showed_comments_post_ids = array_filter(explode('+', filter_input(INPUT_GET, 'comments')));
$profile_data['login'] = implode('<br>', explode(' ', $profile_data['login']));
$profile_data['is_follower'] = is_follower($link, $profile_id, $user_data['id']);

switch ($query_parameter) {
    case 'posts':
        $posts = get_sql_posts_filters($link, ['user_id = ?' => $profile_id, 'user_data_id' => $user_data['id']], 'dt_add', 'DESC', 100);

        foreach ($posts as $key => $post) {
            $posts[$key]['hashtags'] = get_sql_hashtags($link, $post['id']);//получаем хештеги поста
            $posts[$key]['repost_count'] = get_sql_repost_count($link, $post['id']);//получаем число репостов

            if (in_array($post['id'], $showed_comments_post_ids)) {//получаем комментарии поста, если в поисковом запросе есть id этого поста
                $posts[$key]['comments'] = get_sql_comments($link, $post['id']);
            }

            if ($post['author_id']) {//если у поста есть автор, значит это репост и мы получаем по его id логин, аватар и дату публикации оригинального поста
                $author = get_sql_user($link, $post['author_id']);
                $posts[$key]['author_login'] = $author['login'];
                $posts[$key]['author_avatar'] = $author['avatar'];
                $original_post = get_sql_post($link, $post['original_id'], $user_data['id']);
                $posts[$key]['original_post_dt_add'] = $original_post['dt_add'];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_comment = remove_space($_POST);
            $rules = ['content' => function($value) {return validate_comment($value); },];

            $errors = array_filter(validate($new_comment, $rules));

            if (!count($errors)) {
                $result = create_sql_comment($link, $new_comment['content'], $new_comment['post_id'], $user_data['id']);

                if ($result) {
                    header('Location: profile.php?id='. $profile_id . '&comments=' . filter_input(INPUT_GET, 'comments'));
                    die();
                }

                mysqli_error($link);
            }
        }

        $profile_content = include_template('profile/profile-posts.php', [
            'user_data' => $user_data,
            'profile_data' => $profile_data,
            'posts' => $posts,
            'showed_comments_post_ids' => $showed_comments_post_ids,
            'new_comment' => $new_comment,
            'errors' => $errors,
        ]);
        break;
    case 'likes':
        $posts = get_sql_posts_likes($link, $profile_id);

        $profile_content = include_template('profile/profile-likes.php', ['posts' => $posts,]);
        break;
    case 'subscriptions':
        $authors = get_sql_authors($link, $profile_id);

        foreach ($authors as $key => $author) {
            $authors[$key]['is_follower'] = is_follower($link, $author['id'], $user_data['id']);
        }

        $profile_content = include_template('profile/profile-subscriptions.php', [
            'authors' => $authors,
            'user_data' => $user_data,
        ]);
        break;
}

$page_content = include_template('profile.php', [
    'profile_content' => $profile_content,
    'query_parameter' => $query_parameter,
    'user_data' => $user_data,
    'profile_data' => $profile_data,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: профиль',
]);

print ($layout_content);
