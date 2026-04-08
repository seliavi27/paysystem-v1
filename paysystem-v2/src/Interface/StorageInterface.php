<?php
declare(strict_types=1);

namespace PaySystem\Interface;

interface StorageInterface
{
    public function save(object $object): void;

    public function find(string $id): ?object;

    public function findAll(): array;
}