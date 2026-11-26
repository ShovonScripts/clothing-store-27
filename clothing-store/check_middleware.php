<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$middleware = $kernel->getMiddleware();
echo "Middleware count: " . count($middleware) . PHP_EOL;
foreach ($middleware as $m) {
    echo "  - " . $m . PHP_EOL;
}