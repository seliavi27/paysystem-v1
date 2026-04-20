<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PaySystem\Application;
use PaySystem\Controller\AuthController;
use PaySystem\Controller\PaymentController;
use PaySystem\Controller\UserController;
use PaySystem\Exception\ExceptionHandler;
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
use PaySystem\View\TemplateEngine;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

$session = new Session(new NativeSessionStorage());
$session->start();

//if (session_status() === PHP_SESSION_NONE)
//{
//    session_start();
//}

Dotenv::createImmutable(BASE_PATH)->safeLoad();

// ===== Logger =====
$logger = new Logger('paysystem');
$logger->pushHandler(new StreamHandler($_ENV['OPERATIONS_LOG'] ?? OPERATIONS_LOG, Logger::INFO));
$logger->pushHandler(new StreamHandler($_ENV['ERRORS_LOG'] ?? ERRORS_LOG, Logger::ERROR));

$logService = new LogService([$logger]);
$notificationService = new NotificationService([$logger]);

// ===== Repositories =====
$userRepository = new UserRepository(new JsonStorage(USERS_FILE));
$paymentRepository = new PaymentRepository(new JsonStorage(PAYMENTS_FILE));

// ===== Domain services =====
$paymentFactory = new PaymentMethodFactory(
    stripeKey: $_ENV['STRIPE_API_KEY'] ?? 'dev',
    stripeSecret: $_ENV['STRIPE_WEBHOOK_KEY'] ?? 'dev',
    mollieKey: $_ENV['MOLLIE_API_KEY'] ?? 'dev',
    mollieSecret: $_ENV['MOLLIE_WEBHOOK_KEY'] ?? 'dev',
    flutterwaveKey: $_ENV['FLUTTERWAVE_API_KEY'] ?? 'dev',
    flutterwaveSecret: $_ENV['FLUTTERWAVE_WEBHOOK_KEY'] ?? 'dev',
);

$paymentService = new PaymentService(
    $paymentFactory,
    $paymentRepository,
    $notificationService,
    $logService,
);

$userService = new UserService($userRepository);
$authenticationService = new AuthenticationService($userService);
$jwtTokenService = new JwtTokenService(
    $_ENV['JWT_SECRET'] ?? 'change-me',
    $_ENV['JWT_ALGORITHM'] ?? 'HS256',
    (int)($_ENV['JWT_TTL'] ?? 3600),
);

// ===== View + controllers =====
$templateEngine = new TemplateEngine(TEMPLATES_PATH);

$paymentController = new PaymentController($templateEngine, $paymentService);
$userController = new UserController($templateEngine, $userService, $paymentService);
$authController = new AuthController(
    $templateEngine,
    $authenticationService,
    $jwtTokenService,
    $userService,
);

// ===== Router =====
$router = new Router();

// HTML
$router->get('/', fn($req, $res) => $authController->loginForm($req, $res));
$router->get('/login', fn($req, $res) => $authController->loginForm($req, $res));
$router->post('/auth/login', fn($req, $res) => $authController->login($req, $res));
$router->get('/register', fn($req, $res) => $authController->registerForm($req, $res));
$router->post('/auth/register', fn($req, $res) => $authController->register($req, $res));
$router->get('/logout', fn($req, $res) => $authController->logout($req, $res));

$router->get('/profile',         fn($req, $res) => $userController->profile($req, $res));

$router->get('/payments', fn($req, $res) => $paymentController->index($req, $res));
$router->get('/payments/create', fn($req, $res) => $paymentController->createForm($req, $res));
$router->post('/payments/store', fn($req, $res) => $paymentController->store($req, $res));

// JSON API
$router->post('/api/payments', fn($req, $res) => $paymentController->create($req, $res));
$router->get('/api/payments', fn($req, $res) => $paymentController->showAllByUserId($req, $res));
$router->get('/api/payments/status/{status}', fn($req, $res) => $paymentController->showAllByStatus($req, $res));
$router->get('/api/payments/{id}', fn($req, $res) => $paymentController->show($req, $res));
$router->post('/api/payments/{id}/refund', fn($req, $res) => $paymentController->refund($req, $res));
$router->post('/users/register', fn($req, $res) => $userController->create($req, $res));
$router->get('/users/{id}', fn($req, $res) => $userController->show($req, $res));

// ===== Application =====
$app = new Application(
    router: $router,
    exceptionHandler: new ExceptionHandler($logService),
    middlewares: [
        new LoggingMiddleware($logService),
        new AuthMiddleware($jwtTokenService),
    ],
);

return [
    'app' => $app,
    'logger' => $logger,
    'paymentService' => $paymentService,
    'userService' => $userService,
];
