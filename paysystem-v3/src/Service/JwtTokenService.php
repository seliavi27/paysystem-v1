<?php
declare(strict_types=1);

namespace App\Service;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenService implements JwtTokenServiceInterface
{
    private string $secretKey;
    private string $algorithm;
    private int $ttl;

    public function __construct(string $secretKey, string $algorithm, int $ttl)
    {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
        $this->ttl = $ttl;
    }

    public function validate(?string $token): bool
    {
        try
        {
            JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function extractToken(?string $header): ?string
    {
        if (empty($header))
        {
            return null;
        }

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches))
        {
            return null;
        }

        return $matches[1];
    }

    public function decode(string $token): ?array
    {
        try
        {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array)$decoded;
        }
        catch (Exception $e)
        {
            return null;
        }
    }

    public function generate(array $payload): string
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->ttl;

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }
}