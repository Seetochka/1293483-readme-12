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

$active_content_type = filter_input(INPUT_GET, 'content-type');

$active_sorting_type = filter_input(INPUT_GET, 'sorting-type');
$sorting_field = get_sorting_field($active_sorting_type);

$sorting_order = filter_input(INPUT_GET, 'sorting-order') ?? 'desc';

$query_params = [];

if($active_content_type) {
    $query_params['content_type_id = ?'] =  $active_content_type;
}

$current_page = intval($_GET['page'] ?? 1);
$items_count = get_sql_posts_count($link, $query_params);
$page_items = $items_count <= 9 ? 9 : 6;
$pages_count = intval(ceil($items_count / $page_items));
$offset = ($current_page - 1) * $page_items;

$posts = get_sql_posts_filters($link, $query_params, $sorting_field, $sorting_order, $page_items, $offset);
$content_types = get_sql_content_types($link);

$page_content = include_template('popular.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'content_type_size' => $content_type_size,
    'active_content_type' => $active_content_type,
    'active_sorting_type' => $active_sorting_type,
    'sorting_order' => $sorting_order,
    'page' => $current_page,
    'pages_count' => $pages_count,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное',
]);

print ($layout_content);
