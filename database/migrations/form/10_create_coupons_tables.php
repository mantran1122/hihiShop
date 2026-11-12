<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('coupons', function (Blueprint $t) {
        $t->id();
        $t->string('code')->unique();
        $t->enum('type',['fixed','percent']);
        $t->decimal('value',12,2);
        $t->decimal('min_order',12,2)->default(0);
        $t->decimal('max_discount',12,2)->nullable();
        $t->unsignedInteger('usage_limit')->nullable();
        $t->unsignedInteger('used')->default(0);
        $t->timestamp('starts_at')->nullable();
        $t->timestamp('ends_at')->nullable();
        $t->boolean('is_active')->default(true);
        $t->timestamps();
      });
    }
    public function down(): void { Schema::dropIfExists('coupons'); }
  };
  