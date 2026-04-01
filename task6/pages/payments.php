<?php
declare(strict_types=1);

function getPaymentTypeName(string $type): string
{
    return PAYMENTS_TYPES[$type][0] ?? $type;
}

function handlePaymentsGet($data, $user): array
{
    $paymentsFile = PAYMENTS_FILE;
    $payments = json_decode(file_get_contents($paymentsFile), true);

    if (!is_array($payments))
    {
        $payments = [];
    }

    $status = $data['status'] ?? '';
    $type = $data['type'] ?? '';
    $minAmount = $data['min_amount'] ?? '';
    $maxAmount = $data['max_amount'] ?? '';

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

    return [
            'filtered' => $filtered,
            'filters' => [
                    'status' => $status,
                    'type' => $type,
                    'min_amount' => $minAmount,
                    'max_amount' => $maxAmount
            ]
    ];
}

$user = requireLogin();
$data = handlePaymentsGet($_GET, $user);

$filtered = $data['filtered'];
$filters = $data['filters'];
$status = $filters['status'];
$type = $filters['type'];
$minAmount = $filters['min_amount'];
$maxAmount = $filters['max_amount'];
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

    <!--TODO насколько правильно так делать чтобы указывать в запросе "payments"-->
    <input type="hidden" name="page" value="payments">

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
        <?php foreach (PAYMENTS_TYPES as $key => $data): ?>
            <option value="<?= $key ?>" <?= (($type ?? '') === $key) ? 'selected' : '' ?>>
                <?= htmlspecialchars($data[0]) ?>
            </option>
        <?php endforeach; ?>
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
            <td>
                <?= htmlspecialchars(getPaymentTypeName($payment['type'])) ?>
            </td>
            <td><?= htmlspecialchars($payment['status']) ?></td>
            <td><?= htmlspecialchars($payment['description']) ?></td>
            <td>
                <a href="/?page=payment_detail&id=<?= (int)$payment['id'] ?>">Открыть</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br><br>
<a href="/?page=dashboard">Назад</a>

</body>
</html>