<?php
declare(strict_types=1);

namespace PaySystem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PaySystem\Exception\ExceptionHandler;
use Throwable;

class Application
{
    public function __construct(
        private Router $router,
        private ExceptionHandler $exceptionHandler,
        private array $middlewares = []
    ) { }

    public function handle(Request $request): Response
    {
        foreach ($this->middlewares as $middleware)
        {
            $earlyResponse = $middleware->handle($request);

            if ($earlyResponse !== null)
            {
                return $earlyResponse;
            }
        }

        try
        {
            return $this->router->dispatch($request);
        }
        catch (Throwable $e)
        {
            return $this->exceptionHandler->handle($e, $request);
        }
    }
}
