<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
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
        $cart = $this->cartService->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.page')
                ->with('error', __('Your cart is empty.'));
        }

        return view('storefront.checkout.index', [
            'cart' => $cart,
        ]);
    }

    public function store(Request $request)
    {
        // Will be implemented with PlaceOrderAction
        return redirect()->route('checkout')
            ->with('error', __('Order placement not yet implemented.'));
    }
}
