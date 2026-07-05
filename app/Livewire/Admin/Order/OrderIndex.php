<?php

namespace App\Livewire\Admin\Order;

use App\Mail\OrderStatusMail;
use App\Models\CategoryCommission;
use App\Models\Order;
use App\Services\CourierService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $statusFilter = 'all';
    protected $courier;

    // Edit modal state
    public $editingOrder = null;
    public $editForm = [];
    public $showEditModal = false;

    // Delete confirm state
    public $deletingOrderId = null;

    protected $listeners = ['orderUpdated' => '$refresh', 'printInvoiceDirect' => 'handlePrintInvoice', 'deleteConfirmed'];

    public function mount(CourierService $courier)
    {
        $this->courier = $courier;
    }

    // ─── DELETE ──────────────────────────────────────────────────────────────

    public function confirmDelete($orderId)
    {
        $this->deletingOrderId = $orderId;
        $this->js("window.dispatchEvent(new CustomEvent('showDeleteConfirmation'))");
    }

    public function deleteConfirmed()
    {
        if (!$this->deletingOrderId) {
            $this->dispatch('error', message: 'No order selected for deletion.');
            return;
        }

        try {
            $order = Order::findOrFail($this->deletingOrderId);

            // Delete related items first
            if ($order->items()->exists()) {
                $order->items()->delete();
            }

            // Delete the order
            $order->delete();

            $orderId = $this->deletingOrderId;
            $this->deletingOrderId = null;
            $this->resetPage();

            $this->dispatch('success', message: 'Order #' . $orderId . ' deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error deleting order: ' . $e->getMessage());
            $this->deletingOrderId = null;
        }
    }

    public function cancelDelete()
    {
        $this->deletingOrderId = null;
    }

    // Test method to verify Livewire is working
    public function testLivewire()
    {
        $this->dispatch('success', message: 'Livewire is working correctly!');
    }

    // ─── EDIT ─────────────────────────────────────────────────────────────────

    public function openEdit($orderId)
    {
        $order = Order::findOrFail($orderId);

        $this->editingOrder = $order->id;
        $this->editForm = [
            'name'             => $order->name,
            'phone'            => $order->phone,
            'shipping_address' => $order->shipping_address,
            'payment_method'   => $order->payment_method,
            'payment_amt'      => $order->payment_amt,
            'total_amount'     => $order->total_amount,
            'status'           => $order->status,
        ];
        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $this->validate([
            'editForm.name'             => 'required|string|max:255',
            'editForm.phone'            => 'required|string|max:20',
            'editForm.shipping_address' => 'required|string',
            'editForm.payment_method'   => 'required|string',
            'editForm.total_amount'     => 'required|numeric|min:0',
            'editForm.status'           => 'required|string',
        ]);

        $order = Order::findOrFail($this->editingOrder);
        $order->update($this->editForm);

        $this->showEditModal = false;
        $this->editingOrder  = null;
        $this->editForm      = [];

        $this->dispatch('success', message: 'Order updated successfully.');
    }

    public function closeEdit()
    {
        $this->showEditModal = false;
        $this->editingOrder  = null;
        $this->editForm      = [];
    }

    public function markCompleted($orderId)
    {
        $order = Order::with('vendor.vendorDetails')->findOrFail($orderId);

        if ($order->status !== 'completed') {
            $order->update(['status' => 'completed']);

            $vendor = $order->vendor;
            $rate = $vendor->vendorDetails->commission_rate ?? 0;
            $commissionAmount = ($order->total * $rate) / 100;

            CategoryCommission::create([
                'vendor_id'         => $vendor->id,
                'commission_rate'   => $rate,
                'commission_amount' => $commissionAmount,
            ]);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function updateStatus($orderId, $status)
    {
        DB::beginTransaction();

        try {
            $order = Order::with('user')->findOrFail($orderId);
            $order->update(['status' => $status]);

            if (setting('email_notification', true) && !empty($order->user?->email)) {
                Mail::to($order->user->email)->queue(new OrderStatusMail($order));
            }

            if (setting('sms_notification', true)) {
                $message = "Your order invoice {$order->invoice_no} has been {$order->status}. Thank you for being partner with us.\n\nBest regards,\n" . setting('app_name');
                $response = sms_send($order->phone, $message);
                $smsResponse = json_decode($response, true);

                if (isset($smsResponse['response_code']) && $smsResponse['response_code'] == 1000) {
                    $this->dispatch('success', message: 'Order Status Updated & Email/SMS Sent!');
                } else {
                    $this->dispatch('error', message: $smsResponse['error_message'] ?? 'SMS sending failed!');
                }
            } else {
                $this->dispatch('success', message: 'Order Status Updated!');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function getStatusCountsProperty()
    {
        return Order::select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function printInvoiceDirect($orderId)
    {
        $order = Order::with(['items.product', 'items.variant.variantAttributeValues.attribute', 'items.variant.variantAttributeValues.attributeValue'])->find($orderId);

        if ($order) {
            $this->dispatch('printInvoiceDirect', order: $order->toArray());
        }
    }

    public function sendToCourier($orderId)
    {
        $courier = new CourierService();
        $order   = Order::with('items')->find($orderId);

        if (!$order) {
            $this->dispatch('error', message: 'Order not found.');
            return;
        }

        $itemDescription = $order->items->map(function ($item) {
            $name = $item->product->name ?? 'Item';
            $qty  = $item->quantity ?? 1;
            return "$name x$qty";
        })->implode(', ');

        $data = [
            'invoice'          => $order->invoice_no,
            'name'             => $order->name,
            'phone'            => $order->phone,
            'address'          => $order->shipping_address,
            'cod_amount'       => $order->total_amount,
            'note'             => 'Deliver within 3PM',
            'item_description' => $itemDescription,
        ];

        $response = $courier->createOrder($data);

        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        if (!empty($response['status']) && $response['status'] == 200) {
            DB::transaction(fn() => $order->update(['status' => 'shipped']));
            $this->dispatch('success', message: 'Order sent to courier and status updated to Shipped!');
        } else {
            $this->dispatch('error', message: $response['message'] ?? 'Failed to send order to courier.');
        }
    }

    public function render()
    {
        $query = Order::with(['items', 'vendor']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_no', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->latest()->paginate(setting('items_per_page') ?? 15);

        return view('livewire.admin.order.order-index', [
            'orders'       => $orders,
            'statusCounts' => $this->statusCounts,
        ])->layout('livewire.admin.layouts.app');
    }
}
