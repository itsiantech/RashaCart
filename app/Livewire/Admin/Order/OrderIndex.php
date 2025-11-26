<?php

namespace App\Livewire\Admin\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Services\CourierService;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\DB;

class OrderIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    protected $courier;

    protected $listeners = ['orderUpdated' => '$refresh', 'printInvoiceDirect' => 'handlePrintInvoice'];

 

    public function mount(CourierService $courier)
    {
        $this->courier = $courier;
    }

    // Reset page when search changes
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Reset page when status filter changes
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

            // Update order status
            $order->update(['status' => $status]);

            if (setting('email_notification', true) && !empty($order->user?->email)) {
                Mail::to($order->user->email)->queue(new OrderStatusMail($order));
            }

            if (setting('sms_notification', true)) {
                $message = "Your order invoice {$order->invoice_no} has been {$order->status}. Thank you for being partner with us.\n\nBest regards,\n" . setting('app_name');
                $number = $order->phone;
                $response = sms_send($number, $message);

                $smsResponse = json_decode($response, true);

                if (isset($smsResponse['response_code']) && $smsResponse['response_code'] == 1000) {
                    $this->dispatch('success', message: 'Order Status Updated & Email/SMS Sent!');
                } else {
                    $errorMessage = $smsResponse['error_message'] ?? 'SMS sending failed!';
                    $this->dispatch('error', message: $errorMessage);
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

    // Computed property for counts of each status
    public function getStatusCountsProperty()
    {
        return Order::select('status')->selectRaw('count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();
    }

    // Print invoice
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

        $order = Order::with('items')->find($orderId);

        if (!$order) {
            $this->dispatch('error', message: 'Order not found.');
            return;
        }

        // Prepare item description
        $itemDescription = $order->items
            ->map(function ($item) {
                $name = $item->product->name ?? 'Item';
                $qty = $item->quantity ?? 1;
                return "$name x$qty";
            })
            ->implode(', ');

        $data = [
            'invoice' => $order->invoice_no,
            'recipient_name' => $order->name,
            'recipient_phone' => $order->phone,
            'recipient_address' => $order->shipping_address,
            'cod_amount' => $order->total_amount,
            'note' => 'Deliver within 3PM',
            'delivery_type' => 0,
            'item_description' => $itemDescription,
        ];
        
        $response = $courier->createOrder($data);
        dd($response);
        
        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        
        if (!empty($response['status']) && $response['status'] == 200) {           
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'shipped']);
            });
            $this->dispatch('success', message: 'Order sent to courier and status updated to Shipped!');
        } else {            
            $errorMessage = $response['message'] ?? 'Failed to send order to courier.';
            $this->dispatch('error', message: $errorMessage);
        }
    }

    public function render()
    {
        $query = Order::with('items');

        
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
            'orders' => $orders,
            'statusCounts' => $this->statusCounts,
        ])->layout('livewire.admin.layouts.app');
    }
}
