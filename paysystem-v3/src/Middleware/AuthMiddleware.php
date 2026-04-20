<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function handle(Request $request, Response $response): ?Response
    {
        $path = $request->getPathInfo();

        if (in_array($path, self::PUBLIC_ROUTES, true))
        {
            return null;
        }

        $token = $request->cookies->get('access_token');
        $payload = $token ? $this->jwtTokenService->decode($token) : null;

        if ($payload && isset($payload['user_id']))
        {
            $request->attributes->set('userId', $payload['user_id']);
            return null;
        }

        if ($this->isApiRequest($request->getPathInfo()))
        {
            return new JsonResponse(
                ['error' => 'Unauthorized', 'message' => 'Invalid or missing token'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new RedirectResponse('/login');
    }

    private function isApiRequest(string $path): bool
    {
        return str_starts_with($path, '/api/')
            || str_starts_with($path, '/auth/')
            || str_starts_with($path, '/users/');
    }
}
