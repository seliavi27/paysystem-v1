<?php
declare(strict_types=1);

function startUserSession(array $user): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

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
    if (session_status() === PHP_SESSION_NONE) {
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

function logout_user(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];

    session_destroy();

    clearRememberMeCookie();
}

function requireLogin(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    return $_SESSION['user'];
}

//---------------------register-----------------------

function registerUser($formData): bool
{
    $success = false;
    $validation = validateRegistrationForm($formData);

    if (!$validation['valid'])
    {
        $errors = $validation['errors'];
        log_error("Registration was failed: " . basename(__FILE__));
    }
    else
    {
        $newUser = [
            'email' => $formData['email'],
            'password' => hashPassword($formData['password']),
            'fullName' => $formData['fullName'],
            'phone' => $formData['phone'],
            'createdAt' => date('Y-m-d H:i:s')
        ];

        $users = is_file(USERS_FILE) ?
            json_decode(file_get_contents(USERS_FILE), true) :
            ensure_users_storage(USERS_FILE);

        if (!is_array($users))
        {
            $users = [];
        }

        $listId = array_column($users, 'id');
        $newUser['id'] = max($listId) + 1;

        $users[] = $newUser;

        file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $success = true;

//        header('Location: login.php');
//        exit;
    }

    return $success;
}