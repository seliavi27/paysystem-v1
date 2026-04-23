<?php
declare(strict_types=1);

namespace App\Notification;

use App\Entity\User;

class EmailNotificationChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message): bool
    {
        if (empty($user->email)) {
            return false;
        }

        echo "[EMAIL to {$user->email}] $message\n";

        return true;
    }

    public function getName(): string
    {
        return 'email';
    }
}