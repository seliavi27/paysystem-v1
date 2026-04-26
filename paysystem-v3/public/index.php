<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$container = require_once __DIR__ . '/../bootstrap.php';

try
{
    /** @var PaySystem\Application $app */
    $app = $container['app'];

    $request  = Request::createFromGlobals();
    $request->setSession($container['session']);

    $response = $app->handle($request);
    $response->send();
}
catch (Throwable $e)
{
    $container['logger']->error('Bootstrap error', ['exception' => (string)$e]);

    (new Response(
        json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE),
        Response::HTTP_INTERNAL_SERVER_ERROR,
        ['Content-Type' => 'application/json; charset=UTF-8']
    ))->send();
}
