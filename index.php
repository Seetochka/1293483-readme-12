<?php
require_once 'helpers.php';

$is_auth = rand(0, 1);
$user_name = 'Светлана';

$posts = [
    [
        'title' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'user_name' => 'Лариса',
        'avatar' => 'img/userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'user_name' => 'Владик',
        'avatar' => 'img/userpic.jpg'
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'img/rock-medium.jpg',
        'user_name' => 'Виктор',
        'avatar' => 'img/userpic-mark.jpg'
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'img/coast-medium.jpg',
        'user_name' => 'Лариса',
        'avatar' => 'img/userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'user_name' => 'Владик',
        'avatar' => 'img/userpic.jpg'
    ]
];

function cut_text(string $text, int $max_length = 300): string
{
    $text_length = mb_strlen($text);

    if ($text_length < $max_length) {
        return $text;
    }

    $words = explode(' ', $text);
    $cutted_text = '';
    $index = 0;

    while (mb_strlen($cutted_text . $words[$index]) <= $max_length) {
        $cutted_text .= "{$words[$index]} ";

        ++$index;
    }

    $cutted_text .= '...';

    return $cutted_text;
}

$page_content = include_template('main.php', ['posts' => $posts]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $user_name,
    'title' => 'readme: популярное'
]);

print ($layout_content);
