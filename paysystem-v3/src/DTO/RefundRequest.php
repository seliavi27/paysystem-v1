<?php
declare(strict_types=1);

namespace App\DTO;

use InvalidArgumentException;

final readonly class RefundRequest
{
    public function __construct(
        public string $paymentId,
        public string $reason,
        public ?float $amount = null
    )
    {
        $this->validate();
    }

    private function validate(): void
    {
        if (trim($this->paymentId) === '') {
            throw new InvalidArgumentException('Payment ID is required');
        }

        if (trim($this->reason) === '') {
            throw new InvalidArgumentException('Refund reason is required');
        }

        if (mb_strlen($this->reason) > 500) {
            throw new InvalidArgumentException('Reason must be <= 500 characters');
        }

        if ($this->amount !== null && $this->amount < 0) {
            throw new InvalidArgumentException('Refund amount must be greater than 0');
        }
    }

    public function isPartial(): bool
    {
        return $this->amount !== null;
    }

    public function toArray(): array
    {
        return [
            'paymentId' => $this->paymentId,
            'reason' => $this->reason,
            'amount' => $this->amount,
        ];
    }
}