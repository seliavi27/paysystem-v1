<?php
declare(strict_types=1);

namespace PaySystem\Processor;

use InvalidArgumentException;
use PaySystem\Entity\Payment;
use PaySystem\Strategy\CommissionStrategy;

abstract class AbstractPaymentProcessor implements PaymentProcessorInterface
{
    protected CommissionStrategy $commissionStrategy
    {
        get => $this->commissionStrategy;
        set => $this->commissionStrategy = $value;
    }

    protected string $apiKey
    {
        get => $this->apiKey;
    }

    protected string $webhookSecret
    {
        get => $this->webhookSecret;
    }

    protected float $commissionRate
    {
        get => $this->commissionRate;
    }

    public function __construct(
        string $apiKey,
        string $webhookSecret,
        float  $commissionRate
    )
    {
        $this->apiKey = $apiKey;
        $this->webhookSecret = $webhookSecret;
        $this->commissionRate = $commissionRate;

        $this->validateApiKey();
    }

    public function calculateCommission(float $amount): float
    {
        return $this->commissionStrategy->calculate($amount);
    }

    public function setCommissionStrategy(CommissionStrategy $strategy): void
    {
        $this->commissionStrategy = $strategy;
    }

    abstract public function process(Payment $payment): void;

    abstract public function refund(Payment $payment): void;

    abstract public function getStatus(Payment $payment): string;

    abstract public function getName(): string;

    protected function validateApiKey(): void
    {
        if (empty($this->apiKey))
        {
            throw new InvalidArgumentException(
                $this->getName() . ': API key is missing'
            );
        }
    }

    protected function logTransaction(Payment $payment, string $message): void
    {
        $logMessage = sprintf(
            '[%s] [%s] Payment %s: %s',
            date('Y-m-d H:i:s'),
            $this->getName(),
            $payment->id,
            $message
        );

        error_log($logMessage);
    }

}