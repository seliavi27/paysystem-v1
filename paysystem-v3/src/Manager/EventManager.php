<?php
declare(strict_types=1);

namespace App\Manager;

use App\Event\EventInterface;
use App\Listener\EventListenerInterface;

class EventManager
{
    private array $listeners = [];

    public function subscribe(
        string $eventName, EventListenerInterface $listener): void
    {
        if (!isset($this->listeners[$eventName]))
        {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(EventInterface $event): void
    {
        $eventName = $event->getName();

        if (isset($this->listeners[$eventName]))
        {
            foreach ($this->listeners[$eventName] as $listener)
            {
                $listener->handle($event);
            }
        }
    }
}