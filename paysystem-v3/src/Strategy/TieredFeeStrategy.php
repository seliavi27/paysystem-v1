<?php
declare(strict_types=1);

namespace App\Strategy;

class TieredFeeStrategy implements CommissionStrategy
{
    private array $tiers = [
        1000 => 0.01,
        5000 => 0.015,
        PHP_INT_MAX => 0.02
    ];

    public function calculate(float $amount): float
    {
        foreach ($this->tiers as $limit => $rate)
        {
            if ($amount <= $limit)
            {
                return $amount * $rate;
            }
        }

        return 0;
    }

    public function getName(): string
    {
        return 'Tiered Fee';
    }
}
