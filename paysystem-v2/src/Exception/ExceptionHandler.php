<?php
declare (strict_types=1);

namespace PaySystem\Exception;

use PaySystem\Interface\LogServiceInterface;
use PaySystem\Response;
use Throwable;

class ExceptionHandler
{
    public function __construct(
        private LogServiceInterface $logger
    ) {}

    public function handle(Throwable $exception): Response
    {
        $response = new Response();

        match(get_class($exception))
        {
            'PaySystem\Exception\ValidationException' => $response
                ->setStatusCode(422)
                ->setJson(['error' => $exception->getMessage()]),
            'PaySystem\Exception\NotFoundException' => $response
                ->setStatusCode(404)
                ->setJson(['error' => $exception->getMessage()]),
            'PaySystem\Exception\AuthenticationException' => $response
                ->setStatusCode(401)
                ->setJson(['error' => 'Unauthorized']),
            default => $response
                ->setStatusCode(500)
                ->setJson(['error' => 'Internal Server Error'])
        };

        $this->logger->error('Exception occurred', [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        return $response;
    }
}
