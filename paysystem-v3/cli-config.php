<?php
declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$container = require __DIR__ . '/bootstrap.php';
$entityManager = $container[EntityManagerInterface::class];
$entityManagerProvider  = new SingleManagerProvider($entityManager);
return $entityManagerProvider;