<?php
declare(strict_types=1);

require_once 'validators.php';
require_once 'formatters.php';
require_once 'calculators.php';
require_once 'processors.php';


echo "---------------validators.php-----------------" . "</br>";

$email1 = "anton@gmail.com";
$result = validate_email($email1);
echo "$email1 - " . var_export($result, true) . "</br>";

$email2 = "invalid-email";
$result = validate_email($email2);
echo "$email2 - " . var_export($result, true) . "</br>";

$email3 = "";
$result = validate_email($email3);
echo "$email3 - " . var_export($result, true) . "</br>";
echo "</br>";


$phone1 = "+7 (999) 123-45-67";
$result = validate_phone($phone1);
echo "$phone1 - " . var_export($result, true) . "</br>";

$phone2 = "89991234567";
$result = validate_phone($phone2);
echo "$phone2 - " . var_export($result, true) . "</br>";

$phone3 = "abc";
$result = validate_phone($phone3);
echo "$phone3 - " . var_export($result, true) . "</br>";



echo "</br>";
echo "---------------formatters.php-----------------" . "</br>";

$price1 = 1234.5;
$result = format_price($price1);
echo "$price1 = " . $result . "</br>";

$price2 = 999.99;
$result = format_price($price2, 'USD');
echo "$price2 = " . $result . "</br>";

$price3 = 0.1;
$result = format_price($price3, 'EUR');
echo "$price3 = " . $result . "</br>";



echo "</br>";
echo "---------------calculators.php-----------------" . "</br>";

$amount1 = 1000;
$payment_type1 = 'card';
$result1 = calculate_commission($amount1, $payment_type1);
echo "$amount1, $payment_type1 -> " . $result1 . "</br>";

$amount2 = 1000;
$payment_type2 = 'wallet';
$result2 = calculate_commission($amount2, $payment_type2);
echo "$amount2, $payment_type2 -> " . $result2 . "</br>";

$amount3 = 1000;
$payment_type3 = 'unknown';
$result3 = calculate_commission($amount3, $payment_type3);
echo "$amount3, $payment_type3 -> " . $result3 . "</br>";
echo "</br>";



$code1 = 2;
$result1 = get_payment_status($code1);
echo "$code1 -> " . $result1 . "</br>";

$code2 = 999;
$result2 = get_payment_status($code2);
echo "$code2 -> " . $result2 . "</br>";



echo "</br>";
echo "---------------processors.php-----------------" . "</br>";

$payments = [
    ['id' => 1, 'amount' => 100],
    ['id' => 2, 'amount' => 500],
    ['id' => 3, 'amount' => 1000],
];

$result = filter_payments_by_amount($payments, 200, 800);
echo print_r($result, true) . "</br>";
echo "</br>";


$payments = [
    ['amount' => 100],
    ['amount' => 200],
    ['amount' => 300],
];

$result = sum_payments($payments);
echo $result . "</br>";