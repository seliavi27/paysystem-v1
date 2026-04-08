<?php
declare(strict_types=1);

/* TODO: Как правильно реализовать Processors с передачей
    AbstractPaymentProcessor, что реализован с
    множеством мелких интерфейсов
*/

namespace PaySystem\Interface;

use PaySystem\Entity\Payment;

interface PaymentProcessorInterface
{
    public function process(Payment $payment): void;

    public function refund(Payment $payment): void;

    public function getStatus(Payment $payment): string;

    public function getName(): string;
}