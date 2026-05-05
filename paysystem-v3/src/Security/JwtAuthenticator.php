<?php
declare(strict_types=1);

namespace App\Security;

use App\Service\JwtTokenServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class JwtAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private const string COOKIE_NAME = 'access_token';

    public function __construct(
        private readonly JwtTokenServiceInterface $jwtTokenService,
        private readonly UserProviderInterface $userProvider,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->cookies->has(self::COOKIE_NAME);
    }

    public function authenticate(Request $request): Passport
    {
        $token = (string) $request->cookies->get(self::COOKIE_NAME, '');

        if ($token === '') {
            throw new CustomUserMessageAuthenticationException('No JWT token in cookie');
        }

        $payload = $this->jwtTokenService->decode($token);

        if (!is_array($payload) || !isset($payload['email'])) {
            throw new CustomUserMessageAuthenticationException('Invalid JWT payload');
        }

        $identifier = (string) $payload['email'];

        return new SelfValidatingPassport(
            new UserBadge(
                $identifier,
                fn(string $id) => $this->userProvider->loadUserByIdentifier($id),
            ),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($this->isApiRequest($request)) {
            return new JsonResponse(
                ['error' => 'Unauthorized', 'message' => $exception->getMessageKey()],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        return new RedirectResponse('/login');
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        if ($this->isApiRequest($request)) {
            return new JsonResponse(
                ['error' => 'Authentication required'],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        return new RedirectResponse('/login');
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/');
    }
}
