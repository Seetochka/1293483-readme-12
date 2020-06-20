<?php
require_once 'helpers.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'sql-queries.php';

if (isset($_SESSION['user'])) {
    header("Location: /feed.php");
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = remove_space($_POST);
    $rules = [
        'email' => function ($value) {
            return validate_email($value);
        },
        'password' => function ($value) {
            return validate_field_completion($value);
        },
    ];

    $errors = array_filter(validate($form, $rules));

    if (!count($errors)) {
        $user_db = search_sql_email($link, $_POST['email']);

        if (!empty($user_db) && password_verify($form['password'], $user_db['password'])) {
            $_SESSION['user'] = $user_db;
        } else {
            $errors['email'] = 'Вы ввели неверный email/пароль';
            $errors['password'] = 'Вы ввели неверный email/пароль';
        }
    }

    if (!count($errors)) {
        header('Location: feed.php');
        die();
    }
}

$page_content = include_template('index.php', [
    'form' => $form ?? [],
    'errors' => $errors ?? [],
]);

print ($page_content);
