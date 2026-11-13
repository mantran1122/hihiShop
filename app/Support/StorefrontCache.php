<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Quản lý cache hiển thị (store "runtime") và xoá khi admin thay đổi dữ liệu.
 */
class StorefrontCache
{
    public static function forgetCategories(): void
    {
        static::forget(['hihi.header_categories', 'hihi.home_categories']);
    }

    /**
     * Tăng "phiên bản" sản phẩm: mọi cache khoá theo version cũ sẽ tự hết hiệu lực.
     */
    public static function forgetProducts(): void
    {
        static::forget(['hihi.home_products']);

        try {
            Cache::store('runtime')->increment('hihi.products.version');
        } catch (\Throwable $e) {
            // bỏ qua
        }
    }

    public static function forgetAnnouncement(): void
    {
        static::forget(['hihi.announcement']);
    }

    public static function productVersion(): int
    {
        try {
            return (int) Cache::store('runtime')->get('hihi.products.version', 1);
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /**
     * @param  array<int, string>  $keys
     */
    protected static function forget(array $keys): void
    {
        try {
            Cache::store('runtime')->forget($keys);
        } catch (\Throwable $e) {
            // Cache chưa sẵn sàng (ví dụ lúc migrate) → bỏ qua
        }
    }
}
