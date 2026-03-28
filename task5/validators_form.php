<?php
declare(strict_types=1);

function ensure_users_storage(string $users_file = 'data/users.json'): bool
{
    $dir = dirname($users_file);

    if (!is_dir($dir))
    {
        if (!mkdir($dir, 0755, true) && !is_dir($dir))
        {
            return false;
        }
    }

    if (!file_exists($users_file))
    {
        $result = file_put_contents($users_file, json_encode([], JSON_PRETTY_PRINT));

        if ($result === false) {
            return false;
        }
    }

    return true;
}

function validate_registration_form(array $data): array
{
    $errors = [];

    $required = ['email', 'password', 'password_confirm', 'full_name', 'phone'];
    foreach ($required as $field)
    {
        if (empty(trim($data[$field] ?? '')))
        {
            $errors[$field] = 'This field is required';
        }
    }

    if (!empty($data['email']) && !validate_email_format($data['email']))
    {
        $errors['email'] = 'Invalid email format';
    }

    if (!empty($data['password']) && !validate_password_strength($data['password']))
    {
        $errors['password'] = 'The password must be at least 6 characters long and contain letters and numbers';
    }

    if (!empty($data['password']) && !empty($data['password_confirm']) &&
        $data['password'] !== $data['password_confirm'])
    {
        $errors['password_confirm'] = "The passwords don't match";
    }

    if (!empty($data['phone']) && !validate_phone_format($data['phone']))
    {
        $errors['phone'] = 'Invalid phone format';
    }

    if (!empty($data['email']) && user_exists($data['email']))
    {
        $errors['email'] = 'A user with this email already exists';
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

function validate_email_format(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_password_strength(string $password): bool
{
    return strlen($password) >= 6
        && (preg_match('/[A-Za-z]/', $password) === 1)
        && (preg_match('/\d/', $password) === 1);
}

function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function validate_phone_format(string $phone): bool
{
    return (preg_match('/^\+?\d{7,15}$/', $phone) === 1);
}

function user_exists(string $email, string $users_file = 'data/users.json'): bool
{
    if (!is_file($users_file))
    {
        return false;
    }

    $users = json_decode(file_get_contents($users_file), true);

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