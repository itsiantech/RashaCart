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
        Schema::create('pixel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->unique(); // facebook, tiktok, google
            $table->string('pixel_id')->nullable();
            $table->string('access_token')->nullable(); // for Conversions API / Events API (server-side)
            $table->string('test_event_code')->nullable(); // Meta test events
            $table->boolean('is_active')->default(false);
            $table->json('extra')->nullable(); // room for platform-specific config
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pixel_settings');
    }
};
