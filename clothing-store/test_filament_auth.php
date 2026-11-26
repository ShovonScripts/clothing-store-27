<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

Session::start();

echo "Filament auth guard: " . get_class(Filament::auth()) . PHP_EOL;
echo "Filament auth guard name: " . Filament::auth()->getName() . PHP_EOL;

$authGuard = Filament::auth();
$authProvider = $authGuard->getProvider();

$credentials = ['email' => 'jishovon.job@gmail.com', 'password' => '12345678'];

echo "Testing Filament auth...\n";

$user = app(\Illuminate\Support\Timebox::class)->call(function (\Illuminate\Support\Timebox $timebox) use ($authProvider, $credentials) {
    $user = $authProvider->retrieveByCredentials($credentials);
    if ((! $user) || (! $authProvider->validateCredentials($user, $credentials))) {
        return null;
    }
    return $user;
}, 200000);

echo "User from Filament auth provider: " . ($user ? $user->email : 'NULL') . PHP_EOL;

if ($user) {
    $userScopedAuthIdentifier = \Filament\Facades\Filament::getUserScopedAuthIdentifier($user);
    echo "User scoped auth identifier: " . $userScopedAuthIdentifier . PHP_EOL;
    
    // Check if user can access panel
    if ($user->canAccessPanel(app(\Filament\Panel::class))) {
        echo "User allowed to access panel: YES\n";
    } else {
        echo "User allowed to access panel: NO\n";
    }
}