<?php
declare(strict_types=1);

class Transaction
{
    public string $id
    {
        get
        {
            return $this->id;
        }
    }

    private string $userId;
    private string $paymentId;
    private TransactionType $type;
    public CurrencyType $currency
    {
        get
        {
            return $this->currency;
        }
    }
    private float $amount;
    private string $description;
    public DateTime $timestamp
    {
        get
        {
            return $this->timestamp;
        }
    }

    public function __construct(
        string $userId,
        string $paymentId,
        TransactionType $type,
        CurrencyType $currency,
        float $amount,
        string $description
    )
    {
//        $this->id = Uuid::uuid4()->toString();
        $this->id = $this->generate_uuid();
        $this->userId = $userId;
        $this->paymentId = $paymentId;
        $this->type = $type;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->description = $description;
        $this->timestamp = new DateTime();
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
            'timestamp' => $this->timestamp
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

    function generate_uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}