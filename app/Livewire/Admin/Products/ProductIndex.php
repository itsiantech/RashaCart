<?php

namespace App\Livewire\Admin\Products;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $search = '';
    public $tab = 'all'; // all | trash
    public $selected = []; // [product_id => true]
    public $pageIds = [];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selected = [];
    }

    public function updatedTab()
    {
        $this->resetPage();
        $this->selected = [];
    }

    public function selectAll()
    {
        $allSelected = $this->allPageSelected();

        foreach ($this->pageIds as $id) {
            if ($allSelected) {
                unset($this->selected[$id]);
            } else {
                $this->selected[$id] = true;
            }
        }
    }

    private function allPageSelected()
    {
        if (empty($this->pageIds)) {
            return false;
        }

        foreach ($this->pageIds as $id) {
            if (empty($this->selected[$id])) {
                return false;
            }
        }

        return true;
    }

    private function selectedIds()
    {
        return array_keys(array_filter($this->selected));
    }

    private function clearProductCache()
    {
        Cache::forget('products');
        Cache::forget('global_cache_ready');
    }

    private function baseQuery()
    {
        $user = Auth::user();

        return Product::query()
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
    }

    private function refreshList($message)
    {
        $this->selected = [];
        $this->clearProductCache();
        $this->dispatch('success', message: $message);
    }

    // ── Bulk Actions ──

    public function bulkTrash()
    {
        $ids = $this->selectedIds();
        if (empty($ids)) {
            $this->dispatch('error', message: 'Please select at least one product!');

            return;
        }

        $count = $this->baseQuery()->whereIn('id', $ids)->delete();

        $this->refreshList($count.' product(s) moved to trash!');
    }

    public function bulkRestore()
    {
        $ids = $this->selectedIds();
        if (empty($ids)) {
            $this->dispatch('error', message: 'Please select at least one product!');

            return;
        }

        $count = $this->baseQuery()->onlyTrashed()->whereIn('id', $ids)->restore();

        $this->refreshList($count.' product(s) restored successfully!');
    }

    public function bulkDelete()
    {
        $ids = $this->selectedIds();
        if (empty($ids)) {
            $this->dispatch('error', message: 'Please select at least one product!');

            return;
        }

        $user = Auth::user();
        $deleted = 0;
        $failed = 0;

        $products = Product::onlyTrashed()
            ->whereIn('id', $ids)
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        foreach ($products as $product) {
            try {
                $this->forceDeleteProduct($product);
                $deleted++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        $message = $deleted.' product(s) permanently deleted!';
        if ($failed) {
            $message .= ' '.$failed.' could not be deleted.';
        }

        $this->refreshList($message);
    }

    // ── Single Actions ──

    public function trashProduct($id)
    {
        $this->baseQuery()->where('id', $id)->delete();

        unset($this->selected[$id]);
        $this->clearProductCache();
        $this->dispatch('success', message: 'Product moved to trash!');
    }

    public function restoreProduct($id)
    {
        $this->baseQuery()
            ->onlyTrashed()
            ->where('id', $id)
            ->restore();

        unset($this->selected[$id]);
        $this->clearProductCache();
        $this->dispatch('success', message: 'Product restored successfully!');
    }

    public function deleteProduct($id)
    {
        $user = Auth::user();
        $product = Product::onlyTrashed()
            ->where('id', $id)
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        $this->forceDeleteProduct($product);

        unset($this->selected[$id]);
        $this->clearProductCache();
        $this->dispatch('success', message: 'Product permanently deleted!');
    }

    private function forceDeleteProduct(Product $product)
    {
        $variantIds = $product->variants()->pluck('id');
        if ($variantIds->isNotEmpty()) {
            OrderItem::whereIn('product_variant_id', $variantIds)
                ->update(['product_variant_id' => null]);
        }

        // Delete related data
        $product->variants()->delete();
        $product->inventories()->delete();

        if ($product->thumbnail) {
            Storage::delete('public/'.$product->thumbnail);
        }

        if ($product->gallery_image) {
            foreach ($product->gallery_image as $image) {
                Storage::delete('public/'.$image);
            }
        }

        $product->forceDelete();
    }

    public function render()
    {
        $query = Product::with([
            'orderitems',
            'inventories',
            'variants.inventories',
        ]);

        $user = Auth::user();

        if (! $user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($this->tab === 'trash') {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('short_description', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            });
        }

        $products = $query
            ->latest()
            ->paginate(setting('items_per_page'));

        $this->pageIds = $products->getCollection()->pluck('id')->map(fn ($id) => (string) $id)->all();

        $countAll = $this->baseQuery()->count();
        $countTrash = $this->baseQuery()->onlyTrashed()->count();

        return view('livewire.admin.products.product-index', [
            'products' => $products,
            'countAll' => $countAll,
            'countTrash' => $countTrash,
            'allSelected' => $this->allPageSelected(),
            'selectedCount' => count($this->selectedIds()),
        ])->layout('livewire.admin.layouts.app');
    }

    private function calculateProductStock($product)
    {
        $inventoryIn = $product->inventories
            ->where('variant_id', null)
            ->where('type', 'in')
            ->sum('quantity');

        $inventoryOut = $product->inventories
            ->where('variant_id', null)
            ->where('type', 'out')
            ->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    private function calculateVariantStock($variant)
    {
        $inventoryIn = $variant->inventories->where('type', 'in')->sum('quantity');
        $inventoryOut = $variant->inventories->where('type', 'out')->sum('quantity');

        return $inventoryIn - $inventoryOut;
    }

    public function getTotalStock($product)
    {
        $mainStock = $this->calculateProductStock($product);

        $variantStock = 0;
        foreach ($product->variants as $variant) {
            $variantStock += $this->calculateVariantStock($variant);
        }

        return $mainStock + $variantStock;
    }

    public function getStockBadgeClass($totalStock)
    {
        if ($totalStock > 10) {
            return 'bg-success';
        } elseif ($totalStock > 0) {
            return 'bg-warning';
        } else {
            return 'bg-danger';
        }
    }
}
