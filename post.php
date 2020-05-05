<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';

$is_auth = rand(0, 1);
$user_name = 'Светлана';
define('MAX_COMMENT_COUNT', 6);

if (!$link) {
    header("HTTP/1.0 500 Internal Server Error");
    $error_msg = 'Не удалось выполнить подключение к серверу: ' . mysqli_connect_error();
    die($error_msg);
}

$post_id = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;

$post = get_sql_post($link, $post_id);

if(!$post) {
    header("HTTP/1.0 404 Not Found");
    $error_msg = 'Не удалось выполнить запрос: ' . mysqli_error($link);
    die($error_msg);
}

$comments = get_sql_comments($link, $post_id);
$comments_count = get_sql_comments_count($link, $post_id);
$user = get_sql_user($link, $post['user_id']);

$post_content = include_template("post-{$post['class_name']}.php", [
    'post' => $post,
]);

$page_content = include_template('post.php', [
    'post_content' => $post_content,
    'post' => $post,
    'comments' => $comments,
    'user' => $user,
    'comments_count' => $comments_count,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'readme: публикация',
]);

print ($layout_content);
