<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\SanPham;
use App\Models\User;
use App\Models\Wishlist;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewWishlistTest extends TestCase
{
    use RefreshDatabase;

    protected SanPham $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CategorySeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
        ]);

        $this->product = SanPham::firstOrFail();
    }

    public function test_guest_cannot_post_review(): void
    {
        $response = $this->post(route('review.store', $this->product), ['rating' => 5]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_authenticated_user_can_create_review(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('review.store', $this->product), [
            'rating' => 5,
            'title' => 'Rất đẹp',
            'content' => 'Đóng gói cẩn thận, giao hàng nhanh.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'is_approved' => true,
        ]);

        $page = $this->get(route('product.show', $this->product->slug));
        $page->assertStatus(200);
        $page->assertSee('Rất đẹp');
        $page->assertSee('5.0/5');
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('review.store', $this->product), ['rating' => 6])
            ->assertSessionHasErrors('rating');

        $this->actingAs($user)
            ->post(route('review.store', $this->product), [])
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_updates_existing_review_instead_of_duplicating(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('review.store', $this->product), [
            'rating' => 3,
            'content' => 'Tạm ổn',
        ]);

        $this->actingAs($user)->post(route('review.store', $this->product), [
            'rating' => 4,
            'content' => 'Tốt hơn sau khi dùng thử',
        ]);

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'rating' => 4,
            'content' => 'Tốt hơn sau khi dùng thử',
        ]);
    }

    public function test_user_can_delete_own_review_but_not_someone_elses(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $review = Review::create([
            'product_id' => $this->product->id,
            'user_id' => $owner->id,
            'rating' => 5,
            'is_approved' => true,
        ]);

        $this->actingAs($other)
            ->delete(route('review.destroy', $review))
            ->assertStatus(403);

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);

        $this->actingAs($owner)
            ->delete(route('review.destroy', $review))
            ->assertRedirect();

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_wishlist_toggle_adds_and_removes_product(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('wishlist.toggle', $this->product))
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($user)->post(route('wishlist.toggle', $this->product));

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_wishlist_toggle_returns_json_for_ajax(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('wishlist.toggle', $this->product))
            ->assertOk()
            ->assertJson(['wished' => true, 'count' => 1]);
    }

    public function test_wishlist_page_lists_saved_products(): void
    {
        $user = User::factory()->create();
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($user)->get(route('wishlist.index'));

        $response->assertStatus(200);
        $response->assertSee($this->product->name);
    }

    public function test_guest_cannot_view_wishlist_page(): void
    {
        $this->get(route('wishlist.index'))->assertRedirect(route('login'));
    }

    public function test_product_page_shows_wishlist_state_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($user)
            ->get(route('product.show', $this->product->slug))
            ->assertStatus(200)
            ->assertSee('Đã yêu thích');
    }
}
