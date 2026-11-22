<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categorySlug = request()->query('category');

        return view('storefront.shop', compact('categorySlug'));
    }

    public function categories(): View
    {
        $categories = Category::where('is_active', true)
            ->with('activeChildren')
            ->orderBy('sort_order')
            ->get();

        return view('storefront.category.categories', compact('categories'));
    }
}
