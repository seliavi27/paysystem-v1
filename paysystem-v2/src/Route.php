<?php
declare(strict_types=1);

namespace PaySystem;

use Closure;

class Route
{
    private array $params = [];

    public function __construct(
        private string $method,
        private string $pattern,
        private Closure $handler
    ) {}

    public function matches(string $method, string $path): bool
    {
        if (strtoupper($method) !== $this->method)
        {
            return false;
        }

        $pattern = preg_replace(
            '/{(\w+)}/', '(?P<$1>\d+)',
            $this->pattern);

        $result = preg_match("#^$pattern$#", $path, $this->params) === 1;
        return $result;
    }

    public function getParams(): array
    {
        return array_filter(
            $this->params,
            fn($key) => is_string($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function call(Request $request, Response $response): Response
    {
        return call_user_func($this->handler, $request, $response);
    }
}