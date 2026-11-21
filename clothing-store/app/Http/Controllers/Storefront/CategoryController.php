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
        $rootCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('activeChildren')
            ->orderBy('sort_order')
            ->get();

        return view('storefront.category.index', compact('rootCategories'));
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
