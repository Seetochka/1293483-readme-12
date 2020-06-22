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
$search_query = trim($_GET['q']) ?? '';

if (empty($search_query)) {
    $path = $_SERVER['HTTP_REFERER'];
    header("Location: $path");
    die();
}

$posts = get_sql_posts_filters(
    $link,
    ['q' => $search_query, 'user_data_id' => $user_data['id']],
    'dt_add',
    'DESC',
    100
);

foreach ($posts as $key => $post) {
    $posts[$key]['hashtags'] = get_sql_hashtags($link, $post['id']);

    if ($post['author_id']) {
        unset($posts[$key]);
    }
}

$page_content = include_template('search.php', [
    'search_query' => $search_query,
    'posts' => $posts,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: страница результатов поиска',
    'unread_messages_count' => $unread_messages_count,
    'search_query' => $search_query,
]);

print ($layout_content);
