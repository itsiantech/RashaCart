<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Which products demand an advance, and how much.
        if (! Schema::hasTable('pp_product_rules')) {
            Schema::create('pp_product_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
                $table->enum('advance_type', ['percent', 'fixed'])->default('percent');
                $table->decimal('advance_value', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // 2) One money plan per order created through this add-on.
        if (! Schema::hasTable('pp_order_plans')) {
            Schema::create('pp_order_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
                $table->decimal('advance_amount', 10, 2)->default(0);
                $table->decimal('due_amount', 10, 2)->default(0);
                $table->enum('payment_status', [
                    'advance_pending', 'advance_paid', 'paid', 'refund_due', 'refunded',
                ])->default('advance_pending');
                $table->timestamp('expires_at')->nullable();
                $table->boolean('due_override')->default(false);
                $table->timestamps();

                $table->index('payment_status');
            });
        }

        // 3) Every money claim: the customer's advance submission and the
        //    courier's cash balance collection.
        if (! Schema::hasTable('pp_payments')) {
            Schema::create('pp_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->enum('type', ['advance', 'balance'])->default('advance');
                $table->enum('method', ['bkash', 'nagad', 'rocket', 'cash'])->default('bkash');
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('sender_number', 20)->nullable();
                $table->string('trx_id', 30)->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                // NULL trx ids (cash) stay free; claimed ones are unique per method.
                $table->unique(['method', 'trx_id']);
                $table->index(['order_id', 'status']);
            });
        }

        // 4) Who did what, when.
        if (! Schema::hasTable('pp_activities')) {
            Schema::create('pp_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('payment_id')->nullable()->constrained('pp_payments')->nullOnDelete();
                $table->string('action', 40);
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index(['order_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pp_activities');
        Schema::dropIfExists('pp_payments');
        Schema::dropIfExists('pp_order_plans');
        Schema::dropIfExists('pp_product_rules');
    }
};
