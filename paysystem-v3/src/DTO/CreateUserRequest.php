<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 6)]
        public string $password,

        #[Assert\NotBlank]
        public string $passwordConfirm,

        #[Assert\NotBlank]
        public string $fullName,

        #[Assert\Regex('/^\+?\d{7,15}$/', message: 'Некорректный телефон')]
        public string $phone = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'email'    => $this->email,
            'fullName' => $this->fullName,
            'phone'    => $this->phone,
        ];
    }
}
