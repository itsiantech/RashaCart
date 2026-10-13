<?php

namespace App\Livewire\Admin\Tracking;

use App\Jobs\SendTrackingEventJob;
use App\Models\Order;
use App\Models\TrackingLog;
use App\Models\TrackingPlatform;
use App\Services\Tracking\TrackingManager;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TrackingLogIndex extends Component
{
    use WithPagination;

    public string $platform = '';
    public string $event = '';
    public string $status = '';
    public string $orderId = '';
    public string $date = '';
    public ?int $expandedId = null;
    public string $resendOrderId = '';

    public function updatedPlatform() { $this->resetPage(); }
    public function updatedEvent() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedOrderId() { $this->resetPage(); }
    public function updatedDate() { $this->resetPage(); }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    /**
     * Retry a failed order-based event by rebuilding fresh (unmasked) data
     * from the order. The stored payload is PII-masked and must not be resent.
     */
    public function retry(int $id): void
    {
        $log = TrackingLog::findOrFail($id);

        if (!$log->order_id) {
            $this->dispatch('error', message: 'Only order-based events can be retried.');

            return;
        }

        $order = Order::with(['items.product', 'user'])->find($log->order_id);

        if (!$order) {
            $this->dispatch('error', message: 'Order not found.');

            return;
        }

        // Allow exactly-once guard to pass for this deliberate resend
        $order->update(['purchase_sent_at' => null]);

        app(TrackingManager::class)->trackPurchase($order);

        $this->dispatch('success', message: "Resend queued for order #{$order->id}.");
    }

    /**
     * Resend Purchase for an arbitrary order id.
     */
    public function resendOrder(): void
    {
        $this->validate(['resendOrderId' => 'required|integer|exists:orders,id']);

        $order = Order::with(['items.product', 'user'])->find($this->resendOrderId);

        if (in_array($order->status, ['cancelled', 'returned'])) {
            $this->dispatch('error', message: 'Cancelled/returned orders are never sent.');

            return;
        }

        $order->update(['purchase_sent_at' => null]);

        app(TrackingManager::class)->trackPurchase($order);

        $this->resendOrderId = '';
        $this->dispatch('success', message: "Purchase resend queued for order #{$order->id}.");
    }

    protected function stats(): array
    {
        $today = TrackingLog::whereDate('created_at', today());
        $sentToday = (clone $today)->count();
        $successToday = (clone $today)->where('status', 'success')->count();
        $failedToday = (clone $today)->where('status', 'failed')->count();
        $lastEvent = TrackingLog::latest('id')->first();

        try {
            $stuckJobs = DB::table('jobs')->where('queue', 'tracking')->count();
            $failedJobs = DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            $stuckJobs = 0;
            $failedJobs = 0;
        }

        return [
            'sentToday' => $sentToday,
            'successRate' => $sentToday > 0 ? round($successToday * 100 / $sentToday, 1) : 100,
            'failedToday' => $failedToday,
            'lastEventAt' => $lastEvent?->created_at,
            'stuckJobs' => $stuckJobs,
            'failedJobs' => $failedJobs,
        ];
    }

    public function render()
    {
        $logs = TrackingLog::with('platform')
            ->when($this->platform, fn ($q) => $q->whereHas('platform', fn ($p) => $p->where('platform', $this->platform)))
            ->when($this->event, fn ($q) => $q->where('event_name', $this->event))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->orderId, fn ($q) => $q->where('order_id', $this->orderId))
            ->when($this->date, fn ($q) => $q->whereDate('created_at', $this->date))
            ->latest('id')
            ->paginate(25);

        return view('livewire.admin.tracking.tracking-log-index', [
            'logs' => $logs,
            'stats' => $this->stats(),
            'platforms' => TrackingPlatform::select('platform')->distinct()->pluck('platform'),
            'events' => TrackingLog::select('event_name')->distinct()->pluck('event_name'),
        ])->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'Tracking Event Logs']);
    }
}
