<?php
declare(strict_types=1);

namespace PaySystem;

class Application
{
    public function __construct(
        private Router $router,
        private array  $middlewares = []
    )
    {
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

        $response = $this->router->dispatch($request);
        $response->send();
    }
}