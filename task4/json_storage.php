<?php
declare(strict_types=1);

function save_payments_to_json(array $payments, string $filepath): bool
{
    $json = json_encode($payments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($filepath, $json) !== false;
    return $result;
}