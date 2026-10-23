@extends('admin_panel.layout.app')
@section('content')

<style>
    /* ── Stock Adjustment Terminal – Clean Modern ERP Design System ── */
    :root {
        --pos-primary:       #4f46e5;
        --pos-primary-hover: #4338ca;
        --pos-primary-light: #eef2ff;
        --pos-success:       #059669;
        --pos-success-light: #ecfdf5;
        --pos-danger:        #dc2626;
        --pos-danger-light:  #fef2f2;
        --pos-warning:       #d97706;
        --pos-warning-light: #fffbeb;
        --pos-info:          #0284c7;
        --pos-info-light:    #e0f2fe;
        --pos-card-bg:       #ffffff;
        --pos-border:        #e2e8f0;
        --pos-border-subtle: #f1f5f9;
        --pos-text-main:     #0f172a;
        --pos-text-muted:    #64748b;
        --pos-radius-lg:     14px;
        --pos-radius-md:     10px;
        --pos-radius-sm:     6px;
        --pos-shadow-sm:     0 1px 3px rgba(15,23,42,0.05);
        --pos-shadow-md:     0 4px 16px rgba(15,23,42,0.06);
    }

    /* Modern subtle scrollbar */
    .pos-custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .pos-custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .pos-custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .pos-custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .pos-terminal-page {
        background-color: #f8fafc;
        min-height: calc(100vh - 75px);
        padding: 14px 0 20px 0;
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    .pos-terminal-page .container-fluid {
        max-width: 100%;
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        padding-left: 16px;
        padding-right: 16px;
        box-sizing: border-box;
    }

    /* ── Top Bar Header ── */
    .pos-top-bar {
        background: #ffffff;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-lg);
        box-shadow: var(--pos-shadow-sm);
        padding: 10px 18px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        flex: 0 0 auto;
    }
    .pos-top-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pos-top-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--pos-primary-light) 0%, #e0e7ff 100%);
        color: var(--pos-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 2px 6px rgba(79,70,229,0.12);
    }
    .pos-top-title-group h5 {
        margin: 0;
        font-size: 1.10rem;
        font-weight: 700;
        color: var(--pos-text-main);
        letter-spacing: -0.2px;
    }
    .pos-top-title-group small {
        color: var(--pos-text-muted);
        font-size: 0.76rem;
    }

    .pos-wh-selector-group {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-md);
        padding: 4px 12px;
    }
    .pos-wh-label {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--pos-text-muted);
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        margin: 0;
    }
    .pos-wh-select {
        height: 32px;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-sm);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--pos-text-main);
        background: #ffffff;
        outline: none;
        padding: 0 8px;
        min-width: 170px;
    }
    .pos-wh-select:focus {
        border-color: var(--pos-primary);
    }

    .btn-audit-logs {
        background: #ffffff;
        color: var(--pos-text-muted);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-md);
        height: 38px;
        padding: 0 14px;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .btn-audit-logs:hover {
        background: #f1f5f9;
        color: var(--pos-text-main);
    }

    /* ── Main 2-Column POS Layout ── */
    .pos-main-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 14px;
        flex: 1 1 auto;
        height: calc(100vh - 165px);
        max-height: calc(100vh - 165px);
        min-height: 480px;
        overflow: hidden;
        width: 100%;
        max-width: 100%;
    }

    /* ── LEFT PANEL: Products & Filters ── */
    .pos-products-panel {
        background: var(--pos-card-bg);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-lg);
        box-shadow: var(--pos-shadow-sm);
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        min-width: 0;
        max-width: 100%;
    }

    .pos-search-wrapper {
        position: relative;
        margin-bottom: 12px;
        flex: 0 0 auto;
    }
    .pos-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
    }
    .pos-search-input {
        width: 100%;
        height: 40px;
        padding-left: 40px;
        padding-right: 14px;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-md);
        font-size: 0.86rem;
        color: var(--pos-text-main);
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .pos-search-input:focus {
        border-color: var(--pos-primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
    }

    /* Category bar */
    .pos-cat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        flex: 0 0 auto;
    }
    .pos-cat-label {
        font-size: 0.70rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--pos-text-muted);
    }
    .pos-count-badge {
        font-size: 0.70rem;
        color: var(--pos-text-muted);
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
    }
    .pos-cat-pills {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 6px;
        margin-bottom: 12px;
        flex: 0 0 auto;
        white-space: nowrap;
    }
    .pos-cat-pill {
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid var(--pos-border);
        background: #ffffff;
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--pos-text-muted);
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .pos-cat-pill:hover {
        background: #f1f5f9;
        color: var(--pos-text-main);
    }
    .pos-cat-pill.active {
        background: var(--pos-primary);
        color: #ffffff;
        border-color: var(--pos-primary);
    }

    /* Product Cards Grid */
    .pos-items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(195px, 1fr));
        gap: 14px;
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 4px 6px 14px 4px;
        align-content: start;
    }

    .pos-prod-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px 10px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 145px;
        box-sizing: border-box;
        cursor: pointer;
        user-select: none;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        position: relative;
        overflow: hidden;
    }
    .pos-prod-card:hover {
        transform: translateY(-2px);
        border-color: var(--pos-primary);
        box-shadow: 0 6px 16px rgba(79,70,229,0.12);
    }
    .pos-prod-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: 6px;
    }
    .pos-prod-code {
        font-family: monospace;
        font-size: 0.70rem;
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 2px 7px;
    }
    .pos-prod-title {
        font-weight: 700;
        font-size: 0.84rem;
        color: #1e293b;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 6px;
        word-break: break-word;
    }

    .pos-prod-variant-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.70rem;
        font-weight: 700;
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
        border-radius: 6px;
        padding: 2px 7px;
    }
    .pos-prod-standard-tag {
        font-size: 0.70rem;
        font-weight: 600;
        color: #94a3b8;
    }

    .pos-prod-action {
        margin-top: auto;
        padding-top: 10px;
        width: 100%;
        box-sizing: border-box;
    }
    .pos-prod-btn-action {
        width: 100%;
        height: 30px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.74rem;
        font-weight: 700;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .pos-prod-card:hover .pos-prod-btn-action {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(79,70,229,0.25);
    }

    /* ── RIGHT PANEL: Cart & Totals ── */
    .pos-cart-panel {
        background: var(--pos-card-bg);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-lg);
        box-shadow: var(--pos-shadow-sm);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }

    .pos-cart-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--pos-border);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex: 0 0 auto;
    }
    .pos-cart-header-title {
        font-size: 0.90rem;
        font-weight: 700;
        color: var(--pos-text-main);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }
    .pos-cart-badge {
        background: var(--pos-primary-light);
        color: var(--pos-primary);
        border: 1px solid #c7d2fe;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 12px;
        padding: 2px 8px;
    }
    .btn-clear-cart {
        color: var(--pos-danger);
        background: transparent;
        border: none;
        font-size: 0.76rem;
        font-weight: 600;
        cursor: pointer;
        padding: 4px 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-clear-cart:hover {
        text-decoration: underline;
    }

    /* Cart Items Wrap */
    .pos-cart-items-wrap {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 10px 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pos-cart-item {
        background: #ffffff;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-md);
        padding: 10px 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: border-color 0.15s ease;
    }
    .pos-cart-item:hover {
        border-color: #cbd5e1;
    }
    .pos-cart-item-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }
    .pos-cart-item-name {
        font-weight: 700;
        font-size: 0.83rem;
        color: var(--pos-text-main);
        line-height: 1.25;
    }
    .pos-cart-item-code {
        font-family: monospace;
        font-size: 0.68rem;
        color: var(--pos-text-muted);
    }
    .btn-remove-item {
        color: #94a3b8;
        background: transparent;
        border: none;
        font-size: 13px;
        cursor: pointer;
        padding: 2px 4px;
        transition: color 0.15s ease;
    }
    .btn-remove-item:hover {
        color: var(--pos-danger);
    }

    .pos-cart-item-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid var(--pos-border-subtle);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.78rem;
    }
    .pos-item-action-badge {
        font-weight: 700;
        font-size: 0.74rem;
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .action-add { background: var(--pos-success-light); color: var(--pos-success); border: 1px solid #a7f3d0; }
    .action-subtract { background: var(--pos-danger-light); color: var(--pos-danger); border: 1px solid #fecaca; }
    .action-set { background: var(--pos-info-light); color: var(--pos-info); border: 1px solid #bae6fd; }

    .pos-item-flow {
        font-size: 0.75rem;
        color: var(--pos-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pos-item-flow strong {
        color: var(--pos-text-main);
    }

    /* Cart Reason Section */
    .pos-cart-reason-box {
        padding: 10px 14px;
        background: #f8fafc;
        border-top: 1px solid var(--pos-border);
        flex: 0 0 auto;
    }
    .pos-reason-label {
        font-size: 0.70rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--pos-text-muted);
        margin-bottom: 6px;
        display: block;
    }
    .pos-reason-presets {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .btn-preset-reason {
        background: #ffffff;
        border: 1px solid var(--pos-border);
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 0.70rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.12s ease;
    }
    .btn-preset-reason:hover {
        background: var(--pos-primary-light);
        color: var(--pos-primary);
        border-color: #c7d2fe;
    }
    .btn-preset-reason.active {
        background: var(--pos-primary);
        color: #ffffff;
        border-color: var(--pos-primary);
    }

    .pos-reason-textarea {
        width: 100%;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-sm);
        padding: 6px 10px;
        font-size: 0.80rem;
        color: var(--pos-text-main);
        background: #ffffff;
        outline: none;
        resize: none;
        transition: border-color 0.15s ease;
    }
    .pos-reason-textarea:focus {
        border-color: var(--pos-primary);
    }

    /* Summary Totals Box (Clean ERP Palette) */
    .pos-summary-box {
        padding: 10px 14px;
        background: #ffffff;
        border-top: 1px solid var(--pos-border);
        flex: 0 0 auto;
    }
    .pos-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.76rem;
        color: var(--pos-text-muted);
        margin-bottom: 3px;
    }
    .pos-summary-row strong {
        color: var(--pos-text-main);
        font-weight: 700;
    }
    .pos-summary-row.grand-total {
        font-size: 0.90rem;
        font-weight: 800;
        color: var(--pos-primary);
        border-top: 1px dashed var(--pos-border);
        padding-top: 4px;
        margin-top: 4px;
        margin-bottom: 0;
    }

    .btn-process-adj {
        width: 100%;
        height: 38px;
        background: linear-gradient(135deg, var(--pos-success) 0%, #10b981 100%);
        color: #ffffff;
        border: none;
        border-radius: var(--pos-radius-md);
        font-size: 0.88rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 8px;
        box-shadow: 0 3px 10px rgba(5,150,105,0.22);
    }
    .btn-process-adj:hover:not(:disabled) {
        background: linear-gradient(135deg, #047857 0%, var(--pos-success) 100%);
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(5,150,105,0.32);
    }
    .btn-process-adj:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.7;
    }

    /* ── Modal Variant Cards & Steppers ── */
    .variant-adjust-card {
        background: #ffffff;
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-md);
        padding: 12px 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .variant-adjust-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    }

    /* Segmented Action Pill Group (+ Add, - Deduct, = Set) */
    .action-pill-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 8px;
        border: 1px solid var(--pos-border);
        gap: 3px;
    }
    .action-pill {
        padding: 4px 10px;
        font-size: 0.74rem;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        border: none;
        background: transparent;
        color: var(--pos-text-muted);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        outline: none;
        user-select: none;
    }
    .action-pill:hover {
        color: var(--pos-text-main);
        background: #e2e8f0;
    }
    .action-pill.active[data-action="add"] {
        background: var(--pos-success);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(5,150,105,0.3);
    }
    .action-pill.active[data-action="subtract"] {
        background: var(--pos-danger);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(220,38,38,0.3);
    }
    .action-pill.active[data-action="set"] {
        background: var(--pos-info);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(2,132,199,0.3);
    }

    /* Stepper Control */
    .stepper-control {
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--pos-border);
        border-radius: 8px;
        background: #ffffff;
        overflow: hidden;
    }
    .stepper-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: #f8fafc;
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.12s ease;
        user-select: none;
        outline: none;
    }
    .stepper-btn:hover {
        background: #e2e8f0;
    }
    .stepper-val {
        width: 60px;
        height: 32px;
        border: none;
        border-left: 1px solid var(--pos-border);
        border-right: 1px solid var(--pos-border);
        text-align: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--pos-text-main);
        outline: none;
    }
    .stepper-unit {
        padding: 0 8px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--pos-text-muted);
        background: #f8fafc;
        height: 32px;
        display: flex;
        align-items: center;
        border-left: 1px solid var(--pos-border);
        user-select: none;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .pos-main-layout {
            grid-template-columns: 1fr;
            height: auto;
            max-height: none;
            overflow: visible;
        }
        .pos-items-grid {
            max-height: 440px;
            height: 440px;
        }
    }
