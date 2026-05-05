<?php
declare(strict_types=1);

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use PaySystem\Infrastructure\ContainerFactory;

require_once __DIR__ . '/vendor/autoload.php';

$container = ContainerFactory::build(projectDir: __DIR__, isDebug: true);
/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get(EntityManagerInterface::class);

return DependencyFactory::fromEntityManager(
    new YamlFile(__DIR__ . '/config/migrations.yaml'),
    new ExistingEntityManager($entityManager),
);
