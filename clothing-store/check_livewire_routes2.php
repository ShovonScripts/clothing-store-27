<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$router = app('router');
$routes = $router->getRoutes();

foreach ($routes as $route) {
    if (strpos($route->uri(), 'livewire') !== false) {
        echo "Route: " . $route->uri() . PHP_EOL;
        echo "  Action: " . $route->getActionName() . PHP_EOL;
        echo "  Middleware: " . implode(', ', $route->gatherMiddleware()) . PHP_EOL;
        echo PHP_EOL;
    }
}