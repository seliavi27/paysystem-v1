<?php
declare(strict_types=1);

function loadUsers(): array
{
    $file = __DIR__ . '/users.json';

    if (!file_exists($file))
    {
        return [];
    }

    $content = file_get_contents($file);
    return json_decode($content, true) ?? [];
}

function saveUsers(array $users): bool
{
    $file = __DIR__ . '/users.json';
    return file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function getCurrentUser(): ?array
{
    if (!isset($_SESSION['user']['id']))
    {
        return null;
    }

    $users = loadUsers();

    foreach ($users as $user)
    {
        if ($user['id'] === $_SESSION['user']['id'])
        {
            return $user;
        }
    }
    return null;
}

function updateUser(int $userId, array $data): bool
{
    $users = loadUsers();

    foreach ($users as $key => $user)
    {
        if ($user['id'] === $userId)
        {
            $users[$key] = array_merge($user, $data);
            return saveUsers($users);
        }
    }
    return false;
}