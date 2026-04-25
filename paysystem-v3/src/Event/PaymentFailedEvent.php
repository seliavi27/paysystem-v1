<?php
declare(strict_types=1);

namespace PaySystem\Event;

use PaySystem\Entity\Payment;

class PaymentFailedEvent implements EventInterface
{
    public function __construct(
        private Payment $payment,
        private string  $reason
    ) {}

    public function getName(): string
    {
        return 'payment.failed';
    }

    public function getPayload(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'reason' => $this->reason,
        ];
    }
}