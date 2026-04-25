<?php
declare(strict_types=1);

namespace PaySystem\Notification;

use PaySystem\Entity\User;

interface NotificationChannelInterface
{
    public function send(User $user, string $message): bool;

    public function getName(): string;
}