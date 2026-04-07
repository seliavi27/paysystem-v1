<?php
declare(strict_types=1);

//namespace PaySystem\DTO;

final readonly class TransactionRequest
{
    public function __construct(
        public string $userId,
        public string $paymentId,
        public TransactionType $type,
        public float $amount,
        public string $description
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

        if (trim($this->paymentId) === '')
        {
            throw new InvalidArgumentException('Payment ID is required');
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
            'paymentId' => $this->paymentId,
            'type' => $this->type->value,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}