<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rules\Password;
use App\Models\GioHang;
use App\Models\DanhMuc;
use App\Models\Setting;
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

        // Production: bắt buộc mật khẩu mạnh hơn (>=10 ký tự, có hoa/thường/số)
        Password::defaults(function () {
            return $this->app->isProduction()
                ? Password::min(10)->letters()->mixedCase()->numbers()
                : Password::min(8);
        });

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

            // Danh mục cho menu: cache 5 phút (xóa cache khi admin sửa danh mục)
            $headerCategories = collect();
            try {
                $headerCategories = Cache::store('runtime')->remember(
                    'hihi.header_categories',
                    now()->addMinutes(5),
                    fn () => DanhMuc::active()->orderBy('position')->get()
                );
            } catch (\Throwable $e) {
                $headerCategories = collect();
            }

            // Thông báo chạy trên đầu trang (sửa trong Admin → Cài đặt hiển thị)
            $announcementEnabled = true;
            $announcementText = 'Miễn phí giao hàng COD toàn quốc cho tất cả đơn hàng hôm nay';

            try {
                $announcement = Cache::store('runtime')->remember(
                    'hihi.announcement',
                    now()->addMinutes(10),
                    fn () => Setting::many(['announcement.enabled', 'announcement.text'])
                );

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
