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
        padding: 12px 14px;
        border: none !important;
        background: var(--bg-page);
        border-bottom: 1px solid var(--border-color) !important;
    }
    .transfer-items-table td {
        padding: 10px 10px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        height: var(--height-table-row);
    }
    .readonly-stock-field {
        background-color: var(--bg-page) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
        font-weight: 600 !important;
        font-variant-numeric: tabular-nums;
        font-size: 13px !important;
        cursor: not-allowed;
        text-align: center;
    }
    .btn-add-item-dashed {
        border: 1px dashed var(--border-color) !important;
        border-radius: var(--radius-md);
        background: var(--bg-card);
        color: var(--text-primary);
        font-weight: 500;
        font-size: 13px;
        padding: 10px 16px;
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
        width: 36px;
        height: 36px;
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
            <button type="button" class="btn-close ms-auto" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--success) !important;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-dismiss="alert" aria-label="Close"></button>
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
                <div class="d-flex align-items-center mb-3">
                    <span class="step-num-badge">2</span>
                    <span class="step-title">Items to Transfer</span>
                    <span class="step-subtitle">Select product — choose source location — enter quantity</span>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table erp-table transfer-items-table mb-0" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 40%;">PRODUCT / ITEM</th>
                                <th style="width: 28%;">TRANSFER FROM (SOURCE)</th>
                                <th style="width: 14%;" class="text-center">AVAILABLE QTY</th>
                                <th style="width: 14%;" class="text-center">TRANSFER QTY</th>
                                <th style="width: 4%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Row 0 -->
                            <tr class="item-transfer-row" data-row="0">
                                <td>
                                    <select name="items[0][product_id]" class="form-select erp-input product-select select2-init" required>
                                        <option value="">-- Choose Product to Transfer --</option>
                                        @foreach($products as $p)
                                            @php
                                                $uName = $p->unit_name ?? ($p->unit->name ?? 'Pcs');
                                            @endphp
                                            <option value="{{ $p->id }}" data-unit="{{ $uName }}">
                                                {{ $p->item_name }} ({{ $p->item_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="items[0][from_warehouse_id]" class="form-select erp-input source-warehouse-select" required>
                                        <option value="">Select source...</option>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" data-is-shop="{{ $w->is_shop }}">
                                                {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control erp-input readonly-stock-field available-qty-display" value="—" readonly>
                                </td>
                                <td>
                                    <input type="number" step="any" min="0.001" name="items[0][quantity]" class="form-control erp-input transfer-qty-input text-center font-tabular" placeholder="0.00" required>
                                    <div class="invalid-feedback qty-error-msg small text-danger" style="display:none;"></div>
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
                    <i class="fas fa-plus me-1.5"></i> Add Another Item
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
                                <span>📦 Warehouse</span>
                            </label>
                            <label class="dest-type-card" id="cardDestShop">
                                <input type="radio" name="destination_type" value="shop" class="form-check-input">
                                <span>🏪 Branch Shop</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">
                        <label class="form-field-label">
                            Destination Warehouse <span class="text-danger">*</span>
                        </label>
                        <select name="to_warehouse_id" id="to_warehouse_id" class="form-select erp-input" required>
                            <option value="">— Select Warehouse —</option>
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

                <textarea name="remarks" class="form-control erp-input" rows="3" style="height: auto !important; min-height: 80px;" placeholder="Transfer reason, instructions, or any internal notes..."></textarea>
            </div>

            <!-- SUBMIT & SUMMARY FOOTER -->
            <div class="p-4 bg-light rounded-bottom d-flex align-items-center justify-content-between flex-wrap gap-3" style="border-top: 1px solid var(--border-color);">
                <div class="d-flex align-items-center gap-3">
                    <div class="px-3 py-1.5 rounded erp-card bg-white">
                        <span class="erp-caption fw-semibold text-uppercase">Items Count:</span>
                        <strong class="text-dark ms-1.5 font-tabular" id="summaryItemsCount">1 Item</strong>
                    </div>
                    <div class="px-3 py-1.5 rounded erp-card bg-white">
                        <span class="erp-caption fw-semibold text-uppercase">Total Qty:</span>
                        <strong class="text-dark ms-1.5 font-tabular" id="summaryTotalQty">0.00</strong>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('stock_transfers.index') }}" class="erp-btn-secondary">
                        Cancel
                    </a>
                    <!-- Sole Primary Button in this form -->
                    <button type="submit" id="btnSubmitTransfer" class="erp-btn-primary">
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

        // Initialize Select2 on dropdowns without ugly clear icon
        function initSelect2(element) {
            if ($.fn.select2) {
                $(element).select2({
                    width: '100%',
                    placeholder: '-- Choose Product to Transfer --',
                    allowClear: false
                });
            }
        }

        $('.select2-init').each(function() {
            initSelect2(this);
        });

        // 1. Destination Type Toggle (Warehouse vs Branch Shop)
        $('input[name="destination_type"]').on('change', function() {
            const selectedType = $(this).val();

            if (selectedType === 'warehouse') {
                $('#cardDestWarehouse').addClass('active');
                $('#cardDestShop').removeClass('active');
                // Show Godowns, hide shops
                $('#to_warehouse_id option.godown-opt').removeClass('d-none');
                $('#to_warehouse_id option.shop-opt').addClass('d-none');
                $('#to_warehouse_id').val('');
            } else {
                $('#cardDestShop').addClass('active');
                $('#cardDestWarehouse').removeClass('active');
                // Show Shops, hide Godowns
                $('#to_warehouse_id option.shop-opt').removeClass('d-none');
                $('#to_warehouse_id option.godown-opt').addClass('d-none');
                
                // Auto select first shop if available
                const firstShop = $('#to_warehouse_id option.shop-opt:first').val();
                if (firstShop) {
                    $('#to_warehouse_id').val(firstShop);
                }
            }
        });

        // Trigger on load
        $('input[name="destination_type"]:checked').trigger('change');

        // 2. Fetch Available Sources when a Product is Selected
        $(document).on('change', '.product-select', function() {
            const $row = $(this).closest('.item-transfer-row');
            const productId = $(this).val();
            const $sourceSelect = $row.find('.source-warehouse-select');
            const $availInput = $row.find('.available-qty-display');
            const $transferInput = $row.find('.transfer-qty-input');

            if (!productId) {
                $sourceSelect.html('<option value="">Select source...</option>');
                $availInput.val('—').removeData('avail');
                $transferInput.val('');
                updateSummary();
                return;
            }

            $sourceSelect.html('<option value="">Loading sources...</option>').prop('disabled', true);
            $availInput.val('Loading...');

            $.ajax({
                url: "{{ url('/stock-transfers/product-sources') }}/" + productId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $sourceSelect.prop('disabled', false).empty();
                    $sourceSelect.append('<option value="">Select source...</option>');

                    let firstStockWhId = null;

                    if (response.sources && response.sources.length > 0) {
                        response.sources.forEach(function(src) {
                            const icon = src.is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ';
                            const stockText = src.has_stock ? `(Avail: ${src.formatted_qty} ${src.unit_name})` : '(Out of Stock)';
                            const disabled = src.has_stock ? '' : 'disabled';
                            
                            $sourceSelect.append(`
                                <option value="${src.warehouse_id}" 
                                        data-avail="${src.available_pieces}" 
                                        data-unit="${src.unit_name}" 
                                        ${disabled}>
                                    ${icon} ${src.warehouse_name} ${stockText}
                                </option>
                            `);

                            if (src.has_stock && !firstStockWhId) {
                                firstStockWhId = src.warehouse_id;
                            }
                        });
                    }

                    // Auto-select the first warehouse with available stock
                    if (firstStockWhId) {
                        $sourceSelect.val(firstStockWhId).trigger('change');
                    } else {
                        $availInput.val('0 (Out of stock)');
                    }
                },
                error: function() {
                    $sourceSelect.prop('disabled', false).html('<option value="">Error loading sources</option>');
                    $availInput.val('—');
                }
            });
        });

        // 3. Update Available Qty when Source Warehouse changes
        $(document).on('change', '.source-warehouse-select', function() {
            const $row = $(this).closest('.item-transfer-row');
            const $selectedOption = $(this).find('option:selected');
            const availPieces = parseFloat($selectedOption.data('avail')) || 0;
            const unitName = $selectedOption.data('unit') || 'Pcs';
            const $availInput = $row.find('.available-qty-display');

            if ($(this).val()) {
                $availInput.val(`${availPieces} ${unitName}`).data('avail', availPieces);
            } else {
                $availInput.val('—').removeData('avail');
            }

            validateRowQty($row);
            updateSummary();
        });

        // 4. Validate Transfer Qty Input
        function validateRowQty($row) {
            const $transferInput = $row.find('.transfer-qty-input');
            const $errorDiv = $row.find('.qty-error-msg');
            const enteredQty = parseFloat($transferInput.val()) || 0;
            const availQty = parseFloat($row.find('.available-qty-display').data('avail')) || 0;
            const sourceWh = $row.find('.source-warehouse-select').val();

            if (enteredQty <= 0) {
                $transferInput.removeClass('is-invalid');
                $errorDiv.hide();
                return true;
            }

            if (sourceWh && enteredQty > availQty) {
                $transferInput.addClass('is-invalid');
                $errorDiv.text(`Max: ${availQty}`).show();
                return false;
            } else {
                $transferInput.removeClass('is-invalid');
                $errorDiv.hide();
                return true;
            }
        }

        $(document).on('input', '.transfer-qty-input', function() {
            validateRowQty($(this).closest('.item-transfer-row'));
            updateSummary();
        });

        // 5. Update Bottom Summary
        function updateSummary() {
            const rowCount = $('.item-transfer-row').length;
            let totalQty = 0;

            $('.transfer-qty-input').each(function() {
                totalQty += (parseFloat($(this).val()) || 0);
            });

            $('#summaryItemsCount').text(`${rowCount} Item${rowCount > 1 ? 's' : ''}`);
            $('#summaryTotalQty').text(totalQty.toFixed(2));
        }

        // 6. Add Another Item (Dashed Button)
        $('#btnAddRow').on('click', function() {
            const newIndex = rowCounter++;
            const $firstRow = $('.item-transfer-row:first');

            // Build fresh clean row
            let productOptions = '<option value="">-- Choose Product to Transfer --</option>';
            $firstRow.find('.product-select option').each(function() {
                if ($(this).val()) {
                    productOptions += `<option value="${$(this).val()}" data-unit="${$(this).data('unit') || ''}">${$(this).text()}</option>`;
                }
            });

            const newRowHtml = `
                <tr class="item-transfer-row" data-row="${newIndex}">
                    <td>
                        <select name="items[${newIndex}][product_id]" class="form-select erp-input product-select" required>
                            ${productOptions}
                        </select>
                    </td>
                    <td>
                        <select name="items[${newIndex}][from_warehouse_id]" class="form-select erp-input source-warehouse-select" required>
                            <option value="">Select source...</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}" data-is-shop="{{ $w->is_shop }}">
                                    {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control erp-input readonly-stock-field available-qty-display" value="—" readonly>
                    </td>
                    <td>
                        <input type="number" step="any" min="0.001" name="items[${newIndex}][quantity]" class="form-control erp-input transfer-qty-input text-center font-tabular" placeholder="0.00" required>
                        <div class="invalid-feedback qty-error-msg small text-danger" style="display:none;"></div>
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
            initSelect2($newRow.find('.product-select'));
            updateSummary();
        });

        // 7. Remove Row
        $(document).on('click', '.btn-remove-row', function() {
            if ($('.item-transfer-row').length > 1) {
                $(this).closest('.item-transfer-row').remove();
            } else {
                // Reset first row instead of deleting
                const $row = $(this).closest('.item-transfer-row');
                $row.find('.product-select').val('').trigger('change');
                $row.find('.source-warehouse-select').html('<option value="">Select source...</option>');
                $row.find('.available-qty-display').val('—');
                $row.find('.transfer-qty-input').val('');
            }
            updateSummary();
        });

        // 8. Prevent Submitting Same Source & Destination
        $('#stockTransferForm').on('submit', function(e) {
            const destWh = $('#to_warehouse_id').val();
            let hasError = false;
            let errorMessage = '';

            if (!destWh) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Destination Missing',
                    text: 'Please select a destination warehouse or branch shop.'
                });
                return false;
            }

            $('.item-transfer-row').each(function(i) {
                const srcWh = $(this).find('.source-warehouse-select').val();
                const pId   = $(this).find('.product-select').val();
                const pName = $(this).find('.product-select option:selected').text().trim();
                const qty   = parseFloat($(this).find('.transfer-qty-input').val()) || 0;
                const avail = parseFloat($(this).find('.available-qty-display').data('avail')) || 0;

                if (!pId) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Please select a product.`;
                    return false;
                }

                if (!srcWh) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Please select a source warehouse for ${pName}.`;
                    return false;
                }

                if (srcWh == destWh) {
                    hasError = true;
                    errorMessage = `Row #${i + 1}: Source and Destination locations cannot be the same!`;
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

            $('#btnSubmitTransfer').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1.5"></i> Transferring...');
        });
    });
</script>
@endsection
