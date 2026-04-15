<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\AuthenticationServiceInterface;
use PaySystem\Service\JwtTokenServiceInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private JwtTokenServiceInterface $jwtTokenService;

    public function __construct(
        JwtTokenServiceInterface $jwtTokenService
    )
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    public function handle(Request $request, Response $response): void
    {
        $path = $request->getPath();

        $publicRoutes = [
            '/login',
            '/auth/login',
            '/auth/register'];

        if (in_array($path, $publicRoutes, true))
        {
            return;
        }

        $token = $_COOKIE['access_token'] ?? null;

        if (!$token || !$this->jwtTokenService->validate($token))
        {
            $response->setStatusCode(401)
                ->setJson([
                    'error' => 'Unauthorized',
                    'message' => 'Invalid or missing authentication token'
                ])
                ->send();

            exit;
        }

        $payload = $this->jwtTokenService->decode($token);

        if (!$payload || !isset($payload['user_id']))
        {
            $response->setStatusCode(401)
            ->setJson([
                'error' => 'Unauthorized'
            ])
            ->send();

            exit;
        }

        $request->setAttribute('userId', $payload['user_id']);
    }
}