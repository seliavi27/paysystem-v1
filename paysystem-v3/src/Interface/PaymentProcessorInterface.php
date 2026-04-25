<?php
declare(strict_types=1);

namespace PaySystem\Interface;

use PaySystem\Entity\Payment;
use PaySystem\Enum\PaymentMethod;

interface PaymentProcessorInterface
{
    public function process(Payment $payment): void;

    public function refund(Payment $payment): void;

    public function getStatus(Payment $payment): string;

    public function getName(): string;
    public function supportedMethod(): PaymentMethod;
}