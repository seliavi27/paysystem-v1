<?php
declare(strict_types=1);

function handleDashboardPost($data): array
{
    $flash = [];

    if (!empty($_SESSION['flash']))
    {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }

    $theme = $_SESSION['theme'] ?? 'light';

    $user = requireLogin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['logout']))
    {
        logoutUser();
        redirectToPage('logout');
    }

    $paymentsFile = PAYMENTS_FILE;

    $payments = json_decode(file_get_contents($paymentsFile), true);

    $userPayments = array_filter($payments, function ($pay) use ($user)
    {
        return ($pay['userId'] ?? '') === $user['id'];
    });

    $paymentStats = calculatePaymentStats($userPayments);
    $result = $paymentStats;
    $result['flash'] = $flash;
    $result['theme'] = $theme;
    $result['user'] = $user;

    return $result;
}

$data = handleDashboardPost($_POST);
$flash = $data['flash'];
$theme = $data['theme'];
$user = $data['user'];
$count = $data['count'];
$total = $data['total'];
$average = $data['average'];
$byStatus = $data['byStatus'];
$lastPayments = $data['lastPayments'];
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

<h3>Последние 5 платежей</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Дата</th>
        <th>Сумма</th>
        <th>Тип</th>
        <th>Статус</th>
    </tr>

    <?php foreach ($lastPayments as $payment): ?>
        <tr>
            <td><?= $payment['id'] ?></td>
            <td><?= htmlspecialchars($payment['date']) ?></td>
            <td><?= number_format($payment['amount'], 2) ?></td>
            <td><?= htmlspecialchars($payment['type']) ?></td>
            <td><?= htmlspecialchars($payment['status']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="/?page=payments">Список всех платежей</a>

<br><br>
<a href="/?page=create_payment">Создать платёж</a>

<br><br>
<a href="/?page=profile">Профиль</a>

<br><br>
<form method="POST" style="display:inline;">
    <button type="submit" name="logout">Выход</button>
</form>

</body>
</html>