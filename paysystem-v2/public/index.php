<?php
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require ROUTER_PATH;

require AUTH_PATH;
require FUNCTIONS_PATH;
require SECURITY_PATH;
require LOGGER_PATH;

require DATABASE_PATH;

// Entity
require TIMESTAMPABLE_PATH;
require LOGGABLE_PATH;
require HASUUID_PATH;


// Entity
require PAYMENT_PATH;
require USER_PATH;
require TRANSACTION_PATH;
require CURRENCY_TYPE_PATH;
require PAYMENT_STATUS_PATH;
require PAYMENT_TYPE_PATH;
require TRANSACTION_TYPE_PATH;

// VALIDATOR
require USER_VALIDATOR_PATH;

// Interface
require PAYMENT_PROCESSOR_INTERFACE_PATH;
require STORAGE_INTERFACE_PATH;
require VALIDATOR_INTERFACE_PATH;

// Processor
require ABSTRACT_PAYMENT_PROCESSOR;
require STRIPE_PATH;
require MOLLIE_PATH;
require FLUTTERWAVE_PATH;

// Service
require PAYMENT_SERVICE_PATH;

session_start();

$page = getCurrentPage();
renderPage($page);