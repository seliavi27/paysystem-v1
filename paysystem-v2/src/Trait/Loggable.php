<?php
declare(strict_types=1);

namespace PaySystem\Trait;

use DateTime;

trait Loggable
{
    private array $logs = [];

    protected function log(string $message): void
    {
        $this->logs[] = [
            'message' => $message,
            'time' => new DateTime()->format('Y-m-d H:i:s'),
        ];
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function clearLogs(): void
    {
        $this->logs = [];
    }
}