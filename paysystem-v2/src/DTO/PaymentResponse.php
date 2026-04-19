<?php
declare(strict_types=1);

namespace PaySystem\DTO;

use DateTime;
use PaySystem\Entity\Payment;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\PaymentStatus;

final readonly class PaymentResponse
{
    public function __construct(
        public string $id,
        public string $userId,
        public float $amount,
        public string $description,
        public CurrencyType $currency,
        public PaymentStatus $status,
        public DateTime $createdAt
    )
    {
    }

    public static function fromEntity(Payment $payment): self
    {
        return new self(
            id: $payment->id,
            userId: $payment->userId,
            amount: $payment->amount,
            description: $payment->description,
            currency: $payment->currency,
            status: $payment->status,
            createdAt: $payment->createdAt,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'amount' => $this->amount,
            'description' => $this->description,
            'currency' => $this->currency->value,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public function toJson(): string
    {
        return json_encode(
            $this->toArray(),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );
    }
}