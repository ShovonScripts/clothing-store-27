<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $cartItems = $this->cartService->getCart();
        $variantIds = $cartItems->keys()->toArray();

        if (empty($variantIds)) {
            return redirect()->route('cart.page')
                ->with('error', __('Your cart is empty.'));
        }

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
        $shipping = $subtotal >= 200000 ? 0 : 8000;
        $total = $subtotal + $shipping;

        $districts = District::where('is_active', true)->orderBy('name')->get();

        return view('storefront.checkout.index', compact('items', 'subtotal', 'shipping', 'total', 'districts'));
    }

    public function store(Request $request)
    {
        // Will be implemented with PlaceOrderAction
        return redirect()->route('checkout')
            ->with('error', __('Order placement not yet implemented.'));
    }
}
