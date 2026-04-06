<?php
declare(strict_types=1);

function startUserSession(array $user): void
{
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'fullName' => $user['fullName'] ?? '',
        'phone' => $user['phone'] ?? '',
        'loggedAt' => date('Y-m-d H:i:s')
    ];
}

function isUserLoggedIn(): bool
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    return isset($_SESSION['user']);
}

function getCurrentUserFromSession(): array|false
{
    if (!isUserLoggedIn())
    {
        return false;
    }

    return $_SESSION['user'];
}

function setRememberMeCookie(string $email): void
{
    setcookie('remember_me', $email, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function getRememberedEmail(): string|false
{
    return $_COOKIE['remember_me'] ?? false;
}

function clearRememberMeCookie(): void
{
    setcookie('remember_me', '', [
        'expires' => time() - 3600,
        'path' => '/'
    ]);
}

function logoutUser(): void
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    $_SESSION = [];

    session_destroy();

    clearRememberMeCookie();
}

function requireLogin(): array
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    if (empty($_SESSION['user']))
    {
        redirectToPage('login');
    }

    return $_SESSION['user'];
}