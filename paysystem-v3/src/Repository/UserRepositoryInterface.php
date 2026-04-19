<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\User;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function update(User $user): bool;
}