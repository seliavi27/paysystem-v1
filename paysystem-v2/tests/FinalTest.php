<?php

use Dotenv\Dotenv;
use PaySystem\Controller\PaymentController;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Factory\PaymentMethodFactory;
use PaySystem\Repository\PaymentRepository;
use PaySystem\Service\JwtTokenService;
use PaySystem\Service\LogService;
use PaySystem\Service\NotificationService;
use PaySystem\Service\PaymentService;
use PaySystem\Storage\JsonStorage;
use PaySystem\Strategy\TieredFeeStrategy;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

echo "<pre>";


$baseUrl = 'http://localhost:8000';

echo "=== ТЕСТИРОВАНИЕ PAYMENTS API ===\n\n";
echo "</br>";
echo "</br>";

// 1. Логин
echo "1. Авторизация...\n";
$loginData = [
    'email' => 'anton1999@gmail.com',
    'password' => 'anton1999'
];

$ch = curl_init($baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
//curl_close($ch);

if (!isset($data['data']['access_token'])) {
    die("❌ Ошибка авторизации\n");
}

$token = $data['data']['access_token'];
echo "Token: " . substr($token, 0, 50) . "...\n";
echo "</br>";
echo "✅ Авторизация успешна\n\n";
echo "</br>";



echo "</br>";
echo "2. Получение всех платежей...\n";
echo "</br>";
$ch = curl_init($baseUrl . '/payments');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);
//curl_close($ch);

var_dump($data);
if ($httpCode === 200)
{
    $count = isset($data['data']) ? count($data['data']) : 0;
    echo "✅ Получено {$count} платежей\n";
} else {
    echo "❌ Ошибка: HTTP {$httpCode}\n";
}
echo "\n";
echo "</br>";



echo "</br>";
echo "3. Получение платежей с фильтром по статусу...\n";
echo "</br>";
$ch = curl_init($baseUrl . '/payments/pending');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);
//curl_close($ch);

$count = isset($data['data']) ? count($data['data']) : 0;
echo "✅ Завершенных платежей: {$count}\n\n";
echo "</br>";



echo "</br>";
echo "4. Проверка доступа без токена...\n";
echo "</br>";
$ch = curl_init($baseUrl . '/payments');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//curl_close($ch);

if ($httpCode === 401) {
    echo "✅ Доступ запрещен (401) — правильно\n";
    echo "</br>";
} else {
    echo "❌ Ожидался 401, получен {$httpCode}\n";
    echo "</br>";
}

echo "</br>";
echo "\n=== ТЕСТ ЗАВЕРШЕН ===\n";



//$paymentFactory = new PaymentMethodFactory(
//    stripeKey: $_ENV['STRIPE_API_KEY'] ?? '',
//    stripeSecret: $_ENV['STRIPE_WEBHOOK_KEY'] ?? '',
//    mollieKey: $_ENV['MOLLIE_API_KEY'] ?? '',
//    mollieSecret: $_ENV['MOLLIE_WEBHOOK_KEY'] ?? '',
//    flutterwaveKey: $_ENV['FLUTTERWAVE_API_KEY'] ?? '',
//    flutterwaveSecret: $_ENV['FLUTTERWAVE_WEBHOOK_KEY'] ?? ''
//);
//
//$paymentJsonStorage = new JsonStorage(PAYMENTS_FILE);
//
//$paymentRepository = new PaymentRepository($paymentJsonStorage);
//$notificationService = new NotificationService([]);
//$logService = new LogService([]);
//
//$stripeProcessor = $paymentFactory->create(PaymentMethod::CREDIT_CARD);
//$stripeProcessor->setCommissionStrategy(new TieredFeeStrategy());
//
//$paymentService = new PaymentService(
//    $stripeProcessor,
//    $paymentRepository,
//    $notificationService,
//    $logService
//);
//
//$paymentController = new PaymentController($paymentService);
//
//$paymentJsonStorageResult = $paymentJsonStorage->load();
//var_dump($paymentJsonStorageResult);
//
//$paymentRepositoryResult = $paymentRepository->findById("0baf85db-21e7-4408-93dd-765873c18369");
//var_dump($paymentRepositoryResult);
//
//$paymentServiceResult = $paymentService->show("0baf85db-21e7-4408-93dd-765873c18369");
//var_dump($paymentServiceResult);
//
//$jwtTokenService = new JwtTokenService($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM'], (int)$_ENV['JWT_TTL']);
//$payload = $jwtTokenService->decode($token);
//var_dump($payload);
//
//$paymentRepositoryResult = $paymentRepository->findById("0baf85db-21e7-4408-93dd-765873c18369");
//var_dump($paymentRepositoryResult);
//
//$paymentByStatus = $paymentService->showAllByStatus(
//    "38f091f3-2f9a-43a6-9c61-037ff57f9dee",
//    "completed"
//);
//var_dump($paymentByStatus);

echo "</pre>";