<?php
declare(strict_types=1);

namespace PaySystem\Service;

use InvalidArgumentException;
use PaySystem\Entity\User;
use RuntimeException;

class AuthenticationService implements AuthenticationServiceInterface
{
    private UserServiceInterface $userService;

    public function __construct(
        UserServiceInterface $userService
    )
    {
        $this->userService = $userService;

        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
    }

    public function authenticate(string $email, string $password): User
    {
        if (empty($email))
        {
            throw new InvalidArgumentException('Email is required');
        }

        if (empty($password))
        {
            throw new InvalidArgumentException('Password is required');
        }

        $user = $this->userService->findByEmail($email);

        if (is_null($user))
        {
            throw new RuntimeException('User not found');
        }

        if (!password_verify($password, $user->password))
        {
            throw new RuntimeException('Invalid password');
        }

        $_SESSION[$_ENV['SESSION_KEY']] = $user->id;

        return $user;
    }

    public function isAuthenticated(): bool
    {
        if (!isset($_SESSION[$_ENV['SESSION_KEY']]))
        {
            return false;
        }

        $userId = $_SESSION[$_ENV['SESSION_KEY']];
        $user = $this->userService->findById($userId);

        if (is_null($user))
        {
            $this->logout();
            return false;
        }

        return true;
    }

    public function getCurrentUser(): ?User
    {
        if (!$this->isAuthenticated())
        {
            return null;
        }

        $user = $this->userService->findById($_SESSION[$_ENV['SESSION_KEY']]);

        if ($user instanceof User)
        {
            return $user;
        }

        return null;
    }

    public function logout(): void
    {
        unset($_SESSION[$_ENV['SESSION_KEY']]);
        session_destroy();
    }

//    public function validateToken(?string $header): bool
//    {
//        return $this->jwtTokenService->validate($header);
//    }
}