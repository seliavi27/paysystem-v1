<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Transaction;

interface TransactionRepositoryInterface extends RepositoryInterface
{
    public function findById(string $id): ?Transaction;

    /**
     * @return Transaction[]
     */
    public function findByPaymentId(string $paymentId): array;

    /**
     * @return Transaction[]
     */
    public function findByUserId(string $userId): array;
}
