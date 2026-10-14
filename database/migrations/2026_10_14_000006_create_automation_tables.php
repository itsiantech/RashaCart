<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('automation_modules')) {
            Schema::create('automation_modules', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->boolean('is_enabled')->default(false);
                $table->string('mode', 10)->default('off'); // off|shadow|live
                $table->json('settings')->nullable();
                $table->timestamp('last_enabled_at')->nullable();
                $table->foreignId('enabled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('automation_audit_logs')) {
            Schema::create('automation_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('module_key')->index();
                $table->string('action', 30); // enabled|disabled|mode_changed|settings_changed|master_switch
                $table->json('old_value')->nullable();
                $table->json('new_value')->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }

        if (! Schema::hasTable('automation_activity_logs')) {
            Schema::create('automation_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('module_key')->index();
                $table->string('event');
                $table->string('status', 20)->default('info'); // info|success|failed|skipped
                $table->text('message')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('created_at')->nullable()->index();
            });
        }

        if (! Schema::hasTable('ai_usage_logs')) {
            Schema::create('ai_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->string('module_key')->index();
                $table->integer('tokens_in')->default(0);
                $table->integer('tokens_out')->default(0);
                $table->decimal('cost', 10, 4)->default(0);
                $table->string('status', 20)->default('success');
                $table->timestamp('created_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('automation_activity_logs');
        Schema::dropIfExists('automation_audit_logs');
        Schema::dropIfExists('automation_modules');
    }
};
