<?php
declare(strict_types=1);

function setFlashMessage(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function calculatePaymentStats(array $payments): array
{
    $count = count($payments);
    $total = array_sum(array_map(fn($p) => $p->amount, $payments));
    $average = $count > 0 ? $total / $count : 0;

    $byStatus = [];

    foreach ($payments as $payment)
    {
        $status = $payment->status->value ?? 'unknown';

        if (!isset($byStatus[$status]))
        {
            $byStatus[$status] = 0;
        }

        $byStatus[$status]++;
    }

    usort($payments, function ($a, $b) {
        return $b->createdAt <=> $a->createdAt;
    });

    $lastPayments = array_slice($payments, 0, 5);

    return [
        'count' => $count,
        'total' => $total,
        'average' => $average,
        'byStatus' => $byStatus,
        'lastPayments' => $lastPayments,
    ];
}

//-----------------image-------------------------

function validateUploadedFile(array $file, string $type = 'image'): array
{
    if ($file['error'] !== UPLOAD_ERR_OK)
    {
        return ['valid' => false, 'error' => 'File upload error'];
    }

    if ($file['size'] > 2 * 1024 * 1024)
    {
        return ['valid' => false, 'error' => 'File is too large (max 2MB)'];
    }

    if ($type === 'image')
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        $mime = mime_content_type($file['tmp_name']);

        if (!in_array($mime, $allowed, true))
        {
            return ['valid' => false, 'error' => 'Only images (jpg, png, gif)'];
        }
    }

    return ['valid' => true, 'error' => ''];
}

function saveUploadedFile(array $file, string $upload_dir, string $new_name): string|false
{
    if (!is_dir($upload_dir))
    {
        mkdir($upload_dir, 0755, true);
    }

    $destination = rtrim($upload_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $new_name;

    if (move_uploaded_file($file['tmp_name'], $destination))
    {
        return AVATARS_URL . '/' . $new_name;
    }

    return false;
}

function deleteOldAvatar(string $avatar_url): void
{
    $file_path = PUBLIC_PATH . $avatar_url;
    if ($avatar_url && file_exists($file_path))
    {
        unlink($file_path);
    }
}

//---------------------------cookies--------------------------

function setOneCookie(string $name, $value, int $days = 30): void
{
    setcookie($name, json_encode($value), [
        'expires' => time() + 60 * 60 * 24 * $days,
        'path' => '/',
        'httponly' => false,
        'samesite' => 'Lax'
    ]);
}

function getCookie(string $name, $default = null): mixed
{
    if (!isset($_COOKIE[$name]))
    {
        return $default;
    }

    $value = json_decode($_COOKIE[$name], true);

    return $value ?? $_COOKIE[$name];
}

function deleteCookie(string $name): void
{
    setcookie($name, '', [
        'expires' => time() - 3600,
        'path' => '/'
    ]);
}

function setThemePreference(string $theme = 'light'): void
{
    $allowed = ['light', 'dark'];

    if (!in_array($theme, $allowed, true))
    {
        $theme = 'light';
    }

    setOneCookie('theme', $theme);
}

function getThemePreference(): string
{
    $theme = getCookie('theme', 'light');

    return in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
}