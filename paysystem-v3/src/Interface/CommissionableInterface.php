<?php
declare(strict_types=1);

namespace PaySystem\Interface;

interface CommissionableInterface
{
    public function getCommission(float $amount): float;
}
