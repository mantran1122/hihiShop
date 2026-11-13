<?php

use Illuminate\Foundation\Application;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware): void {
        // Ghi thời gian xử lý vào header Server-Timing (theo dõi hiệu năng)
        $middleware->append(\App\Http\Middleware\ServerTiming::class);

        // Vercel / PaaS chạy sau reverse proxy: tin header X-Forwarded-* để
        // Laravel nhận đúng HTTPS, sinh URL và cookie secure chính xác.
        $middleware->trustProxies(at: '*');

        // Miễn trừ CSRF cho webhook thanh toán của bên thứ ba (PayOS, Casso, Ngân hàng)
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);

        // Middleware Spatie Permission
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions): void {
        // Cấu hình exception (nếu cần)
    })->create();