</style>

<div class="pos-terminal-page">
    <div class="container-fluid px-3 px-md-4">

        {{-- ── 1. Top Bar Header ── --}}
        <div class="pos-top-bar">
            <div class="pos-top-title-group">
                <div class="pos-top-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div>
                    <h5>Stock Adjustment Terminal</h5>
                    <small>Point-of-sale style rapid stock adjustments for items and variants</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                {{-- Active Warehouse Selector --}}
                <div class="pos-wh-selector-group">
                    <label class="pos-wh-label">
                        <i class="fas fa-warehouse text-primary"></i> Target Warehouse:
                    </label>
                    <select id="terminal_warehouse_id" class="pos-wh-select">
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ $wh->id == 1 ? 'selected' : '' }}>
                                {{ $wh->name ?? $wh->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Audit Logs Link --}}
                <a href="{{ route('stock_adjustments.index') }}" class="btn-audit-logs">
                    <i class="fas fa-history text-primary"></i>
                    <span>Audit Logs</span>
                </a>
            </div>
        </div>

        {{-- ── 2. Main 2-Column POS Layout ── --}}
        <div class="pos-main-layout">

            {{-- LEFT PANEL: Products Grid & Search --}}
            <div class="pos-products-panel">
                
                {{-- Search Box --}}
                <div class="pos-search-wrapper">
                    <i class="fas fa-search pos-search-icon"></i>
                    <input type="text" id="posProductSearch" class="pos-search-input" placeholder="Search product name, item code, or barcode to adjust...">
                </div>

                {{-- Category Filter Bar --}}
                <div class="pos-cat-header">
                    <span class="pos-cat-label">Categories</span>
                    <span id="productShowingCount" class="pos-count-badge">
                        Showing {{ count($products) }} Products
                    </span>
                </div>

                <div class="pos-cat-pills pos-custom-scroll">
                    <div class="pos-cat-pill active" data-cat="">All Categories</div>
                    @foreach($categories as $cat)
                        <div class="pos-cat-pill" data-cat="{{ $cat->id }}">{{ $cat->name }}</div>
                    @endforeach
                </div>

                {{-- Product Cards Grid --}}
                <div id="posItemsGrid" class="pos-items-grid pos-custom-scroll">
                    @foreach($products as $p)
                        @php
                            $variants = [];
                            if ($p->color) {
                                try {
                                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                                    if (is_array($parsed) && count($parsed) > 0 && (isset($parsed[0]['name']) || isset($parsed[0]['color']))) {
                                        $variants = $parsed;
                                    }
                                } catch (\Exception $e) {}
                            }
                            $hasVariants = count($variants) > 0;
                        @endphp

                        <div class="pos-prod-card" 
                             data-id="{{ $p->id }}"
                             data-name="{{ $p->item_name }}"
                             data-code="{{ $p->item_code }}"
                             data-cat="{{ $p->category_id }}"
                             data-has-variants="{{ $hasVariants ? 1 : 0 }}"
                             data-search="{{ strtolower($p->item_name . ' ' . $p->item_code) }}">
                            
                            <div>
                                <div class="pos-prod-card-top">
                                    <span class="pos-prod-code">{{ $p->item_code }}</span>
                                </div>
                                <div class="pos-prod-title" title="{{ $p->item_name }}">{{ $p->item_name }}</div>
                                
                                @if($hasVariants)
                                    <div class="pos-prod-variant-tag">
                                        <i class="fas fa-tags"></i>
                                        <span>{{ count($variants) }} Variants</span>
                                    </div>
                                @else
                                    <div class="pos-prod-standard-tag">
                                        <i class="fas fa-box me-1"></i> Standard Product
                                    </div>
                                @endif
                            </div>

                            <div class="pos-prod-action">
                                <div class="pos-prod-btn-action">
                                    <i class="fas fa-hand-pointer"></i> <span>Click to Adjust</span>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Empty Notice --}}
                <div id="noProductsNotice" class="text-center py-5 text-muted d-none">
                    <i class="fas fa-box-open fa-3x mb-2" style="color: #cbd5e1;"></i>
                    <p class="fw-bold mb-0">No matching products found</p>
                    <small>Try adjusting your search query or selecting "All Categories"</small>
                </div>

            </div>

            {{-- RIGHT PANEL: Staged Adjustments Cart & Summary --}}
            <div class="pos-cart-panel">
                
                {{-- Cart Header --}}
                <div class="pos-cart-header">
                    <p class="pos-cart-header-title">
                        <i class="fas fa-sliders-h text-primary"></i>
                        <span>Staged Adjustments</span>
                        <span id="cartCount" class="pos-cart-badge">0</span>
                    </p>
                    <button type="button" id="clearCartBtn" class="btn-clear-cart" title="Clear all staged items">
                        <i class="fas fa-trash-alt"></i> Clear All
                    </button>
                </div>

                {{-- Cart Items List --}}
                <div id="cartItemsWrap" class="pos-cart-items-wrap pos-custom-scroll">
                    <div id="emptyCartNotice" class="text-center py-5 text-muted">
                        <div style="width: 56px; height: 56px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                            <i class="fas fa-plus-circle fa-2x text-muted" style="opacity: 0.5;"></i>
                        </div>
                        <p class="mb-1 fw-bold text-dark">No Adjustments Staged</p>
                        <small class="text-muted">Click on any product on the left to select variants and configure stock adjustments.</small>
                    </div>
                </div>

                {{-- Reason / Remarks Box --}}
                <div class="pos-cart-reason-box">
                    <label class="pos-reason-label">
                        Adjustment Reason / Remarks <span class="text-danger">*</span>
                    </label>
                    <div class="pos-reason-presets">
                        <span class="text-muted" style="font-size: 0.68rem; margin-right: 2px;">Presets:</span>
                        <button type="button" class="btn-preset-reason quick-reason-btn" data-reason="Audit physical inventory recount">Audit Recount</button>
                        <button type="button" class="btn-preset-reason quick-reason-btn" data-reason="Unit conversion / Piece correction">Unit Correction</button>
                        <button type="button" class="btn-preset-reason quick-reason-btn" data-reason="Damaged / broken items">Damaged</button>
                        <button type="button" class="btn-preset-reason quick-reason-btn" data-reason="POS exchange / variant swap">POS Swap</button>
                        <button type="button" class="btn-preset-reason quick-reason-btn" data-reason="Physical surplus found">Surplus Found</button>
                    </div>
                    <textarea id="terminal_reason" rows="2" class="pos-reason-textarea" placeholder="Specify reason (e.g. 'Physical inventory count reconciliation')" required></textarea>
                </div>

                {{-- Summary Totals Box (Light Modern Theme) --}}
                <div class="pos-summary-box">
                    <div class="pos-summary-row">
                        <span>Products Affected:</span>
                        <strong id="statProductsCount">0</strong>
                    </div>
                    <div class="pos-summary-row">
                        <span>Variants / Items Adjusted:</span>
                        <strong id="statItemsCount">0 Items</strong>
                    </div>
                    <div class="pos-summary-row">
                        <span>Stock Added (+):</span>
                        <strong class="text-success" id="statAddedQty">+0 Pcs</strong>
                    </div>
                    <div class="pos-summary-row">
                        <span>Stock Deducted (-):</span>
                        <strong class="text-danger" id="statDeductedQty">-0 Pcs</strong>
                    </div>
                    <div class="pos-summary-row grand-total">
                        <span>Net Stock Difference:</span>
                        <span id="statNetDiff">0 Pcs</span>
                    </div>

                    <button type="button" id="submitTerminalBtn" class="btn-process-adj" disabled>
                        <i class="fas fa-check-circle"></i>
                        <span>Process Stock Adjustment (<span id="btnSubmitCount">0</span>)</span>
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     SELECT VARIANT POPUP MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="variantsModal" tabindex="-1" aria-labelledby="variantsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            
            <div class="modal-header border-bottom bg-white px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="variantsModalLabel">
                        <i class="fas fa-tags text-primary me-2"></i>Select Variant to Adjust
                    </h5>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <small class="text-muted">Choose action (+ Add, - Deduct, = Set), set quantity, and click Add</small>
                        <span id="modalWarehouseBadge" class="badge bg-primary text-white px-2 py-1" style="font-size: 0.72rem;">Warehouse: Main Store</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close" style="font-size: 1.1rem; outline: none; border: none; background: none; color: #64748b;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Real-time Variant Filter Search inside Modal --}}
            <div class="px-4 py-2 bg-light border-bottom d-flex align-items-center justify-content-between gap-3">
                <div class="position-relative flex-grow-1">
                    <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px;"></i>
                    <input type="text" id="variantModalSearch" class="form-control form-control-sm ps-4" placeholder="Filter variants by size, color, or code..." style="border-radius: 7px; font-size: 0.82rem; height: 34px;">
                </div>
                <span id="variantCountBadge" class="badge bg-white text-muted border px-2 py-1" style="font-size: 0.72rem;">0 Variants</span>
            </div>

            <div class="modal-body p-3 pos-custom-scroll" style="max-height: 58vh; overflow-y: auto; background: #f8fafc;">
                <div id="variantsModalList" class="d-flex flex-column gap-2">
                    {{-- Dynamically populated variant cards --}}
                </div>
            </div>

            <div class="modal-footer bg-white py-2 px-4 border-top d-flex align-items-center justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle text-primary me-1"></i> Configured variant adjustments will be added to the Staged Adjustments cart.</small>
                <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal" style="border-radius: 8px;">Close</button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function () {

    // Staged Cart Array
    let cart = [];
    let searchTimer = null;
    let initialCardsHtml = $('#posItemsGrid').html();

    // Category Filter Pills
    $('.pos-cat-pill').on('click', function () {
        $('.pos-cat-pill').removeClass('active');
        $(this).addClass('active');
        triggerProductSearch();
    });

    // Real-time Search Input Filter with debounce
    $('#posProductSearch').on('input', function () {
        triggerProductSearch();
    });

    function triggerProductSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            let cat    = $('.pos-cat-pill.active').data('cat') || '';
            let search = $.trim($('#posProductSearch').val());

            if (!cat && !search) {
                $('#posItemsGrid').html(initialCardsHtml).removeClass('d-none');
                $('#noProductsNotice').addClass('d-none');
                $('#productShowingCount').text('Showing {{ count($products) }} Products');
                return;
            }

            $('#productShowingCount').html('<i class="fas fa-spinner fa-spin me-1"></i> Searching...');
            $('#posItemsGrid').css('opacity', '0.5');

            $.ajax({
                url: "{{ route('stock_adjustments.search_products') }}",
                type: "GET",
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: "json",
                data: { q: search, category_id: cat },
                success: function(res) {
                    $('#posItemsGrid').css('opacity', '1');
                    let container = $('#posItemsGrid');
                    container.empty();

                    if (res && res.products && res.products.length > 0) {
                        $('#noProductsNotice').addClass('d-none');
                        container.removeClass('d-none');

                        $.each(res.products, function(i, p) {
                            let variantTag = p.has_variants 
                                ? `<div class="pos-prod-variant-tag"><i class="fas fa-tags"></i> <span>${p.variant_count} Variants</span></div>`
                                : `<div class="pos-prod-standard-tag"><i class="fas fa-box me-1"></i> Standard Product</div>`;

                            let cardHtml = `
                            <div class="pos-prod-card" 
                                 data-id="${p.id}"
                                 data-name="${p.item_name}"
                                 data-code="${p.item_code}"
                                 data-cat="${p.category_id}"
                                 data-has-variants="${p.has_variants}"
                                 data-search="${(p.item_name + ' ' + p.item_code).toLowerCase()}">
                                <div>
                                    <div class="pos-prod-card-top">
                                        <span class="pos-prod-code">${p.item_code}</span>
                                    </div>
                                    <div class="pos-prod-title" title="${p.item_name}">${p.item_name}</div>
                                    ${variantTag}
                                </div>
                                <div class="pos-prod-action">
                                    <div class="pos-prod-btn-action">
                                        <i class="fas fa-hand-pointer"></i> <span>Click to Adjust</span>
                                    </div>
                                </div>
                            </div>`;
                            container.append(cardHtml);
                        });

                        $('#productShowingCount').text('Found ' + res.products.length + ' Products');
                    } else {
                        container.addClass('d-none');
                        $('#noProductsNotice').removeClass('d-none');
                        $('#productShowingCount').text('Found 0 Products');
                    }
                },
                error: function() {
                    $('#posItemsGrid').css('opacity', '1');
                    $('#productShowingCount').text('Search failed');
                }
            });
        }, 220);
    }

    // Filter variants inside modal
    $('#variantModalSearch').on('input', function () {
        let query = $.trim($(this).val()).toLowerCase();
        let count = 0;
        $('#variantsModalList .variant-adjust-card').each(function () {
            let cardText = $(this).text().toLowerCase();
            if (!query || cardText.indexOf(query) > -1) {
                $(this).removeClass('d-none');
                count++;
            } else {
                $(this).addClass('d-none');
            }
        });
        $('#variantCountBadge').text(count + ' Variants');
    });

    // Quick Reason Presets Click
    $(document).on('click', '.quick-reason-btn', function () {
        let reason = $(this).data('reason');
        $('#terminal_reason').val(reason);
        $('.quick-reason-btn').removeClass('active');
        $(this).addClass('active');
    });

    // Segmented Action Pill Click (+ Add, - Deduct, = Set)
    $(document).on('click', '.action-pill', function () {
        let group = $(this).closest('.action-pill-group');
        group.find('.action-pill').removeClass('active');
        $(this).addClass('active');
        group.data('action', $(this).data('action'));
    });

    // Click on Product Card
    $('.pos-prod-card').on('click', function () {
        let card        = $(this);
        let productId   = card.data('id');
        let productName = card.data('name');
        let productCode = card.data('code');
        let hasVariants = card.data('has-variants') == 1;
        let warehouseId = $('#terminal_warehouse_id').val();
        let whName      = $('#terminal_warehouse_id option:selected').text();

        if (hasVariants) {
            // Reset modal search & header
            $('#variantModalSearch').val('');
            $('#variantsModalLabel').html('<i class="fas fa-tags text-primary me-2"></i>' + productName + ' <span class="text-muted font-monospace" style="font-size:0.82rem;">(' + productCode + ')</span>');
            $('#modalWarehouseBadge').text('Warehouse: ' + whName);
            let container = $('#variantsModalList');
            container.html('<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i><p class="mb-0">Loading variants &amp; warehouse stock...</p></div>');
            $('#variantCountBadge').text('Loading...');
            $('#variantsModal').modal('show');

            $.ajax({
                url: "{{ url('stock-adjustments/product-variants') }}/" + productId,
                type: "GET",
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: "json",
                data: { warehouse_id: warehouseId },
                success: function (res) {
                    container.empty();

                    if (res.has_variants && res.variants.length > 0) {
                        $('#variantCountBadge').text(res.variants.length + ' Variants');

                        $.each(res.variants, function (i, v) {
                            let metaBadges = '';
                            if (v.size && v.size !== '-') metaBadges += `<span class="badge bg-light text-dark border"><i class="fas fa-ruler me-1 text-muted"></i>Size: <strong>${v.size}</strong></span>`;
                            if (v.color && v.color !== '-') metaBadges += `<span class="badge bg-light text-dark border ms-1"><i class="fas fa-palette me-1 text-muted"></i>Color: <strong>${v.color}</strong></span>`;
                            metaBadges += `<span class="badge bg-light text-secondary border ms-1">Unit: <strong>${v.unit}</strong></span>`;
                            if (v.conv_factor && v.conv_factor != 1) metaBadges += `<span class="badge bg-light text-secondary border ms-1">Factor: <strong>${v.conv_factor}</strong></span>`;

                            let stockBadgeClass = v.current_stock > 0 
                                ? 'bg-success-subtle text-success border border-success-subtle' 
                                : 'bg-light text-muted border';

                            let cardHtml = `
                            <div class="variant-adjust-card d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3"
                                 data-pid="${productId}" data-pname="${productName}" data-pcode="${productCode}" 
                                 data-vkey="${v.variant_key}" data-vname="${v.name}" data-stock="${v.current_stock}" 
                                 data-unit="${v.unit}" data-step="${v.step}">
                                
                                {{-- Left: Variant details & current stock --}}
                                <div class="flex-grow-1" style="min-width: 200px;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="fw-bold text-dark fs-6">${v.name}</span>
                                        <span class="badge ${stockBadgeClass} px-2 py-1" style="font-size: 0.74rem;">
                                            <i class="fas fa-boxes me-1"></i> Stock: <strong>${v.current_stock}</strong> ${v.unit}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        ${metaBadges}
                                    </div>
                                </div>

                                {{-- Center: Segmented Action Pill Group --}}
                                <div class="d-flex align-items-center justify-content-start justify-content-md-center">
                                    <div class="action-pill-group" data-action="add">
                                        <button type="button" class="action-pill active" data-action="add">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                        <button type="button" class="action-pill" data-action="subtract">
                                            <i class="fas fa-minus"></i> Deduct
                                        </button>
                                        <button type="button" class="action-pill" data-action="set">
                                            <i class="fas fa-equals"></i> Set
                                        </button>
                                    </div>
                                </div>

                                {{-- Right: Stepper & Add Button --}}
                                <div class="d-flex align-items-center gap-2 justify-content-between justify-content-md-end">
                                    <div class="stepper-control">
                                        <button type="button" class="stepper-btn modal-qty-minus">-</button>
                                        <input type="number" step="${v.step}" min="0" value="1" class="stepper-val modal-qty-val">
                                        <button type="button" class="stepper-btn modal-qty-plus">+</button>
                                        <div class="stepper-unit">${v.unit}</div>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm modal-add-btn fw-bold px-3 shadow-sm" style="border-radius: 8px; height: 32px; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fas fa-plus"></i> <span>Add</span>
                                    </button>
                                </div>

                            </div>`;
                            container.append(cardHtml);
                        });
                    } else {
                        $('#variantCountBadge').text('0 Variants');
                        container.html('<div class="text-center py-4 text-muted"><i class="fas fa-info-circle me-1"></i>No variants found for this product.</div>');
                    }
                }
            });
        } else {
            // Standard Product (No variants) -> Add directly to cart
            $.ajax({
                url: "{{ url('stock-adjustments/product-variants') }}/" + productId,
                type: "GET",
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: "json",
                data: { warehouse_id: warehouseId },
                success: function (res) {
                    let curStock = res.total_stock || 0;
                    let pUnit = res.unit || 'Pcs';
                    let pStep = res.step || 1;
                    addItemToCart({
                        cartKey:      productId + '_std',
                        product_id:   productId,
                        product_name: productName,
                        product_code: productCode,
                        variant_key:  '',
                        variant_name: '',
                        unit:         pUnit,
                        step:         pStep,
                        type:         'add',
                        qty:          1,
                        cur_stock:    curStock
                    });
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title: productName + ' added to adjustment list!', showConfirmButton:false, timer:1100 });
                }
            });
        }
    });

    // Warehouse Change Handler
    $('#terminal_warehouse_id').on('change', function () {
        let whName = $(this).find('option:selected').text();
        if (cart.length > 0) {
            Swal.fire({
                title: 'Warehouse Changed',
                text: 'Switched to ' + whName + '. Staged adjustment items from the previous warehouse have been cleared.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
            cart = [];
            renderCart();
        }
    });

    // Modal Stepper + / - Click Handlers
    $(document).on('click', '.modal-qty-plus', function () {
        let input = $(this).siblings('.modal-qty-val');
        let step = parseFloat(input.attr('step')) || 1;
        let val = parseFloat(input.val()) || 0;
        input.val(parseFloat((val + step).toFixed(4)));
    });

    $(document).on('click', '.modal-qty-minus', function () {
        let input = $(this).siblings('.modal-qty-val');
        let step = parseFloat(input.attr('step')) || 1;
        let val = parseFloat(input.val()) || 0;
        if (val > step) {
            input.val(parseFloat((val - step).toFixed(4)));
        } else if (val > 0) {
            input.val(0);
        }
    });

    // Add Variant to Cart from Modal Row
    $(document).on('click', '.modal-add-btn', function () {
        let card     = $(this).closest('.variant-adjust-card');
        let pid      = card.data('pid');
        let pname    = card.data('pname');
        let pcode    = card.data('pcode');
        let vkey     = card.data('vkey') || '';
        let vname    = card.data('vname') || '';
        let unit     = card.data('unit') || 'Pcs';
        let step     = parseFloat(card.data('step')) || 1;
        let curStock = parseFloat(card.data('stock')) || 0;
        let type     = card.find('.action-pill-group').data('action') || 'add';
        let qty      = parseFloat(card.find('.modal-qty-val').val()) || 0;

        if (qty <= 0) {
            Swal.fire('Invalid Quantity', 'Please enter a quantity greater than 0.', 'warning');
            return;
        }

        addItemToCart({
            cartKey:      pid + '_' + (vkey ? vkey : 'std'),
            product_id:   pid,
            product_name: pname,
            product_code: pcode,
            variant_key:  vkey,
            variant_name: vname,
            unit:         unit,
            step:         step,
            type:         type,
            qty:          qty,
            cur_stock:    curStock
        });

        // Visual feedback on button
        let btn = $(this);
        let origHtml = btn.html();
        btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check"></i> <span>Added</span>');
        setTimeout(() => {
            btn.removeClass('btn-success').addClass('btn-primary').html(origHtml);
        }, 900);

        Swal.fire({ toast:true, position:'top-end', icon:'success', title: (vname || pname) + ' (' + qty + ' ' + unit + ') added to list!', showConfirmButton:false, timer:1100 });
    });

    function addItemToCart(item) {
        let existingIndex = cart.findIndex(c => c.cartKey === item.cartKey);
        if (existingIndex > -1) {
            cart[existingIndex] = item;
        } else {
            cart.push(item);
        }
        renderCart();
    }

    // Render Cart Items & Calculate Totals Box
    function renderCart() {
        let container = $('#cartItemsWrap');
        container.empty();

        if (cart.length === 0) {
            container.append(`
            <div id="emptyCartNotice" class="text-center py-5 text-muted">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                    <i class="fas fa-plus-circle fa-2x text-muted" style="opacity: 0.5;"></i>
                </div>
                <p class="mb-1 fw-bold text-dark">No Adjustments Staged</p>
                <small class="text-muted">Click on any product on the left to select variants and configure stock adjustments.</small>
            </div>`);

            $('#cartCount, #statItemsCount, #btnSubmitCount').text('0');
            $('#statProductsCount').text('0');
            $('#statAddedQty').text('+0 Pcs');
            $('#statDeductedQty').text('-0 Pcs');
            $('#statNetDiff').text('0 Pcs');
            $('#submitTerminalBtn').prop('disabled', true);
            return;
        }

        let uniqueProducts = new Set();
        let addedByUnit = {};
        let deductedByUnit = {};

        $.each(cart, function (index, item) {
            uniqueProducts.add(item.product_id);
            let u = item.unit || 'Pcs';

            let expStock = item.cur_stock;
            let typeBadge = '';
            if (item.type === 'add') {
                typeBadge = '<span class="pos-item-action-badge action-add"><i class="fas fa-plus"></i> Add</span>';
                expStock  = item.cur_stock + item.qty;
                addedByUnit[u] = (addedByUnit[u] || 0) + item.qty;
            } else if (item.type === 'subtract') {
                typeBadge = '<span class="pos-item-action-badge action-subtract"><i class="fas fa-minus"></i> Deduct</span>';
                expStock  = Math.max(0, item.cur_stock - item.qty);
                deductedByUnit[u] = (deductedByUnit[u] || 0) + item.qty;
            } else {
                typeBadge = '<span class="pos-item-action-badge action-set"><i class="fas fa-equals"></i> Set</span>';
                expStock  = Math.max(0, item.qty);
                let diff = item.qty - item.cur_stock;
                if (diff > 0) addedByUnit[u] = (addedByUnit[u] || 0) + diff;
                else if (diff < 0) deductedByUnit[u] = (deductedByUnit[u] || 0) + Math.abs(diff);
            }

            let expStockFormatted = (expStock % 1 === 0) ? expStock : expStock.toFixed(2);
            let curStockFormatted = (item.cur_stock % 1 === 0) ? item.cur_stock : item.cur_stock.toFixed(2);

            let rowHtml = `
            <div class="pos-cart-item">
                <div class="pos-cart-item-header">
                    <div>
                        <div class="pos-cart-item-name">${item.variant_name || item.product_name}</div>
                        <div class="pos-cart-item-code">${item.product_code}${item.variant_name ? ' &bull; ' + item.product_name : ''}</div>
                    </div>
                    <button type="button" class="btn-remove-item remove-cart-item" data-key="${item.cartKey}" title="Remove item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="pos-cart-item-body">
                    <div>
                        ${typeBadge}
                        <strong class="ms-1 fs-6">${item.qty}</strong> <small class="text-muted">${item.unit}</small>
                    </div>
                    <div class="pos-item-flow">
                        <span>${curStockFormatted}</span>
                        <i class="fas fa-arrow-right text-muted" style="font-size:0.7rem;"></i>
                        <strong class="text-success">${expStockFormatted} ${item.unit}</strong>
                    </div>
                </div>
            </div>`;

            container.append(rowHtml);
        });

        let addedSummary = Object.keys(addedByUnit).map(u => '+' + (addedByUnit[u] % 1 === 0 ? addedByUnit[u] : addedByUnit[u].toFixed(2)) + ' ' + u).join(', ') || '+0 Pcs';
        let deductedSummary = Object.keys(deductedByUnit).map(u => '-' + (deductedByUnit[u] % 1 === 0 ? deductedByUnit[u] : deductedByUnit[u].toFixed(2)) + ' ' + u).join(', ') || '-0 Pcs';

        $('#cartCount, #statItemsCount, #btnSubmitCount').text(cart.length);
        $('#statProductsCount').text(uniqueProducts.size);
        $('#statAddedQty').text(addedSummary);
        $('#statDeductedQty').text(deductedSummary);
        $('#statNetDiff').text(cart.length + ' Item(s) staged');

        $('#submitTerminalBtn').prop('disabled', false);
    }

    // Remove Item from Cart
    $(document).on('click', '.remove-cart-item', function () {
        let key = $(this).data('key');
        cart = cart.filter(item => item.cartKey !== key);
        renderCart();
    });

    // Clear Entire Cart
    $('#clearCartBtn').on('click', function () {
        if (cart.length === 0) return;
        cart = [];
        renderCart();
    });

    // Submit Terminal Adjustment AJAX
    $('#submitTerminalBtn').on('click', function () {
        let warehouseId  = $('#terminal_warehouse_id').val();
        let globalReason = $('#terminal_reason').val();

        if (cart.length === 0) {
            Swal.fire('Empty List', 'Please add at least one item to adjust.', 'warning');
            return;
        }

        if (!globalReason) {
            Swal.fire('Reason Required', 'Please specify a reason or remarks for this stock adjustment.', 'warning');
            $('#terminal_reason').focus();
            return;
        }

        let btn = $(this);
        let origHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Processing Adjustment...').prop('disabled', true);

        $.ajax({
            url: "{{ route('stock_adjustments.store_batch') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}",
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}",
                warehouse_id: warehouseId,
                global_reason: globalReason,
                items: cart
            },
            success: function (res) {
                if (res && res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Stock Adjusted Successfully!',
                        text: res.message || 'Adjustments saved successfully.',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('stock_adjustments.index') }}";
                    });
                } else {
                    let errMsg = (res && res.message) ? res.message : 'Failed to process adjustment.';
                    Swal.fire('Error', errMsg, 'error');
                    btn.html(origHtml).prop('disabled', false);
                }
            },
            error: function (xhr) {
                btn.html(origHtml).prop('disabled', false);
                let msg = 'Failed to process adjustment.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                } else if (xhr.responseText) {
                    try {
                        let parsed = JSON.parse(xhr.responseText);
                        msg = parsed.message || msg;
                    } catch (e) {
                        msg = 'Server returned error (' + xhr.status + ')';
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Adjustment Error',
                    html: msg
                });
            }
        });
    });

});
</script>
@endsection
