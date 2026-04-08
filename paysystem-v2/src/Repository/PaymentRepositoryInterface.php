<?php
declare(strict_types=1);

namespace PaySystem\Repository;

use PaySystem\Entity\Payment;

interface PaymentRepositoryInterface
{
    public function find(string $id): ?Payment;

    public function findAll(): array;

    public function save(Payment $payment): bool;

    public function delete(string $id): bool;

}