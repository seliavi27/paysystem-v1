<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$container = require_once __DIR__ . '/../bootstrap.php';

/** @var \Symfony\Component\Routing\RouteCollection $routes */
$routes = $container['routes'];
/** @var RequestContext $context */
$context = $container['requestContext'];

$request = Request::createFromGlobals();
$request->setSession($container['session']);

$context->fromRequest($request);

$cacheFile = __DIR__ . '/../var/cache/matcher.php';
$isProd    = ($_ENV['APP_ENV'] ?? 'dev') === 'prod';

if ($isProd) {
    if (!file_exists($cacheFile)) {
        $compiled = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
        file_put_contents($cacheFile, '<?php return ' . var_export($compiled, true) . ';');
    }
    $matcher = new CompiledUrlMatcher(require $cacheFile, $context);
} else {
    $matcher = new UrlMatcher($routes, $context);
}

try
{
    $parameters = $matcher->match($request->getPathInfo());
    $request->attributes->add($parameters);

    $controllerId = (string)$request->attributes->get('_controller');
    [$class, $method] = explode('::', $controllerId, 2);

    $instance = $container['controllers'][$class] ?? null;
    if ($instance === null) {
        throw new RuntimeException("Controller {$class} is not registered in container.");
    }
    $request->attributes->set('_controller', [$instance, $method]);

    /** @var PaySystem\Application $app */
    $app = $container['app'];
    $response = $app->handle($request);
}
catch (ResourceNotFoundException)
{
    $response = new JsonResponse(['error' => 'Not Found'], Response::HTTP_NOT_FOUND);
}
catch (MethodNotAllowedException $e)
{
    $response = new JsonResponse(
        ['error' => 'Method Not Allowed', 'allowed' => $e->getAllowedMethods()],
        Response::HTTP_METHOD_NOT_ALLOWED,
        ['Allow' => implode(', ', $e->getAllowedMethods())],
    );
}
catch (Throwable $e)
{
    $container['logger']->error('Bootstrap error', ['exception' => (string)$e]);
    $response = new JsonResponse(
        ['error' => 'Internal Server Error', 'message' => $e->getMessage()],
        Response::HTTP_INTERNAL_SERVER_ERROR,
    );
}

$response->send();
