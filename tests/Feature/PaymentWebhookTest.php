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

class PaymentWebhookTest extends TestCase
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
            'name' => 'Khách Webhook',
            'phone' => '0900000000',
            'province' => 'Hà Nội',
            'address' => '1 Đường Test',
            'shipping_service' => 'fast',
            'payment_method' => 'vietqr',
        ]);

        $this->order = DonHang::latest('id')->firstOrFail();
    }

    public function test_webhook_marks_order_paid_when_no_secret_configured(): void
    {
        config(['payment.webhook.secret' => '']);

        $response = $this->postJson('/payment/webhook', [
            'description' => 'HIHI '.$this->order->code,
            'amount' => 100000,
            'reference' => 'TX-NO-SECRET',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->order->refresh();
        $this->assertSame('paid', $this->order->payment_status);
    }

    public function test_webhook_rejects_request_without_signature_when_secret_configured(): void
    {
        config(['payment.webhook.secret' => 'test-secret']);

        $response = $this->postJson('/payment/webhook', [
            'description' => 'HIHI '.$this->order->code,
            'amount' => 100000,
        ]);

        $response->assertStatus(401);

        $this->order->refresh();
        $this->assertSame('unpaid', $this->order->payment_status);
    }

    public function test_webhook_rejects_request_with_wrong_signature(): void
    {
        config(['payment.webhook.secret' => 'test-secret']);

        $payload = ['description' => 'HIHI '.$this->order->code, 'amount' => 100000];
        $json = json_encode($payload);

        $response = $this->call('POST', '/payment/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => hash_hmac('sha256', $json, 'secret-khac'),
        ], $json);

        $response->assertStatus(401);

        $this->order->refresh();
        $this->assertSame('unpaid', $this->order->payment_status);
    }

    public function test_webhook_accepts_valid_signature(): void
    {
        config(['payment.webhook.secret' => 'test-secret']);

        $payload = [
            'description' => 'HIHI '.$this->order->code,
            'amount' => (int) $this->order->grand_total,
            'reference' => 'TX-VALID',
        ];
        $json = json_encode($payload);

        $response = $this->call('POST', '/payment/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => 'sha256='.hash_hmac('sha256', $json, 'test-secret'),
        ], $json);

        $response->assertOk()->assertJson(['success' => true]);

        $this->order->refresh();
        $this->assertSame('paid', $this->order->payment_status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $this->order->id,
            'status' => 'paid',
        ]);
    }
}
