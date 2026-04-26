<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\Entity\User;
use PaySystem\Exception\AuthenticationException;
use PaySystem\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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

    public function logout(SessionInterface $session): void
    {
        $session->invalidate();
    }
}
