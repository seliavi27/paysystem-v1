<?php
declare(strict_types=1);

const APP_NAME = 'PaySystem';
const APP_VERSION = '1.0.0';
const BASE_URL = 'http://localhost:8000/';


define('BASE_PATH', realpath(dirname(__DIR__)));

const PAGES_PATH = BASE_PATH . '/pages';

const DATA_PATH = BASE_PATH . '/data';
const LOGS_PATH = BASE_PATH . '/logs';

const UPLOADS_PATH = BASE_PATH . '/uploads';
const AVATARS_PATH = UPLOADS_PATH . '/avatars';
const UPLOADS_URL = 'uploads';
const AVATARS_URL = UPLOADS_URL . '/avatars';


const CONFIG_PATH = BASE_PATH . '/config';
const CORE_PATH = BASE_PATH . '/core';


const DATABASE_PATH = CONFIG_PATH . '/database.php';


const FUNCTIONS_PATH = CORE_PATH . '/functions.php';
const ROUTER_PATH = CORE_PATH . '/router.php';
const AUTH_PATH = CORE_PATH . '/auth.php';
const SECURITY_PATH = CORE_PATH . '/security.php';
const LOGGER_PATH = CORE_PATH . '/logger.php';
const VALIDATORS_PATH = CORE_PATH . '/validators.php';


const USERS_FILE = DATA_PATH . '/users.json';
const PAYMENTS_FILE = DATA_PATH . '/payments.json';


const OPERATIONS_LOG = LOGS_PATH . '/operations.log';
const ERRORS_LOG = LOGS_PATH . '/errors.log';

const PUBLIC_PATH = BASE_PATH . '/public';
const ASSETS_PATH = PUBLIC_PATH . '/assets';
const LIGHT_CSS_PATH = ASSETS_PATH . '/light.css';
const DARK_CSS_PATH = ASSETS_PATH . '/dark.css';


const SESSION_TIMEOUT = 24 * 60 * 60; // 24 часа

const PAYMENTS_TYPES = [
    'card' => ['Card', 2.5],
    'wallet' => ['Wallet', 0.5],
    'bank_transfer' => ['Bank transfer', 1.0]
];