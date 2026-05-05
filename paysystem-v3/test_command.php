<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Command\CreateUserCommand;

if (class_exists('App\Command\CreateUserCommand')) {
    echo "✅ Class exists\n";
    $reflection = new ReflectionClass('App\Command\CreateUserCommand');
    echo "Command name: " . ($reflection->getStaticPropertyValue('defaultName') ?? 'not set') . "\n";
} else {
    echo "❌ Class NOT found\n";
}