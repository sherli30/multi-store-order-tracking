<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * Prevents the browser from caching the page so that when a user logs out,
     * they cannot use the back button to view sensitive cached pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Make sure it is an instance of Response before adding headers.
        if (method_exists($response, 'headers')) {
            $response->headers->set('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
