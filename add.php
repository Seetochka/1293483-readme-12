<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';
require_once 'constants.php';
require_once 'mail.php';

if (!isset($_SESSION['user'])) {
    header("Location: /");
    die();
}

$user_data = $_SESSION['user'];
$unread_messages_count = get_sql_unread_messages_count($link, $user_data['id']);
$form_title = ['фото', 'видео', 'текста', 'цитаты', 'ссылки'];

$content_types = get_sql_content_types($link);
$active_content_type = strval(filter_input(INPUT_GET, 'content-type') ?? $_POST['content_type_id'] ?? 1);
$index = find_index($content_types, 'id', $active_content_type);

$title_errors = [
    'title' => 'Заголовок',
    'link' => 'Ссылка',
    'content' => $content_types[$index]['class_name'] === 'quote' ? ' Текст цитаты' :'Текст поста',
    'quote_author' => 'Автор',
    'hashtag_name' => 'Теги',
    'photo' => 'Фото',
    'video' => 'Ссылка YouTube',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_to_validate = remove_space($_POST);

    if (!empty($_FILES['photo']['name'])) {
        $post_to_validate['photo'] = $_FILES['photo'];
    }

    $rules = prepare_post_rules($content_types[$index]['class_name']);
    $errors = array_filter(validate($post_to_validate, $rules));

    if (!count($errors)) {
        $post_to_create = remove_space(prepare_post_data($_POST, $content_types[$index]['class_name']));
        $post_id = create_sql_post($link, $content_types[$index]['class_name'], $post_to_create);

        if ($post_id) {
            $followers = get_sql_followers($link, $user_data['id']);

            foreach ($followers as $follower) {
                $message = new Swift_Message('Новая публикация от пользователя ' . $user_data['login']);
                $message->setTo([$follower['email'] => $follower['login']]);
                $message->setBody('Здравствуйте, ' . $follower['login'] . '. Пользователь ' . $user_data['login'] . ' только что опубликовал новую запись «' . $post_to_create['title'] . '». Посмотрите её на странице пользователя: http://1293483-readme-12/profile.php?id=' . $user_data['id']);
                $message->setFrom(['keks@phpdemo.ru' => 'readme']);

                $res = $mailer->send($message);
            }

            header("Location: post.php?id=" . $post_id);
            die();
        }

        mysqli_error($link);
    }
}

$add_post_content = include_template("add-post/add-post-{$content_types[$index]['class_name']}.php", [
    'index' => $index,
    'content_types' => $content_types,
    'post' => $post_to_validate,
    'errors' => $errors ?? [],
    'title_errors' => $title_errors,
]);

$page_content = include_template('add-post.php', [
    'add_post_content' => $add_post_content,
    'content_types' => $content_types,
    'content_type_size' => $content_type_size,
    'active_content_type' => $active_content_type,
    'index' => $index,
    'form_title' => $form_title,
    'post' => $post_to_validate,
    'errors' => $errors ?? [],
    'title_errors' => $title_errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: добавление публикации',
    'unread_messages_count' => $unread_messages_count,
]);

print ($layout_content);
