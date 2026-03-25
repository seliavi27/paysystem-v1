<?php
declare(strict_types=1);

function format_price(float $price, string $currency = 'RUB'): string
{
    $strPrice = number_format($price, 2, '.', ' ');
    $strPrice .= ' ' . $currency;
    return $strPrice;
}
