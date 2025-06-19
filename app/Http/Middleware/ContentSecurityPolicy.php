<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = $this->buildCspPolicy();

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    private function buildCspPolicy(): string
    {
        return "default-src 'self'; " .
               "img-src 'self' data: https://trusted-image-cdn.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; " .
               "font-src 'self' https://fonts.bunny.net; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
               "object-src 'none';";
    }
}
