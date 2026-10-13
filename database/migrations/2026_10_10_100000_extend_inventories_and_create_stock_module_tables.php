<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('notes');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->foreignId('supplier_id')->nullable()->after('reference_id')->constrained('suppliers')->nullOnDelete();
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->default(5)->after('stock');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->default(5)->after('stock');
        });

        Schema::create('damaged_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->integer('quantity');
            $table->text('reason');
            $table->enum('action_status', ['pending', 'scrapped', 'returned_to_supplier', 'refurbished'])->default('pending');
            $table->text('action_note')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->boolean('restocked')->default(false);
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->integer('quantity_change');
            $table->text('reason');
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('from_location');
            $table->string('to_location');
            $table->text('note')->nullable();
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $this->backfill();
    }

    private function backfill(): void
    {
        $soldStatuses = [
            'pending', 'Call Not Received', 'confirmed', 'shipped',
            'delivered', 'completed', 'Pending Return', 'Pending Cancelled',
        ];

        // A. Historical sold orders → OUT movement rows (one per order item, idempotent via reference)
        $items = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $soldStatuses)
            ->where('order_items.quantity', '>', 0)
            ->selectRaw('order_items.id as item_id, order_items.order_id, order_items.product_id, order_items.product_variant_id, order_items.quantity, orders.created_at as order_date')
            ->get();

        foreach ($items as $item) {
            $exists = DB::table('inventories')
                ->where('reference_type', 'order_item')
                ->where('reference_id', $item->item_id)
                ->where('type', 'out')
                ->exists();

            if (! $exists) {
                DB::table('inventories')->insert([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'current_stock' => 0,
                    'notes' => 'Order sale (backfill)',
                    'reference_type' => 'order_item',
                    'reference_id' => $item->item_id,
                    'user_id' => null,
                    'created_at' => $item->order_date,
                    'updated_at' => $item->order_date,
                ]);
            }
        }

        // B. Opening rows so ledger result matches current stock columns (no stock jump).
        //    The OUT rows from step A already deduct sold quantities; target = stock column.
        $ledgerSum = function ($productId, $variantId) {
            return (int) DB::table('inventories')
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->selectRaw("COALESCE(SUM(CASE type WHEN 'in' THEN quantity WHEN 'return' THEN quantity WHEN 'adjustment' THEN quantity ELSE -quantity END), 0) AS s")
                ->value('s');
        };

        $trueUp = function ($productId, $variantId, $target, $hasRows, $createdAt) use ($ledgerSum) {
            $current = $ledgerSum($productId, $variantId);
            $delta = $target - $current;

            if ($delta === 0) {
                return;
            }

            $isOpenRow = ! $hasRows;

            DB::table('inventories')->insert([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'type' => $isOpenRow ? 'in' : 'adjustment',
                'quantity' => $delta,
                'current_stock' => $current + $delta,
                'notes' => $isOpenRow ? 'Opening stock (backfill)' : 'Opening balance true-up (backfill)',
                'reference_type' => 'backfill',
                'reference_id' => $productId,
                'user_id' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        };

        $products = DB::table('products')->select('id', 'stock', 'created_at')->get();
        foreach ($products as $product) {
            $hasRows = DB::table('inventories')
                ->where('product_id', $product->id)
                ->whereNull('variant_id')
                ->exists();

            $target = (int) ($product->stock ?? 0);
            $trueUp($product->id, null, $target, $hasRows, $product->created_at);
        }

        $variants = DB::table('product_variants')->select('id', 'product_id', 'stock', 'created_at')->get();
        foreach ($variants as $variant) {
            $hasRows = DB::table('inventories')
                ->where('variant_id', $variant->id)
                ->exists();

            $target = (int) ($variant->stock ?? 0);
            $trueUp($variant->product_id, $variant->id, $target, $hasRows, $variant->created_at);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfers');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('damaged_stocks');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropColumn(['reference_type', 'reference_id', 'supplier_id']);
        });
    }
};
