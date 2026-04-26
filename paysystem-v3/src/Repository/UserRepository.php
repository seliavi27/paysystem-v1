<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use PaySystem\Entity\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function findAll(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*') ->from('users')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findById(string $id): ?User
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')->from('users')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')->from('users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery()
            ->fetchAssociative();

        return $row ? $this->hydrate($row) : null;
    }

    public function saveEntity(object $entity): bool
    {
        /** @var User $entity */
        $this->connection->insert('users', [
            'id'         => $entity->id,
            'email'      => $entity->email,
            'password'   => $entity->password,
            'full_name'  => $entity->fullName,
            'phone'      => $entity->phone,
            'balance'    => $entity->balance,
            'created_at' => $entity->createdAt->format('Y-m-d H:i:s.u O'),
            'updated_at' => $entity->updatedAt->format('Y-m-d H:i:s.u O'),
        ]);
        return true;
    }

    public function update(User $user): bool
    {
        $user->updatedAt = new DateTime();

        return $this->connection->update('users', [
                'email'      => $user->email,
                'password'   => $user->password,
                'full_name'  => $user->fullName,
                'phone'      => $user->phone,
                'balance'    => $user->balance,
                'updated_at' => $user->updatedAt->format('Y-m-d H:i:s.u O'),
            ], ['id' => $user->id]) > 0;
    }

    public function delete(string $id): bool
    {
        return $this->connection->delete('users', ['id' => $id]) > 0;
    }

    private function hydrate(array $row): User
    {
        return User::fromArray([
            'id'         => $row['id'],
            'email'      => $row['email'],
            'password'   => $row['password'],
            'fullName'   => $row['full_name'],
            'phone'      => $row['phone'],
            'balance'    => (float)$row['balance'],
            'createdAt'  => $row['created_at'],
            'updatedAt'  => $row['updated_at'],
        ]);
    }
}
