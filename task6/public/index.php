<?php
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require ROUTER_PATH;
require AUTH_PATH;
require FUNCTIONS_PATH;
require DATABASE_PATH;

session_start();

$page = getCurrentPage();
renderPage($page);