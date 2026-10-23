<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // New columns only — never alter/drop existing columns.
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
            if (! Schema::hasColumn('users', 'notification_prefs')) {
                $table->json('notification_prefs')->nullable()->after('address');
            }
        });

        // Password reset tokens for phone-based resets (separate from email table).
        if (! Schema::hasTable('customer_password_otps')) {
            Schema::create('customer_password_otps', function (Blueprint $table) {
                $table->id();
                $table->string('phone', 20)->index();
                $table->string('otp', 10);
                $table->timestamp('expires_at');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->timestamps();
                $table->index(['phone', 'otp']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_password_otps');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter(
                ['username', 'phone_verified_at', 'notification_prefs'],
                fn ($c) => Schema::hasColumn('users', $c)
            ));
        });
    }
};
