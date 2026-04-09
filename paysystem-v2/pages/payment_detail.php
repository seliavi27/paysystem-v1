<?php
declare(strict_types=1);

function handlePaymentsGet($data, $user): array
{
    $errors = [];
    $payment = null;

    $id = $data['id'] ?? null;
    $userIdFromCookie = $user['id'] ?? null;

    if (is_null($id) || is_null($userIdFromCookie))
    {
        $message = "Incorrect payment ID";
        $errors[] = "Incorrect payment ID";
        log_error($message);

        return [
                'errors' => $errors,
                'payment' => $payment
        ];
    }

    $paymentsArray = json_decode(file_get_contents(PAYMENTS_FILE), true);
    $payments = array_map(
            fn($p) => Payment::fromArray($p),
            $paymentsArray ?? []
    );

    foreach ($payments as $p)
    {
        if (($p->id === $id) && ($p->userId === $userIdFromCookie))
        {
            $payment = $p;
            break;
        }
    }

    if (is_null($payment))
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

<h2>Детали платежа #<?= $payment->id ?></h2>

<ul>
    <li><strong>Дата:</strong> <?= htmlspecialchars($payment->createdAt->format('Y-m-d H:i')) ?></li>
    <li><strong>Сумма:</strong> <?= number_format($payment->amount, 2) ?></li>
    <li><strong>Тип:</strong> <?= htmlspecialchars($payment->type->getLabel()) ?></li>
    <li><strong>Статус:</strong> <?= htmlspecialchars($payment->status->getLabel()) ?></li>
</ul>

<br>

<a href="/?page=payments">
    <button>Назад к списку</button>
</a>