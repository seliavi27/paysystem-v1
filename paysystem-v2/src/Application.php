<?php
declare(strict_types=1);

namespace PaySystem;

use PaySystem\Exception\ExceptionHandler;
use Throwable;

class Application
{
    public function __construct(
        private Router $router,
        private ExceptionHandler $exceptionHandler,
        private array $middlewares = []
    ) {
    }

    public function run(): void
    {
        $request = Request::fromGlobals();
        $response = new Response();

        foreach ($this->middlewares as $middleware)
        {
            $middleware->handle($request, $response);

            if ($response->isSent())
            {
                return;
            }
        }

        try {
            $response = $this->router->dispatch($request);
        } catch (Throwable $e) {
            $response = $this->exceptionHandler->handle($e, $request);
        }

        $response->send();
    }
}
