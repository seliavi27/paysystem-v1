<?php
declare(strict_types=1);

session_start();

require 'auth_functional.php';

$user = requireLogin();

$payments_file = 'data/payments.json';

$payments = is_file($payments_file)
    ? json_decode(file_get_contents($payments_file), true)
    : [];

if (!is_array($payments))
{
    $payments = [];
}

$status = $_GET['status'] ?? '';
$type = $_GET['type'] ?? '';
$minAmount = $_GET['min_amount'] ?? '';
$maxAmount = $_GET['max_amount'] ?? '';

$filtered = array_filter($payments, function ($payment) use ($user, $status, $type, $minAmount, $maxAmount)
{
    if (($payment['userId'] ?? '') !== $user['id'])
    {
        return false;
    }

    if ($status && ($payment['status'] ?? '') !== $status)
    {
        return false;
    }

    if ($type && ($payment['type'] ?? '') !== $type)
    {
        return false;
    }

    if ($minAmount !== '' && $payment['amount'] < (float)$minAmount)
    {
        return false;
    }

    if ($maxAmount !== '' && $payment['amount'] > (float)$maxAmount)
    {
        return false;
    }

    return true;
});

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Платежи</title>
</head>
<body>

<h2>Платежи</h2>

<form method="GET">
    Статус:
    <select name="status">
        <option value="">Все</option>
        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <br><br>
    Тип:
    <select name="type">
        <option value="">Все</option>
        <option value="card" <?= $type === 'card' ? 'selected' : '' ?>>Card</option>
        <option value="wallet" <?= $type === 'wallet' ? 'selected' : '' ?>>Wallet</option>
        <option value="bank_transfer" <?= $type === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
    </select>

    <br><br>
    Минимальная сумма:
    <input type="text" name="min_amount" value="<?= htmlspecialchars($minAmount) ?>">

    <br><br>
    Максимальная сумма:
    <input type="text" name="max_amount" value="<?= htmlspecialchars($maxAmount) ?>">

    <br><br>
    <button type="submit">Фильтр</button>
</form>

<br><br>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Дата</th>
        <th>Сумма</th>
        <th>Тип</th>
        <th>Статус</th>
        <th>Описание</th>
        <th>Детали</th>
    </tr>

    <?php foreach ($filtered as $payment): ?>
        <tr>
            <td><?= $payment['id'] ?></td>
            <td><?= htmlspecialchars($payment['date']) ?></td>
            <td><?= $payment['amount'] ?></td>
            <td><?= htmlspecialchars($payment['type']) ?></td>
            <td><?= htmlspecialchars($payment['status']) ?></td>
            <td><?= htmlspecialchars($payment['description']) ?></td>
            <td>
                <a href="#">Открыть</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br><br>
<a href="dashboard.php">Назад</a>

</body>
</html>