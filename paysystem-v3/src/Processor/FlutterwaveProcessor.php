<?php
declare(strict_types=1);

namespace App\Processor;

use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Entity\Payment;
use RuntimeException;

class FlutterwaveProcessor extends AbstractPaymentProcessor
{
    public function supportedMethod(): PaymentMethod
    {
        return PaymentMethod::DIGITAL_WALLET;
    }

    public function process(Payment $payment): void
    {
        $this->validateApiKey();
        $this->logTransaction($payment, 'Flutterwave: start processing');

        $commission = $this->calculateCommission($payment->amount);

        echo "POST https://api.flutterwave.com/v2/payments\n";

        $success = random_int(0, 1) === 1;

        if (!$success) {
            $payment->status = PaymentStatus::FAILED;
            $this->logTransaction($payment, 'Flutterwave: failed');
            throw new RuntimeException('Flutterwave payment failed');
        }

        $payment->status = PaymentStatus::COMPLETED;

        $this->logTransaction(
            $payment,
            "Flutterwave: success, commission = {$commission}"
        );
    }

    public function refund(Payment $payment): void
    {
        $this->logTransaction($payment, 'Flutterwave: refund start');

        echo "POST https://api.flutterwave.com/v2/refunds\n";

        $payment->status = PaymentStatus::REFUNDED;

        $this->logTransaction($payment, 'Flutterwave: refunded');
    }

    public function getStatus(Payment $payment): string
    {
        echo "GET https://api.flutterwave.com/v2/transactions/{$payment->id}\n";

        return $payment->status->value;
    }

    public function getName(): string
    {
        return 'Flutterwave';
    }
}