<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
      Schema::create('attributes', function (Blueprint $t) {
        $t->id();
        $t->string('code')->unique();           // vd: concentration, size_eu
        $t->string('name');                     // Hiển thị
        $t->enum('type', ['text','number','boolean','select']);
        $t->timestamps();
      });
  
      Schema::create('attribute_values', function (Blueprint $t) {
        $t->id();
        $t->foreignId('attribute_id')->constrained()->cascadeOnDelete();
        $t->string('value');                    // option cho select
        $t->timestamps();
        $t->unique(['attribute_id','value']);
      });
  
      // map attribute cho category để render filter
      Schema::create('attribute_category', function (Blueprint $t) {
        $t->id();
        $t->foreignId('attribute_id')->constrained()->cascadeOnDelete();
        $t->foreignId('category_id')->constrained()->cascadeOnDelete();
        $t->unique(['attribute_id','category_id']);
        $t->timestamps();
    });
    
  
      // giá trị gắn cho product (EAV)
      Schema::create('product_attribute_values', function (Blueprint $t) {
        $t->id();
        $t->foreignId('product_id')->constrained()->cascadeOnDelete();
        $t->foreignId('attribute_id')->constrained()->cascadeOnDelete();
        $t->foreignId('attribute_value_id')->nullable()->constrained('attribute_values')->nullOnDelete();
        $t->string('value_text')->nullable();
        $t->decimal('value_number',12,3)->nullable();
        $t->boolean('value_boolean')->nullable();
        $t->timestamps();
        $t->index(['product_id','attribute_id']);
      });
    }
    public function down(): void {
      Schema::dropIfExists('product_attribute_values');
      Schema::dropIfExists('attribute_category');
      Schema::dropIfExists('attribute_values');
      Schema::dropIfExists('attributes');
    }
  };
  