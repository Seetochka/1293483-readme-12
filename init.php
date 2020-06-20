<?php
require_once 'db.php';
require_once 'functions.php';

session_start();

$link = get_db_connection($db);
mysqli_set_charset($link, "utf8");

if (!$link) {
    header("HTTP/1.0 500 Internal Server Error");
    $error_msg = 'Не удалось выполнить подключение к серверу: ' . mysqli_connect_error();
    die($error_msg);
}

$posts = [];
$content_types = [];
