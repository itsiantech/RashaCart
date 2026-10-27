<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('category_product')) {
            Schema::create('category_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['product_id', 'category_id']);
            });
        }

        // Backfill: every product's primary category becomes its first pivot row.
        // Only valid category ids are copied, otherwise the FK insert would fail.
        DB::table('products')
            ->whereNotNull('category_id')
            ->whereIn('category_id', fn ($query) => $query->select('id')->from('categories'))
            ->select('id', 'category_id')
            ->chunkById(500, function ($products) {
                $now = now();
                $rows = $products->map(fn ($product) => [
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                DB::table('category_product')->insertOrIgnore($rows);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
