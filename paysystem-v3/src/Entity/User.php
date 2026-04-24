<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use InvalidArgumentException;
use RuntimeException;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use App\Trait\HasUuid;
use App\Trait\Timestampable;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable, HasUuid;

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {

    }

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
        string      $email,
        string      $password,
        string      $fullName,
        string      $phone,
        ?string     $id = null,
        ?DateTime   $createdAt = null,
        ?DateTime   $updatedAt = null,
        ?float      $balance = null,
        ?Collection $payments = null
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
        $this->payments = $payments;
    }

    public static function create(
        string $email,
        string $password,
        string $fullName,
        string $phone
    ): self
    {
        return new self(
            $email,
//            self::hashPassword($password),
            $password,
            $fullName,
            $phone,
            self::generateUuid(),
            new DateTime(),
            new DateTime(),
            0,
            new ArrayCollection()
        );
    }

//    public function toArray(): array
//    {
//        return [
//            'email' => $this->email,
//            'password' => $this->password,
//            'fullName' => $this->fullName,
//            'phone' => $this->phone,
//            'id' => $this->id,
//            'createdAt' => $this->createdAt,
//            'updatedAt' => $this->updatedAt,
//            'balance' => $this->balance,
//        ];
//    }
//
//    public static function fromArray(array $data): self
//    {
//        $createdAt = $data['createdAt'];
//
//        if (is_array($createdAt))
//        {
//            $createdAt = $createdAt['date'];
//        }
//
//        $updatedAt = $data['updatedAt'];
//
//        if (is_array($updatedAt))
//        {
//            $updatedAt = $updatedAt['date'];
//        }
//
//        return new self(
//            $data['email'],
//            $data['password'],
//            $data['fullName'],
//            $data['phone'],
//            $data['id'],
//            new DateTime($createdAt),
//            new DateTime($updatedAt),
//            (float)$data['balance'],
//        );
//    }

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

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

//    public function __serialize(): array
//    {
//        return [
//            'email' => $this->email,
//            'password' => $this->password,
//            'fullName' => $this->fullName,
//            'phone' => $this->phone,
//            'id' => $this->id,
//            'createdAt' => $this->createdAt,
//            'updatedAt' => $this->updatedAt,
//            'balance' => $this->balance,
//        ];
//    }
//
//    public function __unserialize(array $data): void
//    {
//        $this->email = $data['email'];
//        $this->password = $data['password'];
//        $this->fullName = $data['fullName'];
//        $this->phone = $data['phone'];
//        $this->id = $data['id'];
//        $this->createdAt = new DateTime($data['createdAt']);
//        $this->updatedAt = new DateTime($data['createdAt']);
//        $this->balance = (float)$data['balance'];
//    }
}