<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

use PaySystem\Infrastructure\RouterFactory;

$container = require_once __DIR__ . '/../bootstrap.php';

$request  = Request::createFromGlobals();
$routes   = RouterFactory::loadRoutes(__DIR__ . '/../src/Controller');
$context  = new RequestContext()->fromRequest($request);
$matcher  = new UrlMatcher($routes, $context);

try
{
    $parameters = $matcher->match($request->getPathInfo());
    $request->attributes->add($parameters);

    /** @var PaySystem\Application $app */
    $response = $container['app']->handle($request);
}
catch (ResourceNotFoundException)
{
    $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
}

$response->send();