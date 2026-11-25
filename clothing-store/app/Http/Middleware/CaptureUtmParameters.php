<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only capture on first visit (when UTM not already in session)
        if (! $request->session()->has('utm_captured')) {
            $utmParams = [
                'utm_source' => $request->query('utm_source'),
                'utm_medium' => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
                'utm_term' => $request->query('utm_term'),
                'utm_content' => $request->query('utm_content'),
                'fbclid' => $request->query('fbclid'),
                'gclid' => $request->query('gclid'),
                'landing_page' => $request->fullUrl(),
                'referrer' => $request->headers->get('referer'),
            ];

            // Filter out null values
            $utmParams = array_filter($utmParams, fn ($v) => $v !== null);

            if (! empty($utmParams)) {
                $request->session()->put('utm_params', $utmParams);
                $request->session()->put('utm_captured', true);
            }
        }

        return $next($request);
    }
}
