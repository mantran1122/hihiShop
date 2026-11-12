<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbHealthCheck extends Command
{
    protected $signature = 'hihi:db-check';

    protected $description = 'Kiểm tra kết nối CSDL (TLS, phiên bản), migration và dữ liệu mẫu trước khi cho admin test';

    public function handle(): int
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}", []);

        $this->line('Kết nối : <comment>'.$connection.'</comment> ('.($config['driver'] ?? '?').')');
        $this->line('Host    : '.($config['host'] ?? '-').':'.($config['port'] ?? '-'));
        $this->line('Database: '.($config['database'] ?? '-'));

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('KHÔNG kết nối được CSDL: '.$e->getMessage());
            $this->line('Gợi ý: kiểm tra DB_HOST/DB_PORT/DB_USERNAME/DB_PASSWORD; nếu là TiDB Cloud thì cần DB_SSL=true.');

            return self::FAILURE;
        }

        $this->info('✔ Kết nối thành công');

        if (($config['driver'] ?? '') === 'mysql') {
            $ssl = DB::selectOne("SHOW STATUS LIKE 'Ssl_cipher'");
            $cipher = $ssl->Value ?? '';

            if ($cipher !== '') {
                $this->info('✔ TLS đang bật ('.$cipher.')');
            } else {
                $this->warn('! TLS chưa bật — với TiDB Cloud / MySQL cloud bắt buộc TLS, hãy đặt DB_SSL=true');
            }

            $version = DB::selectOne('select version() as v')->v ?? '?';
            $this->line('Phiên bản server: '.$version);
        }

        $requiredTables = ['users', 'categories', 'products', 'product_variants', 'carts', 'orders', 'coupons', 'reviews', 'wishlists', 'sessions'];
        $missing = [];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        if ($missing !== []) {
            $this->newLine();
            $this->error('Thiếu bảng: '.implode(', ', $missing));
            $this->line('Chạy: php artisan migrate --seed --force');

            return self::FAILURE;
        }

        $this->info('✔ Đủ bảng chính ('.count($requiredTables).' bảng)');

        $rows = [
            ['Danh mục', DB::table('categories')->count()],
            ['Sản phẩm', DB::table('products')->count()],
            ['Biến thể', DB::table('product_variants')->count()],
            ['Voucher', DB::table('coupons')->count()],
            ['Đánh giá', DB::table('reviews')->count()],
            ['Người dùng', DB::table('users')->count()],
            ['Đơn hàng', DB::table('orders')->count()],
        ];

        $this->newLine();
        $this->table(['Hạng mục', 'Số bản ghi'], $rows);

        $problems = [];

        if (DB::table('products')->count() === 0) {
            $problems[] = 'chưa có sản phẩm nào → chạy: php artisan db:seed --force';
        }

        if (DB::table('coupons')->count() === 0) {
            $problems[] = 'chưa có voucher → chạy: php artisan db:seed --class=CouponSeeder --force';
        }

        if (DB::table('users')->count() === 0) {
            $problems[] = 'chưa có người dùng → chạy: php artisan db:seed --class=DemoUserAndRolesSeeder --force';
        }

        if ($problems !== []) {
            $this->warn('Cần bổ sung dữ liệu:');
            foreach ($problems as $problem) {
                $this->line('  • '.$problem);
            }

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('SẴN SÀNG: CSDL đã có đủ dữ liệu để test.');
        $this->line('Tài khoản demo: admin@example.com / admin123 · customer@example.com / customer123');

        return self::SUCCESS;
    }
}
