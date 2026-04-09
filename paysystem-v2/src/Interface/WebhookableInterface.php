<?php
declare(strict_types=1);

interface WebhookableInterface
{
    public function handleWebhook(array $payload): void;
}
