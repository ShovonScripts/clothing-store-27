<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function success(string $orderNumber): View
    {
        return view('storefront.order.success', [
            'orderNumber' => $orderNumber,
        ]);
    }

    public function track(): View
    {
        return view('storefront.order.track');
    }

    public function trackPost(Request $request): View
    {
        $request->validate([
            'order_number' => 'required|string',
            'phone' => 'required|string',
        ]);

        return view('storefront.order.track', [
            'order' => null, // Will be replaced with actual order lookup
        ]);
    }
}
