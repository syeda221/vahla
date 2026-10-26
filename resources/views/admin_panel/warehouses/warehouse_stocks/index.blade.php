@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ── Warehouse Stocks ERP Design System ── */
    :root {
        --wh-primary:    #4f46e5;
        --wh-primary-lt: #eef2ff;
        --wh-success:    #059669;
        --wh-success-lt: #ecfdf5;
        --wh-warning:    #d97706;
        --wh-warning-lt: #fffbeb;
        --wh-danger:     #dc2626;
        --wh-danger-lt:  #fef2f2;
        --wh-info:       #0284c7;
        --wh-info-lt:    #e0f2fe;
        --wh-purple:     #7c3aed;
        --wh-purple-lt:  #f5f3ff;
        --wh-border:     #e2e8f0;
        --wh-bg:         #f8fafc;
        --wh-card-bg:    #ffffff;
        --wh-text:       #1e293b;
        --wh-muted:      #64748b;
        --wh-radius:     10px;
        --wh-shadow:     0 1px 3px rgba(0,0,0,.04);
        --wh-shadow-md:  0 4px 14px rgba(0,0,0,.07);
    }

    /* Page Container */
    .wh-page-container {
        padding: 16px 20px;
        background: var(--wh-bg);
        min-height: calc(100vh - 75px);
        width: 100%;
        box-sizing: border-box;
    }

    /* Modern ERP Header */
    .wh-header-card {
        background: var(--wh-card-bg);
        border: 1px solid var(--wh-border);
        border-radius: var(--wh-radius);
        box-shadow: var(--wh-shadow);
        padding: 12px 18px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .wh-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--wh-primary-lt);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* 5 KPI Stat Cards */
    .wh-stat-card {
        background: var(--wh-card-bg);
        border: 1px solid var(--wh-border);
        border-radius: var(--wh-radius);
        box-shadow: var(--wh-shadow);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .wh-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--wh-shadow-md);
    }
    .wh-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .wh-stat-content {
        min-width: 0;
        flex: 1;
    }
    .wh-stat-label {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--wh-muted);
        margin-bottom: 2px;
    }
    .wh-stat-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--wh-text);
        line-height: 1.15;
    }
    .wh-unit {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--wh-muted);
        margin-left: 2px;
    }
    .wh-stat-sub {
        font-size: 0.7rem;
        color: var(--wh-muted);
        margin-top: 2px;
        line-height: 1.2;
    }

    /* Navigation & Filter Bar */
    .filter-bar-card {
        background: var(--wh-card-bg);
        border: 1px solid var(--wh-border);
        border-radius: var(--wh-radius);
        padding: 8px 14px;
        box-shadow: var(--wh-shadow);
        margin-bottom: 14px;
    }

    .nav-tab-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid var(--wh-border);
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }

    .nav-tab-pill:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .nav-tab-pill.active {
        background: var(--wh-primary);
        border-color: var(--wh-primary);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.22);
    }

    .custom-search-input {
        height: 34px;
        border-radius: 6px;
        padding-left: 32px;
        font-size: 0.82rem;
        border: 1px solid var(--wh-border);
        transition: all 0.15s ease;
    }

    .custom-search-input:focus {
        border-color: var(--wh-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    /* Warehouse Accordion Cards */
    .warehouse-accordion-item {
        border-radius: var(--wh-radius);
        overflow: hidden;
        margin-bottom: 10px;
        border: 1px solid var(--wh-border);
        background: #ffffff;
        box-shadow: var(--wh-shadow);
        transition: all 0.15s ease;
    }

    .warehouse-accordion-item:hover {
        border-color: #cbd5e1;
    }

    .warehouse-header-btn {
        background: #ffffff;
        padding: 11px 18px;
        color: var(--wh-text);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid transparent;
        transition: background-color 0.15s ease;
        user-select: none;
    }

    .warehouse-header-btn:hover {
        background: #f8fafc;
    }

    .warehouse-accordion-item.expanded .warehouse-header-btn {
        background: #f8fafc;
        border-bottom-color: var(--wh-border);
    }

    .wh-chevron {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.25s ease, background 0.15s ease;
    }

    .warehouse-accordion-item.expanded .wh-chevron {
        transform: rotate(180deg);
        background: var(--wh-primary-lt);
        color: var(--wh-primary);
    }

    .warehouse-content-body {
        background: #ffffff;
        padding: 14px 18px;
    }

    /* Product Mini Tables */
    .table-custom-wh th {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
        border-top: none;
        border-bottom: 1.5px solid var(--wh-border);
        padding: 8px 12px;
    }

    .table-custom-wh td {
        padding: 8px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.83rem;
    }

    .badge-box-qty {
        background: var(--wh-info-lt);
        color: var(--wh-info);
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 5px;
        font-size: 0.78rem;
        display: inline-block;
    }

    .badge-pieces-qty {
        color: var(--wh-success);
        font-weight: 700;
        font-size: 0.88rem;
    }

    /* Modal Styles */
    .premium-modal .modal-content {
        border-radius: 14px;
        border: none;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.2);
    }

    .premium-modal .modal-header {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        color: white;
        padding: 14px 20px;
    }

    .premium-modal .modal-body {
        padding: 20px;
        background: #f8fafc;
    }

    .info-label {
        font-size: 0.74rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    .big-input {
        font-size: 1.15rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
    }

    .details-card {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 10px;
        margin-bottom: 10px;
        display: none;
    }

    .calc-box {
        background: #ffffff;
        border: 1px solid var(--wh-border);
        border-radius: 10px;
        padding: 10px;
        text-align: center;
    }

    .calc-number {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--wh-info);
    }

    .select2-container {
        width: 100% !important;
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        display: flex;
        align-items: center;
        border-color: #cbd5e1;
        border-radius: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    /* Desktop View Constraints (min-width: 992px) */
    @media (min-width: 992px) {
        #mobileFlatProductsWrapper,
        .mobile-view-toggle {
            display: none !important;
        }
        #desktopFlatTableWrapper {
            display: block !important;
        }
    }

    /* Tablet & Mobile (max-width: 991.98px) */
    @media (max-width: 991.98px) {
        html, body {
            max-width: 100vw !important;
            width: 100% !important;
            overflow-x: hidden !important;
            position: relative;
        }
        .wh-page-container {
            padding: 12px 8px !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
            box-sizing: border-box !important;
        }
        .wh-header-card {
            padding: 10px 12px !important;
            margin-bottom: 12px !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .wh-header-card .btn {
            width: 100% !important;
            justify-content: center !important;
        }
        .wh-stat-card {
            padding: 10px 12px !important;
            gap: 8px !important;
            min-width: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .wh-stat-icon {
            width: 32px !important;
            height: 32px !important;
            font-size: 0.95rem !important;
        }
        .wh-stat-value {
            font-size: 1.05rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .wh-stat-label {
            font-size: 0.62rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .wh-stat-sub {
            font-size: 0.65rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .filter-bar-card {
            padding: 10px 12px !important;
            margin-bottom: 12px !important;
        }
        .filter-bar-card > div {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .filter-bar-card .nav-tab-pill {
            flex: 1 !important;
            justify-content: center !important;
            text-align: center !important;
            padding: 8px 12px !important;
        }
        .filter-bar-card .position-relative {
            width: 100% !important;
        }

        /* Warehouse Header on Mobile: Responsive Stacking */
        .warehouse-header-btn {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 10px 12px !important;
            gap: 8px !important;
        }
        .warehouse-header-btn > .d-flex:first-child {
            padding-right: 0 !important;
            width: 100% !important;
        }
        .warehouse-header-btn > div:last-child {
            width: 100% !important;
            border-top: 1px dashed #e2e8f0 !important;
            padding-top: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        .warehouse-header-btn > div:last-child > div:first-child {
            text-align: left !important;
            padding-right: 8px !important;
            margin-right: 8px !important;
            min-width: auto !important;
            border-right: 1px solid #e2e8f0 !important;
            flex: 1 !important;
        }
        .warehouse-header-btn > div:last-child > div:nth-child(2) {
            text-align: left !important;
            padding-right: 8px !important;
            margin-right: 8px !important;
            min-width: auto !important;
            flex: 1 !important;
        }
        .warehouse-content-body {
            padding: 10px 8px !important;
        }

        /* Flat Products Mobile */
        #desktopFlatTableWrapper.mobile-table-hidden {
            display: none !important;
        }
        #desktopFlatTableWrapper {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }
        #desktopFlatTableWrapper .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        #flatProductsTable {
            min-width: 650px !important;
            white-space: nowrap !important;
        }
        .product-mcard {
            word-break: break-word !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }

        /* Modals */
        .modal-dialog {
            margin: 10px !important;
            max-width: calc(100vw - 20px) !important;
        }
        .modal-body {
            padding: 14px !important;
            max-height: 75vh !important;
            overflow-y: auto !important;
        }
    }
</style>

<div class="wh-page-container">

    <!-- Top Header Card (ERP Theme) -->
    <div class="wh-header-card">
        <div class="d-flex align-items-center">
            <div class="wh-header-icon me-3">
                <i class="fas fa-warehouse" style="color: var(--wh-primary); font-size: 1.1rem;"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: var(--wh-text); font-size: 1.05rem; letter-spacing: -0.2px;">Warehouse Inventory Management</h5>
                <p class="mb-0 text-muted" style="font-size: 0.78rem;">
                    <i class="fas fa-map-marker-alt text-warning me-1"></i> Real-time multi-location stock levels &amp; product distribution
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.78rem; padding: 5px 12px; border-radius: 6px;">
                <i class="fas fa-sync-alt me-1"></i> Refresh Stock
            </a>
        </div>
    </div>

    <!-- 5 Summary KPI Cards (Responsive ERP Design) -->
    <div class="row g-2 mb-3">
        <!-- Card 1: TOTAL PRODUCTS -->
        <div class="col-6 col-md-4 col-xl">
            <div class="wh-stat-card">
                <div class="wh-stat-icon" style="background: var(--wh-primary-lt); color: var(--wh-primary);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="wh-stat-content">
                    <div class="wh-stat-label">TOTAL PRODUCTS</div>
                    <div class="wh-stat-value" id="kpiTotalProducts">{{ $totalProducts }}</div>
                    <div class="wh-stat-sub">Assigned items</div>
                </div>
            </div>
        </div>

        <!-- Card 2: TOTAL LIVE STOCK -->
        <div class="col-6 col-md-4 col-xl">
            <div class="wh-stat-card" style="border-left: 3px solid var(--wh-success);">
                <div class="wh-stat-icon" style="background: var(--wh-success-lt); color: var(--wh-success);">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="wh-stat-content">
                    <div class="wh-stat-label">TOTAL LIVE STOCK</div>
                    <div class="wh-stat-value" style="color: var(--wh-success);" id="kpiTotalStock">
                        {{ number_format($totalStockQuantity) }} <span class="wh-unit">Pcs</span>
                    </div>
                    <div class="wh-stat-sub">All locations combined</div>
                </div>
            </div>
        </div>

        <!-- Card 3: CARTONS & BANDALS -->
        <div class="col-6 col-md-4 col-xl">
            <div class="wh-stat-card" style="border-left: 3px solid var(--wh-warning);">
                <div class="wh-stat-icon" style="background: var(--wh-warning-lt); color: var(--wh-warning);">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="wh-stat-content">
                    <div class="wh-stat-label">CARTONS / BANDALS</div>
                    <div class="wh-stat-value" style="color: #b45309;" id="kpiCartonStock">
                        {{ number_format($totalCartonBoxes) }} <span class="wh-unit">Boxes</span>
                    </div>
                    <div class="wh-stat-sub text-truncate" title="+ {{ number_format($totalCartonLoose) }} loose pcs ({{ number_format($totalCartonPieces) }} pcs total)">
                        + {{ number_format($totalCartonLoose) }} loose
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: PIECES ONLY STOCK -->
        <div class="col-6 col-md-4 col-xl">
            <div class="wh-stat-card" style="border-left: 3px solid var(--wh-info);">
                <div class="wh-stat-icon" style="background: var(--wh-info-lt); color: var(--wh-info);">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="wh-stat-content">
                    <div class="wh-stat-label">PIECES ONLY STOCK</div>
                    <div class="wh-stat-value" style="color: var(--wh-info);" id="kpiPiecesOnlyStock">
                        {{ number_format($totalPiecesOnlyStock) }} <span class="wh-unit">Pcs</span>
                    </div>
                    <div class="wh-stat-sub">Standard single pieces</div>
                </div>
            </div>
        </div>

        <!-- Card 5: ACTIVE WAREHOUSES -->
        <div class="col-12 col-md-4 col-xl">
            <div class="wh-stat-card" style="border-left: 3px solid var(--wh-purple);">
                <div class="wh-stat-icon" style="background: var(--wh-purple-lt); color: var(--wh-purple);">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="wh-stat-content">
                    <div class="wh-stat-label">ACTIVE WAREHOUSES</div>
                    <div class="wh-stat-value" style="color: var(--wh-purple);" id="kpiActiveWarehouses">
                        {{ $activeWarehousesCount }}
                    </div>
                    <div class="wh-stat-sub">Active locations</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation & Filter Bar -->
    <div class="filter-bar-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Tabs on the left -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="nav-tab-pill active" id="tabProductsView" onclick="switchView('products')">
                    <i class="fas fa-box me-1"></i> Products
                </button>
                <button type="button" class="nav-tab-pill" id="tabWarehousesView" onclick="switchView('warehouses')">
                    <i class="fas fa-warehouse me-1"></i> Warehouses
                </button>
            </div>

            <!-- Search input on the right -->
            <div class="position-relative" style="width: 260px; max-width: 100%;">
                <i class="fas fa-search position-absolute text-muted" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 0.8rem;"></i>
                <input type="text" id="liveSearchInput" class="form-control custom-search-input" placeholder="Search warehouse or product..." style="padding-left: 30px;">
            </div>
        </div>
    </div>

    <!-- MAIN VIEW 1: WAREHOUSE-WISE VIEW -->
    <div id="warehouseWiseContainer" style="display: none;">
        @forelse($warehouses as $warehouse)
            @php
                $whTotalPieces = $warehouse->total_units ?? 0;
                $whProductCount = $warehouse->products_count ?? 0;
                $branchName = $warehouse->branch ? $warehouse->branch->name : null;
                $locationText = $warehouse->location ?: ($warehouse->branch->address ?? 'Main Branch');
                $displayName = $warehouse->warehouse_name . ($branchName ? " ({$branchName}) — {$warehouse->warehouse_name}" : '');
            @endphp
            <div class="warehouse-accordion-item" 
                 data-wh-id="{{ $warehouse->id }}"
                 data-wh-name="{{ strtolower($warehouse->warehouse_name) }}"
                 data-branch-id="{{ $warehouse->branch_id ?? '' }}"
                 data-location="{{ strtolower($warehouse->location ?? '') }}">
                
                <!-- Warehouse Header Row (Clean ERP Light Theme) -->
                <div class="warehouse-header-btn" onclick="toggleWarehouseAccordion({{ $warehouse->id }})">
                    <div class="d-flex align-items-center" style="min-width: 0; padding-right: 15px;">
                        <div style="margin-right: 12px; flex-shrink: 0; width: 36px; height: 36px; border-radius: 8px; background: #fffbeb; border: 1px solid #fef3c7; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-warehouse" style="color: #d97706; font-size: 1rem;"></i>
                        </div>
                        <div style="min-width: 0;">
                            <div class="fw-bold mb-0 text-truncate" style="font-size: 0.92rem; color: #1e293b;">{{ $displayName }}</div>
                            <div class="small text-truncate" style="color: #64748b; font-size: 0.76rem;">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $locationText }}
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; flex-shrink: 0;">
                        <!-- Total Units -->
                        <div style="text-align: right; padding-right: 15px; margin-right: 15px; border-right: 1px solid #e2e8f0; min-width: 80px;">
                            <div style="color: #059669; font-weight: 800; font-size: 1.05rem; line-height: 1.1;">
                                {{ number_format($whTotalPieces) }}
                            </div>
                            <div style="color: #64748b; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; margin-top: 2px;">
                                TOTAL UNITS
                            </div>
                        </div>

                        <!-- Products -->
                        <div style="text-align: right; padding-right: 12px; min-width: 65px;">
                            <div style="color: #1e293b; font-weight: 800; font-size: 1.05rem; line-height: 1.1;">
                                {{ $whProductCount }}
                            </div>
                            <div style="color: #64748b; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; margin-top: 2px;">
                                PRODUCTS
                            </div>
                        </div>

                        <!-- Chevron -->
                        <div class="wh-chevron" id="chevron-{{ $warehouse->id }}">
                            <i class="fas fa-chevron-down" style="font-size: 0.78rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Products Table -->
                <div class="warehouse-content-body" id="body-{{ $warehouse->id }}" style="display: none;">
                    @if(isset($warehouse->assigned_items) && $warehouse->assigned_items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-custom-wh mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="min-width: 200px;">Product</th>
                                        <th style="min-width: 160px;">Variant</th>
                                        <th style="min-width: 120px;">Price</th>
                                        <th class="text-center" style="min-width: 200px;">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($warehouse->assigned_items as $item)
                                        @php
                                            $boxes = rtrim(rtrim(number_format((float) $item['boxes'], 2), '0'), '.');
                                            $pieces = number_format((float) $item['pieces']);
                                            $price = number_format((float) ($item['price'] ?? 0), 2);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item['product_image'])
                                                        <img src="{{ $item['product_image'] }}"
                                                             class="rounded me-2 border flex-shrink-0"
                                                             style="width: 36px; height: 36px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                                             style="width: 36px; height: 36px; font-size: 11px;">
                                                            IMG
                                                        </div>
                                                    @endif
                                                    <div style="min-width: 0;">
                                                        <div class="fw-bold text-dark text-truncate">{{ $item['product_name'] }}</div>
                                                        <div class="small text-muted text-truncate">{{ $item['item_code'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge border text-truncate" style="background: #f8fafc; color: #1e293b; font-size: 0.82rem; font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                                    <i class="fas fa-tag me-1 text-primary small"></i>{{ $item['variant_name'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">Rs. {{ $price }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $itemFmt = $item['formatted'] ?? null;
                                                @endphp
                                                @if(!empty($item['is_piece_only']))
                                                    <span class="badge" style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 6px 14px; border-radius: 8px; font-size: 0.86rem; border: 1px solid #bbf7d0;">
                                                        <i class="fas fa-layer-group me-1 text-success"></i> {{ $itemFmt['main'] ?? ($pieces . ' Pcs') }}
                                                    </span>
                                                @elseif($itemFmt)
                                                    <div class="fw-bold" style="color: #047857; font-size: 0.92rem; line-height: 1.2;">
                                                        {{ $itemFmt['main'] }}
                                                    </div>
                                                    @if(!empty($itemFmt['sub']))
                                                        <div class="text-muted" style="font-size: 0.74rem; line-height: 1.2; margin-top: 2px;">
                                                            {{ $itemFmt['sub'] }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge" style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 6px 14px; border-radius: 8px; font-size: 0.86rem; border: 1px solid #bbf7d0;">
                                                        {{ $pieces }} Pcs
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Info for Warehouse -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <span class="text-muted small">
                                Showing <strong>{{ $warehouse->assigned_items->count() }}</strong> assigned variant item(s) in this warehouse
                            </span>
                        </div>
                    @else
                        <!-- Empty Warehouse State -->
                        <div class="text-center py-4">
                            <div class="text-muted mb-2"><i class="fas fa-box-open fa-3x text-secondary opacity-50"></i></div>
                            <h6 class="text-muted fw-bold">No Products Assigned To This Warehouse</h6>
                            <p class="text-muted small mb-0">There are currently no products or variants assigned to {{ $warehouse->warehouse_name }}.</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                <i class="fas fa-warehouse fa-3x text-muted mb-3 opacity-50"></i>
                <h5 class="fw-bold text-muted">No Warehouses Found</h5>
                <p class="text-muted small">Try clearing your search.</p>
            </div>
        @endforelse
    </div>

    <!-- MAIN VIEW 2: FLAT PRODUCTS VIEW -->
    <div id="flatProductsContainer">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden" style="border: 1px solid var(--wh-border) !important;">
            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--wh-border);">
                <div class="d-flex align-items-center gap-2">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--wh-primary); display: inline-block;"></span>
                    <h6 class="mb-0 fw-bold" style="font-size: 0.92rem; color: var(--wh-text);">All Assigned Product Variants</h6>
                </div>
                <span class="badge" style="background: var(--wh-primary-lt); color: var(--wh-primary); font-size: 0.76rem; font-weight: 700; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px;">
                    {{ count($groupedAssignedItems) }} Records
                </span>
            </div>
            
            <div class="card-body p-2 p-md-0">
                {{-- Mobile View Switcher for Products View --}}
                <div class="d-flex justify-content-between align-items-center mobile-view-toggle pb-2 mb-2 border-bottom">
                    <span class="small text-muted font-weight-bold">
                        <i class="fas fa-th-list me-1"></i> View Mode
                    </span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnShowProdCards">
                            <i class="fas fa-th-large me-1"></i> Cards
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnShowProdTable">
                            <i class="fas fa-table me-1"></i> Table
                        </button>
                    </div>
                </div>

                {{-- Mobile Products Cards View --}}
                <div id="mobileFlatProductsWrapper">
                    <div class="mobile-products-list">
                        @foreach ($groupedAssignedItems as $item)
                            @php
                                $boxes = rtrim(rtrim(number_format((float) $item['total_boxes'], 2), '0'), '.');
                                $pieces = number_format((float) $item['total_pieces']);
                                $price = number_format((float) ($item['price'] ?? 0), 2);
                                $locCount = $item['locations_count'];
                                $totFmt = $item['total_formatted'] ?? null;
                            @endphp
                            <div class="product-mcard mb-2 p-3 bg-white border rounded-3 shadow-sm" data-search="{{ strtolower($item['product_name'] . ' ' . $item['item_code'] . ' ' . $item['variant_name']) }}">
                                <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                    @if ($item['product_image'])
                                        <img src="{{ $item['product_image'] }}" class="rounded me-2 border flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 40px; height: 40px; font-size: 11px;">IMG</div>
                                    @endif
                                    <div style="min-width: 0; flex: 1;">
                                        <div class="fw-bold text-dark text-truncate">{{ $item['product_name'] }}</div>
                                        <div class="small text-muted d-flex align-items-center gap-1 flex-wrap">
                                            <code>{{ $item['item_code'] }}</code>
                                            <span>&bull;</span>
                                            <span class="badge border bg-light text-dark">{{ $item['variant_name'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Sale Price:</span>
                                    <span class="fw-bold text-dark">Rs. {{ $price }}</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Total Quantity:</span>
                                    <div>
                                        @if(!empty($item['is_piece_only']))
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">{{ $totFmt['main'] ?? ($pieces . ' Pcs') }}</span>
                                        @elseif($totFmt)
                                            <span class="fw-bold text-success">{{ $totFmt['main'] }}</span>
                                            @if(!empty($totFmt['sub']))
                                                <small class="text-muted d-block text-end">{{ $totFmt['sub'] }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">{{ $pieces }} Pcs</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-2 border-top d-flex justify-content-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1"
                                            data-toggle="modal"
                                            data-target="#warehouseLocationsModal"
                                            onclick="openWarehouseLocationsModal('{{ $item['row_key'] }}')">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        <span>Locations</span>
                                        <span class="badge rounded-pill bg-primary text-white ms-1">{{ $locCount }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Desktop Flat Table View --}}
                <div id="desktopFlatTableWrapper" class="mobile-table-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="flatProductsTable">
                            <thead style="background: #f8fafc; border-bottom: 1.5px solid var(--wh-border);">
                                <tr>
                                    <th class="text-uppercase fw-bold px-3 py-2" style="font-size: 0.72rem; color: #64748b; width: 45px;">#</th>
                                    <th class="text-uppercase fw-bold py-2" style="font-size: 0.72rem; color: #64748b; min-width: 190px;">Product</th>
                                    <th class="text-uppercase fw-bold py-2" style="font-size: 0.72rem; color: #64748b; min-width: 140px;">Variant</th>
                                    <th class="text-uppercase fw-bold py-2" style="font-size: 0.72rem; color: #64748b; min-width: 200px;">Locations / Warehouses</th>
                                    <th class="text-uppercase fw-bold py-2" style="font-size: 0.72rem; color: #64748b; min-width: 110px;">Price</th>
                                    <th class="text-uppercase fw-bold text-center py-2" style="font-size: 0.72rem; color: #64748b; min-width: 170px;">Total Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedAssignedItems as $item)
                                    @php
                                        $boxes = rtrim(rtrim(number_format((float) $item['total_boxes'], 2), '0'), '.');
                                        $pieces = number_format((float) $item['total_pieces']);
                                        $price = number_format((float) ($item['price'] ?? 0), 2);
                                        $locCount = $item['locations_count'];
                                    @endphp
                                    <tr>
                                        <td class="px-3">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($item['product_image'])
                                                    <img src="{{ $item['product_image'] }}"
                                                         class="rounded me-2 border flex-shrink-0"
                                                         style="width: 34px; height: 34px; object-fit: cover;">
                                                @else
                                                    <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                                         style="width: 34px; height: 34px; font-size: 10px;">IMG</div>
                                                @endif
                                                <div style="min-width: 0;">
                                                    <div class="fw-bold text-dark text-truncate">{{ $item['product_name'] }}</div>
                                                    <div class="small text-muted text-truncate">{{ $item['item_code'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge border text-truncate" style="background: #f8fafc; color: #1e293b; font-size: 0.82rem; font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                                <i class="fas fa-tag me-1 text-primary small"></i>{{ $item['variant_name'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm shadow-sm d-inline-flex align-items-center gap-1"
                                                    data-toggle="modal"
                                                    data-target="#warehouseLocationsModal"
                                                    onclick="openWarehouseLocationsModal('{{ $item['row_key'] }}')"
                                                    style="background: #eef2ff; color: #4338ca; border: 1.5px solid #c7d2fe; font-size: 0.8rem; font-weight: 700; padding: 5px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                                    onmouseover="this.style.background='#4338ca'; this.style.color='#fff';"
                                                    onmouseout="this.style.background='#eef2ff'; this.style.color='#4338ca';">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <span>View Locations</span>
                                                <span class="badge rounded-pill bg-primary text-white ms-1" style="font-size: 0.72rem; padding: 2px 7px;">{{ $locCount }}</span>
                                            </button>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark" style="font-size: 0.92rem;">Rs. {{ $price }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $totFmt = $item['total_formatted'] ?? null;
                                            @endphp
                                            @if(!empty($item['is_piece_only']))
                                                <span class="badge" style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 7px 16px; border-radius: 8px; font-size: 0.88rem; border: 1px solid #bbf7d0;">
                                                    <i class="fas fa-layer-group me-1 text-success"></i> {{ $totFmt['main'] ?? ($pieces . ' Pcs') }}
                                                </span>
                                            @elseif($totFmt)
                                                <div class="fw-bold" style="color: #047857; font-size: 0.95rem; line-height: 1.2;">
                                                    {{ $totFmt['main'] }}
                                                </div>
                                                @if(!empty($totFmt['sub']))
                                                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2; margin-top: 2px;">
                                                        {{ $totFmt['sub'] }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge" style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 7px 16px; border-radius: 8px; font-size: 0.88rem; border: 1px solid #bbf7d0;">
                                                    {{ $pieces }} Pcs
                                                </span>
                                            @endif
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

</div>

<!-- Smart Modal (Add & Edit Stock) -->
<div class="modal fade premium-modal" id="stockModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Add Stock</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem; background: transparent; border: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockForm">
                    @csrf
                    <input type="hidden" id="stock_id" name="stock_id">
                    <input type="hidden" id="method_field" name="_method" value="POST">

                    <div class="row g-3">
                        <!-- Warehouse -->
                        <div class="col-md-6 mb-3">
                            <label class="info-label">Warehouse <span class="text-danger">*</span></label>
                            <select id="warehouse_id" name="warehouse_id" class="form-control" required>
                                <option value="">Select Warehouse</option>
                            </select>
                        </div>

                        <!-- Product -->
                        <div class="col-md-6 mb-3">
                            <label class="info-label">Product <span class="text-danger">*</span></label>
                            <select id="product_id" name="product_id" class="form-control" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                    </div>

                    <!-- Product Info Card -->
                    <div id="product-details" class="details-card">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img id="prod-img" src="" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; display: none;">
                                <div id="prod-icon" class="text-secondary"><i class="fas fa-box fa-2x"></i></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark" id="prod-name">--</div>
                                <div class="d-flex small text-muted gap-3 mt-1 flex-wrap">
                                    <span>Code: <b id="prod-code">--</b></span>
                                    <span>PPB: <b id="prod-ppb" class="text-primary">--</b></span>
                                    <span>Current Stock: <b id="prod-current" class="text-success">--</b></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity Input & Box Calculation -->
                    <div class="row align-items-end g-3">
                        <div class="col-md-6 mb-3">
                            <label class="info-label" id="qty-label">Total Pieces <span class="text-danger">*</span></label>
                            <input type="number" name="quantity_input" id="quantity_input"
                                   class="form-control big-input" placeholder="0" min="0" disabled required>
                            <small class="text-muted" id="qty-help">Enter total quantity in pieces.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="calc-box">
                                <div class="text-uppercase small text-muted fw-bold mb-1">Calculated Total Boxes</div>
                                <div class="calc-number" id="calc-result">0 Boxes</div>
                                <input type="hidden" name="total_pieces" id="total_pieces" value="0">
                                <input type="hidden" name="total_box" id="total_box" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="info-label">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>

                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-success py-3 fw-bold shadow-sm" id="btn-save">
                            <span class="btn-text">Save Stock Entry</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: View Stock Across Warehouses / Locations -->
<div class="modal fade" id="warehouseLocationsModal" tabindex="-1" role="dialog" aria-labelledby="warehouseLocationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 860px; width: 95%;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <!-- Header (Deep Navy Blue) -->
            <div class="modal-header py-3 px-3 px-md-4 border-0" style="background: #1c3554;">
                <div class="d-flex align-items-center gap-3" style="min-width: 0; max-width: 85%;">
                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 42px; height: 42px; background: #facc15;">
                        <i class="fas fa-warehouse" style="color: #1e293b; font-size: 1.2rem;"></i>
                    </div>
                    <div style="min-width: 0;">
                        <h5 class="modal-title text-white fw-bold text-uppercase mb-1 text-truncate" id="wh_loc_product_name" style="font-size: 1.05rem; letter-spacing: 0.3px;">
                            Product Name
                        </h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap" id="wh_loc_subtitle_wrap">
                            <span class="badge" id="wh_loc_product_code" style="background: #284a75; color: #fff; font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 4px;">
                                ITEM-0000
                            </span>
                            <span id="wh_loc_product_meta" class="text-truncate" style="color: #94b8e8; font-size: 0.78rem; font-weight: 600; text-transform: uppercase;">
                                • PIECES
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.85; font-size: 1.6rem; background: transparent; border: none; line-height: 1; margin-top: -10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3 p-md-4" style="background: #ffffff;">
                <!-- 3 Top Stat Cards -->
                <div class="row g-2 g-md-3 mb-3 mb-md-4">
                    <!-- Total Available Stock -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background: #f0fdf4; border: 1.5px solid #bbf7d0;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #16a34a; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Total Available Stock
                            </div>
                            <div class="d-flex align-items-baseline gap-1 flex-wrap" id="wh_loc_card_stock" style="color: #15803d; line-height: 1.2;">
                                0 Pcs
                            </div>
                        </div>
                    </div>
                    <!-- Sale Price -->
                    <div class="col-6 col-md-4">
                        <div class="p-3 rounded-3" style="background: #eff6ff; border: 1.5px solid #bfdbfe;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #2563eb; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Sale Price
                            </div>
                            <div class="fw-bold" id="wh_loc_card_price" style="color: #1d4ed8; font-size: 1.15rem; line-height: 1.2;">
                                Rs 0.00
                            </div>
                        </div>
                    </div>
                    <!-- Total Stock Value -->
                    <div class="col-6 col-md-4">
                        <div class="p-3 rounded-3" style="background: #ffffff; border: 1.5px solid #e2e8f0;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Total Stock Value
                            </div>
                            <div class="fw-bold" id="wh_loc_card_value" style="color: #0f172a; font-size: 1.15rem; line-height: 1.2;">
                                Rs 0.00
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Header -->
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt text-primary" style="font-size: 0.95rem;"></i>
                        <span style="font-weight: 800; color: #1e3a8a; font-size: 0.88rem; letter-spacing: 0.5px;">
                            STOCK BREAKDOWN BY WAREHOUSE
                        </span>
                    </div>
                    <span class="badge rounded-pill text-white" id="wh_loc_pill_count" style="background-color: #2563eb; font-size: 0.78rem; padding: 5px 12px; font-weight: 700;">
                        0 Locations
                    </span>
                </div>

                <!-- Warehouses Table -->
                <div class="border rounded-3 table-responsive" style="overflow-x: auto; border-color: #e2e8f0 !important;">
                    <table class="table align-middle mb-0" style="min-width: 480px; font-size: 0.86rem;">
                        <thead style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
                            <tr>
                                <th style="width: 50px; color: #64748b; font-size: 0.74rem; font-weight: 700; padding: 10px 14px;">#</th>
                                <th style="color: #64748b; font-size: 0.74rem; font-weight: 700; padding: 10px 14px;">WAREHOUSE / LOCATION</th>
                                <th class="text-center" style="color: #64748b; font-size: 0.74rem; font-weight: 700; padding: 10px 14px;">AVAILABLE STOCK</th>
                                <th class="text-end" style="color: #64748b; font-size: 0.74rem; font-weight: 700; padding: 10px 14px;">STOCK VALUE</th>
                            </tr>
                        </thead>
                        <tbody id="wh_loc_table_body">
                            <!-- Populated dynamically via openWarehouseLocationsModal() -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer py-2 px-3 px-md-4 border-top" style="background: #f8fafc;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <!-- Select2 -->
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

    <script>
        let piecesPerBox = 0;
        let selectedProduct = null;
        let isEditMode = false;

        $(document).ready(function() {
            initSelect2();

            // Product Selection Logic
            $('#product_id').on('select2:select', function(e) {
                handleProductSelect(e.params.data);
            });

            // Warehouse Change logic
            $('#warehouse_id').on('change', function() {
                if (selectedProduct && $(this).val()) {
                    fetchCurrentStock($(this).val(), selectedProduct.id);
                }
            });

            // Calculation
            $('#quantity_input').on('input', function() {
                calculateTotal();
            });

            // Reset modal on close
            $('#stockModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            // Live Search Filter
            $('#liveSearchInput').on('keyup input', function() {
                filterWarehouseCards();
            });

            // Mobile View Switcher (Cards vs Table for Flat Products)
            $('#btnShowProdCards').on('click', function() {
                $(this).addClass('active');
                $('#btnShowProdTable').removeClass('active');
                $('#mobileFlatProductsWrapper').show();
                $('#desktopFlatTableWrapper').addClass('mobile-table-hidden').hide();
            });

            $('#btnShowProdTable').on('click', function() {
                $(this).addClass('active');
                $('#btnShowProdCards').removeClass('active');
                $('#mobileFlatProductsWrapper').hide();
                $('#desktopFlatTableWrapper').removeClass('mobile-table-hidden').show();
            });

            // Auto-expand first warehouse if exists
            let firstWh = $('.warehouse-accordion-item').first();
            if (firstWh.length > 0) {
                let id = firstWh.data('wh-id');
                toggleWarehouseAccordion(id);
            }
        });

        // Toggle Accordion
        function toggleWarehouseAccordion(whId) {
            let body = $('#body-' + whId);
            let item = body.closest('.warehouse-accordion-item');
            
            body.slideToggle(200, function() {
                if (body.is(':visible')) {
                    item.addClass('expanded');
                } else {
                    item.removeClass('expanded');
                }
            });
        }

        // Switch between 'warehouses' and 'products' view
        function switchView(viewName) {
            $('.nav-tab-pill').removeClass('active');
            if (viewName === 'warehouses') {
                $('#tabWarehousesView').addClass('active');
                $('#warehouseWiseContainer').fadeIn(150);
                $('#flatProductsContainer').hide();
            } else if (viewName === 'products') {
                $('#tabProductsView').addClass('active');
                $('#warehouseWiseContainer').hide();
                $('#flatProductsContainer').fadeIn(150);
            }
        }

        // Live Client-Side Filtering
        function filterWarehouseCards() {
            let searchVal = ($('#liveSearchInput').val() || '').toLowerCase().trim();

            $('.warehouse-accordion-item').each(function() {
                let item = $(this);
                let whName = (item.data('wh-name') || '').toString();
                let fullText = item.text().toLowerCase();

                let matchSearch = !searchVal || whName.indexOf(searchVal) !== -1 || fullText.indexOf(searchVal) !== -1;

                if (matchSearch) {
                    item.show();
                } else {
                    item.hide();
                }
            });

            // Filter Flat Products (Table & Cards)
            $('#flatProductsTable tbody tr').each(function() {
                let rowText = $(this).text().toLowerCase();
                if (!searchVal || rowText.indexOf(searchVal) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('.mobile-products-list .product-mcard').each(function() {
                let sTxt = $(this).attr('data-search') || '';
                if (!searchVal || sTxt.indexOf(searchVal) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Select2 Initialization
        function initSelect2() {
            $('#warehouse_id').select2({
                dropdownParent: $('#stockModal'),
                placeholder: "Select Warehouse",
                allowClear: true,
                ajax: {
                    url: "{{ route('warehouse_stock.search-warehouses') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };
                    }
                }
            });

            $('#product_id').select2({
                dropdownParent: $('#stockModal'),
                placeholder: "Select Product",
                allowClear: true,
                ajax: {
                    url: "{{ route('warehouse_stock.search-products') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };
                    }
                }
            });
        }

        function openAddModal() {
            isEditMode = false;
            $('#modalTitle').text('Add Stock');
            $('#method_field').val('POST');
            $('#btn-save .btn-text').text('Save Stock Entry');
            $('#stockModal').modal('show');
        }

        function openAddModalForWarehouse(warehouseId, warehouseName) {
            openAddModal();
            let whOption = new Option(warehouseName, warehouseId, true, true);
            $('#warehouse_id').empty().append(whOption).trigger('change');
        }

        function editStock(id) {
            isEditMode = true;
            $('#modalTitle').text('Update Stock');
            $('#method_field').val('PUT');
            $('#btn-save .btn-text').text('Update Stock');
            $('#stock_id').val(id);

            let url = "{{ route('warehouse_stock.edit-data', ':id') }}".replace(':id', id);

            $.get(url, function(data) {
                // Populate Form
                let whOption = new Option(data.warehouse_name, data.warehouse_id, true, true);
                $('#warehouse_id').empty().append(whOption).trigger('change');

                let option = new Option(data.product_name, data.product_id, true, true);
                $('#product_id').empty().append(option).trigger('change');

                let mockData = {
                    id: data.product_id,
                    item_name: data.product_name,
                    item_code: data.product_code,
                    pieces_per_box: data.pieces_per_box,
                    image: data.image
                };
                handleProductSelect(mockData);

                $('#quantity_input').val(data.total_pieces);
                $('#total_pieces').val(data.total_pieces);

                if (data.pieces_per_box > 0 && data.total_pieces > 0) {
                    let boxes = Math.floor(data.total_pieces / data.pieces_per_box);
                    let loose = data.total_pieces % data.pieces_per_box;
                    $('#calc-result').html(`<span class="text-primary">${boxes} Boxes</span>` + (loose > 0 ? ` <span class="text-muted small">+ ${loose} Loose</span>` : ''));
                    $('#total_box').val(boxes);
                } else {
                    $('#calc-result').text(data.total_pieces + ' Pcs');
                    $('#total_box').val(0);
                }

                $('#remarks').val(data.remarks);
                $('#stockModal').modal('show');
            }).fail(function() {
                Swal.fire('Error', 'Could not fetch stock data.', 'error');
            });
        }

        function handleProductSelect(data) {
            selectedProduct = data;
            piecesPerBox = parseInt(data.pieces_per_box) || 0;

            $('#product-details').fadeIn();
            $('#prod-name').text(data.item_name);
            $('#prod-code').text(data.item_code || '--');
            $('#prod-ppb').text(piecesPerBox > 0 ? piecesPerBox : 'N/A (Loose)');

            if (data.image) {
                $('#prod-img').attr('src', data.image).show();
                $('#prod-icon').hide();
            } else {
                $('#prod-img').hide();
                $('#prod-icon').show();
            }

            $('#quantity_input').prop('disabled', false).focus();

            let whId = $('#warehouse_id').val();
            if (whId) {
                fetchCurrentStock(whId, data.id);
            } else {
                $('#prod-current').text('Select Warehouse First');
            }

            calculateTotal();
        }

        function fetchCurrentStock(warehouseId, productId) {
            if (!isEditMode) {
                $.get("{{ route('warehouse_stock.get-stock') }}", {
                    warehouse_id: warehouseId,
                    product_id: productId
                }, function(res) {
                    let currentTotal = parseInt(res.total_pieces) || 0;
                    let displayStock = currentTotal + " Pcs";

                    if (piecesPerBox > 0) {
                        let b = Math.floor(currentTotal / piecesPerBox);
                        let l = currentTotal % piecesPerBox;
                        displayStock = `${b} Boxes` + (l > 0 ? ` + ${l} Loose` : '');
                    }
                    $('#prod-current').text(displayStock);
                });
            } else {
                let currentTotal = parseInt($('#total_pieces').val()) || 0;
                let displayStock = currentTotal + " Pcs";

                if (piecesPerBox > 0) {
                    let b = Math.floor(currentTotal / piecesPerBox);
                    let l = currentTotal % piecesPerBox;
                    displayStock = `${b} Boxes` + (l > 0 ? ` + ${l} Loose` : '');
                }
                $('#prod-current').text(displayStock + " (Current)");
            }
        }

        function calculateTotal() {
            let inputQty = parseInt($('#quantity_input').val()) || 0;
            let total = inputQty;

            $('#total_pieces').val(total);

            if (piecesPerBox > 0 && total > 0) {
                let boxes = Math.floor(total / piecesPerBox);
                let loose = total % piecesPerBox;
                $('#calc-result').html(`<span class="text-primary">${boxes} Boxes</span>` + (loose > 0 ? ` <span class="text-muted small">+ ${loose} Loose</span>` : ''));
                $('#total_box').val(boxes);
            } else {
                $('#calc-result').text(total + ' Pcs');
                $('#total_box').val(0);
            }
        }

        function resetForm() {
            selectedProduct = null;
            piecesPerBox = 0;
            isEditMode = false;

            $('#stockForm')[0].reset();
            $('#warehouse_id').val(null).trigger('change');
            $('#product_id').val(null).trigger('change');
            $('#product-details').hide();
            $('#quantity_input').prop('disabled', true);
            $('#calc-result').text('0 Boxes');
            $('#stock_id').val('');
        }

        // Form Submission
        $('#stockForm').on('submit', function(e) {
            e.preventDefault();

            let url = "{{ route('warehouse_stocks.store') }}";
            if (isEditMode) {
                let id = $('#stock_id').val();
                url = "{{ route('warehouse_stocks.update', ':id') }}".replace(':id', id);
            }

            let formData = $(this).serialize();
            let btn = $('#btn-save');
            btn.prop('disabled', true);

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let msg = 'Error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire('Error', msg, 'error');
                    btn.prop('disabled', false);
                }
            });
        });

        // ── WAREHOUSE LOCATIONS MODAL LOGIC ──
        const productsLocationData = @json($productsLocationData ?? []);

        window.openWarehouseLocationsModal = function(rowKey) {
            const data = productsLocationData[rowKey];
            if (!data) return;

            // 1. Header (Product Name + Subtitle with code, category, brand, unit)
            const vTitle = (data.variant_name && data.variant_name !== 'Standard') ? ` (${data.variant_name})` : '';
            $('#wh_loc_product_name').text(data.product_name + vTitle);
            $('#wh_loc_product_code').text(data.item_code || 'ITEM-0000');

            let metaParts = [];
            if (data.variant_name && data.variant_name !== 'Standard') metaParts.push(data.variant_name);
            if (data.category_name) metaParts.push(data.category_name);
            if (data.brand_name) metaParts.push(data.brand_name);
            metaParts.push(data.unit || 'PIECES');

            $('#wh_loc_product_meta').text('• ' + metaParts.join(' • ').toUpperCase());

            // 2. The 3 Stat Cards
            const totFmt = data.total_formatted || {};
            let stockHtml = `<span style="font-size: 1.15rem; font-weight: 800;">${totFmt.main || (parseFloat(data.total_pieces || 0).toFixed(0) + ' ' + (data.unit || 'Pcs'))}</span>`;
            if (totFmt.sub) {
                stockHtml += ` <span style="font-size: 0.75rem; font-weight: 600; opacity: 0.85; white-space: nowrap;">${totFmt.sub}</span>`;
            }
            $('#wh_loc_card_stock').html(stockHtml);

            const price = parseFloat(data.price || 0);
            $('#wh_loc_card_price').text('Rs. ' + price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            const totalPieces = parseFloat(data.total_pieces || 0);
            const totalVal = totalPieces * price;
            $('#wh_loc_card_value').text('Rs. ' + totalVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // 3. Pill count
            const locCount = data.locations_count || (data.locations ? data.locations.length : 0);
            $('#wh_loc_pill_count').text(locCount + (locCount === 1 ? ' Location' : ' Locations'));

            // 4. Locations Table rows
            let tbodyHtml = '';
            if (data.locations && data.locations.length > 0) {
                data.locations.forEach((loc, index) => {
                    const fmt = loc.formatted || {};
                    const mainText = fmt.main || (parseFloat(loc.pieces || 0).toFixed(0) + ' ' + (data.unit || 'Pcs'));
                    const subText = fmt.sub ? `<div class="text-muted" style="font-size: 0.74rem; margin-top: 2px;">${fmt.sub}</div>` : '';
                    const locVal = parseFloat(loc.pieces || 0) * price;

                    tbodyHtml += `
                        <tr>
                            <td class="text-muted fw-bold" style="padding: 11px 14px; width: 50px;">${index + 1}</td>
                            <td style="padding: 11px 14px;">
                                <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.92rem;">
                                    <span>🏪</span>
                                    <span>${loc.warehouse_name || 'Warehouse'}</span>
                                    ${loc.branch_name ? `<span class="badge bg-light text-secondary border small">${loc.branch_name}</span>` : ''}
                                </div>
                                ${loc.location ? `<div class="small text-muted ps-4" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt text-danger me-1"></i>${loc.location}</div>` : ''}
                            </td>
                            <td class="text-center" style="padding: 11px 14px;">
                                <div class="fw-bold" style="color: #047857; font-size: 0.92rem; line-height: 1.2;">
                                    ${mainText}
                                </div>
                                ${subText}
                            </td>
                            <td class="text-end fw-bold text-dark" style="padding: 11px 14px; font-size: 0.9rem;">
                                Rs. ${locVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbodyHtml = `<tr><td colspan="4" class="text-center text-muted py-4">No warehouse stocks found</td></tr>`;
            }

            $('#wh_loc_table_body').html(tbodyHtml);

            showModalSafely('warehouseLocationsModal');
        };

        function showModalSafely(modalId) {
            const $m = $('#' + modalId);
            if ($m.length && typeof $m.modal === 'function') {
                $m.modal('show');
                return;
            }
            const el = document.getElementById(modalId);
            if (el) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(el) : new bootstrap.Modal(el);
                        modal.show();
                        return;
                    } catch(e) {}
                }
                el.classList.add('show');
                el.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }

        $(document).on('click', '#warehouseLocationsModal [data-dismiss="modal"], #warehouseLocationsModal [data-bs-dismiss="modal"]', function() {
            $('#warehouseLocationsModal').removeClass('show').hide();
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        });
    </script>
@endsection
