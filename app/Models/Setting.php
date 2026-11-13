<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cấu hình có thể sửa từ trang quản trị (không cần deploy lại).
 * Giá trị lưu trong bảng settings, fallback về config/.env nếu chưa có.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::query()->where('key', $key)->value('value');

        return $value ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );

        // Xoá cache hiển thị để thay đổi có hiệu lực ngay
        try {
            \Illuminate\Support\Facades\Cache::store('runtime')->forget(['hihi.announcement', 'hihi.site']);
        } catch (\Throwable $e) {
            // bỏ qua nếu cache chưa sẵn sàng
        }
    }

    /**
     * Lấy nhiều key cùng lúc (1 query).
     *
     * @param  array<int, string>  $keys
     * @return array<string, string|null>
     */
    public static function many(array $keys): array
    {
        $stored = static::query()->whereIn('key', $keys)->pluck('value', 'key')->all();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $stored[$key] ?? null;
        }

        return $result;
    }
}
