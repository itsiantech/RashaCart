<?php

namespace App\Livewire\Admin\Vendor;

use App\Models\WithdrawalRequest;
use Livewire\Component;

class VendorApplied extends Component
{
    public function render()
    {
        $payments = WithdrawalRequest::with('vendor.vendorDetails')
            ->where('status', 'approved') // Only approved payout history
            ->latest()
            ->paginate(15);

        return view('livewire.admin.vendor.vendor-applied', 
        [
            'payments' => $payments,
        ])->layout('livewire.admin.layouts.app');
    }
}
