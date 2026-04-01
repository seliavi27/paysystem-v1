<?php
declare(strict_types=1);

function handlePaymentsGet($data, $user): array
{
    $errors = [];
    $payment = null;

    $id = isset($data['id']) ? (int)$data['id'] : 0;

    if ($id <= 0)
    {
        $message = "Incorrect payment ID";
        log_error($message);

        return [
                'errors' => [$message],
                'payment' => $payment
        ];
    }

    $payments = json_decode(file_get_contents(PAYMENTS_FILE), true);

    if (!is_array($payments))
    {
        $payments = [];
    }

    $payment = null;

    foreach ($payments as $p)
    {
        if ((int)$p['id'] === $id && ($p['userId'] ?? null) === $user['id'])
        {
            $payment = $p;
            break;
        }
    }

    if (!$payment)
    {
        $message = "Payment not found or you don't have access";
        log_error($message);

        return [
                'errors' => [$message],
                'payment' => $payment
        ];
    }

    return [
            'errors' => [],
            'payment' => $payment
    ];
}

$user = requireLogin();
$result = handlePaymentsGet($_GET, $user);
$errors = $result['errors'];
$payment = $result['payment'];
?>

<h2>Детали платежа #<?= $payment['id'] ?></h2>

<ul>
    <li><strong>Дата:</strong> <?= htmlspecialchars($payment['date']) ?></li>
    <li><strong>Сумма:</strong> <?= number_format($payment['amount'], 2) ?></li>
    <li><strong>Тип:</strong> <?= htmlspecialchars($payment['type']) ?></li>
    <li><strong>Статус:</strong> <?= htmlspecialchars($payment['status']) ?></li>
    <li><strong>Описание:</strong> <?= htmlspecialchars($payment['description'] ?? '-') ?></li>
</ul>

<br>

<a href="/?page=payments">
    <button>Назад к списку</button>
</a>