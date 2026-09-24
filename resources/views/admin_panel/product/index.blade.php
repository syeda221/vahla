@extends('admin_panel.layout.app')
@section('content')

<style>
    /* ── LAYOUT RESET: make sure page uses full width cleanly ── */
    .erp-page { background: #f8fafc; min-height: calc(100vh - 80px); padding: 20px 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .erp-page .container-fluid { max-width: 100%; box-sizing: border-box; }

    /* ── ERP Product Page – Premium Design System ── */
    :root {
        --erp-primary:    #6366f1;
        --erp-primary-lt: #eef2ff;
        --erp-success:    #10b981;
        --erp-success-lt: #ecfdf5;
        --erp-warning:    #f59e0b;
        --erp-warning-lt: #fffbeb;
        --erp-danger:     #ef4444;
        --erp-danger-lt:  #fef2f2;
        --erp-info:       #3b82f6;
        --erp-info-lt:    #eff6ff;
        --erp-border:     #e2e8f0;
        --erp-bg:         #f8fafc;
        --erp-card-bg:    #ffffff;
        --erp-text:       #0f172a;
        --erp-muted:      #64748b;
        --erp-radius:     14px;
        --erp-shadow:     0 2px 8px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.02);
        --erp-shadow-md:  0 8px 30px rgba(15,23,42,0.08);
    }

    /* ── Stats Cards ── */
    .stat-card {
        background: var(--erp-card-bg);
        border-radius: var(--erp-radius);
        border: 1px solid var(--erp-border);
        box-shadow: var(--erp-shadow);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--erp-shadow-md); }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .stat-card .stat-label { font-size: .73rem; font-weight: 700; color: var(--erp-muted); text-transform: uppercase; letter-spacing: .5px; }
    .stat-card .stat-value { font-size: 1.4rem; font-weight: 800; color: var(--erp-text); line-height: 1.2; }
    .stat-card .stat-sub   { font-size: .72rem; color: var(--erp-muted); margin-top: 2px; }

    /* ── Main Card ── */
    .erp-card {
        background: var(--erp-card-bg);
        border-radius: var(--erp-radius);
        border: 1px solid var(--erp-border);
        box-shadow: var(--erp-shadow);
        overflow: hidden;
    }
    .erp-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .erp-card-header .page-title { font-size: 1.05rem; font-weight: 800; color: var(--erp-text); margin: 0; display: flex; align-items: center; gap: 8px; }
    .erp-card-header .page-sub   { font-size: .78rem; color: var(--erp-muted); margin: 2px 0 0 0; }
    
    /* ── Header action buttons group ── */
    .erp-hdr-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* ── Filter Panel ── */
    .filter-panel {
        background: #f8fafc;
        border-bottom: 1px solid var(--erp-border);
        padding: 16px 20px;
    }
    .filter-panel .filter-heading {
        font-size: .74rem; font-weight: 800; text-transform: uppercase; letter-spacing: .6px;
        color: var(--erp-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;
    }
    .filter-panel label.form-label {
        font-size: .72rem; font-weight: 700; color: var(--erp-muted);
        text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px;
    }

    .erp-filter-row {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        width: 100%;
    }
    .erp-filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 150px;
        min-width: 130px;
        max-width: 240px;
        box-sizing: border-box;
    }
    .erp-filter-search { flex: 1 1 220px; max-width: 320px; }
    .erp-filter-btns   { flex: 0 0 auto; max-width: none; min-width: 0; }

    .erp-flabel {
        display: block;
        font-size: .69rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--erp-muted);
        margin-bottom: 5px;
        white-space: nowrap;
    }
    .erp-finput {
        display: block;
        width: 100%;
        height: 38px;
        padding: 0 12px;
        border: 1px solid var(--erp-border);
        border-radius: 8px;
        font-size: .83rem;
        font-weight: 500;
        color: var(--erp-text);
        background: #fff;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .erp-finput:focus {
        border-color: var(--erp-primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }
    .search-wrap { position: relative; width: 100%; }
    .search-wrap .search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        color: var(--erp-muted); font-size: 13px; pointer-events: none;
        z-index: 1;
    }
    .search-wrap .erp-finput { padding-left: 34px; }

    /* Filter action buttons */
    .btn-erp-filter {
        background: var(--erp-primary); color: #fff; border: none;
        border-radius: 8px; height: 38px; padding: 0 16px;
        font-size: .83rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;
        transition: background .15s, transform .1s; cursor: pointer;
    }
    .btn-erp-filter:hover { background: #4f46e5; color: #fff; transform: translateY(-1px); }
    .btn-erp-clear {
        background: #fff; color: var(--erp-muted); border: 1px solid var(--erp-border);
        border-radius: 8px; height: 38px; padding: 0 14px;
        font-size: .83rem; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;
        transition: all .15s; text-decoration: none; cursor: pointer;
    }
    .btn-erp-clear:hover { border-color: var(--erp-danger); color: var(--erp-danger); background: var(--erp-danger-lt); }

    /* Active filter badges */
    .active-filters { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 12px; }
    .filter-chip {
        background: var(--erp-primary-lt); color: var(--erp-primary);
        border: 1px solid #c7d2fe; border-radius: 20px;
        padding: 3px 10px; font-size: .72rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 4px;
    }

    /* ── Header action buttons ── */
    .btn-hdr {
        border-radius: 8px; padding: 8px 14px; font-size: .82rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; transition: all .15s;
        border: 1px solid transparent; text-decoration: none; cursor: pointer;
    }
    .btn-hdr-outline { background: #fff; color: var(--erp-muted); border-color: var(--erp-border); }
    .btn-hdr-outline:hover { border-color: #94a3b8; color: var(--erp-text); background: var(--erp-bg); }
    .btn-hdr-success { background: #10b981; color: #fff; border-color: #10b981; }
    .btn-hdr-success:hover { background: #059669; border-color: #059669; color: #fff; transform: translateY(-1px); }
    .btn-hdr-warning { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .btn-hdr-warning:hover { background: #f59e0b; color: #fff; border-color: #f59e0b; }
    .btn-hdr-primary { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; border-color: #6366f1; box-shadow: 0 2px 6px rgba(99,102,241,0.25); }
    .btn-hdr-primary:hover { background: #4338ca; border-color: #4338ca; color: #fff; transform: translateY(-1px); }

    /* ── Table ── */
    .erp-table-wrap { padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #productTable {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: .82rem;
    }
    #productTable thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        text-transform: uppercase;
        font-size: .67rem;
        letter-spacing: .5px;
        padding: 12px 14px;
        border-bottom: 2px solid var(--erp-border) !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        white-space: nowrap;
        position: sticky; top: 0; z-index: 2;
    }
    #productTable tbody td {
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: var(--erp-text);
        vertical-align: middle;
        white-space: nowrap;
    }
    #productTable tbody td.td-item-details { white-space: normal; min-width: 180px; max-width: 260px; }
    #productTable tbody tr { transition: background .12s ease; }
    #productTable tbody tr:hover { background: #f8fafc !important; }
    #productTable tbody tr.row-inactive { opacity: .6; background: #fafafa; }

    /* Image cell */
    .product-img {
        width: 40px; height: 40px; object-fit: cover;
        border-radius: 8px; border: 1px solid var(--erp-border);
        transition: transform .2s ease, box-shadow .2s ease;
        cursor: pointer; display: block;
    }
    .product-img:hover { transform: scale(1.35); z-index: 10; position: relative; box-shadow: var(--erp-shadow-md); }
    .no-img-badge {
        width: 40px; height: 40px; border-radius: 8px;
        background: #f1f5f9; border: 1px dashed #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: #94a3b8;
    }

    /* Item details cell */
    .item-name { font-weight: 700; color: var(--erp-text); margin-bottom: 3px; font-size: .85rem; line-height: 1.3; }
    .item-meta { font-size: .7rem; color: var(--erp-muted); display: flex; flex-wrap: wrap; gap: 4px; align-items: center; margin-top: 2px; }
    .item-meta .meta-chip {
        background: #f1f5f9; border-radius: 4px; padding: 2px 6px;
        font-size: .67rem; font-weight: 600; color: #475569;
        display: inline-flex; align-items: center; gap: 3px;
    }
    .item-code { font-family: 'Courier New', monospace; background: #f8fafc; border: 1px solid var(--erp-border); border-radius: 4px; padding: 1px 6px; font-size: .7rem; font-weight: 600; color: #334155; }

    /* Stock badge */
    .stock-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: var(--erp-success-lt); color: var(--erp-success);
        border: 1px solid #a7f3d0; border-radius: 6px;
        padding: 3px 8px; font-size: .75rem; font-weight: 700;
        white-space: nowrap;
    }
    .stock-badge.low  { background: var(--erp-danger-lt); color: var(--erp-danger); border-color: #fecaca; }
    .stock-badge.zero { background: #fef3c7; color: #b45309; border-color: #fde68a; }
    .stock-unit { font-weight: 500; font-size: .67rem; opacity: .85; }

    /* Price cells */
    .price-purchase { color: var(--erp-muted); font-weight: 600; font-size: .81rem; white-space: nowrap; }
    .price-sale     { color: var(--erp-success); font-weight: 800; font-size: .84rem; white-space: nowrap; }

    /* Status badge */
    .status-active {
        background: var(--erp-success-lt); color: var(--erp-success);
        border: 1px solid #a7f3d0; border-radius: 20px;
        padding: 3px 10px; font-size: .7rem; font-weight: 700;
        letter-spacing: .2px; white-space: nowrap;
    }
    .status-inactive {
        background: #f1f5f9; color: #64748b;
        border: 1px solid #cbd5e1; border-radius: 20px;
        padding: 3px 10px; font-size: .7rem; font-weight: 700;
        white-space: nowrap;
    }

    /* Action buttons – single row, compact */
    .action-group {
        display: flex; align-items: center; gap: 4px;
        flex-wrap: nowrap;
        justify-content: flex-start;
    }
    .btn-act {
        border-radius: 6px; padding: 4px 9px; font-size: .72rem;
        font-weight: 600; display: inline-flex; align-items: center; gap: 4px;
        border: 1px solid transparent; transition: all .12s; cursor: pointer;
        line-height: 1.5; white-space: nowrap; flex-shrink: 0;
    }
    .btn-act-view    { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
    .btn-act-view:hover    { background: #0284c7; color: #fff; }
    .btn-act-edit    { background: var(--erp-primary-lt); color: var(--erp-primary); border-color: #c7d2fe; }
    .btn-act-edit:hover    { background: var(--erp-primary); color: #fff; }
    .btn-act-barcode { background: var(--erp-success-lt); color: var(--erp-success); border-color: #a7f3d0; }
    .btn-act-barcode:hover { background: var(--erp-success); color: #fff; }
    .btn-act-deact   { background: var(--erp-danger-lt); color: var(--erp-danger); border-color: #fecaca; }
    .btn-act-deact:hover   { background: var(--erp-danger); color: #fff; }
    .btn-act-quick   { background: #fffbeb; color: #d97706; border-color: #fde68a; font-weight:700; }
    .btn-act-quick:hover   { background: #f59e0b; color: #fff; border-color: #f59e0b; }
    .btn-act-act     { background: var(--erp-success-lt); color: var(--erp-success); border-color: #a7f3d0; }
    .btn-act-act:hover     { background: var(--erp-success); color: #fff; }

    /* ── Pagination ── */
    .erp-pagination { padding: 14px 20px; border-top: 1px solid var(--erp-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .erp-pagination .showing { font-size: .78rem; color: var(--erp-muted); }
    .erp-pagination .page-link { border-radius: 6px !important; border-color: var(--erp-border) !important; color: var(--erp-text) !important; font-size: .8rem; padding: 5px 12px; }
    .erp-pagination .page-item.active .page-link { background: var(--erp-primary) !important; border-color: var(--erp-primary) !important; color: #fff !important; }

    /* ── Actions column – force min-width so buttons never wrap ── */
    #productTable th:last-child,
    #productTable td:last-child { min-width: 240px; }

    /* ── Select checkbox ── */
    input[type="checkbox"].row-check { width: 16px; height: 16px; accent-color: var(--erp-primary); cursor: pointer; }

    /* ── DataTable override ── */
    div.dataTables_wrapper div.dataTables_length select { width: 75px !important; }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate { display: none !important; }
    .dataTables_wrapper { overflow-x: visible !important; }

    /* ════════════════════════════════════════════════════
       MOBILE RESPONSIVE  ≤ 768px
    ════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .erp-page { padding: 12px 0; }
        .container-fluid { padding-left: 10px !important; padding-right: 10px !important; }

        /* Stats: 2-col grid */
        .stat-grid { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
        .stat-card { padding: 12px; gap: 10px; }
        .stat-icon { width: 36px; height: 36px; font-size: 15px; border-radius: 10px; }
        .stat-card .stat-value { font-size: 1.15rem; }
        .stat-card .stat-label { font-size: .65rem; }
        .stat-card .stat-sub   { display: none; }

        /* Card header: title + buttons */
        .erp-card-header { flex-direction: column; align-items: flex-start; gap: 12px; padding: 14px; }
        .erp-hdr-actions {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .btn-hdr {
            width: 100% !important;
            justify-content: center !important;
            text-align: center !important;
            font-size: .78rem !important;
            padding: 9px 10px !important;
            box-sizing: border-box !important;
            border-radius: 10px !important;
            height: 38px !important;
        }

        /* Filter panel: stack fields */
        .filter-panel { padding: 12px 14px; }
        .erp-filter-row { gap: 10px; }
        .erp-filter-field,
        .erp-filter-search { flex: 1 1 100%; max-width: 100%; }
        .erp-filter-btns { flex: 1 1 100%; width: 100%; }
        .erp-filter-btns > div { width: 100%; display: flex; gap: 8px; }
        .btn-erp-filter,
        .btn-erp-clear { flex: 1; text-align: center; justify-content: center; height: 40px; }

        /* Table wrap */
        .erp-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0;
        }
        #productTable {
            min-width: 720px !important;
            font-size: .8rem;
        }
        #productTable thead th { padding: 10px 10px; font-size: .64rem; }
        #productTable tbody td { padding: 8px 10px; }

        /* Pagination */
        .erp-pagination {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
        }
        .erp-pagination nav { width: 100%; }
    }

    /* ── Mobile Product Cards ── */
    .mobile-product-cards { display: none; padding: 14px; }

    @media (max-width: 768px) {
        .erp-table-wrap { display: none !important; }
        .mobile-product-cards { display: flex; flex-direction: column; gap: 12px; }
    }

    .prod-mcard {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.03);
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .prod-mcard.row-inactive { opacity: 0.65; background: #f8fafc; }
    .prod-mcard-hd { display: flex; align-items: flex-start; gap: 12px; }
    .prod-mcard-body {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .prod-mcard-price { font-size: 1.05rem; font-weight: 800; color: #10b981; }
    .prod-mcard-actions {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        margin-top: 12px;
        width: 100%;
    }
    .prod-mcard-actions .btn-act {
        width: 100% !important;
        justify-content: center !important;
        height: 38px !important;
        font-size: .78rem !important;
        border-radius: 8px !important;
    }

    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr 1fr !important; gap: 8px !important; }
        .stat-card { padding: 10px; }
        .stat-card .stat-value { font-size: 1.05rem; }
        #productTable { min-width: 660px !important; }
        .btn-act { padding: 3px 6px; font-size: .68rem; }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-3 py-3">

    {{-- ── Stats Row ── --}}
    <div class="stat-grid mb-4" style="display:grid; grid-template-columns: repeat(4,1fr); gap:16px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-box-open"></i></div>
            <div>
                <div class="stat-label">Total Products</div>
                <div class="stat-value">{{ $products->total() }}</div>
                <div class="stat-sub">in catalog</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ecfdf5; color:#059669;"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-label">Active</div>
                <div class="stat-value">{{ $products->getCollection()->where('is_active',1)->count() }}</div>
                <div class="stat-sub">on this page</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2; color:#dc2626;"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="stat-label">Inactive</div>
                <div class="stat-value">{{ $products->getCollection()->where('is_active',0)->count() }}</div>
                <div class="stat-sub">on this page</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-filter"></i></div>
            <div>
                <div class="stat-label">Filtered Results</div>
                <div class="stat-value">{{ $products->total() }}</div>
                <div class="stat-sub">
                    @if(request()->hasAny(['search','category_id','brand_id','status']))
                        <span style="color:#d97706; font-weight:700;">Filters active</span>
                    @else
                        all products
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Card ── --}}
    <div class="erp-card">

        {{-- Card Header --}}
        <div class="erp-card-header">
            <div>
                <p class="page-title"><i class="fas fa-box me-2" style="color:var(--erp-primary);"></i>Product Catalog</p>
                <p class="page-sub">Manage, filter and bulk-edit your entire product inventory</p>
            </div>
            <div class="erp-hdr-actions">
                <a href="{{ route('products.template') }}" class="btn-hdr btn-hdr-outline" title="Download blank CSV template">
                    <i class="fas fa-file-csv"></i> Template
                </a>
                <a href="{{ route('products.export') }}" class="btn-hdr btn-hdr-success" title="Export all products to CSV">
                    <i class="fas fa-file-download"></i> Export CSV
                </a>
                @if (auth()->user()->can('products.create') || auth()->user()->email === 'admin@admin.com')
                    <button type="button" class="btn-hdr btn-hdr-warning" id="openImportModalBtn">
                        <i class="fas fa-file-upload"></i> Import CSV
                    </button>
                    <a href="create_prodcut" class="btn-hdr btn-hdr-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                @endif
            </div>
        </div>


        {{-- ── Filter Panel ── --}}
        <div class="filter-panel">
            <div class="filter-heading"><i class="fas fa-sliders-h"></i> Filters &amp; Search</div>
            <form method="GET" action="{{ route('product') }}" id="filterForm">
                <div class="erp-filter-row">

                    {{-- Search --}}
                    <div class="erp-filter-field erp-filter-search">
                        <label class="erp-flabel">Search</label>
                        <div class="search-wrap">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="erp-finput" placeholder="Item name, code, barcode…">
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="erp-filter-field">
                        <label class="erp-flabel">Category</label>
                        <select name="category_id" class="erp-finput">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand --}}
                    <div class="erp-filter-field">
                        <label class="erp-flabel">Brand</label>
                        <select name="brand_id" class="erp-finput">
                            <option value="">All Brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="erp-filter-field" style="min-width:120px;">
                        <label class="erp-flabel">Status</label>
                        <select name="status" class="erp-finput">
                            <option value="">All Status</option>
                            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>✅ Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⛔ Inactive</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="erp-filter-field erp-filter-btns">
                        <label class="erp-flabel">&nbsp;</label>
                        <div style="display:flex; gap:6px;">
                            <button type="submit" class="btn-erp-filter">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('product', ['reset' => 1]) }}" class="btn-erp-clear">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Active filter chips --}}
                @if(request()->hasAny(['search','category_id','brand_id','status']))
                <div class="active-filters" style="margin-top:10px;">
                    <span style="font-size:.72rem; font-weight:600; color:var(--erp-muted);">Active:</span>
                    @if(request('search'))
                        <span class="filter-chip"><i class="fas fa-search" style="font-size:.6rem;"></i> "{{ request('search') }}"</span>
                    @endif
                    @if(request('category_id'))
                        <span class="filter-chip"><i class="fas fa-list" style="font-size:.6rem;"></i> {{ $categories->firstWhere('id', request('category_id'))->name ?? 'Category' }}</span>
                    @endif
                    @if(request('brand_id'))
                        <span class="filter-chip"><i class="fas fa-trademark" style="font-size:.6rem;"></i> {{ $brands->firstWhere('id', request('brand_id'))->name ?? 'Brand' }}</span>
                    @endif
                    @if(request('status'))
                        <span class="filter-chip"><i class="fas fa-circle" style="font-size:.6rem;"></i> {{ ucfirst(request('status')) }}</span>
                    @endif
                    <span style="font-size:.72rem; color:var(--erp-muted);">— <strong>{{ $products->total() }}</strong> result(s)</span>
                </div>
                @endif
            </form>
        </div>

        {{-- ── Success Alert ── --}}
        @if (session()->has('success'))
        <div class="mx-4 mt-3 alert d-flex align-items-center gap-2" style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:8px; color:#065f46; font-size:.85rem; padding:10px 14px;">
            <i class="fas fa-check-circle" style="color:#059669; font-size:16px;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size:.6rem;"></button>
        </div>
        @endif


        {{-- ── Table ── --}}
        <div class="erp-table-wrap table-responsive">

                <table id="productTable" class="table table-hover align-middle nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:36px;"><input type="checkbox" id="selectAll" class="row-check"></th>
                            <th style="width:40px;">#</th>
                            <th style="width:52px;">Image</th>
                            <th>Item Details</th>
                            <th>Stock</th>
                            <th>Purchase Price</th>
                            <th>Sale Price</th>
                            <th style="width:90px;">Status</th>
                            <th class="text-center" style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $product)
                            @php
                                $stockPieces = (float) ($product->warehouse_stocks_sum_total_pieces ?? 0);
                                $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;

                                $productUnitName = $product->unit->name ?? match($product->size_mode) {
                                    'by_kg' => 'Kg',
                                    'by_gm' => 'Gm',
                                    'by_ton' => 'Ton',
                                    'by_meter' => 'Mtr',
                                    'by_feet' => 'Ft',
                                    'by_cartons' => 'Carton',
                                    'by_size' => 'M²',
                                    default => 'Pcs',
                                };

                                if (($product->size_mode === 'by_cartons' || $product->size_mode === 'by_size') && $ppb > 1) {
                                    $boxes = floor($stockPieces / $ppb);
                                    $loose = $stockPieces % $ppb;
                                    $stockDisplay = $loose > 0 ? "{$boxes}.{$loose}" : "{$boxes}";
                                    $stockUnit    = $loose > 0 ? 'Box.Loose' : 'Boxes';
                                } else {
                                    $stockDisplay = (float)$stockPieces == (int)$stockPieces ? (int)$stockPieces : rtrim(rtrim(number_format($stockPieces, 2), '0'), '.');
                                    $stockUnit    = $productUnitName;
                                }
                                $stockClass = $stockPieces == 0 ? 'zero' : (($product->alert_carton_quantity && $stockPieces <= $product->alert_carton_quantity) ? 'low' : '');

                                if ($product->size_mode === 'by_size') {
                                    $m2 = ($product->height * $product->width) / 10000;
                                    $tradePrice  = $m2 * (float)$product->purchase_price_per_m2;
                                    $retailPrice = $m2 * (float)$product->price_per_m2;
                                } else {
                                    $tradePrice  = (float)$product->purchase_price_per_piece;
                                    $retailPrice = (float)$product->sale_price_per_piece ?: (float)$product->sale_price_per_box;
                                }

                                $rowVariants = [];
                                if (!empty($product->color)) {
                                    $rawC = $product->color;
                                    if (is_string($rawC)) {
                                        $decodedC = json_decode($rawC, true);
                                        if (is_string($decodedC)) $decodedC = json_decode($decodedC, true) ?? $decodedC;
                                    } else {
                                        $decodedC = $rawC;
                                    }
                                    if (is_array($decodedC) && count($decodedC) > 0) {
                                        $rowVariants = isset($decodedC['name']) || isset($decodedC['color']) ? (isset($decodedC[0]) ? $decodedC : [$decodedC]) : array_values($decodedC);
                                    }
                                }
                            @endphp
                            <tr id="product-row-{{ $product->id }}" class="{{ $product->is_active ? '' : 'row-inactive' }}">
                                <td><input type="checkbox" class="selectProduct row-check" value="{{ $product->id }}"></td>
                                <td style="color:var(--erp-muted); font-size:.72rem;">{{ $products->firstItem() + $key }}</td>
                                <td>
                                    @if ($product->image)
                                        <img src="{{ asset('uploads/products/' . $product->image) }}"
                                            alt="{{ $product->item_name }}" class="product-img">
                                    @else
                                        <div class="no-img-badge"><i class="fas fa-image"></i></div>
                                    @endif
                                </td>
                                <td class="td-item-details" id="td-item-details-{{ $product->id }}">
                                    <div class="item-name" id="p-name-{{ $product->id }}">{{ $product->item_name }}</div>
                                    <div class="item-meta" id="p-meta-{{ $product->id }}">
                                        <span class="item-code" id="p-code-{{ $product->id }}">{{ $product->item_code }}</span>
                                        @if($product->category_relation)
                                            <span class="meta-chip" id="p-cat-{{ $product->id }}"><i class="fas fa-folder" style="font-size:.6rem;"></i> {{ $product->category_relation->name }}@if($product->sub_category_relation) &rsaquo; {{ $product->sub_category_relation->name }}@endif</span>
                                        @endif
                                        @if($product->brand)
                                            <span class="meta-chip" id="p-brand-{{ $product->id }}"><i class="fas fa-trademark" style="font-size:.6rem;"></i> {{ $product->brand->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td id="td-stock-{{ $product->id }}">
                                    <div style="display:flex; flex-direction:column; gap:4px;">
                                        <div>
                                            <span class="stock-badge {{ $stockClass }}" id="p-stock-badge-{{ $product->id }}">
                                                <i class="fas fa-cubes" style="font-size:.65rem;"></i>
                                                <span class="stock-num">{{ $stockDisplay }}</span>
                                                <span class="stock-unit">{{ $stockUnit }}</span>
                                            </span>
                                        </div>
                                        <div class="variant-stock-preview" id="p-var-preview-{{ $product->id }}" style="display:flex; flex-wrap:wrap; gap:3px; max-width:240px; font-size:.68rem;">
                                            @foreach($rowVariants as $vIdx => $v)
                                                @php
                                                    $vColorVal = $v['color'] ?? $v['variant_color'] ?? '';
                                                    $vSizeVal  = $v['size'] ?? $v['variant_size'] ?? '';
                                                    $vUnitVal  = $v['unit'] ?? $v['variant_unit'] ?? '';
                                                    $vWeight   = $v['weight_per_piece'] ?? '';
                                                    $isBase    = !empty($v['is_base_variant']) || ($product->size_mode === 'by_kg' && isset($v['conv_factor']) && (float)$v['conv_factor'] == 1);

                                                    if ($vColorVal !== '-' && $vColorVal !== '' && $vSizeVal !== '-' && $vSizeVal !== '') {
                                                        $vLbl = "{$vColorVal} / {$vSizeVal}";
                                                    } elseif ($vColorVal !== '-' && $vColorVal !== '') {
                                                        $vLbl = $vColorVal;
                                                    } elseif ($vSizeVal !== '-' && $vSizeVal !== '') {
                                                        $vLbl = $vSizeVal;
                                                    } elseif ($product->size_mode === 'by_kg') {
                                                        if ($isBase) {
                                                            $vLbl = 'Kg (Base)';
                                                        } elseif ($vWeight && (float)$vWeight > 0) {
                                                            $vLbl = ((float)$vWeight >= 1000 ? ((float)$vWeight/1000) . 'kg' : (float)$vWeight . 'g') . ' Pcs';
                                                        } elseif ($vUnitVal) {
                                                            $vLbl = $vUnitVal;
                                                        } else {
                                                            $vLbl = 'Piece';
                                                        }
                                                    } elseif ($vUnitVal) {
                                                        $vLbl = $vUnitVal;
                                                    } elseif (!empty($v['name']) && $v['name'] !== $product->item_name) {
                                                        $vLbl = $v['name'];
                                                    } else {
                                                        $vLbl = 'Var ' . ($vIdx + 1);
                                                    }

                                                    $vQ = (float)($v['stock'] ?? $v['variant_stock'] ?? 0);
                                                    if ($product->size_mode === 'by_kg' && isset($v['conv_factor']) && (float)$v['conv_factor'] > 0) {
                                                        $cf = (float)$v['conv_factor'];
                                                        $vQ = $cf == 1 ? $stockPieces : (int)floor($stockPieces / $cf);
                                                    }
                                                    $vQDisplay = (float)$vQ == (int)$vQ ? (int)$vQ : rtrim(rtrim(number_format($vQ, 2), '0'), '.');
                                                    $colorStyle = $vQ > 0 ? '#059669' : '#dc2626';

                                                    $vUnitSuffix = '';
                                                    if ($product->size_mode === 'by_kg') {
                                                        $vUnitSuffix = $isBase ? ' Kg' : ' Pcs';
                                                    } elseif (!empty($vUnitVal)) {
                                                        $vUnitSuffix = ' ' . $vUnitVal;
                                                    }
                                                @endphp
                                                <span style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:4px; padding:2px 6px; font-weight:600; white-space:nowrap; display:inline-flex; align-items:center; gap:3px;">
                                                    <span style="color:#475569;">{{ $vLbl }}:</span>
                                                    <strong style="color:{{ $colorStyle }};">{{ $vQDisplay }}{{ $vUnitSuffix }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="price-purchase" id="p-trade-price-{{ $product->id }}">Rs. {{ number_format($tradePrice, 2) }}</td>
                                <td class="price-sale" id="p-retail-price-{{ $product->id }}">Rs. {{ number_format($retailPrice, 2) }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="status-active" id="status-badge-{{ $product->id }}">Active</span>
                                    @else
                                        <span class="status-inactive" id="status-badge-{{ $product->id }}">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-group">
                                        @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                                            <button type="button" class="btn-act btn-act-quick quickEditProductBtn"
                                                data-id="{{ $product->id }}" title="⚡ Quick Edit & Adjust Stock">
                                                <i class="fas fa-bolt"></i> Quick Edit
                                            </button>
                                        @endif
                                        <button type="button" class="btn-act btn-act-view viewProductBtn"
                                            data-id="{{ $product->id }}" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn-act btn-act-edit" title="Edit Full Product">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </a>
                                        @endif
                                        <a href="{{ route('generate-barcode-image', $product->id) }}"
                                            class="btn-act btn-act-barcode" title="Generate Barcode">
                                            <i class="fas fa-barcode"></i>
                                        </a>
                                        @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                                            <button type="button"
                                                class="btn-act {{ $product->is_active ? 'btn-act-deact' : 'btn-act-act' }} toggle-active-btn"
                                                data-id="{{ $product->id }}"
                                                data-active="{{ $product->is_active ? '1' : '0' }}"
                                                data-name="{{ $product->item_name }}"
                                                title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $product->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>{{-- /erp-table-wrap --}}

        {{-- ── Mobile Product Cards (Shown only on mobile < 768px for 100% user-friendly view) ── --}}
        <div class="mobile-product-cards">
            @foreach ($products as $product)
                @php
                    $stockPieces = (float) ($product->warehouse_stocks_sum_total_pieces ?? 0);
                    $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;

                    $productUnitName = $product->unit->name ?? match($product->size_mode) {
                        'by_kg' => 'Kg',
                        'by_gm' => 'Gm',
                        'by_ton' => 'Ton',
                        'by_meter' => 'Mtr',
                        'by_feet' => 'Ft',
                        'by_cartons' => 'Carton',
                        'by_size' => 'M²',
                        default => 'Pcs',
                    };

                    if (($product->size_mode === 'by_cartons' || $product->size_mode === 'by_size') && $ppb > 1) {
                        $boxes = floor($stockPieces / $ppb);
                        $loose = $stockPieces % $ppb;
                        $stockDisplay = $loose > 0 ? "{$boxes}.{$loose}" : "{$boxes}";
                        $stockUnit    = $loose > 0 ? 'Box.Loose' : 'Boxes';
                    } else {
                        $stockDisplay = (float)$stockPieces == (int)$stockPieces ? (int)$stockPieces : rtrim(rtrim(number_format($stockPieces, 2), '0'), '.');
                        $stockUnit    = $productUnitName;
                    }
                    $stockClass = $stockPieces == 0 ? 'zero' : (($product->alert_carton_quantity && $stockPieces <= $product->alert_carton_quantity) ? 'low' : '');

                    if ($product->size_mode === 'by_size') {
                        $m2 = ($product->height * $product->width) / 10000;
                        $tradePrice  = $m2 * (float)$product->purchase_price_per_m2;
                        $retailPrice = $m2 * (float)$product->price_per_m2;
                    } else {
                        $tradePrice  = (float)$product->purchase_price_per_piece;
                        $retailPrice = (float)$product->sale_price_per_piece ?: (float)$product->sale_price_per_box;
                    }
                @endphp
                <div class="prod-mcard {{ $product->is_active ? '' : 'row-inactive' }}" id="pmcard-{{ $product->id }}">
                    <div class="prod-mcard-hd">
                        <input type="checkbox" class="selectProduct row-check mt-1" value="{{ $product->id }}">
                        @if ($product->image)
                            <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->item_name }}" class="product-img">
                        @else
                            <div class="no-img-badge"><i class="fas fa-image"></i></div>
                        @endif
                        <div style="flex:1; min-width:0;">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <div class="item-name mb-0 text-truncate">{{ $product->item_name }}</div>
                                @if($product->is_active)
                                    <span class="status-active" id="mstatus-badge-{{ $product->id }}">Active</span>
                                @else
                                    <span class="status-inactive" id="mstatus-badge-{{ $product->id }}">Inactive</span>
                                @endif
                            </div>
                            <div class="item-meta mt-1">
                                <span class="item-code">{{ $product->item_code }}</span>
                                @if($product->category_relation)
                                    <span class="meta-chip"><i class="fas fa-list"></i> {{ $product->category_relation->name }}</span>
                                @endif
                                @if($product->brand)
                                    <span class="meta-chip"><i class="fas fa-trademark"></i> {{ $product->brand->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="prod-mcard-body">
                        <div>
                            <div style="font-size:.68rem; font-weight:700; color:var(--erp-muted); text-transform:uppercase;">Sale Price</div>
                            <div class="prod-mcard-price">Rs. {{ number_format($retailPrice, 2) }}</div>
                            <div style="font-size:.7rem; color:var(--erp-muted);">Cost: Rs. {{ number_format($tradePrice, 2) }}</div>
                        </div>
                        <div class="text-end">
                            <div style="font-size:.68rem; font-weight:700; color:var(--erp-muted); text-transform:uppercase; margin-bottom:2px;">Stock</div>
                            <span class="stock-badge {{ $stockClass }}">
                                <i class="fas fa-cubes"></i> {{ $stockDisplay }} <span class="stock-unit">{{ $stockUnit }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="prod-mcard-actions">
                        @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                            <button type="button" class="btn-act btn-act-quick quickEditProductBtn" data-id="{{ $product->id }}">
                                <i class="fas fa-bolt"></i> Quick Edit
                            </button>
                        @endif
                        <button type="button" class="btn-act btn-act-view viewProductBtn" data-id="{{ $product->id }}">
                            <i class="fas fa-eye"></i> View
                        </button>
                        @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                            <a href="{{ route('products.edit', $product->id) }}" class="btn-act btn-act-edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('generate-barcode-image', $product->id) }}" class="btn-act btn-act-barcode">
                            <i class="fas fa-barcode"></i> Barcode
                        </a>
                        @if (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com')
                            <button type="button"
                                class="btn-act {{ $product->is_active ? 'btn-act-deact' : 'btn-act-act' }} toggle-active-btn"
                                data-id="{{ $product->id }}"
                                data-active="{{ $product->is_active ? '1' : '0' }}"
                                data-name="{{ $product->item_name }}">
                                <i class="fas {{ $product->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>{{-- /mobile-product-cards --}}


        {{-- ── Pagination ── --}}
        <div class="erp-pagination">
            <span class="showing">
                Showing <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong>
                of <strong>{{ $products->total() }}</strong> products
            </span>
            {{ $products->appends(request()->query())->links() }}
        </div>

    </div>{{-- /erp-card --}}
        </div>{{-- /container-fluid --}}
    </div>{{-- /main-content-inner --}}
</div>{{-- /main-content --}}


{{-- ══════════════════════════════════════════════════════════════
     IMPORT MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; border-bottom: none;">
                <div>
                    <h5 class="modal-title fw-bold" id="importModalLabel">
                        <i class="fas fa-file-upload me-2"></i>Import Products from CSV
                    </h5>
                    <small style="color: rgba(255,255,255,0.85);">New products will be created. Existing ones (matched by Barcode or Item Code) will be updated.</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.8; text-shadow:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background:#f8fafc;">
                <form action="{{ route('products.import.validate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(session('error'))
                        <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
                    @endif
                    <div class="alert alert-info d-flex gap-2 align-items-start mb-4" style="font-size:.85rem;">
                        <i class="fas fa-info-circle fs-5 mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>How to use:</strong><br>
                            1. Download the <a href="{{ route('products.template') }}" class="alert-link">CSV Template</a> first.<br>
                            2. Fill in your data in Excel and save as <strong>CSV</strong>.<br>
                            3. Upload here to validate and preview the changes.<br>
                            4. Confirm the preview to actually import the data.
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="fw-bold">Import Mode</label>
                        <select name="import_mode" class="form-select form-control" required>
                            <option value="create">Create (Add new products &amp; variants)</option>
                            <option value="update_only">Update Only (Update existing variants only)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="autoCreate" name="auto_create" value="1" checked>
                            <label class="form-check-label fw-bold ms-2" for="autoCreate">Auto-create missing Category &amp; Brand</label>
                        </div>
                        <small class="text-muted ms-4 d-block">If disabled, missing master data will throw validation errors.</small>
                    </div>
                    <div class="form-group mb-4">
                        <label class="fw-bold">Upload CSV File</label>
                        <input type="file" name="csv_file" class="form-control p-1" accept=".csv,.txt" required>
                        <small class="text-muted">Max 5 MB</small>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-arrow-right me-1"></i> Next: Validate &amp; Preview</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     PRODUCT VIEW MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="productViewModal" tabindex="-1" aria-labelledby="productViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header border-bottom bg-white px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="productViewModalLabel">
                        <span id="view_item_name">Product</span>
                    </h5>
                    <small class="text-muted" id="view_item_subtext">CODE</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="modalLoadingSpinner" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2">Fetching product details…</p>
                </div>
                <div id="modalContentRow" class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center" style="font-size:.88rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="text-start ps-4" style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Variant Name</th>
                                <th style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Size</th>
                                <th style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Color</th>
                                <th style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Stock</th>
                                <th style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Sale Price</th>
                                <th style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Purch Price</th>
                                <th style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Alert</th>
                                <th class="text-end pe-4" style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#475569;">Barcode</th>
                            </tr>
                        </thead>
                        <tbody id="variantTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-white py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



{{-- ══════════════════════════════════════════════════════════════
     QUICK EDIT & STOCK ADJUST MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog" aria-labelledby="quickEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color:#fff; border-bottom: none; padding: 16px 22px;">
                <div>
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="quickEditModalLabel" style="font-size:1.1rem; color:#fff;">
                        <i class="fas fa-bolt" style="color:#fcd34d;"></i> Quick Edit &amp; Stock Adjustment
                    </h5>
                    <div class="d-flex align-items-center gap-2 mt-1" style="font-size:.8rem; color:rgba(255,255,255,0.85);">
                        <span id="qe_item_title" class="fw-bold text-white">Loading...</span>
                        <span>•</span>
                        <code id="qe_item_code" class="text-white" style="background:rgba(255,255,255,0.2); padding:1px 6px; border-radius:4px;">-</code>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.9; text-shadow:none; font-size:1.4rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background:#f8fafc; max-height:calc(85vh - 120px); overflow-y:auto;">
                <div id="qeLoadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2">Loading product data &amp; live stock...</p>
                </div>

                <form id="quickEditForm" class="d-none">
                    <input type="hidden" id="qe_product_id" name="product_id">
                    <input type="hidden" id="qe_size_mode" name="size_mode">

                    {{-- SECTION 1: Master Details --}}
                    <div class="card mb-3 border-0 shadow-sm" style="border-radius:12px; background:#fff;">
                        <div class="card-header bg-white border-bottom py-2 px-3">
                            <span class="fw-bold" style="font-size:.8rem; color:#475569; text-transform:uppercase; letter-spacing:.5px;">
                                <i class="fas fa-info-circle text-primary me-1"></i> Product Information
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Item Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="qe_item_name" name="item_name" required>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Category</label>
                                    <select class="form-select form-control form-control-sm" id="qe_category_id" name="category_id">
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Sub-Category</label>
                                    <select class="form-select form-control form-control-sm" id="qe_sub_category_id" name="sub_category_id">
                                        <option value="">Select Sub-Category</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Brand</label>
                                    <select class="form-select form-control form-control-sm" id="qe_brand_id" name="brand_id">
                                        <option value="">Select Brand</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Unit</label>
                                    <div class="form-control form-control-sm bg-light text-dark fw-bold d-flex align-items-center justify-content-between" style="border-color:#cbd5e1; height:31px; cursor:not-allowed;" title="Unit cannot be modified in Quick Edit">
                                        <span id="qe_unit_display_text" class="d-flex align-items-center gap-1"><i class="fas fa-balance-scale text-primary" style="font-size:.75rem;"></i> <span>Pcs</span></span>
                                        <span class="badge bg-secondary-subtle text-muted" style="font-size:.65rem; border:1px solid #cbd5e1;"><i class="fas fa-lock me-1"></i>Locked</span>
                                    </div>
                                    <input type="hidden" id="qe_unit_id" name="unit_id" value="">
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Low Stock Alert Qty</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm" id="qe_alert_quantity" name="alert_carton_quantity" placeholder="e.g. 5">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Pricing (Commented out - pricing is handled directly per variant/stock below) --}}
                    {{--
                    <div class="card mb-3 border-0 shadow-sm" style="border-radius:12px; background:#fff;">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="font-size:.8rem; color:#475569; text-transform:uppercase; letter-spacing:.5px;">
                                <i class="fas fa-tags text-success me-1"></i> Pricing &amp; Rates
                            </span>
                            <span id="qe_margin_badge" class="badge" style="font-size:.75rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">Margin: 0%</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3" id="qe_standard_prices">
                                <div class="col-md-4 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Purchase Price (Cost) <span class="badge bg-light text-muted border qe-price-unit-badge">/ Pcs</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">Rs.</span>
                                        <input type="number" step="any" min="0" class="form-control qe-price-input" id="qe_purchase_price_per_piece" name="purchase_price_per_piece">
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Sale Price (Retail) <span class="badge bg-light text-muted border qe-price-unit-badge">/ Pcs</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">Rs.</span>
                                        <input type="number" step="any" min="0" class="form-control qe-price-input" id="qe_sale_price_per_piece" name="sale_price_per_piece">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Wholesale Price <span class="badge bg-light text-muted border qe-price-unit-badge">/ Pcs</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">Rs.</span>
                                        <input type="number" step="any" min="0" class="form-control" id="qe_wholesale_price" name="wholesale_price">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 d-none" id="qe_size_prices">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Purchase Price (Per M²)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">Rs.</span>
                                        <input type="number" step="any" min="0" class="form-control qe-price-input" id="qe_purchase_price_per_m2" name="purchase_price_per_m2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Sale Price (Per M²)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">Rs.</span>
                                        <input type="number" step="any" min="0" class="form-control qe-price-input" id="qe_price_per_m2" name="price_per_m2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}

                    {{-- SECTION 3: Live Stock Adjustment & Variants (Audit-Safe) --}}
                    <div class="card mb-3 border-0 shadow-sm" style="border-radius:12px; background:#fff; border-left: 4px solid #f59e0b !important;">
                        <div class="card-header bg-white border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="font-size:.8rem; color:#b45309; text-transform:uppercase; letter-spacing:.5px;">
                                <i class="fas fa-cubes text-warning me-1"></i> Stock Adjustment (Physical Count Audit)
                            </span>
                            <span class="badge" style="font-size:.72rem; background:#fffbeb; color:#d97706; border:1px solid #fde68a;">Safe Audit Log</span>
                        </div>
                        <div class="card-body p-3" style="background:#fffdf7;">
                            <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:.78rem; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; color:#92400e;">
                                <i class="fas fa-shield-alt text-warning fs-6 flex-shrink-0"></i>
                                <div>
                                    Yahan se actual physical stock enter karein. Agar current stock se different hoga to system <strong>Stock Adjustment</strong> record banayega aur Inventory Ledger update karega.
                                </div>
                            </div>
                            
                            <input type="hidden" id="qe_adjust_warehouse_id" name="adjust_warehouse_id" value="1">

                            {{-- SINGLE PRODUCT STOCK INPUT (Shown if no variants) --}}
                            <div id="qe_single_stock_wrap" class="row g-3">
                                <div class="col-md-6 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Current System Stock</label>
                                    <div class="form-control form-control-sm bg-light fw-bold text-dark d-flex align-items-center justify-content-between" style="border-color:#cbd5e1;" id="qe_current_stock_display">
                                        <span>0</span> <small class="text-muted">Pcs</small>
                                    </div>
                                    <input type="hidden" id="qe_current_stock_val" value="0">
                                </div>
                                <div class="col-md-6 col-6">
                                    <label class="form-label small fw-bold text-muted mb-1">New Actual Stock (Pcs)</label>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm fw-bold border-warning" id="qe_new_stock_qty" name="new_stock_qty" placeholder="Enter physical count">
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2 pt-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small text-muted fw-semibold">Adjustment Result:</span>
                                        <span id="qe_diff_badge" class="badge" style="font-size:.8rem; padding:4px 10px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;">0 (No change)</span>
                                    </div>
                                </div>
                            </div>

                            {{-- MULTI-VARIANT STOCK TABLE (Shown if product has variants) --}}
                            <div id="qe_variant_stock_wrap" class="d-none">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-dark"><i class="fas fa-layer-group text-primary me-1"></i> Variant-Wise Specific Stock &amp; Rates:</span>
                                    <span class="badge" id="qe_var_total_diff_badge" style="font-size:.78rem; padding:3px 8px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;">Sum: 0 (No change)</span>
                                </div>
                                <div class="table-responsive rounded border bg-white mb-2">
                                    <table class="table table-sm table-hover align-middle mb-0 text-center" style="font-size:.78rem;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-start ps-3" style="min-width:140px;">Variant Details</th>
                                                <th style="min-width:70px;">Current</th>
                                                <th style="min-width:90px;" class="bg-warning-subtle text-dark">New Actual Stock</th>
                                                <th style="min-width:85px;">Diff (+/-)</th>
                                                <th style="min-width:90px;">Sale Price</th>
                                                <th style="min-width:90px;">Cost Price</th>
                                                <th style="min-width:75px;">Alert Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="qe_variant_stock_tbody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Adjustment Reason / Audit Notes</label>
                                    <input type="text" class="form-control form-control-sm" id="qe_adjustment_reason" name="adjustment_reason" placeholder="e.g. Physical Count Audit, Damaged Stock, Re-counting" value="Physical Count Adjustment via Quick Edit">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer bg-white py-2 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" id="saveQuickEditBtn">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function () {

    // ── Open Import Modal ──
    $('#openImportModalBtn').on('click', function () {
        $('#importModal').modal('show');
    });

    // ── Select All ──
    $('#selectAll').click(function() {
        $('.selectProduct').prop('checked', this.checked);
    });

    // ── DataTable init ── (responsive:false – we use CSS horizontal scroll instead)
    let table = $('#productTable').DataTable({
        responsive: false,
        paging:     false,
        ordering:   true,
        info:       false,
        order:      [[3, 'asc']],
        dom:        'rt',
        scrollX:    false,
        columnDefs: [{ targets: [0, 8], orderable: false, searchable: false }]
    });   // DataTable closes here

    // ── Select All ──
    $('#selectAll').click(function() {
        $('.selectProduct').prop('checked', this.checked);
    });

    // ── View Product Modal ──
    $(document).on('click', '.viewProductBtn', function() {
        let productId = $(this).data('id');
        $('#modalContentRow').addClass('d-none');
        $('#modalLoadingSpinner').removeClass('d-none');
        $('#productViewModal').modal('show');

        $.ajax({
            url:  "/productview/" + productId,
            type: "GET",
            success: function(product) {
                $('#modalLoadingSpinner').addClass('d-none');
                $('#modalContentRow').removeClass('d-none');

                $('#view_item_name').text(product.item_name ?? 'Unknown');
                $('#view_item_subtext').text(
                    (product.item_code ?? '') + ' | ' +
                    (product.category_relation?.name ?? '') + ' | ' +
                    (product.brand?.name ?? '')
                );

                let tbody = $('#variantTableBody');
                tbody.empty();

                let colorList = ['-'];
                let variants  = [];
                if (product.color) {
                    let parsed = product.color;
                    if (typeof parsed === 'string') {
                        try { parsed = JSON.parse(parsed); } catch (e) {}
                    }
                    if (typeof parsed === 'string') {
                        try { parsed = JSON.parse(parsed); } catch (e) {}
                    }
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        if (typeof parsed[0] === 'object' && parsed[0] !== null) {
                            variants = parsed;
                        } else {
                            colorList = parsed;
                        }
                    } else if (typeof parsed === 'object' && parsed !== null) {
                        variants = [parsed];
                    } else if (typeof parsed === 'string') {
                        colorList = [parsed];
                    }
                }

                let sizeStr = '-';
                if (product.size_mode === 'by_size')
                    sizeStr = (product.height || 0) + ' x ' + (product.width || 0) + ' cm';

                let stock     = product.calculated_total_stock_qty ?? 0;
                let alertDef  = product.alert_carton_quantity != null ? product.alert_carton_quantity + '' : '-';
                let salePrice = product.size_mode === 'by_size' ? product.price_per_m2 : (product.sale_price_per_piece || product.sale_price_per_box || 0);
                let purchPrice= product.size_mode === 'by_size' ? product.purchase_price_per_m2 : (product.purchase_price_per_piece || 0);
                let priceLabel= product.size_mode === 'by_size' ? '/m²' : '/pc';

                function stockBadgeHtml(qty, alert) {
                    let isLow = qty > 0 && alert != null && qty <= alert;
                    let cls   = qty == 0 ? 'background:#fef3c7;color:#b45309;border:1px solid #fde68a;' : (isLow ? 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;' : 'background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;');
                    return `<span style="${cls} border-radius:6px; padding:3px 8px; font-size:.78rem; font-weight:600;">${qty}</span>`;
                }

                if (variants.length > 0) {
                    variants.forEach(v => {
                        let vName     = v.name || v.variant_name || product.item_name;
                        let vSize     = v.size || v.variant_size || '-';
                        let vColorVal = v.color || v.variant_color || '-';
                        let vStock    = (v.stock !== undefined && v.stock !== null && v.stock !== '') ? v.stock : (v.variant_stock ?? 0);
                        let vSale     = (v.sale_price !== undefined && v.sale_price !== null && v.sale_price !== '') ? v.sale_price : (v.variant_sale_price ?? 0);
                        let vPurch    = (v.purch_price !== undefined && v.purch_price !== null && v.purch_price !== '') ? v.purch_price : (v.purchase_price ?? v.variant_purchase_price ?? 0);
                        let vAlert    = (v.alert !== undefined && v.alert !== null && v.alert !== '') ? v.alert : (v.variant_alert_qty ?? 0);
                        let vBarcode  = v.barcode || v.variant_barcode || (product.barcode_path ?? product.item_code);
                        let vUnit     = v.unit || v.variant_unit || (product.unit ? product.unit.name : 'Pcs');

                        let colorBadge = (vColorVal && vColorVal !== '-') ? `<span style="background:#e2e8f0;border-radius:4px;padding:2px 6px;font-size:.72rem;">${vColorVal}</span>` : '<span style="color:#94a3b8;">—</span>';
                        let alertQty  = (vAlert != null && vAlert != 0) ? vAlert : '-';
                        
                        if (product.size_mode === 'by_kg' && v.conv_factor != 1 && !v.unit) vUnit = 'Pcs';
                        let vPriceLabel = product.size_mode === 'by_size' ? '/m²' : '/' + vUnit;

                        tbody.append(`<tr>
                            <td class="text-start ps-4 fw-semibold">${vName}</td>
                            <td>${vSize}</td>
                            <td>${colorBadge}</td>
                            <td>${stockBadgeHtml(vStock, vAlert)}</td>
                            <td class="fw-bold" style="color:#059669;">Rs. ${parseFloat(vSale||0).toFixed(2)} <small class="fw-normal text-muted">${vPriceLabel}</small></td>
                            <td class="text-muted">Rs. ${parseFloat(vPurch||0).toFixed(2)} <small>${vPriceLabel}</small></td>
                            <td><span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:4px;padding:2px 6px;font-size:.72rem;">${alertQty}</span></td>
                            <td class="text-end pe-4"><code style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;padding:2px 6px;font-size:.75rem;">${vBarcode}</code></td>
                        </tr>`);
                    });
                } else {
                    colorList.forEach((color, index) => {
                        let barcode   = (product.barcode_path ?? product.item_code ?? '') + (index > 0 ? '-' + String(index+1).padStart(2,'0') : '');
                        let colorBadge = (color && color !== '-') ? `<span style="background:#e2e8f0;border-radius:4px;padding:2px 6px;font-size:.72rem;">${color}</span>` : '<span style="color:#94a3b8;">—</span>';
                        tbody.append(`<tr>
                            <td class="text-start ps-4 fw-semibold">${product.item_name}</td>
                            <td>${sizeStr}</td>
                            <td>${colorBadge}</td>
                            <td>${stockBadgeHtml(stock, product.alert_carton_quantity)}</td>
                            <td class="fw-bold" style="color:#059669;">Rs. ${parseFloat(salePrice||0).toFixed(2)} <small class="fw-normal text-muted">${priceLabel}</small></td>
                            <td class="text-muted">Rs. ${parseFloat(purchPrice||0).toFixed(2)} <small>${priceLabel}</small></td>
                            <td><span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:4px;padding:2px 6px;font-size:.72rem;">${alertDef}</span></td>
                            <td class="text-end pe-4"><code style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;padding:2px 6px;font-size:.75rem;">${barcode}</code></td>
                        </tr>`);
                    });
                }
            },
            error: function() {
                $('#modalLoadingSpinner').addClass('d-none');
                Swal.fire('Error', 'Could not fetch product details.', 'error');
            }
        });
    });

    // ── Toggle Active ──
    $(document).on('click', '.toggle-active-btn', function () {
        const btn        = $(this);
        const productId  = btn.data('id');
        const isActive   = btn.data('active') == '1';
        const name       = btn.data('name');
        const actionText = isActive ? 'Deactivate' : 'Activate';

        Swal.fire({
            title: actionText + ' Product?',
            html:  `<b>${name}</b><br><small class="text-muted">${isActive ? 'Product will be hidden from Sale/Purchase forms.' : 'Product will be visible in Sale/Purchase forms.'}</small>`,
            icon:  isActive ? 'warning' : 'success',
            showCancelButton:    true,
            confirmButtonText:   'Yes, ' + actionText,
            confirmButtonColor:  isActive ? '#dc2626' : '#059669',
            cancelButtonText:    'Cancel',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url:  `/product/${productId}/toggle-active`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (!res.success) return;
                    const row    = $(`#product-row-${productId}`);
                    const card   = $(`#pmcard-${productId}`);
                    const badge  = $(`#status-badge-${productId}`);
                    const mbadge = $(`#mstatus-badge-${productId}`);
                    if (res.is_active) {
                        row.removeClass('row-inactive');
                        card.removeClass('row-inactive');
                        badge.attr('class', 'status-active').text('Active');
                        mbadge.attr('class', 'status-active').text('Active');
                        btn.removeClass('btn-act-act').addClass('btn-act-deact')
                           .attr('title','Deactivate').html('<i class="fas fa-ban"></i>')
                           .data('active','1');
                    } else {
                        row.addClass('row-inactive');
                        card.addClass('row-inactive');
                        badge.attr('class', 'status-inactive').text('Inactive');
                        mbadge.attr('class', 'status-inactive').text('Inactive');
                        btn.removeClass('btn-act-deact').addClass('btn-act-act')
                           .attr('title','Activate').html('<i class="fas fa-check"></i>')
                           .data('active','0');
                    }
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title:res.message, showConfirmButton:false, timer:2500, timerProgressBar:true });
                },
                error: () => Swal.fire('Error', 'Could not update product status.', 'error')
            });
        });
    });

    // ── Subcategory fetch helpers ──
    $('#categorySelect').change(function() {
        var id = $(this).val();
        $('#subCategorySelect').html('<option value="">Loading...</option>');
        if (id) {
            $.get("/get-subcategories/" + id, { category_id: id }, function(data) {
                $('#subCategorySelect').html('<option value="">Select Sub-Category</option>');
                $.each(data, function(k, sub) {
                    $('#subCategorySelect').append('<option value="' + sub.id + '">' + sub.name + '</option>');
                });
            }).fail(() => alert('Error fetching subcategories.'));
        } else {
            $('#subCategorySelect').html('<option value="">Select Sub-Category</option>');
        }
    });

    // ══════════════════════════════════════════════════════════════
    //  QUICK EDIT & STOCK ADJUST MODAL JAVASCRIPT
    // ══════════════════════════════════════════════════════════════
    let qeWarehouseStocksMap = {};
    let qeCurrentVariants = [];
    let qeProductUnit = 'Pcs';

    function updateStockDiffBadge() {
        let curr = parseFloat($('#qe_current_stock_val').val()) || 0;
        let newQtyStr = $('#qe_new_stock_qty').val();
        let badge = $('#qe_diff_badge');

        if (newQtyStr === '' || isNaN(newQtyStr)) {
            badge.css({ background: '#f1f5f9', color: '#64748b', border: '1px solid #cbd5e1' })
                 .html('0 (No change)');
            return;
        }

        let newQty = parseFloat(newQtyStr);
        let diff = Math.round((newQty - curr) * 100) / 100;

        if (diff > 0) {
            badge.css({ background: '#ecfdf5', color: '#047857', border: '1px solid #a7f3d0' })
                 .html('<i class="fas fa-arrow-up me-1"></i> +' + diff + ' ' + qeProductUnit + ' (Stock will be ADDED)');
        } else if (diff < 0) {
            badge.css({ background: '#fef2f2', color: '#b91c1c', border: '1px solid #fecaca' })
                 .html('<i class="fas fa-arrow-down me-1"></i> ' + diff + ' ' + qeProductUnit + ' (Stock will be DEDUCTED)');
        } else {
            badge.css({ background: '#f1f5f9', color: '#64748b', border: '1px solid #cbd5e1' })
                 .html('0 (No change)');
        }
    }

    function recalculateVariantStocks(sourceInput) {
        let sizeMode = $('#qe_size_mode').val();
        let totalCurrent = 0;
        let totalNew = 0;

        if (sizeMode === 'by_kg') {
            if (sourceInput) {
                let changedRow = $(sourceInput).closest('.qe-var-row');
                let isBase = changedRow.data('isbase') == 1;
                let conv = parseFloat(changedRow.data('conv')) || 1;
                let enteredVal = parseFloat($(sourceInput).val()) || 0;
                let baseKg = isBase ? enteredVal : (conv > 0 ? enteredVal * conv : 0);

                $('.qe-var-row').each(function () {
                    let row = $(this);
                    let rowIsBase = row.data('isbase') == 1;
                    let rowConv = parseFloat(row.data('conv')) || 1;
                    let input = row.find('.qe-var-stock-input');

                    if (row[0] !== changedRow[0]) {
                        if (rowIsBase) {
                            input.val(baseKg);
                        } else {
                            input.val(rowConv > 0 ? Math.floor(baseKg / rowConv) : 0);
                        }
                    }
                });
            }

            $('.qe-var-row').each(function () {
                let row = $(this);
                let currStock = parseFloat(row.data('curr')) || 0;
                let isBase = row.data('isbase') == 1;
                let vUnit = row.data('unit') || (isBase ? 'Kg' : 'Pcs');
                let newStockVal = row.find('.qe-var-stock-input').val();
                let newStock = newStockVal === '' || isNaN(newStockVal) ? currStock : parseFloat(newStockVal);
                let diffBadge = row.find('.qe-var-diff-badge');

                if (isBase) {
                    totalCurrent = currStock;
                    totalNew = newStock;
                }

                let diff = Math.round((newStock - currStock) * 100) / 100;
                if (diff > 0) {
                    diffBadge.css({ background: '#ecfdf5', color: '#047857', border: '1px solid #a7f3d0' })
                              .text('+' + diff + ' ' + vUnit);
                } else if (diff < 0) {
                    diffBadge.css({ background: '#fef2f2', color: '#b91c1c', border: '1px solid #fecaca' })
                              .text(diff + ' ' + vUnit);
                } else {
                    diffBadge.css({ background: '#f1f5f9', color: '#64748b', border: '1px solid #cbd5e1' })
                              .text('0');
                }
            });

            let totalDiff = Math.round((totalNew - totalCurrent) * 100) / 100;
            let diffText = totalDiff > 0 ? `+${totalDiff} Kg (Added)` : (totalDiff < 0 ? `${totalDiff} Kg (Deducted)` : '0 (No change)');
            let badgeColor = totalDiff > 0 ? '#ecfdf5' : (totalDiff < 0 ? '#fef2f2' : '#f1f5f9');
            let textColor = totalDiff > 0 ? '#047857' : (totalDiff < 0 ? '#b91c1c' : '#64748b');
            let borderColor = totalDiff > 0 ? '#a7f3d0' : (totalDiff < 0 ? '#fecaca' : '#cbd5e1');

            $('#qe_var_total_diff_badge').css({ background: badgeColor, color: textColor, border: '1px solid ' + borderColor })
                                         .html(`Total Weight: <strong>${totalNew}</strong> Kg | Net Diff: <strong>${diffText}</strong>`);

            $('#qe_wh_total_badge').text(`${totalNew} Kg`);

        } else {
            $('.qe-var-row').each(function () {
                let row = $(this);
                let currStock = parseFloat(row.data('curr')) || 0;
                let newStockInput = row.find('.qe-var-stock-input');
                let diffBadge = row.find('.qe-var-diff-badge');

                let newStockVal = newStockInput.val();
                let newStock = newStockVal === '' || isNaN(newStockVal) ? currStock : parseFloat(newStockVal);

                totalCurrent += currStock;
                totalNew += newStock;

                let diff = Math.round((newStock - currStock) * 100) / 100;
                if (diff > 0) {
                    diffBadge.css({ background: '#ecfdf5', color: '#047857', border: '1px solid #a7f3d0' })
                              .text('+' + diff);
                } else if (diff < 0) {
                    diffBadge.css({ background: '#fef2f2', color: '#b91c1c', border: '1px solid #fecaca' })
                              .text(diff);
                } else {
                    diffBadge.css({ background: '#f1f5f9', color: '#64748b', border: '1px solid #cbd5e1' })
                              .text('0');
                }
            });

            let totalDiff = Math.round((totalNew - totalCurrent) * 100) / 100;
            let diffText = totalDiff > 0 ? `+${totalDiff} ${qeProductUnit} (Added)` : (totalDiff < 0 ? `${totalDiff} ${qeProductUnit} (Deducted)` : '0 (No change)');
            let badgeColor = totalDiff > 0 ? '#ecfdf5' : (totalDiff < 0 ? '#fef2f2' : '#f1f5f9');
            let textColor = totalDiff > 0 ? '#047857' : (totalDiff < 0 ? '#b91c1c' : '#64748b');
            let borderColor = totalDiff > 0 ? '#a7f3d0' : (totalDiff < 0 ? '#fecaca' : '#cbd5e1');

            $('#qe_var_total_diff_badge').css({ background: badgeColor, color: textColor, border: '1px solid ' + borderColor })
                                         .html(`Total Stock: <strong>${totalNew}</strong> ${qeProductUnit} | Net Diff: <strong>${diffText}</strong>`);

            $('#qe_wh_total_badge').text(`${totalNew} ${qeProductUnit}`);
        }
    }

    function updateMarginBadge() {
        let sizeMode = $('#qe_size_mode').val();
        let purch = 0, sale = 0;
        if (sizeMode === 'by_size') {
            purch = parseFloat($('#qe_purchase_price_per_m2').val()) || 0;
            sale  = parseFloat($('#qe_price_per_m2').val()) || 0;
        } else {
            purch = parseFloat($('#qe_purchase_price_per_piece').val()) || 0;
            sale  = parseFloat($('#qe_sale_price_per_piece').val()) || 0;
        }

        let badge = $('#qe_margin_badge');
        if (sale > 0 && purch >= 0) {
            let margin = ((sale - purch) / sale) * 100;
            let marginFormatted = margin.toFixed(1) + '%';
            if (margin >= 0) {
                badge.css({ background: '#ecfdf5', color: '#059669', border: '1px solid #a7f3d0' })
                     .text('Margin: ' + marginFormatted);
            } else {
                badge.css({ background: '#fef2f2', color: '#dc2626', border: '1px solid #fecaca' })
                     .text('Loss: ' + marginFormatted);
            }
        } else {
            badge.css({ background: '#f1f5f9', color: '#64748b', border: '1px solid #cbd5e1' })
                 .text('Margin: 0%');
        }
    }

    // ── Open Quick Edit Modal ──
    $(document).on('click', '.quickEditProductBtn', function () {
        let productId = $(this).data('id');
        $('#quickEditForm').addClass('d-none');
        $('#qeLoadingSpinner').removeClass('d-none');
        $('#quickEditModal').modal('show');

        $.ajax({
            url: '/product/quick-edit-data/' + productId,
            type: 'GET',
            success: function (res) {
                if (!res.success) {
                    Swal.fire('Error', res.error || 'Failed to load product data', 'error');
                    $('#quickEditModal').modal('hide');
                    return;
                }

                let p = res.product;
                qeWarehouseStocksMap = res.warehouse_stocks || {};
                qeCurrentVariants = p.variants || [];
                qeProductUnit = p.unit_name || 'Pcs';

                $('#qe_product_id').val(p.id);
                $('#qe_size_mode').val(p.size_mode || 'std');
                $('#qe_item_title').text(p.item_name || 'Product');
                $('#qe_item_code').text(p.item_code || '-');
                $('#qe_item_name').val(p.item_name || '');
                $('#qe_alert_quantity').val(p.alert_carton_quantity != null ? p.alert_carton_quantity : '');

                // Populate Categories
                let catSelect = $('#qe_category_id').empty().append('<option value="">Select Category</option>');
                (res.categories || []).forEach(c => {
                    catSelect.append(`<option value="${c.id}" ${c.id == p.category_id ? 'selected' : ''}>${c.name}</option>`);
                });

                // Populate Subcategories
                let subSelect = $('#qe_sub_category_id').empty().append('<option value="">Select Sub-Category</option>');
                (res.subcategories || []).forEach(s => {
                    subSelect.append(`<option value="${s.id}" ${s.id == p.sub_category_id ? 'selected' : ''}>${s.name}</option>`);
                });

                // Populate Brands
                let brandSelect = $('#qe_brand_id').empty().append('<option value="">Select Brand</option>');
                (res.brands || []).forEach(b => {
                    brandSelect.append(`<option value="${b.id}" ${b.id == p.brand_id ? 'selected' : ''}>${b.name}</option>`);
                });

                // Set Locked Unit Display & Hidden Field
                $('#qe_unit_id').val(p.unit_id || '');
                $('#qe_unit_display_text span').text(qeProductUnit);

                // Update Price Badges (/ Kg or / Pcs or / m²)
                $('.qe-price-unit-badge').text('/ ' + (p.size_mode === 'by_size' ? 'm²' : qeProductUnit));

                // Set Default Warehouse ID (Main Warehouse)
                let selectedWhId = (res.warehouses && res.warehouses.length > 0) ? res.warehouses[0].id : 1;
                $('#qe_adjust_warehouse_id').val(selectedWhId);

                // Price Fields
                if (p.size_mode === 'by_size') {
                    $('#qe_size_prices').removeClass('d-none');
                    $('#qe_standard_prices').addClass('d-none');
                    $('#qe_purchase_price_per_m2').val(p.purchase_price_per_m2 || 0);
                    $('#qe_price_per_m2').val(p.price_per_m2 || 0);
                } else {
                    $('#qe_standard_prices').removeClass('d-none');
                    $('#qe_size_prices').addClass('d-none');
                    $('#qe_purchase_price_per_piece').val(p.purchase_price_per_piece || 0);
                    $('#qe_sale_price_per_piece').val(p.sale_price_per_piece || p.sale_price_per_box || 0);
                    $('#qe_wholesale_price').val(p.wholesale_price || 0);
                }

                // Setup Initial Warehouse Stock Display
                let initialWhStock = qeWarehouseStocksMap[selectedWhId] ? qeWarehouseStocksMap[selectedWhId].total_pieces : (p.total_pieces || 0);
                $('#qe_current_stock_display').html(`<span>${initialWhStock}</span> <small class="text-muted">${qeProductUnit}</small>`);
                $('#qe_current_stock_val').val(initialWhStock);
                $('#qe_new_stock_qty').val(initialWhStock);

                // Check Single vs Variant Mode
                if (qeCurrentVariants && qeCurrentVariants.length > 0) {
                    $('#qe_single_stock_wrap').addClass('d-none');
                    $('#qe_variant_stock_wrap').removeClass('d-none');

                    let varTbody = $('#qe_variant_stock_tbody').empty();
                    qeCurrentVariants.forEach((v, idx) => {
                        let vName = v.name || p.item_name;
                        let vLabel = v.label || (v.color && v.color !== '-' ? v.color : (v.unit || 'Variant ' + (idx + 1)));
                        let vColor = v.color || '-';
                        let vSize  = v.size || '-';
                        let vStock = parseFloat(v.current_stock || v.initial_stock || 0);
                        let vSale  = parseFloat(v.sale_price || 0);
                        let vPurch = parseFloat(v.purch_price || 0);
                        let vAlert = v.alert !== undefined ? v.alert : '';
                        let conv   = parseFloat(v.conv_factor || 1);
                        let isBase = v.is_base_variant == 1 || (p.size_mode === 'by_kg' && conv == 1);
                        let vUnit  = v.unit || (p.size_mode === 'by_kg' ? (isBase ? 'Kg' : 'Pcs') : qeProductUnit);

                        let metaExtra = '';
                        if (p.size_mode === 'by_kg') {
                            if (isBase) {
                                metaExtra = `<span class="badge bg-primary-subtle text-primary border" style="font-size:.65rem;">Base (1 Kg)</span>`;
                            } else if (v.weight_per_piece) {
                                metaExtra = `<span class="badge bg-light text-muted border" style="font-size:.65rem;">${v.weight_per_piece}g / piece</span>`;
                            }
                        } else if (vColor !== '-' || vSize !== '-') {
                            metaExtra = `<span class="badge bg-light text-muted border" style="font-size:.65rem;">${vColor} / ${vSize}</span>`;
                        }

                        varTbody.append(`
                            <tr class="qe-var-row" data-index="${idx}" data-curr="${vStock}" data-conv="${conv}" data-isbase="${isBase ? 1 : 0}" data-unit="${vUnit}">
                                <td class="text-start ps-3">
                                    <div class="fw-bold text-dark">${vLabel}</div>
                                    <div class="item-meta mt-1">${metaExtra}</div>
                                    <input type="hidden" name="variants[${idx}][name]" value="${vName}">
                                    <input type="hidden" name="variants[${idx}][color]" value="${vColor}">
                                    <input type="hidden" name="variants[${idx}][size]" value="${vSize}">
                                    <input type="hidden" name="variants[${idx}][conv_factor]" value="${conv}">
                                    <input type="hidden" name="variants[${idx}][unit]" value="${vUnit}">
                                    <input type="hidden" name="variants[${idx}][is_base_variant]" value="${isBase ? 1 : 0}">
                                    <input type="hidden" name="variants[${idx}][weight_per_piece]" value="${v.weight_per_piece || ''}">
                                    <input type="hidden" name="variants[${idx}][current_stock]" value="${vStock}">
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark border px-2 py-1">${vStock} ${vUnit}</span>
                                </td>
                                <td class="bg-warning-subtle" style="min-width:115px;">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="any" min="0" class="form-control form-control-sm text-center fw-bold border-warning qe-var-stock-input" name="variants[${idx}][new_stock]" value="${vStock}">
                                        <span class="input-group-text bg-light text-muted px-1" style="font-size:.7rem;">${vUnit}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge qe-var-diff-badge" style="font-size:.72rem; padding:3px 6px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;">0</span>
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variants[${idx}][sale_price]" value="${vSale}">
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variants[${idx}][purch_price]" value="${vPurch}">
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variants[${idx}][alert]" value="${vAlert}">
                                </td>
                            </tr>
                        `);
                    });

                    recalculateVariantStocks();
                } else {
                    $('#qe_variant_stock_wrap').addClass('d-none');
                    $('#qe_single_stock_wrap').removeClass('d-none');
                    updateStockDiffBadge();
                }

                updateMarginBadge();
                $('#qeLoadingSpinner').addClass('d-none');
                $('#quickEditForm').removeClass('d-none');
            },
            error: function () {
                $('#qeLoadingSpinner').addClass('d-none');
                Swal.fire('Error', 'Could not load product details.', 'error');
                $('#quickEditModal').modal('hide');
            }
        });
    });


    // ── Single Stock input keyup/change ──
    $(document).on('input change', '#qe_new_stock_qty', function () {
        updateStockDiffBadge();
    });

    // ── Variant Stock input keyup/change (Sums all variant quantities) ──
    $(document).on('input change', '.qe-var-stock-input', function () {
        recalculateVariantStocks(this);
    });

    // ── Modal Category change -> Load Subcategories ──
    $('#qe_category_id').on('change', function () {
        let catId = $(this).val();
        let subSelect = $('#qe_sub_category_id').empty().append('<option value="">Select Sub-Category</option>');
        if (catId) {
            $.get('/get-subcategories/' + catId, { category_id: catId }, function (data) {
                $.each(data, function (k, sub) {
                    subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                });
            });
        }
    });

    // ── Live Margin update on price inputs ──
    $(document).on('input change', '.qe-price-input', function () {
        updateMarginBadge();
    });

    // ── Save Quick Edit Form ──
    $('#saveQuickEditBtn').on('click', function () {
        let productId = $('#qe_product_id').val();
        if (!productId) return;

        let btn = $(this);
        let origBtnHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        let formData = $('#quickEditForm').serializeArray();
        formData.push({ name: '_token', value: '{{ csrf_token() }}' });

        $.ajax({
            url: '/product/quick-update/' + productId,
            type: 'POST',
            data: $.param(formData),
            success: function (res) {
                btn.prop('disabled', false).html(origBtnHtml);
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Failed to update product', 'error');
                    return;
                }

                $('#quickEditModal').modal('hide');

                // Update Table Row Dynamically
                let d = res.data;
                if (d) {
                    let nameEl = $(`#p-name-${productId}`);
                    let metaEl = $(`#p-meta-${productId}`);
                    let stockBadgeEl = $(`#p-stock-badge-${productId}`);
                    let varPreviewEl = $(`#p-var-preview-${productId}`);
                    let tradePriceEl = $(`#p-trade-price-${productId}`);
                    let retailPriceEl = $(`#p-retail-price-${productId}`);

                    if (nameEl.length) nameEl.text(d.item_name);
                    if (metaEl.length) {
                        let metaHtml = `<span class="item-code" id="p-code-${productId}">${d.item_code}</span>`;
                        if (d.category_name) {
                            metaHtml += `<span class="meta-chip" id="p-cat-${productId}"><i class="fas fa-folder" style="font-size:.6rem;"></i> ${d.category_name}${d.sub_category_name ? ' &rsaquo; ' + d.sub_category_name : ''}</span>`;
                        }
                        if (d.brand_name) {
                            metaHtml += `<span class="meta-chip" id="p-brand-${productId}"><i class="fas fa-trademark" style="font-size:.6rem;"></i> ${d.brand_name}</span>`;
                        }
                        metaEl.html(metaHtml);
                    }
                    if (stockBadgeEl.length) {
                        stockBadgeEl.attr('class', 'stock-badge ' + (d.stock_class || ''));
                        stockBadgeEl.html(`<i class="fas fa-cubes" style="font-size:.65rem;"></i> <span class="stock-num">${d.stock_display}</span> <span class="stock-unit">${d.stock_unit}</span>`);
                    }
                    if (varPreviewEl.length && d.variants_preview_html) {
                        varPreviewEl.html(d.variants_preview_html);
                    }
                    if (tradePriceEl.length) tradePriceEl.text(d.trade_price);
                    if (retailPriceEl.length) retailPriceEl.text(d.retail_price);
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            },
            error: function (xhr) {
                btn.prop('disabled', false).html(origBtnHtml);
                let msg = 'Failed to update product.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

});  // ── end $(document).ready ──
</script>
@endsection
