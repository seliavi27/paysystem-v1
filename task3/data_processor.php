<?php
declare(strict_types=1);

function apply_filters(array $payment, array $filters): bool
{
    if (isset($filters['status'])
        && $payment['status'] !== $filters['status'])
    {
        return false;
    }

    if (isset($filters['type'])
        && $payment['type'] !== $filters['type'])
    {
        return false;
    }

    if (isset($filters['min_amount'])
        && $payment['amount'] < $filters['min_amount'])
    {
        return false;
    }

    // Фильтр по максимальной сумме
    if (isset($filters['max_amount'])
        && $payment['amount'] > $filters['max_amount'])
    {
        return false;
    }

    return true;
}

function format_amount(string $currency = 'BYN'): string
{
    $symbols = [
        'BYN' => 'Б',
        'RUB' => 'P',
        'USD' => '$',
        'EUR' => 'E'
    ];

    $symbol = $symbols[$currency] ?? $currency;

    return $symbol;
}

function transform_payment(array $payment): array
{
    $payment['formatted_amount'] = format_amount($payment['currency']);

    return $payment;
}

function filter_and_transform(array $payments, array $filters = []): array
{
    $filteredParameters =
        array_filter($payments, function($payment) use ($filters) {
            return apply_filters($payment, $filters);
        });

    $transformedParameters = array_map(function($payment) {
        return transform_payment($payment);
    }, $filteredParameters);

    $result = array_values($transformedParameters);

    return $result;
}

function generate_summary(array $payments): array
{
    if (empty($payments))
    {
        return [
            'count' => 0,
            'total' => 0.0,
            'average' => 0.0,
            'min' => 0.0,
            'max' => 0.0,
        ];
    }

    $amounts = array_column($payments, 'amount');
    $count  = count($amounts);
    $total  = array_sum($amounts);
    $average  = $total / $count;
    $min = min($amounts);
    $max = max($amounts);

    $summary = [
        'count' => $count,
        'total' => round($total),
        'average' => round($average),
        'min' => round($min),
        'max' => round($max),
    ];

    return $summary;
}
