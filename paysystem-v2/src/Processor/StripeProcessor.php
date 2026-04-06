<?php
declare(strict_types=1);

class StripeProcessor extends AbstractPaymentProcessor
{
    public function process(Payment $payment): void
    {
        $this->validateApiKey();
        $this->logTransaction($payment, 'Stripe: start processing');

        $commission = $this->calculateCommission($payment->amount) + 0.30;

        echo "POST https://api.stripe.com/v2/charges\n";

        $success = random_int(0, 1) === 1;

        if (!$success)
        {
            $payment->status = PaymentStatus::FAILED;
            $this->logTransaction($payment, 'Stripe: failed');
            throw new RuntimeException('Stripe payment failed');
        }

        $payment->status = PaymentStatus::COMPLETED;

        $this->logTransaction(
            $payment,
            "Stripe: success, commission = {$commission}"
        );
    }

    public function refund(Payment $payment): void
    {
        $this->logTransaction($payment, 'Stripe: refund start');

        echo "POST https://api.stripe.com/v2/refunds\n";

        $payment->status = PaymentStatus::REFUNDED;

        $this->logTransaction($payment, 'Stripe: refunded');
    }

    public function getStatus(Payment $payment): string
    {
        echo "GET https://api.stripe.com/v2/charges/{$payment->id}\n";

        return $payment->status->value;
    }

    public function getName(): string
    {
        return 'Stripe';
    }
}