<?php

namespace Addons\PartialPayment\Console;

use Addons\PartialPayment\Models\Activity;
use Addons\PartialPayment\Models\OrderPlan;
use Illuminate\Console\Command;

class ExpireAdvanceOrders extends Command
{
    protected $signature = 'partial-payment:expire';

    protected $description = 'Cancel orders whose advance payment window has run out';

    public function handle(): int
    {
        $plans = OrderPlan::query()
            ->where('payment_status', OrderPlan::PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with('order')
            ->get();

        $cancelled = 0;

        foreach ($plans as $plan) {
            $order = $plan->order;

            if ($order === null || $order->status !== 'pending') {
                continue;
            }

            $order->update(['status' => 'cancelled']);

            Activity::log((int) $order->id, null, 'expired', 'Advance window closed');
            $cancelled++;
        }

        $this->line($cancelled.' order(s) cancelled for an unpaid advance.');

        return self::SUCCESS;
    }
}
