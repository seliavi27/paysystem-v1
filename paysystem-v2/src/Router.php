<?php
declare(strict_types=1);

namespace PaySystem;

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
        $response = new Response();

        foreach ($this->routes as $route)
        {
            if ($route->matches($request->method, $request->getPath()))
            {
                $params = $route->getParams();

                foreach ($params as $key => $value)
                {
                    $request->setAttribute($key, (int)$value);
                }

                return $route->call($request, $response);
            }
        }

        return $response
            ->setStatusCode(404)
            ->setJson(['error' => 'Not Found']);
    }
}