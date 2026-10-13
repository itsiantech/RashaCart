<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Purchase;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierStock extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';

    public $search = '';
    public $supplierId = '';
    public $from = '';
    public $to = '';
    public $expanded = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSupplierId()
    {
        $this->resetPage();
    }

    public function toggleExpand($purchaseId)
    {
        if (in_array($purchaseId, $this->expanded)) {
            $this->expanded = array_values(array_diff($this->expanded, [$purchaseId]));
        } else {
            $this->expanded[] = $purchaseId;
        }
    }

    private function baseQuery()
    {
        return Purchase::query()
            ->with(['supplier:id,name,company', 'items'])
            ->when($this->search, fn ($q) => $q->where(function ($w) {
                $w->where('invoice_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($s) => $s
                        ->where('name', 'like', "%{$this->search}%")
                        ->orWhere('company', 'like', "%{$this->search}%"));
            }))
            ->when($this->supplierId, fn ($q) => $q->where('supplier_id', $this->supplierId))
            ->when($this->from, fn ($q) => $q->whereDate('purchase_date', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('purchase_date', '<=', $this->to))
            ->orderByDesc('purchase_date')
            ->orderByDesc('id');
    }

    public function render()
    {
        $base = $this->baseQuery();

        $metrics = [
            'total_pos' => (clone $base)->count(),
            'total_qty' => (clone $base)->sum('total_quantity'),
            'total_value' => (clone $base)->sum('net_total'),
            'total_due' => (clone $base)->sum('due_amount'),
        ];

        $purchases = $base->paginate(15);

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('livewire.admin.inventory.supplier-stock', [
            'purchases' => $purchases,
            'metrics' => $metrics,
            'suppliers' => $suppliers,
        ])->layout('livewire.admin.layouts.app');
    }
}
