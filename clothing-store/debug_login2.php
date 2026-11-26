<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

Session::start();

$credentials = ['email' => 'jishovon.job@gmail.com', 'password' => '12345678', 'remember' => false];

echo "Before attempt - Session ID: " . Session::getId() . PHP_EOL;

$result = Auth::guard('web')->attempt($credentials, false);

echo "Auth attempt result: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
echo "Auth check: " . (Auth::guard('web')->check() ? 'YES' : 'NO') . PHP_EOL;
echo "Session ID after: " . Session::getId() . PHP_EOL;

if (Auth::guard('web')->check()) {
    echo "User: " . Auth::guard('web')->user()->email . PHP_EOL;
} else {
    echo "User not authenticated" . PHP_EOL;
}

// Check session data
echo "Session data: " . json_encode(Session::all()) . PHP_EOL;

// Check if user is in session
$user = Auth::guard('web')->user();
if ($user) {
    echo "User in guard: " . $user->email . PHP_EOL;
} else {
    echo "No user in guard" . PHP_EOL;
}

// Check session data directly
echo "Session 'login_web_' key: " . (Session::has('login_web_' . config('auth.guards.web.provider')) ? 'EXISTS' : 'NOT SET') . PHP_EOL;