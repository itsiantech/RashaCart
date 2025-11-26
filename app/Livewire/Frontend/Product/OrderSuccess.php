<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
use App\Models\Order;

class OrderSuccess extends Component
{
    public $invoiceNo;
    public function mount($order)
    {
        $this->invoiceNo = Order::find($order)->invoice_no;
    }

    public function render()
    {
        return view('livewire.frontend.product.order-success')->layout('livewire.frontend.layouts.app');
    }
    
}
