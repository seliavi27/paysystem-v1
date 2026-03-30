<?php
declare(strict_types=1);

function ensure_directory_exists(string $path): bool
{
    if (!is_dir($path))
    {
        if (!mkdir($path, 0755, true))
        {
            return false;
        }
    }

    return true;
}

function list_files_in_directory(string $path, string $extension = ''): array
{
    $result = [];

    if (!is_dir($path))
    {
        return $result;
    }

    $files = scandir($path);

    foreach ($files as $file)
    {
        if ($file === '.' || $file === '..')
        {
            continue;
        }

        $fullPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;

        if (!is_file($fullPath)) {
            continue;
        }

        if ($extension !== '') {
            if (pathinfo($file, PATHINFO_EXTENSION) !== ltrim($extension, '.')) {
                continue;
            }
        }

        $result[] = $file;
    }

    return $result;
}

function get_file_info(string $filepath): array|false
{
    if (!file_exists($filepath) || !is_file($filepath)) {
        return false;
    }

    $type = function_exists('mime_content_type')
        ? mime_content_type($filepath)
        : 'unknown';

    return [
        'name' => basename($filepath),
        'size' => filesize($filepath),
        'modified' => date('Y-m-d H:i:s', filemtime($filepath)),
        'permissions' => substr(sprintf('%o', fileperms($filepath)), -4),
        'type' => $type,
        'is_readable' => is_readable($filepath),
        'is_writable' => is_writable($filepath),
    ];
}

function backup_file(string $source, string $backup_dir = 'backups'): string|false
{
    if (!file_exists($source) || !is_file($source)) {
        return false;
    }

    if (!is_dir($backup_dir))
    {
        if (!mkdir($backup_dir, 0755, true) && !is_dir($backup_dir))
        {
            return false;
        }
    }

    $filename = basename($source);
    $timestamp = date('Y-m-d_H-i-s');

    $backupPath = rtrim($backup_dir, DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR
        . $filename . '.' . $timestamp . '.bak';

    if (!copy($source, $backupPath))
    {
        return false;
    }

    return $backupPath;
}