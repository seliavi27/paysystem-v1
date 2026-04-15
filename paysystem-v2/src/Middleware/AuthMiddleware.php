<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\AuthenticationServiceInterface;
use PaySystem\Service\JwtTokenServiceInterface;

class AuthMiddleware implements MiddlewareInterface
{
//    private AuthenticationServiceInterface $authenticationService;
    private JwtTokenServiceInterface $jwtTokenService;

    public function __construct(
//        AuthenticationServiceInterface $authenticationService,
        JwtTokenServiceInterface $jwtTokenService
    )
    {
//        $this->authenticationService = $authenticationService;
        $this->jwtTokenService = $jwtTokenService;
    }

    public function handle(Request $request, Response $response): void
    {
        $path = $request->getPath();

        $publicRoutes = [
            '/auth/login',
            '/auth/register'];

        if (in_array($path, $publicRoutes, true))
        {
            return;
        }

//        if (mb_strpos($path, '/auth/') !== false)
//        {
//            return;
//        }

        $authHeader = $request->getHeader('Authorization');
        $token = $this->jwtTokenService->extractToken($authHeader);

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