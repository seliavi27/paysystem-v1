<?php
declare(strict_types=1);

namespace PaySystem\Interface;

use PaySystem\Entity\Payment;

interface RefundableInterface
{
    public function refund(Payment $payment): void;
}
