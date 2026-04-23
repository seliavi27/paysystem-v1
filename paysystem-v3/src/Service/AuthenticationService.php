<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\AuthenticationException;
use App\Exception\ValidationException;

class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(
        private UserServiceInterface $userService
    ) {
    }

    public function authenticate(string $email, string $password): User
    {
        if ($email === '' || $password === '')
        {
            throw new ValidationException('Email and password are required');
        }

        $user = $this->userService->findByEmail($email);

        if ($user === null || !password_verify($password, $user->password))
        {
            throw new AuthenticationException('Invalid credentials');
        }

        return $user;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }
}
