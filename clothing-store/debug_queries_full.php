<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Enable query log
DB::enableQueryLog();

// Simulate category listing (shop page)
$categorySlug = '';
$query = Product::query()
    ->where('is_active', true)
    ->with(['category', 'variants.color', 'media'])
    ->when($categorySlug, function ($query) {
        // no category slug
    })
    ->latest('published_at')
    ->paginate(12);

echo "=== CATEGORY LISTING QUERIES ===\n";
$queries = DB::getQueryLog();
echo 'Total queries: '.count($queries)."\n\n";

$grouped = [];
foreach ($queries as $q) {
    $sql = $q['query'];
    if (! isset($grouped[$sql])) {
        $grouped[$sql] = ['count' => 0, 'bindings' => []];
    }
    $grouped[$sql]['count']++;
    $grouped[$sql]['bindings'] = $q['bindings'];
}

foreach ($grouped as $sql => $info) {
    echo "Count: {$info['count']}\n";
    echo "SQL: $sql\n";
    echo 'Bindings: '.json_encode($info['bindings'])."\n\n";
}

DB::flushQueryLog();

// Simulate product detail
$product = Product::where('slug', 'classic-cotton-crew-neck-t-shirt')
    ->where('is_active', true)
    ->with(['category', 'variants.color', 'media', 'sizeChart'])
    ->firstOrFail();

$product->increment('views');

$relatedProducts = Product::where('is_active', true)
    ->where('id', '!=', $product->id)
    ->where(function ($query) use ($product) {
        $query->where('category_id', $product->category_id)
            ->orWhereHas('category', function ($q) use ($product) {
                $q->where('parent_id', $product->category?->parent_id);
            });
    })
    ->with(['media', 'variants'])
    ->inRandomOrder()
    ->limit(4)
    ->get();

echo "=== PRODUCT DETAIL QUERIES ===\n";
$queries = DB::getQueryLog();
echo 'Total queries: '.count($queries)."\n\n";

$grouped = [];
foreach ($queries as $q) {
    $sql = $q['query'];
    if (! isset($grouped[$sql])) {
        $grouped[$sql] = ['count' => 0, 'bindings' => []];
    }
    $grouped[$sql]['count']++;
    $grouped[$sql]['bindings'] = $q['bindings'];
}

foreach ($grouped as $sql => $info) {
    echo "Count: {$info['count']}\n";
    echo "SQL: $sql\n";
    echo 'Bindings: '.json_encode($info['bindings'])."\n\n";
}

DB::flushQueryLog();

// Simulate home page
use App\Models\Banner;

$banners = Banner::where('is_active', true)
    ->where('placement', 'hero')
    ->orderBy('sort_order')
    ->get();

$categories = Category::where('is_active', true)
    ->where('show_on_home', true)
    ->whereNull('parent_id')
    ->with('activeChildren')
    ->orderBy('sort_order')
    ->limit(6)
    ->get();

$featuredProducts = Product::where('is_active', true)
    ->where('is_featured', true)
    ->with(['category', 'media', 'variants'])
    ->latest('published_at')
    ->limit(8)
    ->get();

$newProducts = Product::where('is_active', true)
    ->where('is_new', true)
    ->with(['category', 'media', 'variants'])
    ->latest('published_at')
    ->limit(8)
    ->get();

echo "=== HOME PAGE QUERIES ===\n";
$queries = DB::getQueryLog();
echo 'Total queries: '.count($queries)."\n\n";

$grouped = [];
foreach ($queries as $q) {
    $sql = $q['query'];
    if (! isset($grouped[$sql])) {
        $grouped[$sql] = ['count' => 0, 'bindings' => []];
    }
    $grouped[$sql]['count']++;
    $grouped[$sql]['bindings'] = $q['bindings'];
}

foreach ($grouped as $sql => $info) {
    echo "Count: {$info['count']}\n";
    echo "SQL: $sql\n";
    echo 'Bindings: '.json_encode($info['bindings'])."\n\n";
}
