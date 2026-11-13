<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gắn thời gian xử lý vào header Server-Timing để theo dõi hiệu năng
 * (xem được trong DevTools → Network → Timing, không ảnh hưởng response).
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        if (method_exists($response, 'headers')) {
            $duration = round((microtime(true) - $start) * 1000, 1);
            $response->headers->set('Server-Timing', "app;dur={$duration}");
        }

        return $response;
    }
}
