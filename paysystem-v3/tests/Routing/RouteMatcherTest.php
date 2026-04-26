<?php
declare(strict_types=1);

namespace PaySystem\Tests\Routing;

use PaySystem\Controller\AuthController;
use PaySystem\Controller\PaymentController;
use PaySystem\Controller\UserController;
use PaySystem\Infrastructure\RouterFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class RouteMatcherTest extends TestCase
{
    private static RouteCollection $routes;

    public static function setUpBeforeClass(): void
    {
        self::$routes = RouterFactory::loadRoutes(__DIR__ . '/../../src/Controller');
    }

    /**
     * @return iterable<string, array{string, string, string, array<string,string>}>
     */
    public static function knownRoutesProvider(): iterable
    {
        yield 'home'                 => ['GET',  '/',                                                AuthController::class    . '::loginForm',          []];
        yield 'login form'           => ['GET',  '/login',                                           AuthController::class    . '::loginForm',          []];
        yield 'login submit'         => ['POST', '/auth/login',                                      AuthController::class    . '::login',              []];
        yield 'register form'        => ['GET',  '/register',                                        AuthController::class    . '::registerForm',       []];
        yield 'register submit'      => ['POST', '/auth/register',                                   AuthController::class    . '::register',           []];
        yield 'logout'               => ['GET',  '/logout',                                          AuthController::class    . '::logout',             []];
        yield 'payments index'       => ['GET',  '/payments',                                        PaymentController::class . '::index',              []];
        yield 'payments create form' => ['GET',  '/payments/create',                                 PaymentController::class . '::createForm',         []];
        yield 'payments store'       => ['POST', '/payments/store',                                  PaymentController::class . '::store',              []];
        yield 'profile'              => ['GET',  '/profile',                                         UserController::class    . '::profile',            []];
        yield 'api list'             => ['GET',  '/api/payments',                                    PaymentController::class . '::showAllByUserId',    []];
        yield 'api create'           => ['POST', '/api/payments',                                    PaymentController::class . '::create',             []];
        yield 'api show by uuid'     => ['GET',  '/api/payments/aa65fa62-1ab0-4b69-bc52-335938e10e88', PaymentController::class . '::show',             ['id' => 'aa65fa62-1ab0-4b69-bc52-335938e10e88']];
        yield 'api by status'        => ['GET',  '/api/payments/status/pending',                     PaymentController::class . '::showAllByStatus',    ['status' => 'pending']];
        yield 'api refund'           => ['POST', '/api/payments/aa65fa62-1ab0-4b69-bc52-335938e10e88/refund', PaymentController::class . '::refund',    ['id' => 'aa65fa62-1ab0-4b69-bc52-335938e10e88']];
        yield 'users register'       => ['POST', '/users/register',                                  UserController::class    . '::create',             []];
        yield 'users show'           => ['GET',  '/users/aa65fa62-1ab0-4b69-bc52-335938e10e88',      UserController::class    . '::show',               ['id' => 'aa65fa62-1ab0-4b69-bc52-335938e10e88']];
    }

    /**
     * @param array<string,string> $expectedAttrs
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('knownRoutesProvider')]
    public function test_route_matches_and_returns_expected_controller(
        string $method,
        string $path,
        string $expectedController,
        array $expectedAttrs,
    ): void {
        $matcher = new UrlMatcher(self::$routes, new RequestContext('', $method));
        $params  = $matcher->match($path);

        self::assertSame($expectedController, $params['_controller']);
        foreach ($expectedAttrs as $key => $value) {
            self::assertSame($value, $params[$key]);
        }
    }

    public function test_invalid_uuid_does_not_match_show_route(): void
    {
        $matcher = new UrlMatcher(self::$routes, new RequestContext('', 'GET'));
        $this->expectException(ResourceNotFoundException::class);
        $matcher->match('/api/payments/foo');
    }

    public function test_invalid_status_does_not_match_status_route(): void
    {
        $matcher = new UrlMatcher(self::$routes, new RequestContext('', 'GET'));
        $this->expectException(ResourceNotFoundException::class);
        $matcher->match('/api/payments/status/zzz');
    }

    public function test_status_route_takes_precedence_over_uuid_route(): void
    {
        $matcher = new UrlMatcher(self::$routes, new RequestContext('', 'GET'));
        $params  = $matcher->match('/api/payments/status/pending');

        self::assertSame(PaymentController::class . '::showAllByStatus', $params['_controller']);
        self::assertSame('pending', $params['status']);
    }

    public function test_method_not_allowed_returns_405(): void
    {
        $matcher = new UrlMatcher(self::$routes, new RequestContext('', 'POST'));
        $this->expectException(MethodNotAllowedException::class);
        $matcher->match('/login');
    }

    public function test_unknown_route_returns_404(): void
    {
        $matcher = new UrlMatcher(self::$routes, new RequestContext('', 'GET'));
        $this->expectException(ResourceNotFoundException::class);
        $matcher->match('/this/does/not/exist');
    }
}
