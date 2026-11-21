<?php

use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CategoryController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [CategoryController::class, 'index'])->name('shop');
Route::get('/categories', [CategoryController::class, 'categories'])->name('categories');

Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.page');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{variant}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{variant}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/success/{order:order_number}', [OrderController::class, 'success'])->name('order.success');
Route::get('/track', [OrderController::class, 'track'])->name('track-order');
Route::post('/track', [OrderController::class, 'trackPost'])->name('track-order.post');

Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/search', [ProductController::class, 'search'])->name('search');

Route::prefix('admin')->middleware(['auth', 'role:owner|manager|staff'])->group(function () {
    // Filament will handle admin routes
});
