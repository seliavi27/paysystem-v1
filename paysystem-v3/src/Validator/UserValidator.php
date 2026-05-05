<?php
declare(strict_types=1);

namespace App\Validator;

class UserValidator
{
    public static function validateRegistrationForm(array $data): array
    {
        $errors = [];

        $required = ['email', 'password', 'passwordConfirm', 'fullName', 'phone'];

        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[$field] = 'This field is required';
            }
        }

        if (!empty($data['email']) && !self::validateEmailFormat($data['email'])) {
            $errors['email'] = 'Invalid email format';
        }

        if (!empty($data['password']) && !self::validatePasswordStrength($data['password'])) {
            $errors['password'] = 'The password must be at least 6 characters long and contain letters and numbers';
        }

        if (!empty($data['password']) && !empty($data['passwordConfirm']) &&
            $data['password'] !== $data['passwordConfirm']) {
            $errors['passwordConfirm'] = "The passwords don't match";
        }

        if (!empty($data['phone']) && !self::validatePhoneFormat($data['phone'])) {
            $errors['phone'] = 'Invalid phone format';
        }

        if (!empty($data['email']) && self::userExists($data['email'])) {
            $errors['email'] = 'A user with this email already exists';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public static function validateEmailFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePasswordStrength(string $password): bool
    {
        return strlen($password) >= 6
            && (preg_match('/[A-Za-z]/', $password) === 1)
            && (preg_match('/\d/', $password) === 1);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function validatePhoneFormat(string $phone): bool
    {
        return (preg_match('/^\+?\d{7,15}$/', $phone) === 1);
    }
}