<?php
declare(strict_types=1);

namespace PaySystem;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $body = '';
    private bool $sent = false;

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setJson(array $data): self
    {
        $this->body = json_encode($data);
        $this->setHeader('Content-Type', 'application/json');
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function send(): void
    {
        if ($this->sent)
        {
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value)
        {
            header("$name: $value");
        }

        echo $this->body;
        $this->sent = true;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }
}