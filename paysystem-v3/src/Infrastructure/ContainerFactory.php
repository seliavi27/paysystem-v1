<?php
declare(strict_types=1);

namespace PaySystem\Infrastructure;

use Dotenv\Dotenv;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerFactory
{
    public static function build(string $projectDir, bool $isDebug): ContainerInterface
    {
        $cacheFile = "{$projectDir}/var/cache/container.php";
        if (!$isDebug && file_exists($cacheFile))
        {
            require_once $cacheFile;
            return new CachedContainer();
        }

        Dotenv::createImmutable($projectDir)->safeLoad();
        $_ENV['KERNEL_PROJECT_DIR'] = $projectDir;

        $container = new ContainerBuilder();
        $loader    = new YamlFileLoader($container, new FileLocator("{$projectDir}/config"));
        $loader->load('services.yaml');
        $container->compile();

        if (!$isDebug) {
            @mkdir(dirname($cacheFile), recursive: true);
            file_put_contents(
                $cacheFile,
                (new PhpDumper($container))->dump(['class' => 'CachedContainer']),
            );
        }

        return $container;
    }
}