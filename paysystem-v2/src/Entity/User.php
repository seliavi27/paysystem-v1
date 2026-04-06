<?php
declare(strict_types=1);

class User
{
    use Timestampable, Loggable, HasUuid;

    public string $email
    {
        get
        {
            return $this->email;
        }
        set
        {
            if (!UserValidator::validateEmailFormat($value))
            {
                throw new InvalidArgumentException('Invalid email format');
            }

            $this->email = $value;
        }
    }

    public string $password
    {
        get
        {
            return $this->password;
        }
        set
        {
            if (!empty($value) && !UserValidator::validatePasswordStrength($value))
            {
                throw new InvalidArgumentException('Invalid password format');
            }

            $this->password = $value;
        }
    }

    public string $fullName
    {
        get => $this->fullName;
        set => $this->fullName = $value;
    }

    public string $phone
    {
        get => $this->phone;
        set => $this->phone = $value;
    }

    public float $balance
    {
        get => $this->balance;
        set => $this->balance = $value;
    }

    public function __construct(
        string $email,
        string $password,
        string $fullName,
        string $phone,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        ?float $balance = null
    )
    {
        $this->email = $email;
        $this->password = $password;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->balance = $balance;
    }

    public static function create(
        string $email,
        string $password,
        string $fullName,
        string $phone
    ): self {
        return new self(
            $email,
            $password,
            $fullName,
            $phone,
            self::generateUuid(),
            new DateTime(),
            new DateTime(),
            0
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'fullName' => $this->fullName,
            'phone' => $this->phone,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'balance' => $this->balance,
        ];
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
            $data['email'],
            $data['password'],
            $data['fullName'],
            $data['phone'],
            $data['id'],
            new DateTime($createdAt),
            new DateTime($updatedAt),
            (float)$data['balance'],
        );
    }

    public function __toString(): string
    {
        return sprintf(
            'User %s: (%s)',
            $this->fullName,
            $this->email
        );
    }

    public function addBalance(float $amount): void
    {
        if ($amount <= 0)
        {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        $this->balance += $amount;
        $this->log("Add to balance: $amount");
    }

    public function deductBalance(float $amount): void
    {
        if ($amount <= 0)
        {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        if ($this->balance < $amount)
        {
            throw new RuntimeException('Insufficient funds');
        }

        $this->balance -= $amount;
        $this->log("Deduct from balance: $amount");
    }
}