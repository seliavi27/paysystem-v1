<?php
declare(strict_types=1);

function calculate_commission(float $amount, string $payment_type = 'card'): float
{
    $duty = match ($payment_type)
    {
        "card" => 2.5,
        "bank_transfer" => 1.0,
        "wallet" => 0.5,
        default => 3.0,
    };

    $duty /= 100;
    $commission = $amount * $duty;
    return $commission;
}



function get_payment_status(int $code): string
{
    $status = match ($code)
    {
        0 => "Ожидание",
        1 => "Обработка",
        2 => "Завершено",
        3 => "Ошибка",
        4 => "Отменено",
        default => "Неизвестный статус"
    };

    return $status;
}


