<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // null = regular checkout, cod/whatsapp = popup order channel
            $table->string('channel')->nullable()->after('payment_status');
            $table->string('email')->nullable()->after('phone');

            $table->index('channel');
            $table->index('status');
            $table->index('phone');
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Snapshot so popup orders survive product renames/deletes
            $table->string('product_name')->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['channel']);
            $table->dropIndex(['status']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['channel', 'email']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });
    }
};
