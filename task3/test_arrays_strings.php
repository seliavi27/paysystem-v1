<?php
declare(strict_types=1);

require_once 'payments_processor.php';
require_once 'string_processor.php';
require_once 'validators_advanced.php';
require_once 'data_processor.php';

$payments = [
    [
        'id' => 1,
        'user_id' => 101,
        'amount' => 1500.50,
        'currency' => 'RUB',
        'type' => 'card',
        'status' => 'completed',
        'date' => '2024-01-15',
        'description' => 'Оплата заказа #12345'
    ],
    [
        'id' => 2,
        'user_id' => 102,
        'amount' => 500.00,
        'currency' => 'RUB',
        'type' => 'wallet',
        'status' => 'pending',
        'date' => '2024-01-16',
        'description' => 'Пополнение счета'
    ],
    [
        'id' => 3,
        'user_id' => 101,
        'amount' => 2300.00,
        'currency' => 'RUB',
        'type' => 'bank_transfer',
        'status' => 'completed',
        'date' => '2024-01-17',
        'description' => 'Оплата заказа #12346'
    ],
    [
        'id' => 4,
        'user_id' => 103,
        'amount' => 89.99,
        'currency' => 'USD',
        'type' => 'card',
        'status' => 'completed',
        'date' => '2024-01-17',
        'description' => 'Оплата заказа #12347'
    ],
    [
        'id' => 5,
        'user_id' => 104,
        'amount' => 1250.00,
        'currency' => 'EUR',
        'type' => 'card',
        'status' => 'failed',
        'date' => '2024-01-18',
        'description' => 'Оплата заказа #12348'
    ],
    [
        'id' => 6,
        'user_id' => 102,
        'amount' => 300.00,
        'currency' => 'RUB',
        'type' => 'wallet',
        'status' => 'completed',
        'date' => '2024-01-18',
        'description' => 'Оплата заказа #12349'
    ],
    [
        'id' => 7,
        'user_id' => 105,
        'amount' => 75.50,
        'currency' => 'USD',
        'type' => 'card',
        'status' => 'pending',
        'date' => '2024-01-19',
        'description' => 'Подписка на сервис'
    ],
    [
        'id' => 8,
        'user_id' => 101,
        'amount' => 450.00,
        'currency' => 'RUB',
        'type' => 'bank_transfer',
        'status' => 'completed',
        'date' => '2024-01-20',
        'description' => 'Пополнение счета'
    ],
    [
        'id' => 9,
        'user_id' => 106,
        'amount' => 3200.00,
        'currency' => 'RUB',
        'type' => 'card',
        'status' => 'completed',
        'date' => '2024-01-20',
        'description' => 'Оплата заказа #12350'
    ],
    [
        'id' => 10,
        'user_id' => 103,
        'amount' => 125.75,
        'currency' => 'EUR',
        'type' => 'wallet',
        'status' => 'completed',
        'date' => '2024-01-21',
        'description' => 'Оплата заказа #12351'
    ],
    [
        'id' => 11,
        'user_id' => 107,
        'amount' => 50.00,
        'currency' => 'USD',
        'type' => 'card',
        'status' => 'failed',
        'date' => '2024-01-21',
        'description' => 'Пополнение счета'
    ],
    [
        'id' => 12,
        'user_id' => 102,
        'amount' => 999.99,
        'currency' => 'RUB',
        'type' => 'card',
        'status' => 'pending',
        'date' => '2024-01-22',
        'description' => 'Оплата заказа #12352'
    ],
    [
        'id' => 13,
        'user_id' => 108,
        'amount' => 150.00,
        'currency' => 'RUB',
        'type' => 'wallet',
        'status' => 'completed',
        'date' => '2024-01-22',
        'description' => 'Оплата заказа #12353'
    ],
    [
        'id' => 14,
        'user_id' => 101,
        'amount' => 75.00,
        'currency' => 'USD',
        'type' => 'bank_transfer',
        'status' => 'completed',
        'date' => '2024-01-23',
        'description' => 'Оплата заказа #12354'
    ],
    [
        'id' => 15,
        'user_id' => 109,
        'amount' => 2800.00,
        'currency' => 'EUR',
        'type' => 'card',
        'status' => 'completed',
        'date' => '2024-01-23',
        'description' => 'Оплата заказа #12355'
    ],
    [
        'id' => 16,
        'user_id' => 104,
        'amount' => 42.50,
        'currency' => 'RUB',
        'type' => 'wallet',
        'status' => 'completed',
        'date' => '2024-01-24',
        'description' => 'Оплата заказа #12356'
    ],
    [
        'id' => 17,
        'user_id' => 110,
        'amount' => 200.00,
        'currency' => 'USD',
        'type' => 'card',
        'status' => 'failed',
        'date' => '2024-01-24',
        'description' => 'Пополнение счета'
    ],
    [
        'id' => 18,
        'user_id' => 102,
        'amount' => 1750.00,
        'currency' => 'RUB',
        'type' => 'bank_transfer',
        'status' => 'pending',
        'date' => '2024-01-25',
        'description' => 'Оплата заказа #12357'
    ],
    [
        'id' => 19,
        'user_id' => 111,
        'amount' => 89.99,
        'currency' => 'EUR',
        'type' => 'wallet',
        'status' => 'completed',
        'date' => '2024-01-25',
        'description' => 'Оплата заказа #12358'
    ],
    [
        'id' => 20,
        'user_id' => 105,
        'amount' => 560.00,
        'currency' => 'RUB',
        'type' => 'card',
        'status' => 'completed',
        'date' => '2024-01-26',
        'description' => 'Оплата заказа #12359'
    ]
];

