<?php
declare(strict_types=1);

function filter_payments_by_amount(array $payments, float $min, float $max): array
{
    $filterPayments = array_filter(
        $payments,
        function ($payment) use ($min, $max)
        {
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



function sum_payments(array $payments): float
{
    $amounts = array_column($payments, 'amount');
    $sumPayments = array_sum($amounts);
    return $sumPayments;
}