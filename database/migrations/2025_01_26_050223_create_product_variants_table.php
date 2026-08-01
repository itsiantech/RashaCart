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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');

            $table->string('sku')->unique();
            $table->decimal('variant_price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();

            // Constraint Definition
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            // Index for performance
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
