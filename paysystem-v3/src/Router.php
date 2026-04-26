<?php
declare(strict_types=1);

namespace PaySystem;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = new Route('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->routes[] = new Route('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->routes[] = new Route('PUT', $pattern, $handler);
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPathInfo();

        foreach ($this->routes as $route)
        {
            if ($route->matches($method, $path))
            {
                foreach ($route->getParams() as $key => $value)
                {
                    $request->attributes->set($key, $value);
                }

                return $route->call($request);
            }
        }

        return new JsonResponse(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
    }
}