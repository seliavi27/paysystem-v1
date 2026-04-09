<?php
declare(strict_types=1);

namespace PaySystem\Listener;

use PaySystem\Event\EventInterface;
use PaySystem\Event\PaymentCompletedEvent;
use PaySystem\Notification\NotificationChannelInterface;

class EmailNotificationListener implements EventListenerInterface
{
    public function __construct(
        private NotificationChannelInterface $emailChannel
    ) {}

    public function handle(EventInterface $event): void
    {
        if ($event instanceof PaymentCompletedEvent)
        {
            $payload = $event->getPayload();
            $message = "Payment #{$payload['payment_id']} completed!";

            $this->emailChannel->send(
                $event->getUser(),
                $message,
            );
        }
    }
}
