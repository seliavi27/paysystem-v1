<?php
declare(strict_types=1);

$container = require_once __DIR__ . '/../bootstrap.php';

try
{
    $app = $container['app'];
    $app->run();
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