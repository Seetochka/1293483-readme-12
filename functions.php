<?php

use ___PHPSTORM_HELPERS\object;

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

function format_time($date): string {
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

    return "$date_int $noun_plural_form ";
}

function fetch($connection, string $sql_request) {
    $query_result = mysqli_query($connection, $sql_request);

    if (mysqli_errno($connection) > 0) {
        $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($connection);
        die($errorMsg);
    }

    return $query_result;
}

function fetch_all($connection, string $sql_request): array {
    $query_result = fetch($connection, $sql_request);

    return mysqli_fetch_all($query_result, MYSQLI_ASSOC);
}

function fetch_assoc($connection, string $sql_request): array {
    $query_result = fetch($connection, $sql_request);

    return mysqli_fetch_assoc($query_result);
}

function get_sorting_field(?string $active_sorting_type): string {
    switch ($active_sorting_type) {
        case 'date':
            return 'dt_add';
        case 'likes':
            return 'likes_count';
        default:
            return 'show_count';
    }
}

function get_query_href(array $params, string $path): string {
    $current_params = $_GET;
    $merged_params = array_merge($current_params, $params);
    $query = http_build_query($merged_params);

    return $path . ($query ? "?$query" : '');
}
