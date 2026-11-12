<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Coupon;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\BienTheSanPham;
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

    // 6.1. Form thêm sản phẩm
    public function createProduct()
    {
        return view('admin.products.form', [
            'product' => null,
            'variant' => null,
            'categories' => DanhMuc::orderBy('name')->get(),
        ]);
    }

    // 6.2. Lưu sản phẩm mới (kèm 1 biến thể mặc định)
    public function storeProduct(Request $request)
    {
        $data = $this->validateProductPayload($request);

        $product = DB::transaction(function () use ($data) {
            $product = SanPham::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
                'short_desc' => $data['short_desc'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            $product->bienThes()->create([
                'sku' => $data['sku'] ?: 'SP-'.Str::upper(Str::random(6)),
                'price' => $data['price'],
                'stock_qty' => $data['stock_qty'],
                'is_default' => true,
            ]);

            $this->syncCoverImage($product, $data['image'] ?? null);

            return $product;
        });

        return redirect()->route('admin.products.index')
            ->with('success', "Đã thêm sản phẩm: {$product->name}");
    }

    // 6.3. Form sửa sản phẩm
    public function editProduct($id)
    {
        $product = SanPham::with(['bienTheMacDinh', 'anh'])->findOrFail($id);

        return view('admin.products.form', [
            'product' => $product,
            'variant' => $product->bienTheMacDinh ?? $product->bienThes()->first(),
            'categories' => DanhMuc::orderBy('name')->get(),
        ]);
    }

    // 6.4. Cập nhật sản phẩm + biến thể mặc định
    public function updateProduct(Request $request, $id)
    {
        $product = SanPham::with('bienTheMacDinh')->findOrFail($id);
        $variant = $product->bienTheMacDinh ?? $product->bienThes()->first();
        $data = $this->validateProductPayload($request, $variant);

        DB::transaction(function () use ($data, $product, $variant) {
            $product->update([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'short_desc' => $data['short_desc'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);

            if ($variant) {
                $variant->update([
                    'sku' => $data['sku'] ?: $variant->sku,
                    'price' => $data['price'],
                    'stock_qty' => $data['stock_qty'],
                ]);
            } else {
                $product->bienThes()->create([
                    'sku' => $data['sku'] ?: 'SP-'.Str::upper(Str::random(6)),
                    'price' => $data['price'],
                    'stock_qty' => $data['stock_qty'],
                    'is_default' => true,
                ]);
            }

            $this->syncCoverImage($product, $data['image'] ?? null);
        });

        return redirect()->route('admin.products.index')
            ->with('success', "Đã cập nhật sản phẩm: {$product->name}");
    }

    /**
     * Validate dữ liệu sản phẩm dùng chung cho create/update.
     */
    private function validateProductPayload(Request $request, ?BienTheSanPham $variant = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'stock_qty' => ['required', 'integer', 'min:0', 'max:1000000'],
            'sku' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/'],
            'short_desc' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là số.',
            'stock_qty.required' => 'Vui lòng nhập số lượng tồn kho.',
            'sku.regex' => 'SKU chỉ được chứa chữ, số và các ký tự . _ -',
        ]);

        if (!empty($data['sku'])) {
            $duplicated = BienTheSanPham::where('sku', $data['sku'])
                ->when($variant, fn ($query) => $query->where('id', '!=', $variant->id))
                ->exists();

            if ($duplicated) {
                throw ValidationException::withMessages([
                    'sku' => 'SKU này đã được dùng cho một sản phẩm khác.',
                ]);
            }
        }

        if (!empty($data['image']) && !is_file(public_path($data['image']))) {
            throw ValidationException::withMessages([
                'image' => 'Không tìm thấy file ảnh trong thư mục public (ví dụ: images/placeholder-giay.svg).',
            ]);
        }

        return $data;
    }

    /**
     * Gán ảnh bìa cho sản phẩm (chỉ nhận đường dẫn đã có sẵn trong public).
     */
    private function syncCoverImage(SanPham $product, ?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $product->anh()->update(['is_cover' => false]);

        $product->anh()->updateOrCreate(
            ['path' => $path],
            ['is_cover' => true, 'sort' => 0]
        );
    }

    // 6.5. Danh sách voucher
    public function vouchers(Request $request)
    {
        $query = Coupon::query()->latest('id');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $vouchers = $query->paginate(20)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    // 6.6. Form tạo voucher
    public function createVoucher()
    {
        return view('admin.vouchers.form', ['voucher' => null]);
    }

    // 6.7. Lưu voucher mới
    public function storeVoucher(Request $request)
    {
        $data = $this->validateVoucherPayload($request);

        Coupon::create($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Đã tạo voucher {$data['code']}");
    }

    // 6.8. Form sửa voucher
    public function editVoucher($id)
    {
        $voucher = Coupon::findOrFail($id);

        return view('admin.vouchers.form', compact('voucher'));
    }

    // 6.9. Cập nhật voucher
    public function updateVoucher(Request $request, $id)
    {
        $voucher = Coupon::findOrFail($id);
        $data = $this->validateVoucherPayload($request, $voucher);

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Đã cập nhật voucher {$voucher->code}");
    }

    // 6.10. Bật / tắt voucher
    public function toggleVoucher($id)
    {
        $voucher = Coupon::findOrFail($id);
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();

        $status = $voucher->is_active ? 'Đã bật' : 'Đã tạm ngưng';

        return back()->with('success', "{$status} voucher {$voucher->code}");
    }

    /**
     * Validate dữ liệu voucher dùng chung cho create/update.
     */
    private function validateVoucherPayload(Request $request, ?Coupon $voucher = null): array
    {
        $valueRules = ['required', 'numeric', 'min:0'];

        if ($request->input('type') === 'percent') {
            $valueRules[] = 'max:100';
        }

        $data = $request->validate([
            'code' => [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('coupons', 'code')->ignore($voucher?->id),
            ],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['required', 'in:order_discount,free_shipping'],
            'type' => ['required', 'in:fixed,percent'],
            'value' => $valueRules,
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.unique' => 'Mã voucher này đã tồn tại.',
            'code.regex' => 'Mã voucher chỉ gồm chữ, số, dấu gạch dưới và gạch ngang.',
            'title.required' => 'Vui lòng nhập tên voucher.',
            'value.max' => 'Voucher giảm theo % không được vượt quá 100.',
            'ends_at.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        $data['code'] = Str::upper(trim($data['code']));
        $data['min_order'] = $data['min_order'] ?? 0;
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['usage_limit'] = $data['usage_limit'] ?? null;

        return $data;
    }

    // 7. Quản lý Người dùng
    public function manageUsers()    {
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
