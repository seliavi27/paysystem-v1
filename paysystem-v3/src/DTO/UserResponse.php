<?php
declare(strict_types=1);

namespace PaySystem\DTO;

use PaySystem\Entity\User;
use DateTime;
use PaySystem\Trait\HasUuid;
use PaySystem\Trait\Timestampable;

final class UserResponse
{
    use Timestampable, HasUuid;

    public string $email
    {
        get => $this->email;
    }

    public string $fullName
    {
        get => $this->fullName;
    }

    public string $phone
    {
        get => $this->phone;
    }

    public float $balance
    {
        get => $this->balance;
    }

    public function __construct(
        string    $email,
        string    $fullName,
        string    $phone,
        ?string   $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        ?float    $balance = null
    )
    {
        $this->email = $email;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->balance = $balance;
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            email: $user->email,
            fullName: $user->fullName,
            phone: $user->phone,
            id: $user->id,
            createdAt: $user->createdAt,
            updatedAt: $user->updatedAt,
            balance: $user->balance
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'fullName' => $this->fullName,
            'phone' => $this->phone,
            'id' => $this->id,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'balance' => $this->balance,
        ];
    }
}