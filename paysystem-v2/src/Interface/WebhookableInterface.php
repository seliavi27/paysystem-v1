<?php
declare(strict_types=1);

namespace PaySystem\Interface;

interface WebhookableInterface
{
    public function handleWebhook(array $payload): void;
}
