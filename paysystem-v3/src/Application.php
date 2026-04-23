<?php
declare(strict_types=1);

namespace PaySystem;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Throwable;

use PaySystem\Exception\ExceptionHandler;

class Application
{
    public function __construct(
        private ControllerResolver $controllerResolver,
        private ArgumentResolver   $argumentResolver,
        private ExceptionHandler   $exceptionHandler,
        #[AutowireIterator('middleware', defaultPriorityMethod: 'priority')]
        private array $middlewares = [],
    ) { }

    public function handle(Request $request): Response
    {
        foreach ($this->middlewares as $middleware)
        {
            if (($response = $middleware->handle($request)) !== null)
            {
                return $response;
            }
        }

        try
        {
            $controller = $this->controllerResolver->getController($request);
            $arguments  = $this->argumentResolver->getArguments($request, $controller);
            return $controller(...$arguments);
        }
        catch (Throwable $e)
        {
            return $this->exceptionHandler->handle($e, $request);
        }
    }
}
