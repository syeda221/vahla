@extends('admin_panel.layout.app')

@section('content')
<style>
    .transfer-card {
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color) !important;
        background: var(--bg-card);
        box-shadow: var(--shadow-card);
    }
    .step-section {
        padding: var(--space-20) var(--space-24);
        border-bottom: 1px solid var(--border-color);
    }
    .step-section:last-child {
        border-bottom: none;
    }
    .step-num-badge {
        width: 28px;
        height: 28px;
        border-radius: var(--radius-pill);
        background: var(--primary-soft);
        color: var(--primary);
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: var(--space-12);
        flex-shrink: 0;
        border: 1px solid #C7D2FE;
    }
    .step-title {
        font-size: var(--font-card-title);
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    .step-subtitle {
        font-size: var(--font-caption);
        font-weight: 400;
        color: var(--text-secondary);
        margin-left: var(--space-8);
    }
    .form-field-label {
        font-size: var(--font-caption);
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
        display: block;
    }
    .transfer-items-table th {
        font-size: var(--font-table-header);
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 12px;
        border: none !important;
        background: var(--bg-page);
        border-bottom: 1px solid var(--border-color) !important;
        vertical-align: middle;
        white-space: nowrap;
    }
    .transfer-items-table td {
        padding: 10px 10px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card);
    }
    .readonly-stock-field {
        background-color: var(--bg-page) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
        font-weight: 600 !important;
        font-variant-numeric: tabular-nums;
        font-size: 13px !important;
        cursor: not-allowed;
    }
    .btn-add-item-dashed {
        border: 1.5px dashed var(--border-color) !important;
        border-radius: var(--radius-md);
        background: var(--bg-card);
        color: var(--text-primary);
        font-weight: 600;
        font-size: 13px;
        padding: 12px 16px;
        width: 100%;
        display: block;
        text-align: center;
        transition: all var(--transition);
        cursor: pointer;
    }
    .btn-add-item-dashed:hover {
        border-color: var(--primary) !important;
        background: var(--primary-soft);
        color: var(--primary);
    }
    .dest-type-card {
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        padding: 0 var(--space-16);
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        color: var(--text-primary);
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        user-select: none;
        height: var(--height-control);
    }
    .dest-type-card:hover {
        border-color: var(--border-hover);
        background: var(--bg-page);
    }
    .dest-type-card.active {
        border-color: var(--primary) !important;
        background: var(--primary-soft) !important;
        color: var(--primary) !important;
        box-shadow: 0 0 0 1px var(--primary);
    }
    .btn-trash-item {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all var(--transition);
        cursor: pointer;
    }
    .btn-trash-item:hover {
        background: var(--danger-soft);
        border-color: var(--danger);
        color: var(--danger);
    }
    .qty-addon-label {
        background: var(--bg-page);
        font-weight: 600;
        font-size: 12px;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 0 10px !important;
        height: 40px !important;
        display: inline-flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }
    .badge-stock-in {
        background: #DCFCE7 !important;
        color: #15803D !important;
        border: 1px solid #BBF7D0 !important;
        font-weight: 600 !important;
    }
    .badge-stock-out {
        background: #FEE2E2 !important;
        color: #B91C1C !important;
        border: 1px solid #FECACA !important;
        font-weight: 600 !important;
    }
    .badge-stock-none {
        background: #F3F4F6 !important;
        color: #6B7280 !important;
        border: 1px solid #E5E7EB !important;
        font-weight: 500 !important;
    }
    .row-index-badge {
        width: 24px;
        height: 24px;
        border-radius: var(--radius-pill);
        background: var(--bg-page);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    /* Select2 High-Precision ERP Styling */
    .select2-container--default .select2-selection--single {
        height: 40px !important;
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-md) !important;
        background-color: var(--bg-card) !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px !important;
        color: var(--text-primary) !important;
        font-size: 13px !important;
        font-weight: 500 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-md) !important;
        box-shadow: var(--shadow-dropdown) !important;
        background-color: var(--bg-card) !important;
        z-index: 9999 !important;
    }
    .select2-search--dropdown {
        padding: 8px !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-sm) !important;
        padding: 6px 10px !important;
        font-size: 13px !important;
    }
    .select2-results__option {
        padding: 8px 12px !important;
        font-size: 13px !important;
        border-bottom: 1px solid #F1F5F9;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary-soft) !important;
        color: var(--primary) !important;
    }
    .select2-results__option--highlighted[aria-selected] strong,
    .select2-results__option--highlighted[aria-selected] span {
        color: var(--primary) !important;
    }
