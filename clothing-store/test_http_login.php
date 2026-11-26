<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

// Test via HTTP client
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
    
    // Submit login
    $response = $client->post('/admin/login', [
        'form_params' => [
            'email' => 'jishovon.job@gmail.com',
            'password' => '12345678',
            'remember' => 'false',
        ],
        'headers' => [
            'X-CSRF-TOKEN' => $csrf,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'allow_redirects' => false,
    ]);
    
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
    $body = (string)$response->getBody();
    if (strpos($body, 'dashboard') !== false || strpos($body, 'Dashboard') !== false || $response->getStatusCode() === 302) {
        echo "LOGIN SUCCESS - Redirected\n";
    } elseif (strpos($body, 'invalid') !== false || strpos($body, 'Sign in') !== false) {
        echo "LOGIN FAILED - Still on login page\n";
    } else {
        echo "Response: " . substr($body, 0, 500) . PHP_EOL;
    }
} else {
    echo "CSRF not found\n";
}