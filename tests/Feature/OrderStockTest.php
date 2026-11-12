<?php

namespace Tests\Feature;

use App\Models\BienTheSanPham;
use App\Models\Coupon;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $admin;

    private BienTheSanPham $variant;

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

        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->variant = SanPham::with('bienThes')->firstOrFail()->bienThes->firstOrFail();
    }

    private function addToCart(int $qty = 1): void
    {
        $this->actingAs($this->customer)->post('/cart/add', [
            'variant_id' => $this->variant->id,
            'qty' => $qty,
        ]);
    }

    private function placeOrder(?string $couponCode = null): void
    {
        $this->actingAs($this->customer)->post('/checkout', array_filter([
            'name' => 'Khách Tồn Kho',
            'phone' => '0911000111',
            'province' => 'Hà Nội',
            'address' => '1 Đường Kho',
            'shipping_service' => 'fast',
            'payment_method' => 'cod',
            'coupon_code' => $couponCode,
        ]));
    }

    public function test_cancel_reason_and_admin_note_are_saved_and_visible_to_customer(): void
    {
        $this->addToCart(1);
        $this->placeOrder();

        $order = DonHang::latest('id')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.orders.updateStatus', $order->id), [
            'status' => 'cancelled',
            'cancel_reason' => 'Khách đổi ý',
            'admin_note' => 'Đã gọi xác nhận với khách',
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame('Khách đổi ý', $order->cancel_reason);
        $this->assertSame('Đã gọi xác nhận với khách', $order->admin_note);

        $this->actingAs($this->customer)
            ->get(route('orders.show', $order->code))
            ->assertOk()
            ->assertSee('Khách đổi ý');
    }

    public function test_cancelling_order_restores_stock_and_coupon_usage(): void
    {
        $coupon = Coupon::where('code', 'HIHI10')->firstOrFail();
        $stockBefore = (int) $this->variant->stock_qty;

        $this->addToCart(2);
        $this->placeOrder('HIHI10');

        $order = DonHang::latest('id')->firstOrFail();
        $this->assertSame($stockBefore - 2, (int) $this->variant->fresh()->stock_qty);
        $this->assertSame(1, (int) $coupon->fresh()->used);

        // Admin huỷ đơn
        $this->actingAs($this->admin)
            ->post(route('admin.orders.updateStatus', $order->id), ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertSame($stockBefore, (int) $this->variant->fresh()->stock_qty);
        $this->assertSame(0, (int) $coupon->fresh()->used);
        $this->assertSame('cancelled', $order->fresh()->status);

        // Huỷ lần 2 không được cộng tồn kho lần nữa
        $this->actingAs($this->admin)
            ->post(route('admin.orders.updateStatus', $order->id), ['status' => 'cancelled']);

        $this->assertSame($stockBefore, (int) $this->variant->fresh()->stock_qty);
        $this->assertSame(0, (int) $coupon->fresh()->used);
    }

    public function test_checkout_is_blocked_when_stock_runs_out(): void
    {
        $this->addToCart(2);

        // Mô phỏng kho bị người khác mua hết sau khi khách đã thêm vào giỏ
        $this->variant->update(['stock_qty' => 1]);

        $this->placeOrder();

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, (int) $this->variant->fresh()->stock_qty);
    }

    public function test_cannot_order_more_than_available_stock(): void
    {
        $this->variant->update(['stock_qty' => 3]);
        $this->addToCart(5);

        $this->placeOrder();

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(3, (int) $this->variant->fresh()->stock_qty);
    }
}
