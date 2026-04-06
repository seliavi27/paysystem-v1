<?php
declare(strict_types=1);

class Transaction
{
    use Timestampable, Loggable, HasUuid;

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

    public string $description
    {
        get => $this->description;
        set => $this->description = $value;
    }

    public function __construct(
        string $userId,
        string $paymentId,
        TransactionType $type,
        CurrencyType $currency,
        float $amount,
        string $description,
        DateTime $createdAt,
        DateTime $updatedAt
    )
    {
        $this->id = self::generateUuid();
        $this->userId = $userId;
        $this->paymentId = $paymentId;
        $this->type = $type;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }

    public function __toString(): string
    {
        $sign = "+";

        if ($this->type === TransactionType::INCOME
            || $this->type === TransactionType::REFUND)
        {
            $sign = "+";
        }
        elseif ($this->type === TransactionType::EXPENSE)
        {
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