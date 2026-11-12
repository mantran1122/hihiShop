<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
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

        return view('product', compact('p', 'reviews', 'reviewStats', 'myReview', 'isWished'));
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
