<?php
declare(strict_types=1);

namespace PaySystem\Strategy;

interface CommissionStrategy
{
    public function calculate(float $amount): float;

    public function getName(): string;
}