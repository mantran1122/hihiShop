<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CouponSeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

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

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
    }

    public function test_admin_can_open_payment_settings(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.payment'))
            ->assertOk()
            ->assertSee('Cài Đặt Thanh Toán VietQR')
            ->assertSee('MB');
    }

    public function test_admin_can_update_vietqr_settings(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.payment.update'), [
            'bank_id' => 'VCB',
            'bank_name' => 'Vietcombank',
            'account_no' => '0123456789',
            'account_name' => 'HIHI SHOP TEST',
            'template' => 'compact',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame('VCB', Setting::get('vietqr.bank_id'));
        $this->assertSame('0123456789', Setting::get('vietqr.account_no'));
        $this->assertSame('HIHI SHOP TEST', Setting::get('vietqr.account_name'));
        $this->assertSame('compact', Setting::get('vietqr.template'));
    }

    public function test_qr_page_uses_saved_settings(): void
    {
        // Đặt cấu hình mới
        $this->actingAs($this->admin)->post(route('admin.settings.payment.update'), [
            'bank_id' => 'TCB',
            'bank_name' => 'Techcombank',
            'account_no' => '19001234567',
            'account_name' => 'NGUYEN VAN A',
            'template' => 'compact2',
        ]);

        // Tạo đơn hàng VietQR của khách
        $variant = SanPham::with('bienThes')->firstOrFail()->bienThes->firstOrFail();

        $this->actingAs($this->customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $this->actingAs($this->customer)->post('/checkout', [
            'name' => 'Khách QR',
            'phone' => '0901234567',
            'province' => 'Hà Nội',
            'address' => '1 Đường QR',
            'shipping_service' => 'fast',
            'payment_method' => 'vietqr',
        ]);

        $order = DonHang::latest('id')->firstOrFail();

        $this->actingAs($this->customer)
            ->get(route('checkout.payment.qr', $order->code))
            ->assertOk()
            ->assertSee('19001234567')
            ->assertSee('NGUYEN VAN A')
            ->assertSee('Techcombank');
    }

    public function test_settings_fall_back_to_config_when_not_saved(): void
    {
        $this->assertNull(Setting::get('vietqr.bank_id'));

        $this->actingAs($this->admin)
            ->get(route('admin.settings.payment'))
            ->assertOk()
            ->assertSee(config('payment.vietqr.account_no'));
    }

    public function test_validation_requires_bank_and_account(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.payment.update'), [])
            ->assertSessionHasErrors(['bank_id', 'bank_name', 'account_no', 'account_name', 'template']);
    }

    public function test_customer_cannot_change_payment_settings(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.settings.payment'))
            ->assertStatus(403);

        $this->actingAs($this->customer)
            ->post(route('admin.settings.payment.update'), [])
            ->assertStatus(403);
    }
}
