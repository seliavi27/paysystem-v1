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

$email1 = "anton@gmail.com";
$result = validate_email($email1);
echo "$email1 - " . var_export($result, true) . "</br>";

$email2 = "invalid-email";
$result = validate_email($email2);
echo "$email2 - " . var_export($result, true) . "</br>";

$email3 = "";
$result = validate_email($email3);
echo "$email3 - " . var_export($result, true) . "</br>";
echo "</br>";


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

$phone1 = "+7 (999) 123-45-67";
$result = validate_phone($phone1);
echo "$phone1 - " . var_export($result, true) . "</br>";

$phone2 = "89991234567";
$result = validate_phone($phone2);
echo "$phone2 - " . var_export($result, true) . "</br>";

$phone3 = "abc";
$result = validate_phone($phone3);
echo "$phone3 - " . var_export($result, true) . "</br>";
