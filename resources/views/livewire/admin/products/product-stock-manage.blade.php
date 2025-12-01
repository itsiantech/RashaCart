<div>
    <div class="card mb-4">
        <div class="card-header">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Stock Management - {{ $product->name }}</h5>
                <div>                       
                    <a wire:navigate href="{{ route('admin.product.index') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-left-arrow-alt"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Main Product Stock -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Main Product Stock</h6>
                                <p class="mb-0 text-muted">Current stock:
                                    <strong class="text-primary">{{ $productStock }}</strong>
                                </p>
                            </div>
                            <button type="button" wire:click="showAddInventory()" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Manage Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variants Stock -->
            @if ($product->has_variant && $product->variants->count() > 0)
                <div class="row">
                   
                        <div class="col-md-12">
                            <div class="border rounded p-3 bg-light">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Variants Stock</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="15%">SKU</th>
                                                    <th width="25%">Attributes</th>
                                                    <th width="12%">Price</th>
                                                    <th width="10%">Current Stock</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($product->variants as $variant)
                                                    @php
                                                        $variantStock = $variantStocks[$variant->id] ?? 0;
                                                        $stockBadgeClass = $this->getStockBadgeClass($variantStock);
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $variant->sku }}</strong>
                                                        </td>
                                                        <td>
                                                            @foreach ($variant->variantAttributeValues as $vav)
                                                                <span class="badge bg-primary me-1 mb-1">
                                                                    {{ $vav->attribute->name }}:
                                                                    {{ $vav->attributeValue->value }}
                                                                </span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <strong>৳{{ number_format($product->price + $variant->varient_price, 2) }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $stockBadgeClass }}">
                                                                {{ $variantStock }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                wire:click="showAddInventory({{ $variant->id }})"
                                                                class="btn btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Manage Stock
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                  
                </div>
            @endif

            <!-- Inventory History -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6 class="mb-3">Stock History</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Variant</th>
                                    <th>Quantity</th>
                                    <th>Stock After</th>
                                    <th>Notes</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryHistory as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span
                                                class="badge @if ($history->type == 'in') bg-success @else bg-danger @endif">
                                                {{ $history->type == 'in' ? 'IN' : 'OUT' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($history->variant)
                                                {{ $history->variant->sku }}
                                            @else
                                                <span class="text-muted">Main Product</span>
                                            @endif
                                        </td>
                                        <td
                                            class="@if ($history->type == 'in') text-success @else text-danger @endif">
                                            {{ $history->type == 'in' ? '+' : '-' }}{{ $history->quantity }}
                                        </td>
                                        <td>
                                            <strong>{{ $history->current_stock }}</strong>
                                        </td>
                                        <td>{{ $history->notes ?? '-' }}</td>
                                        <td>{{ $history->user->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No stock history found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Modal -->
    @if ($showInventoryModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($selectedVariantId)
                                Manage Variant Stock
                            @else
                                Manage Product Stock
                            @endif
                        </h5>
                        <button type="button" wire:click="closeInventoryModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select wire:model="inventoryType" class="form-select">
                                <option value="in">Stock In (Add)</option>
                                <option value="out">Stock Out (Remove)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" wire:model="inventoryQuantity" class="form-control" min="1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea wire:model="inventoryNotes" class="form-control" rows="3" placeholder="Reason for stock change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeInventoryModal"
                            class="btn btn-secondary">Cancel</button>
                        <button type="button" wire:click="addInventory" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
