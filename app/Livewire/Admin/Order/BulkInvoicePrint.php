<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class BulkInvoicePrint extends Component
{
    use WithPagination;

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // PROPERTIES
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Filter: Search by order number, invoice number, customer name, or phone
     */
    public string $search = '';

    /**
     * Filter: Order status (pending, completed, cancelled, etc.)
     */
    public string $status = '';

    /**
     * Filter: Date range - From
     */
    public string $dateFrom = '';

    /**
     * Filter: Date range - To
     */
    public string $dateTo = '';

    /**
     * Filter: Printing status (printed, not printed)
     */
    public string $printingStatus = '';

    /**
     * Filter: Product SKU
     */
    public string $productSku = '';

    /**
     * Filter: Product Name
     */
    public string $productName = '';

    /**
     * Filter: Quantity Range - Minimum
     */
    public int|string $qtyMin = '';

    /**
     * Filter: Quantity Range - Maximum
     */
    public int|string $qtyMax = '';

    /**
     * Selected order IDs for bulk operations
     */
    public array $selectedOrders = [];

    /**
     * Select all orders on current page
     */
    public bool $selectAll = false;

    /**
     * Query string parameters (persisted in URL)
     */
    protected $queryString = ['search', 'status', 'dateFrom', 'dateTo', 'printingStatus', 'productSku', 'productName', 'qtyMin', 'qtyMax', 'page'];

    /**
     * Pagination records per page
     */
    private int $perPage = 20;

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // LIFECYCLE & UPDATE LISTENERS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * When "Select All" checkbox is toggled
     */
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedOrders = $value
            ? $this->getOrdersQuery()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray()
            : [];
    }

    /**
     * When individual order selection changes, uncheck "Select All"
     */
    public function updatedSelectedOrders(): void
    {
        $this->selectAll = false;
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // PUBLIC ACTIONS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Clear all filters and reset pagination
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'status',
            'dateFrom',
            'dateTo',
            'printingStatus',
            'productSku',
            'productName',
            'qtyMin',
            'qtyMax',
            'selectedOrders',
            'selectAll',
        ]);
        $this->resetPage();
    }

    /**
     * Clear all selections
     */
    public function clearSelection(): void
    {
        $this->selectedOrders = [];
        $this->selectAll = false;
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // PRIVATE QUERY BUILDERS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Build base query with filters and eager loading
     */
    private function getOrdersQuery()
    {
        return Order::query()
            ->with([
                'items.product',
                'items.variant.variantAttributeValues.attribute',
                'items.variant.variantAttributeValues.attributeValue',
            ])
            ->when($this->search, function ($q) {
                $searchTerm = "%{$this->search}%";

                // ✅ Wrap orWhere in a grouped closure so it doesn't leak
                return $q->where(function ($inner) use ($searchTerm) {
                    $inner->where('order_number', 'like', $searchTerm)
                        ->orWhere('invoice_no', 'like', $searchTerm)
                        ->orWhere('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm);
                });
            })
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->printingStatus, function ($q) {
                if ($this->printingStatus === 'printed') {
                    return $q->where('is_printed', true);
                }
                if ($this->printingStatus === 'not_printed') {
                    return $q->where('is_printed', false);
                }
            })
            ->when($this->productSku, fn ($q) => $q->whereHas(
                'items.product',
                fn ($sub) => $sub->where('sku', 'like', "%{$this->productSku}%")
            ))
            ->when($this->productName, fn ($q) => $q->whereHas(
                'items.product',
                fn ($sub) => $sub->where('name', 'like', "%{$this->productName}%")
            ))
            // ✅ Fixed qty filters — use whereHas with a join-style subquery
            ->when($this->qtyMin, fn ($q) => $q->whereHas(
                'items',
                fn ($sub) => $sub->where('quantity', '>=', $this->qtyMin)
            ))
            ->when($this->qtyMax, fn ($q) => $q->whereHas(
                'items',
                fn ($sub) => $sub->where('quantity', '<=', $this->qtyMax)
            ))
            ->latest('id');
    }

    /**
     * Get distinct order statuses for filter dropdown
     */
    private function getAvailableStatuses()
    {
        return Order::distinct('status')
            ->pluck('status')
            ->filter()
            ->sort()
            ->values();
    }

    /**
     * Get available printing statuses
     */
    private function getPrintingStatuses()
    {
        return [
            'printed' => 'Printed',
            'not_printed' => 'Not Printed',
        ];
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    // RENDER
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    /**
     * Render the Livewire component view
     */
    public function render()
    {
        return view('livewire.admin.order.bulk-invoice-print', [
            'orders' => $this->getOrdersQuery()->paginate($this->perPage),
            'statuses' => $this->getAvailableStatuses(),
            'printingStatuses' => $this->getPrintingStatuses(),
            'selectedCount' => count($this->selectedOrders),
            'totalCount' => $this->getOrdersQuery()->count(),
        ])->layout('livewire.admin.layouts.app');
    }
}
