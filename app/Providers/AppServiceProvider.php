<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\GioHang;
use App\Models\DanhMuc;

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
        Paginator::useTailwind();

        // Share Cart Count and Categories to header across views
        View::composer(['layouts.shop', 'partials.header'], function ($view) {
            $cartCount = 0;
            try {
                if (auth()->check()) {
                    $cart = GioHang::where('user_id', auth()->id())->first();
                    $cartCount = $cart ? (int) $cart->mucs()->sum('qty') : 0;
                }
            } catch (\Throwable $e) {
                $cartCount = 0;
            }

            try {
                $headerCategories = DanhMuc::active()->orderBy('position')->get();
            } catch (\Throwable $e) {
                $headerCategories = collect();
            }

            $view->with('cartCount', $cartCount)
                 ->with('headerCategories', $headerCategories);
        });
    }
}
