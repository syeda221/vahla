{{-- ===== QUICK ADD PRODUCT MODAL ===== --}}
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
                    {{-- Row 1: Product Name & Unit --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted mb-1">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" id="qap_product_name" required placeholder="Enter product name" style="height: 38px;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted mb-1">Unit <span class="text-danger">*</span></label>
                            <select class="form-select fw-semibold" name="size_mode" id="qap_unit" required style="height: 38px;">
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
                    </div>

                    {{-- Row 2: Category, Sub Category, Brand --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted mb-1">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="qap_category" required style="height: 38px;">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted mb-1">Sub Category</label>
                            <select class="form-select" name="sub_category_id" id="qap_subcategory" style="height: 38px;">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted mb-1">Brand <span class="text-danger">*</span></label>
                            <select class="form-select" name="brand_id" id="qap_brand" required style="height: 38px;">
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
                                            <input type="text" class="form-control form-control-sm fw-bold" name="variant_name[]" value="Base" required placeholder="Variant Name">
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

    // Modal shown event
    $('#quickAddProductModal').on('show.bs.modal', function() {
        loadQapDropdowns();
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
        
        // Reset rows to single Base row
        $('#qap_variant_rows').html(`
            <tr class="qap-variant-row bg-white rounded border-bottom">
                <td class="py-1">
                    <input type="text" class="form-control form-control-sm fw-bold" name="variant_name[]" value="Base" required placeholder="Variant Name">
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
    }

    // Submit Quick Add Product Form
    $('#quickAddProductForm').on('submit', function(e) {
        e.preventDefault();
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
