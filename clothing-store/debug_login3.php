<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

Session::start();

$credentials = ['email' => 'jishovon.job@gmail.com', 'password' => '12345678', 'remember' => false];

echo "Before attempt - Session ID: " . Session::getId() . PHP_EOL;

// Try to manually authenticate
$user = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($user) {
    echo "User found: " . $user->email . PHP_EOL;
    echo "User ID: " . $user->id . PHP_EOL;
    echo "Password hash: " . $user->password . PHP_EOL;
    echo "Hash check: " . (Hash::check('12345678', $user->password) ? 'VALID' : 'INVALID') . PHP_EOL;
    echo "is_active: " . ($user->is_active ? 'true' : 'false') . PHP_EOL;
    
    // Try manual login using the provider directly
    $provider = Auth::guard('web')->getProvider();
    $userFromProvider = $provider->retrieveByCredentials(['email' => 'jishovon.job@gmail.com', 'password' => '12345678']);
    if ($userFromProvider) {
        echo "Provider retrieved user: " . $userFromProvider->email . PHP_EOL;
        $valid = $provider->validateCredentials($userFromProvider, ['password' => '12345678']);
        echo "Validate credentials: " . ($valid ? 'VALID' : 'INVALID') . PHP_EOL;
        
        if ($valid) {
            echo "Attempting manual login via Auth::guard()->login()..." . PHP_EOL;
            Auth::guard('web')->login($userFromProvider, false);
            echo "After login - Auth check: " . (Auth::guard('web')->check() ? 'YES' : 'NO') . PHP_EOL;
            echo "Session ID: " . Session::getId() . PHP_EOL;
        }
    }
}