<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

$u = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($u) {
    // Simulate a real HTTP request with session
    $request = Illuminate\Http\Request::create('/admin/login', 'POST', [
        'email' => 'jishovon.job@gmail.com',
        'password' => '12345678',
        'remember' => false,
    ]);
    
    // Start session manually for testing
    Session::start();
    
    $result = auth('web')->attempt([
        'email' => 'jishovon.job@gmail.com',
        'password' => '12345678',
        'remember' => false,
    ], false);
    
    echo "Auth attempt: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    echo "Auth check: " . (Auth::guard('web')->check() ? 'YES' : 'NO') . PHP_EOL;
    echo "Session started: " . (Session::isStarted() ? 'YES' : 'NO') . PHP_EOL;
    echo "Session ID: " . Session::getId() . PHP_EOL;
    if (auth('web')->check()) {
        echo "User: " . auth('web')->user()->email . PHP_EOL;
    }
}