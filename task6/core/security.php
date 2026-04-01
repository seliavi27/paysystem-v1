<?php
declare(strict_types=1);

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

function redirectWithMessage(string $page, string $message, string $type = 'success', array $params = []): void
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    setFlashMessage($message, type: $type);

    redirectToPage($page);
}