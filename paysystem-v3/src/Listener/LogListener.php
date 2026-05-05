<?php
declare(strict_types=1);

namespace App\Listener;

use App\Event\EventInterface;
use App\Interface\LogServiceInterface;

class LogListener implements EventListenerInterface
{
    public function __construct(
        private LogServiceInterface $logger
    ) {}

    public function handle(EventInterface $event): void
    {
        $this->logger->info(
            "Event: {$event->getName()}",
            $event->getPayload()
        );
    }
}