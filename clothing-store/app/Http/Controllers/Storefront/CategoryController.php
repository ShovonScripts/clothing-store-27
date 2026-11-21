<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('storefront.category.index');
    }

    public function categories(): View
    {
        return view('storefront.category.categories');
    }
}
