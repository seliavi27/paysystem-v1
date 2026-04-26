<?php
declare(strict_types=1);

namespace App\Strategy;

class FixedFeeStrategy implements CommissionStrategy
{
    public function __construct(
        private float $fixedAmount)
    {

    }

    public function calculate(float $amount): float
    {
        return $this->fixedAmount;
    }

    public function getName(): string
    {
        return 'Fixed Fee';
    }
}