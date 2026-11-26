<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$middleware = $kernel->getRouteMiddleware();
echo "Route Middleware count: " . count($middleware) . PHP_EOL;
foreach ($middleware as $name => $class) {
    echo "  - $name: $class" . PHP_EOL;
}

echo PHP_EOL . "Middleware groups:" . PHP_EOL;
$middlewareGroups = $kernel->getMiddlewareGroups();
foreach ($middlewareGroups as $name => $middlewares) {
    echo "  $name:" . PHP_EOL;
    foreach ($middlewares as $m) {
        echo "    - $m" . PHP_EOL;
    }
}