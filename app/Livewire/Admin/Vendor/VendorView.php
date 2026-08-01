<?php

namespace App\Livewire\Admin\Vendor;

use App\Models\CategoryCommission;
use App\Models\OrderItem;
use App\Models\User;
use Livewire\Component;

class VendorView extends Component
{
    public $vendorId;

    public $vendor;

    public $totalProducts = 0;

    public $totalOrders = 0;

    public $completedOrders = 0;

    public $pendingOrders = 0;

    public $vendorEarnings = 0;

    public $adminCommission = 0;

    public function mount($vendorId)
    {
        $this->vendorId = $vendorId;

        $this->loadVendor();
        $this->calculateStatistics();
        $this->calculateCommission();
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
            ->whereHas('order', fn ($q) => $q->where('status', 'completed'))
            ->count();

        $this->pendingOrders = (clone $orderItemsQuery)
            ->whereHas('order', fn ($q) => $q->where('status', 'pending'))
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
                fn ($q) => $q->where('user_id', $this->vendorId)
            )
            ->whereHas(
                'order',
                fn ($q) => $q->where('status', 'completed')
            )
            ->get();

        $totalAdmin = 0;
        $totalVendor = 0;

        foreach ($orderItems as $item) {

            $product = $item->product;
            if (! $product) {
                continue;
            }

            $categoryId = $product->category_id;

            $rate = $categoryCommissions[$categoryId] ?? 0;

            // Price fallback hierarchy:
            // order_item.price → product.price → 0
            $price = $item->price ?? $product->price ?? 0;
            $quantity = $item->quantity ?? 0;

            $amount = $price * $quantity;

            if ($amount <= 0) {
                continue;
            }

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
        return view('livewire.admin.vendor.vendor-view', [
            'vendor' => $this->vendor,
            'totalProducts' => $this->totalProducts,
            'totalOrders' => $this->totalOrders,
            'completedOrders' => $this->completedOrders,
            'pendingOrders' => $this->pendingOrders,
        ])->layout('livewire.admin.layouts.app');
    }
}
