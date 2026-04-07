<?php
declare(strict_types=1);

class LogService
{
    private array $logs = [];
    private array $channels;

    public function __construct(
        array $channels
    )
    {
        $this->channels = $channels;
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    private function log(string $level, string $message, array $context): void
    {
        $formatted = $this->formatMessage($level, $message, $context);

        $this->logs[] = $formatted;

        foreach ($this->channels as $channel)
        {
            try
            {
                $channel->write($level, $message, $context);
            }
            catch (Throwable $e)
            {
                error_log($e->getMessage());
            }
        }
    }

    private function formatMessage(string $level, string $message, array $context): string
    {
        $contextStr = empty($context)
            ? ''
            : json_encode($context, JSON_UNESCAPED_UNICODE);

        return sprintf(
            '[%s] [%s] %s %s',
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $contextStr
        );
    }
}