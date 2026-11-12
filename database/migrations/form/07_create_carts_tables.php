<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('carts', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $t->string('session_id')->nullable();
        $t->string('currency',3)->default('VND');
        $t->decimal('subtotal',12,2)->default(0);
        $t->decimal('discount_total',12,2)->default(0);
        $t->decimal('shipping_total',12,2)->default(0);
        $t->decimal('grand_total',12,2)->default(0);
        $t->timestamps();
        $t->index(['user_id','session_id']);
      });
  
      Schema::create('cart_items', function (Blueprint $t) {
        $t->id();
        $t->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
        $t->unsignedInteger('qty');
        $t->decimal('unit_price',12,2);
        $t->decimal('line_total',12,2);
        $t->timestamps();
      });
    }
    public function down(): void {
      Schema::dropIfExists('cart_items');
      Schema::dropIfExists('carts');
    }
  };
  