echo '<pre>';
echo "---------------payments_processor.php-----------------" . "</br>";

$result = get_all_amounts($payments);
echo print_r($result, true) . "</br>";

$result = group_payments_by_status($payments);
echo print_r($result, true) . "</br>";

$result = calculate_total_by_type($payments);
echo print_r($result, true) . "</br>";

$result = sort_payments_by_amount($payments, false);
echo print_r($result, true) . "</br>";

$result = get_top_payments($payments, 5);
echo print_r($result, true) . "</br>";

echo "</br>";
echo "---------------string_processor.php-----------------" . "</br>";

$result = format_description("Оплата заказа номер 12345 с доставкой по России", 30);
echo print_r($result, true) . "</br>";
$result = format_description("Пополнение счета", 50);
echo print_r($result, true) . "</br>";

$result = parse_payment_description("Заказ #12345: Пополнение счета на сумму 500 РУБ");
echo print_r($result, true) . "</br>";

$result = slugify_payment_id("Payment #12345");
echo print_r($result, true) . "</br>";
$result = slugify_payment_id("Оплата заказа");
echo print_r($result, true) . "</br>";

$text = "Это описание платежа для тестирования";
$keywords = ['платежа', 'тестирования'];
$result = highlight_keywords($text, $keywords);
echo print_r($result, true) . "</br>";

echo "</br>";
echo "---------------validators_advanced.php-----------------" . "</br>";

$result = validate_credit_card("4111 1111 1111 1111");
echo var_export($result, true) . "</br>";
$result = validate_credit_card("4111111111111111");
echo var_export($result, true) . "</br>";
$result = validate_credit_card("4111111111111112");
echo var_export($result, true) . "</br>";
$result = validate_credit_card("invalid");
echo var_export($result, true) . "</br>";

$result = validate_iban("DE89370400440532013000");
echo var_export($result, true) . "</br>";
$result = validate_iban("RU123456789");
echo var_export($result, true) . "</br>";

$result = extract_urls("Посетите https://paysystem.io и https://google.com для справки");
echo var_export($result, true) . "</br>";

$result = mask_sensitive_data("4111111111111111", "card");
echo var_export($result, true) . "</br>";
$result = mask_sensitive_data("anton.petrov@mail.com", "email");
echo var_export($result, true) . "</br>";

echo "</br>";
echo "---------------data_processor.php-----------------" . "</br>";

$result = filter_and_transform($payments, [
    'status' => 'completed',
    'min_amount' => 100,
    'max_amount' => 5000
]);
echo var_export($result, true) . "</br>";

$result = generate_summary($payments);
echo print_r($result, true) . "</br>";

echo '</pre>';