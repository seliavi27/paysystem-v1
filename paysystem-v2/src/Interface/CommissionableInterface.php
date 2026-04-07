<?php
declare(strict_types=1);

interface CommissionableInterface
{
    public function getCommission(float $amount): float;
}
