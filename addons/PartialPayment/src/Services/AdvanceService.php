<?php

namespace Addons\PartialPayment\Services;

use Addons\PartialPayment\Models\Activity;
use Addons\PartialPayment\Models\OrderPlan;
use Addons\PartialPayment\Models\Payment;
use Addons\PartialPayment\Support\Options;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;

class AdvanceService
{
    /**
     * Called from the order.created hook, inside the order's own transaction.
     * Amounts always come from the server-side calculator — a browser can
     * claim whatever it likes, it will not be believed.
     */
    public function registerOrder(Order $order, array $addonInput = []): void
    {
        if (! $this->tablesReady()) {
            return;
        }

        $plan = PlanCalculator::fromOrder($order);

        if ($plan === null) {
            return;
        }

        $hours = Options::expiryHours();

        $record = OrderPlan::updateOrCreate(
            ['order_id' => $order->id],
            [
                'advance_amount' => $plan['advance'],
                'due_amount' => $plan['due'],
                'payment_status' => OrderPlan::PENDING,
                'expires_at' => $hours > 0 ? now()->addHours($hours) : null,
            ]
        );

        Activity::log($order->id, null, 'plan_created', 'Advance '.$this->money($plan['advance']).', due '.$this->money($plan['due']));

        $claimed = trim((string) ($addonInput['trx_id'] ?? ''));

        if ($claimed === '') {
            return;
        }

        $method = array_key_exists($addonInput['method'] ?? '', Options::METHODS)
            ? (string) $addonInput['method']
            : 'bkash';

        // A replayed or double-submitted TrxID never overwrites a real claim.
        if (Payment::where('method', $method)->where('trx_id', $claimed)->exists()) {
            Activity::log($order->id, null, 'duplicate_trx', $method.' / '.$claimed);

            return;
        }

        Payment::create([
            'order_id' => $order->id,
            'type' => 'advance',
            'method' => $method,
            'amount' => $plan['advance'],
            'sender_number' => $this->digits((string) ($addonInput['sender_number'] ?? '')),
            'trx_id' => $claimed,
            'status' => 'pending',
        ]);

        Activity::log($order->id, null, 'advance_submitted', $method.' / '.$claimed);
    }

    public function verify(Payment $payment): Payment
    {
        $payment->forceFill([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ])->save();

        $plan = OrderPlan::forOrder((int) $payment->order_id);

        if ($plan && $payment->type === 'advance') {
            $plan->forceFill(['payment_status' => OrderPlan::ADVANCE_PAID])->save();
        }

        Activity::log((int) $payment->order_id, (int) $payment->id, 'advance_verified', $payment->trx_id);

        return $payment;
    }

    public function reject(Payment $payment, string $note): Payment
    {
        $payment->forceFill([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'note' => $note,
        ])->save();

        $plan = OrderPlan::forOrder((int) $payment->order_id);

        if ($plan && $payment->type === 'advance') {
            $plan->forceFill(['payment_status' => OrderPlan::PENDING])->save();
        }

        Activity::log((int) $payment->order_id, (int) $payment->id, 'advance_rejected', $note);

        return $payment;
    }

    /** Courier collected the remaining cash: record it and close the plan. */
    public function collectBalance(Order $order, float $amount, ?string $note = null): Payment
    {
        $plan = OrderPlan::forOrder((int) $order->id);

        $payment = Payment::create([
            'order_id' => $order->id,
            'type' => 'balance',
            'method' => 'cash',
            'amount' => $amount,
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'note' => $note,
        ]);

        if ($plan) {
            $plan->forceFill([
                'payment_status' => OrderPlan::PAID,
                'due_amount' => 0,
                'due_override' => false,
            ])->save();
        }

        Activity::log((int) $order->id, (int) $payment->id, 'balance_collected', $this->money($amount));

        return $payment;
    }

    public function markRefundDue(Order $order): void
    {
        $plan = OrderPlan::forOrder((int) $order->id);

        if (! $plan || ! $plan->advanceVerified() || $plan->payment_status === OrderPlan::PAID) {
            return;
        }

        $plan->forceFill(['payment_status' => OrderPlan::REFUND_DUE])->save();
        Activity::log((int) $order->id, null, 'refund_due', 'Cancelled after advance was verified');
    }

    public function markRefunded(OrderPlan $plan): void
    {
        $plan->forceFill(['payment_status' => OrderPlan::REFUNDED, 'due_amount' => 0])->save();
        Activity::log((int) $plan->order_id, null, 'refunded');
    }

    public function releaseDue(OrderPlan $plan, string $note): void
    {
        $plan->forceFill(['due_override' => true, 'due_amount' => 0])->save();
        Activity::log((int) $plan->order_id, null, 'due_released', $note);
    }

    /** True when the tables this add-on owns actually exist right now. */
    protected ?bool $tablesReady = null;

    public function tablesReady(): bool
    {
        return $this->tablesReady ??= Schema::hasTable('pp_order_plans') && Schema::hasTable('pp_payments');
    }

    private function money($value): string
    {
        return setting('currency').' '.number_format((float) $value, 2);
    }

    private function digits(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return $digits === '' ? '' : substr($digits, -11);
    }
}
