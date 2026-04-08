<?php
declare(strict_types=1);

$container = require_once __DIR__ . '/../bootstrap.php';
$logger = $container['logger'];

use PaySystem\Notification\EmailNotificationChannel;
use PaySystem\Repository\PaymentRepository;
use PaySystem\Service\LogService;
use PaySystem\Service\PaymentService;
use PaySystem\Processor\StripeProcessor;
use PaySystem\Storage\JsonStorage;

try
{
    $processor = new StripeProcessor(
        $_ENV['STRIPE_API_KEY'] ?? '',
        $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',
        0.029
    );

    $storage = new JsonStorage(
        "context"
    );

    $repository = new PaymentRepository(
        $storage
    );

    $notification = new EmailNotificationChannel();

    $log = new LogService(
        [$logger]
    );

    $service = new PaymentService(
        $processor, $repository, $notification);

    $logger->info('PaySystem started successfully');
    echo "PaySystem v2.0 ready!\n";

}
catch (Exception $e)
{
    $logger->error('Failed to start PaySystem', ['error' => $e->getMessage()]);
    echo "Error: " . $e->getMessage() . "\n";
}

$page = getCurrentPage();
renderPage($page);

