<?php

namespace Addons\HelloWorld\Livewire\Admin;

use App\Support\Hooks;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('hello-world::admin.page', [
            'initialStatus' => Hooks::filter('order.initial_status', 'pending'),
            'hasMenuSlot' => Hooks::hasSlot('admin.menu'),
        ])->layout('livewire.admin.layouts.app');
    }
}
