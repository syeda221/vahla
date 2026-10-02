@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --pos-border: #cbd5e1;
        --pos-border-focus: #3b82f6;
        --pos-primary: #dc2626; /* Danger/Red theme for returns */
        --pos-primary-hover: #b91c1c;
        --pos-text-main: #1e293b;
        --pos-radius: 8px;
    }

    .card-panel {
        background: #ffffff !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    }

    .meta-label {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin-bottom: 2px !important;
        display: block !important;
    }

    /* Grid Table */
    .table-responsive {
        border: 1px solid var(--pos-border) !important;
        border-radius: 6px !important;
        overflow-x: auto !important;
        background-color: #ffffff;
    }

    .sales-table {
        border-collapse: collapse !important;
        width: 100%;
        margin-bottom: 0 !important;
        min-width: 960px;
    }

    .sales-table thead th {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 6px !important;
        border: 1px solid var(--pos-border) !important;
        border-bottom: 2px solid #cbd5e1 !important;
        vertical-align: middle !important;
        text-align: center;
        white-space: nowrap;
    }

    .sales-table tbody td {
        border: 1px solid var(--pos-border) !important;
        padding: 0 !important;
        vertical-align: middle !important;
        background-color: #ffffff;
    }

    .sales-table tbody tr:hover td {
        background-color: #f8fafc !important;
    }

    .sales-table tbody .form-control,
    .sales-table tbody .form-select {
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        height: 32px !important;
        margin: 0 !important;
        padding: 2px 6px !important;
        width: 100% !important;
        background-color: transparent !important;
        font-size: 0.8rem !important;
        color: var(--pos-text-main) !important;
    }

    .sales-table tbody .form-control:focus,
    .sales-table tbody .form-select:focus {
        outline: none !important;
        background-color: #fef2f2 !important;
        box-shadow: inset 0 0 0 1.5px var(--pos-primary) !important;
    }

    .sales-table tbody .input-readonly {
        background-color: #f8fafc !important;
        color: #475569 !important;
        cursor: not-allowed !important;
    }

    /* Column Width Restraints */
    .sales-table thead th.col-product,
    .sales-table tbody td.col-product {
        width: 230px !important;
        min-width: 190px !important;
        max-width: 260px !important;
        text-align: left !important;
        padding-left: 8px !important;
    }

    .sales-table tbody td.col-product .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }

    .sales-table tbody td.col-product .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
        max-width: 230px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
    }

    .sales-table thead th.col-code,
    .sales-table tbody td.col-code {
        width: 110px !important;
        min-width: 105px !important;
        max-width: 120px !important;
        text-align: center !important;
    }

    .sales-table tbody td.col-code input.item-code-display {
        width: 100% !important;
        min-width: 100px !important;
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.3px !important;
        text-align: center !important;
        padding: 2px 4px !important;
        background-color: #f8fafc !important;
        color: #334155 !important;
    }

    .sales-table thead th.col-stock,
    .sales-table tbody td.col-stock {
        width: 80px !important;
        min-width: 75px !important;
    }

    .sales-table tbody td.col-stock input.stock {
        font-size: 0.75rem !important;
        padding: 2px 4px !important;
    }

    .btn-del-row {
        width: 26px;
        height: 26px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecdd3;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-del-row:hover {
        background: #ef4444;
        color: #ffffff;
    }

    /* Summary Card Styling */
    .summary-card {
        background: #ffffff !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        padding: 14px 16px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px dashed #f1f5f9;
        font-size: 0.82rem;
    }
    .summary-row:last-child {
        border-bottom: none;
    }
    .summary-val-net {
        font-weight: 800;
        color: #dc2626;
        font-size: 1.25rem;
    }
</style>

<div class="container-fluid py-2 px-2">
    <div class="main-container bg-white border mx-auto p-3 rounded-3 shadow-sm">

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="directReturnForm" action="{{ route('sale.return.store_direct') }}" method="POST" autocomplete="off">
            @csrf

            {{-- TOP HEADER BAR --}}
            <div class="d-flex justify-content-between align-items-center mb-3 px-1 border-bottom pb-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('sale.return.index') }}" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Back to Sale Returns">
                        <i class="fas fa-arrow-left text-secondary"></i>
                    </a>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.1rem;">
                            <i class="fas fa-undo-alt text-danger"></i> Create Sale Return (نئی سیل ریٹرن)
                        </h5>
                        <small class="text-muted" style="font-size: 0.72rem;">Return items to warehouse & credit customer ledger balance</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-semibold fs-6">
                        <i class="fas fa-box-open me-1"></i> Return Mode: Direct (Standalone)
                    </span>
                    <button type="submit" class="btn btn-danger px-3 py-1 fw-bold d-flex align-items-center gap-1 shadow-sm" style="font-size: 0.82rem;">
                        <i class="fas fa-check-circle"></i> Save & Post Return
                    </button>
                </div>
            </div>

            {{-- TOP INFORMATION PANEL --}}
            <div class="card-panel p-3 mb-3">
                <div class="row g-2 align-items-end">
                    <!-- Return Invoice No -->
                    <div class="col-sm-6 col-md-3 col-lg-2">
                        <label class="meta-label"><i class="fas fa-receipt text-danger"></i> Return No.</label>
                        <input type="text" class="form-control text-center fw-bold bg-light" name="invoice_no" id="inputInvoiceNo" value="{{ $nextInvoice }}" readonly style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem;">
                    </div>

                    <!-- Return Date -->
                    <div class="col-sm-6 col-md-3 col-lg-2">
                        <label class="meta-label"><i class="far fa-calendar-alt text-danger"></i> Return Date</label>
                        <input type="date" name="return_date" class="form-control text-center fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Customer Selection -->
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <label class="meta-label"><i class="fas fa-user text-danger"></i> Select Customer <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="customerSelect" name="customer_id" required style="width: 100%;">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->customer_id ? $cust->customer_id . ' — ' : '' }}{{ $cust->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Warehouse Selection -->
                    <div class="col-sm-6 col-md-3 col-lg-2">
                        <label class="meta-label"><i class="fas fa-warehouse text-danger"></i> Warehouse <span class="text-danger">*</span></label>
                        <select class="form-select fw-semibold" name="warehouse_id" id="warehouseSelect" required>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ (auth()->user()->warehouse_id ?? 1) == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Return Reason / Remarks -->
                    <div class="col-sm-12 col-md-12 col-lg-3">
                        <label class="meta-label"><i class="far fa-comment-dots text-muted"></i> Return Reason / Remarks</label>
                        <input type="text" class="form-control" name="return_reason" id="returnReason" placeholder="e.g. Defective / Returned by customer">
                    </div>
                </div>
            </div>

            {{-- 2-COLUMN ERP LAYOUT --}}
            <div class="row g-3 align-items-stretch">
                <!-- LEFT MAIN AREA: Items Grid Table (col-lg-8 col-xl-9) -->
                <div class="col-lg-8 col-xl-9">
                    <div class="card-panel d-flex flex-column h-100 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-6">
                                    <i class="fas fa-boxes text-danger me-1"></i> Return Items
                                </span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0" style="font-size:0.7rem;" id="itemsRowCount">1</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger btn-sm py-1 px-3 rounded-2 fw-bold d-flex align-items-center gap-1 shadow-sm" id="btnAddRow" style="font-size:0.75rem;">
                                    <i class="fas fa-plus"></i> Add Row
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive flex-grow-1">
                            <table class="table table-bordered sales-table mb-0" id="returnTable">
                                <thead>
                                    <tr>
                                        <th style="width:30px;" class="text-center">#</th>
                                        <th class="col-product" style="width: 230px; min-width: 190px; max-width: 260px;">PRODUCT</th>
                                        <th class="col-code" style="width: 110px; min-width: 105px;">CODE</th>
                                        <th class="col-stock" style="width: 80px; min-width: 75px;">STOCK</th>
                                        <th class="col-qty" style="width: 85px;">QTY</th>
                                        <th class="col-unit" style="width: 65px;">UNIT</th>
                                        <th class="col-price" style="width: 105px;">RATE</th>
                                        <th class="col-disc" style="width: 80px;">DISC</th>
                                        <th class="col-amount" style="width: 95px;">AMOUNT</th>
                                        <th class="col-action" style="width: 35px;">×</th>
                                    </tr>
                                </thead>
                                <tbody id="returnTableBody">
                                    <tr>
                                        <!-- # ROW INDEX -->
                                        <td class="text-center fw-bold text-muted row-index" style="vertical-align:middle; font-size:0.75rem;">1</td>

                                        <!-- PRODUCT -->
                                        <td class="col-product" style="width: 230px; min-width: 190px; max-width: 260px;">
                                            <select class="form-select product-select" style="width:100%" required>
                                                <option value=""></option>
                                            </select>
                                            <input type="hidden" class="product-id-hidden" name="product_id[]">
                                            <input type="hidden" class="variant-data-hidden" name="color[]">
                                            <input type="hidden" class="size-mode-hidden">
                                            <input type="hidden" class="pack-qty-hidden" value="1">
                                        </td>

                                        <!-- ITEM CODE -->
                                        <td class="col-code" style="width: 110px; min-width: 105px;">
                                            <input type="text" class="form-control item-code-display text-center input-readonly" readonly placeholder="Code" tabindex="-1" style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; font-weight: 600;">
                                        </td>

                                        <!-- STOCK -->
                                        <td class="col-stock" style="width: 80px; min-width: 75px;">
                                            <input type="text" class="form-control stock text-center input-readonly" readonly tabindex="-1" placeholder="0" style="font-size: 0.75rem;">
                                        </td>

                                        <!-- QTY -->
                                        <td class="col-qty" style="width: 85px;">
                                            <input type="number" step="any" min="0.001" class="form-control return-qty text-center fw-bold" name="qty[]" placeholder="1" value="1" required style="padding-left: 4px; padding-right: 4px;">
                                        </td>

                                        <!-- UNIT -->
                                        <td class="col-unit" style="width: 65px; text-align: center;">
                                            <input type="text" class="form-control unit-display text-center input-readonly" name="unit[]" value="Pcs" readonly tabindex="-1" style="font-size: 0.75rem; font-weight: 600;">
                                        </td>

                                        <!-- PRICE -->
                                        <td class="col-price" style="width: 105px;">
                                            <input type="number" step="any" min="0" class="form-control return-price text-end fw-semibold" name="price[]" placeholder="0" value="0" required>
                                        </td>

                                        <!-- DISCOUNT -->
                                        <td class="col-disc" style="width: 80px;">
                                            <input type="number" step="any" min="0" class="form-control return-disc text-end" name="item_disc[]" placeholder="0" value="0">
                                        </td>

                                        <!-- AMOUNT -->
                                        <td class="col-amount" style="width: 95px;">
                                            <input type="text" class="form-control return-amount text-end input-readonly fw-bold text-danger" readonly value="0.00" tabindex="-1">
                                        </td>

                                        <!-- ACTION -->
                                        <td class="col-action text-center" style="width: 35px;">
                                            <button type="button" class="btn-del-row" title="Remove">&times;</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2 text-muted small d-flex align-items-center gap-1">
                            <i class="fas fa-info-circle text-primary"></i>
                            <span>Aap ek se zyada products bhi <b>Add Row</b> button daba kar is return mein shamil kar sakti hain.</span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR: SUMMARY & REFUND OPTIONS (col-lg-4 col-xl-3) -->
                <div class="col-lg-4 col-xl-3">
                    <div class="summary-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fas fa-calculator text-danger me-1"></i> Return Summary
                            </h6>

                            <div class="summary-row">
                                <span class="text-muted">Total Line Items:</span>
                                <span class="fw-bold text-dark" id="summaryTotalItems">1</span>
                            </div>

                            <div class="summary-row">
                                <span class="text-muted">Total Quantity:</span>
                                <span class="fw-bold text-dark" id="summaryTotalQty">1</span>
                            </div>

                            <div class="summary-row">
                                <span class="text-muted">Gross Amount:</span>
                                <span class="fw-semibold text-dark" id="summaryGrossAmount">0.00</span>
                            </div>

                            <div class="summary-row">
                                <span class="text-muted">Line Discounts:</span>
                                <span class="fw-semibold text-danger" id="summaryLineDiscount">0.00</span>
                            </div>

                            <div class="summary-row">
                                <span class="text-muted">Extra Discount:</span>
                                <input type="number" step="any" min="0" class="form-control form-control-sm text-end fw-semibold" style="width: 90px; height: 26px;" name="extra_discount" id="extraDiscount" value="0">
                            </div>

                            <div class="summary-row mt-2 pt-2 border-top">
                                <span class="fw-bold text-dark fs-6">Net Return Total:</span>
                                <span class="summary-val-net" id="summaryNetAmount">0.00</span>
                            </div>

                            <!-- Customer Ledger Credit Adjustment Notice -->
                            <div class="mt-3 p-2 bg-success-subtle rounded-2 border border-success-subtle text-success-emphasis">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-file-invoice-dollar fs-5"></i>
                                    <span class="fw-bold small">Customer Ledger Credit (کھاتے میں کمی)</span>
                                </div>
                                <small class="d-block text-secondary" style="font-size: 0.73rem; line-height: 1.35;">
                                    Return post hote hi yeh amount customer ke ledger account mein <b>Credit</b> ho jayegi aur unka baqaya balance utna <b>minus (kam)</b> ho jayega.
                                </small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnSubmitReturn" style="font-size: 0.95rem;">
                                <i class="fas fa-check-circle"></i> Save & Post Return
                            </button>
                            <a href="{{ route('sale.return.index') }}" class="btn btn-light border w-100 mt-2 py-1 text-muted small fw-semibold">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();


    // Select2 Product Search Initializer
    function initProductSelect2($el) {
        $el.select2({
            placeholder: 'Search Product (Name / SKU / Barcode)',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("products.ajax.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(repo) {
                if (repo.loading) return repo.text;
                let stock = repo.stock !== undefined ? repo.stock : 0;
                let sku = repo.sku || repo.item_code || 'N/A';
                return $(`
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <div>
                            <div class="fw-bold">${repo.name || repo.text}</div>
                            <small class="text-muted">Code: ${sku}</small>
                        </div>
                        <div>
                            <span class="badge bg-secondary rounded-pill">Stock: ${stock}</span>
                        </div>
                    </div>
                `);
            },
            templateSelection: function(repo) {
                return repo.name || repo.text;
            }
        });
    }

    // Initialize initial row
    initProductSelect2($('#returnTableBody .product-select'));

    // Handle product selection
    $('#returnTableBody').on('select2:select', '.product-select', function(e) {
        const data = e.params.data;
        if (!data || !data.id) return;

        const $row = $(this).closest('tr');
        const pid = data.id.toString().split('|')[0];

        $row.find('.product-id-hidden').val(pid);
        $row.find('.variant-data-hidden').val(data.variant_data || '');
        $row.find('.item-code-display').val(data.sku || data.item_code || '');
        $row.find('.stock').val(data.stock !== undefined ? data.stock : 0);
        $row.find('.size-mode-hidden').val(data.size_mode || 'by_pieces');
        $row.find('.pack-qty-hidden').val(data.pieces_per_box || 1);

        // Set Unit display: prioritize variant unit_name, then parse variant_data, then fallback to size_mode
        let unit = data.unit_name || '';
        if (!unit && data.variant_data) {
            try {
                let vd = JSON.parse(atob(data.variant_data));
                unit = vd.unit || '';
            } catch(e) {}
        }
        if (!unit) {
            if (data.size_mode === 'by_cartons') unit = 'Ctn';
            else if (data.size_mode === 'by_kg') unit = 'Kg';
            else if (data.size_mode === 'by_feet') unit = 'Ft';
            else if (data.size_mode === 'by_meter') unit = 'Mtr';
            else unit = 'Pcs';
        }
        if (unit.length > 0) {
            unit = unit.charAt(0).toUpperCase() + unit.slice(1);
        }
        $row.find('.unit-display').val(unit);

        // Fetch last sold rate for this customer, or fallback to retail price
        const customerId = $('#customerSelect').val();
        let defaultRate = data.retail_price || data.trade_price || 0;
        $row.find('.return-price').val(defaultRate);

        if (customerId && pid) {
            $.get('{{ route("get-price") }}', {
                product_id: pid,
                customer_id: customerId,
                _t: new Date().getTime()
            }).done(function(res) {
                if (res && res.price && parseFloat(res.price) > 0) {
                    $row.find('.return-price').val(parseFloat(res.price));
                }
                computeRow($row);
            }).fail(function() {
                computeRow($row);
            });
        } else {
            computeRow($row);
        }
    });

    // Compute single row total
    function computeRow($row) {
        const qty = parseFloat($row.find('.return-qty').val()) || 0;
        const price = parseFloat($row.find('.return-price').val()) || 0;
        const disc = parseFloat($row.find('.return-disc').val()) || 0;

        const sub = Math.max(0, (qty * price) - disc);
        $row.find('.return-amount').val(sub.toFixed(2));

        computeTotals();
    }

    // Compute grand totals
    function computeTotals() {
        let totalQty = 0;
        let grossAmount = 0;
        let lineDiscounts = 0;
        let itemsCount = 0;

        $('#returnTableBody tr').each(function() {
            const $r = $(this);
            const pId = $r.find('.product-id-hidden').val();
            if (pId) itemsCount++;

            const q = parseFloat($r.find('.return-qty').val()) || 0;
            const p = parseFloat($r.find('.return-price').val()) || 0;
            const d = parseFloat($r.find('.return-disc').val()) || 0;

            totalQty += q;
            grossAmount += (q * p);
            lineDiscounts += d;
        });

        const extraDisc = parseFloat($('#extraDiscount').val()) || 0;
        const netAmount = Math.max(0, grossAmount - lineDiscounts - extraDisc);

        $('#itemsRowCount').text(itemsCount || 1);
        $('#summaryTotalItems').text(itemsCount);
        $('#summaryTotalQty').text(totalQty);
        $('#summaryGrossAmount').text(grossAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summaryLineDiscount').text(lineDiscounts.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summaryNetAmount').text(netAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

    }

    // Input listeners for calculations
    $('#returnTableBody').on('input', '.return-qty, .return-price, .return-disc', function() {
        computeRow($(this).closest('tr'));
    });

    $('#extraDiscount').on('input', function() {
        computeTotals();
    });

    // Add Row functionality
    $('#btnAddRow').on('click', function() {
        const rowCount = $('#returnTableBody tr').length + 1;
        const rowHtml = `
            <tr>
                <td class="text-center fw-bold text-muted row-index" style="vertical-align:middle; font-size:0.75rem;">${rowCount}</td>
                <td class="col-product" style="width: 230px; min-width: 190px; max-width: 260px;">
                    <select class="form-select product-select" style="width:100%" required>
                        <option value=""></option>
                    </select>
                    <input type="hidden" class="product-id-hidden" name="product_id[]">
                    <input type="hidden" class="variant-data-hidden" name="color[]">
                    <input type="hidden" class="size-mode-hidden">
                    <input type="hidden" class="pack-qty-hidden" value="1">
                </td>
                <td class="col-code" style="width: 110px; min-width: 105px;">
                    <input type="text" class="form-control item-code-display text-center input-readonly" readonly placeholder="Code" tabindex="-1" style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; font-weight: 600;">
                </td>
                <td class="col-stock" style="width: 80px; min-width: 75px;">
                    <input type="text" class="form-control stock text-center input-readonly" readonly tabindex="-1" placeholder="0" style="font-size: 0.75rem;">
                </td>
                <td class="col-qty" style="width: 85px;">
                    <input type="number" step="any" min="0.001" class="form-control return-qty text-center fw-bold" name="qty[]" placeholder="1" value="1" required style="padding-left: 4px; padding-right: 4px;">
                </td>
                <td class="col-unit" style="width: 65px; text-align: center;">
                    <input type="text" class="form-control unit-display text-center input-readonly" name="unit[]" value="Pcs" readonly tabindex="-1" style="font-size: 0.75rem; font-weight: 600;">
                </td>
                <td class="col-price" style="width: 105px;">
                    <input type="number" step="any" min="0" class="form-control return-price text-end fw-semibold" name="price[]" placeholder="0" value="0" required>
                </td>
                <td class="col-disc" style="width: 80px;">
                    <input type="number" step="any" min="0" class="form-control return-disc text-end" name="item_disc[]" placeholder="0" value="0">
                </td>
                <td class="col-amount" style="width: 95px;">
                    <input type="text" class="form-control return-amount text-end input-readonly fw-bold text-danger" readonly value="0.00" tabindex="-1">
                </td>
                <td class="col-action text-center" style="width: 35px;">
                    <button type="button" class="btn-del-row" title="Remove">&times;</button>
                </td>
            </tr>
        `;

        const $newRow = $(rowHtml);
        $('#returnTableBody').append($newRow);
        initProductSelect2($newRow.find('.product-select'));
        updateIndexes();
    });

    // Delete Row functionality
    $('#returnTableBody').on('click', '.btn-del-row', function() {
        if ($('#returnTableBody tr').length > 1) {
            $(this).closest('tr').remove();
            updateIndexes();
            computeTotals();
        } else {
            // Reset the only row
            const $r = $(this).closest('tr');
            $r.find('.product-select').val(null).trigger('change');
            $r.find('.product-id-hidden').val('');
            $r.find('.item-code-display').val('');
            $r.find('.stock').val('0');
            $r.find('.return-qty').val(1);
            $r.find('.return-price').val(0);
            $r.find('.return-disc').val(0);
            $r.find('.return-amount').val('0.00');
            computeTotals();
        }
    });

    function updateIndexes() {
        $('#returnTableBody tr').each(function(index) {
            $(this).find('.row-index').text(index + 1);
        });
    }

    // Form submit validation & confirmation
    $('#directReturnForm').on('submit', function(e) {
        if (!this.checkValidity()) {
            return;
        }

        e.preventDefault();

        // Validate at least one valid product
        let validProduct = false;
        $('#returnTableBody tr').each(function() {
            const pid = $(this).find('.product-id-hidden').val();
            const q = parseFloat($(this).find('.return-qty').val()) || 0;
            if (pid && q > 0) validProduct = true;
        });

        if (!validProduct) {
            Swal.fire({
                icon: 'warning',
                title: 'No Product Selected',
                text: 'Barah-e-karam kam az kam ek product aur uski return quantity lazmi enter karein!'
            });
            return;
        }

        const net = $('#summaryNetAmount').text();
        const customerName = $('#customerSelect option:selected').text();

        Swal.fire({
            title: 'Confirm Sale Return?',
            html: `Aap <b>${customerName}</b> ke liye <b>Rs. ${net}</b> ka Sale Return post karne lage hain.<br><br><span class="text-danger small">Is se product stock godam mein barh jayega aur customer ke ledger se yeh raqam minus ho jayegi.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Yes, Post Return',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form natively
                this.submit();
            }
        });
    });
});
</script>
@endpush
