<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminIdleTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('admin*') && ! $request->is('admin')) {
            return $next($request);
        }

        $timeout = Config::get('session.admin_idle_timeout');

        if (! $timeout) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();

        if (! $user || ! $user->hasRole(['owner', 'manager', 'staff'])) {
            return $next($request);
        }

        $lastActivity = Session::get('admin_last_activity');
        $now = time();

        if ($lastActivity && ($now - $lastActivity) > ($timeout * 60)) {
            // Idle timeout exceeded - log out and redirect to login
            Auth::guard('web')->logout();
            Session::invalidate();
            Session::regenerateToken();

            return redirect()->route('filament.admin.auth.login')
                ->with('error', __('Your session has expired due to inactivity. Please log in again.'));
        }

        // Update last activity timestamp
        Session::put('admin_last_activity', $now);

        return $next($request);
    }
}