<?php
declare(strict_types=1);

namespace PaySystem\Infrastructure;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RouterFactory
{
    public static function loadRoutes(string $controllerDir): RouteCollection
    {
        $locator     = new FileLocator([$controllerDir]);
        $classLoader = new class extends AttributeClassLoader {
            protected function configureRoute(
                Route $route,
                ReflectionClass $class,
                ReflectionMethod $method,
                object $attr,
            ): void {
                if (!$route->getDefault('_controller')) {
                    $route->setDefault('_controller', $class->name . '::' . $method->name);
                }
            }
        };

        $loader = new AttributeDirectoryLoader($locator, $classLoader);

        return $loader->load($controllerDir);
    }
}
