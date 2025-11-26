<?php

namespace App\Livewire\Admin\Order;

use Livewire\Component;

class OrderEdit extends Component
{
    public function render()
    {
        return view('livewire.admin.order.order-edit')->layout('livewire.admin.layouts.app');
    }
}
