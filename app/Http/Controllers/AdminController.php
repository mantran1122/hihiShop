<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\User;
use App\Models\DanhMuc;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    // 1. Dashboard tổng quan
    public function index()
    {
        $totalRevenue = DonHang::whereNotIn('status', ['cancelled'])->sum('grand_total');
        $totalOrders = DonHang::count();
        $pendingOrders = DonHang::where('status', 'pending')->count();
        $totalUsers = User::count();
        $recentOrders = DonHang::with(['diaChi', 'user'])->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'totalUsers',
            'recentOrders'
        ));
    }

    // 2. Quản lý Đơn hàng
    public function orders(Request $request)
    {
        $query = DonHang::with(['diaChi', 'user', 'mucs'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(12)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    // 3. Chi tiết Đơn hàng
    public function orderDetail($id)
    {
        $order = DonHang::with(['diaChi', 'user', 'mucs.sanPham', 'mucs.bienThe', 'payment'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // 4. Cập nhật trạng thái Đơn hàng
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        $order = DonHang::findOrFail($id);
        $order->status = $request->status;

        // Nếu hoàn thành đơn hàng, tự động đánh dấu đã thanh toán
        if (in_array($request->status, ['completed', 'delivered'])) {
            $order->payment_status = 'paid';
            $order->shipping_status = 'fulfilled';
        } elseif ($request->status === 'cancelled') {
            $order->shipping_status = 'cancelled';
        }

        $order->save();

        return back()->with('success', "Đã cập nhật trạng thái đơn hàng #{$order->code} sang: {$order->status}");
    }

    // 5. Quản lý Sản phẩm
    public function products(Request $request)
    {
        $query = SanPham::with(['danhMuc', 'bienThes', 'anh'])->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = DanhMuc::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    // 6. Bật / Tắt trạng thái Sản phẩm
    public function toggleProductStatus($id)
    {
        $product = SanPham::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        $st = $product->is_active ? 'Đã kích hoạt' : 'Đã ẩn';
        return back()->with('success', "{$st} sản phẩm: {$product->name}");
    }

    // 7. Quản lý Người dùng
    public function manageUsers()
    {
        $users = User::with('roles')->latest()->paginate(15);
        $roles = Role::all();
        return view('admin.users', compact('users', 'roles'));
    }

    // 8. Cập nhật Vai trò Người dùng
    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::findOrFail($id);

        // Không cho phép tự xóa quyền Admin của chính mình
        if ($user->id === auth()->id() && $request->role !== 'Admin') {
            return back()->with('error', 'Bạn không thể tự hạ quyền Admin của chính tài khoản đang đăng nhập!');
        }

        $user->syncRoles([$request->role]);

        return back()->with('success', "Đã gán vai trò '{$request->role}' cho tài khoản {$user->email}");
    }
}
