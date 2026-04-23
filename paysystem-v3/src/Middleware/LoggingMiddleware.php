<?php
declare(strict_types=1);

namespace App\Middleware;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Interface\LogServiceInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LogServiceInterface $logger
    )
    {
    }

    public function handle(Request $request, Response $response): ?Response
    {
        $this->logger->info('Incoming request', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
        ]);

        //return new RedirectResponse('/payments');
        return null;
    }
}