<?php
declare(strict_types=1);

function start_user_session(array $user): void
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    $_SESSION['user'] = [
        'email' => $user['email'],
        'full_name' => $user['full_name'] ?? '',
        'phone' => $user['phone'] ?? '',
        'logged_at' => date('Y-m-d H:i:s')
    ];
}

function is_user_logged_in(): bool
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    return isset($_SESSION['user']);
}

function get_current_user_from_session(): array|false
{
    if (!is_user_logged_in())
    {
        return false;
    }

    return $_SESSION['user'];
}

function logout_user(): void
{
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    $_SESSION = [];

    session_destroy();

    clear_remember_me_cookie();
}

function set_remember_me_cookie(string $email): void
{
    setcookie('remember_me', $email, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function get_remembered_email(): string|false
{
    return $_COOKIE['remember_me'] ?? false;
}

function clear_remember_me_cookie(): void
{
    setcookie('remember_me', '', [
        'expires' => time() - 3600,
        'path' => '/'
    ]);
}