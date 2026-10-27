@extends('admin_panel.layout.app')

@section('content')
<style>
    /* In-page overrides ensuring strict adherence to the Indigo Design System */
    .warehouse-header-card {
        background-color: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-lg) !important;
        padding: var(--space-16) var(--space-20) !important;
        color: var(--text-primary) !important;
        box-shadow: var(--shadow-card) !important;
        transition: background-color var(--transition), border-color var(--transition);
        cursor: pointer;
        user-select: none;
    }
    .warehouse-header-card:hover {
        background-color: var(--bg-page) !important;
        border-color: var(--border-hover) !important;
    }
    .wh-avatar {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        border: 1px solid #C7D2FE;
    }
    .wh-toggle-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        background: var(--bg-card);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition);
        flex-shrink: 0;
        cursor: pointer;
    }
    .warehouse-header-card:hover .wh-toggle-btn {
        background: var(--primary-soft);
        color: var(--primary);
        border-color: var(--primary);
    }
    .toggle-chevron {
        transition: transform var(--transition);
        font-size: 0.90rem;
    }
    .prod-letter-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        background: var(--bg-page);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid var(--border-color);
    }
    /* Clean Sort Indicators & Disable sort on Action */
    th.no-sort,
    th.no-sort::before,
    th.no-sort::after,
    table.dataTable thead th.no-sort,
    table.dataTable thead th.no-sort::before,
    table.dataTable thead th.no-sort::after,
    table.dataTable thead > tr > th.no-sort,
    table.dataTable thead > tr > th.no-sort::before,
    table.dataTable thead > tr > th.no-sort::after {
        cursor: default !important;
        background-image: none !important;
    }
    th.no-sort::before,
    th.no-sort::after,
    table.dataTable thead th.no-sort::before,
    table.dataTable thead th.no-sort::after,
    table.dataTable thead > tr > th.no-sort::before,
    table.dataTable thead > tr > th.no-sort::after {
        display: none !important;
        content: "" !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }
    /* Disable DataTables collapse (+ / -) controls and child row styling */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
        padding-left: 16px !important;
    }
    table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
        display: none !important;
        content: "" !important;
        visibility: hidden !important;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: var(--font-caption) !important;
        font-weight: 500 !important;
        color: var(--text-secondary) !important;
    }
    .dataTables_wrapper .dataTables_paginate .page-link {
        border-radius: var(--radius-md) !important;
        font-weight: 500 !important;
        font-size: 13px !important;
        padding: 6px 12px !important;
        color: var(--text-primary) !important;
        border: 1px solid var(--border-color) !important;
        background-color: var(--bg-card) !important;
        margin: 0 2px !important;
    }
    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
    }
</style>

