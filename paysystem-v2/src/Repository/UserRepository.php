<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\User;
use PaySystem\Storage\StorageInterface;

class UserRepository implements UserRepositoryInterface
{
    private StorageInterface $storage;
    private array $users = [];

    public function __construct(
        StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->load();
    }

    private function load(): void
    {
        $items = $this->storage->load();

        foreach ($items as $data)
        {
            $user = User::fromArray($data);
            $this->users[$user->id] = $user;
        }
    }

    public function findById(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->users);
    }

    public function save(object $entity): bool
    {
        $this->users[$entity->id] = $entity;
        return $this->storage->save($entity);
    }

    public function delete(string $id): bool
    {
        if (!isset($this->users[$id]))
        {
            return false;
        }

        unset($this->users[$id]);
        return $this->storage->delete($id);
    }
}
