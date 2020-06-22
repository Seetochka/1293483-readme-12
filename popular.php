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

date_default_timezone_set('Europe/Moscow');

$user_data = $_SESSION['user'];
$unread_messages_count = get_sql_unread_messages_count($link, $user_data['id']);
$content_types = get_sql_content_types($link);
$active_content_type = filter_input(INPUT_GET, 'content-type', FILTER_VALIDATE_INT);
$index = isset($active_content_type) ? find_index($content_types, 'id', $active_content_type) : '';

if (!isset($index)) {
    header("HTTP/1.0 404 Not Found");
    $error_msg = 'Не удалось выполнить запрос';
    die($error_msg);
}

$active_sorting_type = filter_input(INPUT_GET, 'sorting-type');
$sorting_field = get_sorting_field($active_sorting_type);

$sorting_order = filter_input(INPUT_GET, 'sorting-order') ?? 'desc';

$query_params['author_id IS NULL = ?'] = '1';

if (!empty($active_content_type)) {
    $query_params['content_type_id = ?'] = $active_content_type;
}

$current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
$items_count = get_sql_posts_count($link, $query_params);
$page_items = $items_count <= PAGE_ITEMS_POOR_DB ? PAGE_ITEMS_POOR_DB : PAGE_ITEMS_RICH_DB;
$pages_count = intval(ceil($items_count / $page_items));

if ($current_page <= 0 || $current_page > $pages_count) {
    header("HTTP/1.0 404 Not Found");
    $error_msg = 'Не удалось выполнить запрос';
    die($error_msg);
}

$offset = ($current_page - 1) * $page_items;

$query_params['user_data_id'] = $user_data['id'];
$posts = get_sql_posts_filters($link, $query_params, $sorting_field, $sorting_order, $page_items, $offset);

$page_content = include_template('popular.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'active_content_type' => $active_content_type,
    'active_sorting_type' => $active_sorting_type,
    'sorting_order' => $sorting_order,
    'page' => $current_page,
    'pages_count' => $pages_count,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное',
    'unread_messages_count' => $unread_messages_count,
]);

print ($layout_content);
