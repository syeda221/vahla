@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ── ERP Opening Stock Module Design System ── */
    :root {
        --os-primary:    #4f46e5;
        --os-primary-lt: #eef2ff;
        --os-success:    #059669;
        --os-success-lt: #ecfdf5;
        --os-warning:    #d97706;
        --os-warning-lt: #fffbeb;
        --os-danger:     #dc2626;
        --os-danger-lt:  #fef2f2;
        --os-info:       #0284c7;
        --os-info-lt:    #e0f2fe;
        --os-border:     #e2e8f0;
        --os-bg:         #f8fafc;
        --os-card-bg:    #ffffff;
        --os-text:       #1e293b;
        --os-muted:      #64748b;
        --os-radius:     12px;
        --os-shadow:     0 2px 8px rgba(0,0,0,.04);
        --os-shadow-md:  0 6px 20px rgba(0,0,0,.07);
    }

    .os-page {
        background: var(--os-bg);
        min-height: calc(100vh - 80px);
        padding: 20px 0;
    }
    .os-page .container-fluid { max-width: 100%; }

    /* ── Stats Grid ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: var(--os-card-bg);
        border-radius: var(--os-radius);
        border: 1px solid var(--os-border);
        box-shadow: var(--os-shadow);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--os-shadow-md);
    }
    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-card .stat-label {
        font-size: .72rem;
        font-weight: 700;
        color: var(--os-muted);
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .stat-card .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--os-text);
        line-height: 1.2;
    }
    .stat-card .stat-sub {
        font-size: .72rem;
        color: var(--os-muted);
        margin-top: 2px;
    }

    /* ── Main ERP Card ── */
    .os-card {
        background: var(--os-card-bg);
        border-radius: var(--os-radius);
        border: 1px solid var(--os-border);
        box-shadow: var(--os-shadow);
        overflow: hidden;
    }
    .os-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--os-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .os-card-header .page-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--os-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .os-card-header .page-sub {
        font-size: .76rem;
        color: var(--os-muted);
        margin: 0;
    }

    /* Header action buttons */
    .btn-hdr {
        border-radius: 6px;
        padding: 5px 12px;
        font-size: .78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        line-height: 1;
        border: 1px solid transparent;
        transition: all .15s ease;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-hdr-primary {
        background: var(--os-primary);
        color: #fff;
        border-color: var(--os-primary);
    }
    .btn-hdr-primary:hover {
        background: #4338ca;
        color: #fff;
        transform: translateY(-1px);
    }
    .btn-hdr-success {
        background: var(--os-success);
        color: #fff;
        border-color: var(--os-success);
    }
    .btn-hdr-success:hover {
        background: #047857;
        color: #fff;
        transform: translateY(-1px);
    }

    /* ── Filter Panel ── */
    .filter-panel {
        padding: 14px 20px;
        background: var(--os-bg);
        border-bottom: 1px solid var(--os-border);
    }
    .filter-panel .filter-heading {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: var(--os-muted);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .os-filter-row {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        width: 100%;
    }
    .os-filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 150px;
        min-width: 140px;
        max-width: 230px;
        box-sizing: border-box;
    }
    .os-filter-search {
        flex: 1 1 200px;
        max-width: 300px;
    }
    .os-filter-btns {
        flex: 0 0 auto;
    }
    .os-flabel {
        display: block;
        font-size: .70rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--os-muted);
        margin-bottom: 5px;
        white-space: nowrap;
    }
    .os-finput {
        width: 100%;
        height: 36px;
        padding: 0 10px;
        border: 1px solid var(--os-border);
        border-radius: 7px;
        font-size: .82rem;
        color: var(--os-text);
        background: #fff;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .os-finput:focus {
        border-color: var(--os-primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    }
    .search-wrap { position: relative; }
    .search-wrap .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--os-muted);
        font-size: 12px;
        pointer-events: none;
        z-index: 1;
    }
    .search-wrap .os-finput { padding-left: 30px; }

    .btn-os-filter {
        background: var(--os-primary);
        color: #fff;
        border: none;
        border-radius: 7px;
        height: 36px;
        padding: 0 14px;
        font-size: .8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-os-filter:hover { background: #4338ca; }
    .btn-os-clear {
        background: #fff;
        color: var(--os-muted);
        border: 1px solid var(--os-border);
        border-radius: 7px;
        height: 36px;
        padding: 0 12px;
        font-size: .8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s;
    }
    .btn-os-clear:hover { background: #f1f5f9; color: var(--os-text); }

    /* ── Data Table ── */
    .os-table-wrap { overflow-x: auto; }
    .os-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .82rem;
    }
    .os-table th {
        background: #f8fafc;
        padding: 10px 14px;
        font-size: .70rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--os-muted);
        border-bottom: 1px solid var(--os-border);
        white-space: nowrap;
    }
    .os-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--os-text);
        vertical-align: middle;
    }
    .os-table tr:hover td { background: #f8fafc; }

    /* Badges */
    .badge-wh {
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: .75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-qty {
        font-weight: 700;
        color: var(--os-primary);
        background: var(--os-primary-lt);
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .badge-box {
        font-weight: 600;
        color: #065f46;
        background: var(--os-success-lt);
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .badge-live {
        font-weight: 700;
        color: #0d9488;
        background: #ccfbf1;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .badge-live-zero {
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .badge-live-danger {
        font-weight: 700;
        color: #b91c1c;
        background: #fee2e2;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Action Buttons UI */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        font-size: 0.85rem;
        transition: all 0.18s ease-in-out;
        cursor: pointer;
        outline: none;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-action-edit {
        background: #f0fdf4;
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .btn-action-edit:hover {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.25);
    }
    .btn-action-delete {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .btn-action-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.25);
    }

    /* Modal styling */
    .modal-os .modal-content {
        border-radius: 14px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,.15);
    }
    .modal-os .modal-header {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        padding: 16px 22px;
        border-bottom: 1px solid rgba(255,255,255,.1);
    }
    .modal-os .modal-title {
        color: #ffffff !important;
        font-weight: 700;
        font-size: 1.15rem;
    }
    .modal-os .btn-close {
        filter: invert(1) brightness(200%);
    }

    /* Select2 dropdown inside Bootstrap Modal Fix */
    .select2-container {
        width: 100% !important;
        z-index: 99999 !important;
    }
    .select2-dropdown {
        z-index: 99999 !important;
        border: 1px solid var(--os-border);
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        border-radius: 8px;
    }
    .select2-container--default .select2-selection--single {
        height: 42px;
        display: flex;
        align-items: center;
        border: 1px solid var(--os-border);
        border-radius: 8px;
        padding: 0 12px;
        font-size: .88rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 10px;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--os-border);
        border-radius: 6px;
        padding: 6px 12px;
        outline: none;
    }
    .select2-results__option {
        padding: 8px 12px;
        font-size: .86rem;
    }

    /* Batch Table Modern ERP Select2 & Inputs Styling */
    #batchTable .select2-container {
        width: 100% !important;
    }
    #batchTable .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 4px 10px !important;
        font-size: 0.83rem !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
        transition: all .15s ease-in-out !important;
    }
    #batchTable .select2-container--default.select2-container--focus .select2-selection--single,
    #batchTable .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
        outline: none !important;
    }
    #batchTable .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        padding-right: 20px !important;
        line-height: 28px !important;
        color: #1e293b !important;
        font-weight: 500 !important;
    }
    #batchTable .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
        top: 0 !important;
    }
    #batchTable .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        opacity: 0.8 !important;
    }
    #batchTable .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-weight: 400 !important;
    }
    #batchTable input.form-control-sm {
        height: 38px !important;
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        font-size: 0.85rem !important;
        transition: all .15s ease-in-out !important;
    }
    #batchTable input.form-control-sm:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
    }
    #batchTable .badge-qty {
        display: inline-block;
        padding: 6px 12px;
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
    }
    .nav-tabs-os {
        border-bottom: 2px solid var(--os-border);
        gap: 6px;
    }
    .nav-tabs-os .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--os-muted);
        font-weight: 600;
        font-size: .85rem;
        padding: 8px 16px;
        border-radius: 0;
        background: transparent;
    }
    .nav-tabs-os .nav-link.active {
        color: var(--os-primary);
        border-bottom-color: var(--os-primary);
        background: transparent;
    }

    /* Multi-warehouse Matrix Table */
    .wh-matrix-table th {
        background: #f1f5f9;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--os-muted);
        padding: 8px 12px;
    }
    .wh-matrix-table td {
        padding: 8px 12px;
        vertical-align: middle;
    }
    .wh-matrix-card {
        border: 1px solid var(--os-border);
        border-radius: 10px;
        padding: 12px;
        background: #fff;
        transition: border-color .15s;
    }
    .wh-matrix-card:hover {
        border-color: var(--os-primary);
    }

    /* Product Preview Card */
    .prod-preview-card {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 16px;
    }

    @media (max-width: 992px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .stat-grid { grid-template-columns: 1fr; }
        .os-filter-row { flex-direction: column; }
        .os-filter-field, .os-filter-search { max-width: 100%; width: 100%; }
    }
