<?php
declare(strict_types=1);

//namespace PaySystem\DTO;

final readonly class CreatePaymentRequest
{
    public function __construct(
        public string $userId,
        public float $amount,
        public string $description,
        public CurrencyType $currency,
        public PaymentMethod $paymentMethod
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->userId) === '')
        {
            throw new InvalidArgumentException('User ID is required');
        }

        if ($this->amount < 0)
        {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        if (trim($this->description) === '')
        {
            throw new InvalidArgumentException('Description is required');
        }

        if (mb_strlen($this->description) > 500)
        {
            throw new InvalidArgumentException('Description must be <= 500 characters');
        }
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'amount' => $this->amount,
            'description' => $this->description,
            'currency' => $this->currency->value,
            'paymentMethod' => $this->paymentMethod->value,
        ];
    }
}
