<?php

declare(strict_types=1);

namespace App\Exception;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Throwable;

use App\Interface\LogServiceInterface;

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
            $exception instanceof UniqueConstraintViolationException => [409, 'Conflict'],
            default => [500, 'Internal Server Error'],
        };

        return new JsonResponse(['error' => $message], $status);
    }
}
