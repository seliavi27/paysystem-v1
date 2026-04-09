<?php
declare(strict_types=1);

namespace PaySystem\Listener;

use PaySystem\Event\EventInterface;

interface EventListenerInterface
{
    public function handle(EventInterface $event): void;
}