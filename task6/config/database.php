<?php
declare(strict_types=1);

require_once CONFIG_PATH . '/config.php';

// Убедитесь что все папки существуют
$required_dirs = [DATA_PATH, LOGS_PATH, UPLOADS_PATH, UPLOADS_PATH . '/avatars'];
foreach ($required_dirs as $dir)
{
    if (!is_dir($dir))
    {
        mkdir($dir, 0755, true);
    }
}

// Убедитесь что все JSON файлы существуют
if (!file_exists(USERS_FILE))
{
    file_put_contents(USERS_FILE, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (!file_exists(PAYMENTS_FILE))
{
    file_put_contents(PAYMENTS_FILE, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Запустить сессию
session_start();