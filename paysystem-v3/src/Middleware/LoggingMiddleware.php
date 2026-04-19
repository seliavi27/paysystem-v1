<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use PaySystem\Interface\LogServiceInterface;
use PaySystem\Response;
use PaySystem\Request;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LogServiceInterface $logger
    )
    {
    }

    public function handle(Request $request, Response $response): void
    {
        $this->logger->info('Incoming request', [
            'method' => $request->method,
            'path' => $request->getPath(),
        ]);
    }
}