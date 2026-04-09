<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PaySystem\Factory\PaymentMethodFactory;

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

$container = [];

$container['logger'] = function() {
    $logger = new Logger('paysystem');

    $logger->pushHandler(new StreamHandler(
        $_ENV['OPERATIONS_LOG'] ?? OPERATIONS_LOG,
        Logger::INFO
    ));

    $logger->pushHandler(new StreamHandler(
        $_ENV['ERRORS_LOG'] ?? ERRORS_LOG,
        Logger::ERROR
    ));

    return $logger;
};

$container['payment.factory'] = function() {
    return new PaymentMethodFactory(
        stripeKey: $_ENV['STRIPE_API_KEY'],
        stripeSecret: $_ENV['STRIPE_WEBHOOK_KEY'],
        mollieKey: $_ENV['MOLLIE_API_KEY'],
        mollieSecret: $_ENV['MOLLIE_WEBHOOK_KEY'],
        flutterwaveKey: $_ENV['FLUTTERWAVE_API_KEY'],
        flutterwaveSecret: $_ENV['FLUTTERWAVE_WEBHOOK_KEY']
    );
};

return $container;