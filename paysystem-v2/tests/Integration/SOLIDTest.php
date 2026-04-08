<?php
declare(strict_types=1);

use PaySystem\Entity\Payment;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Processor\AbstractPaymentProcessor;
use PaySystem\Processor\FlutterwaveProcessor;
use PaySystem\Processor\MollieProcessor;
use PaySystem\Processor\StripeProcessor;

require __DIR__ . '/../../config/config.php';
require ROUTER_PATH;

require AUTH_PATH;
require FUNCTIONS_PATH;
require SECURITY_PATH;
require LOGGER_PATH;

require DATABASE_PATH;

// Trait
require TIMESTAMPABLE_PATH;
require LOGGABLE_PATH;
require HASUUID_PATH;

// Entity
require PAYMENT_PATH;
require USER_PATH;
require TRANSACTION_PATH;
require CURRENCY_TYPE_PATH;
require PAYMENT_STATUS_PATH;
require PAYMENT_METHOD_PATH;
require TRANSACTION_TYPE_PATH;

// DTO
require CREATE_PAYMENT_REQUEST_PATH;
require PAYMENT_RESPONSE_PATH;
require TRANSACTION_REQUEST_PATH;
require REFUND_REQUEST_PATH;

// VALIDATOR
require USER_VALIDATOR_PATH;

// Interface
require PAYMENT_PROCESSOR_INTERFACE_PATH;
require STORAGE_INTERFACE_PATH;
require VALIDATOR_INTERFACE_PATH;
require COMMISSIONABLE_INTERFACE_PATH;
require PROCESSABLE_INTERFACE_PATH;
require REFUNDABLE_INTERFACE_PATH;
require WEBHOOKABLE_INTERFACE_PATH;
require LOG_SERVICE_INTERFACE_PATH;

// Processor
require ABSTRACT_PAYMENT_PROCESSOR;
require STRIPE_PATH;
require MOLLIE_PATH;
require FLUTTERWAVE_PATH;

// Service
require PAYMENT_SERVICE_PATH;
require USER_SERVICE_PATH;
require AUTHENTICATION_SERVICE_PATH;
require NOTIFICATION_SERVICE_PATH;
require LOG_SERVICE_PATH;

// Notification
require NOTIFICATION_CHANNEL_INTERFACE_PATH;
require EMAIL_NOTIFICATION_CHANNEL_PATH;
require SMS_NOTIFICATION_CHANNEL_PATH;
require WEBHOOK_NOTIFICATION_CHANNEL_PATH;
require LOG_NOTIFICATION_CHANNEL_PATH;

// Notification
require REPOSITORY_INTERFACE_PATH;
require PAYMENT_REPOSITORY_PATH;
require USER_REPOSITORY_PATH;
require TRANSACTION_REPOSITORY_PATH;


function processWithAnyProcessor(
    AbstractPaymentProcessor $processor, Payment $payment): string
{
    $result = "OK";

    try
    {
        $processor->process($payment);
    }
    catch (RuntimeException $exception)
    {
        $result = $exception->getMessage();
    }

    return $result;
}

$stripe = new StripeProcessor("stripeApiKey", "publishableKey", 0.5);
$mollie = new MollieProcessor("mollieApiKey", "publishableKey", 1.5);
$flutterwave = new FlutterwaveProcessor("flutterwaveApiKey", "publishableKey", 1);

$payment = Payment::create(
    "38f091f3-2f9a-43a6-9c61-037ff57f9dee",
    140,
    "Pay",
    CurrencyType::USD,
    PaymentMethod::CREDIT_CARD
);

$result = processWithAnyProcessor($stripe, $payment);
echo "<br/>";
echo var_dump($result, true) . "<br/>";

$result = processWithAnyProcessor($mollie, $payment);
echo "<br/>";
echo var_dump($result, true) . "<br/>";

$result = processWithAnyProcessor($flutterwave, $payment);
echo "<br/>";
echo var_dump($result, true) . "<br/>";