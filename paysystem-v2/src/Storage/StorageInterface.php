<?php
declare(strict_types=1);

namespace PaySystem\Storage;

interface StorageInterface
{
    public function save(object $object): bool;

    public function delete(string $id): bool;

    public function find(string $id): ?object;

    public function findAll(): array;

    public function load(): array;
}