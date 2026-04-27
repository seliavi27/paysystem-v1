<?php
declare(strict_types=1);

use PaySystem\Application;
use PaySystem\Exception\ExceptionHandler;
use PaySystem\Infrastructure\ContainerFactory;
use PaySystem\Infrastructure\RouterFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require __DIR__ . '/../vendor/autoload.php';

$projectDir = dirname(__DIR__);
$isDebug    = ($_SERVER['APP_DEBUG'] ?? '0') === '1';

$container = ContainerFactory::build($projectDir, $isDebug);

// Session — общий объект на запрос (synthetic в services.yaml)
$session = new Session(new NativeSessionStorage());
$session->start();
$container->set(Session::class, $session);

$request = Request::createFromGlobals();
$request->setSession($session);

// Routing — RouteCollection собираем из атрибутов контроллеров
$routes  = RouterFactory::loadRoutes($projectDir . '/src/Controller');
$context = (new RequestContext())->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

// UrlGenerator — synthetic, нужен AuthController/PaymentController
$container->set(UrlGenerator::class, new UrlGenerator($routes, $context));

try {
    $parameters = $matcher->match($request->getPathInfo());
    $request->attributes->add($parameters);

    $controllerId       = (string) $request->attributes->get('_controller');
    [$class, $method]   = explode('::', $controllerId, 2);
    $controllerInstance = $container->get($class);
    $request->attributes->set('_controller', [$controllerInstance, $method]);

    $response = $container->get(Application::class)->handle($request);
} catch (ResourceNotFoundException) {
    $response = new JsonResponse(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
} catch (MethodNotAllowedException $e) {
    $response = new JsonResponse(
        ['error' => 'Method Not Allowed', 'allowed' => $e->getAllowedMethods()],
        Response::HTTP_METHOD_NOT_ALLOWED,
        ['Allow' => implode(', ', $e->getAllowedMethods())],
    );
} catch (Throwable $e) {
    $response = $container->get(ExceptionHandler::class)->handle($e, $request);
}

$response->send();
