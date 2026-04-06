<?php
declare(strict_types=1);

//use Cassandra\Uuid;

class Payment
{
    public string $id
    {
        get
        {
            return $this->id;
        }
    }
    public string $userId
    {
        get
        {
            return $this->userId;
        }
        set
        {
            $this->userId = $value;
        }
    }
    public float $amount
    {
        get
        {
            return $this->amount;
        }
    }

    public CurrencyType $currency
    {
        get
        {
            return $this->currency;
        }
    }
    public PaymentStatus $status
    {
        get
        {
            return $this->status;
        }
        set
        {
            $this->status = $value;
        }
    }
    public PaymentType $type
    {
        get
        {
            return $this->type;
        }
        set
        {
            $this->type = $value;
        }
    }
    public DateTime $createdAt
    {
        get
        {
            return $this->createdAt;
        }
    }

    private function __construct(
        string $userId,
        float $amount,
        CurrencyType $currency,
        PaymentType $type,
        ?string $id = null,
        ?PaymentStatus $status = null,
        ?DateTime $createdAt = null
    ) {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->type = $type;
        $this->id = $id;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public static function create(
        string $userId,
        float $amount,
        CurrencyType $currency,
        PaymentType $type
    ): self {
        return new self(
            $userId,
            $amount,
            $currency,
            $type,
            self::generate_uuid(),
            PaymentStatus::PENDING,
            new DateTime()
        );
    }

    public static function fromArray(array $data): self
    {
        $createdAt = $data['createdAt'];

        if (is_array($createdAt)) {
            $createdAt = $createdAt['date'];
        }

        return new self(
            $data['userId'],
            (float)$data['amount'],
            CurrencyType::from($data['currency']),
            PaymentType::from($data['type']),
            $data['id'],
            PaymentStatus::from($data['status']),
            new DateTime($createdAt)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'type' => $this->type,
            'createdAt' => $this->createdAt
        ];
    }

    public function __toString(): string
    {
        return sprintf(
            'Payment #%s: %.2f %s (%s)',
            $this->id,
            $this->amount,
            $this->currency->value,
            $this->status->value
        );
    }

    public static function generate_uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}