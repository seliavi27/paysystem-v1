<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PDO;

class DatabasePaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function save(object $entity): void
    {

    }

    public function findById(string|int $id): ?object
    {

    }

}