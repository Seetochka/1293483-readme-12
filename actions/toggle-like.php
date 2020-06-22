<?php
require_once '../helpers.php';
require_once '../init.php';
require_once '../functions.php';
require_once '../sql-queries.php';

$user_data = $_SESSION['user'];
$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$post = !empty($post_id) ? get_sql_post($link, $post_id, $user_data['id']) : null;

if (empty($post)) {
    $path = $_SERVER['HTTP_REFERER'] ?? '/';
    header("Location: $path");
    die();
}

$result = create_like($link, $post_id, $user_data['id']);

$path = $_SERVER['HTTP_REFERER'];
header("Location: $path");
die();
