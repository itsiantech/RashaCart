<div>
    <div class="new-arrival-admin">

        {{-- Page Header --}}
        <div class="na-page-header">
            <div>
                <h1 class="na-page-title">✨ New Arrivals</h1>
                <p class="na-page-sub">Manage products shown in the New Arrivals section</p>
            </div>
            <span class="na-badge">{{ $items->total() }} Products</span>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="na-alert na-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="na-alert na-alert-error">{{ session('error') }}</div>
        @endif

        {{-- Add Product Search --}}
        <div class="na-add-card">
            <h2 class="na-card-title">Add Product</h2>
            <div class="na-search-wrap" x-data>
                <div class="na-search-input-wrap">
                    <i class="fas fa-search na-search-icon"></i>
                    <input
                        wire:model.live.debounce.300ms="productSearch"
                        type="text"
                        placeholder="Search product by name..."
                        class="na-search-input"
                        autocomplete="off" />
                </div>

                @if(count($searchResults))
                <div class="na-dropdown">
                    @foreach($searchResults as $product)
                    <div class="na-dropdown-item" wire:click="addProduct({{ $product['id'] }})">
                        <img
                            src="{{ $product['thumbnail'] ? asset('storage/' . $product['thumbnail']) : 'https://placehold.co/48x48' }}"
                            alt="{{ $product['name'] }}"
                            class="na-dropdown-img" />
                        <div class="na-dropdown-info">
                            <span class="na-dropdown-name">{{ $product['name'] }}</span>
                            <span class="na-dropdown-price">৳{{ number_format($product['price'], 0) }}</span>
                        </div>
                        <span class="na-dropdown-add"><i class="fas fa-plus"></i> Add</span>
                    </div>
                    @endforeach
                </div>
                @elseif(strlen($productSearch) >= 2)
                <div class="na-dropdown">
                    <div class="na-dropdown-empty">No products found.</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Search / Filter --}}
        <div class="na-table-card">
            <div class="na-table-header">
                <h2 class="na-card-title">All New Arrival Products</h2>
                <div class="na-search-input-wrap" style="max-width:260px">
                    <i class="fas fa-search na-search-icon"></i>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Filter list..."
                        class="na-search-input" />
                </div>
            </div>

            {{-- Table --}}
            <div class="na-table-wrap">
                <table class="na-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr wire:key="na-{{ $item->id }}">
                            <td class="na-td-num">{{ $items->firstItem() + $index }}</td>
                            <td>
                                <div class="na-product-cell">
                                    <img
                                        src="{{ $item->product->thumbnail ? asset('storage/' . $item->product->thumbnail) : 'https://placehold.co/48x48' }}"
                                        alt="{{ $item->product->name }}"
                                        class="na-product-thumb" />
                                    <span class="na-product-name">{{ $item->product->name }}</span>
                                </div>
                            </td>
                            <td>৳{{ number_format($item->product->price, 0) }}</td>
                            <td>{{ $item->sort_order }}</td>
                            <td>w
                                <button
                                    wire:click="toggleActive({{ $item->id }})"
                                    class="na-status-btn {{ $item->is_active ? 'active' : 'inactive' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <button
                                    wire:click="remove({{ $item->id }})"
                                    wire:confirm="Remove this product from New Arrivals?"
                                    class="na-remove-btn">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="na-empty">No products added yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="na-pagination">
                {{ $items->links() }}
            </div>
        </div>

    </div>

    <style>
        /* ── Layout ────────────────────────────────────── */
        .new-arrival-admin {
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ── Header ────────────────────────────────────── */
        .na-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .na-page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .na-page-sub {
            font-size: 0.85rem;
            color: #666;
            margin: 2px 0 0;
        }

        .na-badge {
            background: #f0f0f0;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
        }

        /* ── Alerts ────────────────────────────────────── */
        .na-alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .na-alert-success {
            background: #e6f9f0;
            color: #1a7a4a;
            border: 1px solid #b2e8cf;
        }

        .na-alert-error {
            background: #fef0f0;
            color: #c0392b;
            border: 1px solid #f5c6c6;
        }

        /* ── Cards ─────────────────────────────────────── */
        .na-add-card,
        .na-table-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .na-card-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 1rem;
        }

        .na-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        /* ── Search ────────────────────────────────────── */
        .na-search-wrap {
            position: relative;
        }

        .na-search-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .na-search-icon {
            position: absolute;
            left: 12px;
            color: #aaa;
            font-size: 0.85rem;
        }

        .na-search-input {
            width: 100%;
            padding: 10px 14px 10px 36px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            transition: border 0.2s;
        }

        .na-search-input:focus {
            border-color: #f97316;
        }

        /* ── Dropdown ──────────────────────────────────── */
        .na-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.10);
            z-index: 100;
            max-height: 320px;
            overflow-y: auto;
        }

        .na-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            cursor: pointer;
            transition: background 0.15s;
        }

        .na-dropdown-item:hover {
            background: #fff7ed;
        }

        .na-dropdown-img {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .na-dropdown-info {
            flex: 1;
            min-width: 0;
        }

        .na-dropdown-name {
            display: block;
            font-size: 0.88rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .na-dropdown-price {
            font-size: 0.8rem;
            color: #f97316;
        }

        .na-dropdown-add {
            font-size: 0.8rem;
            color: #f97316;
            white-space: nowrap;
            font-weight: 600;
        }

        .na-dropdown-empty {
            padding: 1rem;
            text-align: center;
            color: #999;
            font-size: 0.88rem;
        }

        /* ── Table ─────────────────────────────────────── */
        .na-table-wrap {
            overflow-x: auto;
        }

        .na-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .na-table th {
            text-align: left;
            padding: 10px 12px;
            background: #fafafa;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            color: #555;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .na-table td {
            padding: 12px;
            border-bottom: 1px solid #f5f5f5;
            color: #333;
            vertical-align: middle;
        }

        .na-td-num {
            color: #aaa;
            width: 40px;
        }

        .na-product-cell {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .na-product-thumb {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .na-product-name {
            font-weight: 500;
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Buttons ───────────────────────────────────── */
        .na-status-btn {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: opacity 0.15s;
        }

        .na-status-btn.active {
            background: #e6f9f0;
            color: #1a7a4a;
        }

        .na-status-btn.inactive {
            background: #f5f5f5;
            color: #999;
        }

        .na-status-btn:hover {
            opacity: 0.8;
        }

        .na-remove-btn {
            background: #fef0f0;
            color: #e53935;
            border: none;
            padding: 7px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .na-remove-btn:hover {
            background: #fdd;
        }

        /* ── Empty / Pagination ────────────────────────── */
        .na-empty {
            text-align: center;
            color: #aaa;
            padding: 2rem;
        }

        .na-pagination {
            margin-top: 1rem;
        }
    </style>
</div>