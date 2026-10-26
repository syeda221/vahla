@extends('admin_panel.layout.app')

@section('content')
<style>
    .opening-manager-card {
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color) !important;
        background: var(--bg-card);
        box-shadow: var(--shadow-card);
    }
    .table-manager-header {
        background: var(--bg-page) !important;
    }
    .table-manager-header th {
        color: var(--text-secondary) !important;
        font-size: var(--font-table-header) !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        padding: 12px 10px !important;
        border: none !important;
        border-bottom: 1px solid var(--border-color) !important;
        background: var(--bg-page) !important;
        vertical-align: middle;
        white-space: nowrap;
    }
    .table.erp-table td {
        vertical-align: middle !important;
        padding: 8px 10px !important;
        border-bottom: 1px solid var(--border-color) !important;
        background: var(--bg-card);
    }
    .location-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 4px 6px;
        margin-bottom: 6px;
        box-shadow: var(--shadow-sm);
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: nowrap !important;
    }
    .qty-addon-label {
        background: var(--bg-page);
        font-weight: 600;
        font-size: 11px;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        padding: 0 8px !important;
        height: 40px !important;
        display: inline-flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }
    .erp-input {
        height: var(--height-control) !important;
        font-size: var(--font-body) !important;
        font-weight: 500;
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-md) !important;
        color: var(--text-primary);
        transition: border-color var(--transition), box-shadow var(--transition);
    }
    .erp-input:focus {
        border-color: var(--primary) !important;
        box-shadow: var(--focus-ring) !important;
        outline: none !important;
    }
    .readonly-detail-field {
        background-color: var(--bg-page) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-secondary) !important;
        font-weight: 600 !important;
        font-variant-numeric: tabular-nums;
        cursor: not-allowed !important;
        user-select: all;
    }
    .readonly-detail-field:focus {
        box-shadow: none !important;
        border-color: var(--border-color) !important;
        outline: none !important;
    }
    .total-qty-badge {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary);
        background: var(--primary-soft);
        border: 1px solid #C7D2FE;
        padding: 4px 12px;
        border-radius: var(--radius-pill);
        display: inline-block;
        min-width: 65px;
        font-variant-numeric: tabular-nums;
    }
    .select2-container--default .select2-selection--single {
        height: var(--height-control) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-md) !important;
        display: flex;
        align-items: center;
        padding-left: 12px;
        font-size: var(--font-body);
        font-weight: 500;
        color: var(--text-primary);
        transition: border-color var(--transition), box-shadow var(--transition);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: var(--height-control) !important;
        color: var(--text-primary) !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: var(--height-control) !important;
        right: 8px;
    }
    .select2-dropdown {
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-md) !important;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        font-size: var(--font-body);
        font-weight: 500;
    }
    @keyframes dynamicPulse {
        0% {
            background-color: var(--success-soft);
            border-color: var(--success) !important;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);
        }
        50% {
            background-color: #d1fae5;
            border-color: var(--success) !important;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.3);
        }
        100% {
            background-color: var(--bg-page);
            border-color: var(--border-color) !important;
            box-shadow: none;
        }
    }
    .field-updated {
        animation: dynamicPulse 1.2s ease-in-out !important;
    }
    .editable-indicator {
        font-size: 11px;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 3px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 3px;
    }
    .editable-indicator i {
        font-size: 10px;
    }
</style>

