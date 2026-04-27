<?php
declare(strict_types=1);

namespace PaySystem\Factory;

use InvalidArgumentException;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Interface\ProcessableInterface;
use PaySystem\Processor\AbstractPaymentProcessor;
use PaySystem\Processor\FlutterwaveProcessor;
use PaySystem\Processor\MollieProcessor;
use PaySystem\Processor\StripeProcessor;
use PaySystem\Strategy\PercentageFeeStrategy;

class PaymentMethodFactory {
    private array $processors = [];

    public function __construct(
        private readonly string $stripeKey,
        private readonly string $stripeSecret,
        private readonly string $mollieKey,
        private readonly string $mollieSecret,
        private readonly string $flutterwaveKey,
        private readonly string $flutterwaveSecret
    ) {}

    public function create(PaymentMethod $method): AbstractPaymentProcessor
    {
        $key = $method->value;

        if (isset($this->processors[$key]))
        {
            return $this->processors[$key];
        }

        $processor = match($method)
        {
            PaymentMethod::CREDIT_CARD => new StripeProcessor(
                $this->stripeKey,
                $this->stripeSecret,
                PaymentMethod::CREDIT_CARD->getCommission()
            ),
            PaymentMethod::BANK_TRANSFER => new MollieProcessor(
                $this->mollieKey,
                $this->mollieSecret,
                PaymentMethod::BANK_TRANSFER->getCommission()
            ),
            PaymentMethod::DIGITAL_WALLET => new FlutterwaveProcessor(
                $this->flutterwaveKey,
                $this->flutterwaveSecret,
                PaymentMethod::DIGITAL_WALLET->getCommission()
            ),
            default => throw new InvalidArgumentException(
                "Unknown payment method: {$method->value}"
            )
        };

        $processor->setCommissionStrategy(new PercentageFeeStrategy($method->getCommission()));

        $this->processors[$key] = $processor;
        return $processor;
    }

    public function getAll(): array
    {
        return array_keys($this->processors);
    }
}