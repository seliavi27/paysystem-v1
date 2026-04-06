<?php
declare(strict_types=1);

$statusCode = 404;
http_response_code($statusCode);
$pageTitle = 'Page not found - ' . $statusCode;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <h1 align="center"><?= htmlspecialchars($pageTitle) ?></h1>
    <p align="center"><a href="/?page=home">Вернуться на главную</a></p>
</body>
</html>