<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('addresses', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $t->string('name');
        $t->string('phone',30);
        $t->string('province');  $t->string('district');  $t->string('ward');
        $t->string('address_line');
        $t->boolean('is_default')->default(false);
        $t->timestamps();
      });
  
      Schema::create('orders', function (Blueprint $t) {
        $t->id();
        $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $t->string('code')->unique();
        $t->enum('status', ['pending','paid','processing','shipped','delivered','completed','cancelled'])->default('pending');
        $t->enum('payment_status', ['unpaid','paid','refunded','failed'])->default('unpaid');
        $t->enum('shipping_status', ['unfulfilled','fulfilled','returned','cancelled'])->default('unfulfilled');
        $t->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
        $t->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();
        $t->string('currency',3)->default('VND');
        $t->decimal('subtotal',12,2)->default(0);
        $t->decimal('discount_total',12,2)->default(0);
        $t->decimal('shipping_total',12,2)->default(0);
        $t->decimal('grand_total',12,2)->default(0);
        $t->timestamps();
        $t->index(['user_id','status','created_at']);
      });
  
      Schema::create('order_items', function (Blueprint $t) {
        $t->id();
        $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
        $t->string('sku');
        $t->string('name_snapshot');
        $t->decimal('price',12,2);
        $t->unsignedInteger('qty');
        $t->decimal('subtotal',12,2);
        $t->timestamps();
      });
    }
    public function down(): void {
      Schema::dropIfExists('order_items');
      Schema::dropIfExists('orders');
      Schema::dropIfExists('addresses');
    }
  };
  