<?php
declare(strict_types=1);

namespace PaySystem\Strategy;

class PercentageFeeStrategy implements CommissionStrategy
{
    public function __construct(
        private float $percentage)
    {

    }

    public function calculate(float $amount): float
    {
        return $amount * ($this->percentage / 100);
    }

    public function getName(): string
    {
        return 'Percentage Fee';
    }
}