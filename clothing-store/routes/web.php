<?php

use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\LocaleController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\RobotsController;
use App\Http\Controllers\Storefront\SitemapController;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\OrderSuccess;
use App\Livewire\Storefront\TrackOrder;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [CategoryController::class, 'index'])->name('shop');
Route::get('/categories', [CategoryController::class, 'categories'])->name('categories');

Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/search/suggestions', [ProductController::class, 'suggestions'])->name('search.suggestions');

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/cart', CartPage::class)->name('cart.page');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/order/success/{order}', OrderSuccess::class)->name('order.success');
Route::get('/track', TrackOrder::class)->name('track.order');

Route::prefix('api')->group(function () {
    Route::get('/districts', [DistrictController::class, 'index'])->name('api.districts.index');
    Route::get('/districts/{district}/areas', [DistrictController::class, 'areas'])->name('api.districts.areas');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

Route::prefix('admin')->middleware(['auth', 'role:owner|manager|staff'])->group(function () {
    // Filament will handle admin routes
});
