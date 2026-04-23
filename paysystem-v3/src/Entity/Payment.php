<?php
declare(strict_types=1);

namespace PaySystem\Entity;

//use Cassandra\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

use PaySystem\Trait\HasUuid;
use PaySystem\Trait\Loggable;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Enum\CurrencyType;
use PaySystem\Trait\Timestampable;

#[ORM\Entity(repositoryClass: \PaySystem\Repository\PaymentRepository::class)]
#[ORM\Table(name: 'payments')]
#[ORM\Index(name: 'idx_payments_user_status', columns: ['user_id', 'status'])]
#[ORM\Index(name: 'idx_payments_status', columns: ['status'])]
class Payment
{
    use Timestampable, Loggable, HasUuid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public User $user
    {
        get => $this->user;
    }

//    public string $userId
//    {
//        get => $this->userId;
//        set => $this->userId = $value;
//    }

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    public float $amount
    {
        get => $this->amount;
    }

    #[ORM\Column(type: 'text', options: ['default' => ''])]
    public string $description
    {
        get => $this->description;
    }

    #[ORM\Column(type: 'string', length: 3, enumType: CurrencyType::class)]
    public CurrencyType $currency
    {
        get => $this->currency;
    }

    #[ORM\Column(type: 'string', length: 16, enumType: PaymentStatus::class)]
    public PaymentStatus $status
    {
        get => $this->status;
        set => $this->status = $value;
    }

    #[ORM\Column(type: 'string', length: 32, enumType: PaymentMethod::class)]
    public PaymentMethod $method
    {
        get => $this->method;
        set => $this->method = $value;
    }

    public function __construct(
        User           $user,
        float          $amount,
        string         $description,
        CurrencyType   $currency,
        PaymentMethod  $method,
        ?string        $id = null,
        ?PaymentStatus $status = null,
        ?DateTime      $createdAt = null,
        ?DateTime      $updatedAt = null
    )
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->description = $description;
        $this->currency = $currency;
        $this->method = $method;
        $this->id = $id;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        User          $user,
        float         $amount,
        string        $description,
        CurrencyType  $currency,
        PaymentMethod $method
    ): self
    {
        return new self(
            $user,
            $amount,
            $description,
            $currency,
            $method,
            self::generateUuid(),
            PaymentStatus::PENDING,
            new DateTime(),
            new DateTime()
        );
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
    public function markCompleted(): void
    {
        $this->status = PaymentStatus::COMPLETED;
        $this->updatedAt = new DateTime();
    }
    public function markFailed(): void
    {
        $this->status = PaymentStatus::FAILED;
        $this->updatedAt = new DateTime();
    }
    public function markRefunded(): void
    {
        $this->status = PaymentStatus::REFUNDED;
        $this->updatedAt = new DateTime();
    }
    public function markProcessing(): void
    {
        $this->status = PaymentStatus::PROCESSING;
        $this->updatedAt = new DateTime();
    }

//    public static function fromArray(array $data): self
//    {
//        $createdAt = $data['createdAt'];
//
//        if (is_array($createdAt)) {
//            $createdAt = $createdAt['date'];
//        }
//
//        $updatedAt = $data['updatedAt'];
//
//        if (is_array($updatedAt)) {
//            $updatedAt = $updatedAt['date'];
//        }
//
//        return new self(
//            $data['userId'],
//            (float)$data['amount'],
//            $data['description'],
//            CurrencyType::from($data['currency']),
//            PaymentMethod::from($data['method']),
//            $data['id'],
//            PaymentStatus::from($data['status']),
//            new DateTime($createdAt),
//            new DateTime($updatedAt)
//        );
//    }
//
//    public function toArray(): array
//    {
//        return [
//            'id' => $this->id,
//            'userId' => $this->userId,
//            'amount' => $this->amount,
//            'description' => $this->description,
//            'currency' => $this->currency,
//            'status' => $this->status,
//            'method' => $this->method,
//            'createdAt' => $this->createdAt,
//            'updatedAt' => $this->updatedAt
//        ];
//    }

//    public function __serialize(): array
//    {
//        return [
//            'id' => $this->id,
//            'userId' => $this->userId,
//            'amount' => $this->amount,
//            'description' => $this->description,
//            'currency' => $this->currency,
//            'status' => $this->status,
//            'method' => $this->method,
//            'createdAt' => $this->createdAt,
//            'updatedAt' => $this->updatedAt
//        ];
//    }
//
//    public function __unserialize(array $data): void
//    {
//        $this->userId = $data['userId'];
//        $this->amount = (float)$data['amount'];
//        $this->description = $data['description'];
//        $this->currency = CurrencyType::from($data['currency']);
//        $this->method = PaymentMethod::from($data['method']);
//        $this->id = $data['id'];
//        $this->status = PaymentStatus::from($data['status']);
//        $this->createdAt = new DateTime($data['createdAt']);
//        $this->updatedAt = new DateTime($data['createdAt']);
//    }

}