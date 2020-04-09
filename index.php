<?php
require_once 'helpers.php';

date_default_timezone_set('Europe/Moscow');

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

function format_time($date) {
    define('WEEK', 7);

    $dt_current = date_create('now');
    $dt_date = date_create($date);
    $dt_diff = date_diff($dt_current, $dt_date);
    $date_diff_unix = strtotime(date_interval_format($dt_diff, '%Y-%M-%D %H:%I'));
    $date_int = null;
    $noun_plural_form = null;

    switch (true) {
        case ($date_diff_unix < strtotime('00-00-00 01:00')):
            $date_int = idate('i', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'минуту', 'минуты', 'минут');

            break;

        case ($date_diff_unix < strtotime('00-00-01 00:00')):
            $date_int = idate('H', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'час', 'часа', 'часов');

            break;

        case ($date_diff_unix < strtotime('00-00-07 00:00')):
            $date_int = idate('d', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'день', 'дня', 'дней');

            break;

        case ($date_diff_unix < strtotime('00-01-00 00:00')):
            $date_int = floor(idate('d', $date_diff_unix) / WEEK);
            $noun_plural_form = get_noun_plural_form($date_int, 'неделю', 'недели', 'недель');

            break;

        default:
            $date_int = idate('m', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'месяц', 'месяца', 'месяцев');

            break;
    }

    return "$date_int $noun_plural_form назад";
}

$page_content = include_template('main.php', ['posts' => $posts]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $user_name,
    'title' => 'readme: популярное'
]);

print ($layout_content);
