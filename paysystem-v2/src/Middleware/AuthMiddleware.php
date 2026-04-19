<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\JwtTokenServiceInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private const PUBLIC_ROUTES = [
        '/',
        '/login',
        '/register',
        '/auth/login',
        '/auth/register',
        '/users/register',
    ];

    public function __construct(
        private JwtTokenServiceInterface $jwtTokenService
    ) {
    }

    public function handle(Request $request, Response $response): void
    {
        $path = $request->getPath();

        if (in_array($path, self::PUBLIC_ROUTES, true))
        {
            return;
        }

        $token = $_COOKIE['access_token'] ?? null;
        $payload = $token ? $this->jwtTokenService->decode($token) : null;

        if ($payload && isset($payload['user_id'])) {
            $request->setAttribute('userId', $payload['user_id']);

            return;
        }

        if ($this->isApiRequest($path))
        {
            $response->setStatusCode(401)
                ->setJson(['error' => 'Unauthorized', 'message' => 'Invalid or missing token'])
                ->send();

            return;
        }

        $response->setStatusCode(302)
            ->setHeader('Location', '/login')
            ->send();
    }

    private function isApiRequest(string $path): bool
    {
        return str_starts_with($path, '/api/')
            || str_starts_with($path, '/auth/')
            || str_starts_with($path, '/users/');
    }
}
