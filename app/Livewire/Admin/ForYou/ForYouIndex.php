<?php

namespace App\Livewire\Admin\ForYou;

use Livewire\Component;

class ForYouIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.for-you.for-you-index')->layout('livewire.admin.layouts.app');
    }
}
