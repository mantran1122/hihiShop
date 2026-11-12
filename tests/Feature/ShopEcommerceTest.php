<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\DonHang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\CategorySeeder;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\RolesSeeder;

class ShopEcommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
            DemoUserAndRolesSeeder::class,
            RolesSeeder::class,
        ]);
    }

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('HIHI');
        $response->assertSee('Danh mục');
        $response->assertSee('Sản phẩm mới nhất');
    }

    public function test_category_page_displays_products(): void
    {
        $category = DanhMuc::first();
        $this->assertNotNull($category);

        $response = $this->get('/category/' . $category->slug);
        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_product_detail_page_loads(): void
    {
        $product = SanPham::with('bienThes')->first();
        $this->assertNotNull($product);

        $response = $this->get('/product/' . $product->slug);
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Thêm vào giỏ hàng');
    }

    public function test_search_functionality(): void
    {
        $response = $this->get('/search?q=Citrus');
        $response->assertStatus(200);
        $response->assertSee('Kết quả tìm kiếm cho:');
    }

    public function test_cart_operations(): void
    {
        $customer = User::where('email', 'customer@example.com')->first();
        $this->assertNotNull($customer);

        $product = SanPham::with('bienThes')->first();
        $variant = $product->bienThes->first();

        // 1. Thêm vào giỏ
        $addResponse = $this->actingAs($customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1
        ]);
        $addResponse->assertRedirect(route('cart.index'));

        // 2. Xem giỏ hàng
        $cartResponse = $this->actingAs($customer)->get('/cart');
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee($product->name);
    }

    public function test_checkout_cod_creates_order_and_redirects(): void
    {
        $customer = User::where('email', 'customer@example.com')->first();
        $product = SanPham::with('bienThes')->first();
        $variant = $product->bienThes->first();

        // Thêm vào giỏ
        $this->actingAs($customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1
        ]);

        // Đặt hàng (với các trường mới: province, shipping_service, payment_method)
        $response = $this->actingAs($customer)->post('/checkout', [
            'name' => 'Nguyễn Kiểm Thử',
            'phone' => '0911223344',
            'province' => 'TP. Hồ Chí Minh',
            'address' => '123 Đường Test, Quận 1, TP. HCM',
            'shipping_service' => 'fast',
            'payment_method' => 'cod'
        ]);

        $order = DonHang::latest()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.success', $order->code));

        // Kiểm tra trang Success
        $successResponse = $this->get(route('checkout.success', $order->code));
        $successResponse->assertStatus(200);
        $successResponse->assertSee($order->code);
    }

    public function test_customer_can_view_order_history(): void
    {
        $customer = User::where('email', 'customer@example.com')->first();
        $response = $this->actingAs($customer)->get('/my-orders');
        $response->assertStatus(200);
        $response->assertSee('Đơn hàng của tôi');
    }

    public function test_regular_customer_cannot_access_admin(): void
    {
        $customer = User::where('email', 'customer@example.com')->first();
        $response = $this->actingAs($customer)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_manage_orders(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($admin);

        // 1. Vào Dashboard
        $dashResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Bảng Điều Khiển Tổng Quan');

        // 2. Vào Danh sách đơn hàng
        $ordersResponse = $this->actingAs($admin)->get('/admin/orders');
        $ordersResponse->assertStatus(200);
        $ordersResponse->assertSee('Danh Sách Đơn Hàng');

        // 3. Cập nhật trạng thái đơn hàng
        $order = DonHang::first();
        if ($order) {
            $updateResponse = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
                'status' => 'processing'
            ]);
            $updateResponse->assertRedirect();
            $order->refresh();
            $this->assertEquals('processing', $order->status);
        }
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $product = SanPham::first();
        $this->assertNotNull($product);

        $initialStatus = $product->is_active;
        $response = $this->actingAs($admin)->post("/admin/products/{$product->id}/toggle-status");
        $response->assertRedirect();

        $product->refresh();
        $this->assertEquals(!$initialStatus, $product->is_active);

        // Đổi lại như cũ
        $this->actingAs($admin)->post("/admin/products/{$product->id}/toggle-status");
    }
}

