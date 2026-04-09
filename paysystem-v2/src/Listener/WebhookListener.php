<?php
declare(strict_types=1);

namespace PaySystem\Listener;

use PaySystem\Event\EventInterface;

class WebhookListener implements EventListenerInterface
{
    private array $webhooks = [];

    public function subscribe(string $eventName, string $url): void
    {
        $this->webhooks[$eventName][] = $url;
    }

    public function handle(EventInterface $event): void
    {
        $eventName = $event->getName();

        if (isset($this->webhooks[$eventName]))
        {
            foreach ($this->webhooks[$eventName] as $url)
            {
                echo "POST to $url: " . json_encode($event->getPayload()) . "\n";
            }
        }
    }
}