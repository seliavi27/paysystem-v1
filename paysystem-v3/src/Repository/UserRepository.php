<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\User;
use PaySystem\Storage\StorageInterface;

class UserRepository implements UserRepositoryInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function saveEntity(object $entity): bool
    {
        /** @var User $entity */
        $users = $this->storage->load();
        $users[] = $entity->toArray();
        return $this->storage->save($users);
    }

    public function update(User $user): bool
    {
        $users = $this->load();

        if (!isset($user->id))
        {
            return false;
        }

        foreach ($users as $u)
        {
            if (isset($user->id) && $u->id === $user->id)
            {
                unset($u);
            }
        }

        return $this->save($users);
    }

    public function delete(string $id): bool
    {
        $users = $this->load();

        foreach ($users as $user)
        {
            if (isset($user->id) && $user->id === $id)
            {
                unset($user);
            }
        }

        return $this->save($users);
    }

    public function findById(string $id): ?User
    {
        $users = $this->load();
        return array_find($users, fn($user) => isset($user->id) && $user->id === $id);
    }

    public function findByEmail(string $email): ?User
    {
        $users = $this->findAll();
        return array_find($users, fn($user) => $user->email === $email);
    }

    public function findAll(): array
    {
        return $this->load();
    }

    private function load(): array
    {
        return array_map(
            fn($item) => User::fromArray($item),
            $this->storage->load()
        );
    }

    private function save(array $data): bool
    {
        return $this->storage->save($data);
    }
}
