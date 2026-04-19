<?php
declare(strict_types=1);

namespace PaySystem\DTO;

use PaySystem\Exception\ValidationException;
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
            'fullName' => $this->fullName,
            'phone' => $this->phone,
        ];
    }

    private function validate(): void
    {
        if (!UserValidator::validateEmailFormat($this->email))
        {
            throw new ValidationException('Invalid email format');
        }

        if (!UserValidator::validatePasswordStrength($this->password))
        {
            throw new ValidationException('Password must be at least 6 characters and contain letters and numbers');
        }

        if ($this->password !== $this->passwordConfirm)
        {
            throw new ValidationException("Passwords don't match");
        }

        if ($this->phone !== '' && !UserValidator::validatePhoneFormat($this->phone))
        {
            throw new ValidationException('Invalid phone format');
        }

        if (trim($this->fullName) === '') {
            throw new ValidationException('Full name is required');
        }
    }
}
