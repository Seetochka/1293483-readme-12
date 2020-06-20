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
$interlocutor_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$interlocutor_data = !empty($interlocutor_id) ? get_sql_user($link, $interlocutor_id) : null;

if (isset($interlocutor_id) && empty($interlocutor_data)) {
    header("HTTP/1.0 404 Not Found");
    $error_msg = 'Не удалось выполнить запрос: ' . mysqli_error($link);
    die($error_msg);
}

if (!empty($interlocutor_data)) {
    read_sql_message($link, $interlocutor_id, $user_data['id']);
}

$unread_messages_count = get_sql_unread_messages_count($link, $user_data['id']);
$interlocutors = get_sql_interlocutors($link, $user_data['id']);
$is_interlocutor = false;

foreach ($interlocutors as $key => $interlocutor) {
    if ($interlocutor_id === $interlocutor['id']) {
        $is_interlocutor = true;
    }

    $user = get_sql_user($link, $interlocutor['id']);
    $interlocutors[$key]['avatar'] = $user['avatar'];
    $interlocutors[$key]['login'] = $user['login'];
    $interlocutors[$key]['unread_messages_count'] = get_sql_unread_messages_count($link, $user_data['id'],
        $interlocutor['id']);

    $date_diff = strtotime('now') - strtotime($interlocutor['dt_add']);
    $hours_count = $date_diff / 3600;

    if ($hours_count <= HOURS_A_DAY) {
        $interlocutors[$key]['date'] = date_format(date_create($interlocutor['dt_add']), 'H:i');
    } else {
        $interlocutors[$key]['date'] = date_format(date_create($interlocutor['dt_add']), 'j M');
    }
}

if (!empty($interlocutor_data)) {
    if (!$is_interlocutor) {
        $interlocutors[] = array(
            'id' => $interlocutor_data['id'],
            'avatar' => $interlocutor_data['avatar'],
            'login' => $interlocutor_data['login']
        );
    } else {
        $messages = get_sql_messages($link, $user_data['id'], $interlocutor_id);

        foreach ($messages as $key => $message) {
            $author_message = get_sql_user($link, $message['sender_id']);
            $messages[$key]['avatar'] = $author_message['avatar'];
            $messages[$key]['login'] = $author_message['login'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message = remove_space($_POST);
    $rules = [
        'content' => function ($value) {
            return validate_field_completion($value);
        },
    ];

    $errors = array_filter(validate($new_message, $rules));

    if (!count($errors)) {
        $result = create_sql_message($link, $new_message['content'], $user_data['id'], $interlocutor_id);

        if ($result) {
            header('Location: messages.php?id=' . $interlocutor_id);
            die();
        }

        mysqli_error($link);
    }
}

$page_content = include_template('messages.php', [
    'user_data' => $user_data,
    'messages' => $messages ?? [],
    'interlocutors' => $interlocutors,
    'interlocutor_id' => $interlocutor_id,
    'errors' => $errors ?? [],
    'new_message' => $new_message ?? [],
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: личные сообщения',
    'unread_messages_count' => $unread_messages_count,
]);

print ($layout_content);
