<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Khách đã đăng nhập gửi hoặc cập nhật đánh giá của mình cho một sản phẩm.
     */
    public function store(Request $request, SanPham $product)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'content' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá (1 - 5).',
            'rating.between' => 'Số sao đánh giá phải nằm trong khoảng 1 - 5.',
            'title.max' => 'Tiêu đề đánh giá tối đa 120 ký tự.',
            'content.max' => 'Nội dung đánh giá tối đa 2000 ký tự.',
        ]);

        $review = Review::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
            ],
            [
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'content' => $data['content'] ?? null,
                // Tự động duyệt để khách thấy ngay; admin có thể ẩn bằng cột is_approved.
                'is_approved' => true,
            ]
        );

        $message = $review->wasRecentlyCreated
            ? 'Cảm ơn bạn đã đánh giá sản phẩm!'
            : 'Đã cập nhật đánh giá của bạn.';

        return back()->with('success', $message);
    }

    /**
     * Khách xoá đánh giá của mình (Admin cũng được phép xoá).
     */
    public function destroy(Request $request, Review $review)
    {
        $user = $request->user();

        abort_unless(
            $user && ($review->user_id === $user->id || $user->hasRole('Admin')),
            403,
            'Bạn không có quyền xoá đánh giá này.'
        );

        $review->delete();

        return back()->with('success', 'Đã xoá đánh giá.');
    }
}
