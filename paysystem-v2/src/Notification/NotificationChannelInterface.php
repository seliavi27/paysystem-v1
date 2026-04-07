<?php
declare(strict_types=1);

interface NotificationChannelInterface
{
    public function send(User $user, string $message): bool;
    public function getName(): string;
}