<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    private string $context;
    private array $transactions = [];

    public function __construct(
        string $_context)
    {
        $this->context = $_context;
        $this->load();
    }

    private function load(): void
    {

    }

    private function saveToFile(): void
    {
        $data = array_map(fn(Transaction $t) => $t->toArray(), $this->transactions);
        // save to $_context
    }

    public function find(string $id): ?Transaction
    {
        return $this->transactions[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->transactions);
    }

    public function save(Transaction $transaction): bool
    {
        $this->transactions[$transaction->id] = $transaction;
        $this->saveToFile();
        return true;
    }

    public function delete(string $id): bool
    {
        if (!isset($this->transactions[$id]))
        {
            return false;
        }

        unset($this->transactions[$id]);
        $this->saveToFile();
        return true;
    }
}
