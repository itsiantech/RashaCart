<?php

namespace App\Livewire\Admin\Order;

use App\Mail\OrderStatusMail;
use App\Models\CategoryCommission;
use App\Models\Order;
use App\Models\Product;
use App\Services\CourierService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class OrderVendor extends Component
{
    use WithoutUrlPagination, WithPagination;

    public Product $product;

    public $search = '';

    public $statusFilter = 'all';

    protected $listeners = ['orderUpdated' => '$refresh', 'printInvoiceDirect' => 'handlePrintInvoice'];

    // --------------------
    // Status constants
    // --------------------
    const STATUS_PENDING = 'pending';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_RETURNED = 'returned';

    const STATUS_COMPLETED = 'completed';

    public static $statuses = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
        self::STATUS_RETURNED,
        self::STATUS_COMPLETED,
    ];

    public function mount(CourierService $courier)
    {
        $this->courier = $courier;
    }

    public function markCompleted($orderId)
    {
        $order = Order::with('items.product.user')->findOrFail($orderId);

        if ($order->status === self::STATUS_COMPLETED) {
            return; // Prevent duplicate settlement
        }

        DB::beginTransaction();

        try {

            $order->update(['status' => self::STATUS_COMPLETED]);

            // Preload category commission rates
            $commissionRates = CategoryCommission::pluck('commission_rate', 'category_id');

            foreach ($order->items as $item) {

                $product = $item->product;
                $vendor = $product?->user;

                if (! $vendor) {
                    continue;
                }

                $categoryId = $product->category_id;

                $rate = $commissionRates[$categoryId] ?? 0;

                $total = $item->total ?? ($item->price * $item->quantity);

                $adminCommission = $total * $rate / 100;
                $vendorEarning = $total - $adminCommission;

                DB::table('order_commissions')->insert([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'vendor_id' => $vendor->id,
                    'commission_rate' => $rate,
                    'admin_commission' => $adminCommission,
                    'vendor_earning' => $vendorEarning,
                    'created_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatus($orderId, $status)
    {
        if (! in_array($status, self::$statuses)) {
            $this->dispatch('error', message: 'Invalid order status.');

            return;
        }

        DB::beginTransaction();

        try {
            $order = Order::with('items.product.user')->findOrFail($orderId);

            $previousStatus = $order->status;

            $order->update(['status' => $status]);

            // ðŸ‘‰ Run commission only when changing TO completed
            if ($status === self::STATUS_COMPLETED && $previousStatus !== self::STATUS_COMPLETED) {

                $this->settleCommission($order);
            }

            if (setting('email_notification', true) && ! empty($order->user?->email)) {
                Mail::to($order->user->email)->queue(new OrderStatusMail($order));
            }

            if (setting('sms_notification', true)) {
                $message = "Your order invoice {$order->invoice_no} has been {$order->status}.";
                sms_send($order->phone, $message);
            }

            DB::commit();

            $this->dispatch('success', message: 'Order status updated successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('error', message: 'Something went wrong: '.$e->getMessage());
        }
    }

    protected function settleCommission($order)
    {
        $commissionRates = CategoryCommission::pluck('commission_rate', 'category_id');

        foreach ($order->items as $item) {

            $product = $item->product;
            $vendor = $product?->user;

            if (! $vendor) {
                continue;
            }

            $categoryId = $product->category_id ?? 0;
            $rate = $commissionRates[$categoryId] ?? 0;

            $total = $item->total ?? ($item->price * $item->quantity);

            $adminCommission = $total * $rate / 100;
            $vendorEarning = $total - $adminCommission;

            // Store snapshot
            DB::table('order_commissions')->insert([
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'vendor_id' => $vendor->id,
                'commission_rate' => $rate,
                'admin_commission' => $adminCommission,
                'vendor_earning' => $vendorEarning,
                'created_at' => now(),
            ]);

            // Credit vendor
            $vendor->vendorDetails->increment('balance', $vendorEarning);
        }
    }

    public function sendToCourier($orderId)
    {
        $courier = app(CourierService::class);

        $order = Order::with('items.product')->find($orderId);
        if (! $order) {
            $this->dispatch('error', message: 'Order not found.');

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
            'note' => 'Deliver within 3PM',
            'item_description' => $itemDescription,
        ];

        $response = $courier->createOrder($data);

        // Check for dry_run (test environment)
        if (isset($response['status']) && $response['status'] === 'dry_run') {
            $this->dispatch('error', message: 'App is not in production mode. Order not sent.');

            return;
        }

        // Steadfast returns 200, Pathao returns data directly on success
        $success = ! empty($response['status']) && $response['status'] == 200   // Steadfast
            || ! empty($response['consignment_id'])                              // Pathao
            || ! empty($response['tracking_code']);                              // Steadfast alt

        if ($success) {
            $order->update(['status' => self::STATUS_SHIPPED]);
            $this->dispatch('success', message: 'Order sent to courier and status updated to Shipped!');
        } else {
            $this->dispatch('error', message: $response['message'] ?? 'Failed to send order to courier.');
        }
    }

    // --------------------
    // Reset page when search changes
    // --------------------
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // --------------------
    // Filter by status
    // --------------------
    public function filterByStatus($status)
    {
        if (! in_array($status, self::$statuses) && $status !== 'all') {
            return;
        }
        $this->statusFilter = $status;
        $this->resetPage();
    }

    // --------------------
    // Status counts for cards
    // --------------------
    public function getStatusCountsProperty()
    {
        $query = Order::query();

        if (auth()->user()->hasRole('vendor')) {
            $vendorId = auth()->id();
            $query->whereHas('items.product', function ($q) use ($vendorId) {
                // Must have at least one product from this vendor
                $q->where('user_id', $vendorId);
            })
                ->whereDoesntHave('items.product', function ($q) use ($vendorId) {
                    // Must NOT have any products from other vendors
                    $q->where('user_id', '!=', $vendorId);
                });
        }

        return $query->select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    // --------------------
    // Print invoice
    // --------------------
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

    // --------------------
    // Render orders table
    // --------------------
    public function render()
    {
        $query = Order::with([
            'items.product' => function ($q) {
                $q->select('id', 'name', 'category_id', 'sale_price', 'buying_price', 'discount_price', 'discount_type');
            },
            'items.variant',
        ]);

        $query = Order::with([
            'items.product.user.vendorDetails',
            'items.variant',
        ]);

        // Vendor sees only orders with their products exclusively (no admin/other vendor products)
        if (auth()->user()->hasRole('vendor')) {
            $vendorId = auth()->id();
            $query->whereHas('items.product', function ($q) use ($vendorId) {
                // Must have at least one product from this vendor
                $q->where('user_id', $vendorId);
            })
                ->whereDoesntHave('items.product', function ($q) use ($vendorId) {
                    // Must NOT have any products from other vendors
                    $q->where('user_id', '!=', $vendorId);
                });
        }

        if ($this->search) {
            $query->where(
                fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('invoice_no', 'like', "%{$this->search}%")
            );
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->latest()->paginate(setting('items_per_page') ?? 15);

        return view('livewire.admin.order.order-vendor', [
            'orders' => $orders,
            'statusCounts' => $this->statusCounts,
        ])->layout('livewire.admin.layouts.app');
    }
}
