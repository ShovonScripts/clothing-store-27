<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request): JsonResponse
    {
        $request->validate([
            'locale' => 'required|in:en,bn',
        ]);

        $locale = $request->input('locale');

        app()->setLocale($locale);
        session(['locale' => $locale]);

        $cookie = cookie('locale', $locale, 525600, '/', null, true, true, false, 'lax');

        return response()->json(['success' => true, 'locale' => $locale])->withCookie($cookie);
    }
}
