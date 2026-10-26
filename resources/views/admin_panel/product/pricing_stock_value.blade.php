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

    /* ── SaaS Themed Select2 Dropdown ── */
    .select2-cat-wrapper {
        min-width: 175px;
    }
    .select2-cat-wrapper .select2-container {
        width: 100% !important;
    }
    .select2-cat-wrapper .select2-container--default .select2-selection--single {
        height: 32px !important;
        border: 1px solid var(--saas-border-dark) !important;
        border-radius: 4px !important;
        background-color: #FFFFFF !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 4px !important;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .select2-cat-wrapper .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-cat-wrapper .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--saas-accent) !important;
        box-shadow: 0 0 0 2px rgba(52, 82, 217, 0.15) !important;
        outline: none !important;
    }
    .select2-cat-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-size: 0.76rem !important;
        font-weight: 500 !important;
        color: var(--saas-text-main) !important;
        line-height: 30px !important;
        padding-left: 6px !important;
        padding-right: 20px !important;
    }
    .select2-cat-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
        right: 4px !important;
    }
    .select2-cat-wrapper .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #94a3b8 !important;
        font-size: 14px !important;
        line-height: 30px !important;
        margin-right: 18px !important;
    }
    .select2-cat-wrapper .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #ef4444 !important;
    }
    .select2-dropdown {
        border: 1px solid var(--saas-border-dark) !important;
        border-radius: 6px !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
        font-size: 0.8rem !important;
        z-index: 9999 !important;
    }
    .select2-search--dropdown {
        padding: 6px 8px !important;
    }
    .select2-search--dropdown .select2-search__field {
        height: 30px !important;
        border: 1px solid var(--saas-border-dark) !important;
        border-radius: 4px !important;
        padding: 4px 8px !important;
        font-size: 0.78rem !important;
        outline: none !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: var(--saas-accent) !important;
        box-shadow: 0 0 0 2px rgba(52, 82, 217, 0.15) !important;
    }
    .select2-results__option {
        padding: 6px 10px !important;
        font-size: 0.78rem !important;
        font-weight: 500 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--saas-accent) !important;
        color: #FFFFFF !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #f1f5f9 !important;
        color: var(--saas-accent) !important;
        font-weight: 600 !important;
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
    .editable-cell, .editable-variant-cell {
        cursor: pointer;
        position: relative;
        background: #FFFFFF;
        transition: background 0.12s ease;
    }
    .editable-cell:hover, .editable-variant-cell:hover {
        background: #EFF6FF !important;
    }
    .editable-cell::after, .editable-variant-cell::after {
        content: "\f304";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 0.58rem;
        color: #94A3B8;
        margin-left: 4px;
        opacity: 0;
        transition: opacity 0.1s ease;
    }
    .editable-cell:hover::after, .editable-variant-cell:hover::after {
        opacity: 1;
        color: var(--saas-accent);
    }

    /* Editable Code Cell in Grid */
    .editable-code-cell {
        cursor: pointer;
        position: relative;
        background: #FFFFFF;
        transition: background 0.12s ease;
    }
    .editable-code-cell:hover {
        background: #EFF6FF !important;
    }
    .editable-code-cell:hover .edit-code-icon {
        opacity: 1 !important;
        color: var(--saas-accent) !important;
    }

    /* Unified Item Code Series Wrapper in Modal */
    .eic-code-wrapper {
        display: flex !important;
        align-items: stretch !important;
        width: 100% !important;
        height: 40px !important;
        border-radius: 6px !important;
        overflow: hidden !important;
        border: 1.5px solid #0ea5e9 !important;
        box-shadow: 0 1px 3px rgba(14, 165, 233, 0.15);
        background: #f1f5f9;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .eic-code-wrapper.has-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
    }
    .eic-code-wrapper.has-success {
        border-color: #10b981 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }

    /* Series Dropdown */
    .eic-series-select {
        width: 115px !important;
        min-width: 95px !important;
        max-width: 130px !important;
        height: 100% !important;
        background-color: #0ea5e9 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-position: right 10px center !important;
        background-repeat: no-repeat !important;
        background-size: 11px 11px !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        font-size: 0.95rem !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 24px 0 12px !important;
        cursor: pointer !important;
        outline: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .eic-series-select option {
        background-color: #ffffff !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        padding: 6px 12px !important;
    }

    /* Code Number Input */
    .eic-code-input {
        flex: 1 1 auto !important;
        height: 100% !important;
        border: none !important;
        border-radius: 0 !important;
        border-left: 1px solid #bae6fd !important;
        border-right: 1px solid #e2e8f0 !important;
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 800 !important;
        font-size: 1rem !important;
        letter-spacing: 0.5px;
        text-align: center !important;
        padding: 0 10px !important;
        outline: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .eic-code-input.is-invalid {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
    }
    .eic-code-input.is-valid {
        background-color: #f0fdf4 !important;
        color: #166534 !important;
    }

    /* Action Buttons (Edit & Add) */
    .eic-code-btn {
        width: 40px !important;
        min-width: 40px !important;
        height: 100% !important;
        background: #f8fafc !important;
        color: #475569 !important;
        border: none !important;
        border-radius: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 0.95rem !important;
        transition: all 0.15s ease !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .eic-code-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .eic-code-btn-add {
        border-left: 1px solid #e2e8f0 !important;
        font-weight: 800 !important;
        font-size: 1.25rem !important;
        color: #0284c7 !important;
    }
    .eic-code-btn-add:hover {
        background: #e0f2fe !important;
        color: #0369a1 !important;
    }

    /* Active Editing Mode */
    .editable-cell.is-editing, .editable-variant-cell.is-editing {
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

    /* Modal stacking context fix for Bootstrap 4 */
    #editItemCodeModal {
        z-index: 1060 !important;
    }
    #newSeriesModal {
        z-index: 1070 !important;
    }
    .modal-backdrop {
        z-index: 1050 !important;
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

                    {{-- Category (Searchable Ajax Select2) --}}
                    <div class="select2-cat-wrapper">
                        <select name="category_id" id="category_filter" class="saas-control select2-cat" style="width: 100%;">
                            <option value="all">All categories</option>
                            @if(request()->filled('category_id') && request('category_id') !== 'all')
                                @php
                                    $selectedCat = $categories->firstWhere('id', request('category_id'));
                                @endphp
                                @if($selectedCat)
                                    <option value="{{ $selectedCat->id }}" selected>{{ $selectedCat->name }}</option>
                                @endif
                            @endif
                            @foreach ($categories as $cat)
                                @if(!request()->filled('category_id') || request('category_id') != $cat->id)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

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
                            <th style="width: 7%;">Code</th>
                            <th style="width: 18%;">Item name &amp; variants</th>
                            <th style="width: 11%;">Category</th>
                            <th style="width: 7%;" class="text-end">Current stock</th>
                            <th style="width: 7%;" class="text-end" title="Double click to edit">Alert limit</th>
                            <th style="width: 9%;" class="text-end" title="Weighted average purchase price from invoices">Avg. purchase rate</th>
                            <th style="width: 9%;" class="text-end" title="Double click to edit base purchase rate">Base purchase rate</th>
                            <th style="width: 9%;" class="text-end" title="Stock quantity multiplied by average purchase price">Stock value</th>
                            <th style="width: 10%;" class="text-end" title="Double click to edit base sale rate">Base sale rate</th>
                            <th style="width: 10%;" class="text-end" title="Double click to edit wholesale rate">Wholesale rate</th>
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
                                <td class="num-col editable-code-cell" 
                                    data-id="{{ $item->id }}" 
                                    data-code="{{ $item->item_code }}" 
                                    data-name="{{ $item->item_name }}"
                                    style="text-align:left; color:#111827; font-weight:700; cursor:pointer;" 
                                    title="Click to edit item code series &amp; number">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <span class="product-item-code" id="code-val-{{ $item->id }}" style="color: #0f172a !important; font-weight: 800; font-size: 0.82rem; letter-spacing: 0.3px;">{{ $item->item_code }}</span>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 ms-1 btn-edit-code-trigger" style="font-size: 10px; line-height: 1.3; border-radius: 3px; font-weight: 600;" title="Edit Series &amp; Number">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </div>
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

                                {{-- Wholesale Rate (Editable) --}}
                                <td class="num-col editable-cell" 
                                    data-field="wholesale_price" 
                                    data-raw="{{ $item->wholesale_price }}"
                                    title="Double-click to edit wholesale rate">
                                    <span class="cell-val" style="color:#111827; font-weight:700;">Rs. {{ number_format($item->wholesale_price, 2) }}</span>
                                </td>
                            </tr>

                            {{-- Collapsible Inset Variant Sub-Table --}}
                            @if($hasVariants)
                                <tr id="drawer-{{ $item->id }}" class="variant-drawer d-none" data-parent-id="{{ $item->id }}">
                                    <td colspan="11">
                                        <div class="p-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="fw-semibold text-muted small" style="font-size: 0.69rem;">
                                                    Variants breakdown for <strong class="text-dark">{{ $item->item_name }}</strong>:
                                                    <span class="text-primary ms-1"><i class="fas fa-edit me-1"></i>Double-click any rate to edit variant price separately</span>
                                                </div>
                                            </div>
                                            <table class="variant-inset-table">
                                                <thead>
                                                    <tr>
                                                        <th>Variant name / specifications</th>
                                                        <th>Weight factor</th>
                                                        <th class="text-end">Piece purchase rate</th>
                                                        <th class="text-end">Piece sale rate</th>
                                                        <th class="text-end">Piece wholesale rate</th>
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
                                                            $vWholeP = (float)($v['wholesale_price'] ?? 0);
                                                            $vRetP   = (float)($v['retail_price'] ?? 0);
                                                            $vStock  = (float)($v['stock'] ?? $v['variant_stock'] ?? 0);

                                                            // Fallbacks if variant rate was not yet saved explicitly
                                                            if ($vPurchP <= 0 && $item->base_purchase_price > 0) {
                                                                if (in_array($item->size_mode, ['by_kg', 'by_meter', 'by_feet']) && $vConv > 0) {
                                                                    $vPurchP = round($item->base_purchase_price * $vConv, 2);
                                                                } elseif ($item->size_mode === 'by_cartons' && $item->pieces_per_box > 0) {
                                                                    $vPurchP = round($item->base_purchase_price / $item->pieces_per_box, 2);
                                                                } else {
                                                                    $vPurchP = (float)$item->base_purchase_price;
                                                                }
                                                            }
                                                            if ($vSaleP <= 0 && $item->base_sale_price > 0) {
                                                                if (in_array($item->size_mode, ['by_kg', 'by_meter', 'by_feet']) && $vConv > 0) {
                                                                    $vSaleP = round($item->base_sale_price * $vConv, 2);
                                                                } elseif ($item->size_mode === 'by_cartons' && $item->pieces_per_box > 0) {
                                                                    $vSaleP = round($item->base_sale_price / $item->pieces_per_box, 2);
                                                                } else {
                                                                    $vSaleP = (float)$item->base_sale_price;
                                                                }
                                                            }
                                                        @endphp
                                                        <tr class="var-row" data-product-id="{{ $item->id }}" data-variant-index="{{ $vIdx }}" data-conv="{{ $vConv }}">
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
                                                            <td class="num-col editable-variant-cell var-purch-price" data-field="purch_price" data-raw="{{ $vPurchP }}" title="Double-click to edit variant purchase rate">
                                                                Rs. {{ number_format($vPurchP, 2) }}
                                                            </td>
                                                            <td class="num-col editable-variant-cell var-sale-price" data-field="sale_price" data-raw="{{ $vSaleP }}" title="Double-click to edit variant sale rate">
                                                                Rs. {{ number_format($vSaleP, 2) }}
                                                            </td>
                                                            <td class="num-col editable-variant-cell var-wholesale-price" data-field="wholesale_price" data-raw="{{ $vWholeP }}" title="Double-click to edit variant wholesale rate">
                                                                Rs. {{ number_format($vWholeP, 2) }}
                                                            </td>
                                                            <td class="num-col editable-variant-cell var-retail-price" data-field="retail_price" data-raw="{{ $vRetP }}" title="Double-click to edit variant retail rate">
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
                                <td colspan="11" class="text-center py-4 text-muted">
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

{{-- Edit Item Code & Series Modal --}}
<div class="modal fade" id="editItemCodeModal" tabindex="-1" role="dialog" aria-labelledby="editItemCodeModalLabel" aria-hidden="true" data-backdrop="static" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden; background: #ffffff;">
            <div class="modal-header py-2 px-3" style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editItemCodeModalLabel" style="font-size: 0.95rem;">
                        <i class="fas fa-barcode text-primary me-2"></i>Edit Item Code &amp; Series
                    </h6>
                    <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                        Product: <strong id="eic_product_name" class="text-dark"></strong>
                    </div>
                </div>
                <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="closeEditItemCodeModal()" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <input type="hidden" id="eic_product_id">
                <input type="hidden" id="eic_original_code">

                <div class="mb-2">
                    <label class="form-label fw-bold text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                        ITEM CODE SERIES
                    </label>
                    <div class="eic-code-wrapper" id="eic_code_wrapper" style="display: flex !important; height: 40px !important; border: 1.5px solid #0ea5e9 !important; border-radius: 6px !important; overflow: hidden !important;">
                        <select class="eic-series-select" id="eic_series_dropdown" title="Choose series prefix" style="background-color: #0ea5e9 !important; color: #ffffff !important; font-weight: 800 !important; font-size: 0.95rem !important; border: none !important; width: 110px !important; min-width: 95px !important; padding: 0 24px 0 12px !important;">
                            @foreach($itemCodeSeries as $series)
                                <option value="{{ $series->prefix }}" data-next="{{ $series->prefix }}{{ str_pad($series->next_number, 4, '0', STR_PAD_LEFT) }}" style="background-color: #ffffff !important; color: #0f172a !important; font-weight: 700 !important;">
                                    {{ trim($series->prefix, '-') }}
                                </option>
                            @endforeach
                        </select>
                        <input type="text" class="eic-code-input" id="eic_code_input" placeholder="e.g. item-024" autocomplete="off" style="background-color: #f1f5f9 !important; color: #0f172a !important; font-weight: 800 !important; font-size: 1rem !important; text-align: center !important;">
                        <button class="eic-code-btn" type="button" id="eic_btn_unlock" title="Focus &amp; Edit Number" style="background: #f8fafc !important; color: #475569 !important; width: 40px !important; border: none !important;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="eic-code-btn eic-code-btn-add" type="button" id="eic_btn_add_series" title="Add New Series" style="background: #f8fafc !important; color: #0284c7 !important; width: 40px !important; border: none !important; border-left: 1px solid #e2e8f0 !important; font-size: 1.25rem !important; font-weight: 800 !important;">+</button>
                    </div>
                    <div id="eic_feedback" class="small mt-2" style="font-size: 12px; min-height: 20px;"></div>
                </div>

                <div class="alert alert-light border small mt-2 mb-0 p-2 text-muted" style="font-size: 11.5px; border-radius: 4px; background: #F8FAFC;">
                    <i class="fas fa-shield-alt text-primary me-1"></i>
                    <strong>Duplicate Prevention Active:</strong> Agar ye code kisi aur product ko pehle se assign hai (jaise <code>item/024</code>), to system use duplicate nahi hone dega aur error aayega.
                </div>
            </div>
            <div class="modal-footer py-2 px-3" style="background: #F8FAFC; border-top: 1px solid #E2E8F0;">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeEditItemCodeModal()">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary fw-bold px-3" id="eic_btn_save">
                    <i class="fas fa-save me-1"></i> Save Item Code
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Add New Series Modal --}}
<div class="modal fade" id="newSeriesModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 6px; background: #ffffff;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-1"></i> Add Series</h6>
                <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="$('#newSeriesModal').modal('hide')" style="border:none; background:transparent; font-size:24px; line-height:1; opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newSeriesForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Prefix (e.g. PRD-, dbd, item/)</label>
                        <input type="text" name="prefix" id="new_series_prefix" class="form-control form-control-sm" required placeholder="e.g. PRD- or item/">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded fw-bold" id="btn_save_series">Save Series</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    
    // Ensure modals are appended to body to prevent backdrop/overflow issues
    $('#editItemCodeModal, #newSeriesModal').appendTo('body');
    
    // ── Searchable Ajax Select2 for Category Filter ──
    if ($.fn.select2) {
        $('#category_filter').select2({
            placeholder: "All categories",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('get-categories') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        select2: 1
                    };
                },
                processResults: function (data) {
                    return data;
                },
                cache: true
            }
        });

        $('#category_filter').on('select2:clear', function () {
            $(this).val('all');
        });

        $('#filterForm').on('submit', function () {
            if (!$('#category_filter').val()) {
                $('#category_filter').val('all');
            }
        });
    }
    
    // Toggle Variant Inset Rows
    $(document).on('click', '.toggle-variants-btn', function() {
        const target = $(this).data('target');
        $(target).toggleClass('d-none');
        const isVisible = !$(target).hasClass('d-none');
        $(this).text(isVisible ? 'Hide' : $(target).find('.var-row').length + ' variants');
    });

    // ── Excel-like Click or Double-Click Inline Editing ──
    $(document).on('click dblclick', '.editable-cell', function() {
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
                    } else if (field === 'wholesale_price') {
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
                const wholeP = parseFloat(v.wholesale_price) || 0;
                const retP   = parseFloat(v.retail_price) || 0;

                const $purch = $(this).find('.var-purch-price');
                const $sale = $(this).find('.var-sale-price');
                const $whole = $(this).find('.var-wholesale-price');
                const $ret = $(this).find('.var-retail-price');

                if (!$purch.hasClass('is-editing')) {
                    $purch.data('raw', purchP).attr('data-raw', purchP).text(`Rs. ${purchP.toFixed(2)}`);
                }
                if (!$sale.hasClass('is-editing')) {
                    $sale.data('raw', saleP).attr('data-raw', saleP).text(`Rs. ${saleP.toFixed(2)}`);
                }
                if (!$whole.hasClass('is-editing')) {
                    $whole.data('raw', wholeP).attr('data-raw', wholeP).text(`Rs. ${wholeP.toFixed(2)}`);
                }
                if (!$ret.hasClass('is-editing')) {
                    $ret.data('raw', retP).attr('data-raw', retP).text(`Rs. ${retP.toFixed(2)}`);
                }
            }
        });
    }

    // ── Variant-specific Inline Click or Double-Click Editing ──
    $(document).on('click dblclick', '.editable-variant-cell', function() {
        startVariantCellEdit($(this));
    });

    function startVariantCellEdit($cell) {
        if ($cell.hasClass('is-editing')) return;

        const currentVal = parseFloat($cell.data('raw')) || 0;
        const fieldName = $cell.data('field');
        const $row = $cell.closest('.var-row');

        // Store original content
        $cell.data('orig-html', $cell.html());
        $cell.addClass('is-editing');

        // Create numeric input box
        const $input = $('<input type="number" step="any" min="0" class="cell-input-box">').val(currentVal);
        $cell.html($input);
        $input.focus().select();

        // Keydown handlers: Enter (Save & Down), Tab (Save & Right), Esc (Cancel)
        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveVariantCellEdit($cell, $input.val());
                const $nextRow = $row.next('.var-row');
                if ($nextRow.length) {
                    const $nextCell = $nextRow.find(`.editable-variant-cell[data-field="${fieldName}"]`);
                    if ($nextCell.length) setTimeout(() => startVariantCellEdit($nextCell), 50);
                }
            } else if (e.key === 'Tab') {
                e.preventDefault();
                saveVariantCellEdit($cell, $input.val());
                const $nextEditable = $cell.nextAll('.editable-variant-cell').first();
                if ($nextEditable.length) {
                    setTimeout(() => startVariantCellEdit($nextEditable), 50);
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelVariantCellEdit($cell);
            }
        });

        // Blur handler: Save
        $input.on('blur', function() {
            if ($cell.hasClass('is-editing')) {
                saveVariantCellEdit($cell, $input.val());
            }
        });
    }

    function cancelVariantCellEdit($cell) {
        $cell.removeClass('is-editing');
        $cell.html($cell.data('orig-html'));
    }

    function saveVariantCellEdit($cell, newVal) {
        const $row = $cell.closest('.var-row');
        const productId = $row.data('product-id');
        const variantIndex = $row.data('variant-index');
        const field = $cell.data('field');
        const parsedVal = parseFloat(newVal);

        if (isNaN(parsedVal) || parsedVal < 0) {
            cancelVariantCellEdit($cell);
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
                variant_index: variantIndex,
                field: field,
                value: parsedVal
            },
            success: function(res) {
                if (res.success) {
                    $cell.data('raw', parsedVal).attr('data-raw', parsedVal);
                    $cell.html(`<span class="cell-val" style="color:#111827; font-weight:700;">Rs. ${parsedVal.toFixed(2)}</span>`);
                    $cell.addClass('save-flash');
                    setTimeout(() => $cell.removeClass('save-flash'), 800);

                    if (res.avg_purchase_price !== undefined) {
                        $(`#avg-purch-${productId}`).text(`Rs. ${parseFloat(res.avg_purchase_price).toFixed(2)}`);
                    }
                    if (res.stock_value !== undefined) {
                        $(`#stock-value-${productId}`).text(`Rs. ${parseFloat(res.stock_value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
                    }
                    if (res.margin_pct !== undefined) {
                        updateMarginPill(productId, res.margin_pct);
                    }

                    // Update Top/Master Row Base Sale Rate
                    if (res.base_sale_price !== undefined) {
                        const $masterSaleCell = $(`#row-product-${productId}`).find('.editable-cell[data-field="sale_price"]');
                        if ($masterSaleCell.length) {
                            $masterSaleCell.data('raw', res.base_sale_price).attr('data-raw', res.base_sale_price);
                            $masterSaleCell.find('.cell-val').text(`Rs. ${parseFloat(res.base_sale_price).toFixed(2)}`);
                            $masterSaleCell.addClass('save-flash');
                            setTimeout(() => $masterSaleCell.removeClass('save-flash'), 800);
                        }
                    }

                    // Update Top/Master Row Base Purchase Rate
                    if (res.base_purchase_price !== undefined) {
                        const $masterPurchCell = $(`#row-product-${productId}`).find('.editable-cell[data-field="purchase_price"]');
                        if ($masterPurchCell.length) {
                            $masterPurchCell.data('raw', res.base_purchase_price).attr('data-raw', res.base_purchase_price);
                            $masterPurchCell.find('.cell-val').text(`Rs. ${parseFloat(res.base_purchase_price).toFixed(2)}`);
                            $masterPurchCell.addClass('save-flash');
                            setTimeout(() => $masterPurchCell.removeClass('save-flash'), 800);
                        }
                    }

                    // Update Top/Master Row Wholesale Rate
                    if (res.wholesale_price !== undefined) {
                        const $masterWholeCell = $(`#row-product-${productId}`).find('.editable-cell[data-field="wholesale_price"]');
                        if ($masterWholeCell.length) {
                            $masterWholeCell.data('raw', res.wholesale_price).attr('data-raw', res.wholesale_price);
                            $masterWholeCell.find('.cell-val').text(`Rs. ${parseFloat(res.wholesale_price).toFixed(2)}`);
                            $masterWholeCell.addClass('save-flash');
                            setTimeout(() => $masterWholeCell.removeClass('save-flash'), 800);
                        }
                    }

                    // Update other variant rows if variants array returned
                    if (res.variants && res.variants.length) {
                        updateVariantSubtable(productId, res.base_purchase_price, res.base_sale_price, res.variants);
                    }

                    showToast('success', res.message || 'Variant price updated successfully');
                } else {
                    cancelVariantCellEdit($cell);
                    showToast('error', res.message || 'Update failed');
                }
            },
            error: function(xhr) {
                cancelVariantCellEdit($cell);
                let err = 'Error updating variant price';
                if (xhr.responseJSON && xhr.responseJSON.message) err = xhr.responseJSON.message;
                showToast('error', err);
            }
        });
    }

    // ── Open Edit Item Code Modal on Click or Double-Click ──
    $(document).on('click', '.editable-code-cell, .btn-edit-code-trigger, .product-item-code', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $cell = $(this).closest('.editable-code-cell');
        const productId = $cell.data('id');
        const currentCode = String($cell.attr('data-code') || $cell.find('.product-item-code').text()).trim();
        const productName = $cell.data('name') || '';

        openEditItemCodeModal(productId, currentCode, productName);
    });

    $(document).on('dblclick', '.editable-code-cell', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $cell = $(this);
        const productId = $cell.data('id');
        const currentCode = String($cell.attr('data-code') || $cell.find('.product-item-code').text()).trim();
        const productName = $cell.data('name') || '';

        openEditItemCodeModal(productId, currentCode, productName);
    });

    let checkCodeTimer = null;

    window.openEditItemCodeModal = function(productId, currentCode, productName) {
        $('#eic_product_id').val(productId);
        $('#eic_original_code').val(currentCode);
        $('#eic_product_name').text(productName || 'Product #' + productId);
        $('#eic_code_input').val(currentCode).removeClass('is-invalid is-valid');
        $('#eic_code_wrapper').removeClass('has-error has-success');
        $('#eic_feedback').html('');
        $('#eic_btn_save').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Item Code');

        // Match series prefix from dropdown options
        let matched = false;
        $('#eic_series_dropdown option').each(function() {
            const prefix = $(this).val();
            if (prefix && currentCode.startsWith(prefix)) {
                $('#eic_series_dropdown').val(prefix);
                matched = true;
                return false;
            }
        });

        if (!matched) {
            $('#eic_series_dropdown option').each(function() {
                const prefix = $(this).val().replace(/[-/_]$/, '');
                if (prefix && currentCode.startsWith(prefix)) {
                    $('#eic_series_dropdown').val($(this).val());
                    matched = true;
                    return false;
                }
            });
        }

        // Ensure modal is attached directly to body
        if (!$('#editItemCodeModal').parent().is('body')) {
            $('#editItemCodeModal').appendTo('body');
        }

        // Show modal via Bootstrap 4 jQuery
        $('#editItemCodeModal').modal('show');

        setTimeout(function() {
            $('#eic_code_input').focus().select();
            eicCheckAvailability();
        }, 250);
    };

    window.closeEditItemCodeModal = function() {
        $('#editItemCodeModal').modal('hide');
    };

    // ── When user changes the series prefix dropdown ──
    $(document).on('change', '#eic_series_dropdown', function() {
        const $sel = $(this);
        const $opt = $sel.find('option:selected');
        const nextVal = $opt.attr('data-next') || $sel.val();
        $('#eic_code_input').val(nextVal).focus();
        eicCheckAvailability();
    });

    // ── Unlock / Focus input button ──
    $(document).on('click', '#eic_btn_unlock', function() {
        $('#eic_code_input').focus().select();
    });

    // ── Open Add Series modal ──
    $(document).on('click', '#eic_btn_add_series', function() {
        showNewSeriesModal();
    });

    // ── Input typing / pasting debounced live check ──
    $(document).on('input propertychange paste change', '#eic_code_input', function() {
        eicCheckAvailability();
    });

    $(document).on('keydown', '#eic_code_input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (!$('#eic_btn_save').is(':disabled')) {
                eicSaveItemCode();
            }
        }
    });

    function eicCheckAvailability() {
        clearTimeout(checkCodeTimer);
        const code = $('#eic_code_input').val().trim();
        const prefix = $('#eic_series_dropdown').val() || '';
        const productId = $('#eic_product_id').val();
        const origCode = $('#eic_original_code').val().trim();

        if (!code) {
            $('#eic_feedback').html('<span class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i> Item code cannot be empty.</span>');
            $('#eic_code_wrapper').addClass('has-error').removeClass('has-success');
            $('#eic_code_input').addClass('is-invalid').removeClass('is-valid');
            $('#eic_btn_save').prop('disabled', true);
            return;
        }

        if (code.toLowerCase() === origCode.toLowerCase()) {
            $('#eic_feedback').html('<span class="fw-bold" style="color: #0284c7 !important;"><i class="fas fa-check-circle me-1"></i> Current item code of this product (unchanged).</span>');
            $('#eic_code_wrapper').removeClass('has-error has-success');
            $('#eic_code_input').removeClass('is-invalid is-valid');
            $('#eic_btn_save').prop('disabled', false);
            return;
        }

        $('#eic_feedback').html('<span class="text-muted fw-semibold"><i class="fas fa-spinner fa-spin me-1"></i> Checking availability...</span>');

        checkCodeTimer = setTimeout(function() {
            $.ajax({
                url: "{{ route('products.check_item_code') }}",
                type: 'GET',
                data: {
                    code: code,
                    prefix: prefix,
                    exclude_id: productId
                },
                success: function(res) {
                    if (res.exists) {
                        $('#eic_feedback').html(
                            `<span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> Item code "${res.full_code}" already exists! Duplicate codes are not allowed.</span>`
                        );
                        $('#eic_code_wrapper').addClass('has-error').removeClass('has-success');
                        $('#eic_code_input').addClass('is-invalid').removeClass('is-valid');
                        $('#eic_btn_save').prop('disabled', true);
                    } else {
                        $('#eic_feedback').html(
                            `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Item code "${res.full_code}" is available.</span>`
                        );
                        $('#eic_code_wrapper').removeClass('has-error').addClass('has-success');
                        $('#eic_code_input').removeClass('is-invalid').addClass('is-valid');
                        $('#eic_btn_save').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#eic_feedback').html('<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Unable to verify code availability.</span>');
                    $('#eic_btn_save').prop('disabled', false);
                }
            });
        }, 220);
    }

    // ── Save Item Code ──
    $(document).on('click', '#eic_btn_save', function() {
        eicSaveItemCode();
    });

    window.eicSaveItemCode = function() {
        const productId = $('#eic_product_id').val();
        const code = $('#eic_code_input').val().trim();
        const prefix = $('#eic_series_dropdown').val() || '';

        if (!code) {
            $('#eic_feedback').html('<span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> Please enter an item code.</span>');
            return;
        }

        const $btn = $('#eic_btn_save');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('products.pricing.update_item_code') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                item_code: code,
                prefix: prefix
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Item Code');
                if (res.success) {
                    const updatedCode = res.item_code;
                    const $cell = $(`#code-val-${productId}`).closest('td');
                    $(`#code-val-${productId}`).text(updatedCode);
                    $cell.data('code', updatedCode).attr('data-code', updatedCode);
                    $cell.addClass('save-flash');
                    setTimeout(() => $cell.removeClass('save-flash'), 1000);

                    // Close Modal
                    closeEditItemCodeModal();
                    showToast('success', res.message || 'Item code updated successfully');
                } else {
                    $('#eic_feedback').html(`<span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> ${res.message}</span>`);
                    showToast('error', res.message || 'Failed to update item code');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Item Code');
                let err = 'Error updating item code.';
                if (xhr.responseJSON && xhr.responseJSON.message) err = xhr.responseJSON.message;
                $('#eic_feedback').html(`<span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> ${err}</span>`);
                $('#eic_code_wrapper').addClass('has-error');
                showToast('error', err);
            }
        });
    };

    // ── Add New Series Modal ──
    window.showNewSeriesModal = function() {
        if (!$('#newSeriesModal').parent().is('body')) {
            $('#newSeriesModal').appendTo('body');
        }
        $('#new_series_prefix').val('');
        $('#newSeriesModal').modal('show');
        setTimeout(() => $('#new_series_prefix').focus(), 250);
    };

    $('#newSeriesForm').on('submit', function(e) {
        e.preventDefault();
        const prefixVal = $('#new_series_prefix').val().trim();
        if (!prefixVal) return;

        const $btn = $('#btn_save_series');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('item-code-series.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).html('Save Series');
                if (res.success && res.series) {
                    const s = res.series;
                    if ($(`#eic_series_dropdown option[value="${s.prefix}"]`).length === 0) {
                        const newOpt = `<option value="${s.prefix}" data-next="${s.formatted_next}">${s.label}</option>`;
                        $('#eic_series_dropdown').append(newOpt);
                    }
                    $('#eic_series_dropdown').val(s.prefix);
                    $('#eic_code_input').val(s.formatted_next);

                    $('#newSeriesModal').modal('hide');
                    showToast('success', 'Series "' + s.label + '" created successfully!');
                    eicCheckAvailability();
                } else {
                    showToast('error', res.message || 'Failed to create series');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('Save Series');
                let err = 'Error creating series.';
                if (xhr.responseJSON && xhr.responseJSON.message) err = xhr.responseJSON.message;
                showToast('error', err);
            }
        });
    });

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
