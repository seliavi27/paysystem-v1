<?php
declare(strict_types=1);

namespace App\Interface;

use App\Entity\Payment;

interface RefundableInterface
{
    public function refund(Payment $payment): void;
}
