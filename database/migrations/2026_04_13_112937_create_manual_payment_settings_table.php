<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_payment_settings', function (Blueprint $table) {
            $table->id();

            // bKash
            $table->boolean('bkash_enabled')->default(false);
            $table->string('bkash_number', 15)->nullable();
            $table->enum('bkash_account_type', ['personal', 'merchant'])->default('personal');

            // Nagad
            $table->boolean('nagad_enabled')->default(false);
            $table->string('nagad_number', 15)->nullable();
            $table->enum('nagad_account_type', ['personal', 'merchant'])->default('personal');

            // Rocket
            $table->boolean('rocket_enabled')->default(false);
            $table->string('rocket_number', 15)->nullable();

            // Bank Account
            $table->boolean('bank_enabled')->default(false);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            $table->string('bank_account_number', 30)->nullable();
            $table->string('bank_routing_number', 20)->nullable();
            $table->string('bank_branch', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_payment_settings');
    }
};
