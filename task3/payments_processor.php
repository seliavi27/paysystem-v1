<?php
declare(strict_types=1);

$payments = [
    [
        'id' => 1,
        'user_id' => 101,
        'amount' => 1500.50,
        'type' => 'card',
        'status' => 'completed',
        'date' => '2024-01-15',
        'description' => 'Оплата заказа #12345'
    ],
    [
        'id' => 2,
        'user_id' => 102,
        'amount' => 500.00,
        'type' => 'wallet',
        'status' => 'pending',
        'date' => '2024-01-16',
        'description' => 'Пополнение счета'
    ],
    // ... больше платежей
];

function get_all_amounts(array $payments): array
{
    $amounts = array_column($payments, 'amount');
    return $amounts;
}

$result = get_all_amounts($payments);
echo print_r($result, true) . "</br>";



function filter_payments_by_amount(array $payments, float $min, float $max): array
{
    $filterPayments = array_filter(
        $payments,
        function ($payment) use ($min, $max) {
            if (!isset($payment['amount'])) {
                return false;
            }

            $amount = $payment['amount'];
            $result = $amount >= $min && $amount <= $max;
            return $result;
        }
    );

    return $filterPayments;
}
