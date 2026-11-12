<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Review;
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

class AdminReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    private Review $review;

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

        $this->review = Review::create([
            'product_id' => SanPham::firstOrFail()->id,
            'user_id' => $this->customer->id,
            'rating' => 4,
            'title' => 'Đánh giá chờ duyệt',
            'content' => 'Sản phẩm ổn, giao hàng hơi chậm.',
            'is_approved' => true,
        ]);
    }

    public function test_admin_can_view_review_list(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertSee('Danh Sách Đánh Giá')
            ->assertSee('Đánh giá chờ duyệt');
    }

    public function test_admin_can_hide_review_and_it_disappears_from_product_page(): void
    {
        $product = $this->review->sanPham;

        $this->actingAs($this->admin)
            ->post(route('admin.reviews.toggle', $this->review->id))
            ->assertRedirect();

        $this->assertFalse($this->review->fresh()->is_approved);

        $this->get(route('product.show', $product->slug))
            ->assertOk()
            ->assertDontSee('Đánh giá chờ duyệt');
    }

    public function test_admin_can_delete_review(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('admin.reviews.destroy', $this->review->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('reviews', ['id' => $this->review->id]);
    }

    public function test_customer_cannot_moderate_reviews(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.reviews.index'))
            ->assertStatus(403);

        $this->actingAs($this->customer)
            ->post(route('admin.reviews.toggle', $this->review->id))
            ->assertStatus(403);
    }

    public function test_voucher_per_user_limit_blocks_second_use(): void
    {
        $coupon = Coupon::create([
            'code' => 'ONCE1',
            'title' => 'Chỉ dùng 1 lần',
            'category' => 'order_discount',
            'type' => 'fixed',
            'value' => 20000,
            'min_order' => 0,
            'usage_limit' => 1000,
            'per_user_limit' => 1,
            'is_active' => true,
        ]);

        $variant = SanPham::with('bienThes')->firstOrFail()->bienThes->firstOrFail();

        $this->actingAs($this->customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $this->actingAs($this->customer)->post('/checkout', [
            'name' => 'Khách Dùng Mã',
            'phone' => '0900000001',
            'province' => 'Hà Nội',
            'address' => '1 Đường Thử',
            'shipping_service' => 'fast',
            'payment_method' => 'cod',
            'coupon_code' => $coupon->code,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->customer->id,
            'coupon_code' => 'ONCE1',
        ]);

        // Lần 2: tính lại giỏ hàng với cùng mã → báo đã dùng
        $this->actingAs($this->customer)->post('/cart/add', [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $response = $this->actingAs($this->customer)->postJson('/checkout/calculate', [
            'province' => 'Hà Nội',
            'shipping_service' => 'fast',
            'coupon_code' => $coupon->code,
        ]);

        $response->assertOk();
        $this->assertFalse($response->json('order_coupon_valid'));
        $this->assertStringContainsString('đã dùng', (string) $response->json('order_coupon_message'));

        // Khách khác vẫn dùng được
        $other = User::factory()->create();
        [$valid] = $coupon->isValidFor(500000, 0, $other);
        $this->assertTrue($valid);
    }
}
