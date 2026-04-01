<?php
declare(strict_types=1);

function sanitizeInput(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail(string $email): string|false
{
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ?: false;
}

function sanitizeFilename(string $filename): string
{
    $filename = basename($filename);

    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

function preventCsrf(): void
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    if (empty($_SESSION['csrf_token']))
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function verifyCsrfToken(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    return isset($_SESSION['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $token);
}

function redirectWithMessage(string $url, string $message, string $type = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];

    header("Location: $url");
    exit;
}