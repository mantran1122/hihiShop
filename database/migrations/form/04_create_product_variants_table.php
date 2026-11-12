<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('product_variants', function (Blueprint $t) {
        $t->id();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->string('sku')->unique();
        $t->decimal('price', 12, 2);
        $t->decimal('compare_price', 12, 2)->nullable();
        $t->decimal('cost', 12, 2)->nullable();
        $t->integer('stock_qty')->default(0);
        $t->boolean('is_default')->default(false);
        $t->decimal('weight', 8, 3)->nullable(); // kg
        $t->timestamps();
        $t->index(['product_id','price']);
      });
    }
    public function down(): void { Schema::dropIfExists('product_variants'); }
  };
  