<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\NotifyAdminsOfNewOrder;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

        Event::listen(OrderPlaced::class, NotifyAdminsOfNewOrder::class);
    }
}
