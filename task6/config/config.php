<?php
declare(strict_types=1);

const APP_NAME = 'PaySystem';
const APP_VERSION = '1.0.0';
const BASE_URL = 'http://localhost:63342/paysystem-v1/task6/';


define('BASE_PATH', realpath(dirname(__DIR__)));
const CONFIG_PATH = BASE_PATH . '/config';
const CORE_PATH = BASE_PATH . '/core';
const PAGES_PATH = BASE_PATH . '/pages';
const DATA_PATH = BASE_PATH . '/data';
const LOGS_PATH = BASE_PATH . '/logs';
const UPLOADS_PATH = BASE_PATH . '/uploads';


const ROUTER_PATH = CONFIG_PATH . '/router.php';
const DATABASE_PATH = CONFIG_PATH . '/database.php';


const AUTH_PATH = CORE_PATH . '/auth.php';
const FUNCTIONS_PATH = CORE_PATH . '/functions.php';




const USERS_FILE = DATA_PATH . '/users.json';
const PAYMENTS_FILE = DATA_PATH . '/payments.json';


const OPERATIONS_LOG = LOGS_PATH . '/operations.log';
const ERRORS_LOG = LOGS_PATH . '/errors.log';


const SESSION_TIMEOUT = 24 * 60 * 60; // 24 часа


const COMMISSION_RATES = [
    'card' => 2.5,
    'wallet' => 0.5,
    'bank_transfer' => 1.0,
    'other' => 3.0,
];