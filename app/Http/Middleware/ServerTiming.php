<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gắn thời gian xử lý + số query DB vào header Server-Timing để theo dõi hiệu năng
 * (xem trong DevTools → Network → Timing; không ảnh hưởng nội dung response).
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $queries = 0;
        $dbTime = 0.0;

        DB::listen(function ($query) use (&$queries, &$dbTime) {
            $queries++;
            $dbTime += (float) $query->time;
        });

        $response = $next($request);

        $appMs = round((microtime(true) - $start) * 1000, 1);
        $dbMs = round($dbTime, 1);

        // Response của Symfony luôn có property headers (HeaderBag)
        if (isset($response->headers)) {
            $response->headers->set('Server-Timing', "app;dur={$appMs}, db;dur={$dbMs}, dbq;count={$queries}");
        }

        return $response;
    }
}
