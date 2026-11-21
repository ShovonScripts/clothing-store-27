<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        return view('storefront.product.show', [
            'product' => null, // Will be replaced with actual product
        ]);
    }

    public function search(Request $request): View
    {
        return view('storefront.search', [
            'query' => $request->get('q', ''),
        ]);
    }
}
