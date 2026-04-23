<?php
declare(strict_types=1);

namespace App\Interface;

use App\Entity\Payment;

interface ProcessableInterface
{
    public function process(Payment $payment): void;
}