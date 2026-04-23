<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\User;
use App\Notification\NotificationChannelInterface;

interface NotificationServiceInterface
{
    public function notify(User $user, string $message, string $channelName): bool;
    public function notifyPaymentCompleted(Payment $payment, User $user): void;
    public function notifyPaymentFailed(Payment $payment, User $user, string $reason): void;
    public function notifyRefund(Payment $payment, User $user): void;
    public function addChannel(NotificationChannelInterface $channel): void;
}