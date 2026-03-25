<?php
declare(strict_types=1);

//Проверить формат email
function validate_email(string $email): bool
{
    $result = false;

    if ((mb_strlen($email) <= 255)
        && (filter_var($email, FILTER_VALIDATE_EMAIL) !== true))
    {
        $result = true;
    }

    return $result;
}

// Проверить формат номера телефона
function validate_phone(string $phone): bool
{
    $phone = str_replace(' ', '', $phone);
    $result = false;
    $pattern = '/^[0-9+\-()]+$/';

    if ((preg_match_all('/[0-9]/', $phone) >= 10)
        && (preg_match($pattern, $phone) === 1))
    {
        $result = true;
    }

    return $result;
}

