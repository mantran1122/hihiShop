<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('payments', function (Blueprint $t) {
        $t->id();
        $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
        $t->string('provider');      // cod, vnpay, momo...
        $t->string('transaction_id')->nullable();
        $t->enum('status', ['pending','paid','failed','refunded'])->default('pending');
        $t->decimal('amount',12,2);
        $t->json('payload')->nullable();
        $t->timestamps();
      });
  
      Schema::create('shipments', function (Blueprint $t) {
        $t->id();
        $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
        $t->string('carrier')->nullable();  // ghn, ghtk...
        $t->string('service')->nullable();
        $t->string('tracking_code')->nullable();
        $t->enum('status',['pending','created','shipped','delivered','returned','cancelled'])->default('pending');
        $t->decimal('fee',12,2)->default(0);
        $t->string('label_url')->nullable();
        $t->timestamps();
      });
    }
    public function down(): void {
      Schema::dropIfExists('shipments');
      Schema::dropIfExists('payments');
    }
  };
  