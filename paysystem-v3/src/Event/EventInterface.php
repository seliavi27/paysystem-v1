<?php
declare(strict_types=1);

namespace PaySystem\Event;
interface EventInterface
{
    public function getName(): string;

    public function getPayload(): array;
}