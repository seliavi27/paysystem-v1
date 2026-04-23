<?php
declare(strict_types=1);

namespace App;

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

        $regex = preg_replace_callback(
            '/\{(\w+)(?::([^}]+))?\}/',
            fn(array $m): string => '(?P<'.$m[1].'>'.($m[2] ?? '[^/]+').')',
            $this->pattern
        );

        return preg_match("#^{$regex}$#", $path, $this->params) === 1;
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
