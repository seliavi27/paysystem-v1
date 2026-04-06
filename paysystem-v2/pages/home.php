<?php

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>">
</head>
<body>
<header class="header">
    <div class="container">
        <h1><a href="/?page=home"><?= APP_NAME ?></a></h1>
        <nav class="nav">
            <?php if (isUserLoggedIn()): ?>
                <a href="/?page=dashboard">Дашборд</a>
                <a href="/?page=payments">Платежи</a>
                <a href="/?page=profile">Профиль</a>
                <a href="/?page=logout">Выход</a>
            <?php else: ?>
                <a href="/?page=login">Логин</a>
                <a href="/?page=register">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<footer class="footer">
    <p>2026 PaySystem</p>
</footer>
</body>
</html>