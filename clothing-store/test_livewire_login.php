<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

// Get login page to get CSRF token
$response = $client->get('/admin/login');
$html = (string)$response->getBody();

// Extract CSRF token from meta tag
if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
    
    // Submit login via Livewire update endpoint
    $payload = [
        'fingerprint' => [
            'id' => 'TyefG9m6dDEOizbMoiEq',
            'name' => 'storefront.checkout',
            'locale' => 'en',
            'path' => 'admin/login',
            'method' => 'GET',
            'v' => 'a-a-a',
        ],
        'serverMemo' => [
            'data' => [
                'email' => 'jishovon.job@gmail.com',
                'password' => '12345678',
                'remember' => false,
            ],
            'memo' => [
                'id' => 'TyefG9m6dDEOizbMoiEq',
                'name' => 'Filament\\Auth\\Pages\\Login',
                'path' => 'admin/login',
                'method' => 'GET',
                'origin' => 'http://127.0.0.1:8000',
                'route' => ['uri' => 'admin/login', 'domain' => null],
                'release' => 'a-a-a',
                'children' => [],
                'scripts' => [],
                'assets' => [],
                'errors' => [],
                'locale' => 'en',
                'islands' => [],
            ],
            'checksum' => 'cc2183feec8968611505dac1ea9094c16c82eb099dff6a78a6b4e5666cacd1ef',
        ],
        'updates' => [
            ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
            ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
            ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
            ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
        ],
    ];
    
    $response = $client->post('/livewire-d3b1056d/update', [
        'json' => $payload,
        'headers' => [
            'X-CSRF-TOKEN' => $csrf,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Livewire' => 'true',
        ],
    ]);
    
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
    $body = (string)$response->getBody();
    echo "Response: " . substr($body, 0, 1000) . PHP_EOL;
} else {
    echo "CSRF not found\n";
}