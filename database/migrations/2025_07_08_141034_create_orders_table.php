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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('delivery_zone');
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->string('payment_method');
            $table->string('pay_amt')->nullable();      
            $table->string('coupon_code')->nullable();    
            $table->decimal('coupon_discount_amount')->nullable();      
            $table->text('shipping_address');
            $table->text('note')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'confiemed', 'shipped', 'delivered', 'cancelled','returned']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
