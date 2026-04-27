<?php
declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use PaySystem\Application;
use PaySystem\Controller\AuthController;
use PaySystem\Controller\PaymentController;
use PaySystem\Controller\UserController;
use PaySystem\Exception\ExceptionHandler;
use PaySystem\Factory\PaymentMethodFactory;
use PaySystem\Infrastructure\RouterFactory;
use PaySystem\Middleware\AuthMiddleware;
use PaySystem\Middleware\LoggingMiddleware;
use PaySystem\Repository\PaymentRepository;
use PaySystem\Repository\TransactionRepository;
use PaySystem\Repository\UserRepository;
use PaySystem\Service\AuthenticationService;
use PaySystem\Service\JwtTokenService;
use PaySystem\Service\LogService;
use PaySystem\Service\NotificationService;
use PaySystem\Service\PaymentService;
use PaySystem\Service\UserService;
use PaySystem\View\TemplateEngine;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

Dotenv::createImmutable(BASE_PATH)->safeLoad();

$session = new Session(new NativeSessionStorage());
$session->start();

// ===== Logger =====
$logger = new Logger('paysystem');
$logger->pushHandler(new StreamHandler($_ENV['OPERATIONS_LOG'] ?? OPERATIONS_LOG, Logger::INFO));
$logger->pushHandler(new StreamHandler($_ENV['ERRORS_LOG'] ?? ERRORS_LOG, Logger::ERROR));

$logService = new LogService([$logger]);
$notificationService = new NotificationService([$logger]);

// ===== Database =====
if (empty($_ENV['DATABASE_URL'])) {
    throw new RuntimeException(
        'DATABASE_URL is required. Copy .env.example to .env or export DATABASE_URL in the environment.'
    );
}

$dsnParser  = new DsnParser(['postgres' => 'pdo_pgsql', 'postgresql' => 'pdo_pgsql']);
$connection = DriverManager::getConnection($dsnParser->parse($_ENV['DATABASE_URL']));

// ===== Repositories =====
$userRepository        = new UserRepository($connection);
$paymentRepository     = new PaymentRepository($connection);
$transactionRepository = new TransactionRepository($connection);

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
    $transactionRepository,
    $notificationService,
    $logService,
    $connection,
);

$userService = new UserService($userRepository);
$authenticationService = new AuthenticationService($userService);
$jwtTokenService = new JwtTokenService(
    $_ENV['JWT_SECRET'] ?? 'change-me',
    $_ENV['JWT_ALGORITHM'] ?? 'HS256',
    (int)($_ENV['JWT_TTL'] ?? 3600),
);

// ===== Routing =====
$routes  = RouterFactory::loadRoutes(__DIR__ . '/src/Controller');
$context = new RequestContext();

/** @var UrlGeneratorInterface $urlGenerator */
$urlGenerator = new UrlGenerator($routes, $context);

// ===== View + controllers =====
$templateEngine = new TemplateEngine(TEMPLATES_PATH);

$paymentController = new PaymentController($templateEngine, $paymentService, $urlGenerator);
$userController    = new UserController($templateEngine, $userService, $paymentService);
$authController    = new AuthController(
    $templateEngine,
    $authenticationService,
    $jwtTokenService,
    $userService,
    $session,
    $urlGenerator,
);

// ===== Application =====
$app = new Application(
    controllerResolver: new ControllerResolver(),
    argumentResolver:   new ArgumentResolver(),
    exceptionHandler:   new ExceptionHandler($logService),
    middlewares: [
        new LoggingMiddleware($logService),
        new AuthMiddleware($jwtTokenService),
    ],
);

return [
    'app'                 => $app,
    'logger'              => $logger,
    'session'             => $session,
    'routes'              => $routes,
    'requestContext'      => $context,
    'urlGenerator'        => $urlGenerator,
    'controllers'         => [
        AuthController::class    => $authController,
        PaymentController::class => $paymentController,
        UserController::class    => $userController,
    ],
    Connection::class     => $connection,
];
