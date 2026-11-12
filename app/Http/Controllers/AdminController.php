<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Coupon;
use App\Models\DonHang;
use App\Models\Review;
use App\Models\SanPham;
use App\Models\BienTheSanPham;
use App\Models\User;
use App\Models\DanhMuc;
use App\Models\ThuongHieu;
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
        $data = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled',
            'cancel_reason' => 'nullable|string|max:255',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $order = DonHang::with('mucs')->findOrFail($id);
        $wasCancelled = $order->status === 'cancelled';

        DB::transaction(function () use ($request, $data, $order, $wasCancelled) {
            // Huỷ đơn → hoàn tồn kho và trả lại lượt dùng voucher
            if ($data['status'] === 'cancelled' && !$wasCancelled) {
                foreach ($order->mucs as $item) {
                    if ($item->variant_id) {
                        BienTheSanPham::whereKey($item->variant_id)->increment('stock_qty', $item->qty);
                    }
                }

                foreach ([$order->coupon_code, $order->shipping_coupon_code] as $code) {
                    if (!empty($code)) {
                        Coupon::where('code', $code)->where('used', '>', 0)->decrement('used');
                    }
                }

                $order->cancel_reason = $data['cancel_reason'] ?? null;
            }

            if ($request->has('admin_note')) {
                $order->admin_note = $data['admin_note'] ?? null;
            }

            $order->status = $data['status'];

            // Nếu hoàn thành đơn hàng, tự động đánh dấu đã thanh toán
            if (in_array($data['status'], ['completed', 'delivered'])) {
                $order->payment_status = 'paid';
                $order->shipping_status = 'fulfilled';
            } elseif ($data['status'] === 'cancelled') {
                $order->shipping_status = 'cancelled';
            }

            $order->save();
        });

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
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
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
        $data['per_user_limit'] = $data['per_user_limit'] ?? null;

        return $data;
    }

    // 6.11. Danh sách đánh giá sản phẩm
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'sanPham'])->latest('id');

        if ($request->filled('status')) {
            $query->where('is_approved', $request->input('status') === 'approved');
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    // 6.12. Duyệt / ẩn đánh giá
    public function toggleReview($id)
    {
        $review = Review::with('user')->findOrFail($id);
        $review->is_approved = !$review->is_approved;
        $review->save();

        $status = $review->is_approved ? 'Đã duyệt' : 'Đã ẩn';

        return back()->with('success', "{$status} đánh giá của ".($review->user->name ?? 'khách hàng'));
    }

    // 6.13. Xoá đánh giá
    public function destroyReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Đã xoá đánh giá.');
    }

    // 6.14. Danh sách danh mục
    public function categories()
    {
        $categories = DanhMuc::withCount('sanPhams')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    // 6.15. Form tạo danh mục
    public function createCategory()
    {
        return view('admin.categories.form', [
            'category' => null,
            'parents' => DanhMuc::orderBy('name')->get(),
        ]);
    }

    // 6.16. Lưu danh mục mới
    public function storeCategory(Request $request)
    {
        $data = $this->validateCategoryPayload($request);

        DanhMuc::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', "Đã thêm danh mục: {$data['name']}");
    }

    // 6.17. Form sửa danh mục
    public function editCategory($id)
    {
        $category = DanhMuc::findOrFail($id);

        return view('admin.categories.form', [
            'category' => $category,
            'parents' => DanhMuc::where('id', '!=', $category->id)->orderBy('name')->get(),
        ]);
    }

    // 6.18. Cập nhật danh mục
    public function updateCategory(Request $request, $id)
    {
        $category = DanhMuc::findOrFail($id);
        $data = $this->validateCategoryPayload($request, $category);

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', "Đã cập nhật danh mục: {$category->name}");
    }

    // 6.19. Bật / tắt danh mục
    public function toggleCategory($id)
    {
        $category = DanhMuc::withCount('sanPhams')->findOrFail($id);
        $productCount = (int) ($category->san_phams_count ?? 0);

        if ($category->is_active && $productCount > 0) {
            return back()->with('error', "Danh mục \"{$category->name}\" còn {$productCount} sản phẩm. Hãy chuyển sản phẩm sang danh mục khác trước khi ẩn.");
        }

        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'Đã bật' : 'Đã ẩn';

        return back()->with('success', "{$status} danh mục: {$category->name}");
    }

    /**
     * Validate dữ liệu danh mục (create/update).
     */
    private function validateCategoryPayload(Request $request, ?DanhMuc $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'nullable', 'string', 'max:140', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('categories', 'slug')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id', Rule::notIn([$category?->id])],
            'position' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'slug.regex' => 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.',
            'slug.unique' => 'Slug này đã được dùng.',
            'parent_id.not_in' => 'Danh mục cha không thể là chính nó.',
        ]);

        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);
        $data['parent_id'] = $data['parent_id'] ?? null;
        $data['position'] = $data['position'] ?? 0;
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return $data;
    }

    // 6.20. Danh sách thương hiệu
    public function brands()
    {
        $brands = ThuongHieu::withCount('sanPhams')->orderBy('name')->get();

        return view('admin.brands.index', compact('brands'));
    }

    // 6.21. Form tạo thương hiệu
    public function createBrand()
    {
        return view('admin.brands.form', ['brand' => null]);
    }

    // 6.22. Lưu thương hiệu
    public function storeBrand(Request $request)
    {
        $data = $this->validateBrandPayload($request);

        ThuongHieu::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', "Đã thêm thương hiệu: {$data['name']}");
    }

    // 6.23. Form sửa thương hiệu
    public function editBrand($id)
    {
        return view('admin.brands.form', ['brand' => ThuongHieu::findOrFail($id)]);
    }

    // 6.24. Cập nhật thương hiệu
    public function updateBrand(Request $request, $id)
    {
        $brand = ThuongHieu::findOrFail($id);
        $brand->update($this->validateBrandPayload($request, $brand));

        return redirect()->route('admin.brands.index')
            ->with('success', "Đã cập nhật thương hiệu: {$brand->name}");
    }

    // 6.25. Xoá thương hiệu (chỉ khi chưa gắn sản phẩm)
    public function destroyBrand($id)
    {
        $brand = ThuongHieu::withCount('sanPhams')->findOrFail($id);
        $productCount = (int) ($brand->san_phams_count ?? 0);

        if ($productCount > 0) {
            return back()->with('error', "Thương hiệu \"{$brand->name}\" đang gắn với {$productCount} sản phẩm, không thể xoá.");
        }

        $brand->delete();

        return back()->with('success', "Đã xoá thương hiệu: {$brand->name}");
    }

    /**
     * Validate dữ liệu thương hiệu (create/update).
     */
    private function validateBrandPayload(Request $request, ?ThuongHieu $brand = null): array
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('brands', 'name')->ignore($brand?->id),
            ],
            'slug' => [
                'nullable', 'string', 'max:140', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('brands', 'slug')->ignore($brand?->id),
            ],
            'origin' => ['nullable', 'string', 'max:80'],
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'name.unique' => 'Tên thương hiệu này đã tồn tại.',
            'slug.unique' => 'Slug này đã được dùng.',
            'slug.regex' => 'Slug chỉ gồm chữ thường, số và dấu gạch ngang.',
        ]);

        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);

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
