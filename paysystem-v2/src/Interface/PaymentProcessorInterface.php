<?php
declare(strict_types=1);

namespace PaySystem\Interface;

use PaySystem\Entity\Payment;

interface PaymentProcessorInterface
{
    public function process(Payment $payment): void;

    public function refund(Payment $payment): void;

    public function getStatus(Payment $payment): string;

    public function getName(): string;
}