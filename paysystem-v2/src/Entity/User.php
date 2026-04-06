<?php
declare(strict_types=1);

class User
{
    public string $id
    {
        get
        {
            return $this->id;
        }
    }
    public string $email
    {
        get
        {
            return $this->email;
        }
        set
        {
            if (!validateEmailFormat($value))
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
            if (!empty($value) && !validatePasswordStrength($value))
            {
                throw new InvalidArgumentException('Invalid password format');
            }

            $this->password = $value;
        }
    }
    public string $fullName
    {
        get
        {
            return $this->fullName;
        }
        set
        {
            $this->fullName = $value;
        }
    }
    public string $phone
    {
        get
        {
            return $this->phone;
        }
        set
        {
            $this->phone = $value;
        }
    }
    public DateTime $createdAt
    {
        get
        {
            return $this->createdAt;
        }
        set
        {
            $this->createdAt = $value;
        }
    }
    public float $balance
        {
            get
            {
                return $this->balance;
            }
            set
            {
                $this->balance = $value;
            }
        }

    public function __construct(
        string $email,
        string $password,
        string $fullName,
        string $phone,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?float $balance = null
    )
    {
//        $this->id = Uuid::uuid4()->toString();
        $this->email = $email;
        $this->password = $password;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->id = $id;
        $this->createdAt = $createdAt;
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
            self::generate_uuid(),
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
            'balance' => $this->balance,
        ];
    }

    public static function fromArray(array $data): self
    {
        $createdAt = $data['createdAt'];

        if (is_array($createdAt)) {
            $createdAt = $createdAt['date'];
        }

        return new self(
            $data['email'],
            $data['password'],
            $data['fullName'],
            $data['phone'],
            $data['id'],
            new DateTime($createdAt),
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
    }

    public static function generate_uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}