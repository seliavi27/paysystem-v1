<?php
declare(strict_types=1);

function get_all_amounts(array $payments): array
{
    $amounts = array_column($payments, 'amount');
    return $amounts;
}


function group_payments_by_status(array $payments): array
{
    $group = array_reduce(
        $payments,
        function ($carry, $payment)
        {
            $status = $payment['status'];
            if (!isset($status)) {
                return false;
            }

            $carry[$status][] = $payment;
            return $carry;
        }
    );

    return $group;
}


function calculate_total_by_type(array $payments): array
{
    $group = array_reduce(
        $payments,
        function ($carry, $payment)
        {
            $type = $payment['type'] ?? 'unknown';
            $amount = $payment['amount'] ?? 0;

            $carry[$type] = ($carry[$type] ?? 0) + $amount;
            return $carry;
        }
    );

    return $group;
}


function sort_payments_by_amount(array $payments, bool $descending = true): array
{
    $sorted = $payments;

    usort(
        $sorted,
        function($a, $b) use ($descending)
        {
            $amountA = $a['amount'] ?? 0;
            $amountB = $b['amount'] ?? 0;

            if ($descending)
            {
                return $amountB <=> $amountA;
            }
            else
            {
                return $amountA <=> $amountB;
            }
        }
    );

    return $sorted;
}


function get_top_payments(array $payments, int $limit = 5): array
{
    $sorted = $payments;

    usort(
        $sorted,
        function($a, $b)
        {
            $amountA = $a['amount'] ?? 0;
            $amountB = $b['amount'] ?? 0;
            return $amountB <=> $amountA;
        }
    );

    $topPayments = array_slice($sorted, 0, $limit);
    return $topPayments;
}