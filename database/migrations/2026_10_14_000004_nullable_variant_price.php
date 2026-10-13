<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Allow "auto price": empty variant_price (NULL) falls back to product price.
 * Existing 0 values keep their meaning (explicit ৳0) — strict, no data change.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE product_variants MODIFY COLUMN variant_price DECIMAL(10,2) NULL DEFAULT NULL');
            return;
        }

        // SQLite (tests): rebuild table with nullable variant_price
        Schema::create('product_variants_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('sku')->unique();
            $table->decimal('variant_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('product_id');
            $table->index(['product_id', 'is_active']);
        });

        DB::statement('INSERT INTO product_variants_new (id, product_id, sku, variant_price, sale_price, cost_price, stock, low_stock_threshold, weight, barcode, is_active, is_default, image, created_at, updated_at)
            SELECT id, product_id, sku, variant_price, sale_price, cost_price, stock, low_stock_threshold, weight, barcode, is_active, is_default, image, created_at, updated_at FROM product_variants');

        Schema::drop('product_variants');
        Schema::rename('product_variants_new', 'product_variants');
    }

    public function down(): void
    {
        DB::table('product_variants')->whereNull('variant_price')->update(['variant_price' => 0]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE product_variants MODIFY COLUMN variant_price DECIMAL(10,2) NOT NULL DEFAULT 0.00');
        }
        // SQLite down: NULLs already converted to 0 above; constraint stays nullable (harmless).
    }
};
