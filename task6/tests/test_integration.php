<?php

require_once __DIR__ . '/../config/config.php';
require_once AUTH_PATH;
require_once VALIDATORS_PATH;
require_once LOGGER_PATH;



echo '<pre>';
// ------------------register----------------------
$validData = [
    'email' => 'test@gmail.com',
    'password' => 'pass1234',
    'passwordConfirm' => 'pass1234',
    'fullName' => 'Test User',
    'phone' => '+123456789012'
];
$result = validateRegistrationForm($validData);
echo var_export($result, true) . "</br>";
log_operation('TEST', 'Valid registration tested step 1');
echo "</br>";
$user = [
    'email' => $validData['email'],
    'password' => hashPassword($validData['password']),
    'fullName' => $validData['fullName'],
    'phone' => $validData['phone'],
];
$user['id'] = !empty($listId) ? max($listId) + 1 : 1;
echo var_export($user, true) . "</br>";
log_operation('TEST', 'Valid registration tested step 2');
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
log_operation('TEST', 'Registration validation failed');



// ------------------create_payment----------------------
$payments = [];

for ($i = 1; $i <= 3; $i++) {
    $payments[] = [
        'id' => $i,
        'userId' => $user['id'],
        'date' => date('Y-m-d H:i:s'),
        'amount' => rand(100, 1000),
        'type' => 'card',
        'status' => 'pending',
        'description' => "Test payment #$i"
    ];
}

echo var_export($payments, true) . "</br>";
log_operation('TEST', 'Valid create payment tested step 1');


$keys = array_keys(PAYMENTS_TYPES);
$status = 'pending';
$type = $keys[0];
$minAmount = 300;
$maxAmount = 700;

$filtered = array_filter($payments, function ($payment) use ($user, $status, $type, $minAmount, $maxAmount)
{
    if (($payment['userId'] ?? '') !== $user['id'])
    {
        return false;
    }

    if ($status && ($payment['status'] ?? '') !== $status)
    {
        return false;
    }

    if ($type && ($payment['type'] ?? '') !== $type)
    {
        return false;
    }

    if ($minAmount !== '' && $payment['amount'] < (float)$minAmount)
    {
        return false;
    }

    if ($maxAmount !== '' && $payment['amount'] > (float)$maxAmount)
    {
        return false;
    }

    return true;
});

$data = [
    'filtered' => $filtered,
    'filters' => [
        'status' => $status,
        'type' => $keys[0],
        'min_amount' => $minAmount,
        'max_amount' => $maxAmount
    ]
];

$filtered = $data['filtered'];
$filters = $data['filters'];
$status = $filters['status'];
$type = $filters['type'];
$minAmount = $filters['min_amount'];
$maxAmount = $filters['max_amount'];

echo var_export($filters, true) . "</br>";
echo var_export($status, true) . "</br>";
echo var_export($type, true) . "</br>";
echo var_export($minAmount, true) . "</br>";
echo var_export($maxAmount, true) . "</br>";
echo var_export($filtered, true) . "</br>";
log_operation('TEST', 'Valid create payment tested step 1');


echo "</pre>";