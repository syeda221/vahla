@extends('admin_panel.layout.app')
@section('content')

<style>
    /* ── POS Stock Adjustment Terminal – Fixed Viewport & Visible Product Scrollbar ── */
    :root {
        --pos-primary:    #4f46e5;
        --pos-primary-lt: #eef2ff;
        --pos-success:    #059669;
        --pos-success-lt: #ecfdf5;
        --pos-danger:     #dc2626;
        --pos-danger-lt:  #fef2f2;
        --pos-warning:    #d97706;
        --pos-warning-lt: #fffbeb;
        --pos-info:       #0284c7;
        --pos-info-lt:    #e0f2fe;
        --pos-border:     #e2e8f0;
        --pos-bg:         #f1f5f9;
        --pos-card-bg:    #ffffff;
        --pos-text:       #0f172a;
        --pos-muted:      #64748b;
        --pos-radius:     12px;
        --pos-shadow:     0 2px 10px rgba(0,0,0,.04);
    }

    /* Custom Webkit Scrollbars for Desktop & POS */
    .custom-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #64748b; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }

    /* Prominent Product Grid Scrollbar on Right Side of Left Box */
    .pos-items-grid::-webkit-scrollbar { width: 12px !important; }
    .pos-items-grid::-webkit-scrollbar-track { background: #cbd5e1 !important; border-radius: 6px !important; }
    .pos-items-grid::-webkit-scrollbar-thumb { background: #4f46e5 !important; border-radius: 6px !important; border: 2px solid #cbd5e1 !important; }
    .pos-items-grid::-webkit-scrollbar-thumb:hover { background: #3730a3 !important; }

    .pos-adj-page {
        background: var(--pos-bg);
        min-height: calc(100vh - 75px);
        padding: 10px 0;
        display: flex;
        flex-direction: column;
    }
    .pos-adj-page .container-fluid { max-width: 100%; flex: 1; display: flex; flex-direction: column; }

    /* Top Bar Header */
    .pos-top-bar {
        background: #ffffff; border-radius: var(--pos-radius); border: 1px solid var(--pos-border);
        box-shadow: var(--pos-shadow); padding: 8px 16px; margin-bottom: 10px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
        flex: 0 0 auto;
    }
    .pos-top-bar .title-box { display: flex; align-items: center; gap: 10px; }
    .pos-top-bar .title-icon { width: 36px; height: 36px; border-radius: 9px; background: var(--pos-primary-lt); color: var(--pos-primary); display: flex; align-items: center; justify-content: center; font-size: 16px; }

    /* Main 2-Column POS Layout with Viewport Height Lock */
    .pos-grid-layout {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 14px;
        flex: 1 1 auto;
        height: calc(100vh - 165px);
        max-height: calc(100vh - 165px);
        min-height: 480px;
        overflow: hidden;
        transition: grid-template-columns 0.25s ease;
    }

    /* Left Side — Product & Variant Search/Grid */
    .pos-products-panel {
        background: var(--pos-card-bg); border-radius: var(--pos-radius);
        border: 1px solid var(--pos-border); box-shadow: var(--pos-shadow);
        padding: 12px; display: flex; flex-direction: column; height: 100%;
        overflow: hidden;
    }

    /* Product Search & Category Bar */
    .pos-search-box { position: relative; margin-bottom: 8px; flex: 0 0 auto; }
    .pos-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--pos-muted); font-size: 14px; pointer-events: none; }
    .pos-search-input { width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; border: 1px solid var(--pos-border); border-radius: 8px; font-size: .88rem; font-weight: 600; outline: none; transition: border-color .15s; }
    .pos-search-input:focus { border-color: var(--pos-primary); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }

    /* Category Filter Pills Header + Count */
    .cat-bar-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; flex: 0 0 auto; }
    .cat-pills { display: flex; gap: 6px; overflow-x: auto; padding-bottom: 6px; margin-bottom: 8px; flex: 0 0 auto; white-space: nowrap; }
    .cat-pill { padding: 4px 12px; border-radius: 20px; border: 1px solid var(--pos-border); background: #fff; font-size: .75rem; font-weight: 600; color: var(--pos-muted); cursor: pointer; transition: all .15s; user-select: none; }
    .cat-pill.active, .cat-pill:hover { background: var(--pos-primary); color: #fff; border-color: var(--pos-primary); }

    /* Product Cards Grid with Explicit Height & Visible Scrollbar */
    .pos-items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
        gap: 10px;
        flex: 1 1 auto;
        height: calc(100vh - 280px) !important;
        max-height: calc(100vh - 280px) !important;
        min-height: 340px !important;
        overflow-y: scroll !important; /* Force visible right-side scrollbar on product container */
        padding-right: 8px;
        align-content: start;
    }

    .product-card {
        background: #ffffff !important; border: 1px solid var(--pos-border) !important; border-radius: 10px !important;
        display: flex; flex-direction: column; justify-content: space-between; min-height: 125px;
        transition: all .15s ease; cursor: pointer; user-select: none; position: relative; padding: 10px 12px !important;
    }
    .product-card:hover { border-color: var(--pos-primary) !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(79,70,229,.12); }
    
    .product-card-title {
        font-weight: 700 !important; font-size: .84rem !important; color: var(--pos-text) !important;
        line-height: 1.25 !important; margin-bottom: 2px !important; font-family: inherit !important;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        min-height: 2.5em; word-break: break-word;
    }
    .product-card-code  { font-size: .70rem !important; color: var(--pos-muted) !important; font-family: monospace !important; }
    .variant-badge      { background: var(--pos-info-lt) !important; color: var(--pos-info) !important; border-radius: 4px !important; padding: 2px 6px !important; font-size: .68rem !important; font-weight: 700 !important; margin-top: 4px !important; display: inline-block !important; }
    .product-card-stock { font-size: .72rem !important; font-weight: 700 !important; background: var(--pos-primary-lt) !important; color: var(--pos-primary) !important; border-radius: 6px !important; padding: 4px 8px !important; margin-top: 6px !important; display: inline-block !important; width: fit-content !important; }

    /* Right Side — Adjustment Cart & Summary Panel */
    .pos-cart-panel {
        background: var(--pos-card-bg); border-radius: var(--pos-radius);
        border: 1px solid var(--pos-border); box-shadow: var(--pos-shadow);
        display: flex; flex-direction: column; height: 100%; overflow: hidden;
        transition: all 0.25s ease;
    }

    .cart-header { padding: 10px 14px; border-bottom: 1px solid var(--pos-border); background: #fff; display: flex; align-items: center; justify-content: space-between; flex: 0 0 auto; }
    .cart-header .cart-title { font-size: .88rem; font-weight: 700; color: var(--pos-text); margin: 0; display: flex; align-items: center; gap: 8px; }

    .cart-items-wrap {
        height: calc(100vh - 460px) !important;
        min-height: 160px !important;
        max-height: calc(100vh - 460px) !important;
        overflow-y: scroll !important;
        padding: 8px 12px;
        flex: 1 1 auto;
    }

    .cart-item-row {
        background: #fff; border: 1px solid var(--pos-border); border-radius: 8px;
        padding: 8px 10px; margin-bottom: 6px; display: flex; flex-direction: column; gap: 4px;
    }
    .cart-item-head { display: flex; align-items: center; justify-content: space-between; font-weight: 700; font-size: .80rem; }
    .cart-item-sub  { font-size: .70rem; color: var(--pos-muted); }
    .cart-flow      { font-size: .73rem; display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 4px 6px; border-radius: 6px; }

    /* Reason Box */
    .cart-reason-wrap { padding: 8px 12px; background: #fff; border-top: 1px solid var(--pos-border); flex: 0 0 auto; }

    /* POS Totals Box */
    .pos-totals-box { background: #1e293b; color: #fff; padding: 12px 14px; border-top: 1px solid var(--pos-border); flex: 0 0 auto; }
    .totals-row { display: flex; align-items: center; justify-content: space-between; font-size: .78rem; margin-bottom: 4px; color: #cbd5e1; }
    .totals-row.grand { font-size: 1.02rem; font-weight: 700; color: #38bdf8; border-top: 1px solid #334155; padding-top: 6px; margin-top: 4px; margin-bottom: 0; }

    .btn-submit-pos {
        background: #059669; color: #fff; border: none; border-radius: 9px;
        width: 100%; height: 42px; font-size: .92rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        cursor: pointer; transition: background .15s; margin-top: 8px;
    }
    .btn-submit-pos:hover { background: #047857; }
    .btn-submit-pos:disabled { background: #475569; opacity: .6; cursor: not-allowed; }

    /* Stepper controls */
    .qty-controls { display: flex; align-items: center; gap: 2px; }
    .qty-btn {
        width: 26px; height: 26px; border-radius: 5px; border: 1px solid var(--pos-border);
        background: #f8fafc; color: var(--pos-text); font-weight: 700; font-size: 12px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        user-select: none; transition: all .12s;
    }
    .qty-btn:active { transform: scale(0.92); }
    .qty-input { width: 42px; height: 26px; text-align: center; font-weight: 700; font-size: .78rem; border: 1px solid var(--pos-border); border-radius: 5px; outline: none; }

    /* ── COLLAPSED / FOOTER DOCK CART MODE ── */
    .pos-grid-layout.cart-collapsed {
        grid-template-columns: 1fr;
    }

    .pos-grid-layout.cart-collapsed .pos-cart-panel {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 1050;
        height: auto;
        max-height: 80vh;
        border-radius: 16px 16px 0 0;
        box-shadow: 0 -6px 24px rgba(0,0,0,0.25);
        border-top: 2px solid var(--pos-primary);
    }

    .pos-grid-layout.cart-collapsed .pos-cart-panel.dock-minimized {
        height: 52px !important;
        overflow: hidden !important;
    }

    .pos-grid-layout.cart-collapsed .pos-cart-panel.dock-minimized .cart-items-wrap,
    .pos-grid-layout.cart-collapsed .pos-cart-panel.dock-minimized .cart-reason-wrap,
    .pos-grid-layout.cart-collapsed .pos-cart-panel.dock-minimized .pos-totals-box {
        display: none !important;
    }

    /* Circle Arrow Toggle Buttons */
    .btn-circle-toggle {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--pos-primary-lt); color: var(--pos-primary);
        border: 1px solid var(--pos-border); display: inline-flex;
        align-items: center; justify-content: center; font-size: 14px;
        cursor: pointer; transition: all .2s ease;
    }
    .btn-circle-toggle:hover {
        background: var(--pos-primary); color: #fff; transform: scale(1.08);
    }

    /* Fixed Floating Footer Trigger Bar (Always available when collapsed) */
    .cart-dock-trigger-bar {
        display: none;
        position: fixed; bottom: 0; left: 0; right: 0;
        background: #1e293b; color: #fff;
        padding: 8px 20px; z-index: 1040;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.2);
        border-top: 2px solid var(--pos-primary);
        align-items: center; justify-content: space-between;
    }

    .pos-grid-layout.cart-collapsed + .cart-dock-trigger-bar {
        display: flex;
    }

    /* Mobile Responsive Layout */
    @media (max-width: 992px) {
        .pos-grid-layout { grid-template-columns: 1fr; max-height: none; overflow: visible; height: auto; }
        .pos-cart-panel { margin-top: 16px; height: auto; }
        .pos-items-grid { max-height: 480px !important; height: 480px !important; }
    }
</style>

<div class="pos-adj-page">
<div class="container-fluid px-3">

    {{-- Top Bar Header --}}
    <div class="pos-top-bar">
        <div class="title-box">
            <div class="title-icon"><i class="fas fa-cash-register"></i></div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Stock Adjustment Terminal</h5>
                <small class="text-muted">POS Sale Style Multi-Variant &amp; Piece Adjustment Screen</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            {{-- Target Warehouse Selector --}}
            <div class="d-flex align-items-center gap-2">
                <label class="fw-bold text-secondary small text-uppercase mb-0"><i class="fas fa-warehouse text-primary"></i> Warehouse:</label>
                <select id="terminal_warehouse_id" class="form-select form-select-sm fw-bold" style="width:200px; height:36px; border-radius:7px;">
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name ?? $wh->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Audit Log Link --}}
            <a href="{{ route('stock_adjustments.index') }}" class="btn btn-outline-secondary btn-sm fw-bold px-3" style="border-radius:7px; height:36px; display:inline-flex; align-items:center; gap:6px;">
                <i class="fas fa-history"></i> Audit Logs
            </a>
        </div>
    </div>

    {{-- Main POS 2-Column Grid Layout --}}
    <div id="posGridLayout" class="pos-grid-layout">

        {{-- LEFT SIDE: Product Cards Grid --}}
        <div class="pos-products-panel">
            
            {{-- Search Input --}}
            <div class="pos-search-box">
                <i class="fas fa-search pos-search-icon"></i>
                <input type="text" id="posProductSearch" class="pos-search-input" placeholder="Type Product Name, Code, or Scan Barcode to filter cards…">
            </div>

            {{-- Category Filter Pills Header + Count --}}
            <div class="cat-bar-header">
                <span class="text-uppercase fw-bold text-muted" style="font-size:.70rem; letter-spacing:0.5px;">Categories</span>
                <span id="productShowingCount" class="badge bg-light text-dark border font-monospace" style="font-size:.68rem;">
                    Showing {{ count($products) }} Products
                </span>
            </div>

            {{-- Category Filter Pills --}}
            <div class="cat-pills custom-scroll">
                <div class="cat-pill active" data-cat="">All Categories</div>
                @foreach($categories as $cat)
                    <div class="cat-pill" data-cat="{{ $cat->id }}">{{ $cat->name }}</div>
                @endforeach
            </div>

            {{-- Product POS Cards Grid with Prominent Right-Side Vertical Scrollbar --}}
            <div id="posItemsGrid" class="pos-items-grid">
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

                    <div class="product-card" 
                         data-id="{{ $p->id }}"
                         data-name="{{ $p->item_name }}"
                         data-code="{{ $p->item_code }}"
                         data-cat="{{ $p->category_id }}"
                         data-has-variants="{{ $hasVariants ? 1 : 0 }}"
                         data-variants="{{ json_encode($variants) }}"
                         data-search="{{ strtolower($p->item_name . ' ' . $p->item_code) }}">
                        <div>
                            <div class="product-card-title" title="{{ $p->item_name }}">{{ $p->item_name }}</div>
                            <div class="product-card-code">{{ $p->item_code }}</div>
                            
                            @if($hasVariants)
                                <div class="variant-badge">
                                    <i class="fa fa-tags me-1"></i> {{ count($variants) }} Variants (Size/Color)
                                </div>
                            @else
                                <div class="text-muted small mt-1" style="font-size:.70rem;">Standard Item</div>
                            @endif
                        </div>

                        <div class="product-card-stock">
                            <i class="fa fa-hand-pointer me-1"></i> Click to Adjust
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No Products Found Notice --}}
            <div id="noProductsNotice" class="text-center py-5 text-muted d-none">
                <i class="fas fa-box-open fa-3x mb-2" style="color:#cbd5e1;"></i>
                <p class="fw-bold mb-0">No matching products found</p>
                <small>Try adjusting your search query or category filter</small>
            </div>
        </div>

        {{-- RIGHT SIDE: Adjustment Cart & POS Totals Box --}}
        <div id="posCartPanel" class="pos-cart-panel">
            
            {{-- Cart Header --}}
            <div class="cart-header">
                <p class="cart-title">
                    <i class="fas fa-shopping-cart text-primary"></i> Staged Adjustments (<span id="cartCount">0</span>)
                </p>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="clearCartBtn" class="btn btn-link btn-sm text-danger text-decoration-none p-0 me-2" title="Clear all staged adjustments">
                        <i class="fas fa-trash-alt me-1"></i> Clear
                    </button>
                    {{-- Up/Down Toggle Button for Side/Bottom Mode --}}
                    <button type="button" id="toggleCartCollapseBtn" class="btn-circle-toggle" title="Toggle Dock / Full View">
                        <i class="fas fa-chevron-down" id="toggleCartIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Cart Items List --}}
            <div id="cartItemsWrap" class="cart-items-wrap custom-scroll">
                <div id="emptyCartNotice" class="text-center py-5 text-muted">
                    <i class="fas fa-cart-plus fa-3x mb-3" style="color:#cbd5e1;"></i>
                    <p class="mb-0 fw-semibold">No items staged for adjustment</p>
                    <small>Click on any product card on the left to select variants and adjust stock!</small>
                </div>
            </div>

            {{-- Reason / Remarks --}}
            <div class="cart-reason-wrap">
                <label class="fw-bold text-secondary small text-uppercase mb-1" style="font-size:.70rem;">Adjustment Reason / Remarks <span class="text-danger">*</span></label>
                <textarea id="terminal_reason" rows="2" class="form-control form-control-sm" style="border-radius:7px; font-size:.82rem;" placeholder="Specify reason (e.g., 'Wrong variant sold in POS', 'Physical recount', 'Damaged items')" required></textarea>
            </div>

            {{-- POS Totals Box --}}
            <div class="pos-totals-box">
                <div class="totals-row">
                    <span>Products Affected:</span>
                    <strong id="statProductsCount">0</strong>
                </div>
                <div class="totals-row">
                    <span>Variants Adjusted:</span>
                    <strong id="statItemsCount">0 Items</strong>
                </div>
                <div class="totals-row">
                    <span>Total Added (+):</span>
                    <strong class="text-success" id="statAddedQty">+0 Pcs</strong>
                </div>
                <div class="totals-row">
                    <span>Total Deducted (-):</span>
                    <strong class="text-danger" id="statDeductedQty">-0 Pcs</strong>
                </div>
                <div class="totals-row grand">
                    <span>Net Stock Difference:</span>
                    <span id="statNetDiff">0 Pcs</span>
                </div>

                <button type="button" id="submitTerminalBtn" class="btn-submit-pos" disabled>
                    <i class="fas fa-bolt"></i> Process Stock Adjustment (<span id="btnSubmitCount">0</span>)
                </button>
            </div>

        </div>

    </div>

    {{-- FLOATING BOTTOM DOCK TRIGGER BAR (Visible when collapsed into bottom bar) --}}
    <div id="cartDockTriggerBar" class="cart-dock-trigger-bar">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 fw-bold">
                <i class="fas fa-shopping-cart me-1"></i> Staged Adjustments (<span id="dockCartCount">0</span>)
            </span>
            <span class="text-white small font-monospace">
                Net Diff: <strong class="text-info" id="dockNetDiff">0 Pcs</strong>
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Click to Expand Cart</span>
            <button type="button" id="expandDockCartBtn" class="btn-circle-toggle bg-primary text-white border-0" title="Expand Cart Panel">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </div>

</div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     SELECT VARIANT POPUP MODAL (Matching POS Sale Modal)
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="variantsModal" tabindex="-1" aria-labelledby="variantsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
            
            <div class="modal-header border-bottom bg-white px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="variantsModalLabel">
                        <i class="fas fa-tags text-primary me-2"></i>Select Variant to Adjust
                    </h5>
                    <small class="text-muted">Set action (+ / - / =) and quantity for each variant, then click Add</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Real-time Variant Filter Search inside Modal --}}
            <div class="px-4 py-2 bg-light border-bottom">
                <input type="text" id="variantModalSearch" class="form-control form-control-sm" placeholder="Filter variants by name, size, or color..." style="border-radius:6px; font-size:.82rem;">
            </div>

            <div class="modal-body p-0 custom-scroll" style="max-height: 55vh; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size:.85rem;">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-4">Variant Description</th>
                                <th class="text-center">Current Stock</th>
                                <th class="text-center">Action Type</th>
                                <th class="text-center">Adjustment Qty</th>
                                <th class="text-center pe-4">Add to Cart</th>
                            </tr>
                        </thead>
                        <tbody id="variantsModalList">
                            {{-- Dynamically populated --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-white py-2 px-4 border-top">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
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
    let totalProductCards = $('.product-card').length;
    let isCartCollapsed = false;
    let currentStockScope = 'all'; // 'all' or 'in_stock'
    let previousWhId = $('#terminal_warehouse_id').val();

    // ── 1. Load Live Warehouse Stocks for Cards ──
    function loadWarehouseStocks(warehouseId) {
        $('.product-wh-stock-badge').html('<i class="fas fa-spinner fa-spin me-1"></i> Checking...');

        $.ajax({
            url: "{{ route('stock_adjustments.warehouse_products') }}",
            type: "GET",
            data: { warehouse_id: warehouseId },
            success: function (res) {
                if (res.success && res.stocks) {
                    $.each(res.stocks, function (pid, st) {
                        let card = $('.product-card[data-id="' + pid + '"]');
                        card.data('in-stock', st.in_stock ? 1 : 0);
                        card.data('pieces', st.total_pieces);

                        let badge = $('#card_stock_' + pid);
                        if (st.in_stock) {
                            badge.removeClass('bg-light text-muted')
                                 .addClass('bg-primary-lt text-primary fw-bold')
                                 .html('<i class="fas fa-boxes me-1"></i> ' + st.formatted.full);
                        } else {
                            badge.removeClass('bg-primary-lt text-primary fw-bold')
                                 .addClass('bg-light text-muted')
                                 .html('<i class="fas fa-box-open me-1"></i> 0 In Stock');
                        }
                    });
                    filterGrid();
                }
            },
            error: function () {
                $('.product-wh-stock-badge').html('<i class="fas fa-box me-1"></i> Adjust Stock');
            }
        });
    }

    // Initial load on page ready
    loadWarehouseStocks(previousWhId);

    // ── 2. Warehouse Change Handler ──
    $('#terminal_warehouse_id').on('change', function () {
        let newWhId = $(this).val();
        if (cart.length > 0) {
            Swal.fire({
                title: 'Warehouse Badlein?',
                text: 'Warehouse badalne se cart mein mojood staged items clear ho jayenge. Kya aap aagay barhna chahte hain?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Haan, Clear & Change',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    renderCart();
                    previousWhId = newWhId;
                    loadWarehouseStocks(newWhId);
                } else {
                    $('#terminal_warehouse_id').val(previousWhId);
                }
            });
        } else {
            previousWhId = newWhId;
            loadWarehouseStocks(newWhId);
        }
    });

    // ── 3. Stock Scope Toggle (All Items vs In-Stock Only) ──
    $('.stock-scope-btn').on('click', function () {
        $('.stock-scope-btn').removeClass('active');
        $(this).addClass('active');
        currentStockScope = $(this).data('scope');
        filterGrid();
    });

    // ── 4. Toggle Collapse / Bottom Dock Cart Mode ──
    $('#toggleCartCollapseBtn, #expandDockCartBtn').on('click', function () {
        isCartCollapsed = !isCartCollapsed;
        let layout = $('#posGridLayout');
        let icon   = $('#toggleCartIcon');

        if (isCartCollapsed) {
            layout.addClass('cart-collapsed');
            $('#posCartPanel').addClass('dock-minimized');
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        } else {
            layout.removeClass('cart-collapsed');
            $('#posCartPanel').removeClass('dock-minimized');
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    });

    // ── 5. Category & Search Filters ──
    $('.cat-pill').on('click', function () {
        $('.cat-pill').removeClass('active');
        $(this).addClass('active');
        filterGrid();
    });

    $('#posProductSearch').on('input', function () {
        filterGrid();
    });

    function filterGrid() {
        let cat    = $('.cat-pill.active').data('cat');
        let search = $.trim($('#posProductSearch').val()).toLowerCase();
        let visibleCount = 0;

        $('.product-card').each(function () {
            let card    = $(this);
            let itemCat = card.data('cat');
            let itemSearch = card.data('search');
            let inStock = card.data('in-stock') == 1;

            let matchCat   = !cat || itemCat == cat;
            let matchSearch= !search || itemSearch.indexOf(search) > -1;
            let matchStock = (currentStockScope === 'all') || inStock;

            if (matchCat && matchSearch && matchStock) {
                card.removeClass('d-none');
                visibleCount++;
            } else {
                card.addClass('d-none');
            }
        });

        $('#productShowingCount').text('Showing ' + visibleCount + ' of ' + totalProductCards + ' Products');

        if (visibleCount === 0) {
            $('#noProductsNotice').removeClass('d-none');
            $('#posItemsGrid').addClass('d-none');
        } else {
            $('#noProductsNotice').addClass('d-none');
            $('#posItemsGrid').removeClass('d-none');
        }
    }

    // Filter variants inside modal
    $('#variantModalSearch').on('input', function () {
        let query = $.trim($(this).val()).toLowerCase();
        $('#variantsModalList tr').each(function () {
            let rowText = $(this).text().toLowerCase();
            if (!query || rowText.indexOf(query) > -1) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });

    // ── 6. Click on Product Card: Opens Modal with Live Carton & Variant Stock ──
    $('.product-card').on('click', function () {
        let card        = $(this);
        let productId   = card.data('id');
        let productName = card.data('name');
        let productCode = card.data('code');
        let warehouseId = $('#terminal_warehouse_id').val();
        let whName      = $('#terminal_warehouse_id option:selected').text();

        $('#variantModalSearch').val('');
        $('#variantsModalLabel').html('<i class="fas fa-boxes text-primary me-2"></i>' + productName + ' <span class="text-muted fw-normal">(' + productCode + ') &bull; ' + whName + '</span>');
        let tbody = $('#variantsModalList');
        tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading live warehouse stock &amp; carton packaging...</td></tr>');
        $('#variantsModal').modal('show');

        $.ajax({
            url: "/stock-adjustments/product-variants/" + productId,
            type: "GET",
            data: { warehouse_id: warehouseId },
            success: function (res) {
                tbody.empty();

                if (res.variants && res.variants.length > 0) {
                    $.each(res.variants, function (i, v) {
                        let fmt = v.formatted || {};
                        let isCarton = fmt.is_carton ? 1 : 0;
                        let ppb = fmt.ppb || 1;
                        let unitLabel = fmt.unit_label || 'Pcs';
                        let mainFmt = fmt.main || (v.current_stock + ' Pcs');
                        let subFmt = fmt.sub ? `(${fmt.sub})` : '';

                        let row = `
                        <tr data-pid="${productId}" 
                            data-pname="${productName}" 
                            data-pcode="${productCode}" 
                            data-vkey="${v.variant_key}" 
                            data-vname="${v.name}" 
                            data-stock="${v.current_stock}" 
                            data-is-carton="${isCarton}" 
                            data-ppb="${ppb}" 
                            data-unit-label="${unitLabel}" 
                            data-formatted-stock="${mainFmt}">
                            
                            <td class="ps-4 fw-bold">
                                <div>${v.name}</div>
                                <div class="text-muted small font-monospace">
                                    ${v.size !== '-' || v.color !== '-' ? 'Size: ' + v.size + ' | Color: ' + v.color : (isCarton ? subFmt : 'Standard Item')}
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-primary px-2 py-1" style="font-size:.82rem;">
                                    ${mainFmt}
                                </span>
                                ${isCarton ? `<div class="text-muted font-monospace mt-1" style="font-size:.70rem;">(${v.current_stock} Pcs)</div>` : ''}
                            </td>

                            <td class="text-center">
                                <select class="form-select form-select-sm modal-action-type mx-auto" style="width:115px; font-weight:600; font-size:.78rem;">
                                    <option value="add">🟢 Add (+)</option>
                                    <option value="subtract">🔴 Deduct (-)</option>
                                    <option value="set">🔵 Set (=)</option>
                                </select>
                            </td>

                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <div class="qty-controls">
                                        <button type="button" class="qty-btn modal-qty-minus">-</button>
                                        <input type="number" step="any" min="0.01" value="1" class="qty-input modal-qty-val">
                                        <button type="button" class="qty-btn modal-qty-plus">+</button>
                                    </div>
                                    ${isCarton ? `
                                    <select class="form-select form-select-sm modal-unit-select" style="width:85px; font-size:.75rem; font-weight:700;">
                                        <option value="carton" selected>${unitLabel}</option>
                                        <option value="piece">Pcs</option>
                                    </select>` : `<span class="badge bg-light text-dark border px-2 py-1" style="font-size:.75rem;">Pcs</span>`}
                                </div>
                                ${isCarton ? `<div class="text-muted small text-center mt-1 modal-qty-hint" style="font-size:.68rem;">= <strong>${ppb}</strong> Pcs</div>` : ''}
                            </td>

                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-primary btn-sm px-3 modal-add-btn fw-bold">
                                    <i class="fas fa-plus me-1"></i> Add
                                </button>
                            </td>
                        </tr>`;
                        tbody.append(row);
                    });
                } else {
                    tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No stock or variants found for this product in this warehouse.</td></tr>');
                }
            },
            error: function () {
                tbody.html('<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load variant details.</td></tr>');
            }
        });
    });

    // Modal Stepper + / - Click Handlers
    $(document).on('click', '.modal-qty-plus', function () {
        let input = $(this).siblings('.modal-qty-val');
        let val = parseFloat(input.val()) || 0;
        input.val(val + 1).trigger('input');
    });

    $(document).on('click', '.modal-qty-minus', function () {
        let input = $(this).siblings('.modal-qty-val');
        let val = parseFloat(input.val()) || 0;
        if (val > 1) input.val(val - 1).trigger('input');
    });

    // Live update of converted pieces hint in modal
    $(document).on('input change', '.modal-qty-val, .modal-unit-select', function () {
        let row = $(this).closest('tr');
        let isCarton = row.data('is-carton') == 1;
        if (isCarton) {
            let val  = parseFloat(row.find('.modal-qty-val').val()) || 0;
            let unit = row.find('.modal-unit-select').val();
            let ppb  = parseFloat(row.data('ppb')) || 1;
            let lbl  = row.data('unit-label') || 'Ctn';
            let hint = row.find('.modal-qty-hint');

            if (unit === 'carton') {
                hint.html('= <strong>' + (val * ppb) + '</strong> Pcs');
            } else {
                let ctns = (val / ppb).toFixed(2);
                hint.html('= <strong>' + ctns + '</strong> ' + lbl);
            }
        }
    });

    // ── 7. Add to Cart from Modal ──
    $(document).on('click', '.modal-add-btn', function () {
        let row          = $(this).closest('tr');
        let pid          = row.data('pid');
        let pname        = row.data('pname');
        let pcode        = row.data('pcode');
        let vkey         = row.data('vkey');
        let vname        = row.data('vname');
        let curStock     = parseFloat(row.data('stock')) || 0;
        let isCarton     = row.data('is-carton') == 1;
        let ppb          = parseFloat(row.data('ppb')) || 1;
        let unitLabel    = row.data('unit-label') || 'Pcs';
        let formattedStock = row.data('formatted-stock') || (curStock + ' Pcs');
        let type         = row.find('.modal-action-type').val();
        let inputVal     = parseFloat(row.find('.modal-qty-val').val()) || 0;
        let unit         = isCarton ? (row.find('.modal-unit-select').val() || 'carton') : 'piece';

        if (inputVal <= 0) {
            Swal.fire('Invalid Qty', 'Please enter quantity > 0.', 'warning');
            return;
        }

        // Calculate pieces for backend processing
        let piecesQty = (isCarton && unit === 'carton') ? (inputVal * ppb) : inputVal;
        let displayQty = (isCarton && unit === 'carton')
            ? (inputVal + ' ' + unitLabel + ' (' + piecesQty + ' Pcs)')
            : (piecesQty + ' Pcs');

        addItemToCart({
            cartKey:             pid + '_' + vkey,
            product_id:          pid,
            product_name:        pname,
            product_code:        pcode,
            variant_key:         vkey,
            variant_name:        vname,
            type:                type,
            qty:                 piecesQty,
            display_qty:         displayQty,
            unit_label:          unitLabel,
            ppb:                 ppb,
            is_carton:           isCarton,
            cur_stock_formatted: formattedStock,
            cur_stock:           curStock
        });

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: (vname || pname) + ' added to adjustment cart!',
            showConfirmButton: false,
            timer: 1200
        });
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

    // Helper: format pieces into Carton + Pieces for Cart Display
    function formatStockDisplay(pieces, item) {
        if (item.is_carton && item.ppb > 1) {
            let ctns = Math.floor(pieces / item.ppb);
            let loose = Math.round(pieces - (ctns * item.ppb));
            if (ctns > 0 && loose > 0) return `${ctns} ${item.unit_label} + ${loose} Pcs`;
            if (ctns > 0) return `${ctns} ${item.unit_label}`;
            return `${loose} Pcs`;
        }
        return pieces + ' Pcs';
    }

    // ── 8. Render Cart Items & Summary ──
    function renderCart() {
        let container = $('#cartItemsWrap');
        container.empty();

        if (cart.length === 0) {
            container.append(`
            <div id="emptyCartNotice" class="text-center py-5 text-muted">
                <i class="fas fa-cart-plus fa-3x mb-3" style="color:#cbd5e1;"></i>
                <p class="mb-0 fw-semibold">No items staged for adjustment</p>
                <small>Click on any product card on the left to select variants and adjust stock!</small>
            </div>`);

            $('#cartCount, #dockCartCount, #statItemsCount, #btnSubmitCount').text('0');
            $('#statProductsCount').text('0');
            $('#statAddedQty').text('+0 Pcs');
            $('#statDeductedQty').text('-0 Pcs');
            $('#statNetDiff, #dockNetDiff').text('0 Pcs');
            $('#submitTerminalBtn').prop('disabled', true);
            return;
        }

        let totalAdded = 0;
        let totalDeducted = 0;
        let uniqueProducts = new Set();

        $.each(cart, function (index, item) {
            uniqueProducts.add(item.product_id);

            let expStockPieces = item.cur_stock;
            let typeBadge = '';
            if (item.type === 'add') {
                typeBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle">+ Add</span>';
                expStockPieces = item.cur_stock + item.qty;
                totalAdded += item.qty;
            } else if (item.type === 'subtract') {
                typeBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">- Deduct</span>';
                expStockPieces = Math.max(0, item.cur_stock - item.qty);
                totalDeducted += item.qty;
            } else {
                typeBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle text-dark border-info">= Set</span>';
                expStockPieces = Math.max(0, item.qty);
                let diff = expStockPieces - item.cur_stock;
                if (diff > 0) totalAdded += diff;
                else totalDeducted += Math.abs(diff);
            }

            let expStockFormatted = formatStockDisplay(expStockPieces, item);

            let rowHtml = `
            <div class="cart-item-row">
                <div class="cart-item-head">
                    <div>
                        <span>${item.variant_name || item.product_name}</span>
                        <div class="cart-item-sub">${item.product_code}</div>
                    </div>
                    <button type="button" class="btn btn-link btn-sm text-danger remove-cart-item p-0" data-key="${item.cartKey}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <div class="cart-flow">
                    <div>${typeBadge} <strong>${item.display_qty}</strong></div>
                    <div>Stock: <span class="text-muted">${item.cur_stock_formatted}</span> &rarr; <strong class="text-success">${expStockFormatted}</strong></div>
                </div>
            </div>`;

            container.append(rowHtml);
        });

        let netDiff = totalAdded - totalDeducted;
        let netStr  = (netDiff > 0 ? '+' : '') + netDiff.toLocaleString() + ' Pcs';

        $('#cartCount, #dockCartCount, #statItemsCount, #btnSubmitCount').text(cart.length);
        $('#statProductsCount').text(uniqueProducts.size);
        $('#statAddedQty').text('+' + totalAdded.toLocaleString() + ' Pcs');
        $('#statDeductedQty').text('-' + totalDeducted.toLocaleString() + ' Pcs');
        $('#statNetDiff, #dockNetDiff').text(netStr);

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
        cart = [];
        renderCart();
    });

    // ── 9. Submit Terminal Adjustment AJAX ──
    $('#submitTerminalBtn').on('click', function () {
        let warehouseId  = $('#terminal_warehouse_id').val();
        let globalReason = $('#terminal_reason').val();

        if (cart.length === 0) {
            Swal.fire('Empty Cart', 'Please add at least one item to adjust.', 'warning');
            return;
        }

        if (!globalReason) {
            Swal.fire('Reason Required', 'Please specify a reason/remarks for this stock adjustment.', 'warning');
            $('#terminal_reason').focus();
            return;
        }

        let btn = $(this);
        let origHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Processing Adjustment...').prop('disabled', true);

        $.ajax({
            url: "{{ route('stock_adjustments.store_batch') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                warehouse_id: warehouseId,
                global_reason: globalReason,
                items: cart
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Stock Adjustment Processed!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('stock_adjustments.index') }}";
                    });
                } else {
                    Swal.fire('Error', res.message || 'Failed to process adjustment.', 'error');
                    btn.html(origHtml).prop('disabled', false);
                }
            },
            error: function (xhr) {
                btn.html(origHtml).prop('disabled', false);
                let msg = xhr.responseJSON?.message || 'Server error occurred.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

});
</script>
@endsection