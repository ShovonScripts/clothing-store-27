<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Get CSRF token from login page
$client = new \GuzzleHttp\Client(['base_uri' => 'http://127.0.0.1:8000', 'cookies' => true]);
$response = $client->get('/admin/login');
$html = (string)$response->getBody();

// Extract CSRF token
if (preg_match('/name="_token" value="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
    
    // Submit login
    $client = new \GuzzleHttp\Client(['base_uri' => 'http://127.0.0.1:8000', 'cookies' => true]);
    $response = $client->post('/admin/login', [
        'form_params' => [
            'email' => 'jishovon.job@gmail.com',
            'password' => '12345678',
            'remember' => 'false',
            '_token' => $csrf,
        ],
        'headers' => [
            'X-CSRF-TOKEN' => $csrf,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ],
    ]);
    
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
    $body = (string)$response->getBody();
    if (strpos($body, 'dashboard') !== false || strpos($body, 'Dashboard') !== false) {
        echo "LOGIN SUCCESS - Redirected to dashboard\n";
    } elseif (strpos($body, 'invalid') !== false || strpos($body, 'Sign in') !== false) {
        echo "LOGIN FAILED - Still on login page\n";
    } else {
        echo "Response: " . substr($body, 0, 500) . PHP_EOL;
    }
} else {
    echo "CSRF not found\n";
}