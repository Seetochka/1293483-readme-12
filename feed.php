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
$active_content_type = filter_input(INPUT_GET, 'content-type');
$query_params['follower_id = ?'] =  $user_data['id'];

if($active_content_type) {
    $query_params['content_type_id = ?'] =  $active_content_type;
}

$posts = get_sql_posts_filters($link, $query_params, 'dt_add', 'desc', 100);

foreach ($posts as $key => $post) {
    $posts[$key]['repost_count'] = get_sql_repost_count($link, $post['id']);

    if ($posts[$key]['author_id']) {
        $author = get_sql_user($link, $posts[$key]['author_id']);
        $posts[$key]['author_login'] = $author['login'];
        $posts[$key]['author_avatar'] = $author['avatar'];
    }
}

$content_types = get_sql_content_types($link);

$page_content = include_template('feed.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'content_type_size' => $content_type_size,
    'active_content_type' => $active_content_type,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: моя лента',
    'unread_messages_count' => $unread_messages_count,
]);

print ($layout_content);
