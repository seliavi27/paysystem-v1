<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PaySystem\Interface\LogServiceInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LogServiceInterface $logger
    )
    {
    }

    public function handle(Request $request): ?Response
    {
        $this->logger->info('Incoming request', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'route' => (string)$request->attributes->get('_route', ''),
        ]);

        return null;
    }
}
