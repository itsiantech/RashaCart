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
        Schema::table('orders', function (Blueprint $table) {
            // Click IDs
            $table->string('fbp')->nullable()->after('payment_status');
            $table->string('fbc')->nullable()->after('fbp');
            $table->string('fbclid')->nullable()->after('fbc');
            $table->string('gclid')->nullable()->after('fbclid');
            $table->string('ttclid')->nullable()->after('gclid');
            $table->string('ttp')->nullable()->after('ttclid');

            // GA4
            $table->string('ga_client_id')->nullable()->after('ttp');
            $table->string('ga_session_id')->nullable()->after('ga_client_id');

            // UTM parameters
            $table->string('utm_source')->nullable()->after('ga_session_id');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
            $table->string('utm_content')->nullable()->after('utm_campaign');
            $table->string('utm_term')->nullable()->after('utm_content');

            // Client info
            $table->string('ip_address')->nullable()->after('utm_term');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('event_source_url')->nullable()->after('user_agent');

            // Tracking event
            $table->string('tracking_event_id')->nullable()->after('event_source_url');
            $table->timestamp('purchase_sent_at')->nullable()->after('tracking_event_id');

            // Indexes
            $table->index('fbclid');
            $table->index('gclid');
            $table->index('ttclid');
            $table->index('tracking_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['fbclid', 'gclid', 'ttclid', 'tracking_event_id']);
            $table->dropColumn([
                'fbp', 'fbc', 'fbclid', 'gclid', 'ttclid', 'ttp',
                'ga_client_id', 'ga_session_id',
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
                'ip_address', 'user_agent', 'event_source_url',
                'tracking_event_id', 'purchase_sent_at'
            ]);
        });
    }
};
