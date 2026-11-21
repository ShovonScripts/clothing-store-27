<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
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

        return view('storefront.home', compact('banners', 'categories', 'featuredProducts', 'newProducts'));
    }
}
