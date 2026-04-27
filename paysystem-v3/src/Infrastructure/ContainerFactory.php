<?php
declare(strict_types=1);

namespace PaySystem\Infrastructure;

use Dotenv\Dotenv;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContainerFactory
{
    public static function build(string $projectDir, bool $isDebug): ContainerInterface
    {
        $cacheFile = "{$projectDir}/var/cache/container.php";

        if (!$isDebug && file_exists($cacheFile)) {
            require_once $cacheFile;
            return new \CachedContainer();
        }

        Dotenv::createImmutable($projectDir)->safeLoad();
        $_ENV['KERNEL_PROJECT_DIR'] = $projectDir;

        // Symfony EnvVarProcessor резолвит %env(NAME)% через getenv().
        // Dotenv::createImmutable пишет только в $_ENV — нужно прокинуть в putenv,
        // иначе env-placeholders в дампленном контейнере получают пустые значения.
        foreach ($_ENV as $key => $value) {
            if (is_string($value) && getenv($key) === false) {
                putenv("$key=$value");
            }
        }

        $container = new ContainerBuilder();
        $loader    = new YamlFileLoader($container, new FileLocator("{$projectDir}/config"));
        $loader->load('services.yaml');
        $container->compile(true);

        // Дампим компилированный контейнер всегда — это резолвит %env(...)% placeholders
        // в реальные get_env-вызовы и даёт ~10x ускорение между запросами.
        @mkdir(dirname($cacheFile), recursive: true);
        file_put_contents(
            $cacheFile,
            (new PhpDumper($container))->dump(['class' => 'CachedContainer']),
        );

        require_once $cacheFile;
        return new \CachedContainer();
    }
}
