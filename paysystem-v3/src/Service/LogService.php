<?php
declare(strict_types=1);

namespace App\Service;

use App\Interface\LogServiceInterface;
use Psr\Log\LoggerInterface;

class LogService implements LogServiceInterface
{
    /** @param LoggerInterface[] $loggers */
    public function __construct(
        private array $loggers = []
    ) {}

    public function info(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger)
        {
            $logger->info($message, $context);
        }
    }

    public function error(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger)
        {
            $logger->error($message, $context);
        }
    }
}
