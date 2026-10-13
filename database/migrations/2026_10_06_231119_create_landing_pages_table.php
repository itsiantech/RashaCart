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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('product_ids')->nullable();
            $table->string('main_title')->nullable();
            $table->string('hero_image')->nullable();
            $table->dateTime('countdown_end_date')->nullable();
            $table->decimal('regular_price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('benefits_title')->nullable();
            $table->json('benefits_list')->nullable();
            $table->string('trust_title')->nullable();
            $table->json('trust_list')->nullable();
            $table->string('secondary_image')->nullable();
            $table->string('contact_number')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
