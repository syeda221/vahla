@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ── Clean Enterprise SaaS Design System ── */
    :root {
        --saas-bg:          #F3F4F7;
        --saas-card:        #FFFFFF;
        --saas-border:      #E3E5EA;
        --saas-border-dark: #D1D5DB;
        --saas-navy:        #0B1020;
        --saas-navy-lt:     #172036;
        --saas-accent:      #3452D9;
        --saas-accent-lt:   #ECEFFB;
        --saas-text-main:   #111827;
        --saas-text-sec:    #374151;
        --saas-text-muted:  #6B7280;
        
        /* Semantic Status Colors (Used strictly for meaning) */
        --saas-success:     #16A34A;
        --saas-success-lt:  #F0FDF4;
        --saas-danger:      #DC2626;
        --saas-danger-lt:   #FEF2F2;
        --saas-amber:       #D97706;
        --saas-amber-lt:    #FFFBEB;
    }

    body {
        background-color: var(--saas-bg) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        color: var(--saas-text-main);
    }

    .enterprise-wrapper {
        background: var(--saas-bg);
        min-height: calc(100vh - 65px);
        padding: 0 0 24px 0;
    }

    /* ── Deep Navy Section Top Bar ── */
    .saas-top-header {
        background: var(--saas-navy);
        color: #FFFFFF;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        border-bottom: 1px solid #1E293B;
    }
    .saas-top-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #FFFFFF;
        letter-spacing: -0.2px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .saas-top-tag {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(52, 82, 217, 0.25);
        color: #93C5FD;
        border: 1px solid rgba(147, 197, 253, 0.2);
        padding: 2px 8px;
        border-radius: 4px;
    }
    .saas-top-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-saas-nav {
        background: transparent;
        color: #E2E8F0;
        border: 1px solid #334155;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .btn-saas-nav:hover {
        background: #1E293B;
        border-color: #64748B;
        color: #FFFFFF;
    }

    /* ── Compact Stat Cards with Left Accent Border ── */
    .saas-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        padding: 16px 24px 0 24px;
    }
    .saas-kpi-card {
        background: var(--saas-card);
        border: 1px solid var(--saas-border);
        border-left: 3px solid var(--saas-accent);
        border-radius: 6px;
        padding: 12px 16px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .saas-kpi-card.kpi-danger { border-left-color: var(--saas-danger); }
    .saas-kpi-card.kpi-neutral { border-left-color: #6B7280; }
    
    .saas-kpi-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--saas-text-muted);
        letter-spacing: 0.1px;
    }
    .saas-kpi-val {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--saas-text-main);
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
        margin-top: 2px;
        line-height: 1.2;
    }
    .saas-kpi-sub {
        font-size: 0.68rem;
        color: var(--saas-text-muted);
        margin-top: 2px;
    }

    /* ── Main Content Container ── */
    .saas-main-container {
        padding: 14px 24px;
    }
    .saas-panel {
        background: var(--saas-card);
        border: 1px solid var(--saas-border);
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    /* ── Filter & Search Toolbar ── */
    .saas-toolbar {
        background: #FFFFFF;
        border-bottom: 1px solid var(--saas-border);
        padding: 10px 14px;
    }
    .saas-control {
        height: 32px;
        border: 1px solid var(--saas-border-dark);
        border-radius: 4px;
        padding: 0 8px;
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--saas-text-main);
        background: #FFFFFF;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .saas-control:focus {
        border-color: var(--saas-accent);
        box-shadow: 0 0 0 2px rgba(52, 82, 217, 0.15);
    }
    .btn-saas-primary {
        height: 32px;
        background: var(--saas-accent);
        color: #FFFFFF;
        border: 1px solid var(--saas-accent);
        border-radius: 4px;
        padding: 0 12px;
        font-size: 0.76rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .btn-saas-primary:hover {
        background: #2842BC;
        color: #FFFFFF;
    }
    .btn-saas-reset {
        height: 32px;
        background: #FFFFFF;
        color: var(--saas-text-muted);
        border: 1px solid var(--saas-border-dark);
        border-radius: 4px;
        padding: 0 10px;
        font-size: 0.76rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-saas-reset:hover {
        background: #F9FAFB;
        color: var(--saas-text-main);
        border-color: #9CA3AF;
    }

    /* ── Clean Tabular Data Table with Crisp Grid Borders ── */
    .saas-table-container {
        width: 100%;
        overflow-x: hidden;
    }
    .saas-table {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        border: 1px solid #CBD5E1 !important;
        font-size: 0.77rem;
        margin: 0;
    }
    .saas-table thead th {
        background: #F1F5F9;
        color: #0F172A;
        font-weight: 700;
        font-size: 0.73rem;
        letter-spacing: 0.1px;
        padding: 8px 8px;
        border: 1px solid #CBD5E1 !important;
        text-align: left;
        vertical-align: middle;
        position: sticky;
        top: 0;
        z-index: 5;
    }
    .saas-table tbody td {
        padding: 6px 8px;
        border: 1px solid #CBD5E1 !important;
        vertical-align: middle;
        color: #111827;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background: #FFFFFF;
    }
    .saas-table tbody tr:hover td {
        background: #F8FAFC;
    }

    /* Tabular numbers for financial & inventory values - Solid dark & bold */
    .num-col {
        font-family: 'JetBrains Mono', 'Roboto Mono', 'SF Mono', Consolas, monospace;
        font-size: 0.76rem;
        font-weight: 700;
        color: #111827 !important;
        font-variant-numeric: tabular-nums;
        text-align: right;
    }
    .num-col.text-accent { 
        color: #111827 !important; 
        font-weight: 700 !important; 
    }
    .num-col.text-stock-val { 
        color: #111827 !important; 
        font-weight: 700 !important; 
    }

    .item-title-bold {
        color: #0F172A !important;
        font-weight: 700 !important;
        font-size: 0.77rem;
    }

    /* ── Inline Double-Click Editable Cells (Preserving solid grid borders) ── */
    .editable-cell {
        cursor: pointer;
        position: relative;
        background: #FFFFFF;
        transition: background 0.12s ease;
    }
    .editable-cell:hover {
        background: #EFF6FF !important;
    }
    .editable-cell::after {
        content: "\f304";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 0.58rem;
        color: #94A3B8;
        margin-left: 4px;
        opacity: 0;
        transition: opacity 0.1s ease;
    }
    .editable-cell:hover::after {
        opacity: 1;
        color: var(--saas-accent);
    }

    /* Active Editing Mode */
    .editable-cell.is-editing {
        padding: 0 !important;
        background: #FFFFFF !important;
    }
    .cell-input-box {
        width: 100%;
        height: 28px;
        border: 2px solid var(--saas-accent) !important;
        border-radius: 0;
        padding: 0 4px;
        font-family: 'JetBrains Mono', 'Roboto Mono', monospace;
        font-size: 0.78rem;
        font-weight: 700;
        background: #FFFFFF;
        color: #111827;
        text-align: right;
        outline: none;
        box-shadow: 0 0 0 2px rgba(52, 82, 217, 0.2);
    }

    /* Flash highlight on save */
    .save-flash {
        animation: saasSaveFlash 0.8s ease-out;
    }
    @keyframes saasSaveFlash {
        0% { background-color: #DCFCE7 !important; }
        100% { background-color: transparent; }
    }

    /* ── Semantic Status Badges ── */
    .status-tag {
        display: inline-block;
        font-size: 0.69rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 3px;
        line-height: 1.3;
    }
    .status-tag.tag-instock {
        background: var(--saas-success-lt);
        color: var(--saas-success);
        border: 1px solid #BBF7D0;
    }
    .status-tag.tag-lowstock {
        background: var(--saas-danger-lt);
        color: var(--saas-danger);
        border: 1px solid #FECACA;
    }

    .tag-neutral {
        font-size: 0.65rem;
        font-weight: 600;
        background: #F1F5F9;
        color: #334155;
        border: 1px solid #CBD5E1;
        padding: 1px 4px;
        border-radius: 3px;
    }

    /* ── Collapsible Variants Inset Row ── */
    .variant-drawer td {
        background: #F8FAFC !important;
        padding: 6px 12px !important;
        border: 1px solid #CBD5E1 !important;
    }
    .variant-inset-table {
        width: 100%;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-collapse: collapse;
        border-radius: 4px;
        font-size: 0.72rem;
    }
    .variant-inset-table th {
        background: #F1F5F9 !important;
        padding: 5px 8px;
        font-size: 0.68rem;
        font-weight: 700;
        color: #0F172A !important;
        border: 1px solid #CBD5E1;
    }
    .variant-inset-table td {
        padding: 5px 8px !important;
        border: 1px solid #CBD5E1 !important;
        color: #111827 !important;
        font-weight: 600;
    }

    /* ── Pagination Footer ── */
    .saas-footer {
        padding: 8px 14px;
        background: #FFFFFF;
        border-top: 1px solid var(--saas-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.74rem;
        color: var(--saas-text-muted);
    }

    @media (max-width: 992px) {
        .saas-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .saas-kpi-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="enterprise-wrapper">

    {{-- ── Dark Top Navigation Bar ── --}}
    <div class="saas-top-header">
        <div class="d-flex align-items-center gap-3">
            <h1 class="saas-top-title">
                <i class="fas fa-cubes text-muted" style="font-size: 0.95rem;"></i>
                <span>Product Pricing &amp; Stock Valuation</span>
            </h1>
            <span class="saas-top-tag">Inventory Master</span>
        </div>

        <div class="saas-top-actions">
            <a href="{{ route('product') }}" class="btn-saas-nav">
                <i class="fas fa-list-ul"></i>
                <span>Products List</span>
            </a>
            <a href="{{ route('report.item_stock') }}" class="btn-saas-nav">
                <i class="fas fa-chart-bar"></i>
                <span>Stock Report</span>
            </a>
        </div>
    </div>

    {{-- ── Compact Stat Cards (Left Accent Border, No Saturated Badges) ── --}}
    <div class="saas-kpi-grid">
        <div class="saas-kpi-card">
            <div class="saas-kpi-label">Total stock valuation (Avg cost)</div>
            <div class="saas-kpi-val" id="kpi-total-stock-val">Rs. {{ number_format($totalStockValue, 2) }}</div>
            <div class="saas-kpi-sub">Weighted cost basis</div>
        </div>

        <div class="saas-kpi-card">
            <div class="saas-kpi-label">Potential retail valuation</div>
            <div class="saas-kpi-val" id="kpi-total-sale-val">Rs. {{ number_format($totalSaleValue, 2) }}</div>
            <div class="saas-kpi-sub">At current retail prices</div>
        </div>

        <div class="saas-kpi-card kpi-neutral">
            <div class="saas-kpi-label">Total inventory units</div>
            <div class="saas-kpi-val" id="kpi-total-pieces">{{ number_format($totalStockPieces) }} <span style="font-size:0.75rem; font-weight:500; color:var(--saas-text-muted);">Units</span></div>
            <div class="saas-kpi-sub">Aggregated all warehouses</div>
        </div>

        <div class="saas-kpi-card kpi-danger">
            <div class="saas-kpi-label">Low stock alert items</div>
            <div class="saas-kpi-val text-danger" id="kpi-low-stock-count">{{ $lowStockCount }} <span style="font-size:0.75rem; font-weight:500; color:var(--saas-text-muted);">SKUs</span></div>
            <div class="saas-kpi-sub">Below minimum threshold</div>
        </div>
    </div>

    {{-- ── Main Table Panel ── --}}
    <div class="saas-main-container">
        <div class="saas-panel">
            
            {{-- Toolbar: Filters & Search --}}
            <div class="saas-toolbar">
                <form method="GET" action="{{ route('products.pricing_valuation') }}" id="filterForm" class="d-flex flex-wrap align-items-center gap-2">
                    
                    {{-- Search Input --}}
                    <div style="min-width: 180px; flex: 1;">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="saas-control w-100" placeholder="Search item name, code, barcode…">
                    </div>

                    {{-- Category --}}
                    <select name="category_id" class="saas-control" style="min-width: 140px;">
                        <option value="all">All categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Brand --}}
                    <select name="brand_id" class="saas-control" style="min-width: 120px;">
                        <option value="all">All brands</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Product Mode --}}
                    <select name="size_mode" class="saas-control" style="min-width: 140px;">
                        <option value="all">All unit types</option>
                        <option value="by_kg" {{ request('size_mode') === 'by_kg' ? 'selected' : '' }}>Weight based (Kg/Gm)</option>
                        <option value="by_cartons" {{ request('size_mode') === 'by_cartons' ? 'selected' : '' }}>Cartons / Boxes</option>
                        <option value="by_size" {{ request('size_mode') === 'by_size' ? 'selected' : '' }}>Area (M²)</option>
                        <option value="std" {{ request('size_mode') === 'std' ? 'selected' : '' }}>Standard (Pcs)</option>
                    </select>

                    {{-- Stock Status --}}
                    <select name="stock_status" class="saas-control" style="min-width: 120px;">
                        <option value="all">All stock</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In stock</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low stock</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
                    </select>

                    {{-- Rows per page --}}
                    <select name="per_page" class="saas-control" style="min-width: 90px;" onchange="$('#filterForm').submit();">
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                        <option value="50" {{ request('per_page') == 50 || !request('per_page') ? 'selected' : '' }}>50 rows</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
                        <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 rows</option>
                    </select>

                    <button type="submit" class="btn-saas-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    @if(request()->hasAny(['search', 'category_id', 'brand_id', 'size_mode', 'stock_status']))
                        <a href="{{ route('products.pricing_valuation') }}" class="btn-saas-reset" title="Reset filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Grid (100% Fit, Crisp Borders, Tabular Bold Numbers) --}}
            <div class="saas-table-container">
                <table class="saas-table" id="pricingGrid">
                    <thead>
                        <tr>
                            <th style="width: 3%;" class="text-center">#</th>
                            <th style="width: 8%;">Code</th>
                            <th style="width: 21%;">Item name &amp; variants</th>
                            <th style="width: 12%;">Category</th>
                            <th style="width: 8%;" class="text-end">Current stock</th>
                            <th style="width: 8%;" class="text-end" title="Double click to edit">Alert limit</th>
                            <th style="width: 10%;" class="text-end" title="Weighted average purchase price from invoices">Avg. purchase rate</th>
                            <th style="width: 10%;" class="text-end" title="Double click to edit base purchase rate">Base purchase rate</th>
                            <th style="width: 10%;" class="text-end" title="Stock quantity multiplied by average purchase price">Stock value</th>
                            <th style="width: 10%;" class="text-end" title="Double click to edit base sale rate">Base sale rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $idx => $item)
                            @php
                                $stockClass = $item->is_low_stock ? 'tag-lowstock' : 'tag-instock';
                                $hasVariants = !empty($item->variants) && count($item->variants) > 0;
                            @endphp
                            <tr id="row-product-{{ $item->id }}" data-id="{{ $item->id }}" data-sizemode="{{ $item->size_mode }}" data-ppb="{{ $item->pieces_per_box }}">
                                
                                {{-- Index --}}
                                <td class="text-center" style="font-size:0.72rem; color:#111827; font-weight:700;">
                                    {{ $paginator->firstItem() + $idx }}
                                </td>

                                {{-- Code --}}
                                <td class="num-col" style="text-align:left; color:#111827; font-weight:700;">
                                    {{ $item->item_code }}
                                </td>

                                {{-- Item Name --}}
                                <td>
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <span class="text-truncate item-title-bold" title="{{ $item->item_name }}">{{ $item->item_name }}</span>
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                            <span class="tag-neutral">{{ strtoupper(str_replace('by_', '', $item->size_mode)) }}</span>
                                            @if($hasVariants)
                                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 toggle-variants-btn" data-target="#drawer-{{ $item->id }}" style="font-size: 9px; line-height: 1.2;">
                                                    {{ count($item->variants) }} variants
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td>
                                    <div class="text-truncate" style="font-size:0.74rem; color:#111827; font-weight:700;">{{ $item->category_name }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.65rem;">{{ $item->subcategory_name }}</div>
                                </td>

                                {{-- Current Stock --}}
                                <td class="num-col" style="color:#111827; font-weight:700;">
                                    <span class="status-tag {{ $stockClass }}" id="stock-val-{{ $item->id }}">
                                        {{ number_format($item->current_stock, ($item->size_mode === 'by_kg' ? 2 : 0)) }}
                                    </span>
                                    <span class="ms-1" style="font-size:0.68rem; color:#111827; font-weight:700;">{{ $item->unit_name }}</span>
                                </td>

                                {{-- Alert Limit (Editable) --}}
                                <td class="num-col editable-cell" 
                                    data-field="alert_quantity" 
                                    data-raw="{{ $item->alert_quantity }}"
                                    title="Double-click to edit alert limit">
                                    <span class="cell-val" style="color:#DC2626; font-weight:700;">{{ $item->alert_quantity > 0 ? number_format($item->alert_quantity, 0) : '—' }}</span>
                                </td>

                                {{-- Avg Purchase Rate --}}
                                <td class="num-col" id="avg-purch-{{ $item->id }}" style="color:#111827 !important; font-weight:700;">
                                    Rs. {{ number_format($item->avg_purchase_price, 2) }}
                                </td>

                                {{-- Base Purchase Rate (Editable) --}}
                                <td class="num-col editable-cell" 
                                    data-field="purchase_price" 
                                    data-raw="{{ $item->base_purchase_price }}"
                                    title="Double-click to edit purchase rate">
                                    <span class="cell-val" style="color:#111827; font-weight:700;">Rs. {{ number_format($item->base_purchase_price, 2) }}</span>
                                </td>

                                {{-- Total Stock Value --}}
                                <td class="num-col" id="stock-value-{{ $item->id }}" style="color:#111827 !important; font-weight:700;">
                                    Rs. {{ number_format($item->stock_value, 2) }}
                                </td>

                                {{-- Base Sale Rate (Editable) --}}
                                <td class="num-col editable-cell" 
                                    data-field="sale_price" 
                                    data-raw="{{ $item->base_sale_price }}"
                                    title="Double-click to edit sale rate">
                                    <span class="cell-val" style="color:#111827; font-weight:700;">Rs. {{ number_format($item->base_sale_price, 2) }}</span>
                                </td>
                            </tr>

                            {{-- Collapsible Inset Variant Sub-Table --}}
                            @if($hasVariants)
                                <tr id="drawer-{{ $item->id }}" class="variant-drawer d-none" data-parent-id="{{ $item->id }}">
                                    <td colspan="10">
                                        <div class="p-1">
                                            <div class="fw-semibold mb-1 text-muted small" style="font-size: 0.69rem;">
                                                Variants breakdown for {{ $item->item_name }} (Auto-recalculated from base rates):
                                            </div>
                                            <table class="variant-inset-table">
                                                <thead>
                                                    <tr>
                                                        <th>Variant name / specifications</th>
                                                        <th>Weight factor</th>
                                                        <th class="text-end">Piece purchase rate</th>
                                                        <th class="text-end">Piece sale rate</th>
                                                        <th class="text-end">Piece retail rate</th>
                                                        <th class="text-end">Stock units</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="variant-tbody-{{ $item->id }}">
                                                    @foreach($item->variants as $vIdx => $v)
                                                        @php
                                                            $vName = $v['name'] ?? ($v['color'] ?? '') . ' ' . ($v['size'] ?? '');
                                                            if (empty(trim($vName))) $vName = "Variant #" . ($vIdx + 1);
                                                            $vWeight = $v['weight_per_piece'] ?? '';
                                                            $vConv   = (float)($v['conv_factor'] ?? 0);
                                                            if ($vConv <= 0 && $vWeight > 0) $vConv = (float)$vWeight / 1000.0;
                                                            $vPurchP = (float)($v['purch_price'] ?? 0);
                                                            $vSaleP  = (float)($v['sale_price'] ?? 0);
                                                            $vRetP   = (float)($v['retail_price'] ?? 0);
                                                            $vStock  = (float)($v['stock'] ?? $v['variant_stock'] ?? 0);
                                                        @endphp
                                                        <tr class="var-row" data-conv="{{ $vConv }}">
                                                            <td class="fw-bold" style="color:#111827;">{{ $vName }}</td>
                                                            <td>
                                                                @if($item->size_mode === 'by_kg' && $vWeight)
                                                                    <span class="tag-neutral">{{ $vWeight }}g ({{ $vConv }} kg)</span>
                                                                @elseif($item->size_mode === 'by_cartons')
                                                                    <span class="tag-neutral">1/{{ $item->pieces_per_box }} box</span>
                                                                @else
                                                                    <span class="tag-neutral">{{ $v['unit'] ?? 'pcs' }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="num-col var-purch-price" style="color:#111827 !important; font-weight:700;">
                                                                Rs. {{ number_format($vPurchP, 2) }}
                                                            </td>
                                                            <td class="num-col var-sale-price" style="color:#111827 !important; font-weight:700;">
                                                                Rs. {{ number_format($vSaleP, 2) }}
                                                            </td>
                                                            <td class="num-col var-retail-price" style="color:#111827 !important; font-weight:700;">
                                                                Rs. {{ number_format($vRetP, 2) }}
                                                            </td>
                                                            <td class="num-col" style="color:#111827 !important; font-weight:700;">
                                                                {{ number_format($vStock, 0) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    No products found matching the criteria
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Clean SaaS Footer --}}
            <div class="saas-footer">
                <div>
                    Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} records
                </div>
                <div>
                    {{ $paginator->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    
    // Toggle Variant Inset Rows
    $(document).on('click', '.toggle-variants-btn', function() {
        const target = $(this).data('target');
        $(target).toggleClass('d-none');
        const isVisible = !$(target).hasClass('d-none');
        $(this).text(isVisible ? 'Hide' : $(target).find('.var-row').length + ' variants');
    });

    // ── Excel-like Double-Click Inline Editing ──
    $(document).on('dblclick', '.editable-cell', function() {
        startCellEdit($(this));
    });

    function startCellEdit($cell) {
        if ($cell.hasClass('is-editing')) return;

        const currentVal = parseFloat($cell.data('raw')) || 0;
        const fieldName = $cell.data('field');
        const $row = $cell.closest('tr');
        const productId = $row.data('id');

        // Store original content
        $cell.data('orig-html', $cell.html());
        $cell.addClass('is-editing');

        // Create clean numeric input
        const $input = $('<input type="number" step="any" min="0" class="cell-input-box">').val(currentVal);
        $cell.html($input);
        $input.focus().select();

        // Keydown handlers: Enter (Save & Down), Tab (Save & Right), Esc (Cancel)
        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveCellEdit($cell, $input.val());
                const $nextRow = $row.nextAll('tr:not(.variant-drawer)').first();
                if ($nextRow.length) {
                    const $nextCell = $nextRow.find(`.editable-cell[data-field="${fieldName}"]`);
                    if ($nextCell.length) setTimeout(() => startCellEdit($nextCell), 50);
                }
            } else if (e.key === 'Tab') {
                e.preventDefault();
                saveCellEdit($cell, $input.val());
                const $nextEditable = $cell.nextAll('.editable-cell').first();
                if ($nextEditable.length) {
                    setTimeout(() => startCellEdit($nextEditable), 50);
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelCellEdit($cell);
            }
        });

        // Blur handler: Save
        $input.on('blur', function() {
            if ($cell.hasClass('is-editing')) {
                saveCellEdit($cell, $input.val());
            }
        });
    }

    function cancelCellEdit($cell) {
        $cell.removeClass('is-editing');
        $cell.html($cell.data('orig-html'));
    }

    function saveCellEdit($cell, newVal) {
        const productId = $cell.closest('tr').data('id');
        const field = $cell.data('field');
        const parsedVal = parseFloat(newVal);

        if (isNaN(parsedVal) || parsedVal < 0) {
            cancelCellEdit($cell);
            return;
        }

        // Show subtle saving state
        $cell.removeClass('is-editing').html('<span class="text-muted" style="font-size:10px;">Saving…</span>');

        $.ajax({
            url: '{{ route("products.pricing.inline_update") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                field: field,
                value: parsedVal
            },
            success: function(res) {
                if (res.success) {
                    $cell.data('raw', parsedVal);
                    $cell.addClass('save-flash');
                    setTimeout(() => $cell.removeClass('save-flash'), 800);

                    if (field === 'alert_quantity') {
                        $cell.html(`<span class="cell-val" style="color:#DC2626; font-weight:700;">${parsedVal > 0 ? parsedVal.toLocaleString() : '—'}</span>`);
                        if (res.is_low_stock) {
                            $(`#stock-val-${productId}`).removeClass('tag-instock').addClass('tag-lowstock');
                        } else {
                            $(`#stock-val-${productId}`).removeClass('tag-lowstock').addClass('tag-instock');
                        }
                    } else if (field === 'purchase_price') {
                        $cell.html(`<span class="cell-val" style="color:#111827; font-weight:700;">Rs. ${parsedVal.toFixed(2)}</span>`);
                        $(`#avg-purch-${productId}`).text(`Rs. ${res.avg_purchase_price.toFixed(2)}`);
                        $(`#stock-value-${productId}`).text(`Rs. ${res.stock_value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
                        updateVariantSubtable(productId, res.base_purchase_price, res.base_sale_price, res.variants);
                    } else if (field === 'sale_price') {
                        $cell.html(`<span class="cell-val" style="color:#111827; font-weight:700;">Rs. ${parsedVal.toFixed(2)}</span>`);
                        updateVariantSubtable(productId, res.base_purchase_price, res.base_sale_price, res.variants);
                    }

                    showToast('success', res.message);
                } else {
                    cancelCellEdit($cell);
                    showToast('error', res.message || 'Update failed');
                }
            },
            error: function(xhr) {
                cancelCellEdit($cell);
                let err = 'Error updating product';
                if (xhr.responseJSON && xhr.responseJSON.message) err = xhr.responseJSON.message;
                showToast('error', err);
            }
        });
    }

    function updateMarginPill(productId, marginPct) {
        const marginClass = marginPct >= 0 ? 'pos' : 'neg';
        const sign = marginPct >= 0 ? '+' : '';
        $(`#margin-${productId}`).html(`<span class="margin-pill ${marginClass}">${sign}${marginPct.toFixed(1)}%</span>`);
    }

    function updateVariantSubtable(productId, basePurch, baseSale, variants) {
        const $drawerTbody = $(`#variant-tbody-${productId}`);
        if (!$drawerTbody.length || !variants || !variants.length) return;

        $drawerTbody.find('.var-row').each(function(idx) {
            const v = variants[idx];
            if (v) {
                const purchP = parseFloat(v.purch_price) || 0;
                const saleP  = parseFloat(v.sale_price) || 0;
                const retP   = parseFloat(v.retail_price) || 0;

                $(this).find('.var-purch-price').text(`Rs. ${purchP.toFixed(2)}`);
                $(this).find('.var-sale-price').text(`Rs. ${saleP.toFixed(2)}`);
                $(this).find('.var-retail-price').text(`Rs. ${retP.toFixed(2)}`);
            }
        });
    }

    function showToast(icon, title) {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: false,
            });
            Toast.fire({
                icon: icon,
                title: title
            });
        }
    }
});
</script>
@endsection
