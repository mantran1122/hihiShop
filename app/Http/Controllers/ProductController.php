<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\UserActivity;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = SanPham::active()->with(['bienTheMacDinh','anh','bienThes'])->latest()->paginate(12);
        $q = 'Tất cả sản phẩm';
        return view('search', compact('products', 'q'));
    }

    public function show($slug)
    {
        $p = SanPham::with(['bienThes','anh','danhMuc'])->where('slug',$slug)->firstOrFail();

        $reviews = $p->approvedReviews()->with('user')->latest()->get();

        $reviewStats = [
            'count' => $reviews->count(),
            'average' => round((float) $reviews->avg('rating'), 1),
            'distribution' => collect(range(5, 1))->mapWithKeys(
                fn ($star) => [$star => $reviews->where('rating', $star)->count()]
            ),
        ];

        $myReview = auth()->check()
            ? $reviews->firstWhere('user_id', auth()->id())
            : null;

        $isWished = $p->isWishedBy(auth()->user());

        $this->trackProductView($p);

        return view('product', compact('p', 'reviews', 'reviewStats', 'myReview', 'isWished'));
    }

    /**
     * Ghi nhận hành vi xem sản phẩm để chatbot tự cập nhật hồ sơ sở thích.
     */
    private function trackProductView(SanPham $product): void
    {
        try {
            UserActivity::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'event_type' => 'product_view',
                'payload' => [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'price' => (float) $product->price,
                ],
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Không để lỗi tracking làm hỏng trang sản phẩm
        }
    }
    // app/Http/Controllers/ProductController.php
    public function search(Request $request)
    {
        $q = $request->input('q');

        $products = SanPham::where('name', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->paginate(12);

        return view('search', compact('products', 'q'));
    }

}
