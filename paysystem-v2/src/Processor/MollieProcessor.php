<?php
declare(strict_types=1);

namespace PaySystem\Processor;

use PaySystem\Enum\PaymentStatus;
use PaySystem\Entity\Payment;
use RuntimeException;

class MollieProcessor extends AbstractPaymentProcessor
{
    public function process(Payment $payment): void
    {
        $this->validateApiKey();
        $this->logTransaction($payment, 'Mollie: start processing');

        $commission = $this->calculateCommission($payment->amount);

        echo "POST https://api.mollie.com/v2/payments\n";

        $success = random_int(0, 1) === 1;

        if (!$success) {
            $payment->status = PaymentStatus::FAILED;
            $this->logTransaction($payment, 'Mollie: failed');
            throw new RuntimeException('Mollie payment failed');
        }

        $payment->status = PaymentStatus::COMPLETED;

        $this->logTransaction(
            $payment,
            "Mollie: success, commission = {$commission}"
        );
    }

    public function refund(Payment $payment): void
    {
        $this->logTransaction($payment, 'Mollie: refund start');

        echo "POST https://api.mollie.com/v2/refunds\n";

        $payment->status = PaymentStatus::REFUNDED;

        $this->logTransaction($payment, 'Mollie: refunded');
    }

    public function getStatus(Payment $payment): string
    {
        echo "GET https://api.mollie.com/v2/payments/{$payment->id}\n";

        return $payment->status->value;
    }

    public function getName(): string
    {
        return 'Mollie';
    }
}