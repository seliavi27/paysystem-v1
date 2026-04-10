<?php
declare(strict_types=1);

namespace PaySystem\Entity;

use DateTime;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\TransactionType;
use PaySystem\Trait\HasUuid;
use PaySystem\Trait\Loggable;
use PaySystem\Trait\Timestampable;

class Transaction
{
    use Loggable, HasUuid, Timestampable;

    public string $userId
        {
            get => $this->userId;
            set => $this->userId = $value;
        }

    public string $paymentId
        {
            get => $this->paymentId;
            set => $this->paymentId = $value;
        }

    public TransactionType $type
        {
            get => $this->type;
            set => $this->type = $value;
        }

    public CurrencyType $currency
        {
            get => $this->currency;
        }

    public float $amount
        {
            get => $this->amount;
        }

    public DateTime $timestamp
        {
            get => $this->timestamp;
            set => $this->timestamp = $value;
        }

    public string $description
        {
            get => $this->description;
        }

    public function __construct(
        string          $userId,
        string          $paymentId,
        TransactionType $type,
        CurrencyType    $currency,
        float           $amount,
        string          $description,
        DateTime        $timestamp,
        ?string         $id = null,
        ?DateTime       $createdAt = null,
        ?DateTime       $updatedAt = null
    )
    {
        $this->id = $id ?? self::generateUuid();
        $this->userId = $userId;
        $this->paymentId = $paymentId;
        $this->type = $type;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->description = $description;
        $this->timestamp = $timestamp;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'paymentId' => $this->paymentId,
            'type' => $this->type,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'description' => $this->description,
            'timestamp' => $this->timestamp,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }

    public static function fromArray(array $data): self
    {
        $timestamp = $data['timestamp'];

        if (is_array($timestamp))
        {
            $timestamp = $timestamp['date'];
        }

        $createdAt = $data['createdAt'] ?? null;

        if (is_array($createdAt))
        {
            $createdAt = $createdAt['date'];
        }

        $updatedAt = $data['updatedAt'] ?? null;

        if (is_array($updatedAt))
        {
            $updatedAt = $updatedAt['date'];
        }

        return new self(
            $data['userId'],
            $data['paymentId'],
            TransactionType::from($data['type']),
            CurrencyType::from($data['currency']),
            (float)$data['amount'],
            $data['description'],
            new DateTime($timestamp),
            $data['id'] ?? null,
            $createdAt ? new DateTime($createdAt) : null,
            $updatedAt ? new DateTime($updatedAt) : null,
        );
    }

    public function __toString(): string
    {
        $sign = "+";

        if ($this->type === TransactionType::INCOME
            || $this->type === TransactionType::REFUND) {
            $sign = "+";
        } elseif ($this->type === TransactionType::EXPENSE) {
            $sign = "-";
        }

        return sprintf(
            'Transaction: %s%s%.2f (%s) - %s',
            $sign,
            $this->currency->value,
            $this->amount,
            $this->type->value,
            $this->description,
        );
    }
}
