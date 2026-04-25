<?php
declare(strict_types=1);

use PaySystem\Application;
use PaySystem\Infrastructure\ContainerFactory;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

use PaySystem\Infrastructure\RouterFactory;

require __DIR__ . '/../vendor/autoload.php';

try
{
    $container = ContainerFactory::build(
        projectDir: dirname(__DIR__),
        isDebug: ($_SERVER['APP_DEBUG'] ?? '0') === '1',
    );

    $request  = Request::createFromGlobals();
    $response = $container->get(Application::class)->handle($request);
}
catch (ServiceNotFoundException | ServiceNotFoundException | ResourceNotFoundException)
{
    $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
}

$response->send();