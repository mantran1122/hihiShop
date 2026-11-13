<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\SanPham;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ReviewSeeder extends Seeder
{
    /**
     * Đánh giá mẫu để giao diện có dữ liệu khi demo.
     * Chỉ chạy ở local/testing hoặc khi bật SEED_DEMO_DATA=true —
     * production mặc định KHÔNG tạo để tránh đánh giá ảo.
     */
    public function run(): void
    {
        $demoEnabled = app()->environment(['local', 'testing'])
            || filter_var(env('SEED_DEMO_DATA', false), FILTER_VALIDATE_BOOLEAN);

        if (!$demoEnabled) {
            return;
        }

        $personas = [
            ['name' => 'Nguyễn Thu Hà', 'email' => 'thuha.demo@hihishop.vn'],
            ['name' => 'Trần Minh Anh', 'email' => 'minhanh.demo@hihishop.vn'],
            ['name' => 'Lê Quốc Bảo', 'email' => 'quocbao.demo@hihishop.vn'],
            ['name' => 'Phạm Ngọc Lan', 'email' => 'ngoclan.demo@hihishop.vn'],
        ];

        $role = Role::firstOrCreate(['name' => 'Customer']);

        $reviewers = collect($personas)->map(function (array $persona) use ($role) {
            $user = User::firstOrCreate(
                ['email' => $persona['email']],
                [
                    'name' => $persona['name'],
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            if (!$user->hasRole('Customer')) {
                $user->syncRoles([$role]);
            }

            return $user;
        })->values();

        $samplesByCategory = [
            'nuoc-hoa' => [
                ['rating' => 5, 'title' => 'Mùi thơm dễ chịu', 'content' => 'Hương cam chanh nhẹ, lưu được khoảng 6 tiếng trên da. Đóng gói cẩn thận, giao 2 ngày là nhận được.'],
                ['rating' => 4, 'title' => 'Đúng hàng chính hãng', 'content' => 'Có tem bảo hành đầy đủ. Hộp hơi móp một góc nhưng chai nguyên vẹn, mùi đúng như mô tả.'],
            ],
            'giay' => [
                ['rating' => 5, 'title' => 'Form chuẩn, đi êm', 'content' => 'Mình lấy size 42 vừa in, đế mềm đi cả ngày không mỏi. Vải canvas dễ vệ sinh.'],
                ['rating' => 4, 'title' => 'Đẹp hơn hình', 'content' => 'Màu trắng kem ngoài đời đẹp hơn ảnh. Giao nhanh, hộp giày còn nguyên.'],
            ],
            'mat-kinh' => [
                ['rating' => 5, 'title' => 'Chống chói tốt', 'content' => 'Đi nắng buổi trưa không bị chói mắt, gọng nhẹ đeo lâu không đau tai.'],
                ['rating' => 4, 'title' => 'Hợp khuôn mặt', 'content' => 'Form Wayfarer dễ đeo, kèm hộp cứng và khăn lau. Trừ 1 sao vì giao hơi chậm so với dự kiến.'],
            ],
            'dong-ho' => [
                ['rating' => 5, 'title' => 'Máy chạy chuẩn', 'content' => 'Đeo 3 ngày không sai giờ, mặt kính sapphire chưa thấy xước. Dây đeo chắc tay.'],
                ['rating' => 4, 'title' => 'Đóng gói chuyên nghiệp', 'content' => 'Hộp to, có thẻ bảo hành và hướng dẫn sử dụng. Mặt đồng hồ hơi nhỏ so với ảnh nhưng vẫn ưng.'],
            ],
            'default' => [
                ['rating' => 5, 'title' => 'Hài lòng', 'content' => 'Sản phẩm đúng mô tả, shop tư vấn nhiệt tình. Sẽ quay lại mua tiếp.'],
                ['rating' => 4, 'title' => 'Chất lượng ổn', 'content' => 'Đóng gói kỹ, giao đúng hẹn. Chất lượng tương xứng với giá tiền.'],
            ],
        ];

        $products = SanPham::with('danhMuc')->orderBy('id')->get();

        foreach ($products as $index => $product) {
            $categorySlug = $product->danhMuc?->slug ?? 'default';
            $samples = $samplesByCategory[$categorySlug] ?? $samplesByCategory['default'];

            foreach ($samples as $offset => $sample) {
                $reviewer = $reviewers[($index + $offset) % $reviewers->count()];

                Review::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'user_id' => $reviewer->id,
                    ],
                    [
                        'rating' => $sample['rating'],
                        'title' => $sample['title'],
                        'content' => $sample['content'],
                        'is_approved' => true,
                    ]
                );
            }
        }
    }
}
