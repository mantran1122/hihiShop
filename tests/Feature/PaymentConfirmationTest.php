<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\ThanhToan;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $admin;

    private DonHang $order;

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

        $variant = SanPham::with('bienThes')->firstOrFail()->bienThes->firstOrFail();

        $this->actingAs($this->customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $this->actingAs($this->customer)->post('/checkout', [
            'name' => 'Khách CK',
            'phone' => '0901234567',
            'province' => 'Hà Nội',
            'address' => '1 Đường CK',
            'shipping_service' => 'fast',
            'payment_method' => 'vietqr',
        ]);

        $this->order = DonHang::latest('id')->firstOrFail();
    }

    public function test_customer_can_report_transfer_without_marking_paid(): void
    {
        $this->actingAs($this->customer)
            ->postJson(route('checkout.payment.reported', $this->order->code))
            ->assertOk()
            ->assertJson(['success' => true, 'already_paid' => false]);

        $this->order->refresh();

        // Không được tự đánh dấu đã thanh toán
        $this->assertSame('unpaid', $this->order->payment_status);
        $this->assertNotNull($this->order->customer_reported_paid_at);
    }

    public function test_report_is_idempotent(): void
    {
        $this->actingAs($this->customer)
            ->postJson(route('checkout.payment.reported', $this->order->code));

        $first = $this->order->fresh()->customer_reported_paid_at;

        $this->actingAs($this->customer)
            ->postJson(route('checkout.payment.reported', $this->order->code));

        $this->assertSame(
            $first->toDateTimeString(),
            $this->order->fresh()->customer_reported_paid_at->toDateTimeString()
        );
    }

    public function test_other_customer_cannot_report(): void
    {
        $other = User::factory()->create();

        $this->actingAs($other)
            ->postJson(route('checkout.payment.reported', $this->order->code))
            ->assertStatus(403);

        $this->assertNull($this->order->fresh()->customer_reported_paid_at);
    }

    public function test_guest_cannot_report(): void
    {
        $this->app['auth']->forgetGuards();
        $this->flushSession();

        $this->postJson(route('checkout.payment.reported', $this->order->code))
            ->assertStatus(401);

        $this->assertNull($this->order->fresh()->customer_reported_paid_at);
    }

    public function test_admin_can_confirm_payment(): void
    {
        $this->actingAs($this->customer)
            ->postJson(route('checkout.payment.reported', $this->order->code));

        $this->actingAs($this->admin)
            ->post(route('admin.orders.confirmPayment', $this->order->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->order->refresh();
        $this->assertSame('paid', $this->order->payment_status);
        $this->assertSame('processing', $this->order->status);

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->order->id,
            'provider' => 'manual',
            'status' => 'paid',
        ]);

        // Trang polling trả is_paid = true để màn hình khách tự chuyển sang trang thành công
        $this->actingAs($this->customer)
            ->getJson(route('checkout.payment.status', $this->order->code))
            ->assertOk()
            ->assertJson(['is_paid' => true]);
    }

    public function test_customer_cannot_confirm_payment(): void
    {
        $this->actingAs($this->customer)
            ->post(route('admin.orders.confirmPayment', $this->order->id))
            ->assertStatus(403);

        $this->assertSame('unpaid', $this->order->fresh()->payment_status);
    }

    public function test_customer_sees_waiting_notice_on_qr_page(): void
    {
        $this->actingAs($this->customer)
            ->get(route('checkout.payment.qr', $this->order->code))
            ->assertOk()
            ->assertSee('Tôi đã chuyển khoản xong');
    }
}
