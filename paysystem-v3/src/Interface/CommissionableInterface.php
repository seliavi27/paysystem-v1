<?php
declare(strict_types=1);

namespace App\Interface;

interface CommissionableInterface
{
    public function getCommission(float $amount): float;
}
