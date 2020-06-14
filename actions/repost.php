<?php
require_once '../helpers.php';
require_once '../init.php';
require_once '../functions.php';
require_once '../sql-queries.php';

$user_data = $_SESSION['user'];
$post_id = filter_input(INPUT_GET, 'id') ?? null;
$post = get_sql_post($link, $post_id, $user_data['id']);

if (empty($post)) {
    $path = $_SERVER['HTTP_REFERER'];
    header("Location: $path");
    die();
}

if ($post['user_id'] === $user_data['id']) {//не дает сделать репост своего поста, выполняет переадресацию на страницу пользователя
    header('Location: /profile.php?id=' . $user_data['id']);
    die();
}

if (!empty($post['original_id'])) {//если я пытаюсь сделать репост репоста, то делаю репост оригинального поста
    header('Location: /actions/repost.php?id=' . $post['original_id']);
    die();
}

if (is_reposted($link, $post_id, $user_data['id'])) {//не дает сделать повторный репост, выполняет переадресацию на страницу пользователя
    header('Location: /profile.php?id=' . $user_data['id']);
    die();
}

$repost['title'] = $post['title'];
$repost['content_type_id'] = $post['content_type_id'];
$content_type = $post['class_name'];

switch ($content_type) {
    case 'photo':
    case 'video':
    case 'link':
        $repost[$content_type] = $post[$content_type];
        break;
    case 'text':
        $repost['content'] = $post['content'];
        break;
    case 'quote':
        $repost['content'] = $post['content'];
        $repost['quote_author'] = $post['quote_author'];
        break;
}

$repost['user_id'] = $user_data['id'];
$repost['author_id'] = $post['user_id'];
$repost['original_id'] = $post_id;
$repost['hashtag_name'] = implode(' ', get_sql_hashtags($link, $post_id));

$post_id = create_sql_post($link, $content_type, $repost);

if ($post_id) {
    header("Location: /profile.php?id=" . $user_data['id']);
    die();
}
