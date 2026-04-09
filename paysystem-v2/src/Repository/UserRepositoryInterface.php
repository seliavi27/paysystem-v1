<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\User;

interface UserRepositoryInterface
{
    public function find(string $id): ?User;

    public function findAll(): array;

    public function save(User $user): bool;

    public function delete(string $id): bool;

}