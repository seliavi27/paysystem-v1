<?php
declare(strict_types=1);

namespace PaySystem\Notification;

use PaySystem\Entity\User;

class LogNotificationChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message): bool
    {
        error_log("[NOTIFICATION] {$message}");
        return true;
    }

    public function getName(): string
    {
        return 'log';
    }
}