<?php
declare(strict_types=1);

interface StorageInterface
{
    public function save(Payment $payment): void;
    public function find(string $id): ?Payment;
    public function findAll(): array;
}