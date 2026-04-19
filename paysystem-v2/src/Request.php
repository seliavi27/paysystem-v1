<?php
declare(strict_types=1);

namespace PaySystem;

class Request
{
    public string $method
    {
        get => $this->method;
    }

    private string $path;
    private array $query;
    private array $post;
    private array $headers;
    private array $attributes = [];

    private function __construct(
        string $method,
        string $path,
        array  $query,
        array  $post,
        array  $headers
    )
    {
        $this->method = mb_strtoupper($method);
        $this->path = $path;
        $this->query = $query;
        $this->post = $post;
        $this->headers = $headers;
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            $_GET,
            $_POST,
            getallheaders() ?? []
        );
    }

    public function getPath(): string
    {
        return parse_url($this->path, PHP_URL_PATH);
    }

    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function getJson(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }
}