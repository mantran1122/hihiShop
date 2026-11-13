<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\GioHang;
use App\Models\DanhMuc;
use App\Models\Wishlist;

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
            $wishlistCount = 0;
            try {
                if (auth()->check()) {
                    $cart = GioHang::where('user_id', auth()->id())->first();
                    $cartCount = $cart ? (int) $cart->mucs()->sum('qty') : 0;
                    $wishlistCount = Wishlist::where('user_id', auth()->id())->count();
                }
            } catch (\Throwable $e) {
                $cartCount = 0;
                $wishlistCount = 0;
            }

            try {
                $headerCategories = DanhMuc::active()->orderBy('position')->get();
            } catch (\Throwable $e) {
                $headerCategories = collect();
            }

            // Thông báo chạy trên đầu trang (sửa được trong Admin → Cài đặt hiển thị)
            $announcementEnabled = true;
            $announcementText = 'Miễn phí giao hàng COD toàn quốc cho tất cả đơn hàng hôm nay';

            try {
                $announcement = \App\Models\Setting::many(['announcement.enabled', 'announcement.text']);
                $announcementEnabled = ($announcement['announcement.enabled'] ?? '1') !== '0';
                $announcementText = $announcement['announcement.text'] ?: $announcementText;
            } catch (\Throwable $e) {
                // Bảng settings chưa migrate → dùng mặc định
            }

            $view->with('cartCount', $cartCount)
                 ->with('wishlistCount', $wishlistCount)
                 ->with('headerCategories', $headerCategories)
                 ->with('announcementEnabled', $announcementEnabled)
                 ->with('announcementText', $announcementText);
        });
    }
}
