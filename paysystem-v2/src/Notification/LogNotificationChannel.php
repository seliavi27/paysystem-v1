<?php
declare(strict_types=1);

class LogNotificationChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message): bool
    {
        log_operation('NOTIFICATION', $message);
        return true;
    }

    public function getName(): string
    {
        return 'log';
    }
}