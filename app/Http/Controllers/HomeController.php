<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\DanhMuc;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache 5 phút cho danh mục (kèm ảnh đại diện) — tránh 8 query mỗi lần tải trang
        $categories = Cache::store('runtime')->remember(
            'hihi.home_categories',
            now()->addMinutes(5),
            fn () => DanhMuc::active()
                ->with(['sanPhams' => fn ($query) => $query->with('anh')->orderBy('id')])
                ->orderBy('position')
                ->get()
        );

        // Cache 1 phút cho danh sách sản phẩm mới nhất
        $products = Cache::store('runtime')->remember(
            'hihi.home_products',
            now()->addMinute(),
            fn () => SanPham::active()
                ->with('bienTheMacDinh', 'anh', 'bienThes')
                ->latest()
                ->take(12)
                ->get()
        );

        return view('home', compact('categories', 'products'));
    }
}
