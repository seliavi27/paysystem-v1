<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\Entity\User;
use PaySystem\Repository\UserRepositoryInterface;
use RuntimeException;

class AuthenticationService
{
    private const SESSION_KEY = 'userId';
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function authenticate(string $email, string $password): User
    {
        $user = $this->findByEmail($email);

        if (!$user)
        {
            throw new RuntimeException('User not found');
        }

        if (!password_verify($password, $user->password))
        {
            throw new RuntimeException('Invalid password');
        }

        $_SESSION[self::SESSION_KEY] = $user->id;

        return $user;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public function getCurrentUser(): ?User
    {
        if (!$this->isAuthenticated())
        {
            return null;
        }

        return $this->repository->findById($_SESSION[self::SESSION_KEY]);
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_destroy();
    }

    private function findByEmail(string $email): ?User
    {
        $users = $this->repository->findAll();

        foreach ($users as $user)
        {
            if (strtolower($user->email) === strtolower($email))
            {
                return $user;
            }
        }

        return null;
    }
}