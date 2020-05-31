<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';

if (!isset($_SESSION['user'])) {
    header("Location: /");
    die();
}

$search_query = trim($_GET['q']) ?? '';

if (!$search_query) {
    $path = $_SERVER['HTTP_REFERER'];
    header("Location: $path");
    die();
}

$posts = get_sql_posts_filters($link, ['q' => $search_query], 'dt_add', 'DESC', 100);

$page_content = include_template('search.php', [
    'search_query' => $search_query,
    'posts' => $posts,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: страница результатов поиска',
]);

print ($layout_content);
