<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Database\Seeders\CouponSeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVoucherTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CouponSeeder::class,
            DemoUserAndRolesSeeder::class,
            RolesSeeder::class,
        ]);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
    }

    public function test_admin_can_open_voucher_list_and_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.vouchers.index'))
            ->assertOk()
            ->assertSee('Danh Sách Voucher')
            ->assertSee('FREESHIP15');

        $this->actingAs($this->admin)
            ->get(route('admin.vouchers.create'))
            ->assertOk()
            ->assertSee('Thêm Voucher Mới');
    }

    public function test_admin_can_create_fixed_voucher(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.vouchers.store'), [
            'code' => 'test50',
            'title' => 'Giảm 50K cho đơn 300K',
            'description' => 'Áp dụng cho mọi sản phẩm',
            'category' => 'order_discount',
            'type' => 'fixed',
            'value' => 50000,
            'min_order' => 300000,
            'usage_limit' => 100,
            'per_user_limit' => 2,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.vouchers.index'));

        $voucher = Coupon::where('code', 'TEST50')->first();
        $this->assertNotNull($voucher);
        $this->assertSame('order_discount', $voucher->category);
        $this->assertSame('fixed', $voucher->type);
        $this->assertEquals(50000, (float) $voucher->value);
        $this->assertSame(2, (int) $voucher->per_user_limit);
        $this->assertTrue($voucher->is_active);

        // Voucher mới dùng được ở bước thanh toán
        [$valid] = $voucher->isValidFor(350000);
        $this->assertTrue($valid);
    }

    public function test_duplicate_code_is_rejected(): void
    {
        $existing = Coupon::firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.vouchers.store'), [
            'code' => $existing->code,
            'title' => 'Trùng mã',
            'category' => 'order_discount',
            'type' => 'fixed',
            'value' => 10000,
        ])->assertSessionHasErrors('code');
    }

    public function test_percent_value_must_not_exceed_100(): void
    {
        $this->actingAs($this->admin)->post(route('admin.vouchers.store'), [
            'code' => 'SALE150',
            'title' => 'Sai phần trăm',
            'category' => 'order_discount',
            'type' => 'percent',
            'value' => 150,
        ])->assertSessionHasErrors('value');
    }

    public function test_end_date_must_not_be_before_start_date(): void
    {
        $this->actingAs($this->admin)->post(route('admin.vouchers.store'), [
            'code' => 'TIME01',
            'title' => 'Sai thời gian',
            'category' => 'free_shipping',
            'type' => 'fixed',
            'value' => 15000,
            'starts_at' => '2026-12-10T08:00',
            'ends_at' => '2026-12-01T08:00',
        ])->assertSessionHasErrors('ends_at');
    }

    public function test_admin_can_update_and_toggle_voucher(): void
    {
        $voucher = Coupon::where('code', 'HIHI10')->firstOrFail();

        $this->actingAs($this->admin)->put(route('admin.vouchers.update', $voucher->id), [
            'code' => $voucher->code,
            'title' => 'Giảm 10% (đã sửa)',
            'category' => $voucher->category,
            'type' => $voucher->type,
            'value' => $voucher->value,
            'min_order' => $voucher->min_order,
            'is_active' => 1,
        ])->assertRedirect(route('admin.vouchers.index'));

        $voucher->refresh();
        $this->assertSame('Giảm 10% (đã sửa)', $voucher->title);

        $this->actingAs($this->admin)
            ->post(route('admin.vouchers.toggle', $voucher->id))
            ->assertRedirect();

        $this->assertFalse($voucher->fresh()->is_active);
    }

    public function test_customer_cannot_manage_vouchers(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.vouchers.index'))
            ->assertStatus(403);

        $this->actingAs($this->customer)
            ->post(route('admin.vouchers.store'), [])
            ->assertStatus(403);
    }
}
