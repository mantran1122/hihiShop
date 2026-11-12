<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\SanPham;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Vài đánh giá mẫu để trang chi tiết sản phẩm không trống khi demo.
     */
    public function run(): void
    {
        $users = User::whereIn('email', [
            'customer@example.com',
            'staff@example.com',
            'admin@example.com',
        ])->get()->keyBy('email');

        $products = SanPham::orderBy('id')->take(3)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $samples = [
            [
                'email' => 'customer@example.com',
                'rating' => 5,
                'title' => 'Rất đáng tiền',
                'content' => 'Sản phẩm đúng như mô tả, đóng gói cẩn thận, giao hàng nhanh. Sẽ ủng hộ shop tiếp.',
            ],
            [
                'email' => 'staff@example.com',
                'rating' => 4,
                'title' => 'Chất lượng tốt',
                'content' => 'Hàng chính hãng, tem bảo hành đầy đủ. Trừ 1 sao vì hộp hơi móp khi nhận.',
            ],
        ];

        foreach ($products as $index => $product) {
            foreach ($samples as $offset => $sample) {
                $user = $users[$sample['email']] ?? null;
                if (!$user) {
                    continue;
                }

                // Giảm 1 sao ở một vài sản phẩm cho số liệu tự nhiên hơn
                $rating = max(3, min(5, $sample['rating'] - (($index + $offset) % 2)));

                Review::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'rating' => $rating,
                        'title' => $sample['title'],
                        'content' => $sample['content'],
                        'is_approved' => true,
                    ]
                );
            }
        }
    }
}
