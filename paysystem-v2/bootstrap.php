<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PaySystem\Application;
use PaySystem\Controller\AuthController;
use PaySystem\Controller\PaymentController;
use PaySystem\Controller\UserController;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Factory\PaymentMethodFactory;
use PaySystem\Middleware\AuthMiddleware;
use PaySystem\Middleware\LoggingMiddleware;
use PaySystem\Repository\PaymentRepository;
use PaySystem\Repository\UserRepository;
use PaySystem\Router;
use PaySystem\Service\AuthenticationService;
use PaySystem\Service\JwtTokenService;
use PaySystem\Service\LogService;
use PaySystem\Service\NotificationService;
use PaySystem\Service\PaymentService;
use PaySystem\Service\UserService;
use PaySystem\Storage\JsonStorage;
use PaySystem\Strategy\TieredFeeStrategy;
use PaySystem\View\TemplateEngine;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

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

$paymentFactory = new PaymentMethodFactory(
    stripeKey: $_ENV['STRIPE_API_KEY'] ?? '',
    stripeSecret: $_ENV['STRIPE_WEBHOOK_KEY'] ?? '',
    mollieKey: $_ENV['MOLLIE_API_KEY'] ?? '',
    mollieSecret: $_ENV['MOLLIE_WEBHOOK_KEY'] ?? '',
    flutterwaveKey: $_ENV['FLUTTERWAVE_API_KEY'] ?? '',
    flutterwaveSecret: $_ENV['FLUTTERWAVE_WEBHOOK_KEY'] ?? ''
);

$userJsonStorage = new JsonStorage(USERS_FILE);
$paymentJsonStorage = new JsonStorage(PAYMENTS_FILE);

$userRepository = new UserRepository($userJsonStorage);

$paymentRepository = new PaymentRepository($paymentJsonStorage);
$notificationService = new NotificationService([$logger]);
$logService = new LogService([$logger]);

$stripeProcessor = $paymentFactory->create(PaymentMethod::CREDIT_CARD);
$stripeProcessor->setCommissionStrategy(new TieredFeeStrategy());

$paymentService = new PaymentService(
    $stripeProcessor,
    $paymentRepository,
    $notificationService,
    $logService
);

$userService = new UserService($userRepository);
$authenticationService = new AuthenticationService($userService);
$jwtTokenService = new JwtTokenService($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM'], (int)$_ENV['JWT_TTL']);

$templateEngine = new TemplateEngine(TEMPLATES_PATH);

$paymentController = new PaymentController($templateEngine, $paymentService);
$userController = new UserController($templateEngine, $userService);
$authController = new AuthController($templateEngine, $authenticationService, $jwtTokenService);

$router = new Router();

$router->post('/auth/login', fn($req, $res) => $authController->loginForm($req, $res));
$router->post('/auth/logout', fn($req, $res) => $authController->logout($req, $res));
$router->get('/auth/profile', fn($req, $res) => $authController->profile($req, $res));

$router->get('/payments', fn($req, $res) => $paymentController->showAllByUserId($req, $res));
$router->get('/payments/{id}', fn($req, $res) => $paymentController->show($req, $res));
$router->post('/payments/{id}/refund', fn($req, $res) => $paymentController->refund($req, $res));
$router->get('/payments/{status}', fn($req, $res) => $paymentController->showAllByStatus($req, $res));

$router->post('/users/register', fn($req, $res) => $userController->create($req, $res));
$router->get('/users/{id}', fn($req, $res) => $userController->show($req, $res));


$middlewares = [
    new LoggingMiddleware($logService),
    new AuthMiddleware($jwtTokenService),
];

$app = new Application($router, $middlewares);

return [
    'app' => $app,
    'logger' => $logger,
    'paymentService' => $paymentService,
    'userService' => $userService,
];