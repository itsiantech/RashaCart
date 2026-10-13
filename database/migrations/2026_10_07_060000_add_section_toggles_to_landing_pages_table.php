<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->boolean('show_hero')->default(true)->after('status');
            $table->boolean('show_countdown')->default(true)->after('show_hero');
            $table->boolean('show_price')->default(true)->after('show_countdown');
            $table->boolean('show_benefits')->default(true)->after('show_price');
            $table->boolean('show_trust')->default(true)->after('show_benefits');
            $table->boolean('show_contact')->default(true)->after('show_trust');
            $table->boolean('show_checkout')->default(true)->after('show_contact');
            $table->boolean('show_footer')->default(true)->after('show_checkout');
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'show_hero',
                'show_countdown',
                'show_price',
                'show_benefits',
                'show_trust',
                'show_contact',
                'show_checkout',
                'show_footer',
            ]);
        });
    }
};
