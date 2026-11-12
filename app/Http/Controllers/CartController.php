<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\MucGioHang;
use App\Models\BienTheSanPham;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $req)
    {
        if (!Auth::check()) {
            session()->put('url.intended', route('cart.index'));
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem và quản lý giỏ hàng của bạn.');
        }

        $cart = GioHang::for($req->user(), $req->session()->getId());
        $cart->load('mucs.sanPham', 'mucs.bienThe');
        return view('cart', compact('cart'));
    }

    public function add(Request $req)
    {
        // Yêu cầu bắt buộc đăng nhập để thêm vào giỏ hàng
        if (!Auth::check()) {
            session()->put('url.intended', url()->previous());
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }

        $req->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        $qty = max((int)($req->qty ?? 1), 1);
        $variant = BienTheSanPham::with('sanPham')->findOrFail($req->variant_id);

        if ($variant->stock_qty < $qty) {
            return back()->with('error', 'Sản phẩm đã hết hàng hoặc số lượng yêu cầu vượt quá tồn kho.');
        }

        $cart = GioHang::for($req->user(), $req->session()->getId());
        $item = $cart->mucs()->where('variant_id', $variant->id)->first();
        $unit = $variant->price;

        if ($item) {
            $item->qty += $qty;
            $item->line_total = $item->qty * $unit;
            $item->save();
        } else {
            $item = MucGioHang::create([
                'cart_id' => $cart->id,
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'qty' => $qty,
                'unit_price' => $unit,
                'line_total' => $qty * $unit
            ]);
        }

        $cart->recalc();

        // Ghi nhận sự kiện thời gian thực cho Chatbot AI RAG Context
        try {
            UserActivity::create([
                'user_id' => Auth::id(),
                'session_id' => $req->session()->getId(),
                'event_type' => 'add_to_cart',
                'payload' => [
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'product_name' => $variant->sanPham?->name,
                    'qty' => $qty,
                    'price' => $unit,
                ],
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Ignore activity logging errors
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng thành công.');
    }

    public function update(Request $req)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $req->validate(['item_id' => 'required|exists:cart_items,id', 'qty' => 'required|integer|min:1']);
        $item = MucGioHang::findOrFail($req->item_id);
        $item->qty = (int)$req->qty;
        $item->line_total = $item->qty * $item->unit_price;
        $item->save();
        $item->gioHang->recalc();
        return back()->with('success', 'Đã cập nhật số lượng sản phẩm.');
    }

    public function remove(Request $req)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $req->validate(['item_id' => 'required|exists:cart_items,id']);
        $item = MucGioHang::findOrFail($req->item_id);
        $cart = $item->gioHang;
        $item->delete();
        $cart->recalc();
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
