<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($u) {
    $u->assignRole('owner');
    echo "Assigned owner role to: " . $u->email . PHP_EOL;
    echo "Roles: " . $u->getRoleNames()->implode(', ') . PHP_EOL;
} else {
    echo "User not found" . PHP_EOL;
}