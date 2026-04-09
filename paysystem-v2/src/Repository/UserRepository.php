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

    }

    private function saveToFile(): void
    {
        $data = array_map(fn(User $u) => $u->toArray(), $this->users);
        // save to $_context
    }

    public function find(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->users);
    }

    public function save(User $user): bool
    {
        $this->users[$user->id] = $user;
        $this->saveToFile();
        return true;
    }

    public function delete(string $id): bool
    {
        if (!isset($this->users[$id]))
        {
            return false;
        }

        unset($this->users[$id]);
        $this->saveToFile();
        return true;
    }
}
