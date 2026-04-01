<?php
declare(strict_types=1);

function export_payments_to_csv(array $payments, string $output_file): bool
{
    $handle = fopen($output_file, 'w');

    if ($handle === false)
    {
        return false;
    }

    $headers = array_keys($payments[0]);

    if (fputcsv($handle, $headers, ',', '"', '\\') === false)
    {
        fclose($handle);
        return false;
    }

    foreach ($payments as $payment)
    {
        $values = array_map(function($value) {
            return $value !== null ? (string)$value : '';
        }, $payment);

        if (fputcsv($handle, $values, ',', '"', '\\') === false)
        {
            fclose($handle);
            return false;
        }
    }

    fclose($handle);

    return true;
}


function import_transactions_from_csv(string $csv_file): array
{
    $payments = [];

    if (!file_exists($csv_file) || !is_readable($csv_file))
    {
        return $payments;
    }

    if (($handle = fopen("$csv_file", "r")) !== false)
    {
        $headers = fgetcsv($handle, 0, ',', '"', '\\');

        if ($headers === false)
        {
            fclose($handle);
            return $payments;
        }

        while (($data = fgetcsv($handle, 0, ',', '"', '\\'))
            !== false)
        {
            if (count($data) !== 0)
            {
                $payments[] = array_combine($headers, $data);
            }
        }

        fclose($handle);
    }

    return $payments;
}

function validate_csv_structure(string $csv_file, array $required_columns): bool
{
    if (!file_exists($csv_file) || !is_readable($csv_file))
    {
        return false;
    }

    $handle = fopen("$csv_file", "r");

    if ($handle === false)
    {
        return false;
    }

    $headers = fgetcsv($handle, 0, ',', '"', '\\');
    fclose($handle);

    if ($headers === false)
    {
        return false;
    }

    $result = true;

    foreach ($required_columns as $column)
    {
        if (!in_array($column, $headers))
        {
            $result = false;
        }
    }

    return $result;
}