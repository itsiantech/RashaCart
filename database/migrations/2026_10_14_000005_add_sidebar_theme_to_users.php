<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'sidebar_theme')) {
            Schema::table('users', function (Blueprint $table) {
                // Per-admin sidebar prefs: {"theme": "light", "font_size": 14}
                $table->json('sidebar_theme')->nullable()->after('remember_token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'sidebar_theme')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('sidebar_theme');
            });
        }
    }
};
