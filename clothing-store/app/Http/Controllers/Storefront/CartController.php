<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $cartItems = $this->cartService->getCart();
        $variantIds = $cartItems->keys()->toArray();

        $variants = ProductVariant::whereIn('id', $variantIds)
            ->with(['product.media', 'color'])
            ->get()
            ->keyBy('id');

        $items = $cartItems->map(function ($quantity, $variantId) use ($variants) {
            $variant = $variants->get($variantId);
            if (! $variant) {
                return null;
            }

            return [
                'variant' => $variant,
                'quantity' => $quantity,
                'subtotal' => $variant->getEffectivePrice() * $quantity,
            ];
        })->filter();

        $subtotal = $items->sum('subtotal');

        return view('storefront.cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request)
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

    public function update(Request $request, int $variantId)
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

    public function remove(int $variantId)
    {
        $this->cartService->remove($variantId);

        return response()->json([
            'success' => true,
            'cart_count' => $this->cartService->count(),
        ]);
    }

    public function clear()
    {
        $this->cartService->clear();

        return response()->json(['success' => true]);
    }
}
