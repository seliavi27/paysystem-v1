<?php
declare(strict_types=1);

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;

$container = require __DIR__ . '/bootstrap.php';
/** @var EntityManagerInterface $entityManager */
$entityManager = $container[EntityManagerInterface::class];

return DependencyFactory::fromEntityManager(
    new YamlFile(__DIR__ . '/config/migrations.yaml'),
    new ExistingEntityManager($entityManager),
);
