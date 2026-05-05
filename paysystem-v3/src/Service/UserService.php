<?php
declare(strict_types=1);

namespace PaySystem\Service;

use InvalidArgumentException;
use PaySystem\DTO\CreateUserRequest;
use PaySystem\Entity\User;
use PaySystem\Exception\ValidationException;
use PaySystem\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) { }

    public function create(CreateUserRequest $request): User
    {
        if ($this->repository->findByEmail($request->email) !== null) {
            throw new ValidationException('User with this email already exists');
        }

        $user = User::create(
            email: $request->email,
            password: $request->password,
            fullName: $request->fullName,
            phone: $request->phone,
        );

        $this->repository->saveEntity($user);

        return $user;
    }

    public function findById(string $id): ?User
    {
        $user = $this->repository->findById($id);

        return $user instanceof User ? $user : null;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    public function update(User $user): void
    {
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
