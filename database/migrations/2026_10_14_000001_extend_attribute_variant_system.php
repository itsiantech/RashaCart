<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── attributes: swatch/filter/sort support ──
        Schema::table('attributes', function (Blueprint $table) {
            if (! Schema::hasColumn('attributes', 'type')) {
                $table->string('type')->default('button')->after('slug'); // color_swatch|image_swatch|button|dropdown
            }
            if (! Schema::hasColumn('attributes', 'is_filterable')) {
                $table->boolean('is_filterable')->default(false)->after('type');
            }
            if (! Schema::hasColumn('attributes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_filterable');
            }
            if (! Schema::hasColumn('attributes', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });

        // ── attribute_values: slug + swatch data ──
        Schema::table('attribute_values', function (Blueprint $table) {
            if (! Schema::hasColumn('attribute_values', 'slug')) {
                $table->string('slug')->nullable()->after('value');
            }
            if (! Schema::hasColumn('attribute_values', 'color_hex')) {
                $table->string('color_hex', 7)->nullable()->after('slug');
            }
            if (! Schema::hasColumn('attribute_values', 'swatch_image')) {
                $table->string('swatch_image')->nullable()->after('color_hex');
            }
            if (! Schema::hasColumn('attribute_values', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('swatch_image');
            }
        });

        // ── product_attributes: which attributes a product uses ──
        if (! Schema::hasTable('product_attributes')) {
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['product_id', 'attribute_id']);
            });
        }

        // ── product_variants: pricing/stock/status fields (old columns kept for BC) ──
        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('variant_price');
            }
            if (! Schema::hasColumn('product_variants', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('sale_price');
            }
            if (! Schema::hasColumn('product_variants', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(5)->after('stock');
            }
            if (! Schema::hasColumn('product_variants', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('low_stock_threshold');
            }
            if (! Schema::hasColumn('product_variants', 'barcode')) {
                $table->string('barcode')->nullable()->after('weight');
            }
            if (! Schema::hasColumn('product_variants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('barcode');
            }
            if (! Schema::hasColumn('product_variants', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('is_active');
            }
        });

        // ── product_images: gallery + swatch linking ──
        if (! Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->integer('sort_order')->default(0);
                $table->foreignId('attribute_value_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
                $table->index(['product_id', 'sort_order']);
                $table->index('attribute_value_id');
            });
        }

        // ── order_items: variant snapshot at purchase ──
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'variant_sku')) {
                $table->string('variant_sku')->nullable()->after('product_variant_id');
            }
            if (! Schema::hasColumn('order_items', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('variant_sku');
            }
            if (! Schema::hasColumn('order_items', 'variant_image')) {
                $table->string('variant_image')->nullable()->after('variant_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['variant_sku', 'variant_name', 'variant_image']);
        });
        Schema::dropIfExists('product_images');
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['weight', 'barcode', 'is_active', 'is_default']);
        });
        Schema::dropIfExists('product_attributes');
    }
};
