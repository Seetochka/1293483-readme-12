<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';
require_once 'constants.php';

if (isset($_SESSION['user'])) {
    header("Location: /feed.php");
    die();
}

$title_errors = [
    'email' => 'Электронная почта',
    'login' => 'Логин',
    'password' => 'Пароль',
    'password-repeat' => 'Повтор пароля',
    'avatar' => 'Фото',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = remove_space($_POST);
    $rules = [
        'email' => function($value) {return validate_email($value); },
        'login' => function($value) {return validate_login($value); },
        'password' => function($value) {return validate_password($value); },
        'password-repeat' => function($value) {return validate_password_repeat($value); },
    ];

    $errors = validate($user, $rules);

    if (empty($errors['login'])) {
        $errors = ['login' => search_sql_login($link, $_POST['login']) ? 'Пользователь с таким логином уже существует' : ''] + $errors;
    }

    if (empty($errors['email'])) {
        $errors = ['email' => search_sql_email($link, $_POST['email']) ? 'Пользователь с таким Email уже существует' : ''] + $errors;
    }

    if (!empty($_FILES['avatar']['name'])) {
        $errors['avatar'] = validate_avatar($_FILES['avatar']);
    }

    $errors = array_filter($errors);

    if (!count($errors)) {
        create_sql_user($link, $user) ? header('Location: index.php') : mysqli_error($link);
    }
}

$page_content = include_template('registration.php', [
    'user' => $user,
    'errors' => $errors ?? [],
    'title_errors' => $title_errors,
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: регистрация',
]);

print ($layout_content);
