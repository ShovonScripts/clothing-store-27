<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

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
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
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

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('name_bn', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('media')
            ->limit(5)
            ->get(['id', 'name', 'name_bn', 'slug', 'price', 'sku']);

        return response()->json($products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->getDisplayNameAttribute(),
                'slug' => $product->slug,
                'price' => $product->price,
                'price_formatted' => number_format($product->price / 100, 2).' '.config('store.currency', 'BDT'),
                'image' => $product->getFirstImageUrl('thumb'),
                'sku' => $product->sku,
            ];
        }));
    }

    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $this->cartService->add($request->integer('variant_id'), $request->integer('quantity'));

        return response()->json([
            'success' => true,
            'cart_count' => $this->cartService->count(),
        ]);
    }

    public function updateCart(Request $request, int $variantId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $this->cartService->update($variantId, $request->integer('quantity'));

        return response()->json([
            'success' => true,
            'cart_count' => $this->cartService->count(),
        ]);
    }

    public function removeFromCart(int $variantId): JsonResponse
    {
        $this->cartService->remove($variantId);

        return response()->json([
            'success' => true,
            'cart_count' => $this->cartService->count(),
        ]);
    }

    public function clearCart(): JsonResponse
    {
        $this->cartService->clear();

        return response()->json(['success' => true]);
    }

    public function cartCount(): JsonResponse
    {
        return response()->json(['count' => $this->cartService->count()]);
    }
}
