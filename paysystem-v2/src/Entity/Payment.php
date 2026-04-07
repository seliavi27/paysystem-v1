<?php
declare(strict_types=1);

//use Cassandra\Uuid;

class Payment
{
    use Timestampable, Loggable, HasUuid;

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
        get => $this->amount;
    }

    public string $description
    {
        get => $this->description;
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

    public PaymentMethod $type
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

    public function __construct(
        string $userId,
        float $amount,
        string $description,
        CurrencyType $currency,
        PaymentMethod $type,
        ?string $id = null,
        ?PaymentStatus $status = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    ) {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->description = $description;
        $this->currency = $currency;
        $this->type = $type;
        $this->id = $id;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        string $userId,
        float $amount,
        string $description,
        CurrencyType $currency,
        PaymentMethod $type
    ): self {
        return new self(
            $userId,
            $amount,
            $description,
            $currency,
            $type,
            self::generateUuid(),
            PaymentStatus::PENDING,
            new DateTime(),
            new DateTime()
        );
    }

    public static function fromArray(array $data): self
    {
        $createdAt = $data['createdAt'];

        if (is_array($createdAt))
        {
            $createdAt = $createdAt['date'];
        }

        $updatedAt = $data['updatedAt'];

        if (is_array($updatedAt))
        {
            $updatedAt = $updatedAt['date'];
        }

        return new self(
            $data['userId'],
            (float)$data['amount'],
            $data['description'],
            CurrencyType::from($data['currency']),
            PaymentMethod::from($data['type']),
            $data['id'],
            PaymentStatus::from($data['status']),
            new DateTime($createdAt),
            new DateTime($updatedAt)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'amount' => $this->amount,
            'description' => $this->description,
            'currency' => $this->currency,
            'status' => $this->status,
            'type' => $this->type,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
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
}