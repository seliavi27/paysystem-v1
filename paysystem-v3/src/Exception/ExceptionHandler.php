<?php

declare(strict_types=1);

namespace PaySystem\Exception;

use PaySystem\Interface\LogServiceInterface;
use PaySystem\Request;
use PaySystem\Response;
use Throwable;

class ExceptionHandler
{
    public function __construct(
        private LogServiceInterface $logger
    ) {}

    public function handle(Throwable $exception, ?Request $request = null): Response
    {
        $this->logger->error('Exception occurred', [
            'type' => $exception::class,
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        [$status, $message] = match (true) {
            $exception instanceof ValidationException => [422, $exception->getMessage()],
            $exception instanceof NotFoundException => [404, $exception->getMessage()],
            $exception instanceof AuthenticationException => [401, 'Unauthorized'],
            default => [500, 'Internal Server Error'],
        };

        return (new Response())
            ->setStatusCode($status)
            ->setJson(['error' => $message]);
    }
}
