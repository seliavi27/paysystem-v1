<?php
declare(strict_types=1);

namespace PaySystem\Entity;

use Doctrine\ORM\Mapping as ORM;

use PaySystem\Trait\HasUuid;
use PaySystem\Trait\Timestampable;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Enum\CurrencyType;

#[ORM\Entity(repositoryClass: \PaySystem\Repository\PaymentRepository::class)]
#[ORM\Table(name: 'payments')]
#[ORM\Index(name: 'idx_payments_user_status', columns: ['user_id', 'status'])]
#[ORM\Index(name: 'idx_payments_status', columns: ['status'])]
class Payment
{
    use Timestampable, HasUuid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public User $user
    {
        get => $this->user;
    }

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
        User          $user,
        float         $amount,
        string        $description,
        CurrencyType  $currency,
        PaymentMethod $method,
    ) {
        $this->user        = $user;
        $this->amount      = $amount;
        $this->description = $description;
        $this->currency    = $currency;
        $this->method      = $method;
        $this->status      = PaymentStatus::PENDING;

        $this->initializeUuid();
        $this->initializeTimestamps();
    }

    public static function create(
        User          $user,
        float         $amount,
        string        $description,
        CurrencyType  $currency,
        PaymentMethod $method,
    ): self {
        return new self($user, $amount, $description, $currency, $method);
    }

    public function __toString(): string
    {
        return sprintf(
            'Payment #%s: %.2f %s (%s)',
            $this->id,
            $this->amount,
            $this->currency->value,
            $this->status->value,
        );
    }
}
