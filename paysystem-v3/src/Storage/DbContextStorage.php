<?php
declare(strict_types=1);

namespace App\Storage;

use Doctrine\DBAL\Connection;

class DbContextStorage implements StorageInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function load(): array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')->from('users')
            ->executeQuery()
            ->fetchAssociative();
        return is_array($data) ? $data : [];
    }

    public function save(array $data): bool
    {
        return (bool)file_put_contents(
            $this->filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}