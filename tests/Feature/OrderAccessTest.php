<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private DonHang $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            DemoUserAndRolesSeeder::class,
            RolesSeeder::class,
        ]);

        $this->owner = User::where('email', 'customer@example.com')->firstOrFail();

        $variant = SanPham::with('bienThes')->firstOrFail()->bienThes->firstOrFail();

        $this->actingAs($this->owner)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $this->actingAs($this->owner)->post('/checkout', [
            'name' => 'Chủ Đơn Hàng',
            'phone' => '0912345678',
            'province' => 'Hà Nội',
            'address' => '1 Đường Bảo Mật',
            'shipping_service' => 'fast',
            'payment_method' => 'vietqr',
        ]);

        $this->order = DonHang::latest('id')->firstOrFail();
    }

    public function test_owner_can_view_order_pages(): void
    {
        $this->actingAs($this->owner)
            ->get(route('checkout.success', $this->order->code))
            ->assertOk();

        $this->actingAs($this->owner)
            ->get(route('checkout.payment.qr', $this->order->code))
            ->assertOk()
            ->assertSee($this->order->code);

        $this->actingAs($this->owner)
            ->getJson(route('checkout.payment.status', $this->order->code))
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->app['auth']->forgetGuards();
        $this->flushSession();

        $this->get(route('checkout.success', $this->order->code))
            ->assertRedirect(route('login'));

        $this->get(route('checkout.payment.qr', $this->order->code))
            ->assertRedirect(route('login'));
    }

    public function test_other_customer_cannot_view_order(): void
    {
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('checkout.success', $this->order->code))
            ->assertStatus(403);

        $this->actingAs($other)
            ->get(route('checkout.payment.qr', $this->order->code))
            ->assertStatus(403);

        $this->actingAs($other)
            ->getJson(route('checkout.payment.status', $this->order->code))
            ->assertStatus(403);
    }

    public function test_other_customer_cannot_simulate_payment(): void
    {
        $other = User::factory()->create();

        $this->actingAs($other)
            ->post(route('checkout.payment.simulate', $this->order->code))
            ->assertStatus(403);

        $this->order->refresh();
        $this->assertSame('unpaid', $this->order->payment_status);
    }

    public function test_admin_can_view_any_order(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('checkout.success', $this->order->code))
            ->assertOk();
    }
}
