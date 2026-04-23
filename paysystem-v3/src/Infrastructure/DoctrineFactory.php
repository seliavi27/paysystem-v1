<?php
declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

final class DoctrineFactory
{
    private ?EntityManagerInterface $em = null;
    private ?Connection $connection = null;

    public function __construct(private string $databaseUrl)
    {

    }

    public function createEntityManager(): EntityManagerInterface
    {
        return $this->em ??= new EntityManager(
            $this->createConnection(),
            ORMSetup::createAttributeMetadataConfiguration(
                paths: [dirname(__DIR__) . '/Entity'],
                isDevMode: ($_ENV['APP_ENV'] ?? 'dev') === 'dev'
            ),
        );
    }

    public function createConnection(): Connection
    {
        return $this->connection ??= DriverManager::getConnection([
            //'driver' => 'pdo_pgsql',
            'url' => $this->databaseUrl
        ]);
    }
}