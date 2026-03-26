<?php
declare(strict_types=1);

function save_payments_to_json(array $payments, string $filepath): bool
{
    $json = json_encode($payments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($filepath, $json) !== false;
    return $result;
}

function load_payments_from_json(string $filepath): array
{
    $payments = [];
    $json = file_get_contents($filepath);

    if ($json !== false)
    {
        $payments = json_decode($json, true);
    }

    return $payments;
}

function add_payment_to_storage(array $payment, string $storage_dir): bool
{
    $payments = load_payments_from_json($storage_dir);
    $ids = array_column($payments, 'id');
    $payment['id'] = max($ids) + 1;
    $payments[] = $payment;
    $result = save_payments_to_json($payments, $storage_dir);

    return $result;
}


function update_payment_status(
    int $payment_id, string $new_status, string $storage_dir): bool
{
    $found = false;
    $payments = load_payments_from_json($storage_dir);

    foreach ($payments as $key => $payment)
    {
        if (isset($payment['id']) && ($payment['id'] === $payment_id))
        {
            $payments[$key]['status'] = $new_status;
            $found = true;
        }
    }

    $save = save_payments_to_json($payments, $storage_dir);
    $result = $found && $save;

    return $result;
}