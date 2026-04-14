<?php
declare(strict_types=1);

namespace PaySystem\Repository;

interface RepositoryInterface
{
    public function saveEntity(object $entity): bool;

    public function findById(string $id): ?object;

    public function findAll(): array;

    public function delete(string $id): bool;
}
