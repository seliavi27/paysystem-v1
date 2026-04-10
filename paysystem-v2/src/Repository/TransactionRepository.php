<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Transaction;
use PaySystem\Storage\StorageInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    private StorageInterface $storage;
    private array $transactions = [];

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
            $transaction = Transaction::fromArray($data);
            $this->transactions[$transaction->id] = $transaction;
        }
    }

    public function findById(string $id): ?Transaction
    {
        return $this->transactions[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->transactions);
    }

    public function save(object $entity): bool
    {
        $this->transactions[$entity->id] = $entity;
        return $this->storage->save($entity);
    }

    public function delete(string $id): bool
    {
        if (!isset($this->transactions[$id]))
        {
            return false;
        }

        unset($this->transactions[$id]);
        return $this->storage->delete($id);
    }
}