<div class="erp-container py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="erp-kpi-icon" style="width: 44px; height: 44px; font-size: 1.15rem;">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="erp-page-title mb-0">Opening Stock Manager</h1>
                    <span class="erp-badge erp-badge-primary">ERP Standard</span>
                </div>
                <p class="erp-caption mb-0 mt-1">Add opening stock &mdash; products are global, stock is allocated per branch/warehouse.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @canany(['warehouse.stock.view', 'opening.stock.view'])
            <a href="{{ route('warehouse_stocks.index') }}" class="erp-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Opening Stocks
            </a>
            @endcanany
            @can('products.view')
            <a href="{{ route('product') }}" class="erp-btn-secondary">
                <i class="fas fa-box"></i> Products Catalog
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--success) !important;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--danger) !important;">
            <i class="fas fa-exclamation-triangle me-3 text-danger fs-5"></i>
            <div>
                <strong>Notice:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show erp-card p-3 mb-4" role="alert" style="border-left: 4px solid var(--danger) !important;">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-exclamation-triangle me-2 text-danger fs-5"></i>
                <strong class="fs-6">Please fix the following errors:</strong>
            </div>
            <ul class="mb-0 ps-4 erp-caption">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('warehouse_stocks.store') }}" method="POST" id="bulkOpeningForm">
        @csrf

        <!-- Target Branch Card -->
        <div class="card erp-card p-3 p-md-4 mb-4">
            <div class="d-flex align-items-center flex-wrap gap-4">
                <div class="d-flex align-items-center gap-3" style="min-width: 360px;">
                    <label class="erp-kpi-label mb-0 text-nowrap">
                        Target Branch <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-primary px-3" style="border: 1px solid var(--border-color); border-right: 0;"><i class="fas fa-warehouse"></i></span>
                        <select name="target_warehouse_id" id="target_warehouse_id" class="form-select fw-semibold text-dark erp-input border-start-0" onchange="updateDefaultBranch(this.value)">
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}" {{ $w->is_shop ? 'selected' : '' }}>
                                    {{ $w->is_shop ? '🏪 ' : '🏢 ' }}{{ $w->warehouse_name }}
                                    @if($w->is_shop) (Main Shop) @else (Storage Godown) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="erp-badge erp-badge-info px-3 py-2 ms-lg-2">
                    <i class="fas fa-info-circle me-1"></i>
                    <span>Stock allocations will be recorded for the selected branch by default.</span>
                </div>
            </div>
        </div>

        <!-- Product Allocations Grid Table -->
        <div class="card erp-card overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table erp-table align-middle mb-0" id="openingGridTable">
                    <thead class="table-manager-header">
                        <tr>
                            <th style="width: 35px;" class="text-center">#</th>
                            <th style="min-width: 220px;">Product Name / SKU</th>
                            <th style="min-width: 290px;">Location & Allocated Qty</th>
                            <th style="width: 105px; min-width: 95px;">Wholesale (Rs)</th>
                            <th style="width: 105px; min-width: 95px;">Retail (Rs)</th>
                            <th style="width: 90px; min-width: 80px;">Alert Qty</th>
                            <th style="width: 95px; min-width: 85px;" class="text-center">Total Opening</th>
                            <th style="width: 45px; min-width: 45px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gridTableBody">
                        <!-- Rows rendered via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Controls -->
            <div class="card-footer bg-white p-3 p-md-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button type="button" class="erp-btn-secondary" onclick="addProductRow()">
                    <i class="fas fa-plus"></i> Add Product Row
                </button>

                <div class="d-flex align-items-center gap-3 ms-auto flex-wrap">
                    <div class="p-2 px-3 bg-light rounded-pill border d-flex align-items-center gap-2">
                        <span class="erp-caption text-uppercase fw-semibold">Total Opening Quantity:</span>
                        <span class="font-tabular text-primary fs-6 fw-bold" id="grandTotalQtyBadge">0.00</span>
                        <span class="erp-badge erp-badge-neutral">Mixed Units</span>
                    </div>

                    <!-- Only ONE primary button in this form area -->
                    <button type="submit" class="erp-btn-primary px-4" id="btnSaveAll">
                        <i class="fas fa-save me-1"></i> Save All Opening Stocks
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<!-- Select2 -->
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

