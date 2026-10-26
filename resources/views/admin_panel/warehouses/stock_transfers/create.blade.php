@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Zero horizontal scroll guarantee */
    html, body {
        max-width: 100vw;
        overflow-x: hidden;
    }
    .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .card-header {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            padding: 14px 16px !important;
        }
        .card-header a.btn {
            width: 100% !important;
            justify-content: center !important;
        }
        .card-body.p-4 {
            padding: 14px !important;
        }
        .table-responsive {
            overflow-x: visible !important;
            border: none !important;
        }
        #product_table,
        #product_table thead,
        #product_table tbody,
        #product_table tr.product_row,
        #product_table tr.product_row > td {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        #product_table thead {
            display: none !important;
        }
        #product_table tr.product_row {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 14px !important;
            margin-bottom: 14px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03) !important;
        }
        #product_table tr.product_row > td {
            padding: 6px 0 !important;
            border: none !important;
            text-align: left !important;
        }
        #product_table tr.product_row > td.td-desktop-action {
            display: none !important;
        }
        .bottom-actions-wrap {
            flex-direction: column !important;
            gap: 10px !important;
        }
        .action-btn-mobile {
            width: 100% !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    }

    @media (min-width: 992px) {
        .mobile-row-header {
            display: none !important;
        }
    }
</style>