</style>

<div class="os-page">
    <div class="container-fluid px-3 px-md-4">
        
        <!-- Top Stats Row -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <div class="stat-label">Opening Entries</div>
                    <div class="stat-value">{{ number_format($totalEntries) }}</div>
                    <div class="stat-sub">Total recorded entries</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#ecfdf5; color:#059669;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="stat-label">Total Pieces Added</div>
                    <div class="stat-value">{{ number_format($totalPieces, 2) }}</div>
                    <div class="stat-sub">Across all warehouses</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#fffbeb; color:#d97706;">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <div class="stat-label">Total Boxes / Cartons</div>
                    <div class="stat-value">{{ number_format($totalBoxes, 2) }}</div>
                    <div class="stat-sub">Cartons & Boxes total</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div>
                    <div class="stat-label">Stocked Products</div>
                    <div class="stat-value">{{ number_format($uniqueProducts) }}</div>
                    <div class="stat-sub">Products with opening stock</div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="os-card">
            
            <!-- Card Header -->
            <div class="os-card-header">
                <div>
                    <h5 class="page-title">
                        <i class="fas fa-box-open text-primary"></i> Opening Stock Management
                    </h5>
                    <p class="page-sub">Add product opening stock across single or multiple warehouses seamlessly</p>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('opening_stock.create') }}" class="btn-hdr btn-hdr-primary text-decoration-none">
                        <i class="fas fa-plus-circle me-1"></i> Add Opening Stock
                    </a>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="filter-panel">
                <div class="filter-heading">
                    <i class="fas fa-filter"></i> Filter & Search Records
                </div>
                <form method="GET" action="{{ route('opening_stock.index') }}" id="filterForm">
                    <div class="os-filter-row">
                        <!-- Warehouse Filter -->
                        <div class="os-filter-field">
                            <label class="os-flabel">Warehouse</label>
                            <select name="warehouse_id" class="os-finput form-select" onchange="this.form.submit()">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Product Filter -->
                        <div class="os-filter-field">
                            <label class="os-flabel">Product</label>
                            <select name="product_id" class="os-finput form-select" onchange="this.form.submit()">
                                <option value="">All Products</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->item_name }} ({{ $prod->item_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="os-filter-field">
                            <label class="os-flabel">Date From</label>
                            <input type="date" name="date_from" class="os-finput" value="{{ request('date_from') }}">
                        </div>

                        <!-- Date To -->
                        <div class="os-filter-field">
                            <label class="os-flabel">Date To</label>
                            <input type="date" name="date_to" class="os-finput" value="{{ request('date_to') }}">
                        </div>

                        <!-- Search Field -->
                        <div class="os-filter-field os-filter-search">
                            <label class="os-flabel">Search</label>
                            <div class="search-wrap">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" class="os-finput" placeholder="Product name, code, note..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="os-filter-btns d-flex gap-2">
                            <button type="submit" class="btn-os-filter">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('opening_stock.index') }}" class="btn-os-clear">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table of Past Opening Stock Entries -->
            <div class="os-table-wrap">
                <table class="os-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Date & Time</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Variant</th>
                            <th class="text-center">Opening Qty</th>
                            <th class="text-center">Live Stock</th>
                            <th class="text-end">Cost Price</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-end">Total Value</th>
                            <th class="text-center" style="width: 95px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedProducts as $row)
                            <tr>
                                <td>{{ $productPagination->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="fw-bold">{{ $row->latest_created_at ? $row->latest_created_at->format('d M, Y') : '-' }}</div>
                                    <div class="small text-muted">{{ $row->latest_created_at ? $row->latest_created_at->format('h:i A') : '' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($row->product_image)
                                            <img src="{{ $row->product_image }}" class="rounded" style="width:34px; height:34px; object-fit:cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width:34px; height:34px; font-size:12px; border:1px solid #e2e8f0;">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $row->product_name }}</div>
                                            <div class="small text-muted font-monospace">{{ $row->product_code ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1.5 fw-bold"
                                            onclick="openLocationsModal('{{ $row->row_key }}')"
                                            title="Click to view warehouse allocations and quantities">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        @if($row->warehouses_count == 1)
                                            <span>{{ $row->first_warehouse_name }}</span>
                                        @else
                                            <span>{{ $row->warehouses_count }} Locations</span>
                                        @endif
                                        <span class="badge bg-primary text-white rounded-circle ms-0.5" style="font-size:0.65rem; width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center;">
                                            {{ $row->warehouses_count }}
                                        </span>
                                    </button>
                                </td>
                                <td>
                                    @if(!empty($row->variant_display) && $row->variant_display !== '-')
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size:0.8rem; padding: 4px 8px;">
                                            <i class="fas fa-tag text-primary me-1"></i>{{ $row->variant_display }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $opFmt = $row->opening_formatted ?? null;
                                    @endphp
                                    @if($opFmt)
                                        <div class="fw-bold" style="color: #4338ca; font-size: 0.95rem; line-height: 1.2;">
                                            {{ $opFmt['main'] }}
                                        </div>
                                        @if(!empty($opFmt['sub']))
                                            <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2; margin-top: 2px;">
                                                {{ $opFmt['sub'] }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge-qty">
                                            {{ number_format($row->total_opening_pieces, 2) }} {{ $row->unit_name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $liveFmt = $row->live_formatted ?? null;
                                    @endphp
                                    @if($row->total_live_pieces <= 0)
                                        <span class="badge bg-danger text-white">Out of Stock</span>
                                    @elseif($liveFmt)
                                        <div class="fw-bold" style="color: #047857; font-size: 0.95rem; line-height: 1.2;">
                                            {{ $liveFmt['main'] }}
                                        </div>
                                        @if(!empty($liveFmt['sub']))
                                            <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2; margin-top: 2px;">
                                                {{ $liveFmt['sub'] }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge-live">
                                            {{ number_format($row->total_live_pieces, 2) }} {{ $row->unit_name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    Rs. {{ number_format($row->cost_price ?? 0, 2) }}
                                </td>
                                <td class="text-end fw-semibold text-primary">
                                    @if($row->sale_price > 0)
                                        Rs. {{ number_format($row->sale_price, 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rs. {{ number_format($row->total_value ?? 0, 2) }}
                                </td>
                                <td class="text-center">
                                    <div class="action-btn-group">
                                        <button type="button" class="btn-action-icon btn-action-edit" onclick="openEditStockModal('{{ $row->row_key }}')" title="Edit Opening Stock">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn-action-icon btn-action-delete" onclick="confirmDeleteProductStock('{{ $row->row_key }}', '{{ addslashes($row->product_name) }}', '{{ addslashes($row->variant_display) }}')" title="Revert & Delete Product Opening Stock">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h6>No Opening Stock entries recorded yet</h6>
                                        <p class="small mb-3">Click on <b>"Add Opening Stock"</b> to record initial inventory across your warehouses.</p>
                                        <a href="{{ route('opening_stock.create') }}" class="btn-hdr btn-hdr-primary text-decoration-none">
                                            <i class="fas fa-plus-circle me-1"></i> Add Opening Stock Now
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($productPagination->hasPages())
                <div class="p-3 border-top">
                    {{ $productPagination->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 0. WAREHOUSE LOCATIONS BREAKDOWN MODAL     -->
<!-- ========================================== -->
<div class="modal fade" id="locationsModal" tabindex="-1" role="dialog" aria-labelledby="locationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 860px; width: 95%;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <!-- Header (Deep Navy Blue matching Image 2) -->
            <div class="modal-header py-3 px-4 border-0" style="background: #1c3554;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px; background: #facc15; flex-shrink: 0;">
                        <i class="fas fa-warehouse" style="color: #1e293b; font-size: 1.35rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold text-uppercase mb-1" id="loc_product_name" style="font-size: 1.15rem; letter-spacing: 0.3px;">
                            Product Name
                        </h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap" id="loc_product_subtitle_wrap">
                            <span class="badge" id="loc_product_code" style="background: #284a75; color: #fff; font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 4px;">
                                ITEM-0000
                            </span>
                            <span id="loc_product_meta" style="color: #94b8e8; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                • PIECES
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.85; font-size: 1.6rem; background: transparent; border: none; line-height: 1; margin-top: -10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="background: #ffffff;">
                <!-- 4 Top Stat Cards matching Image 2 -->
                <div class="row g-3 mb-4">
                    <!-- Total Opening -->
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3" style="background: #eff6ff; border: 1.5px solid #bfdbfe;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #2563eb; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Total Opening
                            </div>
                            <div class="d-flex align-items-baseline gap-1 flex-wrap" id="loc_card_opening" style="color: #1d4ed8; line-height: 1.2;">
                                0.00 PIECES
                            </div>
                        </div>
                    </div>
                    <!-- Live Stock -->
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3" style="background: #ffffff; border: 1.5px solid #e2e8f0;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Live Stock
                            </div>
                            <div class="d-flex align-items-baseline gap-1 flex-wrap" id="loc_card_live" style="color: #16a34a; line-height: 1.2;">
                                0.00 PIECES
                            </div>
                        </div>
                    </div>
                    <!-- Wholesale Rate -->
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3" style="background: #ffffff; border: 1.5px solid #e2e8f0;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Wholesale Rate
                            </div>
                            <div class="fw-bold" id="loc_card_wholesale" style="color: #0f172a; font-size: 1.25rem; line-height: 1.2;">
                                Rs 0.00
                            </div>
                        </div>
                    </div>
                    <!-- Retail Rate -->
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3" style="background: #ffffff; border: 1.5px solid #e2e8f0;">
                            <div class="text-uppercase fw-bold mb-1" style="color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                Retail Rate
                            </div>
                            <div class="fw-bold" id="loc_card_retail" style="color: #0f172a; font-size: 1.25rem; line-height: 1.2;">
                                Rs 0.00
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Header matching Image 2 -->
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt" style="color: #2563eb; font-size: 1rem;"></i>
                        <span style="font-weight: 800; color: #1e3a8a; font-size: 0.92rem; letter-spacing: 0.5px;">
                            ALLOCATED LOCATIONS &amp; WAREHOUSES
                        </span>
                    </div>
                    <span class="badge rounded-pill text-white" id="loc_pill_count" style="background-color: #2563eb; font-size: 0.8rem; padding: 6px 14px; font-weight: 700;">
                        0 Locations
                    </span>
                </div>

                <!-- Locations Table matching Image 2 -->
                <div class="border rounded-3" style="overflow: hidden; border-color: #e2e8f0 !important;">
                    <table class="table align-middle mb-0" style="font-size: 0.86rem;">
                        <thead style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
                            <tr>
                                <th style="width: 50px; color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 12px 16px;">#</th>
                                <th style="color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 12px 16px;">LOCATION NAME</th>
                                <th class="text-end" style="color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 12px 16px;">QUANTITY</th>
                            </tr>
                        </thead>
                        <tbody id="loc_table_body">
                            <!-- Populated dynamically via openLocationsModal() matching Image 2 -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 0.1 EDIT OPENING STOCK MODAL               -->
<!-- ========================================== -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true" data-backdrop="static" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 900px; width: 95%;">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
            <!-- Header -->
            <div class="modal-header py-3 px-4 border-0" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2); color: #ffffff; flex-shrink: 0; font-size: 1.25rem;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0" id="edit_modal_title" style="font-size: 1.15rem; letter-spacing: 0.3px;">
                            Edit Opening Stock
                        </h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap text-white-50 small mt-1" id="edit_modal_subtitle">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.85; font-size: 1.6rem; background: transparent; border: none; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editStockForm" onsubmit="submitEditStockForm(event)">
                <input type="hidden" id="edit_product_id" name="product_id" value="">
                <input type="hidden" id="edit_variant_key" name="variant_key" value="">
                <input type="hidden" id="edit_variant_name" name="variant_name" value="">
                
                <div class="modal-body p-4" style="background: #ffffff; max-height: 75vh; overflow-y: auto;">
                    <!-- Price settings -->
                    <div class="row g-3 p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Cost Price (Purchase Rate)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted fw-bold" style="font-size: 0.85rem;">Rs.</span>
                                <input type="number" step="0.01" min="0" class="form-control border-start-0" id="edit_cost_price" name="cost_price" placeholder="0.00" oninput="calcEditTotals()">
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Master purchase cost per piece</small>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-bold text-secondary mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                Sale Price (Retail / Selling)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted fw-bold" style="font-size: 0.85rem;">Rs.</span>
                                <input type="number" step="0.01" min="0" class="form-control border-start-0" id="edit_sale_price" name="sale_price" placeholder="0.00">
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Master selling price per piece</small>
                        </div>
                    </div>

                    <!-- Warehouse Stock Allocation Heading -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-warehouse text-primary"></i>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">Warehouse Stock Allocation</span>
                        </div>
                        <span class="badge bg-light text-secondary border" id="edit_ppb_badge" style="font-size: 0.78rem; padding: 5px 10px;">
                            1 Piece / Unit
                        </span>
                    </div>

                    <!-- Warehouse Allocation Table -->
                    <div class="table-responsive border rounded-3 mb-3" style="border-color: #e2e8f0 !important;">
                        <table class="table align-middle mb-0" style="font-size: 0.86rem;">
                            <thead style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
                                <tr>
                                    <th style="width: 45px; color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 10px 14px;">#</th>
                                    <th style="color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 10px 14px;">WAREHOUSE / LOCATION</th>
                                    <th class="edit-box-col text-center" style="width: 140px; color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 10px 14px;">BOXES / CTN</th>
                                    <th class="text-center" style="width: 150px; color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 10px 14px;">OPENING PIECES</th>
                                    <th class="text-end" style="width: 130px; color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 10px 14px;">TOTAL PIECES</th>
                                    <th class="text-end" style="width: 130px; color: #64748b; font-size: 0.76rem; font-weight: 700; padding: 10px 14px;">LIVE STOCK</th>
                                </tr>
                            </thead>
                            <tbody id="edit_warehouse_tbody">
                                <!-- Populated dynamically by openEditStockModal -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary calculation banner -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #ecfdf5; border: 1.5px solid #a7f3d0;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #10b981; color: #fff;">
                                <i class="fas fa-calculator fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Opening Pieces</div>
                                <div class="fw-bold text-success fs-5" id="edit_summary_total_pieces">0.00 Pcs</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Stock Value</div>
                            <div class="fw-bold text-dark fs-5" id="edit_summary_total_value">Rs. 0.00</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-4 bg-light border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm" id="btn_save_edit_stock">
                        <i class="fas fa-save me-1"></i> Update Opening Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    // Product lookup cache
    const productsData = @json($products);
    const warehousesData = @json($warehouses);

    const productsMap = {};
    productsData.forEach(p => {
        productsMap[p.id] = p;
    });

    // ── 0. LOCATIONS BREAKDOWN & EDIT MODAL DATA ──
    const productsLocationData = @json($groupedProducts->keyBy('row_key'));
    const allWarehouses = @json($warehouses);
    let currentEditPPB = 1;
    let currentEditHasBoxes = 0;

    function showModalSafely(modalId) {
        const $m = $('#' + modalId);
        if ($m.length && typeof $m.modal === 'function') {
            $m.modal('show');
        } else {
            const el = document.getElementById(modalId);
            if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(el) : new bootstrap.Modal(el);
                modal.show();
            }
        }
    }

    window.openLocationsModal = function(rowKey) {
        const data = productsLocationData[rowKey];
        if (!data) return;

        // 1. Header (Product Name + Subtitle with code, category, brand, unit)
        $('#loc_product_name').text(data.product_name);
        $('#loc_product_code').text(data.product_code || 'ITEM-0000');

        let metaParts = [];
        if (data.variant_display && data.variant_display !== '-') metaParts.push(data.variant_display);
        if (data.category_name) metaParts.push(data.category_name);
        if (data.brand_name) metaParts.push(data.brand_name);
        metaParts.push(data.unit_name || 'PIECES');

        $('#loc_product_meta').text('• ' + metaParts.join(' • ').toUpperCase());

        // 2. The 4 Cards matching Image 2
        const formatBadgeHtml = (fmt, fallback) => {
            if (!fmt) return fallback;
            let html = `<span style="font-size: 1.15rem; font-weight: 800;">${fmt.main}</span>`;
            if (fmt.sub) {
                html += ` <span style="font-size: 0.72rem; font-weight: 600; opacity: 0.85; white-space: nowrap;">${fmt.sub}</span>`;
            }
            return html;
        };

        $('#loc_card_opening').html(formatBadgeHtml(data.opening_formatted, parseFloat(data.total_opening_pieces || 0).toFixed(2) + ' ' + (data.unit_name || 'PIECES').toUpperCase()));
        $('#loc_card_live').html(formatBadgeHtml(data.live_formatted, parseFloat(data.total_live_pieces || 0).toFixed(2) + ' ' + (data.unit_name || 'PIECES').toUpperCase()));
        $('#loc_card_wholesale').text('Rs ' + parseFloat(data.cost_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#loc_card_retail').text('Rs ' + parseFloat(data.sale_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // 3. Pill count
        const locCount = data.warehouses_count || (data.warehouses ? data.warehouses.length : 0);
        $('#loc_pill_count').text(locCount + (locCount === 1 ? ' Location' : ' Locations'));

        // 4. Clean Table rows: # | LOCATION NAME | QUANTITY
        let tbodyHtml = '';
        if (data.warehouses && data.warehouses.length > 0) {
            data.warehouses.forEach((wh, index) => {
                const liveFmt = wh.live_formatted || {};
                const mainText = liveFmt.main || (parseFloat(wh.live_pieces || 0).toFixed(2) + ' ' + (data.unit_name || 'Pcs'));
                const subText = liveFmt.sub ? ` <span class="text-muted" style="font-size: 0.72rem; font-weight: 600; white-space: nowrap;">${liveFmt.sub}</span>` : '';

                tbodyHtml += `
                    <tr>
                        <td class="text-muted fw-bold" style="padding: 13px 16px; width: 50px;">${index + 1}</td>
                        <td style="padding: 13px 16px;">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                <span style="font-size:1.15rem;">🏪</span>
                                <span>${wh.warehouse_name || 'Warehouse'}</span>
                            </div>
                            ${wh.location ? `<div class="small text-muted ps-4" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt text-danger me-1"></i>${wh.location}</div>` : ''}
                        </td>
                        <td class="text-end" style="padding: 13px 16px;">
                            <div class="d-inline-flex align-items-baseline justify-content-end gap-1 flex-wrap">
                                <span class="fw-bold" style="color: #15803d; font-size: 1rem;">${mainText}</span>
                                ${subText}
                            </div>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbodyHtml = `<tr><td colspan="3" class="text-center text-muted py-4">No allocated locations found</td></tr>`;
        }

        $('#loc_table_body').html(tbodyHtml);
        showModalSafely('locationsModal');
    };

    // ── 0.1 EDIT OPENING STOCK MODAL LOGIC ──
    function openEditStockModal(rowKey) {
        const data = productsLocationData[rowKey];
        if (!data) return;

        $('#edit_product_id').val(data.product_id);
        $('#edit_variant_key').val(data.variant_key || '');
        $('#edit_variant_name').val(data.variant_name || '');
        
        const vTitle = (data.variant_display && data.variant_display !== '-') ? ` (${data.variant_display})` : '';
        $('#edit_modal_title').text('Edit Opening Stock — ' + data.product_name + vTitle);

        let subtitleHtml = '';
        if (data.product_code) {
            subtitleHtml += `<span class="badge" style="background:#047857; color:#fff; font-size:0.75rem; font-weight:700;">${data.product_code}</span>`;
        }
        if (data.variant_display && data.variant_display !== '-') {
            subtitleHtml += `<span class="badge bg-warning text-dark font-monospace" style="font-size:0.75rem; font-weight:700;"><i class="fas fa-tag me-1"></i>${data.variant_display}</span>`;
        }
        if (data.category_name) subtitleHtml += `<span>• ${data.category_name}</span>`;
        if (data.brand_name) subtitleHtml += `<span>• ${data.brand_name}</span>`;
        subtitleHtml += `<span>• Unit: ${data.unit_name || 'Pieces'}</span>`;
        $('#edit_modal_subtitle').html(subtitleHtml);

        // Prepopulate Master Prices
        $('#edit_cost_price').val(parseFloat(data.cost_price || 0));
        $('#edit_sale_price').val(parseFloat(data.sale_price || 0));

        // PPB and Box support
        currentEditPPB = parseFloat(data.ppb || 1);
        currentEditHasBoxes = (!data.is_piece_only && currentEditPPB > 1 && ['by_cartons', 'by_size', 'by_bandal'].includes(data.size_mode)) ? 1 : 0;

        if (currentEditHasBoxes) {
            $('.edit-box-col').show();
            $('#edit_ppb_badge').text(`${currentEditPPB} ${data.unit_name || 'Pieces'} per Box/Carton`);
        } else {
            $('.edit-box-col').hide();
            $('#edit_ppb_badge').text(`1 ${data.unit_name || 'Piece'} / Unit`);
        }

        // Build warehouse table rows
        let tbodyHtml = '';
        allWarehouses.forEach((wh, index) => {
            const existing = (data.warehouses || []).find(w => w.warehouse_id == wh.id);
            const openingPieces = existing ? parseFloat(existing.opening_pieces || 0) : 0;
            const livePieces = existing ? parseFloat(existing.live_pieces || 0) : 0;
            
            let boxesVal = '';
            let piecesVal = '';

            if (currentEditHasBoxes) {
                const bCount = existing ? parseFloat(existing.opening_boxes || 0) : Math.floor(openingPieces / currentEditPPB);
                const loose = Math.max(0, openingPieces - (bCount * currentEditPPB));
                if (bCount > 0) boxesVal = bCount;
                if (loose > 0) piecesVal = loose;
            } else {
                if (openingPieces > 0) piecesVal = openingPieces;
            }

            tbodyHtml += `
                <tr data-wh-id="${wh.id}">
                    <td class="text-muted fw-bold">${index + 1}</td>
                    <td>
                        <div class="fw-bold text-dark d-flex align-items-center gap-1">
                            <span>🏪</span>
                            <span>${wh.warehouse_name}</span>
                        </div>
                        ${wh.location ? `<div class="small text-muted ps-3"><i class="fas fa-map-marker-alt text-danger me-1"></i>${wh.location}</div>` : ''}
                    </td>
                    <td class="edit-box-col text-center" style="${currentEditHasBoxes ? '' : 'display:none;'}">
                        <input type="number" min="0" step="any" class="form-control form-control-sm text-center edit-boxes-input"
                               data-wh="${wh.id}" value="${boxesVal}" placeholder="0"
                               oninput="onEditQtyChanged(${wh.id})">
                    </td>
                    <td class="text-center">
                        <input type="number" min="0" step="any" class="form-control form-control-sm text-center edit-pieces-input"
                               data-wh="${wh.id}" value="${piecesVal}" placeholder="0"
                               oninput="onEditQtyChanged(${wh.id})">
                    </td>
                    <td class="text-end fw-bold text-success edit-total-pieces-cell" data-wh="${wh.id}" style="font-size: 0.95rem;">
                        ${openingPieces.toFixed(2)}
                    </td>
                    <td class="text-end">
                        <span class="badge ${livePieces > 0 ? 'bg-success text-white' : 'bg-light text-muted border'}" style="font-size: 0.78rem; padding: 4px 8px;">
                            ${livePieces.toFixed(2)} ${data.unit_name || 'Pcs'}
                        </span>
                    </td>
                </tr>
            `;
        });

        $('#edit_warehouse_tbody').html(tbodyHtml);
        calcEditTotals();
        showModalSafely('editStockModal');
    }

    function onEditQtyChanged(whId) {
        const $tr = $(`#edit_warehouse_tbody tr[data-wh-id="${whId}"]`);
        const boxes = parseFloat($tr.find('.edit-boxes-input').val()) || 0;
        const pieces = parseFloat($tr.find('.edit-pieces-input').val()) || 0;

        let total = 0;
        if (currentEditHasBoxes) {
            total = (boxes * currentEditPPB) + pieces;
        } else {
            total = pieces;
        }

        $tr.find('.edit-total-pieces-cell').text(total.toFixed(2));
        calcEditTotals();
    }

    function calcEditTotals() {
        let sumPieces = 0;
        $('#edit_warehouse_tbody .edit-total-pieces-cell').each(function() {
            sumPieces += parseFloat($(this).text()) || 0;
        });

        $('#edit_summary_total_pieces').text(sumPieces.toFixed(2) + ' Pcs');

        const costPrice = parseFloat($('#edit_cost_price').val()) || 0;
        const totalVal = sumPieces * costPrice;
        $('#edit_summary_total_value').text('Rs. ' + totalVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    function submitEditStockForm(e) {
        e.preventDefault();

        const productId = $('#edit_product_id').val();
        if (!productId) return;

        const stocks = [];
        $('#edit_warehouse_tbody tr').each(function() {
            const whId = $(this).data('wh-id');
            const boxes = parseFloat($(this).find('.edit-boxes-input').val()) || 0;
            const pieces = parseFloat($(this).find('.edit-pieces-input').val()) || 0;
            stocks.push({
                warehouse_id: whId,
                boxes: boxes,
                pieces: pieces
            });
        });

        Swal.fire({
            title: 'Updating Opening Stock...',
            html: 'Adjusting quantities and updating inventory balances...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('opening_stock.update') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                variant_key: $('#edit_variant_key').val(),
                variant_name: $('#edit_variant_name').val(),
                cost_price: $('#edit_cost_price').val(),
                sale_price: $('#edit_sale_price').val(),
                stocks: stocks
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: 'Updated!',
                        text: res.message || 'Opening stock updated successfully.',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message || 'Failed to update', 'error');
                }
            },
            error: function(err) {
                Swal.fire('Error', err.responseJSON?.message || 'Server error occurred while updating', 'error');
            }
        });
    }

    function confirmDeleteProductStock(rowKey, productName, variantDisplay) {
        const data = productsLocationData[rowKey];
        const vLabel = (variantDisplay && variantDisplay !== '-') ? ` (Variant: <b>${variantDisplay}</b>)` : '';

        Swal.fire({
            title: 'Revert Opening Stock?',
            html: `Are you sure you want to revert opening stock for <b>${productName}</b>${vLabel} across all warehouses?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Revert All',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Reverting Opening Stock...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const pId = data ? data.product_id : rowKey;
                $.ajax({
                    url: "{{ url('/opening-stock/product') }}/" + pId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        variant_key: data ? (data.variant_key || '') : '',
                        variant_name: data ? (data.variant_name || '') : ''
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Reverted!', res.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Failed to revert', 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Server error occurred', 'error');
                    }
                });
            }
        });
    }

    // ── 3. DELETE / REVERT ENTRY LOGIC ──
    function confirmDeleteEntry(id) {
        Swal.fire({
            title: 'Revert Opening Stock?',
            text: 'This will deduct the opening stock quantity from warehouse stock and record a reversal movement.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Revert & Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Reverting Entry...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: "{{ url('/opening-stock') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Deleted!', res.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Failed to revert entry', 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Server error occurred', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
