<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([DemoUserAndRolesSeeder::class, RolesSeeder::class]);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
    }

    public function test_header_shows_default_announcement_when_not_configured(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Miễn phí giao hàng COD toàn quốc cho tất cả đơn hàng hôm nay');
    }

    public function test_admin_can_open_site_settings(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.site'))
            ->assertOk()
            ->assertSee('Cài Đặt Hiển Thị');
    }

    public function test_admin_can_change_announcement_text(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.site.update'), [
            'announcement_enabled' => 1,
            'announcement_text' => 'Freeship toàn quốc cho đơn từ 250K',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame('Freeship toàn quốc cho đơn từ 250K', Setting::get('announcement.text'));

        // Khách (chưa đăng nhập) thấy nội dung mới
        $this->get('/')
            ->assertOk()
            ->assertSee('Freeship toàn quốc cho đơn từ 250K')
            ->assertDontSee('Miễn phí giao hàng COD toàn quốc cho tất cả đơn hàng hôm nay');
    }

    public function test_admin_can_hide_announcement_bar(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.site.update'), [
            'announcement_enabled' => 0,
            'announcement_text' => 'Nội dung vẫn lưu nhưng bị ẩn',
        ]);

        $this->assertSame('0', Setting::get('announcement.enabled'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Nội dung vẫn lưu nhưng bị ẩn');
    }

    public function test_announcement_text_is_limited(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.site.update'), [
            'announcement_enabled' => 1,
            'announcement_text' => str_repeat('x', 201),
        ])->assertSessionHasErrors('announcement_text');
    }

    public function test_customer_cannot_change_site_settings(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.settings.site'))
            ->assertStatus(403);

        $this->actingAs($this->customer)
            ->post(route('admin.settings.site.update'), ['announcement_text' => 'hack'])
            ->assertStatus(403);
    }
}
