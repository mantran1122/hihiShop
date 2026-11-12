<?php

namespace Tests\Feature;

use App\Models\AiTrainingPair;
use App\Models\SanPham;
use App\Models\User;
use App\Models\UserAiProfile;
use Database\Seeders\AttributeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotLearningTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_first_question_is_answered_and_saved_to_memory(): void
    {
        $response = $this->postJson('/chat/message', [
            'message' => 'chính sách giao hàng như thế nào?',
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'success', 'learned' => false]);

        $this->assertDatabaseCount('ai_training_pairs', 1);

        $pair = AiTrainingPair::firstOrFail();
        $this->assertSame(0, $pair->hits);
        $this->assertEquals(0.5, $pair->quality_score);
        $this->assertSame('rule_based', $pair->source);
        $this->assertTrue($pair->is_active);
    }

    public function test_repeated_question_is_answered_from_memory_without_regenerating(): void
    {
        $first = $this->postJson('/chat/message', [
            'message' => 'chính sách giao hàng như thế nào?',
        ])->json();

        $second = $this->postJson('/chat/message', [
            'message' => 'chính sách giao hàng như thế nào?',
        ])->json();

        $this->assertFalse($first['learned']);
        $this->assertTrue($second['learned']);
        $this->assertSame($first['reply'], $second['reply']);

        // Không sinh thêm cặp mới, chỉ tăng lượt dùng
        $this->assertDatabaseCount('ai_training_pairs', 1);
        $this->assertSame(1, AiTrainingPair::firstOrFail()->hits);
    }

    public function test_similar_wording_also_hits_memory(): void
    {
        $this->postJson('/chat/message', ['message' => 'chính sách giao hàng và bảo hành']);

        $response = $this->postJson('/chat/message', [
            'message' => 'chính sách giao hàng bảo hành thế nào',
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('learned'));
    }

    public function test_thumbs_up_increases_quality_score(): void
    {
        $response = $this->postJson('/chat/message', ['message' => 'đổi trả hàng thế nào?']);
        $messageId = $response->json('message_id');

        $this->postJson('/chat/feedback', [
            'message_id' => $messageId,
            'feedback' => 'thumbs_up',
        ])->assertOk();

        $pair = AiTrainingPair::firstOrFail();
        $this->assertSame(1, $pair->helpful_count);
        $this->assertSame(0, $pair->unhelpful_count);
        // (1 + 1) / (1 + 0 + 2) = 0.67
        $this->assertEquals(0.67, $pair->quality_score);
        $this->assertTrue($pair->is_active);
    }

    public function test_repeated_thumbs_down_deactivates_pair(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/chat/message', ['message' => 'giao hàng mất bao lâu?']);

            $this->postJson('/chat/feedback', [
                'message_id' => $response->json('message_id'),
                'feedback' => 'thumbs_down',
            ])->assertOk();
        }

        $pair = AiTrainingPair::firstOrFail();
        $this->assertSame(3, $pair->unhelpful_count);
        $this->assertFalse($pair->is_active);
    }

    public function test_cannot_send_feedback_for_another_session(): void
    {
        $response = $this->postJson('/chat/message', ['message' => 'xin chào shop']);
        $messageId = $response->json('message_id');

        $this->withSession(['hihi_chat_session' => 'sess_cua_nguoi_khac'])
            ->postJson('/chat/feedback', [
                'message_id' => $messageId,
                'feedback' => 'thumbs_up',
            ])
            ->assertStatus(403);
    }

    public function test_feedback_requires_valid_payload(): void
    {
        $this->postJson('/chat/feedback', [
            'message_id' => 999999,
            'feedback' => 'like',
        ])->assertStatus(422);
    }

    public function test_profile_updates_from_product_views_and_chat(): void
    {
        $user = User::where('email', 'customer@example.com')->firstOrFail();
        $product = SanPham::with('danhMuc')->firstOrFail();

        $this->actingAs($user)
            ->get(route('product.show', $product->slug))
            ->assertOk();

        $this->actingAs($user)
            ->postJson('/chat/message', ['message' => 'tư vấn giúp mình sản phẩm này'])
            ->assertOk();

        $profile = UserAiProfile::where('user_id', $user->id)->first();

        $this->assertNotNull($profile);
        $this->assertGreaterThanOrEqual(2, $profile->interaction_count);
        $this->assertContains($product->danhMuc->name, $profile->preferred_categories);
        $this->assertNotSame('undecided', $profile->budget_range);
    }
}
