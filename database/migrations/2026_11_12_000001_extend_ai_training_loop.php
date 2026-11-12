<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bổ sung số liệu để chatbot tự học từ phản hồi của khách (không cần LLM)
        Schema::table('ai_training_pairs', function (Blueprint $table) {
            $table->string('prompt_hash', 64)->nullable()->after('prompt');
            $table->unsignedInteger('hits')->default(0)->after('source');
            $table->unsignedInteger('helpful_count')->default(0)->after('hits');
            $table->unsignedInteger('unhelpful_count')->default(0)->after('helpful_count');
            $table->decimal('quality_score', 5, 2)->default(0.50)->after('unhelpful_count');
            $table->boolean('is_active')->default(true)->after('quality_score')->index();
        });

        Schema::table('ai_training_pairs', function (Blueprint $table) {
            $table->unique('prompt_hash');
        });

        // Liên kết tin nhắn của bot với cặp hỏi–đáp để ghi nhận 👍/👎
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('training_pair_id')->nullable()->after('recommended_product_ids')->index();
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('training_pair_id');
        });

        Schema::table('ai_training_pairs', function (Blueprint $table) {
            $table->dropUnique(['prompt_hash']);
        });

        Schema::table('ai_training_pairs', function (Blueprint $table) {
            $table->dropColumn([
                'prompt_hash',
                'hits',
                'helpful_count',
                'unhelpful_count',
                'quality_score',
                'is_active',
            ]);
        });
    }
};
