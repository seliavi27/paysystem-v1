<?php
declare(strict_types=1);

function processWithAnyProcessor(
    AbstractPaymentProcessor $processor, Payment $payment): void
{
    $processor->process($payment);
}

$stripe = new StripeProcessor("stripeApiKey", "publishableKey", 0.5);
$mollie = new MollieProcessor("mollieApiKey", "publishableKey", 1.5);
$flutterwave = new FlutterwaveProcessor("flutterwaveApiKey", "publishableKey", 1);

processWithAnyProcessor($stripe, );