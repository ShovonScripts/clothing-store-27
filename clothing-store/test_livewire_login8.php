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

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
);

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
);

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
];

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
];

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
];

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
];

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
];

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->get('/admin/login');
$html = (string)$response->getBody();

if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
    $csrf = $matches[1];
    echo "CSRF: $csrf\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $componentId = $matches[1];
    echo "Component ID: $componentId\n";
}

$checksum = '';
if (preg_match('/wire:snapshot="\{([^&]+)"/', $html, $matches)) {
    $checksum = $matches[1];
    echo "Checksum: $checksum\n";
}

if (preg_match('/wire:id="([^"]+)"/', $html, $matches)) {
    $fingerprintId = $matches[1];
    echo "Fingerprint ID: $fingerprintId\n";
}

// Extract Livewire script URL to get the hash
$livewireHash = '';
if (preg_match('/src="(http:\/\/127\.0\.0\.1:8000\/livewire-[a-f0-9]+\/livewire\.js)"/', $html, $matches)) {
    $livewireJsUrl = $matches[1];
    echo "Livewire JS URL: $livewireJsUrl\n";
    
    if (preg_match('/livewire-([a-f0-9]+)\/livewire\.js/', $livewireJsUrl, $matches)) {
        $livewireHash = $matches[1];
        echo "Livewire hash: $livewireHash\n";
    }
} else {
    echo "Livewire JS URL not found in HTML\n";
    // Debug: search for livewire in HTML
    if (preg_match('/livewire-[a-f0-9]+\/livewire\.js/', $html, $matches)) {
        echo "Found livewire in HTML: " . $matches[0] . "\n";
    }
}

$payload = [
    'fingerprint' => [
        'id' => $componentId,
        'name' => 'Filament\Auth\Pages\Login',
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
            'id' => $componentId,
            'name' => 'Filament\Auth\Pages\Login',
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
        'checksum' => $checksum,
    ],
    'updates' => [
        ['type' => 'syncInput', 'payload' => ['name' => 'email', 'value' => 'jishovon.job@gmail.com']],
        ['type' => 'syncInput', 'payload' => ['name' => 'password', 'value' => '12345678']],
        ['type' => 'syncInput', 'payload' => ['name' => 'remember', 'value' => 'false']],
        ['type' => 'callMethod', 'payload' => ['method' => 'authenticate']],
    ],
];

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://127.0.0.1:8000',
    'cookies' => true,
    'http_errors' => false,
]);

$response = $client->post('/livewire-' . $livewireHash . '/update', [
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
echo "Response: " . substr($body, 0, 2000) . PHP_EOL;