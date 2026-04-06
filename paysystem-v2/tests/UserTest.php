<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once AUTH_PATH;
require_once VALIDATORS_PATH;
require_once LOGGER_PATH;
require_once USER_PATH;

$user = User::create("anton1999@example.com", "anton1999",
    "anton1999", "+375297463952");
echo $user . "</br>";

$user = new User("anton1999@example.com", "anton1999",
    "anton1999", "+375297463952",
    "38f091f3-2f9a-43a6-9c61-037ff57f9dee", new DateTime(), 10);
$result = $user->toArray();
echo var_dump($result) . "</br>";

$user = new User("anton1999@example.com", "anton1999",
    "anton1999", "+375297463952",
    "38f091f3-2f9a-43a6-9c61-037ff57f9dee", new DateTime(), 10);
$result = $user->toArray();
echo var_dump($result) . "</br>";