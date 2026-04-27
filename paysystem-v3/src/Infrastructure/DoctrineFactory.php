<?php
declare(strict_types=1);

namespace PaySystem\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Tools\DsnParser;
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
        if ($this->em !== null) {
            return $this->em;
        }

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [dirname(__DIR__) . '/Entity'],
            isDevMode: ($_ENV['APP_ENV'] ?? 'dev') === 'dev',
        );
        // PHP 8.4+ native lazy objects — нужны для proxy сущностей с property hooks.
        $config->enableNativeLazyObjects(true);

        return $this->em = new EntityManager($this->createConnection(), $config);
    }

    public function createConnection(): Connection
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $parser = new DsnParser(['postgres' => 'pdo_pgsql', 'postgresql' => 'pdo_pgsql']);
        $this->connection = DriverManager::getConnection($parser->parse($this->databaseUrl));

        // transactions остаётся на DBAL — прячем её от Doctrine schema diff'а.
        $this->connection->getConfiguration()->setSchemaAssetsFilter(
            static fn(string|AbstractAsset $name): bool
                => (is_string($name) ? $name : $name->getName()) !== 'transactions'
        );

        return $this->connection;
    }
}
