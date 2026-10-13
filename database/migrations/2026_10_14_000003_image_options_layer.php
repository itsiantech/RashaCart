<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── products: two independent layers ──
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'has_attributes')) {
                $table->boolean('has_attributes')->default(true)->after('has_variant');
            }
            if (! Schema::hasColumn('products', 'has_image_options')) {
                $table->boolean('has_image_options')->default(false)->after('has_attributes');
            }
            if (! Schema::hasColumn('products', 'require_image_option')) {
                $table->boolean('require_image_option')->default(true)->after('has_image_options');
            }
        });

        // Backfill: products that already have variants get has_attributes = true
        DB::table('products')
            ->whereIn('id', DB::table('product_variants')->select('product_id')->distinct())
            ->update(['has_attributes' => true]);

        // ── product_attributes: per-attribute required flag ──
        if (Schema::hasTable('product_attributes') && ! Schema::hasColumn('product_attributes', 'is_required')) {
            Schema::table('product_attributes', function (Blueprint $table) {
                $table->boolean('is_required')->default(true)->after('attribute_id');
            });
        }

        // ── image_options: independent gallery/selection layer ──
        if (! Schema::hasTable('image_options')) {
            Schema::create('image_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('label');
                $table->string('image');
                $table->decimal('price', 10, 2)->nullable();
                $table->decimal('sale_price', 10, 2)->nullable();
                $table->string('sku')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->index(['product_id', 'sort_order']);
            });
        }

        // ── image_option_stock_movements: separate ledger (inventories untouched) ──
        if (! Schema::hasTable('image_option_stock_movements')) {
            Schema::create('image_option_stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('image_option_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('type', ['in', 'out', 'adjustment', 'return']);
                $table->integer('quantity');
                $table->text('notes')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->timestamps();
                $table->index(['image_option_id', 'created_at']);
            });
        }

        // ── order_items: option snapshot ──
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'image_option_id')) {
                $table->foreignId('image_option_id')->nullable()->after('product_variant_id')
                    ->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'option_label')) {
                $table->string('option_label')->nullable()->after('variant_image');
            }
            if (! Schema::hasColumn('order_items', 'option_image')) {
                $table->string('option_image')->nullable()->after('option_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['image_option_id']);
            $table->dropColumn(['image_option_id', 'option_label', 'option_image']);
        });
        Schema::dropIfExists('image_option_stock_movements');
        Schema::dropIfExists('image_options');
        if (Schema::hasColumn('product_attributes', 'is_required')) {
            Schema::table('product_attributes', function (Blueprint $table) {
                $table->dropColumn('is_required');
            });
        }
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_attributes', 'has_image_options', 'require_image_option']);
        });
    }
};
