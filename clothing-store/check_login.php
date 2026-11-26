<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = App\Models\User::where('email', 'jishovon.job@gmail.com')->first();
if ($u) {
    echo "Exists: yes\n";
    echo "Hash: " . $u->password . PHP_EOL;
    echo "Check: " . (password_verify('12345678', $u->password) ? 'valid' : 'INVALID') . PHP_EOL;
} else {
    echo "User not found\n";
}