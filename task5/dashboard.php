<?php
declare(strict_types=1);

require 'auth_functional.php';

$flash = [];

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

if (!empty($_SESSION['flash']))
{
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$theme = $_SESSION['theme'] ?? 'light';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout']))
{
    logout_user();
    header('Location: login.php');
    exit;
}

$paymentsFile = 'data/payments.json';
$payments = is_file($paymentsFile)
    ? json_decode(file_get_contents($paymentsFile), true)
    : [];

$userPayments = array_filter($payments, function ($pay) use ($user)
{
    return ($pay['userId'] ?? '') === $user['id'];
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

<?php if (isset($flash['type']) && isset($flash['message'])): ?>
    <div style="color: <?= $flash['type'] === 'success' ? 'green' : 'red' ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<h2>Добро пожаловать, <?= htmlspecialchars($user['fullName']) ?>!</h2>

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
            <?= htmlspecialchars((string)$payment['amount']) ?> —
            <?= htmlspecialchars($payment['description']) ?>
        </li>
    <?php endforeach; ?>
</ul>

<a href="payments.php">Список всех платежей</a>
<br><br>

<a href="create_payment.php">Создать платёж</a>
<br><br>

<a href="profile.php">Профиль</a>
<br><br>

<form method="POST" style="display:inline;">
    <button type="submit" name="logout">Выход</button>
</form>

</body>
</html>