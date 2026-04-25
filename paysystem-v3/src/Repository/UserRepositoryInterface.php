<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function saveEntity(object $entity): bool;
    public function update(User $user): bool;
}