<?php
declare(strict_types=1);

require 'login_functional.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout']))
{
    logout_user();
    header('Location: login.php');
    exit;
}

$user = require_login();

$payments_file = 'data/payments.json';
$payments = is_file($payments_file)
    ? json_decode(file_get_contents($payments_file), true)
    : [];

$userPayments = array_filter($payments, function ($pay) use ($user) {
    return ($pay['email'] ?? '') === $user['email'];
});

$count = count($userPayments);
$total = array_sum(array_column($userPayments, 'amount'));
$average = $count > 0 ? $total / $count : 0;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

<h2>Добро пожаловать, <?= htmlspecialchars($user['full_name']) ?>!</h2>

<h3>Статистика</h3>
<ul>
    <li>Количество платежей: <?= $count ?></li>
    <li>Общая сумма: <?= $total ?></li>
    <li>Среднее: <?= number_format($average, 2) ?></li>
</ul>

<h3>Последние платежи</h3>
<ul>
    <?php foreach (array_slice($userPayments, -5) as $payment): ?>
        <li>
            <?= htmlspecialchars($payment['date']) ?> —
            <?= htmlspecialchars($payment['amount']) ?> —
            <?= htmlspecialchars($payment['description']) ?>
        </li>
    <?php endforeach; ?>
</ul>

<br>

<a href="create_payment.php">Создать платёж</a><br>
<a href="profile.php">Профиль</a><br>

<form method="POST" style="display:inline;">
    <button type="submit" name="logout">Выход</button>
</form>

</body>
</html>