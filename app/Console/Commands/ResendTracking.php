<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Tracking\TrackingManager;
use Illuminate\Console\Command;

class ResendTracking extends Command
{
    protected $signature = 'tracking:resend
        {order_id? : Single order id to resend Purchase for}
        {--from= : Resend orders created from this date (Y-m-d)}
        {--to= : Resend orders created up to this date (Y-m-d)}
        {--status= : Only orders with this status}';

    protected $description = 'Backfill missed Purchase events (respects 7-day platform event_time limits)';

    public function handle(TrackingManager $manager): int
    {
        $query = Order::with(['items.product', 'user'])
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->whereNull('purchase_sent_at');

        if ($this->argument('order_id')) {
            $query->where('id', $this->argument('order_id'));
        }
        if ($this->option('from')) {
            $query->whereDate('created_at', '>=', $this->option('from'));
        }
        if ($this->option('to')) {
            $query->whereDate('created_at', '<=', $this->option('to'));
        }
        if ($this->option('status')) {
            $query->where('status', $this->option('status'));
        }

        $count = 0;
        $skipped = 0;

        foreach ($query->cursor() as $order) {
            // Meta CAPI rejects event_time older than 7 days — skip stale orders
            if ($order->created_at && $order->created_at->lt(now()->subDays(6))) {
                $this->warn("Skipped order #{$order->id}: older than 6 days (platform event_time limit).");
                $skipped++;
                continue;
            }

            $manager->trackPurchase($order);
            $count++;
        }

        $this->info("Queued Purchase for {$count} order(s), skipped {$skipped}.");

        return self::SUCCESS;
    }
}
