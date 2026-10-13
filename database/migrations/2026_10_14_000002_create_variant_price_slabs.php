<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('variant_price_slabs')) {
            Schema::create('variant_price_slabs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
                $table->integer('min_qty');
                $table->decimal('price', 10, 2);
                $table->timestamps();
                $table->unique(['product_variant_id', 'min_qty']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_price_slabs');
    }
};
