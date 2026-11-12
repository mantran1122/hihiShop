<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('product_images', function (Blueprint $t) {
        $t->id();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
        $t->string('path');
        $t->boolean('is_cover')->default(false);
        $t->unsignedSmallInteger('sort')->default(0);
        $t->timestamps();
      });
    }
    public function down(): void { Schema::dropIfExists('product_images'); }
  };
  