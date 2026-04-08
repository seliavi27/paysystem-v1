<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

require ROUTER_PATH;
require AUTH_PATH;
require FUNCTIONS_PATH;
require SECURITY_PATH;
require LOGGER_PATH;
require DATABASE_PATH;

if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

$logger = new Logger('paysystem');

$logger->pushHandler(new StreamHandler(
    $_ENV['OPERATIONS_LOG'] ?? OPERATIONS_LOG,
    Logger::INFO
));

$logger->pushHandler(new StreamHandler(
    $_ENV['ERRORS_LOG'] ?? ERRORS_LOG,
    Logger::ERROR
));

return [
    'logger' => $logger,
];