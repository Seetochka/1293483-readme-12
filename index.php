<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';
require_once 'constants.php';

date_default_timezone_set('Europe/Moscow');

if (!$link) {
    header("HTTP/1.0 500 Internal Server Error");
    $error_msg = 'Не удалось выполнить подключение к серверу: ' . mysqli_connect_error();
    die($error_msg);
}

$active_content_type = filter_input(INPUT_GET, 'content-type');

$active_sorting_type = filter_input(INPUT_GET, 'sorting-type');
$sorting_field = get_sorting_field($active_sorting_type);

$sorting_order = filter_input(INPUT_GET, 'sorting-order') ?? 'desc';

$query_params = [];

if($active_content_type) {
    $query_params['content_type_id = ?'] =  $active_content_type;
}

$posts = get_sql_posts_filters($link, $query_params, $sorting_field, $sorting_order);
$content_types = get_sql_content_types($link);

$page_content = include_template('main.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'content_type_size' => $content_type_size,
    'active_content_type' => $active_content_type,
    'active_sorting_type' => $active_sorting_type,
    'sorting_order' => $sorting_order,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'readme: популярное',
]);

print ($layout_content);
