<?php

namespace App\Livewire\Frontend\BestOffer;

use App\Models\BestOffer as ModelsBestOffer;
use Livewire\Component;

class BestOffer extends Component
{
    public function render()
    {
        $offers = ModelsBestOffer::with(['product.inventories'])
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        return view('livewire.frontend.best-offer.best-offer', compact('offers'))->layout('livewire.frontend.layouts.app');
    }
}
