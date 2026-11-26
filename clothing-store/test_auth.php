<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Test 1 (auth()): " . (auth()->validate(['email' => 'jishovon.job@gmail.com', 'password' => '12345678']) ? 'PASS' : 'FAIL') . PHP_EOL;
echo "Test 2 (auth web guard): " . (auth('web')->validate(['email' => 'jishovon.job@gmail.com', 'password' => '12345678']) ? 'PASS' : 'FAIL') . PHP_EOL;

$u = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($u) {
    echo "User: " . $u->email . " | Roles: " . $u->getRoleNames()->implode(', ') . PHP_EOL;
    echo "Guard: " . $u->getGuard() . PHP_EOL;
}