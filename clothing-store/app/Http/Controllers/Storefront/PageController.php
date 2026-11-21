<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        return view('storefront.pages.show', [
            'page' => null, // Will be replaced with actual page
        ]);
    }
}
