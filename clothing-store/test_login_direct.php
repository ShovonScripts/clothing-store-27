<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

// Test direct authentication
echo "Testing direct auth...\n";
$result = auth('web')->attempt([
    'email' => 'jishovon.job@gmail.com',
    'password' => '12345678',
    'remember' => false,
]);

echo "Auth attempt: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
echo "User authenticated: " . (auth('web')->check() ? 'YES' : 'NO') . PHP_EOL;
if (auth('web')->check()) {
    echo "User: " . auth('web')->user()->email . PHP_EOL;
    echo "Roles: " . auth('web')->user()->getRoleNames()->implode(', ') . PHP_EOL;
}