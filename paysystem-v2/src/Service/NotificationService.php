<?php
declare(strict_types=1);

namespace PaySystem\Service;

use InvalidArgumentException;
use PaySystem\Entity\Payment;
use PaySystem\Entity\User;
use PaySystem\Notification\NotificationChannelInterface;
use Throwable;

class NotificationService
{
    private array $channels;

    public function __construct(
        array $channels
    )
    {
        $this->channels = $channels;
    }

    public function notify(User $user, string $message, string $channelName): bool
    {
        if (!isset($this->channels[$channelName]))
        {
            throw new InvalidArgumentException("Unknown channel: $channelName");
        }

        return $this->channels[$channelName]->send($user, $message);
    }

    public function notifyPaymentCompleted(Payment $payment, User $user): void
    {
        $message = sprintf(
            'Payment %s completed. Amount: %.2f %s',
            $payment->id,
            $payment->amount,
            $payment->currency->value
        );

        $this->notifyAll($user, $message);
    }

    public function notifyPaymentFailed(Payment $payment, User $user, string $reason): void
    {
        $message = sprintf(
            'Payment %s failed. Reason: %s',
            $payment->id,
            $reason
        );

        $this->notifyAll($user, $message);
    }

    public function notifyRefund(Payment $payment, User $user): void
    {
        $message = sprintf(
            'Payment %s refunded. Amount: %.2f %s',
            $payment->id,
            $payment->amount,
            $payment->currency->value
        );

        $this->notifyAll($user, $message);
    }

    private function notifyAll(User $user, string $message): void
    {
        foreach ($this->channels as $channel)
        {
            try
            {
                $channel->send($user, $message);
            }
            catch (Throwable $e)
            {
                log_error(sprintf(
                    '[%s] Notification failed: %s',
                    $channel->getName(),
                    $e->getMessage()
                ));
            }
        }
    }

    public function addChannel(NotificationChannelInterface $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }
}