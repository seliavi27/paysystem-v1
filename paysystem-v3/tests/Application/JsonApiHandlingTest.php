<?php
declare(strict_types=1);

namespace PaySystem\Tests\Application;

use PaySystem\Application;
use PaySystem\Controller\PaymentController;
use PaySystem\Exception\ExceptionHandler;
use PaySystem\Interface\LogServiceInterface;
use PaySystem\Middleware\AuthMiddleware;
use PaySystem\Middleware\LoggingMiddleware;
use PaySystem\Router;
use PaySystem\Service\JwtTokenServiceInterface;
use PaySystem\Service\PaymentServiceInterface;
use PaySystem\View\TemplateEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class JsonApiHandlingTest extends TestCase
{
    public function test_unauthorized_api_request_returns_401_json(): void
    {
        $app = $this->buildApp(
            jwt: $this->createMock(JwtTokenServiceInterface::class),
            paymentService: $this->createMock(PaymentServiceInterface::class),
        );

        $request = Request::create('/api/payments', 'GET');

        $response = $app->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(401, $response->getStatusCode());
    }

    public function test_authorized_api_request_returns_payment_list(): void
    {
        $jwt = $this->createMock(JwtTokenServiceInterface::class);
        $jwt->method('decode')->willReturn(['user_id' => 'user-42']);

        $payments = $this->createMock(PaymentServiceInterface::class);
        $payments->expects(self::once())
            ->method('showAllByUserId')
            ->with('user-42')
            ->willReturn([]);

        $app = $this->buildApp(jwt: $jwt, paymentService: $payments);

        $request = Request::create('/api/payments', 'GET');
        $request->cookies->set('access_token', 'jwt.payload.signature');

        $response = $app->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(
            ['success' => true, 'count' => 0, 'data' => []],
            json_decode((string)$response->getContent(), true)
        );
    }

    public function test_unknown_route_returns_404_json(): void
    {
        $jwt = $this->createMock(JwtTokenServiceInterface::class);
        $jwt->method('decode')->willReturn(['user_id' => 'user-42']);

        $app = $this->buildApp(
            jwt: $jwt,
            paymentService: $this->createMock(PaymentServiceInterface::class),
        );

        $request = Request::create('/api/nope', 'GET');
        $request->cookies->set('access_token', 'jwt.payload.signature');

        $response = $app->handle($request);

        self::assertSame(404, $response->getStatusCode());
        self::assertSame(['error' => 'Not Found'], json_decode((string)$response->getContent(), true));
    }

    private function buildApp(
        JwtTokenServiceInterface $jwt,
        PaymentServiceInterface $paymentService,
    ): Application {
        $logger = $this->createMock(LogServiceInterface::class);
        $templateEngine = $this->createMock(TemplateEngine::class);

        $controller = new PaymentController($templateEngine, $paymentService);

        $router = new Router();
        $router->get('/api/payments', fn($req) => $controller->showAllByUserId($req));

        return new Application(
            router: $router,
            exceptionHandler: new ExceptionHandler($logger),
            middlewares: [
                new LoggingMiddleware($logger),
                new AuthMiddleware($jwt),
            ],
        );
    }
}
