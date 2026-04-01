<?php
declare(strict_types=1);

require 'validators_form.php';
require 'auth_functional.php';
require 'cookies_handler.php';
require 'logger.php';


echo '<pre>';
echo "---------------validators.php-----------------" . "</br>";

$validData = [
    'email' => 'test@gmail.com',
    'password' => 'pass1234',
    'passwordConfirm' => 'pass1234',
    'fullName' => 'Test User',
    'phone' => '+1234567890'
];
$result = validateRegistrationForm($validData);
echo var_export($result, true) . "</br>";
log_operation('TEST', 'Valid registration tested');
echo "</br>";

$invalidData = [
    'email' => 'test@gmail.com',
    'password' => 'pass1234',
    'passwordConfirm' => 'pass14',
    'fullName' => '',
    'phone' => '+1234567890'
];
$result = validateRegistrationForm($invalidData);
echo var_export($result, true) . "</br>";
log_operation('TEST', 'Valid registration tested');



echo "</br>";
echo "---------------auth_functional.php-----------------" . "</br>";

$user = [
    'id' => 1,
    'email' => 'test@example.com',
    'full_name' => 'Test User'
];

startUserSession($user);
$result = isUserLoggedIn();
echo var_export($result, true) . "</br>";
log_operation('TEST', 'Login session tested');

logout_user();
$result = isUserLoggedIn();
echo var_export($result, true) . "</br>";
log_operation('TEST', 'Logout tested');


echo "</br>";
echo "---------------cookies_handler.php-----------------" . "</br>";

setcookie('test_cookie', 'hello', [
    'expires' => time() + 86400,
    'path' => '/',
    'httponly' => true
]);
$result = getCookie('test_cookie');
echo var_export($result, true) . "</br>";
deleteCookie('test_cookie');

$result = getCookie('test_cookie');
echo var_export($result, true) . "</br>";
log_operation('TEST', 'Cookies tested');


echo '</pre>';