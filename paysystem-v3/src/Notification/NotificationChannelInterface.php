<?php
declare(strict_types=1);

namespace App\Notification;

use App\Entity\User;

interface NotificationChannelInterface
{
    public function send(User $user, string $message): bool;

    public function getName(): string;
}