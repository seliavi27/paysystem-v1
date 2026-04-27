<?php
declare(strict_types=1);

namespace PaySystem\Infrastructure;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class MonologFactory
{
    public function __construct(
        private string $opsLog,
        private string $errLog,
    ) {
    }

    public function create(): LoggerInterface
    {
        $logger = new Logger('paysystem');
        $logger->pushHandler(new StreamHandler($this->opsLog, Logger::INFO));
        $logger->pushHandler(new StreamHandler($this->errLog, Logger::ERROR));
        return $logger;
    }
}
