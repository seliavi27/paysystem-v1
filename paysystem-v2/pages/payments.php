<?php
declare(strict_types=1);

use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Enum\PaymentStatus;

function handlePaymentsGet($data, $user): array
{
    $paymentsFile = PAYMENTS_FILE;
    $paymentsArray = json_decode(file_get_contents($paymentsFile), true);

    $payments = array_map(
            fn($p) => Payment::fromArray($p),
            $paymentsArray ?? []
    );

    $status = $data['status'] ?? '';
    $type = $data['type'] ?? '';
    $minAmount = $data['min_amount'] ?? '';
    $maxAmount = $data['max_amount'] ?? '';

    $filtered = array_filter($payments, function ($payment) use ($user, $status, $type, $minAmount, $maxAmount)
    {
        if (($payment->userId ?? '') !== $user['id'])
        {
            return false;
        }

        if ($status && ($payment->status->name ?? '') !== $status)
        {
            return false;
        }

        if ($type && ($payment->type->name ?? '') !== $type)
        {
            return false;
        }

        if ($minAmount !== '' && $payment->amount < (float)$minAmount)
        {
            return false;
        }

        if ($maxAmount !== '' && $payment->amount > (float)$maxAmount)
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
    <input type="hidden" name="page" value="payments">

    Статус:
    <label>
        <select name="status">
            <option value="">Все</option>
            <?php foreach (PaymentStatus::cases() as $status): ?>
                <option value="<?= $status->name ?>"
                        <?= (($_POST['status'] ?? '') === $status->value) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($status->getLabel()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <br><br>

    Тип:
    <label>
        <select name="type">
            <option value="">Все</option>
            <?php foreach (PaymentMethod::cases() as $type): ?>
                <option value="<?= $type->name ?>"
                        <?= (($_POST['type'] ?? '') === $type->value) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($type->getLabel()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <br><br>
    Минимальная сумма:
    <label>
        <input type="text" name="min_amount" value="<?= htmlspecialchars($minAmount) ?>">
    </label>

    <br><br>
    Максимальная сумма:
    <label>
        <input type="text" name="max_amount" value="<?= htmlspecialchars($maxAmount) ?>">
    </label>

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
    </tr>

    <?php foreach ($filtered as $payment): ?>
        <tr>
            <td><?= htmlspecialchars($payment->id) ?></td>
            <td><?= htmlspecialchars($payment->createdAt->format('Y-m-d H:i')) ?></td>
            <td><?= $payment->amount ?></td>
            <td>
                <?= htmlspecialchars($payment->type->getLabel()) ?>
            </td>
            <td><?= htmlspecialchars($payment->status->getLabel()) ?></td>
            <td>
                <a href="/?page=payment_detail&id=<?= $payment->id ?>">Открыть</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br><br>
<a href="/?page=dashboard">Назад</a>

</body>
</html>