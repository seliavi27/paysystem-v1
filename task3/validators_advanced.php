<?php
declare(strict_types=1);

function luna_algorithm(string $card_number): bool
{
    $result = false;
    $length = mb_strlen($card_number);
    $sum = 0;
    $isSecond = false;

    for ($i = $length - 1; $i >= 0; $i--)
    {
        $digit = (int)$card_number[$i];

        if ($isSecond)
        {
            $digit *= 2;

            if ($digit > 9)
            {
                $digit -= 9;
            }
        }

        $sum += $digit;
        $isSecond = !$isSecond;
    }

    if ($sum % 10 == 0)
    {
        $result = true;
    }

    return $result;
}

function validate_credit_card(string $card_number): bool
{
    $onlyDigits = str_replace(' ', '', $card_number);

    if (!preg_match('/^[0-9]+$/', $onlyDigits))
    {
        return false;
    }

    if (mb_strlen($onlyDigits) != 16)
    {
        return false;
    }

    $result = luna_algorithm($onlyDigits);

    return $result;
}

function validate_iban(string $iban): bool
{
    $iban = strtoupper($iban);
    $length = mb_strlen($iban);

    if (($length < 5) || ($length > 34))
    {
        return false;
    }

    $regular = '/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/';

    if (!preg_match($regular, $iban))
    {
        return false;
    }

    $iban = substr($iban, 4) . substr($iban, 0, 4);

    $iban = preg_replace_callback('/[A-Z]/', function($match) {
        return ord($match[0]) - ord('A') + 10;
    }, $iban);

    $result =  bcmod($iban, '97') == '1';

    return $result;
}


function extract_urls(string $text): array
{
    $urls = [];
    $regular = '/\b(?:https?:\/\/|ftp:\/\/|www\.)[^\s<>"\'{}|\\^`\[\]]+/i';
    preg_match_all($regular, $text, $urls);

    return $urls;
}


function mask_sensitive_data(string $data, string $type = 'card'): string
{
    switch ($type)
    {
        case 'card':
            return mask_card($data);
        case 'email':
            return mask_email($data);
        default:
            return $data;
    }
}

function mask_card(string $card): string
{
    if (strlen($card) < 4)
    {
        return $card;
    }

    $last4 = substr($card, -4);

    $maskLength = strlen($card) - 4;
    $mask = str_repeat('*', $maskLength);
    $result = $mask . $last4;
    $result = implode(' ', str_split($result, 4));
    $result = trim($result);

    return $result;
}

function mask_email(string $email): string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        return $email;
    }

    list($local, $domain) = explode('@', $email, 2);
    $localLength = strlen($local);

    if ($localLength <= 3)
    {
        $maskedLocal = $local[0] . str_repeat('*', $localLength - 1);
    }
    else
    {
        $visible = min(3, $localLength - 2);
        $maskedLocal = substr($local, 0, $visible) . str_repeat('*', $localLength - $visible);
    }

    return $maskedLocal . '@' . $domain;
}