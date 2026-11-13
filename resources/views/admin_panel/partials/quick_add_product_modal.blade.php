{{-- ===== QUICK ADD PRODUCT MODAL ===== --}}
@php
    $modalItemCodeSeries = $itemCodeSeries ?? \App\Models\ItemCodeSeries::all();
    if ($modalItemCodeSeries->isEmpty()) {
        $modalItemCodeSeries = collect([
            (object)['id' => 1, 'prefix' => 'PRD-', 'next_number' => 1, 'is_default' => 1]
        ]);
    }
@endphp

<style>
    /* Quick Add Product Modal Custom Styles */
    #quickAddProductModal .modal-content {
        border: none !important;
        border-radius: 12px !important;
        overflow: hidden;
    }
    #quickAddProductModal .form-control,
    #quickAddProductModal .form-select {
        height: 36px !important;
        font-size: 0.85rem !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
    }
    #quickAddProductModal .form-control:focus,
    #quickAddProductModal .form-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
    }

    /* Unified Item Code Series Wrapper */
    #quickAddProductModal .qap-code-wrapper {
        display: flex !important;
        align-items: stretch !important;
        width: 100% !important;
        height: 36px !important;
        border-radius: 6px !important;
        overflow: hidden !important;
        border: 1px solid #0284c7 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }
    #quickAddProductModal .qap-code-wrapper.has-error {
        border-color: #ef4444 !important;
    }

    /* Series Dropdown */
    #quickAddProductModal .qap-series-select {
        width: 85px !important;
        min-width: 80px !important;
        max-width: 95px !important;
        height: 100% !important;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 22px 0 10px !important;
        cursor: pointer !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.5' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-position: right 7px center !important;
        background-repeat: no-repeat !important;
        background-size: 10px 10px !important;
        outline: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    #quickAddProductModal .qap-series-select option {
        background-color: #ffffff !important;
        color: #0f172a !important;
        font-weight: 600 !important;
    }

    /* Code Number Input */
    #quickAddProductModal .qap-code-input {
        flex: 1 1 auto !important;
        height: 100% !important;
        border: none !important;
        border-radius: 0 !important;
        border-left: 1px solid #bae6fd !important;
        border-right: 1px solid #e2e8f0 !important;
        background-color: #f8fafc !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 0.86rem !important;
        text-align: center !important;
        padding: 0 8px !important;
        outline: none !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    #quickAddProductModal .qap-code-input:not([readonly]) {
        background-color: #ffffff !important;
    }
    #quickAddProductModal .qap-code-input.is-invalid {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
    }

    /* Action Buttons (Edit & Add) */
    #quickAddProductModal .qap-code-btn {
        width: 36px !important;
        min-width: 36px !important;
        height: 100% !important;
        background: #f8fafc !important;
        color: #475569 !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.82rem !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        outline: none !important;
        margin: 0 !important;
    }
    #quickAddProductModal .qap-code-btn:hover {
        background: #e2e8f0 !important;
        color: #0284c7 !important;
    }
    #quickAddProductModal .qap-code-btn-add {
        border-left: 1px solid #e2e8f0 !important;
        color: #0284c7 !important;
        font-size: 1.15rem !important;
        font-weight: 700 !important;
    }
    #quickAddProductModal .qap-code-btn-add:hover {
        background: #0284c7 !important;
        color: #ffffff !important;
    }
</style>

