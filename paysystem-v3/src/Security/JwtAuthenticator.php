<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

use App\Service\JwtTokenServiceInterface;

class JwtAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
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
    )
    {
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'message' => 'Authentication Required',
            'error' => 'JWT Token missing or invalid'
        ], Response::HTTP_UNAUTHORIZED);
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

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') &&
            str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $token = substr($authorizationHeader, 7);

        if (empty($token))
        {
            throw new AuthenticationException('No JWT token found');
        }

        return new SelfValidatingPassport(
            new UserBadge($token, function($userIdentifier) {
                return null;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Invalid credentials',
            'details' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
