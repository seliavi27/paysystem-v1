<?php
declare(strict_types=1);

//Проверить формат email
function validateEmail(string $email): bool
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
$result = validateEmail($email1);
echo "$email1 - " . var_export($result, true) . "</br>";

$email2 = "invalid-email";
$result = validateEmail($email2);
echo "$email2 - " . var_export($result, true) . "</br>";

$email3 = "";
$result = validateEmail($email3);
echo "$email3 - " . var_export($result, true) . "</br>";