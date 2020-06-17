<?php
require_once 'vendor/autoload.php';

$transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
    ->setUsername('keks@phpdemo.ru')
    ->setPassword('htmlacademy');

$mailer = new Swift_Mailer($transport);
