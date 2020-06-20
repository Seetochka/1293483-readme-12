<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';
require_once 'constants.php';

if (!isset($_SESSION['user'])) {
    header("Location: /");
    die();
}

$user_data = $_SESSION['user'];
$unread_messages_count = get_sql_unread_messages_count($link, $user_data['id']);
$post_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
$showed_comments_all = filter_input(INPUT_GET, 'comments');

$post = !empty($post_id) ? get_sql_post($link, $post_id, $user_data['id']) : null;

if (empty($post)) {
    header("HTTP/1.0 404 Not Found");
    $error_msg = 'Не удалось выполнить запрос: ' . mysqli_error($link);
    die($error_msg);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    increase_sql_show_count($link, $post_id);
}

$post['repost_count'] = get_sql_repost_count($link, $post['id']);
$comments = get_sql_comments($link, $post_id);
$comments_count = $showed_comments_all === 'all' ? $post['comments_count'] : MAX_COMMENT_COUNT;
$author = get_sql_user($link, $post['user_id']);
$author['is_follower'] = is_follower($link, $author['id'], $user_data['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_comment = remove_space($_POST);
    $rules = [
        'content' => function ($value) {
            return validate_comment($value);
        },
    ];

    $errors = array_filter(validate($new_comment, $rules));

    if (!count($errors)) {
        $result = create_sql_comment($link, $new_comment['content'], $post_id, $user_data['id']);

        if ($result) {
            header('Location: post.php?id=' . $post_id);
            die();
        }

        mysqli_error($link);
    }
}

$post_content = include_template("post/post-{$post['class_name']}.php", [
    'post' => $post,
]);

$page_content = include_template('post.php', [
    'post_content' => $post_content,
    'post' => $post,
    'comments' => $comments,
    'author' => $author,
    'comments_count' => $comments_count,
    'errors' => $errors ?? [],
    'new_comment' => $new_comment ?? [],
    'user_data' => $user_data,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: публикация',
    'unread_messages_count' => $unread_messages_count,
]);

print ($layout_content);
