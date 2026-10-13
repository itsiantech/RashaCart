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
        Schema::create('tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('pixel_settings')->onDelete('cascade');
            $table->string('event_name');
            $table->string('event_id')->index(); // For deduplication
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->json('payload')->nullable(); // PII-masked
            $table->json('response')->nullable();
            $table->unsignedInteger('http_status')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'skipped', 'duplicate'])->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Indexes for filtering
            $table->index(['platform_id', 'event_name']);
            $table->index(['event_id', 'platform_id'], 'unique_event_per_platform'); // Deduplication
            $table->index('order_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_logs');
    }
};
