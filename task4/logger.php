<?php
declare(strict_types=1);

function log_operation(
    string $action,
    string $details,
    string $log_file = 'logs/operations.log'): bool
{
    $dir  = dirname($log_file);

    if (!is_dir($dir))
    {
        if (!mkdir($dir, 0777, true) || !is_dir($dir))
        {
            return false;
        }
    }

    $dateTime = date('Y-m-d H:i:s');
    $logLine = sprintf("[%s] %s: %s%s", $dateTime, $action, $details, PHP_EOL);
    $writeResult = file_put_contents($log_file, $logLine, FILE_APPEND);

    if ($writeResult === false)
    {
        return false;
    }

    return true;
}

function log_error(
    string $message,
    string $log_file = 'logs/errors.log'): bool
{
    $dir  = dirname($log_file);

    if (!is_dir($dir))
    {
        if (!mkdir($dir, 0777, true) || !is_dir($dir))
        {
            return false;
        }
    }

    $dateTime = date('Y-m-d H:i:s');
    $logLine = sprintf("[%s] ERROR: %s%s", $dateTime, $message, PHP_EOL);
    $writeResult = file_put_contents($log_file, $logLine, FILE_APPEND);

    if ($writeResult === false)
    {
        return false;
    }

    return true;
}

function get_logs(
    string $log_file,
    int $limit = 50): array
{
    $logs = [];

    if (!file_exists($log_file) || !is_readable($log_file))
    {
        return [];
    }

    $handle = fopen($log_file, 'r');

    if ($handle === false)
    {
        return $logs;
    }

    while ((($log = fgets($handle)) !== false) && (count($logs) < $limit))
    {
        $logs[] = trim($log);
    }

    fclose($handle);

    return $logs;
}