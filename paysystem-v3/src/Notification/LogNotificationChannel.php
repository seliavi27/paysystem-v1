<?php
declare(strict_types=1);

namespace App\Notification;

use App\Entity\User;

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