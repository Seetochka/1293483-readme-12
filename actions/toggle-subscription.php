<?php
require_once '../helpers.php';
require_once '../init.php';
require_once '../functions.php';
require_once '../sql-queries.php';
require_once '../mail.php';

$user_data = $_SESSION['user'];
$author_id = filter_input(INPUT_GET, 'author_id');

$result = create_subscription($link, $author_id, $user_data['id']);

if (is_follower($link, $author_id, $user_data['id'])) {
    $author = get_sql_user($link, $author_id);
    $follower = get_sql_user($link, $user_data['id']);

    $message = new Swift_Message('У вас новый подписчик');
    $message->setTo([$author['email'] => $author['login']]);
    $message->setBody('Здравствуйте, ' . $author['login'] . '. На вас подписался новый пользователь ' . $follower['login'] . '. Вот ссылка на его профиль: http://1293483-readme-12/profile.php?id=' . $user_data['id']);
    $message->setFrom(['keks@phpdemo.ru' => 'readme']);

    $res = $mailer->send($message);
}

if ($result) {
    $path = $_SERVER['HTTP_REFERER'];
    header("Location: $path");
    die();
}
