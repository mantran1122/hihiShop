<?php

namespace Tests\Feature;

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

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

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

        $variant = SanPham::with('bienThes')->firstOrFail()->bienThes->firstOrFail();

        $this->actingAs($this->customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $this->actingAs($this->customer)->post('/checkout', [
            'name' => 'Khách Bảo Mật',
            'phone' => '0901112222',
            'province' => 'Hà Nội',
            'address' => '1 Đường Bảo Mật',
            'shipping_service' => 'fast',
            'payment_method' => 'vietqr',
        ]);

        $this->order = DonHang::latest('id')->firstOrFail();
    }

    public function test_webhook_is_rejected_in_production_without_secret(): void
    {
        $this->app['env'] = 'production';
        config(['payment.webhook.secret' => '']);

        $this->postJson('/payment/webhook', [
            'description' => 'HIHI ' . $this->order->code,
            'amount' => (int) $this->order->grand_total,
        ])->assertStatus(503);

        $this->assertSame('unpaid', $this->order->fresh()->payment_status);
    }

    public function test_webhook_rejects_amount_lower_than_order_total(): void
    {
        config(['payment.webhook.secret' => 'secret-test']);

        $payload = json_encode([
            'description' => 'HIHI ' . $this->order->code,
            'amount' => 1000,
        ]);

        $response = $this->call('POST', '/payment/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => hash_hmac('sha256', $payload, 'secret-test'),
        ], $payload);

        $response->assertStatus(422);
        $this->assertSame('unpaid', $this->order->fresh()->payment_status);
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');

        $this->assertStringContainsString(
            "default-src 'self'",
            (string) $response->headers->get('Content-Security-Policy')
        );
    }

    public function test_customer_cannot_escalate_role_via_profile_update(): void
    {
        $this->actingAs($this->customer)->patch('/profile', [
            'name' => 'Khách Hàng Mẫu',
            'email' => $this->customer->email,
            'role' => 'Admin',
            'is_active' => 1,
        ]);

        $this->assertFalse($this->customer->fresh()->hasRole('Admin'));
        $this->assertTrue((bool) $this->customer->fresh()->is_active);
    }
}
