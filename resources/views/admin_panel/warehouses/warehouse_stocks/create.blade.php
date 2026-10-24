@extends('admin_panel.layout.app')

@section('content')
<style>
    .opening-manager-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    .erp-tag {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 6px;
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .table-manager-header {
        background: #0f172a !important;
        color: #ffffff !important;
    }
    .table-manager-header th {
        color: #f8fafc !important;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 13px 14px;
        border: none !important;
        vertical-align: middle;
    }
    .grid-table td {
        vertical-align: top;
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
    }
    .location-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 6px;
    }
    .qty-addon-label {
        background: #f1f5f9;
        font-weight: 600;
        font-size: 0.8rem;
        color: #475569;
    }
    .total-qty-badge {
        font-size: 1rem;
        font-weight: 800;
        color: #0284c7;
        background: #e0f2fe;
        border: 1px solid #bae6fd;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
        min-width: 75px;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border-color: #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Header (Image 1 Style) -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="background: #e0f2fe; width: 48px; height: 48px; color: #0284c7;">
                <i class="fas fa-database fa-lg"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0 fw-bold text-dark">Opening Stock Manager</h4>
                    <span class="erp-tag">ERP STANDARD</span>
                </div>
                <p class="text-muted small mb-0 mt-0.5">Add opening stock &mdash; products are global, stock is allocated per branch/warehouse.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold shadow-sm" style="height: 38px; display: inline-flex; align-items: center;">
                <i class="fas fa-arrow-left me-1.5"></i> Back to Opening Stocks List
            </a>
            <a href="{{ route('product') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold shadow-sm" style="height: 38px; display: inline-flex; align-items: center;">
                <i class="fas fa-box me-1.5"></i> Products Catalog
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fs-4 me-3 text-danger"></i>
            <div>
                <strong>Error!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('warehouse_stocks.store') }}" method="POST" id="bulkOpeningForm">
        @csrf

        <!-- Target Branch Card (Image 1 Style) -->
        <div class="card opening-manager-card border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2" style="min-width: 320px;">
                    <label class="fw-bold text-dark text-nowrap mb-0 small text-uppercase">
                        Target Branch <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-warehouse text-primary"></i></span>
                        <select name="target_warehouse_id" id="target_warehouse_id" class="form-select form-select-sm fw-bold text-dark border-start-0" onchange="updateDefaultBranch(this.value)">
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}" {{ $w->is_shop ? 'selected' : '' }}>
                                    {{ $w->is_shop ? '🏪 ' : '🏢 ' }}{{ $w->warehouse_name }}
                                    @if($w->is_shop) (Main Shop) @else (Storage Godown) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small fw-normal d-flex align-items-center gap-1.5 ms-lg-2">
                    <i class="fas fa-info-circle text-primary"></i>
                    <span>Stock allocations will be recorded for the selected branch.</span>
                </div>
            </div>
        </div>

        <!-- Product Allocations Grid Table (Exact Match to Image 1) -->
        <div class="card opening-manager-card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table grid-table align-middle mb-0" id="openingGridTable">
                    <thead class="table-manager-header">
                        <tr>
                            <th style="width: 3%;" class="text-center">#</th>
                            <th style="width: 28%;">Product Name / SKU</th>
                            <th style="width: 27%;">Location & Allocated Qty</th>
                            <th style="width: 11%;">Wholesale Rs</th>
                            <th style="width: 11%;">Retail Rs</th>
                            <th style="width: 8%;">Alert Qty</th>
                            <th style="width: 9%;" class="text-center">Total Opening Qty</th>
                            <th style="width: 3%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gridTableBody">
                        <!-- Rows rendered via JavaScript or Initial Row 1 -->
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Controls (Image 1 Style) -->
            <div class="card-footer bg-white p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm" onclick="addProductRow()">
                    <i class="fas fa-plus me-1.5"></i> Add Product Row
                </button>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="p-2 px-3 bg-light rounded-pill border d-flex align-items-center gap-2 shadow-sm">
                        <span class="small text-muted fw-bold text-uppercase">Total Opening Quantity:</span>
                        <span class="fw-bold font-monospace text-primary fs-6" id="grandTotalQtyBadge">0.00</span>
                        <span class="badge bg-secondary-subtle text-dark border small rounded-pill">Mixed Units</span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow" id="btnSaveAll">
                        <i class="fas fa-save me-1.5"></i> Save All Opening Stocks
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
                <td class="text-center fw-bold text-muted row-number pt-3" style="font-size: 0.9rem;">
                    ${rIndex}
                </td>

                <!-- Product Name / SKU Search (Image 1 Style) -->
                <td>
                    <select name="rows[${rIndex}][product_id]" class="form-control product-select" id="prod_select_${rIndex}" required style="width: 100%;">
                        <option value="">Search product or SKU...</option>
                    </select>
                    <input type="hidden" name="rows[${rIndex}][unit_name]" id="unit_hidden_${rIndex}" value="Pcs">
                </td>

                <!-- Location & Allocated Qty (Image 1 Style) -->
                <td>
                    <div class="locations-wrapper" id="locations_${rIndex}" data-index="${rIndex}">
                        <div class="location-box d-flex align-items-center gap-2 mb-1.5" data-loc-index="0">
                            <select name="rows[${rIndex}][locations][0][warehouse_id]" class="form-select form-select-sm border-secondary border-opacity-25 wh-dropdown" style="font-size: 0.85rem;">
                                ${getWarehouseOptionsHtml()}
                            </select>
                            <div class="input-group input-group-sm" style="max-width: 190px;">
                                <span class="input-group-text qty-addon-label px-2">QTY</span>
                                <input type="number" step="any" min="0" name="rows[${rIndex}][locations][0][quantity]" 
                                       class="form-control form-control-sm font-monospace fw-bold qty-input text-end" 
                                       value="0" placeholder="0" oninput="calculateRowTotal(${rIndex})">
                                <span class="input-group-text qty-addon-label px-2 unit-label-${rIndex}">Pcs</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-bold" style="font-size: 0.78rem;" onclick="addLocationSlot(${rIndex})">
                        <i class="fas fa-plus-circle me-1"></i> Add Location
                    </button>
                </td>

                <!-- Wholesale Rs Input -->
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="any" min="0" name="rows[${rIndex}][wholesale_price]" id="wholesale_${rIndex}" 
                               class="form-control form-control-sm font-monospace text-end" value="0" placeholder="0">
                        <span class="input-group-text text-muted small px-1.5">Rs</span>
                    </div>
                </td>

                <!-- Retail Rs Input -->
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="any" min="0" name="rows[${rIndex}][retail_price]" id="retail_${rIndex}" 
                               class="form-control form-control-sm font-monospace text-end" value="0" placeholder="0">
                        <span class="input-group-text text-muted small px-1.5">Rs</span>
                    </div>
                </td>

                <!-- Alert Qty Input -->
                <td>
                    <input type="number" step="any" min="0" name="rows[${rIndex}][alert_quantity]" id="alert_${rIndex}" 
                           class="form-control form-control-sm font-monospace text-end" value="0" placeholder="0">
                </td>

                <!-- Total Opening Qty Display (Calculated Live) -->
                <td class="text-center pt-2.5">
                    <span class="total-qty-badge font-monospace" id="row_total_${rIndex}">0.00</span>
                </td>

                <!-- Remove Row Action -->
                <td class="text-center pt-2.5">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 30px; height: 30px;" onclick="removeProductRow(${rIndex})" title="Remove Row">
                        <i class="fas fa-trash fa-xs"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#gridTableBody').append(rowHtml);
        reNumberRows();
        initProductSelect2(rIndex);
    }

    function initProductSelect2(rIndex) {
        $(`#prod_select_${rIndex}`).select2({
            placeholder: "Search product or SKU...",
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
        }).on('select2:select', function(e) {
            let item = e.params.data;
            let unit = item.unit_name || 'Pcs';
            $(`#unit_hidden_${rIndex}`).val(unit);
            $(`.unit-label-${rIndex}`).text(unit);

            // Pre-fill prices and alert qty
            $(`#wholesale_${rIndex}`).val(item.wholesale_price || 0);
            $(`#retail_${rIndex}`).val(item.retail_price || 0);
            $(`#alert_${rIndex}`).val(item.alert_quantity || 0);
        });
    }

    function addLocationSlot(rIndex) {
        const wrapper = $(`#locations_${rIndex}`);
        const locIndex = wrapper.find('.location-box').length;
        const currentUnit = $(`#unit_hidden_${rIndex}`).val() || 'Pcs';

        const slotHtml = `
            <div class="location-box d-flex align-items-center gap-2 mb-1.5" data-loc-index="${locIndex}">
                <select name="rows[rIndex][locations][${locIndex}][warehouse_id]" class="form-select form-select-sm border-secondary border-opacity-25 wh-dropdown" style="font-size: 0.85rem;">
                    ${getWarehouseOptionsHtml()}
                </select>
                <div class="input-group input-group-sm" style="max-width: 190px;">
                    <span class="input-group-text qty-addon-label px-2">QTY</span>
                    <input type="number" step="any" min="0" name="rows[${rIndex}][locations][${locIndex}][quantity]" 
                           class="form-control form-control-sm font-monospace fw-bold qty-input text-end" 
                           value="0" placeholder="0" oninput="calculateRowTotal(${rIndex})">
                    <span class="input-group-text qty-addon-label px-2 unit-label-${rIndex}">${currentUnit}</span>
                </div>
                <button type="button" class="btn btn-sm text-danger p-0 border-0 bg-transparent ms-1" onclick="$(this).closest('.location-box').remove(); calculateRowTotal(${rIndex});" title="Remove Location">
                    <i class="fas fa-times-circle"></i>
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
        // If user changes top Target Branch, update any untouched location dropdowns
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