<div class="modal fade" id="quickAddProductModal" tabindex="-1" aria-labelledby="quickAddProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            {{-- Header --}}
            <div class="modal-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background-color: #2563eb !important;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="quickAddProductModalLabel" style="font-size: 1.15rem;">
                        <i class="fas fa-box-open"></i> Quick Add Product
                    </h5>
                    <small class="text-white-50" style="font-size: 0.82rem;">Create a new product with variants &amp; add to this sale</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="quickAddProductForm" autocomplete="off">
                @csrf
                <div class="modal-body p-4">
                    {{-- Row 1: Product Name & Item Code Series --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-muted mb-1">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" id="qap_product_name" required placeholder="Enter product name" style="height: 36px;">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted mb-1">Item Code Series</label>
                            <div class="qap-code-wrapper">
                                <select class="qap-series-select" name="item_code_prefix" id="qap_item_code_prefix" title="Choose series">
                                    @foreach($modalItemCodeSeries as $series)
                                        <option value="{{ $series->prefix }}" data-next="{{ $series->prefix }}{{ str_pad($series->next_number, 4, '0', STR_PAD_LEFT) }}" {{ $series->is_default ? 'selected' : '' }}>{{ trim($series->prefix, '-') }}</option>
                                    @endforeach
                                </select>
                                <input type="text" class="qap-code-input" id="qap_item_code_number" name="item_code_number" readonly placeholder="Auto">
                                <button class="qap-code-btn" type="button" id="qap_btn_edit_code" title="Edit Number">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="qap-code-btn qap-code-btn-add" type="button" id="qap_btn_add_series" title="Add Series">+</button>
                            </div>
                            <div id="qap_code_feedback" class="text-danger small mt-1 fw-bold" style="display:none; font-size: 11px;"></div>
                        </div>
                    </div>

                    {{-- Row 2: Unit, Category, Sub Category, Brand --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <label class="form-label fw-bold small text-muted mb-1">Unit <span class="text-danger">*</span></label>
                            <select class="form-select fw-semibold" name="size_mode" id="qap_unit" required style="height: 36px;">
                                <option value="by_pieces" data-unit-name="Pcs" selected>Pcs</option>
                                <option value="by_cartons" data-unit-name="Carton">Carton</option>
                                <option value="by_meter" data-unit-name="Meter">Meter</option>
                                <option value="by_feet" data-unit-name="Ft">Ft (Feet)</option>
                                <option value="by_kg" data-unit-name="Kg">Kg</option>
                                <option value="by_gm" data-unit-name="Gm">Gm</option>
                                <option value="by_ton" data-unit-name="Ton">Ton</option>
                            </select>
                            <input type="hidden" name="unit" id="qap_unit_text" value="Pcs">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label fw-bold small text-muted mb-1">Category</label>
                            <select class="form-select" name="category_id" id="qap_category" style="height: 36px;">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label fw-bold small text-muted mb-1">Sub Category</label>
                            <select class="form-select" name="sub_category_id" id="qap_subcategory" style="height: 36px;">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label fw-bold small text-muted mb-1">Brand</label>
                            <select class="form-select" name="brand_id" id="qap_brand" style="height: 36px;">
                                <option value="">Select Brand</option>
                            </select>
                        </div>
                    </div>

                    {{-- Variant Details Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-dark mb-0" style="font-size: 0.95rem;">Variant Details</label>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1 rounded-2" id="qap_add_variant_btn">
                            <i class="fas fa-plus me-1"></i> Add Variant
                        </button>
                    </div>

                    {{-- Variant Table Container --}}
                    <div class="border rounded-2 p-2 bg-light-subtle mb-2">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0" id="qap_variants_table">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <th style="min-width: 170px; width: 32%;">VARIANT NAME <span class="text-danger">*</span></th>
                                        <th style="min-width: 90px; width: 16%;" class="text-center">SIZE</th>
                                        <th style="min-width: 90px; width: 16%;" class="text-center">PURCHASE</th>
                                        <th style="min-width: 90px; width: 16%;" class="text-center">SALE</th>
                                        <th style="min-width: 90px; width: 16%;" class="text-center">STOCK (<span id="qap_stock_unit_label">Pcs</span>)</th>
                                        <th style="width: 50px;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="qap_variant_rows">
                                    {{-- Base Row (Default) --}}
                                    <tr class="qap-variant-row bg-white rounded border-bottom">
                                        <td class="py-1">
                                            <input type="text" class="form-control form-control-sm fw-bold qap-base-variant-name" name="variant_name[]" value="" required placeholder="Variant Name">
                                        </td>
                                        <td class="py-1">
                                            <input type="text" class="form-control form-control-sm text-center" name="variant_size[]" placeholder="Size">
                                        </td>
                                        <td class="py-1">
                                            <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_purchase_price[]" value="0">
                                        </td>
                                        <td class="py-1">
                                            <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_sale_price[]" value="0">
                                        </td>
                                        <td class="py-1">
                                            <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_stock[]" value="0">
                                        </td>
                                        <td class="py-1 text-center">
                                            <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.72rem;">Base</span>
                                            <input type="hidden" name="variant_is_base[]" value="1">
                                            <input type="hidden" name="variant_conv_factor[]" value="1">
                                            <input type="hidden" class="qap-row-unit" name="variant_unit[]" value="Pcs">
                                            <input type="hidden" name="variant_color[]" value="-">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Barcode Auto-Generated Notice --}}
                    <div class="d-flex align-items-center gap-2 text-muted small mt-2" style="font-size: 0.8rem;">
                        <i class="fas fa-barcode"></i> Barcode is auto-generated automatically, same as the Create Product page.
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center border-top">
                    <div>
                        <a href="{{ route('store') }}" target="_blank" class="text-primary text-decoration-none fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.85rem;">
                            <i class="fas fa-external-link-alt"></i> Open full product form
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light px-3 py-2 fw-semibold" data-bs-dismiss="modal" data-dismiss="modal" style="font-size: 0.88rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" id="btnQuickSaveProduct" style="background-color: #2563eb !important; font-size: 0.88rem;">
                            <i class="fas fa-check-circle"></i> Save and add to sale
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // --- Load Categories, Brands & Subcategories ---
    function loadQapDropdowns() {
        var $catSelect = $('#qap_category');
        var $brandSelect = $('#qap_brand');
        var $subCatSelect = $('#qap_subcategory');

        if ($catSelect.find('option').length <= 1) {
            $.get("{{ url('/get-categories') }}", function(data) {
                (data || []).forEach(function(cat) {
                    $catSelect.append('<option value="'+ cat.id +'">'+ cat.name +'</option>');
                });
            });
        }

        if ($brandSelect.find('option').length <= 1) {
            $.get("{{ url('/get-brands') }}", function(data) {
                (data || []).forEach(function(brand) {
                    $brandSelect.append('<option value="'+ brand.id +'">'+ brand.name +'</option>');
                });
            });
        }
    }

    // Auto-sync product name to base variant name
    var isBaseVariantNameManuallyEdited = false;

    $(document).on('input propertychange paste', '#qap_product_name', function() {
        var val = $(this).val();
        if (!isBaseVariantNameManuallyEdited) {
            $('.qap-base-variant-name').val(val);
        }
    });

    $(document).on('input propertychange paste', '.qap-base-variant-name', function() {
        if ($(this).val().trim() === '') {
            isBaseVariantNameManuallyEdited = false;
            $(this).val($('#qap_product_name').val());
        } else {
            isBaseVariantNameManuallyEdited = true;
        }
    });

    // Update Item Code Preview when Prefix changes or on modal open
    function updateQapNextNumber() {
        var s = document.getElementById('qap_item_code_prefix');
        if (!s) return;
        var opt = s.options[s.selectedIndex];
        if (opt) {
            var nextVal = opt.getAttribute('data-next') || '';
            $('#qap_item_code_number').val(nextVal).attr('readonly', 'readonly');
            $('#qap_item_code_number').removeClass('is-invalid');
            $('.qap-code-wrapper').removeClass('has-error');
            $('#qap_code_feedback').hide().empty();
        }
    }

    $(document).on('change', '#qap_item_code_prefix', function() {
        updateQapNextNumber();
    });

    $(document).on('click', '#qap_btn_edit_code', function() {
        $('#qap_item_code_number').removeAttr('readonly').focus();
    });

    // Real-time duplicate check for Item Code (only when user actively modifies the code)
    var qapCheckCodeTimer = null;
    function checkQapItemCodeAvailability() {
        clearTimeout(qapCheckCodeTimer);
        qapCheckCodeTimer = setTimeout(function() {
            var input = document.getElementById('qap_item_code_number');
            var feedback = document.getElementById('qap_code_feedback');
            var prefixSelect = document.getElementById('qap_item_code_prefix');
            var wrapper = document.querySelector('.qap-code-wrapper');
            if (!input || !feedback) return;
            var code = input.value.trim();
            var prefix = prefixSelect ? prefixSelect.value : '';

            if (!code) {
                feedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Item code cannot be empty';
                $(feedback).show();
                $(input).addClass('is-invalid');
                if (wrapper) wrapper.classList.add('has-error');
                return;
            }

            $.ajax({
                url: "{{ route('products.check_item_code') }}",
                type: "GET",
                data: {
                    code: code,
                    prefix: prefix
                },
                success: function(res) {
                    if (res.exists) {
                        feedback.innerHTML = '<i class="fas fa-times-circle"></i> ' + res.message;
                        $(feedback).show();
                        $(input).addClass('is-invalid');
                        if (wrapper) wrapper.classList.add('has-error');
                    } else {
                        $(feedback).hide().empty();
                        $(input).removeClass('is-invalid');
                        if (wrapper) wrapper.classList.remove('has-error');
                    }
                }
            });
        }, 250);
    }

    $(document).on('input propertychange paste change', '#qap_item_code_number', function() {
        checkQapItemCodeAvailability();
    });

    // Add New Series Button Click
    $(document).on('click', '#qap_btn_add_series', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Add New Series',
                input: 'text',
                inputLabel: 'Prefix (e.g. PRD-)',
                inputPlaceholder: 'e.g. PRD-',
                showCancelButton: true,
                confirmButtonText: 'Save Series',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                showLoaderOnConfirm: true,
                preConfirm: (prefix) => {
                    if (!prefix || !prefix.trim()) {
                        Swal.showValidationMessage('Prefix is required');
                        return false;
                    }
                    return $.ajax({
                        url: "{{ route('item-code-series.store') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            prefix: prefix.trim()
                        },
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(response => {
                        return response;
                    }).catch(error => {
                        var msg = (error.responseJSON && error.responseJSON.message) ? error.responseJSON.message : 'Error creating series';
                        Swal.showValidationMessage(msg);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value && result.value.series) {
                    var s = result.value.series;
                    var opt = new Option(s.label, s.prefix, true, true);
                    $(opt).attr('data-next', s.formatted_next);
                    $('#qap_item_code_prefix').append(opt).val(s.prefix);
                    updateQapNextNumber();
                    Swal.fire({
                        icon: 'success',
                        title: 'Series Added!',
                        text: 'Series ' + s.prefix + ' created successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        } else {
            var prefix = prompt("Enter Prefix (e.g. PRD-):");
            if (prefix && prefix.trim()) {
                $.ajax({
                    url: "{{ route('item-code-series.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        prefix: prefix.trim()
                    },
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    success: function(res) {
                        if (res.series) {
                            var s = res.series;
                            var opt = new Option(s.label, s.prefix, true, true);
                            $(opt).attr('data-next', s.formatted_next);
                            $('#qap_item_code_prefix').append(opt).val(s.prefix);
                            updateQapNextNumber();
                        }
                    },
                    error: function(err) {
                        var msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Error creating series';
                        alert(msg);
                    }
                });
            }
        }
    });

    // Modal shown event
    $('#quickAddProductModal').on('show.bs.modal', function() {
        loadQapDropdowns();
        updateQapNextNumber();
        if ($('#qap_product_name').val()) {
            $('.qap-base-variant-name').val($('#qap_product_name').val());
        }
    });

    // Subcategory loading when category changes
    $('#qap_category').on('change', function() {
        var categoryId = $(this).val();
        var $subCatSelect = $('#qap_subcategory');
        $subCatSelect.html('<option value="">Select Sub Category</option>');
        
        if (categoryId) {
            $.get("{{ url('/get-subcategories') }}/" + categoryId, function(data) {
                (data || []).forEach(function(sub) {
                    $subCatSelect.append('<option value="'+ sub.id +'">'+ sub.name +'</option>');
                });
            });
        }
    });

    // Unit Change Handling
    $('#qap_unit').on('change', function() {
        var unitName = $(this).find('option:selected').data('unit-name') || 'Pcs';
        $('#qap_unit_text').val(unitName);
        $('#qap_stock_unit_label').text(unitName);
        $('.qap-row-unit').val(unitName);
    });

    // Add Variant Row
    $('#qap_add_variant_btn').on('click', function() {
        var unitName = $('#qap_unit option:selected').data('unit-name') || 'Pcs';
        var rowHtml = `
            <tr class="qap-variant-row bg-white rounded border-bottom">
                <td class="py-1">
                    <input type="text" class="form-control form-control-sm" name="variant_name[]" placeholder="e.g. XL / Red" required>
                </td>
                <td class="py-1">
                    <input type="text" class="form-control form-control-sm text-center" name="variant_size[]" placeholder="Size">
                </td>
                <td class="py-1">
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_purchase_price[]" value="0">
                </td>
                <td class="py-1">
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_sale_price[]" value="0">
                </td>
                <td class="py-1">
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_stock[]" value="0">
                </td>
                <td class="py-1 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 qap-remove-variant-row" title="Remove">
                        <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                    </button>
                    <input type="hidden" name="variant_is_base[]" value="0">
                    <input type="hidden" name="variant_conv_factor[]" value="1">
                    <input type="hidden" class="qap-row-unit" name="variant_unit[]" value="${unitName}">
                    <input type="hidden" name="variant_color[]" value="-">
                </td>
            </tr>
        `;
        $('#qap_variant_rows').append(rowHtml);
    });

    // Remove Variant Row
    $(document).on('click', '.qap-remove-variant-row', function() {
        $(this).closest('tr').remove();
    });

    // Reset Modal Form to Default
    function resetQapForm() {
        $('#quickAddProductForm')[0].reset();
        var defaultUnit = 'Pcs';
        $('#qap_unit').val('by_pieces');
        $('#qap_unit_text').val(defaultUnit);
        $('#qap_stock_unit_label').text(defaultUnit);
        isBaseVariantNameManuallyEdited = false;
        
        // Reset rows to single Base row
        $('#qap_variant_rows').html(`
            <tr class="qap-variant-row bg-white rounded border-bottom">
                <td class="py-1">
                    <input type="text" class="form-control form-control-sm fw-bold qap-base-variant-name" name="variant_name[]" value="" required placeholder="Variant Name">
                </td>
                <td class="py-1">
                    <input type="text" class="form-control form-control-sm text-center" name="variant_size[]" placeholder="Size">
                </td>
                <td class="py-1">
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_purchase_price[]" value="0">
                </td>
                <td class="py-1">
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_sale_price[]" value="0">
                </td>
                <td class="py-1">
                    <input type="number" step="any" min="0" class="form-control form-control-sm text-center" name="variant_stock[]" value="0">
                </td>
                <td class="py-1 text-center">
                    <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.72rem;">Base</span>
                    <input type="hidden" name="variant_is_base[]" value="1">
                    <input type="hidden" name="variant_conv_factor[]" value="1">
                    <input type="hidden" class="qap-row-unit" name="variant_unit[]" value="${defaultUnit}">
                    <input type="hidden" name="variant_color[]" value="-">
                </td>
            </tr>
        `);

        updateQapNextNumber();
    }

    // Submit Quick Add Product Form
    $('#quickAddProductForm').on('submit', function(e) {
        e.preventDefault();

        // Check if item code is marked as duplicate/invalid
        if ($('#qap_item_code_number').hasClass('is-invalid') || $('.qap-code-wrapper').hasClass('has-error')) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Item Code',
                    text: 'The selected item code already exists. Please choose a different number or series.'
                });
            } else {
                alert('The selected item code already exists. Please choose a different number or series.');
            }
            return false;
        }

        var $btn = $('#btnQuickSaveProduct');
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: "{{ route('store-product') }}",
            method: "POST",
            data: $(this).serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(response) {
                $btn.prop('disabled', false).html(originalHtml);

                // Hide Modal
                if (typeof $('#quickAddProductModal').modal === 'function') {
                    $('#quickAddProductModal').modal('hide');
                } else {
                    $('#quickAddProductModal').removeClass('show').hide();
                    $('.modal-backdrop').remove();
                }

                // Reset Form
                resetQapForm();

                // Success Notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Created!',
                        text: response.message || 'Product created and added to sale.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                } else {
                    alert(response.message || 'Product created successfully!');
                }

                // Add to Sale Grid
                if (response.product && typeof window.addProductToSaleGrid === 'function') {
                    window.addProductToSaleGrid(response.product);
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                var msg = 'Error adding product.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: msg
                    });
                } else {
                    alert('Error: ' + msg.replace(/<br>/g, '\n'));
                }
            }
        });
    });
});
</script>
@endpush
