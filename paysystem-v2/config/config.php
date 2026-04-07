<?php
declare(strict_types=1);

const APP_NAME = 'PaySystem';
const APP_VERSION = '1.0.0';
const BASE_URL = 'http://localhost:8000/';


define('BASE_PATH', realpath(dirname(__DIR__)));

const SRC_PATH = BASE_PATH . '/src';
const PUBLIC_PATH = BASE_PATH . '/public';
const PAGES_PATH = BASE_PATH . '/pages';

const DATA_PATH = BASE_PATH . '/data';
const LOGS_PATH = BASE_PATH . '/logs';

const UPLOADS_PATH = PUBLIC_PATH . '/uploads';
const AVATARS_PATH = UPLOADS_PATH . '/avatars';
const UPLOADS_URL = '/uploads';
const AVATARS_URL = UPLOADS_URL . '/avatars';

const ASSETS_PATH = PUBLIC_PATH . '/assets';
const LIGHT_CSS_PATH = ASSETS_PATH . '/light.css';
const DARK_CSS_PATH = ASSETS_PATH . '/dark.css';

const CONFIG_PATH = BASE_PATH . '/config';
const CORE_PATH = BASE_PATH . '/core';

const DATABASE_PATH = CONFIG_PATH . '/database.php';

const FUNCTIONS_PATH = CORE_PATH . '/functions.php';
const ROUTER_PATH = CORE_PATH . '/router.php';
const AUTH_PATH = CORE_PATH . '/auth.php';
const SECURITY_PATH = CORE_PATH . '/security.php';
const LOGGER_PATH = CORE_PATH . '/logger.php';
//const VALIDATORS_PATH = CORE_PATH . '/validators.php';

const USERS_FILE = DATA_PATH . '/users.json';
const PAYMENTS_FILE = DATA_PATH . '/payments.json';

const OPERATIONS_LOG = LOGS_PATH . '/operations.log';
const ERRORS_LOG = LOGS_PATH . '/errors.log';

// ENTITY
const ENTITY_PATH = SRC_PATH . '/Entity';
const PAYMENT_PATH = ENTITY_PATH . '/Payment.php';
const USER_PATH = ENTITY_PATH . '/User.php';
const TRANSACTION_PATH = ENTITY_PATH . '/Transaction.php';

// ENUM
const ENUM_PATH = SRC_PATH . '/Enum';
const CURRENCY_TYPE_PATH = ENUM_PATH . '/CurrencyType.php';
const PAYMENT_STATUS_PATH = ENUM_PATH . '/PaymentStatus.php';
const PAYMENT_METHOD_PATH = ENUM_PATH . '/PaymentMethod.php';
const TRANSACTION_TYPE_PATH = ENUM_PATH . '/TransactionType.php';

// Validator
const VALIDATOR_PATH = SRC_PATH . '/Validator';
const USER_VALIDATOR_PATH = VALIDATOR_PATH . '/UserValidator.php';

// Processor
const PROCESSOR_PATH = SRC_PATH . '/Processor';
const ABSTRACT_PAYMENT_PROCESSOR = PROCESSOR_PATH . '/AbstractPaymentProcessor.php';
const STRIPE_PATH = PROCESSOR_PATH . '/StripeProcessor.php';
const MOLLIE_PATH = PROCESSOR_PATH . '/MollieProcessor.php';
const FLUTTERWAVE_PATH = PROCESSOR_PATH . '/FlutterwaveProcessor.php';

// Service
const SERVICE_PATH = SRC_PATH . '/Service';
const PAYMENT_SERVICE_PATH = SERVICE_PATH . '/PaymentService.php';

// Interface
const INTERFACE_PATH = SRC_PATH . '/Interface';
const PAYMENT_PROCESSOR_INTERFACE_PATH = INTERFACE_PATH . '/PaymentProcessorInterface.php';
const STORAGE_INTERFACE_PATH = INTERFACE_PATH . '/StorageInterface.php';
const VALIDATOR_INTERFACE_PATH = INTERFACE_PATH . '/ValidatorInterface.php';

// Trait
const TRAIT_PATH = SRC_PATH . '/Trait';
const TIMESTAMPABLE_PATH = TRAIT_PATH . '/Timestampable.php';
const LOGGABLE_PATH = TRAIT_PATH . '/Loggable.php';
const HASUUID_PATH = TRAIT_PATH . '/HasUuid.php';

// DTO
const DTO_PATH = SRC_PATH . '/DTO';
const CREATE_PAYMENT_REQUEST_PATH = DTO_PATH . '/CreatePaymentRequest.php';
const PAYMENT_RESPONSE_PATH = DTO_PATH . '/PaymentResponse.php';
const TRANSACTION_REQUEST_PATH = DTO_PATH . '/TransactionRequest.php';
const REFUND_REQUEST_PATH = DTO_PATH . '/RefundRequest.php';



const SESSION_TIMEOUT = 24 * 60 * 60; // 24 часа