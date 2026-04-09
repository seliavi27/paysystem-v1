<?php
declare(strict_types=1);

namespace PaySystem\Interface;

use PaySystem\Entity\Payment;

interface ProcessableInterface
{
    public function process(Payment $payment): void;
}