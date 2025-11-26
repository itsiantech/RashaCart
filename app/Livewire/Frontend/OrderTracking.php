<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Order;

class OrderTracking extends Component
{
    public $invoiceNo;
    public $order;

    public function trackOrder()
    {
        $this->validate([
            'invoiceNo' => 'required|string',
        ]);

        $this->order = Order::with(['items.product', 'items.variant'])->where('invoice_no', $this->invoiceNo)
                            ->first();  

        if (!$this->order) {
            $this->dispatch('error', message: 'No order found with this invoice number.');
        } else {
            $this->dispatch('success', message: 'Order found successfully.');
        }
    }

    public function render()
    {
        return view('livewire.frontend.order-tracking')->layout('livewire.frontend.layouts.app');
    }
}
