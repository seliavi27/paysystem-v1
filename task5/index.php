<?php
declare(strict_types=1);

session_start();

require 'auth_functional.php';

if (isUserLoggedIn())
{
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
</head>
<body>

<h1>Добро пожаловать в PaySystem</h1>

<a href="login.php">Войти</a><br>
<a href="register.php">Регистрация</a>

</body>
</html>