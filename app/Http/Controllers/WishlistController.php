<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Trang danh sách sản phẩm yêu thích của khách.
     */
    public function index(Request $request)
    {
        $items = Wishlist::with(['sanPham.anh', 'sanPham.bienTheMacDinh'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->filter(fn ($item) => $item->sanPham !== null)
            ->values();

        return view('wishlist', compact('items'));
    }

    /**
     * Thêm / bỏ tim một sản phẩm (hỗ trợ cả Ajax và submit form thường).
     */
    public function toggle(Request $request, SanPham $product)
    {
        $userId = $request->user()->id;

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $wished = false;
            $message = "Đã bỏ \"{$product->name}\" khỏi danh sách yêu thích.";
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $product->id,
            ]);
            $wished = true;
            $message = "Đã thêm \"{$product->name}\" vào danh sách yêu thích.";
        }

        $count = Wishlist::where('user_id', $userId)->count();

        if ($request->wantsJson()) {
            return response()->json([
                'wished' => $wished,
                'count' => $count,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
