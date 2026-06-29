<?php

namespace App\Livewire\Admin\Vendor;

use Livewire\Component;
use App\Models\CategoryCommission;
use App\Models\OrderItem;
use App\Models\User;
use Livewire\WithFileUploads;
use App\Models\VendorDetails;

class VendorProfile extends Component
{
    use WithFileUploads;

    public $vendorId;
    public $vendor;

    public $totalProducts = 0;
    public $totalOrders = 0;
    public $completedOrders = 0;
    public $pendingOrders = 0;

    public $vendorEarnings = 0;
    public $adminCommission = 0;

    // Editable fields
    public $shop_name;
    public $shop_address;
    public $NID_number;
    public $payment_method;
    public $bank_name;
    public $bank_account_number;
    public $account_holder_name;

    public $profile_image;
    public $trade_license_front;
    public $trade_license_back;

    public function mount($vendorId)
    {
        if (auth()->user()->id != $vendorId) {
            return redirect()->route('admin.dashboard');
        }

        $this->vendorId = $vendorId;

        $this->vendor = User::with('vendorDetails')
            ->findOrFail($vendorId);

        $details = $this->vendor->vendorDetails;

        if ($details) {
            $this->shop_name = $details->shop_name;
            $this->shop_address = $details->shop_address;
            $this->NID_number = $details->NID_number;
            $this->payment_method = $details->payment_method;
            $this->bank_name = $details->bank_name;
            $this->bank_account_number = $details->bank_account_number;
            $this->account_holder_name = $details->account_holder_name;
        }

        $this->loadVendor();
        $this->calculateStatistics();
        $this->calculateCommission();
    }

    public function updateProfile()
    {
        $this->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string',
            'NID_number' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $vendorDetails = VendorDetails::updateOrCreate(
            ['user_id' => $this->vendorId],
            [
                'shop_name' => $this->shop_name,
                'shop_address' => $this->shop_address,
                'NID_number' => $this->NID_number,
                'payment_method' => $this->payment_method,
                'bank_name' => $this->bank_name,
                'bank_account_number' => $this->bank_account_number,
                'account_holder_name' => $this->account_holder_name,
            ]
        );

        // Handle images
        if ($this->profile_image) {
            $vendorDetails->profile_image = $this->profile_image->store('vendors', 'public');
        }

        if ($this->trade_license_front) {
            $vendorDetails->trade_license_front = $this->trade_license_front->store('vendors', 'public');
        }

        if ($this->trade_license_back) {
            $vendorDetails->trade_license_back = $this->trade_license_back->store('vendors', 'public');
        }

        $vendorDetails->save();

        session()->flash('success', 'Profile updated successfully.');

        $this->vendor->refresh();
    }

    private function loadVendor(): void
    {
        $this->vendor = User::role('vendor')
            ->with(['vendorDetails', 'products'])
            ->findOrFail($this->vendorId);
    }

    private function calculateStatistics(): void
    {
        $orderItemsQuery = OrderItem::query()
            ->whereHas('product', function ($q) {
                $q->where('user_id', $this->vendorId);
            });

        $this->totalProducts = $this->vendor->products->count();
        $this->totalOrders = (clone $orderItemsQuery)->count();

        $this->completedOrders = (clone $orderItemsQuery)
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->count();

        $this->pendingOrders = (clone $orderItemsQuery)
            ->whereHas('order', fn($q) => $q->where('status', 'pending'))
            ->count();
    }

    private function calculateCommission(): void
    {
        $categoryCommissions = CategoryCommission::pluck(
            'commission_rate',
            'category_id'
        )->toArray();

        $orderItems = OrderItem::query()
            ->with('product')
            ->whereHas(
                'product',
                fn($q) =>
                $q->where('user_id', $this->vendorId)
            )
            ->whereHas(
                'order',
                fn($q) =>
                $q->where('status', 'completed')
            )
            ->get();

        $totalAdmin = 0;
        $totalVendor = 0;

        foreach ($orderItems as $item) {

            $product = $item->product;
            if (!$product) continue;

            $categoryId = $product->category_id;

            $rate = $categoryCommissions[$categoryId] ?? 0;

            // Price fallback hierarchy:
            // order_item.price → product.price → 0
            $price = $item->price ?? $product->price ?? 0;
            $quantity = $item->quantity ?? 0;

            $amount = $price * $quantity;

            if ($amount <= 0) continue;

            $adminShare = ($amount * $rate) / 100;
            $vendorShare = $amount - $adminShare;

            $totalAdmin += $adminShare;
            $totalVendor += $vendorShare;
        }

        $this->adminCommission = round($totalAdmin, 2);
        $this->vendorEarnings = round($totalVendor, 2);
    }
    public function render()
    {
        return view('livewire.admin.vendor.vendor-profile', [
            'vendor' => $this->vendor,
            'totalProducts' => $this->totalProducts,
            'totalOrders' => $this->totalOrders,
            'completedOrders' => $this->completedOrders,
            'pendingOrders' => $this->pendingOrders,
        ]);
    }
}
