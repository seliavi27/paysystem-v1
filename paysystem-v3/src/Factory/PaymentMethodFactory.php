<?php
declare(strict_types=1);

namespace App\Factory;

use App\Enum\PaymentMethod;
use App\Processor\PaymentProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class PaymentMethodFactory
{
    /** @var array<string, PaymentProcessorInterface> */
    private array $processors;

    /** @param iterable<PaymentProcessorInterface> $processors */
    public function __construct(
        #[AutowireIterator('payment.processor')]
        iterable $processors)
    {
        $this->processors = [];

        foreach ($processors as $processor)
        {
            $this->processors[$processor->supportedMethod()->value] = $processor;
        }
    }

    public function create(PaymentMethod $method): PaymentProcessorInterface
    {
        return $this->processors[$method->value]
            ?? throw new \InvalidArgumentException("No processor for {$method->value}");
    }

    public function getAll(): array
    {
        return array_keys($this->processors);
    }
}