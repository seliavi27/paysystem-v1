<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;

$container = require_once __DIR__ . '/../bootstrap.php';

try
{
    /** @var PaySystem\Application $app */
    $app = $container['app'];

    $request  = Request::createFromGlobals();
    $response = $app->handle($request);
    $response->send();
}
catch (Exception $e)
{
    http_response_code(500);

    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);

    $container['logger']->error('Application error', ['exception' => (string)$e]);
}