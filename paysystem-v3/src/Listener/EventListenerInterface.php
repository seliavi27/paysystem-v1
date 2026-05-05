<?php
declare(strict_types=1);

namespace App\Listener;

use App\Event\EventInterface;

interface EventListenerInterface
{
    public function handle(EventInterface $event): void;
}