<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('reviews', function (Blueprint $t) {
        $t->id();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $t->unsignedTinyInteger('rating'); // 1..5
        $t->string('title')->nullable();
        $t->text('content')->nullable();
        $t->json('images')->nullable();
        $t->boolean('is_approved')->default(false);
        $t->timestamps();
        $t->index(['product_id','is_approved']);
      });
  
      Schema::create('wishlists', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->constrained()->cascadeOnDelete();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->unique(['user_id','product_id']);
        $t->timestamps();
      });
    }
    public function down(): void {
      Schema::dropIfExists('wishlists');
      Schema::dropIfExists('reviews');
    }
  };
  