</style>

<div class="erp-container py-4">
    <!-- Top Breadcrumb & Title -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('warehouse_stocks.index') }}" class="text-decoration-none erp-caption fw-semibold">
                    <i class="fas fa-warehouse me-1"></i> Warehouse Stocks
                </a>
                <span class="text-muted small">/</span>
                <a href="{{ route('stock_transfers.index') }}" class="text-decoration-none erp-caption fw-semibold">
                    Stock Transfers
                </a>
                <span class="text-muted small">/</span>
                <span class="erp-caption fw-semibold text-primary">New Transfer</span>
            </div>
            <h1 class="erp-page-title mb-1">Inter-Warehouse Stock Transfer</h1>
            <p class="erp-body-text mb-0">Move inventory between bulk storage godowns and retail branch shops with instant stock verification</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('stock_transfers.index') }}" class="erp-btn-secondary">
                <i class="fas fa-list"></i> Transfers List
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--danger) !important;">
            <i class="fas fa-exclamation-triangle me-3 text-danger fs-5"></i>
            <div>
                <strong>Transfer Notice:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--success) !important;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('stock_transfers.store') }}" method="POST" id="stockTransferForm">
        @csrf
        <input type="hidden" name="transfer_no" value="{{ $transferNo }}">

        <div class="card erp-card border-0 mb-4">
            <!-- SECTION 1: TRANSFER DETAILS -->
            <div class="step-section">
                <div class="d-flex align-items-center mb-3">
                    <span class="step-num-badge">1</span>
                    <span class="step-title">Transfer Details</span>
                    <span class="step-subtitle">Dispatch date &amp; tracking reference</span>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-field-label">Transfer Reference #</label>
                        <input type="text" class="form-control erp-input readonly-stock-field font-monospace text-start ps-3" value="{{ $transferNo }}" readonly>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-field-label">Transfer Date <span class="text-danger">*</span></label>
                        <input type="date" name="transfer_date" class="form-control erp-input font-tabular" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-field-label">Created By</label>
                        <input type="text" class="form-control erp-input readonly-stock-field text-start ps-3" value="{{ auth()->user()->name ?? 'Administrator' }}" readonly>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: ITEMS TO TRANSFER -->
            <div class="step-section">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center">
                        <span class="step-num-badge">2</span>
                        <span class="step-title">Items to Transfer</span>
                        <span class="step-subtitle">Search by Item Code (e.g. 0001) or Name — choose source — transfer</span>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px;">
                        <i class="fas fa-search me-1"></i> Live Code &amp; Name Search
                    </span>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table erp-table transfer-items-table mb-0" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 4%;" class="text-center">#</th>
                                <th style="width: 38%;">PRODUCT / ITEM (NAME OR CODE)</th>
                                <th style="width: 26%;">TRANSFER FROM (SOURCE)</th>
                                <th style="width: 15%;" class="text-center">AVAILABLE STOCK</th>
                                <th style="width: 14%;" class="text-center">TRANSFER QTY</th>
                                <th style="width: 3%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Initial Row 0 -->
                            <tr class="item-transfer-row" data-row="0" id="row_0">
                                <td class="text-center">
                                    <span class="row-index-badge font-tabular fw-bold text-muted">1</span>
                                </td>
                                <td>
                                    <select name="items[0][product_id]" class="form-control product-select" id="prod_select_0" required style="width: 100%;">
                                        <option value="">Search product by item code (e.g. 0001) or name...</option>
                                    </select>
                                    <div class="product-meta mt-1 px-1 d-flex align-items-center justify-content-between" id="prod_meta_0" style="display: none !important; font-size: 11px;">
                                        <span class="text-muted"><i class="fas fa-cubes me-1 text-primary"></i>All Warehouses: <strong class="total-stock-text text-dark font-tabular">—</strong></span>
                                    </div>
                                </td>
                                <td>
                                    <select name="items[0][from_warehouse_id]" class="form-select erp-input source-warehouse-select" id="source_select_0" required style="height: 40px; font-size: 13px;">
                                        <option value="">Select source...</option>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" data-is-shop="{{ $w->is_shop }}">
                                                {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <div class="avail-badge-wrap" id="avail_badge_0">
                                        <span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;">— Select Source —</span>
                                    </div>
                                    <input type="hidden" class="available-qty-val" id="avail_qty_0" value="0">
                                    <input type="hidden" class="unit-name-val" id="unit_val_0" value="Pcs">
                                </td>
                                <td>
                                    <div class="input-group" style="min-width: 125px; flex-wrap: nowrap;">
                                        <input type="number" step="any" min="0.001" name="items[0][quantity]" class="form-control erp-input transfer-qty-input text-end font-tabular fw-bold px-2" id="qty_input_0" style="height: 40px; font-size: 13px; border-right: 0 !important;" placeholder="0.00" required>
                                        <span class="input-group-text qty-addon-label px-2 unit-label-0" style="font-size: 12px;">Qty</span>
                                    </div>
                                    <div class="qty-error-msg small text-danger text-end mt-1 font-tabular" id="qty_err_0" style="display:none; font-size: 11px; font-weight: 600;"></div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn-trash-item btn-remove-row" title="Remove this item">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Add Another Item Dashed Button -->
                <button type="button" class="btn-add-item-dashed mb-2" id="btnAddRow">
                    <i class="fas fa-plus-circle me-1.5 text-primary"></i> Add Another Item
                </button>
            </div>

            <!-- SECTION 3: TRANSFER DESTINATION -->
            <div class="step-section">
                <div class="d-flex align-items-center mb-3">
                    <span class="step-num-badge">3</span>
                    <span class="step-title">Transfer Destination</span>
                    <span class="step-subtitle">Where should these inventory items be delivered?</span>
                </div>

                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <label class="form-field-label">
                            Destination Type <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            <label class="dest-type-card active" id="cardDestWarehouse">
                                <input type="radio" name="destination_type" value="warehouse" checked class="form-check-input">
                                <span>📦 Warehouse (Godown)</span>
                            </label>
                            <label class="dest-type-card" id="cardDestShop">
                                <input type="radio" name="destination_type" value="shop" class="form-check-input">
                                <span>🏪 Branch Shop</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">
                        <label class="form-field-label">
                            Destination Location <span class="text-danger">*</span>
                        </label>
                        <select name="to_warehouse_id" id="to_warehouse_id" class="form-select erp-input" style="height: 40px; font-size: 13px;" required>
                            <option value="">— Select Destination Warehouse —</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" 
                                        data-is-shop="{{ $wh->is_shop }}"
                                        class="{{ $wh->is_shop ? 'shop-opt d-none' : 'godown-opt' }}">
                                    {{ $wh->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $wh->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: REMARKS / NOTES -->
            <div class="step-section">
                <div class="d-flex align-items-center mb-3">
                    <span class="step-num-badge">4</span>
                    <span class="step-title">Remarks / Notes</span>
                    <span class="step-subtitle">Internal notes, driver details, or dispatch instructions</span>
                </div>

                <textarea name="remarks" class="form-control erp-input" rows="3" style="height: auto !important; min-height: 80px;" placeholder="Transfer reason, dispatch instructions, or any internal notes..."></textarea>
            </div>

            <!-- SUBMIT & SUMMARY FOOTER -->
            <div class="p-4 bg-light rounded-bottom d-flex align-items-center justify-content-between flex-wrap gap-3" style="border-top: 1px solid var(--border-color);">
                <div class="d-flex align-items-center gap-3">
                    <div class="px-3 py-2 rounded erp-card bg-white border">
                        <span class="erp-caption fw-semibold text-uppercase text-muted">Items Count:</span>
                        <strong class="text-dark ms-1.5 font-tabular" id="summaryItemsCount">1 Item</strong>
                    </div>
                    <div class="px-3 py-2 rounded erp-card bg-white border">
                        <span class="erp-caption fw-semibold text-uppercase text-muted">Total Transfer Qty:</span>
                        <strong class="text-primary ms-1.5 font-tabular fs-6" id="summaryTotalQty">0.00</strong>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('stock_transfers.index') }}" class="erp-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" id="btnSubmitTransfer" class="erp-btn-primary px-4 py-2">
                        <i class="fas fa-exchange-alt me-1.5"></i> Transfer Stock Now
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        let rowCounter = 1;

        // Warehouse template options for fresh rows
        const warehouseOptions = `
            <option value="">Select source...</option>
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}" data-is-shop="{{ $w->is_shop }}">
                    {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                </option>
            @endforeach
        `;

        // Initialize AJAX Select2 for Product Search (Supports Item Code 0001, ITEM-0001, Barcode & Name)
        function initProductSelect2(selectElement) {
            const $select = $(selectElement);

            $select.select2({
                placeholder: 'Search product by item code (e.g. 0001) or name...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('stock_transfers.search_products') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term || '' };
                    },
                    processResults: function(data) {
                        return {
                            results: Array.isArray(data) ? data : (data.results || [])
                        };
                    },
                    cache: true
                },
                templateResult: function(item) {
                    if (!item.id || item.loading) return item.text;

                    const stockBadge = (item.total_stock > 0)
                        ? `<span class="badge" style="background: #DCFCE7; color: #15803D; border: 1px solid #BBF7D0; font-size: 11px; padding: 4px 8px;">Stock: ${item.stock_text}</span>`
                        : `<span class="badge" style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; font-size: 11px; padding: 4px 8px;">0 In Stock</span>`;

                    return $(`
                        <div class="d-flex align-items-center justify-content-between py-1">
                            <div>
                                <span class="badge" style="background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; font-weight: 700; font-family: monospace; font-size: 11px; padding: 3px 6px;">
                                    ${item.item_code || 'ITEM'}
                                </span>
                                <strong class="ms-1.5 text-dark" style="font-size: 13px;">${item.item_name || item.text}</strong>
                                <span class="text-muted small ms-1">(${item.unit_name || ''})</span>
                            </div>
                            <div class="ms-2 flex-shrink-0">
                                ${stockBadge}
                            </div>
                        </div>
                    `);
                },
                templateSelection: function(item) {
                    if (!item.id) return item.text;
                    if (item.item_code && item.item_name) {
                        return `[${item.item_code}] — ${item.item_name} [${item.unit_name || ''}]`;
                    }
                    return item.text;
                }
            });

            // Handle Product Selection
            $select.on('select2:select', function(e) {
                const item = e.params.data;
                const $row = $select.closest('.item-transfer-row');
                const rowIdx = $row.data('row');

                // Update Unit Display
                const unitName = item.unit_name || 'Pcs';
                $row.find(`.unit-label-${rowIdx}`).text(unitName);
                $row.find(`#unit_val_${rowIdx}`).val(unitName);

                // Show total stock bar
                if (item.stock_text) {
                    $row.find(`#prod_meta_${rowIdx}`).attr('style', 'display: flex !important; font-size: 11px;')
                        .find('.total-stock-text').text(item.stock_text);
                }

                // Load source warehouses with available stock
                loadProductSources($row, item.id);
            });

            // Handle Clear
            $select.on('select2:clear', function() {
                const $row = $select.closest('.item-transfer-row');
                const rowIdx = $row.data('row');

                $row.find(`.unit-label-${rowIdx}`).text('Qty');
                $row.find(`#unit_val_${rowIdx}`).val('Pcs');
                $row.find(`#prod_meta_${rowIdx}`).attr('style', 'display: none !important;');
                $row.find('.source-warehouse-select').html(warehouseOptions);
                $row.find(`#avail_badge_${rowIdx}`).html('<span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;">— Select Source —</span>');
                $row.find(`#avail_qty_${rowIdx}`).val('0');
                $row.find('.transfer-qty-input').val('');
                $row.find('.qty-error-msg').hide();
                $row.find('.transfer-qty-input').removeClass('is-invalid');
                updateSummary();
            });
        }

        // Initialize on row 0
        initProductSelect2('#prod_select_0');

        // Function: Load sources for selected product
        function loadProductSources($row, productId) {
            const rowIdx = $row.data('row');
            const $sourceSelect = $row.find('.source-warehouse-select');
            const $availBadge = $row.find(`#avail_badge_${rowIdx}`);
            const $availQtyInput = $row.find(`#avail_qty_${rowIdx}`);
            const $qtyInput = $row.find('.transfer-qty-input');

            $sourceSelect.html('<option value="">Loading locations...</option>').prop('disabled', true);
            $availBadge.html('<span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;"><i class="fas fa-spinner fa-spin me-1"></i> Checking...</span>');

            $.ajax({
                url: "{{ url('/stock-transfers/product-sources') }}/" + productId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $sourceSelect.prop('disabled', false).empty();
                    $sourceSelect.append('<option value="">Select source...</option>');

                    let firstStockWhId = null;
                    const unitName = response.unit_name || 'Pcs';

                    if (response.sources && response.sources.length > 0) {
                        response.sources.forEach(function(src) {
                            const icon = src.is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ';
                            const stockLabel = src.has_stock ? `(Avail: ${src.formatted_qty} ${src.unit_name})` : '(Out of Stock)';
                            
                            $sourceSelect.append(`
                                <option value="${src.warehouse_id}" 
                                        data-avail="${src.available_pieces}" 
                                        data-unit="${src.unit_name}" 
                                        ${src.has_stock ? '' : 'style="color: #94A3B8;"'}>
                                    ${icon} ${src.warehouse_name} ${stockLabel}
                                </option>
                            `);

                            if (src.has_stock && !firstStockWhId) {
                                firstStockWhId = src.warehouse_id;
                            }
                        });
                    }

                    // Auto-select first warehouse with positive stock
                    if (firstStockWhId) {
                        $sourceSelect.val(firstStockWhId).trigger('change');
                    } else {
                        $availBadge.html('<span class="badge badge-stock-out px-2.5 py-2 font-tabular" style="font-size: 12px;"><i class="fas fa-exclamation-circle me-1"></i> Out of Stock (0 ' + unitName + ')</span>');
                        $availQtyInput.val(0);
                    }
                },
                error: function() {
                    $sourceSelect.prop('disabled', false).html(warehouseOptions);
                    $availBadge.html('<span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;">Error Loading</span>');
                    $availQtyInput.val(0);
                }
            });
        }

        // On Source Warehouse Change
        $(document).on('change', '.source-warehouse-select', function() {
            const $row = $(this).closest('.item-transfer-row');
            const rowIdx = $row.data('row');
            const $selectedOption = $(this).find('option:selected');
            const availPieces = parseFloat($selectedOption.data('avail')) || 0;
            const unitName = $selectedOption.data('unit') || $row.find(`#unit_val_${rowIdx}`).val() || 'Pcs';
            const $availBadge = $row.find(`#avail_badge_${rowIdx}`);
            const $availQtyInput = $row.find(`#avail_qty_${rowIdx}`);

            if ($(this).val()) {
                $availQtyInput.val(availPieces);
                if (availPieces > 0) {
                    $availBadge.html(`
                        <span class="badge badge-stock-in px-2.5 py-2 font-tabular" style="font-size: 12px;">
                            <i class="fas fa-check-circle me-1"></i> ${availPieces} ${unitName}
                        </span>
                    `);
                } else {
                    $availBadge.html(`
                        <span class="badge badge-stock-out px-2.5 py-2 font-tabular" style="font-size: 12px;">
                            <i class="fas fa-times-circle me-1"></i> 0 ${unitName} (Out of Stock)
                        </span>
                    `);
                }
            } else {
                $availBadge.html('<span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;">— Select Source —</span>');
                $availQtyInput.val(0);
            }

            validateRowQty($row);
            updateSummary();
        });

        // Validate Row Quantity
        function validateRowQty($row) {
            const rowIdx = $row.data('row');
            const $qtyInput = $row.find('.transfer-qty-input');
            const $errorDiv = $row.find(`#qty_err_${rowIdx}`);
            const enteredQty = parseFloat($qtyInput.val()) || 0;
            const availQty = parseFloat($row.find(`#avail_qty_${rowIdx}`).val()) || 0;
            const sourceWh = $row.find('.source-warehouse-select').val();
            const unitName = $row.find(`#unit_val_${rowIdx}`).val() || 'Pcs';

            if (enteredQty <= 0) {
                $qtyInput.removeClass('is-invalid');
                $errorDiv.hide();
                return true;
            }

            if (sourceWh && enteredQty > availQty) {
                $qtyInput.addClass('is-invalid');
                $errorDiv.text(`Exceeds Available: Max ${availQty} ${unitName}`).show();
                return false;
            } else {
                $qtyInput.removeClass('is-invalid');
                $errorDiv.hide();
                return true;
            }
        }

        $(document).on('input', '.transfer-qty-input', function() {
            validateRowQty($(this).closest('.item-transfer-row'));
            updateSummary();
        });

        // Destination Type Toggle (Warehouse vs Shop)
        $('input[name="destination_type"]').on('change', function() {
            const selectedType = $(this).val();

            if (selectedType === 'warehouse') {
                $('#cardDestWarehouse').addClass('active');
                $('#cardDestShop').removeClass('active');
                $('#to_warehouse_id option.godown-opt').removeClass('d-none');
                $('#to_warehouse_id option.shop-opt').addClass('d-none');
                $('#to_warehouse_id').val('');
            } else {
                $('#cardDestShop').addClass('active');
                $('#cardDestWarehouse').removeClass('active');
                $('#to_warehouse_id option.shop-opt').removeClass('d-none');
                $('#to_warehouse_id option.godown-opt').addClass('d-none');
                
                const firstShop = $('#to_warehouse_id option.shop-opt:first').val();
                if (firstShop) {
                    $('#to_warehouse_id').val(firstShop);
                }
            }
        });
        $('input[name="destination_type"]:checked').trigger('change');

        // Add Another Item Row
        $('#btnAddRow').on('click', function() {
            const newIndex = rowCounter++;

            const newRowHtml = `
                <tr class="item-transfer-row" data-row="${newIndex}" id="row_${newIndex}">
                    <td class="text-center">
                        <span class="row-index-badge font-tabular fw-bold text-muted">${newIndex + 1}</span>
                    </td>
                    <td>
                        <select name="items[${newIndex}][product_id]" class="form-control product-select" id="prod_select_${newIndex}" required style="width: 100%;">
                            <option value="">Search product by item code (e.g. 0001) or name...</option>
                        </select>
                        <div class="product-meta mt-1 px-1 d-flex align-items-center justify-content-between" id="prod_meta_${newIndex}" style="display: none !important; font-size: 11px;">
                            <span class="text-muted"><i class="fas fa-cubes me-1 text-primary"></i>All Warehouses: <strong class="total-stock-text text-dark font-tabular">—</strong></span>
                        </div>
                    </td>
                    <td>
                        <select name="items[${newIndex}][from_warehouse_id]" class="form-select erp-input source-warehouse-select" id="source_select_${newIndex}" required style="height: 40px; font-size: 13px;">
                            ${warehouseOptions}
                        </select>
                    </td>
                    <td class="text-center">
                        <div class="avail-badge-wrap" id="avail_badge_${newIndex}">
                            <span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;">— Select Source —</span>
                        </div>
                        <input type="hidden" class="available-qty-val" id="avail_qty_${newIndex}" value="0">
                        <input type="hidden" class="unit-name-val" id="unit_val_${newIndex}" value="Pcs">
                    </td>
                    <td>
                        <div class="input-group" style="min-width: 125px; flex-wrap: nowrap;">
                            <input type="number" step="any" min="0.001" name="items[${newIndex}][quantity]" class="form-control erp-input transfer-qty-input text-end font-tabular fw-bold px-2" id="qty_input_${newIndex}" style="height: 40px; font-size: 13px; border-right: 0 !important;" placeholder="0.00" required>
                            <span class="input-group-text qty-addon-label px-2 unit-label-${newIndex}" style="font-size: 12px;">Qty</span>
                        </div>
                        <div class="qty-error-msg small text-danger text-end mt-1 font-tabular" id="qty_err_${newIndex}" style="display:none; font-size: 11px; font-weight: 600;"></div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn-trash-item btn-remove-row" title="Remove this item">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;

            const $newRow = $(newRowHtml);
            $('#itemsTableBody').append($newRow);
            initProductSelect2(`#prod_select_${newIndex}`);
            reNumberRows();
            updateSummary();
        });

        // Remove Row
        $(document).on('click', '.btn-remove-row', function() {
            if ($('.item-transfer-row').length > 1) {
                $(this).closest('.item-transfer-row').remove();
                reNumberRows();
            } else {
                const $row = $(this).closest('.item-transfer-row');
                const rowIdx = $row.data('row');
                $row.find('.product-select').val(null).trigger('change');
                $row.find('.source-warehouse-select').html(warehouseOptions);
                $row.find(`#avail_badge_${rowIdx}`).html('<span class="badge badge-stock-none px-2.5 py-2 font-tabular" style="font-size: 12px;">— Select Source —</span>');
                $row.find(`#avail_qty_${rowIdx}`).val('0');
                $row.find('.transfer-qty-input').val('');
                $row.find('.qty-error-msg').hide();
                $row.find('.transfer-qty-input').removeClass('is-invalid');
                $row.find(`.unit-label-${rowIdx}`).text('Qty');
            }
            updateSummary();
        });

        // Renumber table rows sequentially
        function reNumberRows() {
            $('.item-transfer-row').each(function(idx) {
                $(this).find('.row-index-badge').text(idx + 1);
            });
        }

        // Update Total Summary Counters
        function updateSummary() {
            const rowCount = $('.item-transfer-row').length;
            let totalQty = 0;

            $('.transfer-qty-input').each(function() {
                totalQty += (parseFloat($(this).val()) || 0);
            });

            $('#summaryItemsCount').text(`${rowCount} Item${rowCount > 1 ? 's' : ''}`);
            $('#summaryTotalQty').text(totalQty.toFixed(2));
        }

        // Form Submit Validation
        $('#stockTransferForm').on('submit', function(e) {
            const destWh = $('#to_warehouse_id').val();
            let hasError = false;
            let errorMessage = '';

            if (!destWh) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Destination Missing',
                    text: 'Please select a destination warehouse or branch shop location.'
                });
                return false;
            }

            $('.item-transfer-row').each(function(i) {
                const rowIdx = $(this).data('row');
                const srcWh = $(this).find('.source-warehouse-select').val();
                const pId   = $(this).find('.product-select').val();
                const pName = $(this).find('.product-select option:selected').text().trim() || `Item #${i+1}`;
                const qty   = parseFloat($(this).find('.transfer-qty-input').val()) || 0;
                const avail = parseFloat($(this).find(`#avail_qty_${rowIdx}`).val()) || 0;

                if (!pId) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Please select a product to transfer.`;
                    return false;
                }

                if (!srcWh) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Please select a source location for ${pName}.`;
                    return false;
                }

                if (srcWh == destWh) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Source location and Destination location cannot be the same!`;
                    return false;
                }

                if (qty <= 0) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Please enter a transfer quantity greater than 0.`;
                    return false;
                }

                if (qty > avail) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Transfer quantity (${qty}) exceeds available stock (${avail}) in source location.`;
                    return false;
                }
            });

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage
                });
                return false;
            }

            $('#btnSubmitTransfer').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1.5"></i> Processing Transfer...');
        });
    });
</script>
@endsection
