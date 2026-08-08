<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('source')->default('online')->after('status'); // 'online' | 'pos'
            $table->string('customer_name')->nullable()->after('source');
            $table->unsignedBigInteger('created_by')->nullable()->after('customer_name');
            $table->decimal('cash_received', 10, 2)->nullable()->after('created_by');
            $table->decimal('change_amount', 10, 2)->nullable()->after('cash_received');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['source', 'customer_name', 'created_by', 'cash_received', 'change_amount']);
        });
    }
};