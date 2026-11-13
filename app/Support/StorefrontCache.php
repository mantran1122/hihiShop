<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Xoá cache hiển thị (store "runtime") khi admin thay đổi dữ liệu.
 */
class StorefrontCache
{
    public static function forgetCategories(): void
    {
        static::forget(['hihi.header_categories', 'hihi.home_categories']);
    }

    public static function forgetProducts(): void
    {
        static::forget(['hihi.home_products']);
    }

    public static function forgetAnnouncement(): void
    {
        static::forget(['hihi.announcement']);
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
