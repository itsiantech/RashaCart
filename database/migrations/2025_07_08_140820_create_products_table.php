<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // for multivendor
            $table->foreignId('category_id')->constrained()->onDelete('cascade')->index();           
            $table->string('name')->index();
            $table->string('sku')->unique();
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('buying_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();            
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->integer('stock')->nullable();            
            $table->string('thumbnail')->nullable();
            $table->json('gallery_image')->nullable();
            $table->string('video')->nullable();
            $table->boolean('has_variant')->default(false);
            $table->boolean('is_featured')->default(true)->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
