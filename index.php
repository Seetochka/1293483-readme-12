<?php
require_once 'helpers.php';
require_once 'init.php';

date_default_timezone_set('Europe/Moscow');

$content_type_size = [
    'quote' => ['width' => '21', 'height' => '20'],
    'link' => ['width' => '21', 'height' => '18'],
    'photo' => ['width' => '22', 'height' => '18'],
    'video' => ['width' => '24', 'height' => '16'],
    'text' => ['width' => '20', 'height' => '21']
];

if (!$link) {
    $error_connect = mysqli_connect_error();
    print $error_connect;
} else {
    $sql_content_types = 'SELECT title, class_name FROM content_types'; //запрос на получение типов только существующих постов SELECT c.title, c.class_name FROM content_types c INNER JOIN posts p ON c.id = p.content_type_id GROUP BY c.title
    $sql_posts = 'SELECT p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, c.class_name FROM posts p '
                . 'INNER JOIN users u ON p.user_id = u.id '
                . 'INNER JOIN content_types c ON p.content_type_id = c.id '
                . 'ORDER BY show_count DESC LIMIT 6';

    if ($res = mysqli_query($link, $sql_content_types)) {
        $content_types = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        $error_content_types = mysqli_error($link);
        print $error_content_types;
    }

    if ($res = mysqli_query($link, $sql_posts)) {
        $posts = mysqli_fetch_all($res, MYSQLI_ASSOC);
    } else {
        $error_posts = mysqli_error($link);
        print $error_posts;
    }
}

$is_auth = rand(0, 1);
$user_name = 'Светлана';

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

$page_content = include_template('main.php', [
    'posts' => $posts,
    'content_types' => $content_types,
    'content_type_size' => $content_type_size
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'readme: популярное'
]);

print ($layout_content);
