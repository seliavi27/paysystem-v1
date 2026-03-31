<?php
declare(strict_types=1);

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