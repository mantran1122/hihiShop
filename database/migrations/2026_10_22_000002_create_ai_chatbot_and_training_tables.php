<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. User AI Persona Profile (Real-time customer context for chatbot)
        Schema::create('user_ai_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('preferred_categories')->nullable()->comment('Categories viewed/purchased most');
            $table->json('preferred_brands')->nullable()->comment('Brands customer prefers');
            $table->string('budget_range', 30)->default('undecided')->comment('budget, mid-range, luxury');
            $table->string('style_persona', 100)->nullable()->comment('Fashion persona detected by AI');
            $table->unsignedInteger('interaction_count')->default(0);
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();
        });

        // 2. User Activities (Real-time tracking feed for AI RAG context)
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 100)->index();
            $table->string('event_type', 50)->index(); // 'page_view', 'product_view', 'search', 'add_to_cart', 'order_created'
            $table->json('payload')->comment('Context details: product_id, keywords, cart contents');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'event_type', 'created_at']);
        });

        // 3. Chatbot Sessions
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_title', 200)->nullable();
            $table->tinyInteger('satisfaction_score')->nullable()->comment('User rating 1-5');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 4. Chat Messages History
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64);
            $table->enum('sender', ['user', 'bot', 'system'])->default('user');
            $table->text('content');
            $table->json('recommended_product_ids')->nullable();
            $table->enum('feedback', ['none', 'thumbs_up', 'thumbs_down'])->default('none');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('session_id')->references('id')->on('chat_sessions')->cascadeOnDelete();
            $table->index(['session_id', 'created_at']);
        });

        // 5. AI Training Pairs (Dataset for Transfer Learning / LoRA Fine-Tuning)
        Schema::create('ai_training_pairs', function (Blueprint $table) {
            $table->id();
            $table->text('prompt');
            $table->text('ideal_response');
            $table->json('context_json')->nullable();
            $table->string('source', 50)->default('high_rated_session')->comment('successful_sale, human_corrected, high_rated_session');
            $table->boolean('is_exported')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_training_pairs');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('user_activities');
        Schema::dropIfExists('user_ai_profiles');
    }
};

