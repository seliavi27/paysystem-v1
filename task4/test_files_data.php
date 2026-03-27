<?php
declare(strict_types=1);

require_once "json_storage.php";
require_once "csv_handler.php";
require_once "logger.php";
require_once "directory_manager.php";

$payments = [
    [
        'id' => 1,
        'date' => '2024-01-15',
        'amount' => 100.50,
        'description' => 'Покупка',
        'type' => 'card'
    ],
    [
        'id' => 2,
        'date' => '2024-01-16',
        'amount' => 500.00,
        'description' => 'Пополнение',
        'type' => 'wallet'
    ],
    [
        'id' => 3,
        'date' => '2024-01-17',
        'amount' => 1000.00,
        'description' => 'Перевод',
        'type' => 'bank_transfer'
    ],
    [
        'id' => 4,
        'date' => '2024-01-18',
        'amount' => 250.75,
        'description' => 'Оплата интернета',
        'type' => 'card'
    ],
    [
        'id' => 5,
        'date' => '2024-01-19',
        'amount' => 1500.00,
        'description' => 'Зарплата',
        'type' => 'bank_transfer'
    ]
];

echo "<pre>";
echo "---------------json_storage.php-----------------" . "</br>";

//$payments = [
//    ['id' => 1, 'amount' => 100, 'status' => 'completed'],
//    ['id' => 2, 'amount' => 200, 'status' => 'pending'],
//];
//$result = save_payments_to_json($payments, 'payments.json');
//echo var_export($result, true) . "</br>";
//
//$result = load_payments_from_json('payments.json');
//echo var_export($result, true) . "</br>";
//
//$payment = [
//    'amount' => 300,
//    'status' => 'pending',
//    'date' => date('Y-m-d')];
//$result = add_payment_to_storage($payment, 'payments.json');
//echo var_export($result, true) . "</br>";
//
//$result = update_payment_status(1, 'completed', 'payments.json');
//echo var_export($result, true) . "</br>";



echo "</br>";
echo "---------------csv_handler.php-----------------" . "</br>";

//$result = export_payments_to_csv($payments, 'export.csv');
//echo var_export($result, true) . "</br>";
//
//$result = import_transactions_from_csv('export.csv');
//echo var_export($result, true) . "</br>";
//
//$result = validate_csv_structure('export.csv', ['id', 'date', 'amount']);
//echo var_export($result, true) . "</br>";



echo "</br>";
echo "---------------logger.php-----------------" . "</br>";

//$result = log_operation('PAYMENT_CREATE', 'Payment #1 created for user 101');
//echo var_export($result, true) . "</br>";
//$result = log_operation('PAYMENT_UPDATE', 'Payment #1 status changed to completed');
//echo var_export($result, true) . "</br>";
//
//$result = log_error("File not found: transactions.csv");
//echo var_export($result, true) . "</br>";
//
//$result = get_logs('logs/operations.log', 1);
//echo var_export($result, true) . "</br>";



echo "</br>";
echo "---------------directory_manager.php-----------------" . "</br>";

$result = get_logs('logs/operations.log', 1);
echo var_export($result, true) . "</br>";
















echo "</pre>";