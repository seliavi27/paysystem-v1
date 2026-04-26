<?php
declare(strict_types=1);

namespace PaySystem\Tests\Middleware;

use PaySystem\Middleware\AuthMiddleware;
use PaySystem\Service\JwtTokenServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class AuthMiddlewareTest extends TestCase
{
    public function test_public_route_passes_through(): void
    {
        $jwt = $this->createMock(JwtTokenServiceInterface::class);
        $jwt->expects(self::never())->method('decode');

        $middleware = new AuthMiddleware($jwt);
        $request = Request::create('/login', 'GET');

        self::assertNull($middleware->handle($request));
    }

    public function test_html_request_without_token_redirects_to_login(): void
    {
        $jwt = $this->createMock(JwtTokenServiceInterface::class);

        $middleware = new AuthMiddleware($jwt);
        $request = Request::create('/payments', 'GET');

        $response = $middleware->handle($request);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/login', $response->getTargetUrl());
    }

    public function test_api_request_without_token_returns_401_json(): void
    {
        $jwt = $this->createMock(JwtTokenServiceInterface::class);

        $middleware = new AuthMiddleware($jwt);
        $request = Request::create('/api/payments', 'GET');

        $response = $middleware->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(401, $response->getStatusCode());
        self::assertSame(
            ['error' => 'Unauthorized', 'message' => 'Invalid or missing token'],
            json_decode((string)$response->getContent(), true)
        );
    }

    public function test_valid_token_sets_user_id_attribute_and_passes_through(): void
    {
        $jwt = $this->createMock(JwtTokenServiceInterface::class);
        $jwt->method('decode')->willReturn(['user_id' => 'user-42', 'email' => 'a@b.c']);

        $middleware = new AuthMiddleware($jwt);
        $request = Request::create('/payments', 'GET');
        $request->cookies->set('access_token', 'jwt.payload.signature');

        self::assertNull($middleware->handle($request));
        self::assertSame('user-42', $request->attributes->get('userId'));
    }
}
