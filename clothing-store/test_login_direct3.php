<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

$u = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($u) {
    echo "User: " . $u->email . PHP_EOL;
    echo "Hash: " . $u->password . PHP_EOL;
    echo "Password verify (Hash facade): " . (Hash::check('12345678', $u->password) ? 'VALID' : 'INVALID') . PHP_EOL;
    echo "getAuthPassword: " . $u->getAuthPassword() . PHP_EOL;
    echo "getAuthIdentifierName: " . $u->getAuthIdentifierName() . PHP_EOL;
    echo "getAuthIdentifier: " . $u->getAuthIdentifier() . PHP_EOL;
    echo "getRememberTokenName: " . $u->getRememberTokenName() . PHP_EOL;
    echo "getRememberToken: " . ($u->getRememberToken() ?? 'null') . PHP_EOL;
    
    // Try manual login with explicit guard
    echo "\nTrying Auth::guard('web')->attempt...\n";
    $result = Auth::guard('web')->attempt([
        'email' => 'jishovon.job@gmail.com',
        'password' => '12345678',
        'remember' => false,
    ]);
    echo "Auth attempt: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    echo "Auth check: " . (Auth::guard('web')->check() ? 'YES' : 'NO') . PHP_EOL;
    
    // Check if the session is started
    echo "Session started: " . (session()->isStarted() ? 'YES' : 'NO') . PHP_EOL;
}