<div class="erp-container py-4">
    <!-- Top Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="erp-page-title mb-1">Warehouse Stock Inventory</h1>
            <p class="erp-body-text mb-0">Live multi-warehouse stock allocations with decimal accuracy &amp; instant filters</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            @canany(['opening.stock.create', 'warehouse.stock.create'])
                <!-- Sole Primary Button in this area -->
                <a href="{{ route('warehouse_stocks.create') }}" class="erp-btn-primary">
                    <i class="fas fa-plus"></i> Add Opening Stock
                </a>
            @endcanany
            <!-- Secondary Action -->
            @can('stock.transfer.create')
                <a href="{{ route('stock_transfers.create') }}" class="erp-btn-secondary">
                    <i class="fas fa-exchange-alt"></i> Stock Transfer
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--success) !important;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i>
            <div class="text-dark font-weight-500">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top KPI Cards (Unified, clean styling without random loud colors) -->
    @php
        $shopPiecesFmt   = rtrim(rtrim(number_format((float)($shopPieces ?? 0), 2, '.', ','), '0'), '.');
        $godownPiecesFmt = rtrim(rtrim(number_format((float)($godownPieces ?? 0), 2, '.', ','), '0'), '.');
        $totalPiecesFmt  = rtrim(rtrim(number_format((float)($totalPiecesOverall ?? 0), 2, '.', ','), '0'), '.');
        if ($shopPiecesFmt === '' || $shopPiecesFmt === '-0') $shopPiecesFmt = '0';
        if ($godownPiecesFmt === '' || $godownPiecesFmt === '-0') $godownPiecesFmt = '0';
        if ($totalPiecesFmt === '' || $totalPiecesFmt === '-0') $totalPiecesFmt = '0';
    @endphp
    <div class="row g-3 mb-4">
        <!-- Shop Stock Card -->
        <div class="col-12 col-md-4">
            <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'shop'])) }}" class="text-decoration-none">
                <div class="erp-kpi-card h-100 {{ ($locationType ?? '') === 'shop' ? 'border-primary' : '' }}"
                     style="{{ ($locationType ?? '') === 'shop' ? 'border-color: var(--primary) !important; background-color: var(--primary-soft) !important;' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="erp-kpi-label mb-0">Main Shop Stock</span>
                                @if(($locationType ?? '') === 'shop')
                                    <span class="erp-badge erp-badge-primary">Active</span>
                                @endif
                            </div>
                            <div class="erp-kpi-value font-tabular">{{ $shopPiecesFmt }} <span class="fs-6 fw-normal text-muted">Units</span></div>
                            <div class="erp-kpi-subtext"><strong>{{ number_format($shopCount ?? 0) }}</strong> products in shop</div>
                        </div>
                        <div class="erp-kpi-icon">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Godowns Stock Card -->
        <div class="col-12 col-md-4">
            <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'godown'])) }}" class="text-decoration-none">
                <div class="erp-kpi-card h-100 {{ ($locationType ?? '') === 'godown' ? 'border-primary' : '' }}"
                     style="{{ ($locationType ?? '') === 'godown' ? 'border-color: var(--primary) !important; background-color: var(--primary-soft) !important;' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="erp-kpi-label mb-0">Storage Godowns</span>
                                @if(($locationType ?? '') === 'godown')
                                    <span class="erp-badge erp-badge-primary">Active</span>
                                @endif
                            </div>
                            <div class="erp-kpi-value font-tabular">{{ $godownPiecesFmt }} <span class="fs-6 fw-normal text-muted">Units</span></div>
                            <div class="erp-kpi-subtext"><strong>{{ number_format($godownCount ?? 0) }}</strong> products in godowns</div>
                        </div>
                        <div class="erp-kpi-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Overall Stock Card -->
        <div class="col-12 col-md-4">
            <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'all'])) }}" class="text-decoration-none">
                <div class="erp-kpi-card h-100 {{ ($locationType ?? 'all') === 'all' ? 'border-primary' : '' }}"
                     style="{{ ($locationType ?? 'all') === 'all' ? 'border-color: var(--primary) !important; background-color: var(--primary-soft) !important;' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="erp-kpi-label mb-0">Total Inventory</span>
                                @if(($locationType ?? 'all') === 'all')
                                    <span class="erp-badge erp-badge-neutral">All Locations</span>
                                @endif
                            </div>
                            <div class="erp-kpi-value font-tabular">{{ $totalPiecesFmt }} <span class="fs-6 fw-normal text-muted">Units</span></div>
                            <div class="erp-kpi-subtext"><strong>{{ number_format($totalCount ?? 0) }}</strong> total stock entries</div>
                        </div>
                        <div class="erp-kpi-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Unified Filter & Global Search Toolbar Card -->
    <div class="erp-card mb-4">
        <div class="p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <!-- Tabs: Warehouses View vs Products List -->
                <div class="erp-tab-group">
                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->query(), ['view' => 'warehouses'])) }}" 
                       class="erp-tab-btn {{ ($viewMode ?? 'warehouses') === 'warehouses' ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i> Warehouses View
                    </a>
                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->query(), ['view' => 'products'])) }}" 
                       class="erp-tab-btn {{ ($viewMode ?? 'warehouses') === 'products' ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> Products List
                    </a>
                </div>

                <!-- Location Quick Pills -->
                <div class="d-flex align-items-center gap-2">
                    <span class="erp-caption fw-semibold text-uppercase">Location:</span>
                    <div class="erp-tab-group">
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'all'])) }}" 
                           class="erp-tab-btn {{ ($locationType ?? 'all') === 'all' ? 'active' : '' }}">
                            All
                        </a>
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'shop'])) }}" 
                           class="erp-tab-btn {{ ($locationType ?? '') === 'shop' ? 'active' : '' }}">
                            🏪 Main Shop
                        </a>
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'godown'])) }}" 
                           class="erp-tab-btn {{ ($locationType ?? '') === 'godown' ? 'active' : '' }}">
                            🏢 Godowns
                        </a>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    {{-- <button type="button" onclick="window.print()" class="erp-btn-secondary">
                        <i class="fas fa-print"></i> Print
                    </button> --}}
                    @canany(['opening.stock.create', 'warehouse.stock.create'])
                        <a href="{{ route('warehouse_stocks.create') }}" class="erp-btn-secondary">
                            <i class="fas fa-plus"></i> Add Stock
                        </a>
                    @endcanany
                </div>
            </div>

            <!-- Warehouse Selector & Global Search Form -->
            <form method="GET" action="{{ route('warehouse_stocks.index') }}" class="row g-2 align-items-center pt-3 border-top">
                <input type="hidden" name="view" value="{{ $viewMode ?? 'warehouses' }}">
                <input type="hidden" name="location_type" value="{{ $locationType ?? 'all' }}">

                <div class="col-12 col-md-4">
                    <select name="warehouse_id" class="form-select erp-input" onchange="this.form.submit()">
                        <option value="all">-- All Warehouses &amp; Shops --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (string)($warehouseId ?? '') === (string)$w->id ? 'selected' : '' }}>
                                {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" name="search" id="globalWarehouseSearch" value="{{ $search ?? '' }}" 
                               class="form-control erp-input" placeholder="Search across all inventory (item name or code)...">
                        <button class="erp-btn-primary flex-shrink-0" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-2 d-flex justify-content-md-end">
                    @if(($locationType ?? 'all') !== 'all' || (($warehouseId ?? 'all') && $warehouseId !== 'all') || !empty($search))
                        <a href="{{ route('warehouse_stocks.index', ['view' => $viewMode ?? 'warehouses']) }}" class="erp-btn-ghost w-100 text-danger">
                            <i class="fas fa-times me-1"></i> Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT VIEW -->
    @if(($viewMode ?? 'warehouses') === 'warehouses')
        <!-- ========================================== -->
        <!-- 1. WAREHOUSES VIEW (WHITE / LIGHT ACCORDION)-->
        <!-- ========================================== -->
        <div class="d-flex flex-column gap-3" id="warehouseStockAccordion">
            @forelse($warehousesWithStock as $whIndex => $wh)
                @php
                    $whTotalUnits = rtrim(rtrim(number_format((float)($wh->total_units_sum ?? 0), 2, '.', ','), '0'), '.');
                    if ($whTotalUnits === '' || $whTotalUnits === '-0') $whTotalUnits = '0';
                    $whProdCount = $wh->total_products_count ?? $wh->warehouseStocks->count();
                @endphp

                <div class="erp-card overflow-hidden mb-3">
                    <!-- Clean Light Accordion Header (NO DARK NAVY) -->
                    <div class="warehouse-header-card d-flex align-items-center justify-content-between flex-wrap gap-3"
                         id="headerWh_{{ $wh->id }}"
                         onclick="if (!$(event.target).closest('a, button, select, input').length) toggleWarehouse({{ $wh->id }});"
                         role="button">
                        <div class="d-flex align-items-center gap-3">
                            <div class="wh-avatar">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h2 class="erp-section-title mb-0">{{ $wh->warehouse_name }}</h2>
                                    @if($wh->is_shop)
                                        <span class="erp-badge erp-badge-success">
                                            <i class="fas fa-store"></i> Main Shop
                                        </span>
                                    @else
                                        <span class="erp-badge erp-badge-info">
                                            <i class="fas fa-dolly"></i> Storage Godown
                                        </span>
                                    @endif
                                    <span class="erp-badge erp-badge-neutral">
                                        {{ number_format($whProdCount) }} Products
                                    </span>
                                </div>
                                <div class="erp-caption mt-1">
                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $wh->location ?: 'Primary Location' }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Stats (Units & Products) & Toggle Button -->
                        <div class="d-flex align-items-center gap-3 ms-auto flex-nowrap">
                            <div class="text-end px-3 py-1 rounded" style="background: var(--primary-soft); border: 1px solid #C7D2FE;">
                                <div class="font-tabular fs-6 fw-bold text-primary">{{ $whTotalUnits }}</div>
                                <div class="erp-caption fw-semibold text-primary" style="font-size: 10px; letter-spacing: 0.04em;">TOTAL UNITS</div>
                            </div>
                            <div class="text-end px-3 py-1 rounded" style="background: var(--bg-page); border: 1px solid var(--border-color);">
                                <div class="font-tabular fs-6 fw-bold text-dark">{{ number_format($whProdCount) }}</div>
                                <div class="erp-caption fw-semibold" style="font-size: 10px; letter-spacing: 0.04em;">PRODUCTS</div>
                            </div>
                            <!-- Toggle Button with Up/Down Chevron Icon -->
                            <div class="wh-toggle-btn ms-1" onclick="event.stopPropagation(); toggleWarehouse({{ $wh->id }});" title="Click to collapse / expand">
                                <i class="fas fa-chevron-up toggle-chevron" id="chevron_{{ $wh->id }}"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table Inside Warehouse -->
                    <div id="collapseWh_{{ $wh->id }}" class="warehouse-content-box" style="display: block;">
                        <div class="p-3 border-top">
                            <!-- In-Table Toolbar: Page Length Selector & Instant Filter Search -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <label class="erp-caption fw-semibold mb-0">Show:</label>
                                    <select class="form-select erp-input erp-table-length" data-table="whTable_{{ $wh->id }}" style="width: auto;">
                                        <option value="25">25 items</option>
                                        <option value="50" selected>50 items</option>
                                        <option value="100">100 items</option>
                                        <option value="250">250 items</option>
                                        <option value="500">500 items</option>
                                        <option value="-1">Show All ({{ number_format($whProdCount) }})</option>
                                    </select>
                                    @if(!request('search') && ($wh->total_products_count ?? 0) > 150)
                                        <span class="badge bg-light text-muted border py-1 px-2 fw-normal" style="font-size: 11px;">
                                            <i class="fas fa-info-circle me-1 text-primary"></i>Showing top 150 items. Use search or <a href="{{ request()->fullUrlWithQuery(['view' => 'products']) }}" class="text-primary fw-semibold text-decoration-none">Products View</a> for all.
                                        </span>
                                    @endif
                                </div>

                                <div style="max-width: 320px;" class="w-100">
                                    <input type="text" class="form-control erp-input quick-wh-filter" data-table="whTable_{{ $wh->id }}" 
                                           placeholder="Live search items in location...">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table erp-table warehouse-stock-table w-100" id="whTable_{{ $wh->id }}">
                                    <thead>
                                        <tr>
                                            <th style="width: 42%; min-width: 180px;">PRODUCT</th>
                                            <th style="width: 20%; min-width: 100px;">CODE</th>
                                            <th class="text-center" style="width: 20%; min-width: 100px;">QUANTITY</th>
                                            <th class="text-end no-sort" style="width: 18%; min-width: 120px;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($wh->warehouseStocks as $stock)
                                            @php
                                                $qtyVal = (float)$stock->total_pieces;
                                                $fmtQty = rtrim(rtrim(number_format($qtyVal, 2, '.', ','), '0'), '.');
                                                if ($fmtQty === '' || $fmtQty === '-0') $fmtQty = '0';
                                                $unit = $stock->product->unit_name ?? ($stock->product->unit->name ?? 'Kg');
                                                $itemName = $stock->product->item_name ?? 'N/A';
                                                $firstLetter = strtoupper(substr(trim($itemName), 0, 1)) ?: 'P';
                                            @endphp
                                            <tr>
                                                <!-- Product Column -->
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($stock->product && $stock->product->image)
                                                            <img src="{{ asset('uploads/products/'.$stock->product->image) }}" class="rounded me-3 flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover; border: 1px solid var(--border-color);" alt="">
                                                        @else
                                                            <div class="prod-letter-icon me-3">
                                                                {{ $firstLetter }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold text-dark">{{ $itemName }}</div>
                                                            <div class="d-flex align-items-center gap-1 mt-0.5 flex-wrap">
                                                                @if($stock->product && $stock->product->category_relation)
                                                                    <span class="erp-badge erp-badge-neutral">{{ $stock->product->category_relation->name }}</span>
                                                                @endif
                                                                @if($stock->remarks)
                                                                    <span class="erp-badge erp-badge-neutral">{{ $stock->remarks }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Code Column -->
                                                <td>
                                                    <span class="erp-code-badge">
                                                        {{ $stock->product->item_code ?? '--' }}
                                                    </span>
                                                </td>

                                                <!-- Quantity Column -->
                                                <td class="text-center">
                                                    @if($qtyVal > 0)
                                                        <div class="erp-qty-cell">
                                                            <span>{{ $fmtQty }}</span>
                                                            <span class="erp-qty-unit">{{ $unit }}</span>
                                                        </div>
                                                    @elseif($qtyVal < 0)
                                                        <div class="erp-qty-cell text-danger">
                                                            <span>{{ $fmtQty }}</span>
                                                            <span class="erp-qty-unit">{{ $unit }}</span>
                                                        </div>
                                                    @else
                                                        <div class="erp-qty-cell text-muted">
                                                            <span>0</span>
                                                            <span class="erp-qty-unit">{{ $unit }}</span>
                                                        </div>
                                                    @endif
                                                </td>

                                                <!-- Action Column -->
                                                <td class="text-end">
                                                    @canany(['warehouse.stock.edit', 'opening.stock.edit'])
                                                    <a href="{{ route('warehouse_stocks.edit', $stock->id) }}" 
                                                       class="erp-btn-secondary erp-btn-sm"
                                                       title="View Breakdown / Adjust Stock">
                                                        <i class="fas fa-layer-group text-muted me-1"></i>
                                                        <span>Breakdown</span>
                                                    </a>
                                                    @endcanany
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <div class="mb-2">No products currently recorded in this location.</div>
                                                    @canany(['opening.stock.create', 'warehouse.stock.create'])
                                                    <a href="{{ route('warehouse_stocks.create') }}" class="erp-btn-primary erp-btn-sm">
                                                        <i class="fas fa-plus"></i> Add Opening Stock Now
                                                    </a>
                                                    @endcanany
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="erp-card p-5 text-center text-muted">
                    <h3 class="erp-card-title mb-2">No warehouses or matching stock records found.</h3>
                    <p class="erp-body-text mb-3">Add opening stock to populate your warehouses.</p>
                    @canany(['opening.stock.create', 'warehouse.stock.create'])
                    <div>
                        <a href="{{ route('warehouse_stocks.create') }}" class="erp-btn-primary">
                            <i class="fas fa-plus"></i> Add Opening Stock
                        </a>
                    </div>
                    @endcanany
                </div>
            @endforelse
        </div>

    @else
        <!-- ========================================== -->
        <!-- 2. PRODUCTS VIEW (FLAT TABLE VIEW)         -->
        <!-- ========================================== -->
        <div class="erp-card overflow-hidden">
            <div class="table-responsive">
                <table class="table erp-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">LOCATION</th>
                            <th style="width: 35%;">PRODUCT</th>
                            <th style="width: 15%;">CODE</th>
                            <th class="text-center" style="width: 15%;">QUANTITY</th>
                            <th class="text-end no-sort" style="width: 10%;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            @php
                                $qtyVal = (float)$stock->total_pieces;
                                $fmtQty = rtrim(rtrim(number_format($qtyVal, 2, '.', ','), '0'), '.');
                                if ($fmtQty === '' || $fmtQty === '-0') $fmtQty = '0';
                                $unit = $stock->product->unit_name ?? ($stock->product->unit->name ?? 'Kg');
                                $itemName = $stock->product->item_name ?? 'N/A';
                                $firstLetter = strtoupper(substr(trim($itemName), 0, 1)) ?: 'P';
                            @endphp
                            <tr>
                                <td class="erp-caption font-tabular">{{ $stocks->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($stock->warehouse && $stock->warehouse->is_shop)
                                        <span class="erp-badge erp-badge-success">
                                            <i class="fas fa-store"></i> {{ $stock->warehouse->warehouse_name }}
                                        </span>
                                    @else
                                        <span class="erp-badge erp-badge-info">
                                            <i class="fas fa-warehouse"></i> {{ $stock->warehouse->warehouse_name ?? 'N/A' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($stock->product && $stock->product->image)
                                            <img src="{{ asset('uploads/products/'.$stock->product->image) }}" class="rounded me-3 flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover; border: 1px solid var(--border-color);" alt="">
                                        @else
                                            <div class="prod-letter-icon me-3">
                                                {{ $firstLetter }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $itemName }}</div>
                                            @if($stock->product && $stock->product->category_relation)
                                                <span class="erp-badge erp-badge-neutral mt-0.5">{{ $stock->product->category_relation->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="erp-code-badge">
                                        {{ $stock->product->item_code ?? '--' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($qtyVal > 0)
                                        <div class="erp-qty-cell">
                                            <span>{{ $fmtQty }}</span>
                                            <span class="erp-qty-unit">{{ $unit }}</span>
                                        </div>
                                    @elseif($qtyVal < 0)
                                        <div class="erp-qty-cell text-danger">
                                            <span>{{ $fmtQty }}</span>
                                            <span class="erp-qty-unit">{{ $unit }}</span>
                                        </div>
                                    @else
                                        <div class="erp-qty-cell text-muted">
                                            <span>0</span>
                                            <span class="erp-qty-unit">{{ $unit }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @canany(['warehouse.stock.edit', 'opening.stock.edit'])
                                    <a href="{{ route('warehouse_stocks.edit', $stock->id) }}" class="erp-btn-secondary erp-btn-sm" title="View Breakdown / Adjust Stock">
                                        <i class="fas fa-layer-group text-muted me-1"></i> <span>Breakdown</span>
                                    </a>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    No stock records found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $stocks->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
    // Global 100% Reliable Toggle Function for Warehouse Stock Cards
    function toggleWarehouse(whId) {
        const $content = $('#collapseWh_' + whId);
        const $chevron = $('#chevron_' + whId);
        const $header = $('#headerWh_' + whId);

        if (!$content.length) return;

        if ($content.is(':visible')) {
            // CLOSE / SLIDE UP
            $content.slideUp(150);
            $chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $header.addClass('collapsed');
        } else {
            // OPEN / SLIDE DOWN
            $content.slideDown(150, function() {
                if ($.fn.dataTable) {
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                }
            });
            $chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $header.removeClass('collapsed');
        }
    }

    $(document).ready(function() {
        const dataTablesMap = {};

        // Initialize DataTables with optimized custom DOM (no duplicate search bar)
        $('.warehouse-stock-table').each(function() {
            const tableId = $(this).attr('id');
            const dtInstance = $(this).DataTable({
                responsive: false,
                autoWidth: false,
                pageLength: 50,
                // Clean DOM: custom toolbar handles length and search
                dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 p-2"ip>',
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ items",
                    infoEmpty: "No products found",
                    infoFiltered: "(filtered from _MAX_ total)",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                order: [], // Preserve backend controller ordering
                columnDefs: [
                    { orderable: false, targets: 3 } // Action column NEVER sortable
                ]
            });

            dataTablesMap[tableId] = dtInstance;
        });

        // Fast In-Table Search Input listener (0ms live filtering)
        $(document).on('input', '.quick-wh-filter', function() {
            const tableId = $(this).data('table');
            const searchVal = $(this).val();
            if (dataTablesMap[tableId]) {
                dataTablesMap[tableId].search(searchVal).draw();
            }
        });

        // Page Length Selector listener
        $(document).on('change', '.erp-table-length', function() {
            const tableId = $(this).data('table');
            const lengthVal = parseInt($(this).val(), 10);
            if (dataTablesMap[tableId]) {
                dataTablesMap[tableId].page.len(lengthVal).draw();
            }
        });
    });
</script>
@endsection
