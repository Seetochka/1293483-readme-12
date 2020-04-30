<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';

$is_auth = rand(0, 1);
$user_name = 'Светлана';
define('MAX_COMMENT_COUNT', 6);

if (!$link) {
    print mysqli_connect_error();
} else {
    $post_id = null;

    if (isset($_GET['id'])) {
        $post_id = $_GET['id'];
    }

    $post = get_sql_post($link, $post_id);

    if(!isset($post['id'])) {
        header("HTTP/1.0 404 Not Found");
    }

    $comments = get_sql_comments($link, $post_id);
    $comments_count = count($comments);
    $user = get_sql_user($link, $post_id);
}

$post_content = include_template("post-{$post['class_name']}.php", [
    'post' => $post,
]);

$page_content = include_template('post.php', [
    'post_content' => $post_content,
    'post' => $post,
    'comments' => $comments,
    'user' => $user,
    'MAX_COMMENT_COUNT' => MAX_COMMENT_COUNT,
    'comments_count' => $comments_count,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'readme: публикация',
]);

print ($layout_content);
