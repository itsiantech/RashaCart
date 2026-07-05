<?php

namespace App\Livewire\Admin\Vendor;

use App\Models\CategoryCommission;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Livewire\Component;
use Livewire\WithPagination;

class VendorPayout extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $updatesQueryString = ['search', 'perPage'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function calculateVendorEarningsForVendors($vendors)
    {
        $vendorIds = $vendors->pluck('id')->toArray();

        // Get commission rates
        $categoryCommissions = CategoryCommission::pluck(
            'commission_rate',
            'category_id'
        )->toArray();

        // Get all completed order items for those vendors
        $orderItems = OrderItem::with(['product'])
            ->whereHas('product', function ($q) use ($vendorIds) {
                $q->whereIn('user_id', $vendorIds);
            })
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->get();

        $earnings = [];

        foreach ($vendors as $vendor) {
            $earnings[$vendor->id] = 0;
        }

        foreach ($orderItems as $item) {

            if (!$item->product) continue;

            $vendorId = $item->product->user_id;

            $categoryId = $item->product->category_id;
            $rate = $categoryCommissions[$categoryId] ?? 0;

            $price = $item->price ?? $item->product->price ?? 0;
            $quantity = $item->quantity ?? 0;

            $amount = $price * $quantity;

            if ($amount <= 0) continue;

            $vendorShare = $amount - (($amount * $rate) / 100);

            $earnings[$vendorId] += $vendorShare;
        }

        return $earnings;
    }

    public function withdraw($vendorId)
    {
        $vendor = User::findOrFail($vendorId);

        $earnings = $this->calculateVendorEarningsForVendors(
            collect([$vendor])
        );

        $totalEarned = $earnings[$vendorId] ?? 0;

        $totalWithdrawn = WithdrawalRequest::where('vendor_id', $vendorId)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');

        $available = $totalEarned - $totalWithdrawn;

        if ($available < 500) {
            session()->flash('error', 'Minimum withdrawal amount is 500 TK.');
            return;
        }

        WithdrawalRequest::create([
            'vendor_id' => $vendorId,
            'amount' => $available,
            'status' => 'pending'
        ]);

        $this->dispatch('$refresh');

        session()->flash('success', 'Withdrawal request submitted successfully.');
    }

    public function render()
    {
        $query = User::query()->role('vendor');

        // ✅ If vendor is logged in, show only his own record
        if (auth()->user()->hasRole('vendor')) {
            $query->where('id', auth()->id());
        } else {
            // Admin can search vendors
            if ($this->search) {
                $query->where('name', 'like', '%' . $this->search . '%');
            }
        }

        $vendors = $query
            ->with(['payouts'])
            ->paginate($this->perPage);

        $earnings = $this->calculateVendorEarningsForVendors(
            $vendors->getCollection()
        );

        $withdrawals = WithdrawalRequest::whereIn(
            'vendor_id',
            $vendors->pluck('id')
        )
            ->whereIn('status', ['approved', 'paid'])
            ->get()
            ->groupBy('vendor_id');

        $withdrawnTotals = [];

        foreach ($withdrawals as $vendorId => $rows) {
            $withdrawnTotals[$vendorId] = $rows->sum('amount');
        }

        return view('livewire.admin.vendor.vendor-payout', [
            'vendors' => $vendors,
            'earnings' => $earnings,
            'withdrawnTotals' => $withdrawnTotals
        ])->layout('livewire.admin.layouts.app');
    }
}
