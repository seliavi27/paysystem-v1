<?php
declare(strict_types=1);

namespace PaySystem\Notification;

use PaySystem\Entity\User;

class WebhookNotificationChannel implements NotificationChannelInterface
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function send(User $user, string $message): bool
    {
        echo "[WEBHOOK to {$this->url}] $message\n";

        return true;
    }

    public function getName(): string
    {
        return 'webhook';
    }
}