<?php
declare(strict_types=1);

class SMSNotificationChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message): bool
    {
        if (empty($user->phone))
        {
            return false;
        }

        echo "[SMS to {$user->phone}] $message\n";

        return true;
    }

    public function getName(): string
    {
        return 'sms';
    }
}