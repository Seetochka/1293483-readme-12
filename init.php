<?php
require_once 'db.php';

function get_db_connection($db_connection_data) {
    return mysqli_connect($db_connection_data['host'], $db_connection_data['user'], $db_connection_data['password'], $db_connection_data['database']);
}

$link = get_db_connection($db);
mysqli_set_charset($link, "utf8");

$posts = [];
$content_types = [];
