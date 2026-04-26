<?php
declare(strict_types=1);

function log_operation(
    string $action,
    string $details): bool
{
    $dir  = dirname(OPERATIONS_LOG);

    if (!is_dir($dir))
    {
        if (!mkdir($dir, 0777, true) || !is_dir($dir))
        {
            return false;
        }
    }

    $dateTime = date('Y-m-d H:i:s');
    $logLine = sprintf("[%s] %s: %s%s", $dateTime, $action, $details, PHP_EOL);
    $writeResult = file_put_contents(OPERATIONS_LOG, $logLine, FILE_APPEND);

    if ($writeResult === false)
    {
        return false;
    }

    return true;
}

function log_error(
    string $message): bool
{
    $dir  = dirname(ERRORS_LOG);

    if (!is_dir($dir))
    {
        if (!mkdir($dir, 0777, true) || !is_dir($dir))
        {
            return false;
        }
    }

    $dateTime = date('Y-m-d H:i:s');
    $logLine = sprintf("[%s] ERROR: %s%s", $dateTime, $message, PHP_EOL);
    $writeResult = file_put_contents(ERRORS_LOG, $logLine, FILE_APPEND);

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