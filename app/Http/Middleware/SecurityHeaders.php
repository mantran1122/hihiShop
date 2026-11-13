<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gắn các header bảo mật cơ bản cho mọi response.
 * (CSP vẫn cho phép inline/eval vì app dùng Alpine.js và một số script inline.)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!isset($response->headers)) {
            return $response;
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; "
            ."img-src 'self' data: https:; "
            ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
            ."font-src 'self' data: https://fonts.gstatic.com; "
            ."script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
            ."connect-src 'self'; "
            ."frame-ancestors 'self'; "
            ."base-uri 'self'; "
            ."form-action 'self'"
        );

        return $response;
    }
}
