<?php
declare(strict_types=1);

$container = require_once __DIR__ . '/../bootstrap.php';
$logger = $container['logger'];

use PaySystem\Enum\PaymentMethod;
use PaySystem\Notification\EmailNotificationChannel;
use PaySystem\Repository\PaymentRepository;
use PaySystem\Service\LogService;
use PaySystem\Service\PaymentService;
use PaySystem\Processor\StripeProcessor;
use PaySystem\Storage\JsonStorage;
use PaySystem\Strategy\TieredFeeStrategy;

try
{
//    $processor = new StripeProcessor(
//        $_ENV['STRIPE_API_KEY'] ?? '',
//        $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '',
//        0.029
//    );
//
//    $storage = new JsonStorage(
//        "context"
//    );
//
//    $repository = new PaymentRepository(
//        $storage
//    );
//
//    $notification = new EmailNotificationChannel();
//
//    $log = new LogService(
//        [$logger]
//    );
//
//    $service = new PaymentService(
//        $processor, $repository, $notification);
//
//    $logger->info('PaySystem started successfully');
//    echo "PaySystem v2.0 ready!\n";


    $factory = $container['payment.factory']();

    $stripeProcessor = $factory->create(PaymentMethod::CREDIT_CARD);
    $stripeProcessor->setCommissionStrategy(new TieredFeeStrategy());
    $commission = $stripeProcessor->calculateCommission(500);
    echo "Commission: " . $commission . "<br/>";

    $sameProcessor = $factory->create(PaymentMethod::CREDIT_CARD);
    $sameProcessor->setCommissionStrategy(new TieredFeeStrategy());
    $commission = $stripeProcessor->calculateCommission(2500);
    echo "Commission: " . $commission . "<br/>";

    $mollieProcessor = $factory->create(PaymentMethod::BANK_TRANSFER);
    $sameProcessor->setCommissionStrategy(new TieredFeeStrategy());
    $commission = $stripeProcessor->calculateCommission(10500);
    echo "Commission: " . $commission . "<br/>";

    $created = $factory->getAll();

}
catch (Exception $e)
{
    $logger->error('Failed to start PaySystem', ['error' => $e->getMessage()]);
    echo "Error: " . $e->getMessage() . "<br/>";
}

$page = getCurrentPage();
renderPage($page);

