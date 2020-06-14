<?php
require_once '../helpers.php';
require_once '../init.php';
require_once '../functions.php';
require_once '../sql-queries.php';

$user_data = $_SESSION['user'];
$author_id = filter_input(INPUT_GET, 'author_id');

$result = create_subscription($link, $author_id, $user_data['id']);

if ($result) {
    $path = $_SERVER['HTTP_REFERER'];
    header("Location: $path");
    die();
}
