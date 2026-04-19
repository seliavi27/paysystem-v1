<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\DTO\CreateUserRequest;
use PaySystem\Entity\User;

interface UserServiceInterface
{
    public function create(CreateUserRequest $request): User;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function update(User $user): void;
    public function addBalance(User $user, float $amount): void;
}