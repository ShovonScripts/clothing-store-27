<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
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
        return view('storefront.cart.index', [
            'cart' => $this->cartService->getCart(),
        ]);
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
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function remove(int $variantId)
    {
        $this->cartService->remove($variantId);

        return response()->json([
            'success' => true,
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function clear()
    {
        $this->cartService->clear();

        return response()->json(['success' => true]);
    }
}
