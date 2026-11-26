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
    echo "Password verify: " . (Hash::check('12345678', $u->password) ? 'VALID' : 'INVALID') . PHP_EOL;
    
    // Try manual login
    $result = Auth::guard('web')->attempt([
        'email' => 'jishovon.job@gmail.com',
        'password' => '12345678',
        'remember' => false,
    ]);
    echo "Auth attempt: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    echo "Auth check: " . (auth('web')->check() ? 'YES' : 'NO') . PHP_EOL;
    if (auth('web')->check()) {
        echo "User: " . auth('web')->user()->email . PHP_EOL;
    }
    
    // Check if user is active
    echo "is_active: " . ($u->is_active ? 'true' : 'false') . PHP_EOL;
    echo "email_verified_at: " . ($u->email_verified_at ? $u->email_verified_at : 'null') . PHP_EOL;
}