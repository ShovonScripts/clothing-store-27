<?php

use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\LocaleController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\RobotsController;
use App\Http\Controllers\Storefront\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [CategoryController::class, 'index'])->name('shop');
Route::get('/categories', [CategoryController::class, 'categories'])->name('categories');

Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update/{variant}', [ProductController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/remove/{variant}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/clear', [ProductController::class, 'clearCart'])->name('cart.clear');
Route::get('/cart/count', [ProductController::class, 'cartCount'])->name('cart.count');

Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/search/suggestions', [ProductController::class, 'suggestions'])->name('search.suggestions');

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Route::prefix('api')->group(function () {
    Route::get('/districts', [DistrictController::class, 'index'])->name('api.districts.index');
    Route::get('/districts/{district}/areas', [DistrictController::class, 'areas'])->name('api.districts.areas');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

Route::prefix('admin')->middleware(['auth', 'role:owner|manager|staff'])->group(function () {
    // Filament will handle admin routes
});
