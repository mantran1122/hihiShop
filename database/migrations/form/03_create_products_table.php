<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('products', function (Blueprint $t) {
        $t->id();
        $t->foreignId('category_id')->constrained()->cascadeOnDelete();
        $t->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
        $t->string('name');
        $t->string('slug')->unique();
        $t->text('short_desc')->nullable();
        $t->longText('description')->nullable();
        $t->boolean('is_active')->default(true);
        $t->unsignedInteger('views')->default(0);
        $t->string('seo_title')->nullable();
        $t->string('seo_description', 300)->nullable();
        $t->timestamps();
        $t->index(['category_id','brand_id','is_active']);
      });
    }
    public function down(): void { Schema::dropIfExists('products'); }
  };
  