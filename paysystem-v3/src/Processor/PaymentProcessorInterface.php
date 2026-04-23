<?php
declare(strict_types=1);

namespace App\Processor;

use App\Entity\Payment;
use App\Enum\PaymentMethod;

interface PaymentProcessorInterface
{
    public function process(Payment $payment): void;

    public function refund(Payment $payment): void;

    public function getStatus(Payment $payment): string;

    public function getName(): string;
    public function supportedMethod(): PaymentMethod;
}