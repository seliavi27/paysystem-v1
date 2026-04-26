<?php
declare(strict_types=1);

namespace App\Interface;

interface WebhookableInterface
{
    public function handleWebhook(array $payload): void;
}
