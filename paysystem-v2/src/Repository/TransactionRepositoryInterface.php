<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Transaction;

interface TransactionRepositoryInterface
{
    public function find(string $id): ?Transaction;

    public function findAll(): array;

    public function save(Transaction $transaction): bool;

    public function delete(string $id): bool;

}