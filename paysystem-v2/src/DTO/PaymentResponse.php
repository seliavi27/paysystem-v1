<?php
declare(strict_types=1);

final readonly class PaymentResponse
{
    public function __construct(
        public string $paymentId,
        public string $userId,
        public float $amount,
        public CurrencyType $currency,
        public PaymentStatus $status,
        public DateTime $createdAt
    ) {}

    public function toArray(): array
    {
        return [
            'paymentId' => $this->paymentId,
            'userId' => $this->userId,
            'amount' => $this->amount,
            'currency' => $this->currency->value,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt,
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