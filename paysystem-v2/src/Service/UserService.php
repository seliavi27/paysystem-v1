<?php
declare(strict_types=1);

namespace PaySystem\Service;

use InvalidArgumentException;
use PaySystem\DTO\CreateUserRequest;
use PaySystem\Entity\User;
use PaySystem\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function create(CreateUserRequest $request): User
    {
        $user = User::create(
            email: $request->email,
            password: $request->password,
            fullName: $request->fullName,
            phone: $request->phone
        );

        $this->repository->saveEntity($user);
        return $user;
    }

    /**
     * @param string $id
     * @return ?User
     */
    public function findById(string $id): ?User
    {
        $user = $this->repository->findById($id);

        if ($user instanceof User)
        {
            return $user;
        }

        return null;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    public function update(User $user): void
    {
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Invalid email');
        }

        if (strlen($user->password) < 6)
        {
            throw new InvalidArgumentException('Password too short');
        }

        $this->repository->update($user);
    }

    public function addBalance(User $user, float $amount): void
    {
        if ($amount < 0)
        {
            throw new InvalidArgumentException('Amount must be positive');
        }

        $user->addBalance($amount);
        $this->repository->update($user);
    }
}