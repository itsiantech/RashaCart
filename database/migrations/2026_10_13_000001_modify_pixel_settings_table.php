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
        Schema::table('pixel_settings', function (Blueprint $table) {
            // Drop the unique constraint on platform to allow multiple pixels per platform
            $table->dropUnique('pixel_settings_platform_unique');

            // Add new columns
            $table->string('name')->nullable()->after('id');
            $table->boolean('is_test_mode')->default(false)->after('is_active');
            $table->timestamp('test_mode_enabled_at')->nullable()->after('is_test_mode');
            $table->foreignId('store_id')->nullable()->after('test_mode_enabled_at')->constrained('users')->nullOnDelete();
            $table->string('api_version')->default('v19.0')->after('extra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pixel_settings', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn(['name', 'is_test_mode', 'test_mode_enabled_at', 'store_id', 'api_version']);

            // Restore unique constraint
            $table->unique('platform');
        });
    }
};
