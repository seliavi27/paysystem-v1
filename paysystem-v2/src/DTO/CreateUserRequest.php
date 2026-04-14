<?php
declare(strict_types=1);

namespace PaySystem\DTO;

use InvalidArgumentException;
use PaySystem\Validator\UserValidator;

final readonly class CreateUserRequest
{
    public function __construct(
        public string $email,
        public string $password,
        public string $passwordConfirm,
        public string $fullName,
        public string $phone
    )
    {
        $this->validate();
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'fullName' => $this->fullName,
            'phone' => $this->phone
        ];
    }

    private function validate(): void
    {
        if (!empty($email) && !UserValidator::validateEmailFormat($email))
        {
            throw new InvalidArgumentException('Invalid email format');
        }

        if (!empty($password) && !UserValidator::validatePasswordStrength($password))
        {
            throw new InvalidArgumentException('The password must be at least 6 characters long and contain letters and numbers');
        }

        if (!empty($password) && !empty($passwordConfirm) &&
            $password !== $passwordConfirm)
        {
            throw new InvalidArgumentException("The passwords don't match");
        }

        if (!empty($phone) && !UserValidator::validatePhoneFormat($phone))
        {
            throw new InvalidArgumentException("Invalid phone format");
        }
    }
}
