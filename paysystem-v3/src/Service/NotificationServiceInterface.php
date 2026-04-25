<?php

namespace PaySystem\Service;

use PaySystem\Entity\Payment;
use PaySystem\Entity\User;
use PaySystem\Notification\NotificationChannelInterface;

interface NotificationServiceInterface
{
    public function notify(User $user, string $message, string $channelName): bool;
    public function notifyPaymentCompleted(Payment $payment, User $user): void;
    public function notifyPaymentFailed(Payment $payment, User $user, string $reason): void;
    public function notifyRefund(Payment $payment, User $user): void;
    public function addChannel(NotificationChannelInterface $channel): void;
}