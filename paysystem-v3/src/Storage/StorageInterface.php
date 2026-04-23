<?php
declare(strict_types=1);

namespace App\Storage;

interface StorageInterface
{
    public function save(array $data): bool;

    public function load(): array;
}