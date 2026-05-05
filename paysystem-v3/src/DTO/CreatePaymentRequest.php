<?php
declare(strict_types=1);

namespace App\DTO;

use App\Enum\CurrencyType;
use App\Enum\PaymentMethod;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreatePaymentRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $userId,

        #[Assert\Positive]
        public float $amount,

        #[Assert\NotBlank]
        #[Assert\Length(max: 500)]
        public string $description,

        public CurrencyType $currency,

        public PaymentMethod $method,
    ) {
    }

    public function toArray(): array
    {
        return [
            'userId'      => $this->userId,
            'amount'      => $this->amount,
            'description' => $this->description,
            'currency'    => $this->currency->value,
            'method'      => $this->method->value,
        ];
    }
}
