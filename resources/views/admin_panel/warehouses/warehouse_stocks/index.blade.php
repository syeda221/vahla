@extends('admin_panel.layout.app')

@section('content')
<style>
    .warehouse-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 16px 22px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        user-select: none;
    }
    .warehouse-header-card:hover {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    }
    .wh-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #f59e0b;
        flex-shrink: 0;
    }
    .prod-letter-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #e0f2fe;
        color: #0284c7;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stock-badge-qty {
        font-size: 0.92rem;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 8px;
        letter-spacing: 0.3px;
    }
    .nav-pill-tab {
        border-radius: 50rem;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 7px 18px;
        transition: all 0.2s;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Top Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes-stacked me-2 text-primary"></i> Warehouse Stock Inventory</h4>
            <p class="text-muted small mb-0">Separate visibility for <strong>Main Shop</strong> vs <strong>Storage Godowns</strong>, with full decimal quantity support</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('warehouse.stock.create')
                <a href="{{ route('warehouse_stocks.create') }}" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-plus-circle me-1"></i> Add Opening / Inward Stock
                </a>
            @endcan
            <a href="{{ route('stock_transfers.create') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-3 fw-bold">
                <i class="fas fa-exchange-alt me-1"></i> Stock Transfer
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top KPI Cards: Shop vs Godowns vs Overall -->
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
                <div class="card border {{ ($locationType ?? '') === 'shop' ? 'border-success shadow' : 'border-0' }} rounded-4 h-100"
                     style="background: {{ ($locationType ?? '') === 'shop' ? 'linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%)' : '#f8fafc' }}; transition: all 0.2s;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small">
                                    <i class="fas fa-store me-1"></i> Main Shop Stock
                                </span>
                                @if(($locationType ?? '') === 'shop')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">Active Filter</span>
                                @endif
                            </div>
                            <h3 class="fw-bold mb-0 text-success">{{ $shopPiecesFmt }} <span class="fs-6 fw-normal text-muted">Units</span></h3>
                            <span class="text-muted small"><i class="fas fa-cubes me-1"></i> {{ number_format($shopCount ?? 0) }} Stock records</span>
                        </div>
                        <div class="rounded-circle p-3 bg-white shadow-sm text-success">
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Godowns Stock Card -->
        <div class="col-12 col-md-4">
            <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'godown'])) }}" class="text-decoration-none">
                <div class="card border {{ ($locationType ?? '') === 'godown' ? 'border-primary shadow' : 'border-0' }} rounded-4 h-100"
                     style="background: {{ ($locationType ?? '') === 'godown' ? 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)' : '#f8fafc' }}; transition: all 0.2s;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 small">
                                    <i class="fas fa-warehouse me-1"></i> Storage Godowns Stock
                                </span>
                                @if(($locationType ?? '') === 'godown')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">Active Filter</span>
                                @endif
                            </div>
                            <h3 class="fw-bold mb-0 text-primary">{{ $godownPiecesFmt }} <span class="fs-6 fw-normal text-muted">Units</span></h3>
                            <span class="text-muted small"><i class="fas fa-dolly me-1"></i> {{ number_format($godownCount ?? 0) }} Stock records</span>
                        </div>
                        <div class="rounded-circle p-3 bg-white shadow-sm text-primary">
                            <i class="fas fa-warehouse fa-2x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Overall Stock Card -->
        <div class="col-12 col-md-4">
            <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'all'])) }}" class="text-decoration-none">
                <div class="card border {{ ($locationType ?? 'all') === 'all' ? 'border-dark shadow-sm' : 'border-0' }} rounded-4 h-100"
                     style="background: {{ ($locationType ?? 'all') === 'all' ? 'linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%)' : '#f8fafc' }}; transition: all 0.2s;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 small">
                                    <i class="fas fa-globe me-1"></i> Total Inventory (All)
                                </span>
                                @if(($locationType ?? 'all') === 'all')
                                    <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle small">All Locations</span>
                                @endif
                            </div>
                            <h3 class="fw-bold mb-0 text-dark">{{ $totalPiecesFmt }} <span class="fs-6 fw-normal text-muted">Units</span></h3>
                            <span class="text-muted small"><i class="fas fa-layer-group me-1"></i> {{ number_format($totalCount ?? 0) }} Stock records</span>
                        </div>
                        <div class="rounded-circle p-3 bg-white shadow-sm text-dark">
                            <i class="fas fa-boxes-stacked fa-2x"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- View Switcher Tabs (Image 1 Style) & Filter Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Tabs: Warehouses View vs Products View -->
                <div class="d-flex align-items-center gap-2 p-1 bg-light rounded-pill border">
                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->query(), ['view' => 'warehouses'])) }}" 
                       class="btn btn-sm nav-pill-tab {{ ($viewMode ?? 'warehouses') === 'warehouses' ? 'btn-dark shadow-sm' : 'btn-light text-muted' }}">
                        <i class="fas fa-warehouse me-1"></i> Warehouses View
                    </a>
                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->query(), ['view' => 'products'])) }}" 
                       class="btn btn-sm nav-pill-tab {{ ($viewMode ?? 'warehouses') === 'products' ? 'btn-dark shadow-sm' : 'btn-light text-muted' }}">
                        <i class="fas fa-boxes me-1"></i> Products List
                    </a>
                </div>

                <!-- Location Quick Pills -->
                <div class="d-flex align-items-center gap-2">
                    <span class="small fw-bold text-muted text-uppercase me-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Location:</span>
                    <div class="btn-group p-1 bg-light rounded-pill border" role="group">
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'all'])) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-bold {{ ($locationType ?? 'all') === 'all' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                            All
                        </a>
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'shop'])) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-bold {{ ($locationType ?? '') === 'shop' ? 'btn-success shadow-sm' : 'btn-light text-muted' }}">
                            🏪 Main Shop
                        </a>
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'godown'])) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-bold {{ ($locationType ?? '') === 'godown' ? 'btn-info text-white shadow-sm' : 'btn-light text-muted' }}">
                            🏢 Godowns
                        </a>
                    </div>
                </div>

                <!-- Search & Warehouse Filter Form -->
                <div class="w-100 mt-2">
                    <form method="GET" action="{{ route('warehouse_stocks.index') }}" class="row g-2 align-items-center">
                        <input type="hidden" name="view" value="{{ $viewMode ?? 'warehouses' }}">
                        <input type="hidden" name="location_type" value="{{ $locationType ?? 'all' }}">

                        <div class="col-12 col-md-4">
                            <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 40px; border-radius: 8px;">
                                <option value="all">-- All Warehouses & Shops --</option>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}" {{ (string)($warehouseId ?? '') === (string)$w->id ? 'selected' : '' }}>
                                        {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-5">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search product name, item code..." style="height: 40px; border-radius: 8px 0 0 8px;">
                                <button class="btn btn-primary px-3" type="submit" style="height: 40px; border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 d-flex gap-2 justify-content-md-end">
                            @if(($locationType ?? 'all') !== 'all' || (($warehouseId ?? 'all') && $warehouseId !== 'all') || !empty($search))
                                <a href="{{ route('warehouse_stocks.index', ['view' => $viewMode ?? 'warehouses']) }}" class="btn btn-sm btn-outline-danger d-flex align-items-center" style="height: 40px; border-radius: 8px;">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            @endif
                            <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary d-flex align-items-center" style="height: 40px; border-radius: 8px;">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT VIEW -->
    @if(($viewMode ?? 'warehouses') === 'warehouses')
        <!-- ========================================== -->
        <!-- 1. WAREHOUSES VIEW (ACCORDION AS IN IMAGE 1) -->
        <!-- ========================================== -->
        <div class="accordion d-flex flex-column gap-3" id="warehouseStockAccordion">
            @forelse($warehousesWithStock as $whIndex => $wh)
                @php
                    $whTotalUnits = rtrim(rtrim(number_format((float)($wh->total_units_sum ?? 0), 2, '.', ','), '0'), '.');
                    if ($whTotalUnits === '' || $whTotalUnits === '-0') $whTotalUnits = '0';
                    $whProdCount = $wh->warehouseStocks->count();
                    $collapseId = 'collapseWh_' . $wh->id;
                @endphp

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <!-- Warehouse Dark Navy Header (Exact match to Image 1) -->
                    <div class="warehouse-header-card d-flex align-items-center justify-content-between flex-wrap gap-2"
                         data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="wh-avatar">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="fw-bold mb-0 text-white">{{ $wh->warehouse_name }}</h5>
                                    @if($wh->is_shop)
                                        <span class="badge bg-success rounded-pill px-2.5 py-1 small">
                                            <i class="fas fa-store me-1"></i> Main Shop
                                        </span>
                                    @else
                                        <span class="badge bg-primary rounded-pill px-2.5 py-1 small">
                                            <i class="fas fa-dolly me-1"></i> Storage Godown
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-white-50 mt-1">
                                    <i class="fas fa-map-marker-alt text-warning me-1"></i> {{ $wh->location ?: 'Primary Location' }}
                                </div>
                            </div>
                        </div>

                        <!-- Right Stats (Image 1 Style with clean separation) -->
                        <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto flex-nowrap">
                            <div class="text-end px-3 border-end border-secondary border-opacity-50">
                                <div class="fs-4 fw-bold font-monospace text-white">{{ $whTotalUnits }}</div>
                                <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.6px; white-space: nowrap;">TOTAL UNITS</div>
                            </div>
                            <div class="text-end px-3">
                                <div class="fs-4 fw-bold font-monospace text-white">{{ $whProdCount }}</div>
                                <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.6px; white-space: nowrap;">PRODUCTS</div>
                            </div>
                            <div class="text-white-50 fs-5 ps-1">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table Inside Warehouse -->
                    <div id="{{ $collapseId }}" class="collapse show" data-bs-parent="#warehouseStockAccordion">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr style="border-bottom: 2px solid #e2e8f0;">
                                            <th class="ps-4 text-uppercase small fw-bold" style="width: 45%;">PRODUCT</th>
                                            <th class="text-uppercase small fw-bold" style="width: 20%;">CODE</th>
                                            <th class="text-center text-uppercase small fw-bold" style="width: 20%;">QUANTITY</th>
                                            <th class="pe-4 text-end text-uppercase small fw-bold" style="width: 15%;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $allStocks = $wh->warehouseStocks;
                                            $totalWhCount = $allStocks->count();
                                            $displayStocks = $allStocks;
                                            $hasMore = false;
                                            if ($totalWhCount > 60 && !$search) {
                                                $displayStocks = $allStocks->take(60);
                                                $hasMore = true;
                                            }
                                        @endphp
                                        @forelse($displayStocks as $stock)
                                            @php
                                                $qtyVal = (float)$stock->total_pieces;
                                                $fmtQty = rtrim(rtrim(number_format($qtyVal, 2, '.', ','), '0'), '.');
                                                if ($fmtQty === '' || $fmtQty === '-0') $fmtQty = '0';
                                                $unit = $stock->product->unit->name ?? 'Pcs';
                                                $itemName = $stock->product->item_name ?? 'N/A';
                                                $firstLetter = strtoupper(substr(trim($itemName), 0, 1)) ?: 'P';
                                            @endphp
                                            <tr>
                                                <!-- Product (Image 1 Style with Letter Icon) -->
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="prod-letter-icon me-3">
                                                            {{ $firstLetter }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark fs-6">{{ $itemName }}</div>
                                                            @if($stock->remarks)
                                                                <span class="small text-muted fst-italic">{{ $stock->remarks }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Code (Image 1 Style rounded badge) -->
                                                <td>
                                                    <span class="badge bg-light text-dark border font-monospace px-2.5 py-1.5" style="font-size: 0.8rem; border-color: #cbd5e1 !important;">
                                                        {{ $stock->product->item_code ?? '--' }}
                                                    </span>
                                                </td>

                                                <!-- Quantity (Clean badge with decimals & real unit) -->
                                                <td class="text-center">
                                                    @if($qtyVal > 0)
                                                        <span class="badge bg-white text-success border border-success stock-badge-qty font-monospace">
                                                            {{ $fmtQty }} <span class="fw-normal text-muted small ms-1">{{ $unit }}</span>
                                                        </span>
                                                    @elseif($qtyVal < 0)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger stock-badge-qty font-monospace">
                                                            {{ $fmtQty }} <span class="fw-normal text-muted small ms-1">{{ $unit }}</span>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-secondary border stock-badge-qty font-monospace">
                                                            0 <span class="fw-normal text-muted small ms-1">{{ $unit }}</span>
                                                        </span>
                                                    @endif
                                                </td>

                                                <!-- Action (Adjust / View Breakdown Button) -->
                                                <td class="pe-4 text-end">
                                                    <a href="{{ route('warehouse_stocks.edit', $stock->id) }}" 
                                                       class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold shadow-sm"
                                                       title="View Breakdown / Adjust Stock">
                                                        <i class="fas fa-eye me-1"></i> View Breakdown
                                                    </a>
                                                    @can('warehouse.stock.delete')
                                                        <form action="{{ route('warehouse_stocks.destroy', $stock->id) }}" method="POST" 
                                                              class="d-inline-block ms-1" onsubmit="return confirm('Delete this stock record?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-box-open fa-2x mb-2 text-secondary opacity-50"></i>
                                                    <div>No products currently recorded in this location.</div>
                                                    <a href="{{ route('warehouse_stocks.create') }}" class="btn btn-sm btn-success rounded-pill px-3 mt-2 fw-bold">
                                                        <i class="fas fa-plus me-1"></i> Add Opening Stock
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse

                                        @if($hasMore)
                                            <tr>
                                                <td colspan="4" class="text-center py-3 bg-light text-muted small">
                                                    <i class="fas fa-info-circle me-1"></i> Showing first 60 of {{ $totalWhCount }} items in this location. 
                                                    Use the search bar above to filter by name or code, or view in <a href="{{ route('warehouse_stocks.index', ['view' => 'products', 'warehouse_id' => $wh->id]) }}" class="fw-bold text-primary">Products List</a>.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                    <i class="fas fa-warehouse fa-3x mb-3 text-secondary opacity-50"></i>
                    <h5>No warehouses or matching stock records found.</h5>
                    <p class="small text-muted mb-3">Add opening stock to populate your warehouses.</p>
                    <div>
                        <a href="{{ route('warehouse_stocks.create') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i class="fas fa-plus me-1"></i> Add Opening Stock
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

    @else
        <!-- ========================================== -->
        <!-- 2. PRODUCTS VIEW (FLAT TABLE VIEW)         -->
        <!-- ========================================== -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4 text-uppercase small fw-bold">#</th>
                                <th class="text-uppercase small fw-bold">Location</th>
                                <th class="text-uppercase small fw-bold">Product</th>
                                <th class="text-uppercase small fw-bold">Code</th>
                                <th class="text-center text-uppercase small fw-bold">Available Quantity</th>
                                <th class="pe-4 text-end text-uppercase small fw-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $stock)
                                @php
                                    $qtyVal = (float)$stock->total_pieces;
                                    $fmtQty = rtrim(rtrim(number_format($qtyVal, 2, '.', ','), '0'), '.');
                                    if ($fmtQty === '' || $fmtQty === '-0') $fmtQty = '0';
                                    $unit = $stock->product->unit->name ?? 'Pcs';
                                    $itemName = $stock->product->item_name ?? 'N/A';
                                    $firstLetter = strtoupper(substr(trim($itemName), 0, 1)) ?: 'P';
                                @endphp
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $stocks->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if($stock->warehouse && $stock->warehouse->is_shop)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                                <i class="fas fa-store me-1"></i> {{ $stock->warehouse->warehouse_name }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                                <i class="fas fa-warehouse me-1"></i> {{ $stock->warehouse->warehouse_name ?? 'N/A' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="prod-letter-icon me-3">
                                                {{ $firstLetter }}
                                            </div>
                                            <div class="fw-bold text-dark">{{ $itemName }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace px-2.5 py-1.5" style="border-color: #cbd5e1 !important;">
                                            {{ $stock->product->item_code ?? '--' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($qtyVal > 0)
                                            <span class="badge bg-white text-success border border-success stock-badge-qty font-monospace">
                                                {{ $fmtQty }} <span class="fw-normal text-muted small ms-1">{{ $unit }}</span>
                                            </span>
                                        @elseif($qtyVal < 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger stock-badge-qty font-monospace">
                                                {{ $fmtQty }} <span class="fw-normal text-muted small ms-1">{{ $unit }}</span>
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border stock-badge-qty font-monospace">
                                                0 <span class="fw-normal text-muted small ms-1">{{ $unit }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('warehouse_stocks.edit', $stock->id) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                                            <i class="fas fa-eye me-1"></i> View Breakdown
                                        </a>
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
        </div>
    @endif
</div>
@endsection
