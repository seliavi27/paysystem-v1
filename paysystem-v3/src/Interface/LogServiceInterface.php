<?php
declare(strict_types=1);

namespace PaySystem\Interface;

interface LogServiceInterface
{
    public function info(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;
}
