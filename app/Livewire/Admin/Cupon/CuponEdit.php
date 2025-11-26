<?php

namespace App\Livewire\Admin\Cupon;

use Livewire\Component;

class CuponEdit extends Component
{
    public function render()
    {
        return view('livewire.admin.cupon.cupon-edit')->layout('livewire.admin.layouts.app');
    }
}
