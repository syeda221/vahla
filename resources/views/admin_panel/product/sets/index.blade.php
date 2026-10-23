@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ========================================================
       PRODUCT SETS - EXECUTIVE RESPONSIVE & SLEEK POS UI
       ======================================================== */
    #setModal .modal-dialog {
        max-width: 1060px;
        width: 95%;
        margin: 1.5rem auto;
    }
    #setModal .modal-content {
        border: 1.5px solid #1e293b !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.28) !important;
        background-color: #ffffff;
        max-height: calc(100vh - 3rem);
        display: flex;
        flex-direction: column;
    }
    #setModal .modal-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border-bottom: 2px solid #334155 !important;
        flex-shrink: 0;
    }
    #setModal #setForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        overflow: hidden;
        margin: 0;
    }
    #setModal .modal-body {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        flex: 1 1 auto;
        max-height: calc(100vh - 13.5rem);
        background: #f8fafc;
        padding: 16px !important;
    }
    #setModal .modal-footer {
        flex-shrink: 0;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 10px 16px !important;
    }

    /* Clean Form Controls */
    #setModal .form-control-dark {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 6px !important;
        color: #0f172a !important;
        font-weight: 600 !important;
        background-color: #ffffff !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    #setModal .form-control-dark:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        outline: 0 !important;
    }

    /* Cards & Containers */
    #setModal .card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
    }
    #setModal #setItemsTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100%;
        margin-bottom: 0;
    }
    #setModal #setItemsTable thead th {
        background: #0f172a !important;
        color: #f8fafc !important;
        font-weight: 700 !important;
        font-size: 0.76rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        border: none !important;
        padding: 9px 12px !important;
    }
    #setModal #setItemsTable tbody tr {
        border-bottom: 1px solid #e2e8f0;
        background-color: #ffffff;
        transition: background-color 0.15s ease;
    }
    #setModal #setItemsTable tbody tr:hover {
        background-color: #f8fafc;
    }
    #setModal #setItemsTable td {
        padding: 10px 12px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Select2 Container */
    #setModal .select2-container {
        width: 100% !important;
    }
    #setModal .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    #setModal .select2-container--default.select2-container--open .select2-selection--single,
    #setModal .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #2563eb !important;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    #setModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 10px !important;
        font-size: 0.88rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
    }
    #setModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }

    /* Select2 Dropdown */
    .select2-container--open {
        z-index: 1065 !important;
    }
    .select2-dropdown {
        z-index: 1070 !important;
        border: 1.5px solid #2563eb !important;
        border-radius: 8px !important;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.2) !important;
        background-color: #ffffff !important;
        overflow: hidden;
    }
    .select2-search--dropdown {
        padding: 8px !important;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #3b82f6 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        width: 100% !important;
        outline: none !important;
    }
    .select2-results__option {
        font-size: 0.84rem !important;
        padding: 8px 12px !important;
        font-weight: 500 !important;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    /* Modern Variant Tag Chips */
    .variant-tags-container {
        margin-top: 5px;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
    }
    .variant-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        padding: 2px 7px;
        border-radius: 4px;
        line-height: 1.3;
        white-space: nowrap;
        font-family: 'JetBrains Mono', 'Segoe UI', monospace;
    }
    .variant-tag-code { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .variant-tag-unit { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .variant-tag-weight { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .variant-tag-rate { background: #fefce8; color: #a16207; border: 1px solid #fde047; }
    .variant-tag-conv { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }

    /* Quantity and Rate Input Styling */
    #setModal .row-qty-input {
        border-color: #cbd5e1 !important;
    }
    #setModal .row-qty-input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    #setModal .row-unit-badge {
        background: #0f172a;
        color: #38bdf8;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Sleek Remove Button */
    #setModal .btn-remove-item-row {
        width: 34px;
        height: 34px;
        border: 1.5px solid #ef4444 !important;
        color: #ef4444 !important;
        background-color: #fef2f2 !important;
        border-radius: 6px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    #setModal .btn-remove-item-row:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
    }

    /* ========================================================
       LISTING PAGE - EXECUTIVE DASHBOARD & TABLE STYLING
       ======================================================== */
    .stat-card-set {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card-set:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
    }
    .stat-card-set .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-card-blue { border-top: 3px solid #2563eb; }
    .stat-card-blue .stat-icon { background: #eff6ff; color: #2563eb; }
    .stat-card-green { border-top: 3px solid #10b981; }
    .stat-card-green .stat-icon { background: #ecfdf5; color: #10b981; }
    .stat-card-purple { border-top: 3px solid #8b5cf6; }
    .stat-card-purple .stat-icon { background: #f5f3ff; color: #8b5cf6; }
    .stat-card-amber { border-top: 3px solid #f59e0b; }
    .stat-card-amber .stat-icon { background: #fffbeb; color: #f59e0b; }

    /* Set Code Badge */
    .set-code-badge {
        background: #0f172a !important;
        color: #38bdf8 !important;
        font-family: 'JetBrains Mono', 'Segoe UI Mono', monospace !important;
        font-size: 0.80rem !important;
        font-weight: 700 !important;
        padding: 4px 8px !important;
        border-radius: 5px !important;
        letter-spacing: 0.4px !important;
        border: 1px solid #1e293b !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    /* Paired Components Badges Container */
    .set-components-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }
    .set-comp-item {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .set-comp-name {
        font-weight: 700;
        color: #1e293b;
    }
    .set-comp-qty {
        background: #0f172a;
        color: #ffffff;
        font-size: 0.70rem;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
    }
    .set-comp-rate {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 4px;
    }
    .set-comp-weight {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.68rem;
        padding: 1px 5px;
        border-radius: 4px;
    }

    /* Listing Table */
    .table-sets-listing {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin-bottom: 0;
    }
    .table-sets-listing thead th {
        background: #0f172a !important;
        color: #f8fafc !important;
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.6px !important;
        text-transform: uppercase !important;
        padding: 10px 14px !important;
        border: none !important;
    }
    .table-sets-listing tbody tr {
        transition: background-color 0.15s ease;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-sets-listing tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .table-sets-listing tbody td {
        padding: 10px 14px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Action Buttons in Listing */
    .btn-action-circle {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.15s ease;
        margin: 0 2px;
    }
</style>

<div class="main-content-inner">
    <div class="container-fluid py-3">
        <!-- Page Header -->
        <div class="row align-items-center mb-3">
            <div class="col-md-7">
                <h4 class="mb-1 text-dark fw-bold d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded p-2" style="width: 38px; height: 38px;">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <span>Product Assembly & Sets</span>
                </h4>
                <p class="text-muted small mb-0">Create assembled product combos (e.g. Bolt + Nut + Nail) with automatic <code>SET-</code> series code and direct sales integration.</p>
            </div>
            <div class="col-md-5 text-md-end mt-2 mt-md-0">
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold shadow-sm" id="btnOpenCreateSetModal" style="background: #0f172a; border-color: #0f172a; border-radius: 6px;">
                    <i class="fas fa-plus-circle me-1"></i> Create New Assembly / Set
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="stat-card-set stat-card-blue d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Assemblies & Sets</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $sets->count() }}</div>
                        <small class="text-primary fw-semibold" style="font-size: 0.72rem;"><i class="fas fa-boxes me-1"></i> Configured assemblies</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="stat-card-set stat-card-green d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active in Sales</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ $sets->where('is_active', true)->count() }}</div>
                        <small class="text-success fw-semibold" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i> Ready for invoice</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="stat-card-set stat-card-purple d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Assembled Components</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $sets->sum('items_count') }}</div>
                        <small class="text-muted fw-semibold" style="font-size: 0.72rem;"><i class="fas fa-cubes me-1"></i> Multi-variant items</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-2">
                <div class="stat-card-set stat-card-amber d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Next Auto Series</div>
                        <div class="fs-4 fw-bold text-dark font-monospace mt-1" style="color: #d97706 !important;">{{ $nextSetCode }}</div>
                        <small class="text-muted fw-semibold" style="font-size: 0.72rem;"><i class="fas fa-sync-alt me-1"></i> Auto-incrementing</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-barcode"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Product Sets Table Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="border: 1px solid #e2e8f0 !important; overflow: hidden;">
            <div class="card-header bg-white py-3 px-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                        <i class="fas fa-list-ul text-primary me-1"></i> Registered Product Assemblies
                    </span>
                    <span class="badge" id="searchResultCount" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.75rem;">
                        {{ $sets->count() }} Total Assemblies
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-md-0" style="max-width: 450px; width: 100%;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0" style="border-color: #cbd5e1;">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="filterSetSearch" class="form-control form-control-sm border-start-0" placeholder="Filter by Name, Code (e.g. SET-0001), or Component..." style="border-color: #cbd5e1;">
                    </div>
                    <select id="filterSetStatus" class="form-select form-select-sm" style="width: auto; min-width: 110px; border-color: #cbd5e1;">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-sets-listing align-middle" id="productSetsTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th style="width: 140px;">Assembly Code</th>
                                <th style="width: 220px;">Assembly / Set Name</th>
                                <th>Assembled Components & Quantities</th>
                                <th class="text-center" style="width: 110px;">Items Count</th>
                                <th class="text-end" style="width: 140px;">Assembly Price</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-center" style="width: 110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="setsListingTableBody" style="font-size: 0.85rem;">
                            @forelse($sets as $index => $set)
                                <tr class="set-row" 
                                    data-search="{{ strtolower($set->name . ' ' . $set->code . ' ' . $set->description . ' ' . $set->items->map(function($i){ return ($i->variant_name ?: optional($i->product)->item_name) . ' ' . optional($i->product)->item_code; })->implode(' ')) }}"
                                    data-status="{{ $set->is_active ? 'active' : 'inactive' }}">
                                    <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="set-code-badge">
                                            <i class="fas fa-layer-group text-info"></i> {{ $set->code ?: 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.90rem;">{{ $set->name }}</div>
                                        @if($set->description)
                                            <small class="text-muted d-block" style="font-size: 0.74rem;">{{ Str::limit($set->description, 55) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="set-components-wrap">
                                            @foreach($set->items as $item)
                                                @if($item->product)
                                                    <div class="set-comp-item" title="Item: {{ $item->variant_name ?: $item->product->item_name }}">
                                                        <i class="fas fa-cube text-primary" style="font-size: 0.72rem;"></i>
                                                        <span class="set-comp-name">{{ $item->variant_name ?: $item->product->item_name }}</span>
                                                        <span class="set-comp-qty">{{ (float)$item->quantity }} {{ $item->variant_unit ?: optional($item->product->unit)->name }}</span>
                                                        @if($item->sale_price > 0)
                                                            <span class="set-comp-rate">Rs. {{ number_format($item->sale_price, 2) }}</span>
                                                        @endif
                                                        @if($item->weight_per_piece > 0)
                                                            <span class="set-comp-weight">{{ (float)$item->weight_per_piece }}g</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-weight: 700; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem;">
                                            {{ $set->items_count }} Items
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($set->set_price > 0)
                                            <span class="fw-bold font-monospace" style="color: #059669; font-size: 0.92rem;">
                                                Rs. {{ number_format($set->set_price, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted small fst-italic">Auto Sum</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($set->is_active)
                                            <span class="badge" style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; padding: 4px 8px; border-radius: 4px; font-size: 0.74rem;">
                                                <i class="fas fa-circle text-success me-1" style="font-size: 0.5rem;"></i> Active
                                            </span>
                                        @else
                                            <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; font-weight: 600; padding: 4px 8px; border-radius: 4px; font-size: 0.74rem;">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center">
                                            <button type="button" class="btn btn-outline-primary btn-action-circle btn-edit-set" 
                                                    data-id="{{ $set->id }}" title="Edit Set">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('product-sets.destroy', $set->id) }}" method="POST" 
                                                  class="d-inline form-delete-set" 
                                                  onsubmit="return confirm('Are you sure you want to delete this Set?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-action-circle" title="Delete Set">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="trEmptyState">
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="fas fa-layer-group fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                                            <h6 class="fw-bold text-dark">No Product Assemblies Created Yet</h6>
                                            <p class="small text-muted mb-3">Combine multiple products (like Bolt + Nut + Nail) into an assembled set to sell together.</p>
                                            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" onclick="$('#btnOpenCreateSetModal').click()" style="background: #0f172a; border-color: #0f172a;">
                                                <i class="fas fa-plus-circle me-1"></i> Create First Assembly
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="trNoFilterResults" style="display: none;">
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-search me-1"></i> No product assemblies match your search filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= CREATE / EDIT SET MODAL ================= -->
<div class="modal fade" id="setModal" tabindex="-1" aria-labelledby="setModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header py-2 px-3 text-white">
                <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="setModalLabel" style="font-size: 0.98rem; letter-spacing: 0.3px;">
                    <i class="fas fa-layer-group text-primary"></i> <span id="modalTitleText">Create Product Assembly / Set</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.85; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="setForm" action="{{ route('product-sets.store') }}" method="POST">
                @csrf
                <input type="hidden" name="set_id" id="edit_set_id" value="">

                <div class="modal-body p-3">
                    <!-- Top Form Info Card -->
                    <div class="card border border-light-subtle shadow-sm mb-3">
                        <div class="card-body p-3 bg-white rounded">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Assembly / Set Name <span class="text-danger">* (Compulsory)</span></label>
                                    <input type="text" class="form-control form-control-sm form-control-dark" name="name" id="set_name" required placeholder="e.g. Washing Set">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Assembly Code <span class="badge bg-primary text-white" style="font-size: 10px;">AUTO</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm form-control-dark font-monospace fw-bold text-primary" name="code" id="set_code" value="{{ $nextSetCode ?? 'SET-0001' }}" placeholder="SET-0001">
                                        <button type="button" class="btn btn-outline-secondary" id="btnRefreshSetCode" title="Refresh Next SET- Code"><i class="fas fa-sync-alt"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Assembly Price (Rs.)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light fw-bold text-muted" style="font-size: 0.75rem;">Rs.</span>
                                        <input type="number" step="any" min="0" class="form-control form-control-sm form-control-dark font-monospace fw-bold text-success" name="set_price" id="set_price" placeholder="Auto Sum">
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 10.5px;">Leave blank to auto-calculate from components</small>
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Description / Notes</label>
                                    <input type="text" class="form-control form-control-sm form-control-dark" name="description" id="set_description" placeholder="Optional details about this combination set">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Repeater Box Container -->
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                            <span class="fw-bold text-dark small d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                <i class="fas fa-cubes text-primary"></i> Products in this Set:
                                <span class="badge bg-primary-subtle text-primary border rounded-pill px-2 py-0" id="setItemsCountBadge" style="font-size: 0.72rem;">0 Items</span>
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1" id="setSummaryBadge" style="font-size: 0.8rem;">
                                    Components Total: <span class="text-success fw-bold" id="summarySumPrice">Rs. 0.00</span>
                                </span>
                                <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm d-flex align-items-center gap-1" id="btnAddSetItemRow" style="font-size: 0.78rem;">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
                                <table class="table table-hover align-middle mb-0" id="setItemsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="min-width: 440px; width: 55%; padding: 8px 12px;">SELECT PRODUCT &amp; VARIANT / UNIT</th>
                                            <th class="text-center" style="width: 20%; min-width: 150px; padding: 8px 12px;">RATE (RS.)</th>
                                            <th class="text-center" style="width: 20%; min-width: 160px; padding: 8px 12px;">QUANTITY PER SET</th>
                                            <th class="text-center" style="width: 5%; min-width: 45px; padding: 8px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="setItemsTableBody">
                                        <!-- Dynamic Rows inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light py-2 px-3 border-top">
                            <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <i class="fas fa-info-circle text-primary"></i> Example: 1 Bolt (Pcs) + 2 Nuts (Pcs) + 4 Nails (Kg). When selling 1 set, these exact variants and quantities will be added to the invoice and deducted from stock.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-white border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted d-none d-md-inline" style="font-size: 0.75rem;">
                        <i class="fas fa-shield-alt text-success me-1"></i> Multi-variant stock deduction is managed automatically in background.
                    </small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 fw-bold" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary text-white fw-bold px-4 shadow-sm" id="btnSaveProductSet" style="background: #0f172a; border: 1.5px solid #0f172a; border-radius: 6px;">
                            <i class="fas fa-save me-1"></i> Save Product Assembly
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function parseProductVariants(p) {
        if (!p || !p.color) return [];
        try {
            let parsed = typeof p.color === 'string' ? JSON.parse(p.color) : p.color;
            if (typeof parsed === 'string') parsed = JSON.parse(parsed);
            if (Array.isArray(parsed) && parsed.length > 0) return parsed;
        } catch(e) {}
        return [];
    }

    function escapeHtmlAttr(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function createProductRow(item = null) {
        const selectedPid = item ? (item.product_id || item.id) : '';
        const qty = item ? (parseFloat(item.quantity) || 1) : 1;
        const itemRate = item ? (parseFloat(item.sale_price) || 0) : 0;
        let selectedVariantKey = item ? (item.variant_key || '').toString().trim() : '';
        let selectedVariantUnit = item ? (item.variant_unit || '').toString().trim().toLowerCase() : '';
        let selectedVariantName = item ? (item.variant_name || '').toString().trim().toLowerCase() : '';

        let itemBarcode = '';
        if (item && item.variant_data) {
            try {
                let parsed = JSON.parse(atob(item.variant_data));
                if (parsed) {
                    if (parsed.barcode) itemBarcode = parsed.barcode.toString().trim();
                    if (parsed.unit && !selectedVariantUnit) selectedVariantUnit = parsed.unit.toString().trim().toLowerCase();
                }
            } catch(e) {
                try {
                    let parsed = JSON.parse(item.variant_data);
                    if (parsed && parsed.barcode) itemBarcode = parsed.barcode.toString().trim();
                } catch(e2) {}
            }
        }

        let options = '<option value="">-- Search Item Code (e.g. 1616), Name, or Unit --</option>';

        // In edit mode with an item, construct its single selected <option> directly (Zero overhead!)
        if (item && item.product) {
            const p = item.product;
            const variants = parseProductVariants(p);
            const itemCode = p.item_code ? p.item_code.toString().trim() : '';
            const codePrefix = itemCode ? `[${itemCode}] ` : '';

            let targetVIdx = -1;
            if (variants.length > 0) {
                // 1. Match by Barcode / Key AND Unit
                targetVIdx = variants.findIndex((v, idx) => {
                    const vBarcode = (v.barcode || '').toString().trim();
                    const vKey = (v.barcode || v.name || idx).toString().trim();
                    const vUnit = (v.unit || '').toString().trim().toLowerCase();

                    const keyMatch = (selectedVariantKey && (vKey === selectedVariantKey || vBarcode === selectedVariantKey)) ||
                                     (itemBarcode && vBarcode === itemBarcode);
                    const unitMatch = selectedVariantUnit ? (vUnit === selectedVariantUnit) : true;

                    return keyMatch && unitMatch;
                });

                // 2. Match by Barcode or Key alone
                if (targetVIdx === -1 && (selectedVariantKey || itemBarcode)) {
                    targetVIdx = variants.findIndex((v, idx) => {
                        const vBarcode = (v.barcode || '').toString().trim();
                        const vKey = (v.barcode || v.name || idx).toString().trim();
                        return (selectedVariantKey && (vKey === selectedVariantKey || vBarcode === selectedVariantKey)) ||
                               (itemBarcode && vBarcode === itemBarcode);
                    });
                }

                // 3. Match by Unit (e.g. "ft", "meter", "pcs", "kg")
                if (targetVIdx === -1 && selectedVariantUnit) {
                    targetVIdx = variants.findIndex(v => {
                        const vUnit = (v.unit || '').toString().trim().toLowerCase();
                        return vUnit === selectedVariantUnit;
                    });
                }

                // 4. Match by full Name AND Unit
                if (targetVIdx === -1 && selectedVariantName && selectedVariantUnit) {
                    targetVIdx = variants.findIndex(v => {
                        const vName = (v.name || '').toString().trim().toLowerCase();
                        const vUnit = (v.unit || '').toString().trim().toLowerCase();
                        return vName === selectedVariantName && vUnit === selectedVariantUnit;
                    });
                }

                // 5. Match by full Name alone ONLY if unique among variants
                if (targetVIdx === -1 && selectedVariantName) {
                    const nameMatches = variants.filter(v => (v.name || '').toString().trim().toLowerCase() === selectedVariantName);
                    if (nameMatches.length === 1) {
                        targetVIdx = variants.findIndex(v => (v.name || '').toString().trim().toLowerCase() === selectedVariantName);
                    }
                }

                // 6. Default to 0 only if no variant key/unit/name was specified at all
                if (targetVIdx === -1 && !selectedVariantKey && !selectedVariantUnit && !selectedVariantName) {
                    targetVIdx = 0;
                }
            }

            if (variants.length > 0 && targetVIdx >= 0 && targetVIdx < variants.length) {
                const v = variants[targetVIdx];
                const vKey = (v.barcode || v.name || targetVIdx).toString();
                const vUnit = item.variant_unit || v.unit || (p.unit ? p.unit.name : 'Pcs');
                const vName = item.variant_name || v.name || p.item_name;
                const vWeight = (item.weight_per_piece !== null && item.weight_per_piece !== undefined && item.weight_per_piece !== '') 
                    ? parseFloat(item.weight_per_piece) 
                    : ((v.weight_per_piece !== undefined && v.weight_per_piece !== null && v.weight_per_piece !== '') ? parseFloat(v.weight_per_piece) : 0);
                const vConv = parseFloat(item.conv_factor || v.conv_factor || 1);
                const vPrice = parseFloat(item.sale_price || v.sale_price || p.sale_price_per_piece || 0);

                let variantDataEncoded = item.variant_data || '';
                if (!variantDataEncoded) {
                    try {
                        variantDataEncoded = btoa(unescape(encodeURIComponent(JSON.stringify(v))));
                    } catch(e) {
                        variantDataEncoded = btoa(JSON.stringify(v));
                    }
                }

                let weightLabel = vWeight > 0 ? ` | ${vWeight}g` : '';
                let priceLabel = vPrice > 0 ? ` | Rs. ${vPrice}` : '';
                let displayLabel = `${codePrefix}${vName} [${vUnit}${weightLabel}${priceLabel}]`;

                options += `<option value="${p.id}||${targetVIdx}" 
                    data-pid="${p.id}" 
                    data-code="${escapeHtmlAttr(itemCode)}" 
                    data-vidx="${targetVIdx}" 
                    data-vkey="${escapeHtmlAttr(vKey)}" 
                    data-vunit="${escapeHtmlAttr(vUnit)}" 
                    data-vname="${escapeHtmlAttr(vName)}" 
                    data-vweight="${vWeight}" 
                    data-vconv="${vConv}" 
                    data-vprice="${vPrice}" 
                    data-vdata="${escapeHtmlAttr(variantDataEncoded)}" 
                    selected>
                    ${escapeHtmlAttr(displayLabel)}
                </option>`;
            } else {
                // Product without variants or base
                const stdUnit = item.variant_unit || (p.unit ? p.unit.name : (p.size_mode === 'by_cartons' ? 'Carton' : 'Pcs'));
                const stdPrice = parseFloat(item.sale_price || p.sale_price_per_piece || p.sale_price_per_box || 0);
                const stdWeight = (item.weight_per_piece !== null && item.weight_per_piece !== undefined && item.weight_per_piece !== '')
                    ? parseFloat(item.weight_per_piece)
                    : parseFloat(p.weight_per_piece || 0);
                const stdName = item.variant_name || p.item_name;

                let weightLabel = stdWeight > 0 ? ` | ${stdWeight}g` : '';
                let priceLabel = stdPrice > 0 ? ` | Rs. ${stdPrice}` : '';
                let displayLabel = `${codePrefix}${stdName} [${stdUnit}${weightLabel}${priceLabel}]`;

                options += `<option value="${p.id}||base" 
                    data-pid="${p.id}" 
                    data-code="${escapeHtmlAttr(itemCode)}" 
                    data-vidx="base" 
                    data-vkey="base" 
                    data-vunit="${escapeHtmlAttr(stdUnit)}" 
                    data-vname="${escapeHtmlAttr(stdName)}" 
                    data-vweight="${stdWeight}" 
                    data-vconv="1" 
                    data-vprice="${stdPrice}" 
                    data-vdata="" 
                    selected>
                    ${escapeHtmlAttr(displayLabel)}
                </option>`;
            }
        } else if (item && item.product_id) {
            const vUnit = item.variant_unit || 'Pcs';
            const vName = item.variant_name || `Product #${item.product_id}`;
            const vPrice = parseFloat(item.sale_price || 0);
            const vWeight = parseFloat(item.weight_per_piece || 0);
            options += `<option value="${item.product_id}||base" 
                data-pid="${item.product_id}" 
                data-code="" 
                data-vidx="base" 
                data-vkey="${escapeHtmlAttr(item.variant_key || 'base')}" 
                data-vunit="${escapeHtmlAttr(vUnit)}" 
                data-vname="${escapeHtmlAttr(vName)}" 
                data-vweight="${vWeight}" 
                data-vconv="${item.conv_factor || 1}" 
                data-vprice="${vPrice}" 
                data-vdata="${escapeHtmlAttr(item.variant_data || '')}" 
                selected>
                ${escapeHtmlAttr(vName)} [${vUnit}]
            </option>`;
        }

        return `
            <tr class="set-item-row" data-initial-pid="${selectedPid}">
                <td class="p-2" style="vertical-align: middle;">
                    <select class="form-select form-select-sm set-variant-select" required style="width: 100%;">
                        ${options}
                    </select>

                    <!-- Dynamic Variant & Unit Info Box (Clean Badges) -->
                    <div class="variant-config-box"></div>

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" class="inp-product-id" name="product_id[]" value="${selectedPid}">
                    <input type="hidden" class="inp-variant-key" name="variant_key[]" value="${escapeHtmlAttr(item ? (item.variant_key || '') : '')}">
                    <input type="hidden" class="inp-variant-unit" name="variant_unit[]" value="${escapeHtmlAttr(item ? (item.variant_unit || '') : '')}">
                    <input type="hidden" class="inp-variant-name" name="variant_name[]" value="${escapeHtmlAttr(item ? (item.variant_name || '') : '')}">
                    <input type="hidden" class="inp-weight-per-piece" name="weight_per_piece[]" value="${item ? (item.weight_per_piece || '') : ''}">
                    <input type="hidden" class="inp-conv-factor" name="conv_factor[]" value="${item ? (item.conv_factor || '') : ''}">
                    <input type="hidden" class="inp-sale-price" name="sale_price[]" value="${itemRate > 0 ? itemRate : ''}">
                    <input type="hidden" class="inp-variant-data" name="variant_data[]" value="${escapeHtmlAttr(item ? (item.variant_data || '') : '')}">
                </td>
                <td class="p-2" style="vertical-align: middle;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted fw-bold" style="font-size: 0.75rem;">Rs.</span>
                        <input type="number" step="any" min="0" class="form-control form-control-sm form-control-dark row-rate-input text-end font-monospace fw-bold text-dark" value="${itemRate > 0 ? itemRate : ''}" placeholder="0.00" style="font-size: 0.88rem;">
                    </div>
                </td>
                <td class="p-2 text-center" style="vertical-align: middle;">
                    <div class="input-group input-group-sm">
                        <input type="number" step="any" min="0.01" class="form-control form-control-sm form-control-dark row-qty-input text-center fw-bold font-monospace" name="quantity[]" value="${qty}" required placeholder="Qty" style="font-size: 0.95rem;">
                        <span class="input-group-text bg-dark text-white fw-bold row-unit-badge" style="min-width: 52px; justify-content: center; font-size: 0.72rem; letter-spacing: 0.3px;">-</span>
                    </div>
                </td>
                <td class="text-center p-2" style="vertical-align: middle;">
                    <button type="button" class="btn btn-remove-item-row" title="Remove Product">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function calculateSetSummary() {
        let total = 0;
        let count = 0;
        $('#setItemsTableBody tr').each(function() {
            const pid = $(this).find('.inp-product-id').val();
            if (pid) {
                count++;
                const qty = parseFloat($(this).find('.row-qty-input').val()) || 0;
                const rate = parseFloat($(this).find('.row-rate-input').val()) || parseFloat($(this).find('.inp-sale-price').val()) || 0;
                total += (qty * rate);
            }
        });
        $('#summarySumPrice').text('Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#setItemsCountBadge').text(count + (count === 1 ? ' Item' : ' Items'));
        const currentCustomPrice = $('#set_price').val();
        if (!currentCustomPrice) {
            $('#set_price').attr('placeholder', total > 0 ? ('Auto: Rs. ' + total.toFixed(2)) : 'Auto Sum');
        }
    }

    function applySelectedVariantToRow($row, isUserChange = false, dataObj = null) {
        const $select = $row.find('.set-variant-select');
        let $opt = $select.find('option:selected');
        const selectedVal = $select.val();
        if ((!$opt.length || !$opt.attr('data-pid')) && selectedVal) {
            $opt = $select.find('option[value="' + selectedVal + '"]');
        }

        let pid = '';
        let vKey = '';
        let vUnit = 'Pcs';
        let vName = '';
        let itemCode = '';
        let vWeight = 0;
        let vConv = 1;
        let vPrice = 0;
        let vData = '';

        if (dataObj && (dataObj.pid || dataObj.id)) {
            pid = dataObj.pid || '';
            vKey = dataObj.vkey || '';
            vUnit = dataObj.vunit || 'Pcs';
            vName = dataObj.vname || '';
            itemCode = dataObj.code || '';
            vWeight = parseFloat(dataObj.vweight || 0);
            vConv = parseFloat(dataObj.vconv || 1);
            vPrice = parseFloat(dataObj.vprice || 0);
            vData = dataObj.vdata || '';
        } else if ($opt.length && ($opt.attr('data-pid') || selectedVal)) {
            pid = $opt.attr('data-pid') || '';
            vKey = $opt.attr('data-vkey') || '';
            vUnit = $opt.attr('data-vunit') || 'Pcs';
            vName = $opt.attr('data-vname') || '';
            itemCode = $opt.attr('data-code') || '';
            vWeight = parseFloat($opt.attr('data-vweight') || 0);
            vConv = parseFloat($opt.attr('data-vconv') || 1);
            vPrice = parseFloat($opt.attr('data-vprice') || 0);
            vData = $opt.attr('data-vdata') || '';
        }

        if (!pid) {
            $row.find('.inp-product-id').val('');
            $row.find('.inp-variant-key').val('');
            $row.find('.inp-variant-unit').val('');
            $row.find('.inp-variant-name').val('');
            $row.find('.inp-weight-per-piece').val('');
            $row.find('.inp-conv-factor').val('');
            $row.find('.inp-sale-price').val('');
            $row.find('.row-rate-input').val('');
            $row.find('.inp-variant-data').val('');
            $row.find('.row-unit-badge').text('-');
            $row.find('.variant-config-box').empty();
            calculateSetSummary();
            return;
        }

        // If base64 variant_data is available, extract verified fields
        if (vData) {
            try {
                const parsedVd = JSON.parse(atob(vData));
                if (parsedVd && parsedVd.name && !vName) vName = parsedVd.name;
                if (parsedVd && parsedVd.unit && !vUnit) vUnit = parsedVd.unit;
                if (parsedVd && parsedVd.conv_factor && !vConv) vConv = parseFloat(parsedVd.conv_factor);
                if (parsedVd && parsedVd.weight_per_piece && !vWeight) vWeight = parseFloat(parsedVd.weight_per_piece);
                if (parsedVd && parsedVd.sale_price && !vPrice) vPrice = parseFloat(parsedVd.sale_price);
            } catch(e) {}
        }

        $row.find('.inp-product-id').val(pid);
        $row.find('.inp-variant-key').val(vKey);
        $row.find('.inp-variant-unit').val(vUnit);
        $row.find('.inp-variant-name').val(vName);
        $row.find('.inp-weight-per-piece').val(vWeight);
        $row.find('.inp-conv-factor').val(vConv);

        // Rate handling: preserve custom saved rate on edit / initial load
        const existingRate = parseFloat($row.find('.inp-sale-price').val() || $row.find('.row-rate-input').val() || 0);
        if (!isUserChange && existingRate > 0) {
            $row.find('.row-rate-input').val(existingRate);
            $row.find('.inp-sale-price').val(existingRate);
        } else {
            $row.find('.inp-sale-price').val(vPrice > 0 ? vPrice : '');
            $row.find('.row-rate-input').val(vPrice > 0 ? vPrice : '');
        }
        $row.find('.inp-variant-data').val(vData);

        $row.find('.row-unit-badge').text(vUnit.toUpperCase());
        $row.find('.row-qty-input').attr('placeholder', `Qty (${vUnit})`);

        let chipsHtml = `<div class="variant-tags-container">`;
        if (itemCode) {
            chipsHtml += `
                <span class="variant-tag variant-tag-code" title="Item Code: ${itemCode}">
                    <i class="fas fa-barcode"></i> ${itemCode}
                </span>
            `;
        }
        chipsHtml += `
            <span class="variant-tag variant-tag-unit" title="Selected Unit">
                <i class="fas fa-tag"></i> UNIT: ${vUnit.toUpperCase()}
            </span>
        `;
        if (vWeight > 0) {
            chipsHtml += `
                <span class="variant-tag variant-tag-weight" title="Weight per piece in Grams">
                    <i class="fas fa-weight-hanging"></i> ${vWeight}g/pc
                </span>
            `;
        }
        if (vPrice > 0) {
            chipsHtml += `
                <span class="variant-tag variant-tag-rate" title="Sale Rate per unit">
                    <i class="fas fa-money-bill-wave"></i> Rs. ${vPrice.toLocaleString()}
                </span>
            `;
        }
        if (vConv > 0 && vConv !== 1) {
            chipsHtml += `
                <span class="variant-tag variant-tag-conv" title="Conversion Factor">
                    <i class="fas fa-exchange-alt"></i> CONV: ${vConv}
                </span>
            `;
        }
        chipsHtml += `</div>`;
        $row.find('.variant-config-box').html(chipsHtml);

        calculateSetSummary();
    }

    function refreshNextSetCode() {
        $.get("{{ route('product-sets.next_code') }}", function(res) {
            if (res && res.next_code) {
                $('#set_code').val(res.next_code);
            }
        });
    }

    // Format option dropdown display
    function formatSetSelect2Option(state) {
        if (!state.id || state.loading) return state.text;
        const el = state.element;

        const code = state.code || (el ? el.getAttribute('data-code') : '') || '';
        const vunit = state.vunit || (el ? el.getAttribute('data-vunit') : '') || 'Pcs';
        const vweight = parseFloat(state.vweight !== undefined ? state.vweight : (el ? el.getAttribute('data-vweight') : 0)) || 0;
        const vprice = parseFloat(state.vprice !== undefined ? state.vprice : (el ? el.getAttribute('data-vprice') : 0)) || 0;
        const vname = state.vname || (el ? el.getAttribute('data-vname') : '') || state.text;

        let codeBadge = code ? `<span style="background: #0f172a; color: #38bdf8; font-family: monospace; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; margin-right: 6px;"><i class="fas fa-barcode"></i> ${escapeHtmlAttr(code)}</span>` : '';
        let unitBadge = `<span style="background: #e0f2fe; color: #0369a1; font-size: 0.70rem; padding: 1px 5px; border-radius: 3px; font-weight: 700; margin-left: 6px;">${escapeHtmlAttr(vunit.toUpperCase())}</span>`;
        let weightBadge = vweight > 0 ? `<span style="background: #f0fdf4; color: #15803d; font-size: 0.70rem; padding: 1px 5px; border-radius: 3px; margin-left: 4px;">${vweight}g</span>` : '';
        let priceBadge = vprice > 0 ? `<span style="background: #fefce8; color: #a16207; font-size: 0.70rem; padding: 1px 5px; border-radius: 3px; font-weight: 700; margin-left: 4px;">Rs. ${vprice.toLocaleString()}</span>` : '';

        return $(`<div style="display: flex; align-items: center; justify-content: space-between; padding: 2px 0;">
            <div style="display: flex; align-items: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                ${codeBadge}
                <span style="font-weight: 600; color: #1e293b; font-size: 0.84rem;">${escapeHtmlAttr(vname)}</span>
            </div>
            <div style="display: flex; align-items: center; flex-shrink: 0;">
                ${unitBadge}
                ${weightBadge}
                ${priceBadge}
            </div>
        </div>`);
    }

    // Format selection pill inside input
    function formatSetSelect2Selection(state) {
        if (!state.id) return state.text;
        const el = state.element;
        const code = state.code || (el ? el.getAttribute('data-code') : '') || '';
        const vname = state.vname || (el ? el.getAttribute('data-vname') : '') || state.text;
        const vunit = state.vunit || (el ? el.getAttribute('data-vunit') : '') || '';
        return code ? `[${code}] ${vname} (${vunit})` : (vunit ? `${vname} (${vunit})` : vname);
    }

    // Function to safely initialize select2 on set variant selects (Instant AJAX, Zero DOM lag)
    function initSetSelect2($target) {
        if (typeof $.fn.select2 === 'undefined') return;

        let $selects;
        if (!$target || $target.length === 0) {
            $selects = $('#setItemsTableBody .set-variant-select');
        } else if ($target.is('.set-variant-select')) {
            $selects = $target;
        } else {
            $selects = $target.find('.set-variant-select');
            if ($target.hasClass('set-variant-select')) {
                $selects = $selects.add($target);
            }
        }

        $selects.each(function() {
            const $select = $(this);
            const $row = $select.closest('tr');

            if ($select.hasClass('select2-hidden-accessible')) {
                return; // Already initialized cleanly
            }

            $select.select2({
                dropdownParent: $('#setModal'),
                width: '100%',
                placeholder: '-- Search Item Code (e.g. 1616), Name, or Unit --',
                allowClear: true,
                ajax: {
                    url: '{{ route("product-sets.ajax-products") }}',
                    dataType: 'json',
                    delay: 200,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results || [],
                            pagination: {
                                more: Boolean(data.pagination && data.pagination.more)
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatSetSelect2Option,
                templateSelection: formatSetSelect2Selection,
                escapeMarkup: function(m) { return m; }
            });

            // When an item is picked from AJAX dropdown
            $select.on('select2:select', function(e) {
                const data = e.params.data;
                if (!data) return;

                let $opt = $select.find('option[value="' + data.id + '"]');
                if (!$opt.length) {
                    $opt = $('<option selected></option>').val(data.id).text(data.text);
                    $select.append($opt);
                }
                $opt.attr('data-pid', data.pid || '');
                $opt.attr('data-code', data.code || '');
                $opt.attr('data-vidx', data.vidx !== undefined ? data.vidx : '');
                $opt.attr('data-vkey', data.vkey || '');
                $opt.attr('data-vunit', data.vunit || '');
                $opt.attr('data-vname', data.vname || '');
                $opt.attr('data-vweight', data.vweight || 0);
                $opt.attr('data-vconv', data.vconv || 1);
                $opt.attr('data-vprice', data.vprice || 0);
                $opt.attr('data-vdata', data.vdata || '');

                applySelectedVariantToRow($row, true, data);
            });

            // When item is cleared
            $select.on('select2:unselect select2:clear', function() {
                applySelectedVariantToRow($row, true, null);
            });

            // If an option is selected, apply its details
            if ($select.val()) {
                applySelectedVariantToRow($row, false);
            }
        });
    }

    $(document).ready(function() {
        // Change product & variant dropdown -> sync hidden fields and badge chips
        $(document).on('change', '.set-variant-select', function() {
            const $row = $(this).closest('tr');
            applySelectedVariantToRow($row, true);
        });

        // Change Rate on Row -> sync inp-sale-price and recalculate summary
        $(document).on('input change', '.row-rate-input', function() {
            const $row = $(this).closest('tr');
            const newRate = parseFloat($(this).val()) || 0;
            $row.find('.inp-sale-price').val(newRate);
            calculateSetSummary();
        });

        // Change Quantity on Row -> recalculate summary
        $(document).on('input change', '.row-qty-input', function() {
            calculateSetSummary();
        });

        // Refresh Next SET Code button
        $('#btnRefreshSetCode').on('click', function() {
            refreshNextSetCode();
        });

        // Open Create Modal - FAST (< 10ms)
        $('#btnOpenCreateSetModal').on('click', function() {
            $('#setForm')[0].reset();
            $('#edit_set_id').val('');
            $('#set_price').val('');
            $('#modalTitleText').text('Create Product Assembly / Set');
            $('#setItemsTableBody').empty();

            refreshNextSetCode();

            // Add 2 initial rows
            const $r1 = $(createProductRow());
            const $r2 = $(createProductRow());
            $('#setItemsTableBody').append($r1).append($r2);

            $('#setModal').modal('show');
            setTimeout(function() {
                initSetSelect2($('#setItemsTableBody'));
                calculateSetSummary();
            }, 50);
        });

        // Initialize Select2 and disable Bootstrap focus stealing so search typing works 100%
        $('#setModal').on('shown.bs.modal', function() {
            $(document).off('focusin.modal');
            initSetSelect2($('#setItemsTableBody'));
            calculateSetSummary();
        });

        // Clean up Select2 when modal is closed
        $('#setModal').on('hidden.bs.modal', function() {
            $('#setItemsTableBody .set-variant-select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            $('#setItemsTableBody').empty();
        });

        // Add Row Button - INSTANT (< 5ms)
        $('#btnAddSetItemRow').on('click', function() {
            const $newRow = $(createProductRow());
            $('#setItemsTableBody').append($newRow);
            initSetSelect2($newRow);
            calculateSetSummary();
        });

        // Remove Row
        $(document).on('click', '.btn-remove-item-row', function() {
            if ($('#setItemsTableBody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateSetSummary();
            } else {
                alert('An assembly must have at least one product.');
            }
        });

        // Edit Set - FAST (< 50ms)
        $('.btn-edit-set').on('click', function() {
            const setId = $(this).data('id');
            const url = "{{ url('product-sets') }}/" + setId;

            $.get(url, function(data) {
                if (!data) return;

                $('#edit_set_id').val(data.id);
                $('#set_name').val(data.name);
                $('#set_code').val(data.code || '');
                $('#set_price').val(data.set_price || '');
                $('#set_description').val(data.description || '');
                $('#modalTitleText').text('Edit Product Assembly: ' + data.name);

                $('#setItemsTableBody').empty();

                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        const $row = $(createProductRow(item));
                        $('#setItemsTableBody').append($row);
                    });
                } else {
                    $('#setItemsTableBody').append(createProductRow());
                }

                $('#setModal').modal('show');
                setTimeout(function() {
                    initSetSelect2($('#setItemsTableBody'));
                    calculateSetSummary();
                }, 50);
            }).fail(function() {
                alert('Could not fetch set details.');
            });
        });

        // Table Live Search & Filter
        $('#filterSetSearch, #filterSetStatus').on('input change', function() {
            const query = ($('#filterSetSearch').val() || '').toLowerCase().trim();
            const status = ($('#filterSetStatus').val() || '').toLowerCase().trim();

            let visibleCount = 0;
            const $rows = $('#setsListingTableBody .set-row');
            
            $rows.each(function() {
                const $row = $(this);
                const rowSearch = $row.attr('data-search') || '';
                const rowStatus = $row.attr('data-status') || '';

                const matchQuery = !query || rowSearch.indexOf(query) > -1;
                const matchStatus = !status || rowStatus === status;

                if (matchQuery && matchStatus) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });

            $('#searchResultCount').text(visibleCount + ' Sets Found');
            if (visibleCount === 0 && $rows.length > 0) {
                $('#trNoFilterResults').show();
            } else {
                $('#trNoFilterResults').hide();
            }
        });
    });
</script>
@endsection
