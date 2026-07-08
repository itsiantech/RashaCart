<?php

namespace App\Livewire\Admin\Order;

use App\Mail\OrderStatusMail;
use App\Models\CategoryCommission;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class OrderInhouse extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $search = '';

    public $statusFilter = 'all';

    protected $listeners = ['orderUpdated' => '$refresh', 'printInvoiceDirect' => 'handlePrintInvoice'];

    // --------------------
    // Status constants
    // --------------------
    const STATUS_PENDING = 'pending';

    const STATUS_CALL_NOT_RECEIVED = 'Call Not Received';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CONFIEMED = 'confirmed';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_RETURNED = 'returned';

    const STATUS_PENDING_RETURN = 'Pending Return';

    const STATUS_PENDING_CANCELLED = 'Pending Cancelled';

    public static $statuses = [
        self::STATUS_PENDING,
        self::STATUS_CALL_NOT_RECEIVED,
        self::STATUS_COMPLETED,
        self::STATUS_CONFIEMED,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
        self::STATUS_RETURNED,
        self::STATUS_PENDING_RETURN,
        self::STATUS_PENDING_CANCELLED,
    ];

    // --------------------
    // Mark an order as delivered
    // --------------------
    public function markCompleted($orderId)
    {
        $order = Order::with('vendor.vendorDetails')->findOrFail($orderId);

        if ($order->status !== self::STATUS_CONFIEMED) {
            $order->update(['status' => self::STATUS_CONFIEMED]);

            $vendor = $order->vendor;
            if ($vendor && $vendor->vendorDetails) {
                $rate = $vendor->vendorDetails->commission_rate ?? 0;
                $commissionAmount = ($order->total_amount ?? 0) * $rate / 100;

                CategoryCommission::create([
                    'category_id' => $vendor->id,
                    'commission_rate' => $rate,
                    'order_id' => $order->id,
                    'amount' => $commissionAmount,
                ]);
            }
        }
    }

    // --------------------
    // Update order status
    // --------------------
    public function updateStatus($orderId, $status)
    {
        if (! in_array($status, self::$statuses)) {
            $this->dispatch('error', message: 'Invalid order status.');

            return;
        }

        DB::beginTransaction();
        try {
            $order = Order::with('user')->findOrFail($orderId);
            $order->update(['status' => $status]);

            if (setting('email_notification', true) && ! empty($order->user?->email)) {
                Mail::to($order->user->email)->queue(new OrderStatusMail($order));
            }

            if (setting('sms_notification', true)) {
                $message = "Your order invoice {$order->invoice_no} has been {$order->status}.";
                sms_send($order->phone, $message);
            }

            $this->dispatch('success', message: 'Order status updated successfully.');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Something went wrong: '.$e->getMessage());
        }
    }

    public function sendToPathao($orderId)
    {
        $order = Order::with('items.product')->find($orderId);
        if (! $order) {
            $this->dispatch('error', message: 'Order not found.');

            return;
        }

        $driver = new \App\Services\Couriers\PathaoDriver;

        if (! $driver->canSend()) {
            $this->dispatch('error', message: 'Pathao credentials are not configured.');

            return;
        }

        $itemDescription = $order->items
            ->map(fn ($item) => ($item->product->name ?? 'Item')." x{$item->quantity}")
            ->implode(', ');

        $data = [
            'invoice' => $order->invoice_no,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->shipping_address,
            'cod_amount' => $order->total_amount,
            'note' => '',
            'delivery_type' => 48,
            'city_id' => $order->city_id ?? setting('pathao_default_city_id'),
            'zone_id' => $order->zone_id ?? setting('pathao_default_zone_id'),
            'area_id' => $order->area_id ?? null,
            'quantity' => $order->items->sum('quantity'),
            'weight' => 0.5,
            'description' => $itemDescription,
        ];

        DB::beginTransaction();
        try {
            $response = $driver->createOrder($data);

            if (! empty($response['status']) && $response['status'] == 200) {
                $order->update([
                    'status' => self::STATUS_SHIPPED,
                    'courier' => 'pathao',
                    'tracking_code' => $response['data']['consignment_id'] ?? null,
                ]);
                $this->dispatch('success', message: 'Order sent to Pathao successfully!');
            } else {
                $this->dispatch('error', message: $response['message'] ?? 'Pathao: Failed to create order.');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Pathao error: '.$e->getMessage());
        }
    }

    public function sendToSteadfast($orderId)
    {

        $order = Order::with('items.product')->find($orderId);
        if (! $order) {
            $this->dispatch('error', message: 'Order not found.');

            return;
        }

        $driver = new \App\Services\Couriers\SteadfastDriver;

        if (! $driver->canSend()) {
            $this->dispatch('error', message: 'Steadfast credentials are not configured.');

            return;
        }

        $itemDescription = $order->items
            ->map(fn ($item) => ($item->product->name ?? 'Item')." x{$item->quantity}")
            ->implode(', ');

        $data = [
            'invoice' => $order->invoice_no,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->shipping_address,
            'cod_amount' => $order->total_amount,
            'note' => $itemDescription,
        ];

        DB::beginTransaction();
        try {
            $response = $driver->createOrder($data);

            if (! empty($response['status']) && $response['status'] == 200) {
                $order->update([
                    'status' => self::STATUS_SHIPPED,
                    'courier' => 'steadfast',
                    'tracking_code' => $response['consignment']['tracking_code'] ?? null,
                ]);
                $this->dispatch('success', message: 'Order sent to Steadfast successfully!');
            } else {
                $this->dispatch('error', message: $response['message'] ?? 'Steadfast: Failed to create order.');
            }

            // ← dd($response) removed
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Steadfast error: '.$e->getMessage());
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        if (! in_array($status, self::$statuses) && $status !== 'all') {
            return;
        }
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function getStatusCountsProperty()
    {
        $adminId = auth()->id(); // Use current authenticated user as admin

        return Order::whereHas('items.product', function ($q) use ($adminId) {
            // Must have at least one admin product
            $q->where('user_id', $adminId);
        })
            ->whereDoesntHave('items.product', function ($q) use ($adminId) {
                // Must NOT have any vendor products
                $q->where('user_id', '!=', $adminId);
            })
            ->select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function printInvoiceDirect($orderId)
    {
        $order = Order::with([
            'items.product',
            'items.variant.variantAttributeValues.attribute',
            'items.variant.variantAttributeValues.attributeValue',
        ])->find($orderId);

        if ($order) {
            $this->dispatch('printInvoiceDirect', order: $order->toArray());
        }
    }

    public function render()
    {
        $adminId = auth()->id(); // Use current authenticated user as admin

        $query = Order::with(['items.product', 'items.variant'])
            ->whereHas('items.product', function ($q) use ($adminId) {
                // Must have at least one admin product
                $q->where('user_id', $adminId);
            })
            ->whereDoesntHave('items.product', function ($q) use ($adminId) {
                // Must NOT have any vendor products
                $q->where('user_id', '!=', $adminId);
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('invoice_no', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->latest()
            ->paginate(setting('items_per_page') ?? 15);

        return view('livewire.admin.order.order-inhouse', [
            'orders' => $orders,
            'statusCounts' => $this->statusCounts,
        ])->layout('livewire.admin.layouts.app');
    }
}
