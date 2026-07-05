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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(false)->after('status');
            $table->timestamp('preorder_start_date')->nullable()->after('is_preorder');
            $table->timestamp('preorder_end_date')->nullable()->after('preorder_start_date');
            $table->timestamp('expected_release_date')->nullable()->after('preorder_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_preorder', 'preorder_start_date', 'preorder_end_date', 'expected_release_date']);
        });
    }
};
