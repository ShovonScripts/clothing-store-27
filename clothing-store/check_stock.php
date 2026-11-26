<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$variants = App\Models\ProductVariant::all();
foreach ($variants as $v) {
    echo $v->id . ' ' . $v->stock . PHP_EOL;
}