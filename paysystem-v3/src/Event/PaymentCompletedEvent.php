<?php
declare(strict_types=1);

namespace App\Event;

use App\Entity\Payment;
use App\Entity\User;

class PaymentCompletedEvent implements EventInterface
{
    public function __construct(
        private Payment $payment,
        private User    $user
    ) {}

    public function getName(): string
    {
        return 'payment.completed';
    }

    public function getPayload(): array
    {
        return [
            'paymentId' => $this->payment->id,
            'userId' => $this->user->id,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
        ];
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}