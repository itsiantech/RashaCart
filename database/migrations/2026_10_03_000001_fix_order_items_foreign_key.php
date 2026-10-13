<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL-only introspection; skip on sqlite/pgsql (e.g. test runs)
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Check if order_items_order_id_foreign points to users
        $fk = DB::select("
            SELECT REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'order_items'
              AND CONSTRAINT_NAME = 'order_items_order_id_foreign'
        ");

        if (! empty($fk) && strtolower($fk[0]->REFERENCED_TABLE_NAME ?? '') === 'users') {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign('order_items_order_id_foreign');
                $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed to revert back to buggy foreign key
    }
};
