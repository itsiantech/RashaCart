<?php

namespace App\Livewire\Admin\Vendor;

use App\Models\CategoryCommission;
use App\Models\OrderItem;
use App\Models\WithdrawalRequest;
use Livewire\Component;

class VendorPayoutRequest extends Component
{
    private function calculateVendorFinancials($vendorId)
    {
        $categoryCommissions = CategoryCommission::pluck(
            'commission_rate',
            'category_id'
        )->toArray();

        $orderItems = OrderItem::with('product')
            ->whereHas('product', function ($q) use ($vendorId) {
                $q->where('user_id', $vendorId);
            })
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->get();

        $totalVendor = 0;
        $totalAdmin = 0;

        foreach ($orderItems as $item) {

            if (! $item->product) {
                continue;
            }

            $categoryId = $item->product->category_id;
            $rate = $categoryCommissions[$categoryId] ?? 0;

            $price = $item->price ?? $item->product->price ?? 0;
            $quantity = $item->quantity ?? 0;

            $amount = $price * $quantity;

            if ($amount <= 0) {
                continue;
            }

            $adminShare = ($amount * $rate) / 100;
            $vendorShare = $amount - $adminShare;

            $totalVendor += $vendorShare;
            $totalAdmin += $adminShare;
        }

        $withdrawn = WithdrawalRequest::where('vendor_id', $vendorId)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');

        $available = $totalVendor - $withdrawn;

        return [
            'total_vendor_earning' => $totalVendor,
            'total_admin_commission' => $totalAdmin,
            'available_balance' => $available,
        ];
    }

    public function approve($id)
    {
        WithdrawalRequest::where('id', $id)->update([
            'status' => 'approved',
        ]);

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Payout approved'
        );

        $this->dispatch('$refresh');
    }

    public function reject($id)
    {
        WithdrawalRequest::where('id', $id)->update([
            'status' => 'rejected',
        ]);

        $this->dispatch('show-toast',
            type: 'error',
            message: 'Payout rejected'
        );

        $this->dispatch('$refresh');
    }

    public function render()
    {
        $requests = WithdrawalRequest::with('vendor')
            ->latest()
            ->get();

        $financialCache = [];

        foreach ($requests as $request) {

            $vendorId = $request->vendor_id;

            if (! isset($financialCache[$vendorId])) {
                $financialCache[$vendorId] =
                    $this->calculateVendorFinancials($vendorId);
            }

            $financials = $financialCache[$vendorId];

            $request->total_vendor_earning =
                $financials['total_vendor_earning'];

            $request->total_admin_commission =
                $financials['total_admin_commission'];

            $request->available_balance =
                $financials['available_balance'];
        }

        return view('livewire.admin.vendor.vendor-payout-request', compact('requests')
        )->layout('livewire.admin.layouts.app');
    }
}
