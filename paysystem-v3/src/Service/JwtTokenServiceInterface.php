<?php
declare(strict_types=1);

namespace App\Service;

interface JwtTokenServiceInterface
{
    public function validate(?string $token): bool;
    public function decode(string $token): ?array;
    public function generate(array $payload): string;
    public function extractToken(?string $header): ?string;
}