<div class="container-fluid px-2 px-md-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary fw-bold">
                <i class="fa fa-exchange-alt me-2"></i> New Stock Transfer
            </h5>
            <a href="{{ route('stock_transfers.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-list me-1"></i> Transfer List
            </a>
        </div>
        <div class="card-body p-3 p-md-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('stock_transfers.store') }}" method="POST" id="stockTransferForm">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">From Warehouse <span class="text-danger">*</span></label>
                        <select name="from_warehouse_id" id="from_warehouse_id" class="form-select form-control" required>
                            <option value="">-- Select Source Warehouse --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->warehouse_name }} {{ $warehouse->location ? '('.$warehouse->location.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">To Destination <span class="text-danger" id="toWhRequired">*</span></label>
                        <select name="to_warehouse_id" id="to_warehouse_id" class="form-select form-control" required>
                            <option value="">-- Select Destination Warehouse --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->warehouse_name }} {{ $warehouse->location ? '('.$warehouse->location.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex flex-column justify-content-center">
                        <div class="p-2 p-md-0 rounded bg-light border border-md-0 bg-md-transparent">
                            <label class="form-label fw-bold text-muted mb-1" style="font-size: 0.85rem;">Shop Transfer</label>
                            <div class="form-check form-switch mt-1 d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" name="to_shop" value="1" id="toShop" {{ old('to_shop') ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.3em;">
                                <label class="form-check-label ms-2 fw-semibold" for="toShop" style="cursor: pointer;">To Shop</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <span class="fw-bold"><i class="fa fa-boxes me-1"></i> Products to Transfer</span>
                        <button type="button" class="btn btn-sm btn-primary" id="add_row_btn">
                            <i class="fa fa-plus me-1"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle text-center" id="product_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45%; text-align: left;" class="ps-3">Product / Variant</th>
                                        <th style="width: 20%;">Available Stock</th>
                                        <th style="width: 25%;">Transfer Qty</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product_body">
                                    <tr class="product_row">
                                        <td class="text-start ps-md-3">
                                            <!-- Mobile Card Top Bar -->
                                            <div class="mobile-row-header d-flex d-lg-none align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                                <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-size: 0.8rem;">
                                                    <i class="fa fa-box text-primary me-1"></i> Item #<span class="row-index">1</span>
                                                </span>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove Row">
                                                    <i class="fa fa-trash"></i> Remove
                                                </button>
                                            </div>

                                            <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.4px;">
                                                <i class="fa fa-box me-1 text-primary"></i> Product / Variant
                                            </label>
                                            <select name="product_id[]" class="form-select form-control product-select" required>
                                                <option value="">-- Select Source Warehouse First --</option>
                                            </select>
                                            <input type="hidden" name="variant_key[]" class="variant-key-input" value="">
                                            <input type="hidden" name="variant_name[]" class="variant-name-input" value="">
                                            <div class="invalid-feedback product-error" style="display:none; font-size: 0.82rem;">
                                                This item is already selected in another row.
                                            </div>
                                        </td>
                                        <td>
                                            <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.4px;">
                                                <i class="fa fa-layer-group me-1 text-secondary"></i> Available Stock
                                            </label>
                                            <input type="number" name="available_stock[]" class="form-control text-center stock" readonly placeholder="0">
                                            <small class="stock-unit-label text-muted d-block mt-1" style="font-size: 0.75rem;"></small>
                                        </td>
                                        <td>
                                            <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.4px;">
                                                <i class="fa fa-hashtag me-1 text-primary"></i> Transfer Quantity
                                            </label>
                                            <input type="number" name="quantity[]" class="form-control text-center quantity" required min="1" placeholder="Enter Qty">
                                            <div class="invalid-feedback stock-error text-start" style="display:none; font-size: 0.82rem;">
                                                Insufficient stock!
                                            </div>
                                        </td>
                                        <td class="td-desktop-action">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove Row">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Remarks / Notes</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="Optional transfer notes...">{{ old('remarks') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 bottom-actions-wrap">
                    <a href="{{ route('stock_transfers.index') }}" class="btn btn-secondary px-4 action-btn-mobile">Cancel</a>
                    <button type="submit" class="btn btn-success px-4 action-btn-mobile" id="submitBtn">
                        <i class="fa fa-check me-1"></i> Transfer Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // availableProducts: array of items (one per variant) returned by AJAX
    let availableProducts = [];

    function updateRowIndices() {
        $('#product_body tr.product_row').each(function(idx) {
            $(this).find('.row-index').text(idx + 1);
        });
    }

    // ─── Helper: unique key to detect duplicates (product_id + variant_key) ───
    function itemUniqueKey(p) {
        return p.id + '||' + (p.variant_key || '');
    }

    // ─── Helper: build <option> HTML for one product/variant entry ───────────
    function buildOption(p, selectedKey) {
        let uKey    = itemUniqueKey(p);
        let selAttr = (uKey === selectedKey) ? 'selected' : '';
        let code    = p.item_code ? ' [' + p.item_code + ']' : '';
        // Use display_stock (formatted "8.1") if available, else fall back to available_qty
        let stockVal = (p.display_stock !== undefined) ? p.display_stock : p.available_qty;
        let stock   = (stockVal !== undefined && stockVal !== '') ? ' (Stock: ' + stockVal + ')' : '';
        let label   = p.display_name || p.item_name;
        // Encode full item as data attribute so we can retrieve it on change
        let dataAttr = 'data-item=\'' + JSON.stringify(p).replace(/'/g, '&#39;') + '\'';
        return `<option value="${uKey}" ${selAttr} ${dataAttr}>${label}${code}${stock}</option>`;
    }

    // ─── Sync destination warehouse options (disable same as source) ─────────
    function syncWarehouseOptions() {
        let fromWh = $('#from_warehouse_id').val();
        let toWh   = $('#to_warehouse_id').val();

        $('#to_warehouse_id option').each(function() {
            let val = $(this).val();
            $(this).prop('disabled', val && val === fromWh);
        });

        if (toWh && toWh === fromWh) {
            $('#to_warehouse_id').val('');
        }
    }

    // ─── Toggle To-Shop mode ──────────────────────────────────────────────────
    function syncToShopState() {
        let isShop = $('#toShop').is(':checked');
        if (isShop) {
            $('#to_warehouse_id').val('').prop('disabled', true).prop('required', false);
            $('#toWhRequired').hide();
        } else {
            $('#to_warehouse_id').prop('disabled', false).prop('required', true);
            $('#toWhRequired').show();
            syncWarehouseOptions();
        }
        validateForm();
    }

    $('#toShop').on('change', syncToShopState);
    syncToShopState();

    // ─── Source warehouse change → fetch products/variants ───────────────────
    $('#from_warehouse_id').on('change', function() {
        let warehouseId = $(this).val();
        syncWarehouseOptions();

        if (!warehouseId) {
            availableProducts = [];
            updateAllProductDropdowns();
            return;
        }

        $('.product-select').html('<option value="">Loading products...</option>').prop('disabled', true);

        $.ajax({
            url: "{{ route('get.products.by.warehouse') }}",
            type: 'GET',
            data: { warehouse_id: warehouseId },
            success: function(data) {
                availableProducts = data || [];
                $('.product-select').prop('disabled', false);
                updateAllProductDropdowns();
            },
            error: function() {
                $('.product-select').prop('disabled', false);
                alert('Error fetching products for this warehouse. Please try again.');
            }
        });
    });

    // ─── Rebuild all dropdowns keeping selected value where possible ──────────
    function updateAllProductDropdowns() {
        $('.product-select').each(function() {
            let currentVal = $(this).val();  // unique key = "productId||variantKey"
            let options = '<option value="">-- Select Product --</option>';

            if (availableProducts.length === 0) {
                options = '<option value="">No stock available in this warehouse</option>';
            } else {
                availableProducts.forEach(function(p) {
                    options += buildOption(p, currentVal);
                });
            }

            $(this).html(options);

            // If previously-selected item no longer available, clear the row
            let stillAvailable = availableProducts.find(p => itemUniqueKey(p) === currentVal);
            if (!stillAvailable) {
                clearRowData($(this).closest('tr'));
            }
        });

        validateForm();
    }

    // ─── Clear stock/qty/hidden fields in a row ───────────────────────────────
    function clearRowData($row) {
        $row.find('.stock').val('');
        $row.find('.stock-unit-label').text('');
        $row.find('.quantity').val('').removeAttr('max');
        $row.find('.variant-key-input').val('');
        $row.find('.variant-name-input').val('');
    }

    // ─── Product/variant selected in a row ───────────────────────────────────
    $(document).on('change', '.product-select', function() {
        let $row     = $(this).closest('tr');
        let uKey     = $(this).val();   // "productId||variantKey"
        let fromWh   = $('#from_warehouse_id').val();

        if (!uKey || !fromWh) {
            clearRowData($row);
            validateForm();
            return;
        }

        // Retrieve the item object from the selected option's data attribute
        let selectedOption = $(this).find('option:selected');
        let itemData = null;
        try {
            itemData = JSON.parse(selectedOption.attr('data-item') || 'null');
        } catch(e) {}

        if (itemData) {
            // Populate hidden variant fields
            $row.find('.variant-key-input').val(itemData.variant_key || '');
            $row.find('.variant-name-input').val(itemData.variant_name || '');

            // Populate stock display — use display_stock ("8.1") for the visible input
            let displayStock = (itemData.display_stock !== undefined) ? itemData.display_stock : itemData.available_qty;
            let maxQty       = parseInt(itemData.available_qty) || 0;

            $row.find('.stock').val(displayStock);
            $row.find('.quantity').attr('max', maxQty);

            let ppb = parseFloat(itemData.pieces_per_box) || 1;
            if (ppb > 1 && ['by_cartons', 'by_bandal'].includes(itemData.size_mode)) {
                $row.find('.stock-unit-label').text('Boxes (' + ppb + ' pcs/box)');
            } else {
                $row.find('.stock-unit-label').text('Units / Pieces');
            }

            if (maxQty <= 0) {
                $row.find('.stock').addClass('text-danger fw-bold');
            } else {
                $row.find('.stock').removeClass('text-danger fw-bold');
            }
        } else {
            // Fallback: fetch from server (shouldn't normally happen)
            let parts    = uKey.split('||');
            let productId = parts[0];
            $.ajax({
                url: "{{ route('warehouse.stock.quantity') }}",
                method: 'GET',
                data: { warehouse_id: fromWh, product_id: productId },
                success: function(response) {
                    let available = parseInt(response.quantity) || 0;
                    if (available <= 0 && response.total_pieces > 0) {
                        available = parseInt(response.total_pieces) || 0;
                    }
                    $row.find('.stock').val(available);
                    $row.find('.quantity').attr('max', available);
                    if (response.ppb && response.ppb > 1) {
                        $row.find('.stock-unit-label').text('Boxes (' + response.ppb + ' pcs/box)');
                    } else {
                        $row.find('.stock-unit-label').text('Units / Pieces');
                    }
                    if (available <= 0) {
                        $row.find('.stock').addClass('text-danger fw-bold');
                    } else {
                        $row.find('.stock').removeClass('text-danger fw-bold');
                    }
                    validateForm();
                },
                error: function() {
                    $row.find('.stock').val('0');
                    $row.find('.quantity').removeAttr('max');
                    validateForm();
                }
            });
            return; // validateForm called inside ajax
        }

        validateForm();
    });

    // ─── Form Validation ──────────────────────────────────────────────────────
    function validateForm() {
        let isValid = true;
        let itemCounts = {};   // key = uniqueKey, count duplicates
        let fromWh   = $('#from_warehouse_id').val();
        let toWh     = $('#to_warehouse_id').val();
        let isShop   = $('#toShop').is(':checked');

        if (!fromWh) isValid = false;
        if (!isShop && (!toWh || toWh === fromWh)) isValid = false;

        // Count occurrences per unique item key
        $('.product-select').each(function() {
            let val = $(this).val();
            if (val) {
                itemCounts[val] = (itemCounts[val] || 0) + 1;
            } else {
                isValid = false;
            }
        });

        // Mark duplicates
        $('.product-select').each(function() {
            let val      = $(this).val();
            let $errDiv  = $(this).siblings('.product-error');
            if (val && itemCounts[val] > 1) {
                $(this).addClass('is-invalid');
                $errDiv.show();
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
                $errDiv.hide();
            }
        });

        // Quantity validation
        let hasAtLeastOne = false;
        $('.quantity').each(function() {
            let entered  = parseInt($(this).val()) || 0;
            let max      = parseInt($(this).attr('max')) || 0;
            let $errDiv  = $(this).siblings('.stock-error');

            if (entered > 0) hasAtLeastOne = true;
            else isValid = false;

            if (entered > max && max > 0) {
                $(this).addClass('is-invalid');
                $errDiv.text('Max available is ' + max).show();
                isValid = false;
            } else if (entered <= 0 && $(this).val() !== '') {
                $(this).addClass('is-invalid');
                $errDiv.text('Quantity must be at least 1').show();
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
                $errDiv.hide();
            }
        });

        if (!hasAtLeastOne) isValid = false;

        $('#submitBtn').prop('disabled', !isValid);
        return isValid;
    }

    $(document).on('input', '.quantity', validateForm);
    $('#from_warehouse_id, #to_warehouse_id').on('change', validateForm);

    // ─── Build a new empty row HTML ───────────────────────────────────────────
    function buildRowOptions() {
        if (availableProducts.length === 0) {
            return '<option value="">-- Select Source Warehouse First --</option>';
        }
        let opts = '<option value="">-- Select Product --</option>';
        availableProducts.forEach(function(p) {
            opts += buildOption(p, '');
        });
        return opts;
    }

    // ─── Add Row ──────────────────────────────────────────────────────────────
    $('#add_row_btn').on('click', function() {
        let rowCount = $('#product_body tr.product_row').length + 1;
        let row = `
            <tr class="product_row">
                <td class="text-start ps-md-3">
                    <!-- Mobile Card Top Bar -->
                    <div class="mobile-row-header d-flex d-lg-none align-items-center justify-content-between pb-2 mb-2 border-bottom">
                        <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-size: 0.8rem;">
                            <i class="fa fa-box text-primary me-1"></i> Item #<span class="row-index">${rowCount}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove Row">
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>

                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.4px;">
                        <i class="fa fa-box me-1 text-primary"></i> Product / Variant
                    </label>
                    <select name="product_id[]" class="form-select form-control product-select" required>
                        ${buildRowOptions()}
                    </select>
                    <input type="hidden" name="variant_key[]" class="variant-key-input" value="">
                    <input type="hidden" name="variant_name[]" class="variant-name-input" value="">
                    <div class="invalid-feedback product-error" style="display:none; font-size: 0.82rem;">
                        This item is already selected in another row.
                    </div>
                </td>
                <td>
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.4px;">
                        <i class="fa fa-layer-group me-1 text-secondary"></i> Available Stock
                    </label>
                    <input type="number" name="available_stock[]" class="form-control text-center stock" readonly placeholder="0">
                    <small class="stock-unit-label text-muted d-block mt-1" style="font-size: 0.75rem;"></small>
                </td>
                <td>
                    <label class="d-lg-none form-label small fw-bold text-muted mb-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.4px;">
                        <i class="fa fa-hashtag me-1 text-primary"></i> Transfer Quantity
                    </label>
                    <input type="number" name="quantity[]" class="form-control text-center quantity" required min="1" placeholder="Enter Qty">
                    <div class="invalid-feedback stock-error text-start" style="display:none; font-size: 0.82rem;">
                        Insufficient stock!
                    </div>
                </td>
                <td class="td-desktop-action">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove Row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#product_body').append(row);
        updateRowIndices();
        validateForm();
    });

    // ─── Remove Row ───────────────────────────────────────────────────────────
    $(document).on('click', '.remove-row', function() {
        if ($('#product_body tr').length > 1) {
            $(this).closest('tr').remove();
            updateRowIndices();
        } else {
            let $row = $(this).closest('tr');
            $row.find('select').val('');
            clearRowData($row);
            $row.find('.product-select').removeClass('is-invalid');
            $row.find('.product-error, .stock-error').hide();
        }
        validateForm();
    });

    // ─── Form submit guard ────────────────────────────────────────────────────
    $('#stockTransferForm').on('submit', function(e) {
        // Run validation first (uses uniqueKey values)
        if (!validateForm()) {
            e.preventDefault();
            alert('Please check all fields, ensure valid products and quantities are selected.');
            return false;
        }

        // Before submitting, inject hidden product_id[] inputs (integer only)
        // and disable the selects so their "productId||variantKey" values don't get sent
        $('.product-select').each(function() {
            let uKey = $(this).val();  // "productId||variantKey"
            if (uKey) {
                let productId = uKey.split('||')[0];
                // Create (or update) a hidden input for product_id[]
                let $pid = $(this).siblings('.pid-hidden');
                if ($pid.length === 0) {
                    $pid = $('<input type="hidden" class="pid-hidden">').insertAfter(this);
                }
                $pid.attr('name', 'product_id[]').val(productId);
            }
            // Rename the select so it won't submit its composite value
            $(this).attr('name', '_product_select_ignore[]');
        });
    });

    // ─── Pre-fill if warehouse was already chosen (old input on back) ─────────
    if ($('#from_warehouse_id').val()) {
        $('#from_warehouse_id').trigger('change');
    }
});
</script>
@endsection
