<?php
require_once '../helpers.php';
require_once '../init.php';
require_once '../functions.php';
require_once '../sql-queries.php';

$user_data = $_SESSION['user'];
$post_id = filter_input(INPUT_GET, 'id');

$result = create_like($link, $post_id, $user_data['id']);

$path = $_SERVER['HTTP_REFERER'];
header("Location: $path");
die();
