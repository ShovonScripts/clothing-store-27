<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
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

        return view('storefront.product.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function search(Request $request): View
    {
        $query = $request->get('q', '');

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('name_bn', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['category', 'media', 'variants'])
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('storefront.search', [
            'query' => $query,
            'products' => $products,
        ]);
    }
}
