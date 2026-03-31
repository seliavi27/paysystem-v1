<?php
declare(strict_types=1);

require_once 'security.php';

function ensure_users_storage(string $usersFile = 'data/users.json'): bool
{
    $dir = dirname($usersFile);

    if (!is_dir($dir))
    {
        if (!mkdir($dir, 0755, true) && !is_dir($dir))
        {
            return false;
        }
    }

    if (!file_exists($usersFile))
    {
        $result = file_put_contents($usersFile, json_encode([], JSON_PRETTY_PRINT));

        if ($result === false) {
            return false;
        }
    }

    return true;
}

function validateRegistrationForm(array $data): array
{
    $errors = [];

    $required = ['email', 'password', 'passwordConfirm', 'fullName', 'phone'];

    foreach ($required as $field)
    {
        if (empty(trim($data[$field] ?? '')))
        {
            $errors[$field] = 'This field is required';
        }
    }

    if (!empty($data['email']) && !validateEmailFormat($data['email']))
    {
        $errors['email'] = 'Invalid email format';
    }

    if (!empty($data['password']) && !validatePasswordStrength($data['password']))
    {
        $errors['password'] = 'The password must be at least 6 characters long and contain letters and numbers';
    }

    if (!empty($data['password']) && !empty($data['passwordConfirm']) &&
        $data['password'] !== $data['passwordConfirm'])
    {
        $errors['passwordConfirm'] = "The passwords don't match";
    }

    if (!empty($data['phone']) && !validatePhoneFormat($data['phone']))
    {
        $errors['phone'] = 'Invalid phone format';
    }

    if (!empty($data['email']) && userExists($data['email']))
    {
        $errors['email'] = 'A user with this email already exists';
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

function validateEmailFormat(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePasswordStrength(string $password): bool
{
    return strlen($password) >= 6
        && (preg_match('/[A-Za-z]/', $password) === 1)
        && (preg_match('/\d/', $password) === 1);
}

function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function validatePhoneFormat(string $phone): bool
{
    return (preg_match('/^\+?\d{7,15}$/', $phone) === 1);
}

function userExists(string $email, string $usersFile = 'data/users.json'): bool
{
    if (!is_file($usersFile))
    {
        return false;
    }

    $users = json_decode(file_get_contents($usersFile), true);

    if (!is_array($users))
    {
        return false;
    }

    foreach ($users as $user)
    {
        if (isset($user['email']) && strtolower($user['email']) === strtolower($email))
        {
            return true;
        }
    }

    return false;
}