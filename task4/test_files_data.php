<?php
declare(strict_types=1);

require_once "json_storage.php";

echo "<pre>";
echo "---------------json_storage.php-----------------" . "</br>";

$payments = [
    ['id' => 1, 'amount' => 100, 'status' => 'completed'],
    ['id' => 2, 'amount' => 200, 'status' => 'pending'],
];
$result = save_payments_to_json($payments, 'payments.json');
echo var_export($result, true) . "</br>";

$result = load_payments_from_json('payments.json');
echo var_export($result, true) . "</br>";

$payment = [
    'amount' => 300,
    'status' => 'pending',
    'date' => date('Y-m-d')];
$result = add_payment_to_storage($payment, 'payments.json');
echo var_export($result, true) . "</br>";

$result = update_payment_status(1, 'completed', 'payments.json');
echo var_export($result, true) . "</br>";









//echo "</br>";
//echo "---------------validators_advanced.php-----------------" . "</br>";

//$result = slugify_payment_id("Payment #12345");
//echo print_r($result, true) . "</br>";
//$result = slugify_payment_id("Оплата заказа");
//echo print_r($result, true) . "</br>";


//$result = validate_credit_card("4111 1111 1111 1111");
//echo var_export($result, true) . "</br>";
//$result = validate_credit_card("4111111111111111");
//echo var_export($result, true) . "</br>";
//$result = validate_credit_card("4111111111111112");
//echo var_export($result, true) . "</br>";
//$result = validate_credit_card("invalid");
//echo var_export($result, true) . "</br>";


echo "</pre>";