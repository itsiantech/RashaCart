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
        Schema::create('tracking_event_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('pixel_settings')->onDelete('cascade');
            $table->string('event_name'); // PageView, ViewContent, Search, AddToCart, InitiateCheckout, AddPaymentInfo, Lead, Purchase, custom
            $table->boolean('is_enabled')->default(true);
            $table->enum('trigger_type', ['frontend', 'order_placed', 'order_status'])->default('frontend');
            $table->string('trigger_status')->nullable(); // e.g. confirmed, delivered
            $table->enum('value_mode', ['full', 'exclude_shipping', 'custom'])->default('full');
            $table->boolean('send_browser')->default(true);
            $table->boolean('send_server')->default(true);
            $table->unsignedInteger('delay_minutes')->nullable();
            $table->timestamps();

            $table->index(['platform_id', 'event_name']);
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_event_rules');
    }
};
