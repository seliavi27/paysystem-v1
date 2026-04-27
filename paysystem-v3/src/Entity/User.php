<?php
declare(strict_types=1);

namespace PaySystem\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use RuntimeException;

use PaySystem\Trait\HasUuid;
use PaySystem\Trait\Timestampable;
use PaySystem\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    use Timestampable, HasUuid;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public string $email
    {
        get => $this->email;
        set => $this->email = $value;
    }

    #[ORM\Column(type: 'string', length: 255)]
    public string $password
    {
        get => $this->password;
        set => $this->password = $value;
    }

    #[ORM\Column(name: 'full_name', type: 'string', length: 255)]
    public string $fullName
    {
        get => $this->fullName;
        set => $this->fullName = $value;
    }

    #[ORM\Column(type: 'string', length: 32, options: ['default' => ''])]
    public string $phone
    {
        get => $this->phone;
        set => $this->phone = $value;
    }

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2, options: ['default' => 0])]
    public float $balance
    {
        get => $this->balance;
        set => $this->balance = $value;
    }

    /** @var Collection<int, Payment> */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'user', cascade: ['persist'])]
    public Collection $payments
    {
        get => $this->payments;
    }

    public function __construct(
        string  $email,
        string  $password,
        string  $fullName,
        string  $phone = '',
        ?string $id = null,
    ) {
        $this->email    = $email;
        $this->password = $password;
        $this->fullName = $fullName;
        $this->phone    = $phone;
        $this->balance  = 0.0;
        $this->payments = new ArrayCollection();

        if ($id !== null) {
            $this->id = $id;
        } else {
            $this->initializeUuid();
        }
        $this->initializeTimestamps();
    }

    public static function create(
        string $email,
        string $password,
        string $fullName,
        string $phone = '',
    ): self {
        return new self(
            email:    $email,
            password: self::hashPassword($password),
            fullName: $fullName,
            phone:    $phone,
        );
    }

    public function __toString(): string
    {
        return sprintf('User %s: (%s)', $this->fullName, $this->email);
    }

    public function addBalance(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        $this->balance += $amount;
        $this->touch();
    }

    public function deductBalance(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0');
        }

        if ($this->balance < $amount) {
            throw new RuntimeException('Insufficient funds');
        }

        $this->balance -= $amount;
        $this->touch();
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
