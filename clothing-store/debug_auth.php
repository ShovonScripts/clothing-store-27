<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

$u = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($u) {
    echo "User: " . $u->email . PHP_EOL;
    echo "ID: " . $u->id . PHP_EOL;
    echo "Password hash: " . $u->password . PHP_EOL;
    echo "Hash check: " . (Hash::check('12345678', $u->password) ? 'VALID' : 'INVALID') . PHP_EOL;
    echo "getAuthPassword: " . $u->getAuthPassword() . PHP_EOL;
    echo "getAuthIdentifier: " . $u->getAuthIdentifier() . PHP_EOL;
    echo "is_active: " . ($u->is_active ? 'true' : 'false') . PHP_EOL;
    
    // Check the user provider
    $provider = Auth::guard('web')->getProvider();
    echo "Provider class: " . get_class($provider) . PHP_EOL;
    
    // Try retrieving by credentials
    $credentials = ['email' => 'jishovon.job@gmail.com', 'password' => '12345678'];
    $user = $provider->retrieveByCredentials($credentials);
    echo "Retrieved by credentials: " . ($user ? 'YES - ' . $user->email : 'NO') . PHP_EOL;
    
    if ($user) {
        echo "Validate credentials: " . ($provider->validateCredentials($user, ['password' => '12345678']) ? 'VALID' : 'INVALID') . PHP_EOL;
    }
}