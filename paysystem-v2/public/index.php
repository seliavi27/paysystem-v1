<?php
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require ROUTER_PATH;

require AUTH_PATH;
require FUNCTIONS_PATH;
require SECURITY_PATH;
require LOGGER_PATH;

require DATABASE_PATH;

//require MODELS_PATH;
require PAYMENT_PATH;
require USER_PATH;
require TRANSACTION_PATH;
require CURRENCY_TYPE_PATH;
require PAYMENT_STATUS_PATH;
require PAYMENT_TYPE_PATH;
require TRANSACTION_TYPE_PATH;

// VALIDATOR
//require VALIDATOR_PATH;
require USER_VALIDATOR_PATH;

session_start();

$page = getCurrentPage();
renderPage($page);