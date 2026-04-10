<?php
declare(strict_types=1);

namespace PaySystem\Service;

use InvalidArgumentException;
use PaySystem\Entity\User;
use PaySystem\Repository\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function create(string $name, string $email, string $password): User
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Invalid email');
        }

        if (strlen($password) < 6)
        {
            throw new InvalidArgumentException('Password too short');
        }

        $user = User::create(
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT),
            fullName: $name,
            phone: ''
        );

        $this->repository->save($user);

        return $user;
    }

    public function findById(string $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function findByEmail(string $email): ?User
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

    public function updateEmail(User $user, string $newEmail): void
    {
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Invalid email');
        }

        $user->email = $newEmail;
        $this->repository->save($user);
    }

    public function addBalance(User $user, float $amount): void
    {
        if ($amount < 0)
        {
            throw new InvalidArgumentException('Amount must be positive');
        }

        $user->addBalance($amount);
        $this->repository->save($user);
    }
}