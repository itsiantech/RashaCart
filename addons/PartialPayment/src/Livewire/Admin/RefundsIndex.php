<?php

namespace Addons\PartialPayment\Livewire\Admin;

use Addons\PartialPayment\Models\OrderPlan;
use Addons\PartialPayment\Models\Payment;
use Addons\PartialPayment\Services\AdvanceService;
use Livewire\Component;

class RefundsIndex extends Component
{
    public function markRefunded($planId): void
    {
        $plan = OrderPlan::find((int) $planId);

        if ($plan === null || $plan->payment_status !== OrderPlan::REFUND_DUE) {
            return;
        }

        app(AdvanceService::class)->markRefunded($plan);

        $this->dispatch('success', message: 'রিফান্ড সম্পন্ন হিসেবে চিহ্নিত হয়েছে।');
    }

    public function render()
    {
        $refunds = OrderPlan::query()
            ->where('payment_status', OrderPlan::REFUND_DUE)
            ->with('order')
            ->latest('id')
            ->limit(200)
            ->get();

        $waiting = Payment::query()
            ->where('type', 'advance')
            ->where('status', 'pending')
            ->with('order')
            ->latest('id')
            ->limit(200)
            ->get();

        return view('partial-payment::livewire.refunds', [
            'refunds' => $refunds,
            'waiting' => $waiting,
            'collected' => Payment::where('type', 'advance')->where('status', 'verified')->sum('amount'),
            'toRefund' => (float) OrderPlan::where('payment_status', OrderPlan::REFUND_DUE)->sum('advance_amount'),
        ])->layout('livewire.admin.layouts.app');
    }
}
