<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\MediaLibrary\MediaCollections\Models\Media;

$media = Media::where('model_type', 'App\Models\Product')
    ->select('id', 'model_id', 'file_name')
    ->orderBy('id')
    ->limit(20)
    ->get()
    ->toArray();

print_r($media);
