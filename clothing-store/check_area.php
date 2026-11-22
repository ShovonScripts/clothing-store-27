<?php

use App\Models\Area;
use App\Models\District;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$district = District::where('is_active', true)->where('zone', 'inside_dhaka')->first();
echo 'District: '.($district ? $district->name : 'null').PHP_EOL;

$area = Area::where('is_active', true)->where('district_id', $district->id)->first();
echo 'Area: '.($area ? $area->name : 'null').PHP_EOL;