<script>
    const warehousesData = @json($warehouses);
    let currentRowIndex = 0;

    $(document).ready(function() {
        // Add initial first row
        addProductRow();

        // Submit loading state
        $('#bulkOpeningForm').on('submit', function() {
            const btn = $('#btnSaveAll');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving Opening Stocks...');
        });
    });

    function getWarehouseOptionsHtml(selectedId = null) {
        let defaultId = selectedId || $('#target_warehouse_id').val();
        let html = '';
        warehousesData.forEach(w => {
            let isSelected = String(w.id) === String(defaultId) ? 'selected' : '';
            let icon = w.is_shop ? '🏪 ' : '🏢 ';
            let label = w.is_shop ? ' (Shop)' : ' (Godown)';
            html += `<option value="${w.id}" ${isSelected}>${icon}${w.warehouse_name}${label}</option>`;
        });
        return html;
    }

    function addProductRow() {
        currentRowIndex++;
        const rIndex = currentRowIndex;

        const rowHtml = `
            <tr class="product-row" id="row_${rIndex}" data-index="${rIndex}">
                <td class="text-center font-tabular text-secondary row-number" style="font-size: 14px;">
                    ${rIndex}
                </td>

                <!-- Product Name / SKU Search -->
                <td>
                    <select name="rows[${rIndex}][product_id]" class="form-control product-select" id="prod_select_${rIndex}" required style="width: 100%;">
                        <option value="">Search product by name or SKU...</option>
                    </select>
                    <input type="hidden" name="rows[${rIndex}][unit_name]" id="unit_hidden_${rIndex}" value="Kg">
                </td>

                <!-- Location & Allocated Qty -->
                <td>
                    <div class="locations-wrapper" id="locations_${rIndex}" data-index="${rIndex}">
                        <div class="location-box" data-loc-index="0">
                            <select name="rows[${rIndex}][locations][0][warehouse_id]" class="form-select form-select-sm erp-input wh-dropdown" style="height: 40px; font-size: 13px; min-width: 130px; flex: 1 1 auto;">
                                ${getWarehouseOptionsHtml()}
                            </select>
                            <div class="input-group" style="width: 145px; min-width: 145px; flex: 0 0 145px; flex-wrap: nowrap;">
                                <span class="input-group-text qty-addon-label px-2" style="font-size: 11px;">QTY</span>
                                <input type="number" step="any" min="0" name="rows[${rIndex}][locations][0][quantity]" 
                                       class="form-control erp-input font-tabular qty-input text-end px-2" 
                                       style="height: 40px; border-left: 0 !important; border-right: 0 !important; font-size: 13px;"
                                       value="0" placeholder="0" oninput="calculateRowTotal(${rIndex})">
                                <span class="input-group-text qty-addon-label px-2 unit-label-${rIndex}" style="font-size: 11px;">Kg</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-medium" style="font-size: 12px;" onclick="addLocationSlot(${rIndex})">
                        <i class="fas fa-plus-circle me-1"></i> Add Location
                    </button>
                </td>

                <!-- Wholesale Rs Input (Unchangeable / Read-only) -->
                <td>
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <input type="text" readonly tabindex="-1" name="rows[${rIndex}][wholesale_price]" id="wholesale_${rIndex}" 
                               class="form-control erp-input font-tabular text-end readonly-detail-field px-2" 
                               style="height: 40px; border-right: 0 !important; font-size: 13px;" 
                               placeholder="—" title="Wholesale price (Read-only master catalog detail)">
                        <span class="input-group-text qty-addon-label text-muted px-2" style="font-size: 11px;">Rs</span>
                    </div>
                    <div class="editable-indicator">
                        <i class="fas fa-lock text-muted"></i> Fixed
                    </div>
                </td>

                <!-- Retail Rs Input (Unchangeable / Read-only) -->
                <td>
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <input type="text" readonly tabindex="-1" name="rows[${rIndex}][retail_price]" id="retail_${rIndex}" 
                               class="form-control erp-input font-tabular text-end readonly-detail-field px-2" 
                               style="height: 40px; border-right: 0 !important; font-size: 13px;" 
                               placeholder="—" title="Retail price (Read-only master catalog detail)">
                        <span class="input-group-text qty-addon-label text-muted px-2" style="font-size: 11px;">Rs</span>
                    </div>
                    <div class="editable-indicator">
                        <i class="fas fa-lock text-muted"></i> Fixed
                    </div>
                </td>

                <!-- Alert Qty Input (Unchangeable / Read-only) -->
                <td>
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <input type="text" readonly tabindex="-1" name="rows[${rIndex}][alert_quantity]" id="alert_${rIndex}" 
                               class="form-control erp-input font-tabular text-end readonly-detail-field px-2" 
                               style="height: 40px; border-right: 0 !important; font-size: 13px;" 
                               placeholder="—" title="Alert quantity (Read-only master catalog detail)">
                        <span class="input-group-text qty-addon-label text-muted px-2 unit-label-${rIndex}" style="font-size: 11px;">Kg</span>
                    </div>
                    <div class="editable-indicator">
                        <i class="fas fa-lock text-muted"></i> Fixed
                    </div>
                </td>

                <!-- Total Opening Qty Display (Calculated Live) -->
                <td class="text-center pt-2">
                    <span class="total-qty-badge font-tabular" id="row_total_${rIndex}">0.00</span>
                    <span class="erp-badge erp-badge-neutral unit-label-${rIndex} mt-1 d-inline-block">Kg</span>
                </td>

                <!-- Remove Row Action -->
                <td class="text-center pt-2">
                    <button type="button" class="erp-btn-icon erp-btn-icon-danger" onclick="removeProductRow(${rIndex})" title="Remove Product Row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#gridTableBody').append(rowHtml);
        reNumberRows();
        initProductSelect2(rIndex);
    }

    function initProductSelect2(rIndex) {
        const $select = $(`#prod_select_${rIndex}`);

        $select.select2({
            placeholder: "Search product by name or SKU...",
            allowClear: true,
            ajax: {
                url: "{{ route('warehouse_stock.search-products') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        $select.on('select2:select', function(e) {
            const item = e.params.data;
            fetchAndPopulateProduct(rIndex, item.id, item);
        });

        $select.on('select2:clear', function(e) {
            $(`#wholesale_${rIndex}`).val('—');
            $(`#retail_${rIndex}`).val('—');
            $(`#alert_${rIndex}`).val('—');
            $(`#unit_hidden_${rIndex}`).val('Kg');
            $(`.unit-label-${rIndex}`).text('Kg');
            calculateRowTotal(rIndex);
        });

        $select.on('change', function() {
            const pId = $(this).val();
            if (pId && !$select.data('fetching')) {
                fetchAndPopulateProduct(rIndex, pId);
            }
        });
    }

    function fetchAndPopulateProduct(rIndex, productId, preloadedData = null) {
        if (!productId) return;

        // Apply preloaded data immediately if present for instant response
        if (preloadedData && preloadedData.unit_name) {
            applyProductFields(rIndex, preloadedData);
        }

        // Query database endpoint for fresh, exact values
        $(`#prod_select_${rIndex}`).data('fetching', true);
        const detailsUrl = "{{ url('/warehouse-stock/product-details') }}/" + productId;

        $.ajax({
            url: detailsUrl,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                applyProductFields(rIndex, res);
            },
            error: function(err) {
                console.warn('Failed to fetch details for product ' + productId, err);
            },
            complete: function() {
                $(`#prod_select_${rIndex}`).data('fetching', false);
            }
        });
    }

    function applyProductFields(rIndex, data) {
        const unit = data.unit_name || 'Kg';
        $(`#unit_hidden_${rIndex}`).val(unit);
        $(`.unit-label-${rIndex}`).text(unit);

        // 1. Wholesale Price (Unchangeable / Read-only)
        const wholesaleVal = (data.wholesale_price !== undefined && data.wholesale_price !== null && data.wholesale_price !== '') 
            ? data.wholesale_price 
            : '—';
        const $wholesale = $(`#wholesale_${rIndex}`);
        $wholesale.val(wholesaleVal);
        flashField($wholesale);

        // 2. Retail Price (Unchangeable / Read-only)
        const retailVal = (data.retail_price !== undefined && data.retail_price !== null && data.retail_price !== '') 
            ? data.retail_price 
            : '—';
        const $retail = $(`#retail_${rIndex}`);
        $retail.val(retailVal);
        flashField($retail);

        // 3. Alert Quantity (Unchangeable / Read-only)
        const alertVal = (data.alert_quantity !== undefined && data.alert_quantity !== null && data.alert_quantity !== '') 
            ? data.alert_quantity 
            : '—';
        const $alert = $(`#alert_${rIndex}`);
        $alert.val(alertVal);
        flashField($alert);
    }

    function flashField($elem) {
        $elem.removeClass('field-updated');
        if ($elem[0]) {
            void $elem[0].offsetWidth; // trigger reflow for smooth re-animation
            $elem.addClass('field-updated');
            setTimeout(() => {
                $elem.removeClass('field-updated');
            }, 1200);
        }
    }

    function addLocationSlot(rIndex) {
        const wrapper = $(`#locations_${rIndex}`);
        const locIndex = wrapper.find('.location-box').length;
        const currentUnit = $(`#unit_hidden_${rIndex}`).val() || 'Kg';

        const slotHtml = `
            <div class="location-box" data-loc-index="${locIndex}">
                <select name="rows[${rIndex}][locations][${locIndex}][warehouse_id]" class="form-select form-select-sm erp-input wh-dropdown" style="height: 40px; font-size: 13px; min-width: 130px; flex: 1 1 auto;">
                    ${getWarehouseOptionsHtml()}
                </select>
                <div class="input-group" style="width: 145px; min-width: 145px; flex: 0 0 145px; flex-wrap: nowrap;">
                    <span class="input-group-text qty-addon-label px-2" style="font-size: 11px;">QTY</span>
                    <input type="number" step="any" min="0" name="rows[${rIndex}][locations][${locIndex}][quantity]" 
                           class="form-control erp-input font-tabular qty-input text-end px-2" 
                           style="height: 40px; border-left: 0 !important; border-right: 0 !important; font-size: 13px;"
                           value="0" placeholder="0" oninput="calculateRowTotal(${rIndex})">
                    <span class="input-group-text qty-addon-label px-2 unit-label-${rIndex}" style="font-size: 11px;">${currentUnit}</span>
                </div>
                <button type="button" class="btn btn-sm text-danger p-0 border-0 bg-transparent ms-1" onclick="$(this).closest('.location-box').remove(); calculateRowTotal(${rIndex});" title="Remove Location" style="line-height: 1;">
                    <i class="fas fa-times-circle fs-5"></i>
                </button>
            </div>
        `;

        wrapper.append(slotHtml);
    }

    function calculateRowTotal(rIndex) {
        let rowSum = 0;
        $(`#locations_${rIndex} .qty-input`).each(function() {
            let val = parseFloat($(this).val()) || 0;
            rowSum += val;
        });

        let formatted = rowSum.toFixed(2).replace(/\.?0+$/, '');
        if (formatted === '' || formatted === '0') formatted = '0.00';
        $(`#row_total_${rIndex}`).text(formatted);

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.qty-input').each(function() {
            let val = parseFloat($(this).val()) || 0;
            grandTotal += val;
        });
        let formatted = grandTotal.toFixed(2).replace(/\.?0+$/, '');
        if (formatted === '' || formatted === '0') formatted = '0.00';
        $('#grandTotalQtyBadge').text(formatted);
    }

    function removeProductRow(rIndex) {
        if ($('.product-row').length <= 1) {
            Swal.fire({
                icon: 'info',
                title: 'Notice',
                text: 'At least one product row must remain.',
                timer: 1500,
                showConfirmButton: false
            });
            return;
        }
        $(`#row_${rIndex}`).remove();
        reNumberRows();
        calculateGrandTotal();
    }

    function reNumberRows() {
        $('.product-row').each(function(index) {
            $(this).find('.row-number').text(index + 1);
        });
    }

    function updateDefaultBranch(whId) {
        $('.wh-dropdown').each(function() {
            if (!$(this).data('user-touched')) {
                $(this).val(whId);
            }
        });
    }

    $(document).on('change', '.wh-dropdown', function() {
        $(this).data('user-touched', true);
    });
</script>
@endsection
