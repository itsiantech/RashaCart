<?php

namespace Addons\PartialPayment\Livewire\Admin;

use Addons\PartialPayment\Models\Activity;
use Addons\PartialPayment\Models\OrderPlan;
use Addons\PartialPayment\Models\Payment;
use Addons\PartialPayment\Services\AdvanceService;
use App\Models\Order;
use Livewire\Component;

class PaymentsPanel extends Component
{
    public int $orderId;

    public ?int $rejecting = null;

    public string $rejectNote = '';

    public string $releaseNote = '';

    public bool $showRelease = false;

    public function mount($orderId): void
    {
        $this->orderId = (int) $orderId;
    }

    public function verify($paymentId): void
    {
        $payment = $this->payment((int) $paymentId);

        if ($payment === null || $payment->status !== 'pending') {
            return;
        }

        app(AdvanceService::class)->verify($payment);

        $this->dispatch('success', message: 'অ্যাডভান্স ভেরিফাই হয়েছে — বাকি টাকা ক্যাশ অন ডেলিভারি।');
    }

    public function startReject($paymentId): void
    {
        $this->rejecting = (int) $paymentId;
        $this->rejectNote = '';
    }

    public function cancelReject(): void
    {
        $this->rejecting = null;
        $this->rejectNote = '';
    }

    public function confirmReject(): void
    {
        if (! $this->rejecting) {
            return;
        }

        $this->validate([
            'rejectNote' => 'required|string|min:3|max:500',
        ], [], [
            'rejectNote' => 'rejection note',
        ]);

        $payment = $this->payment((int) $this->rejecting);

        if ($payment === null) {
            return;
        }

        app(AdvanceService::class)->reject($payment, trim($this->rejectNote));

        $this->cancelReject();
        $this->dispatch('success', message: 'অ্যাডভান্স বাতিল করা হয়েছে। গ্রাহককে নতুন TrxID দিতে বলুন।');
    }

    public function collectDue(): void
    {
        $order = Order::find($this->orderId);
        $plan = OrderPlan::forOrder($this->orderId);

        if ($order === null || $plan === null || (float) $plan->due_amount <= 0) {
            return;
        }

        app(AdvanceService::class)->collectBalance($order, (float) $plan->due_amount);

        $this->dispatch('success', message: 'বাকি টাকা আদায় হিসেবে রেকর্ড হয়েছে।');
    }

    public function releaseDue(): void
    {
        $this->validate([
            'releaseNote' => 'required|string|min:3|max:500',
        ]);

        $plan = OrderPlan::forOrder($this->orderId);

        if ($plan === null) {
            return;
        }

        app(AdvanceService::class)->releaseDue($plan, trim($this->releaseNote));
        $this->showRelease = false;
        $this->releaseNote = '';

        $this->dispatch('success', message: 'বাকি টাকা মওকুফ — ডেলিভার্ড করা যাবে।');
    }

    public function payment(int $id): ?Payment
    {
        $payment = Payment::find($id);

        return ($payment && (int) $payment->order_id === $this->orderId) ? $payment : null;
    }

    public function render()
    {
        $order = Order::find($this->orderId);
        $plan = OrderPlan::forOrder($this->orderId);

        return view('partial-payment::livewire.payments-panel', [
            'order' => $order,
            'plan' => $plan,
            'payments' => $plan
                ? Payment::where('order_id', $this->orderId)->latest('id')->get()
                : collect(),
            'activities' => Activity::forOrder($this->orderId),
        ]);
    }
}
