@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Zero horizontal scroll guarantee */
    html, body {
        max-width: 100vw;
        overflow-x: hidden;
    }
    .os-create-page {
        background: #f8fafc;
        min-height: calc(100vh - 80px);
        padding: 24px 0 60px;
        max-width: 100%;
        overflow-x: hidden;
    }
    .os-create-page .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
    }
    .os-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .os-header-bar {
        padding: 16px 24px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .unified-table-header th {
        background: #1e3a5f !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        padding: 12px 14px !important;
        vertical-align: middle !important;
        border: none !important;
        letter-spacing: 0.3px;
    }
    .prod-entry-row td {
        vertical-align: top;
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .prod-entry-row:hover td {
        background-color: #fafbfc;
    }
    .loc-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 8px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .loc-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .loc-unit-badge {
        color: #0284c7 !important;
        background-color: #f0f9ff !important;
        border: 1px solid #bae6fd !important;
        font-weight: 700 !important;
        font-size: 0.80rem !important;
        height: 32px;
        padding: 0 8px;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        user-select: none;
    }
    .btn-add-location {
        color: #0284c7;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        padding: 3px 6px;
        border-radius: 6px;
        transition: background-color 0.15s ease;
    }
    .btn-add-location:hover {
        background-color: #f0f9ff;
        color: #0369a1;
        text-decoration: none;
    }
    .del-product-row-btn {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .del-product-row-btn:hover {
        background: #fca5a5;
        color: #b91c1c;
    }
    .total-qty-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        text-align: center;
        height: 38px;
        line-height: 26px;
    }
    /* Select2 Tweaks for Table rows */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 4px 6px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        font-size: 0.86rem !important;
        font-weight: 600 !important;
        color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    /* Responsive Styling for Mobile */
    @media (max-width: 991.98px) {
        .os-create-page {
            padding: 14px 0 40px;
        }
        .os-header-bar {
            padding: 14px 16px;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .os-header-bar a.btn {
            width: 100%;
            justify-content: center;
        }
        .table-responsive {
            overflow-x: visible !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        #unifiedStockTable,
        #unifiedStockTable thead,
        #unifiedStockTable tbody,
        #unifiedStockTable tr.prod-entry-row,
        #unifiedStockTable tr.prod-entry-row > td {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }
        #unifiedStockTable thead {
            display: none !important;
        }
        #unifiedStockTable tr.prod-entry-row {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 14px !important;
            margin-bottom: 16px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        }
        #unifiedStockTable tr.prod-entry-row > td {
            padding: 6px 0 !important;
            border: none !important;
            text-align: left !important;
        }
        #unifiedStockTable tr.prod-entry-row > td.td-desktop-action {
            display: none !important;
        }
        .loc-card .input-group {
            max-width: 100% !important;
        }
        .w-100-mobile {
            width: 100% !important;
        }
        .footer-controls-wrap {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .footer-controls-wrap > div {
            width: 100% !important;
        }
        .footer-controls-wrap button, 
        .footer-controls-wrap .btn {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

<div class="os-create-page">
    <div class="container-fluid px-2 px-md-4">
        
        <!-- Main Card -->
        <div class="os-card mb-4">
            <!-- Header bar -->
            <div class="os-header-bar">
                <div>
                    <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-boxes-packing text-primary"></i> Add Opening Stock — Unified Terminal
                    </h5>
                    <p class="text-muted small mb-0">
                        Record opening stock across single or multiple warehouses for one or more products with dynamic Carton, Bandal & Pcs units.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('opening_stock.index') }}" class="btn btn-outline-secondary fw-semibold px-3 py-2 d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; font-size: 0.88rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Opening Stocks
                    </a>
                </div>
            </div>

            <!-- Body Table -->
            <div class="p-2 p-md-4">
                <div class="table-responsive bg-white rounded-3 border shadow-sm" style="overflow-x: auto;">
                    <table class="table mb-0 align-middle" id="unifiedStockTable">
                        <thead class="unified-table-header">
                            <tr>
                                <th style="width: 45px; text-align: center;">#</th>
                                <th style="min-width: 250px;">Product Name / SKU</th>
                                <th style="width: 280px; min-width: 270px; max-width: 290px;">Location & Allocated Qty</th>
                                <th style="width: 175px; min-width: 165px; text-align: right;">Wholesale Rs</th>
                                <th style="width: 175px; min-width: 165px; text-align: right;">Retail Rs</th>
                                <th style="width: 130px; min-width: 120px; text-align: center;">Alert Qty</th>
                                <th style="width: 140px; min-width: 130px; text-align: center;">Total Opening Qty</th>
                                <th style="width: 55px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="unified_tbody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary and Action Controls -->
                <div class="mt-4 p-3 bg-white rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3 shadow-sm footer-controls-wrap">
                    <div class="w-100-mobile">
                        <button type="button" class="btn btn-outline-primary fw-semibold px-3 py-2 d-flex align-items-center gap-2 w-100-mobile" onclick="addProductRow()" style="border-radius: 8px;">
                            <i class="fas fa-plus"></i> Add Product Row
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap w-100-mobile justify-content-between">
                        <div class="px-3 py-2 rounded-3 bg-light border fw-bold text-secondary d-flex align-items-center justify-content-between gap-2 w-100-mobile" style="font-size: 0.92rem;">
                            <span>Total Opening Quantity:</span>
                            <span class="text-primary fw-bolder fs-6" id="grand_total_display">0.00</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem; padding: 4px 8px;">Mixed Units</span>
                        </div>
                        <button type="button" class="btn btn-primary px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm w-100-mobile" id="btn_save_stocks" onclick="submitUnifiedForm()" style="background: #0284c7; border: none; border-radius: 8px; font-size: 0.95rem; height: 42px;">
                            <i class="fas fa-save"></i> Save All Opening Stocks
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Safe lookup cache
    const productsData = @json($products);
    const warehousesData = @json($warehouses);

    const productsMap = {};
    productsData.forEach(p => {
        productsMap[p.id] = p;
    });

    let productRowCounter = 0;
    let locCounter = 0;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    $(document).ready(function() {
        // Automatically start with 1 row
        addProductRow();
    });

    function addProductRow() {
        const rowId = ++productRowCounter;

        let prodOptions = '<option value="">Search product or SKU...</option>';
        productsData.forEach(p => {
            const code = escapeHtml(p.item_code || '');
            const name = escapeHtml(p.item_name || '');
            const mode = escapeHtml(p.size_mode || 'by_pieces');
            const ppb = p.pieces_per_box || 1;
            prodOptions += `<option value="${p.id}" data-code="${code}" data-name="${name}" data-mode="${mode}" data-ppb="${ppb}">${code} - ${name}</option>`;
        });

        const rowHtml = `
            <tr id="prod_row_${rowId}" class="prod-entry-row" data-row-id="${rowId}">
                <!-- Col 1: Row # (Desktop cell / Mobile Card Top Bar) -->
                <td class="text-center fw-bold text-muted td-row-num" style="vertical-align: middle;">
                    <span class="d-none d-lg-inline row-number">${$('#unified_tbody tr.prod-entry-row').length + 1}</span>
                    <div class="d-flex d-lg-none align-items-center justify-content-between pb-2 mb-2 border-bottom">
                        <span class="badge bg-light text-dark border fw-bold px-2.5 py-1.5" style="font-size:0.82rem;">
                            <i class="fas fa-cube me-1 text-primary"></i> Product #<span class="row-number-mobile">${$('#unified_tbody tr.prod-entry-row').length + 1}</span>
                        </span>
                        <button type="button" class="del-product-row-btn" title="Remove Product Row" onclick="removeProductRow(${rowId})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>

                <!-- Col 2: Product Name / SKU -->
                <td style="vertical-align: top;">
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size:0.72rem; letter-spacing:0.4px;">
                        <i class="fas fa-box me-1 text-primary"></i> Product Name / SKU
                    </label>
                    <div class="prod-select-wrapper">
                        <select class="form-control prod-select" id="prod_select_${rowId}">
                            ${prodOptions}
                        </select>
                    </div>
                    <div class="prod-info-pill mt-2 small" id="prod_info_${rowId}" style="display: none;"></div>
                    <div class="variant-wrapper mt-2" id="variant_wrap_${rowId}" style="display: none;">
                        <label class="small text-muted fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;">VARIANT (COLOR / SIZE):</label>
                        <select class="form-select form-control form-control-sm variant-select" id="variant_select_${rowId}" onchange="onRowVariantSelected(${rowId})"></select>
                    </div>
                </td>

                <!-- Col 3: Location & Allocated Qty -->
                <td style="vertical-align: top; width: 280px;">
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size:0.72rem; letter-spacing:0.4px;">
                        <i class="fas fa-warehouse me-1 text-primary"></i> Warehouse Locations &amp; Allocated Qty
                    </label>
                    <div class="locations-wrapper" id="locations_container_${rowId}"></div>
                    <div class="mt-1">
                        <a href="javascript:void(0)" class="btn-add-location" onclick="addLocationSubRow(${rowId})">
                            <i class="fas fa-plus-circle text-primary"></i> Add Location
                        </a>
                    </div>
                </td>

                <!-- Col 4: Wholesale Rs -->
                <td style="vertical-align: top; width: 175px;">
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size:0.72rem; letter-spacing:0.4px;">
                        <i class="fas fa-coins me-1 text-warning"></i> Wholesale Rate (Cost)
                    </label>
                    <div class="input-group" style="min-width: 155px;">
                        <input type="number" step="0.01" min="0" class="form-control text-end fw-bold wholesale-input" id="wholesale_${rowId}" value="0" placeholder="0" style="height: 38px; font-size: 0.95rem;" oninput="calcRowTotals(${rowId})">
                        <span class="input-group-text bg-light text-muted fw-bold px-2" style="font-size:0.82rem;">Rs</span>
                    </div>
                </td>

                <!-- Col 5: Retail Rs -->
                <td style="vertical-align: top; width: 175px;">
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size:0.72rem; letter-spacing:0.4px;">
                        <i class="fas fa-tag me-1 text-primary"></i> Retail Rate (Sale)
                    </label>
                    <div class="input-group" style="min-width: 155px;">
                        <input type="number" step="0.01" min="0" class="form-control text-end fw-bold retail-input" id="retail_${rowId}" value="0" placeholder="0" style="height: 38px; font-size: 0.95rem;">
                        <span class="input-group-text bg-light text-muted fw-bold px-2" style="font-size:0.82rem;">Rs</span>
                    </div>
                </td>

                <!-- Col 6: Alert Qty -->
                <td style="vertical-align: top; width: 130px;">
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size:0.72rem; letter-spacing:0.4px;">
                        <i class="fas fa-bell me-1 text-danger"></i> Alert / Low Stock Qty
                    </label>
                    <input type="number" step="1" min="0" class="form-control text-center fw-bold alert-input" id="alert_${rowId}" value="0" placeholder="0" style="height: 38px; font-size: 0.95rem; min-width: 90px;">
                </td>

                <!-- Col 7: Total Opening Qty -->
                <td class="text-center" style="vertical-align: top; width: 140px;">
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase d-block text-start" style="font-size:0.72rem; letter-spacing:0.4px;">
                        <i class="fas fa-calculator me-1 text-success"></i> Total Opening Qty
                    </label>
                    <div class="total-qty-box row-total-qty" id="total_qty_${rowId}">0.00</div>
                    <div class="small text-muted mt-1 row-total-sub" id="total_qty_sub_${rowId}" style="font-size: 0.72rem;"></div>
                </td>

                <!-- Col 8: Action (Desktop only delete button) -->
                <td class="text-center td-desktop-action" style="vertical-align: top;">
                    <button type="button" class="del-product-row-btn" title="Remove Product Row" onclick="removeProductRow(${rowId})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        const $row = $(rowHtml);
        $('#unified_tbody').append($row);

        // Initialize Select2 on the product select
        if (typeof $.fn.select2 !== 'undefined') {
            $(`#prod_select_${rowId}`).select2({
                placeholder: 'Search product or SKU...',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                onProductRowChanged(rowId, $(this).val());
            });
        } else {
            $(`#prod_select_${rowId}`).on('change', function() {
                onProductRowChanged(rowId, $(this).val());
            });
        }

        // Add 1 default Location sub-row
        addLocationSubRow(rowId);

        updateRowNumbers();
        calcGrandTotal();
    }

    function addLocationSubRow(rowId) {
        const locId = ++locCounter;

        let whOptions = '';
        warehousesData.forEach(w => {
            const wName = escapeHtml(w.warehouse_name || '');
            const wLoc = escapeHtml(w.location ? ' (' + w.location + ')' : '');
            whOptions += `<option value="${w.id}">${wName}${wLoc}</option>`;
        });

        const locHtml = `
            <div class="loc-card" id="loc_card_${rowId}_${locId}" data-loc-id="${locId}">
                <div class="mb-1">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-primary py-0 px-2" style="font-size: 0.75rem;">
                            <i class="fas fa-store"></i>
                        </span>
                        <select class="form-select form-control form-control-sm loc-warehouse-select" id="wh_select_${rowId}_${locId}" style="font-size: 0.82rem; height: 32px;" onchange="calcRowTotals(${rowId})">
                            ${whOptions}
                        </select>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="small fw-bold text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">QTY</span>
                    <div class="input-group input-group-sm flex-grow-1">
                        <input type="number" step="any" min="0" class="form-control text-center fw-bold loc-qty-input" id="qty_${rowId}_${locId}" value="0" style="height: 32px; font-size: 0.88rem; min-width: 65px;" oninput="calcRowTotals(${rowId})">
                        <span class="input-group-text loc-unit-badge" id="unit_badge_${rowId}_${locId}">Pcs</span>
                    </div>
                    <input type="hidden" class="loc-unit-select" id="unit_select_${rowId}_${locId}" value="pcs">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 loc-remove-btn ms-auto" style="text-decoration: none; font-size: 1.1rem; line-height: 1; opacity: 0.7;" title="Remove this warehouse" onclick="removeLocationSubRow(${rowId}, ${locId})">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
                <div class="loc-hint small text-success fw-semibold mt-1" id="loc_hint_${rowId}_${locId}" style="font-size: 0.72rem; display: none;"></div>
            </div>
        `;

        $(`#locations_container_${rowId}`).append(locHtml);

        // Populate unit selector according to currently selected product
        const selectedProdId = $(`#prod_select_${rowId}`).val();
        const product = productsMap[selectedProdId] || null;
        updateLocationUnitSelect(rowId, locId, product);

        updateLocRemoveButtons(rowId);
        calcRowTotals(rowId);
    }

    function removeLocationSubRow(rowId, locId) {
        $(`#loc_card_${rowId}_${locId}`).remove();
        updateLocRemoveButtons(rowId);
        calcRowTotals(rowId);
    }

    function updateLocRemoveButtons(rowId) {
        const count = $(`#locations_container_${rowId} .loc-card`).length;
        if (count <= 1) {
            $(`#locations_container_${rowId} .loc-remove-btn`).hide();
        } else {
            $(`#locations_container_${rowId} .loc-remove-btn`).show();
        }
    }

    function removeProductRow(rowId) {
        const totalRows = $('#unified_tbody tr.prod-entry-row').length;
        if (totalRows <= 1) {
            // Reset if only 1 row
            $(`#prod_select_${rowId}`).val('').trigger('change');
            $(`#wholesale_${rowId}`).val(0);
            $(`#retail_${rowId}`).val(0);
            $(`#alert_${rowId}`).val(0);
            $(`#locations_container_${rowId} .loc-qty-input`).val(0);
            calcRowTotals(rowId);
            return;
        }

        $(`#prod_row_${rowId}`).remove();
        updateRowNumbers();
        calcGrandTotal();
    }

    function updateRowNumbers() {
        $('#unified_tbody tr.prod-entry-row').each(function(index) {
            $(this).find('.row-number').text(index + 1);
            $(this).find('.row-number-mobile').text(index + 1);
        });
    }

    function updateLocationUnitSelect(rowId, locId, product) {
        const $unitBadge = $(`#unit_badge_${rowId}_${locId}`);
        const $unitHidden = $(`#unit_select_${rowId}_${locId}`);
        if (!$unitBadge.length) return;

        let unitLabel = 'Pcs';
        let unitValue = 'pcs';

        if (product) {
            const mode = product.size_mode || 'by_pieces';
            const ppb = parseFloat(product.pieces_per_box) || 1;
            const uName = escapeHtml(product.unit_name || 'Unit');

            const $varSelect = $(`#variant_select_${rowId}`);
            let effectivePpb = ppb;
            if ($varSelect.length && $varSelect.val()) {
                const $varOpt = $varSelect.find('option:selected');
                const varConv = parseFloat($varOpt.data('conv')) || 0;
                if (varConv > 0) effectivePpb = varConv;
            }

            if (mode === 'by_cartons') {
                unitLabel = effectivePpb > 1 ? `Carton (${effectivePpb} Pcs)` : 'Carton';
                unitValue = 'carton';
            } else if (mode === 'by_bandal') {
                unitLabel = effectivePpb > 1 ? `Bandal (${effectivePpb} Pcs)` : 'Bandal';
                unitValue = 'bandal';
            } else if (mode === 'by_pieces') {
                unitLabel = 'Pcs';
                unitValue = 'pcs';
            } else {
                unitLabel = uName;
                unitValue = 'custom';
            }
        }

        $unitBadge.text(unitLabel);
        $unitHidden.val(unitValue);
    }

    function onProductRowChanged(rowId, productId) {
        if (!productId) {
            $(`#wholesale_${rowId}`).val(0);
            $(`#retail_${rowId}`).val(0);
            $(`#alert_${rowId}`).val(0);
            $(`#prod_info_${rowId}`).hide().empty();
            $(`#variant_wrap_${rowId}`).hide();
            $(`#variant_select_${rowId}`).empty();

            $(`#locations_container_${rowId} .loc-card`).each(function() {
                const locId = $(this).data('loc-id');
                updateLocationUnitSelect(rowId, locId, null);
            });

            calcRowTotals(rowId);
            return;
        }

        const product = productsMap[productId];
        if (!product) return;

        // Auto-fill prices
        const wholesale = product.wholesale_price || product.cost_price || 0;
        const retail = product.sale_price || 0;
        const alertVal = product.alert_qty || 0;

        $(`#wholesale_${rowId}`).val(parseFloat(wholesale).toFixed(2));
        $(`#retail_${rowId}`).val(parseFloat(retail).toFixed(2));
        $(`#alert_${rowId}`).val(alertVal);

        // Product packaging info pill
        const mode = product.size_mode || 'by_pieces';
        const ppb = parseFloat(product.pieces_per_box) || 1;
        let pillHtml = '';

        if (mode === 'by_cartons') {
            pillHtml = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1"><i class="fas fa-box me-1"></i>Carton</span> <b>1 Carton = ${ppb} Pcs</b>`;
        } else if (mode === 'by_bandal') {
            pillHtml = `<span class="badge bg-warning-subtle text-dark border border-warning-subtle me-1"><i class="fas fa-layer-group me-1"></i>Bandal</span> <b>1 Bandal = ${ppb} Pcs</b>`;
        } else if (mode === 'by_pieces') {
            pillHtml = `<span class="badge bg-info-subtle text-info border border-info-subtle me-1"><i class="fas fa-circle-check me-1"></i>Pieces</span> <b>Single Pcs</b>`;
        } else {
            pillHtml = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle me-1"><i class="fas fa-tag me-1"></i>${escapeHtml(product.unit_name || 'Unit')}</span> <b>Single Unit</b>`;
        }
        $(`#prod_info_${rowId}`).html(pillHtml).show();

        // Handle Variants if product has variants
        if (product.variants && product.variants.length > 0) {
            let varOptions = '<option value="">-- Base / All Variants --</option>';
            product.variants.forEach(v => {
                const vKey = escapeHtml(v.variant_key || '');
                const vName = escapeHtml(v.name || '');
                const vCost = parseFloat(v.cost_price) || 0;
                const vSale = parseFloat(v.sale_price) || 0;
                const vAlert = parseFloat(v.alert_qty) || 0;
                const vConv = parseFloat(v.conv_factor) || ppb;
                const vLabel = escapeHtml(v.display_label || v.name || '');
                varOptions += `<option value="${vKey}" data-name="${vName}" data-cost="${vCost}" data-sale="${vSale}" data-alert="${vAlert}" data-conv="${vConv}">${vLabel}</option>`;
            });
            $(`#variant_select_${rowId}`).html(varOptions);
            $(`#variant_wrap_${rowId}`).show();
        } else {
            $(`#variant_wrap_${rowId}`).hide();
            $(`#variant_select_${rowId}`).empty();
        }

        // Update unit badge for all location cards in this row
        $(`#locations_container_${rowId} .loc-card`).each(function() {
            const locId = $(this).data('loc-id');
            updateLocationUnitSelect(rowId, locId, product);
        });

        calcRowTotals(rowId);
    }

    function onRowVariantSelected(rowId) {
        const pId = $(`#prod_select_${rowId}`).val();
        const product = productsMap[pId] || null;
        const $selectedOption = $(`#variant_select_${rowId} option:selected`);

        if (!$selectedOption.val()) {
            // Revert to product base prices
            if (product) {
                $(`#wholesale_${rowId}`).val(parseFloat(product.wholesale_price || product.cost_price || 0).toFixed(2));
                $(`#retail_${rowId}`).val(parseFloat(product.sale_price || 0).toFixed(2));
                $(`#alert_${rowId}`).val(product.alert_qty || 0);
            }
        } else {
            const cost = parseFloat($selectedOption.data('cost')) || 0;
            const sale = parseFloat($selectedOption.data('sale')) || 0;
            const alertVal = parseFloat($selectedOption.data('alert')) || 0;

            if (cost > 0) $(`#wholesale_${rowId}`).val(cost.toFixed(2));
            if (sale > 0) $(`#retail_${rowId}`).val(sale.toFixed(2));
            if (alertVal > 0) $(`#alert_${rowId}`).val(alertVal);
        }

        // Update location unit labels and badges
        $(`#locations_container_${rowId} .loc-card`).each(function() {
            const locId = $(this).data('loc-id');
            updateLocationUnitSelect(rowId, locId, product);
        });

        calcRowTotals(rowId);
    }

    function calcRowTotals(rowId) {
        const productId = $(`#prod_select_${rowId}`).val();
        const product = productsMap[productId] || null;
        const mode = product ? (product.size_mode || 'by_pieces') : 'by_pieces';

        let effectivePpb = product ? (parseFloat(product.pieces_per_box) || 1) : 1;
        const $varSelect = $(`#variant_select_${rowId}`);
        if ($varSelect.length && $varSelect.val()) {
            const $varOpt = $varSelect.find('option:selected');
            const varConv = parseFloat($varOpt.data('conv')) || 0;
            if (varConv > 0) effectivePpb = varConv;
        }

        const isCartonOrBandal = effectivePpb > 1 && (mode === 'by_cartons' || mode === 'by_bandal');

        let rowTotalPieces = 0;
        let rowTotalDisplayQty = 0;

        $(`#locations_container_${rowId} .loc-card`).each(function() {
            const $card = $(this);
            const qty = parseFloat($card.find('.loc-qty-input').val()) || 0;
            const unit = $card.find('.loc-unit-select').val() || 'pcs';
            const $hint = $card.find('.loc-hint');

            let locPieces = 0;
            let locDisplay = 0;

            if (unit === 'carton' || unit === 'bandal' || unit === 'box') {
                locPieces = qty * effectivePpb;
                locDisplay = qty;
                if (qty > 0) {
                    $hint.text(`(= ${locPieces.toLocaleString()} Pcs)`).show();
                } else {
                    $hint.hide();
                }
            } else {
                locPieces = qty;
                locDisplay = isCartonOrBandal ? (qty / effectivePpb) : qty;
                if (qty > 0 && isCartonOrBandal) {
                    const unitName = (mode === 'by_bandal') ? 'Bandals' : 'Cartons';
                    $hint.text(`(= ${(qty / effectivePpb).toFixed(2)} ${unitName})`).show();
                } else {
                    $hint.hide();
                }
            }

            rowTotalPieces += locPieces;
            rowTotalDisplayQty += locDisplay;
        });

        // Display formatted row total: Jo quantity di hai wo Total Opening Qty cell me show hogi
        $(`#total_qty_${rowId}`).text(rowTotalDisplayQty.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // Subtitle: Bandal/Carton mode me neeche pieces me show hoga (e.g. "= 50 Pcs")
        if (isCartonOrBandal && rowTotalPieces > 0) {
            const pcsFormatted = Number.isInteger(rowTotalPieces) ? rowTotalPieces.toLocaleString() : rowTotalPieces.toFixed(2);
            $(`#total_qty_sub_${rowId}`).text(`= ${pcsFormatted} Pcs`);
        } else {
            $(`#total_qty_sub_${rowId}`).text('');
        }

        calcGrandTotal();
    }

    function calcGrandTotal() {
        let grandTotal = 0;

        $('#unified_tbody tr.prod-entry-row').each(function() {
            const rowId = $(this).data('row-id');
            const rowQty = parseFloat($(`#total_qty_${rowId}`).text().replace(/,/g, '')) || 0;
            grandTotal += rowQty;
        });

        $('#grand_total_display').text(grandTotal.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    function submitUnifiedForm() {
        const productsPayload = [];
        let totalAllocatedLocations = 0;

        $('#unified_tbody tr.prod-entry-row').each(function() {
            const rowId = $(this).data('row-id');
            const productId = $(`#prod_select_${rowId}`).val();
            if (!productId) return; // Skip empty row

            const variantKey = $(`#variant_select_${rowId}`).val() || null;
            const variantName = variantKey ? $(`#variant_select_${rowId} option:selected`).data('name') : null;
            const wholesalePrice = parseFloat($(`#wholesale_${rowId}`).val()) || 0;
            const retailPrice = parseFloat($(`#retail_${rowId}`).val()) || 0;
            const alertQty = parseFloat($(`#alert_${rowId}`).val()) || 0;

            const locations = [];
            $(`#locations_container_${rowId} .loc-card`).each(function() {
                const whId = parseInt($(this).find('.loc-warehouse-select').val()) || 0;
                const qty = parseFloat($(this).find('.loc-qty-input').val()) || 0;
                const unit = $(this).find('.loc-unit-select').val() || 'pcs';

                if (whId > 0 && qty > 0) {
                    locations.push({
                        warehouse_id: whId,
                        qty: qty,
                        unit: unit,
                        notes: 'Opening Stock'
                    });
                    totalAllocatedLocations++;
                }
            });

            if (locations.length > 0) {
                productsPayload.push({
                    product_id: parseInt(productId),
                    variant_key: variantKey,
                    variant_name: variantName,
                    wholesale_price: wholesalePrice,
                    cost_price: wholesalePrice,
                    retail_price: retailPrice,
                    alert_qty: alertQty,
                    locations: locations
                });
            }
        });

        if (productsPayload.length === 0 || totalAllocatedLocations === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Quantities Entered',
                text: 'Please select a product and enter a quantity greater than 0 for at least one warehouse location.'
            });
            return;
        }

        Swal.fire({
            title: 'Saving Opening Stock...',
            text: `Recording stock for ${productsPayload.length} product(s) across ${totalAllocatedLocations} warehouse location(s)`,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ route('opening_stock.store') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                entry_type: 'unified',
                products: productsPayload
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Opening Stock Saved!',
                        text: res.message,
                        confirmButtonText: 'View Opening Stocks'
                    }).then(() => {
                        window.location.href = "{{ route('opening_stock.index') }}";
                    });
                } else {
                    Swal.fire('Failed', res.message || 'Error processing opening stock entries', 'error');
                }
            },
            error: function(err) {
                const msg = err.responseJSON?.message || 'Server error occurred while saving opening stock';
                Swal.fire('Error', msg, 'error');
            }
        });
    }
</script>
@endpush
