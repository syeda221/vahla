{{-- ===== QUICK BUILD PRODUCT MODAL (variant-aware; reuses Create Product backend) ===== --}}
<style>
    .qb-modal .modal-content {
        border-radius: 14px;
        overflow: hidden;
    }
    .qb-modal .qb-head {
        background: linear-gradient(120deg, #2563EB 0%, #1D4ED8 100%);
        padding: 14px 18px;
    }
    .qb-modal .qb-head h5 {
        font-size: 15px;
    }
    .qb-modal .qb-head .qb-sub {
        font-size: 11.5px;
        opacity: .85;
    }
    .qb-modal .form-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
    }
    .qb-modal .form-control,
    .qb-modal .form-select {
        height: 38px;
        font-size: 13px;
        border-radius: 8px;
        border-color: #C7D0DA;
    }
    .qb-modal .form-control:focus,
    .qb-modal .form-select:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 .15rem rgba(37, 99, 235, .12);
    }
    /* Variants grid */
    .qb-vhead,
    .qb-row {
        display: grid;
        grid-template-columns: 1fr 78px 104px 104px 86px 40px;
        gap: 6px;
        align-items: center;
    }
    #qbGrid.carton .qb-vhead,
    #qbGrid.carton .qb-row {
        grid-template-columns: 1fr 62px 74px 96px 96px 76px 40px;
    }
    #qbGrid.carton .qb-ctn-col {
        display: block;
    }
    .qb-ctn-col {
        display: none;
    }
    #qbGrid.carton .qb-ctn-col .form-control {
        text-align: center;
        font-weight: 600;
    }
    .qb-stock-unit {
        font-size: 8.5px;
        text-transform: none;
        color: #94A3B8;
        display: block;
    }
    .qb-vhead {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .4px;
        font-weight: 700;
        color: #64748B;
        margin-bottom: 4px;
        padding: 0 2px;
    }
    .qb-row {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 6px;
        margin-bottom: 6px;
    }
    .qb-row.is-base {
        background: #EFF6FF;
        border-color: #BFDBFE;
    }
    .qb-row .form-control {
        height: 34px;
        font-size: 12.5px;
    }
    .qb-dot {
        font-size: 10px;
        font-weight: 700;
        color: #2563EB;
    }
    .qb-del {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border-radius: 7px;
    }
</style>

<div class="modal fade qb-modal" id="quickBuildProductModal" tabindex="-1" role="dialog" aria-labelledby="quickBuildProductModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="qb-head d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold text-white mb-0" id="quickBuildProductModalLabel">
                        <i class="fas fa-box-open me-2"></i>Quick Add Product
                    </h5>
                    <div class="qb-sub text-white">Create a new product with variants &amp; add to this sale</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="quickBuildProductForm" autocomplete="off">
                @csrf

                <div class="modal-body pt-3 pb-2">
                    {{-- Basic fields --}}
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product_name" required placeholder="Enter product name">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select fw-bold" name="size_mode" id="qbUnit">
                                <option value="by_pieces" selected>Pcs</option>
                                <option value="by_cartons">Carton</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="qbCategory" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Sub Category</label>
                            <select class="form-select" name="sub_category_id" id="qbSubcategory">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Brand <span class="text-danger">*</span></label>
                            <select class="form-select" name="brand_id" id="qbBrand" required>
                                <option value="">Select Brand</option>
                            </select>
                        </div>
                    </div>

                    {{-- Variants --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">Variant Details</label>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="qbAddVariantBtn">
                            <i class="fas fa-plus me-1"></i> Add Variant
                        </button>
                    </div>

                    <div id="qbGrid">
                    <div class="qb-vhead">
                        <div>Variant Name *</div>
                        <div>Size</div>
                        <div class="qb-ctn-col">Pcs / Ctn</div>
                        <div>Purchase</div>
                        <div>Sale</div>
                        <div>Stock <span class="qb-stock-unit" id="qbStockUnitLabel">(Pcs)</span></div>
                        <div></div>
                    </div>
                    <div id="qbVariantsBody"></div>
                    </div>

                    <div class="text-muted mt-1" style="font-size:11px;">
                        <i class="fas fa-barcode me-1"></i>Barcode is auto-generated automatically, same as the Create Product page.
                    </div>

                    {{-- Hidden mode fields for backend compatibility --}}
                    <input type="hidden" name="piece_quantity" id="qb_pieces" value="0">
                    <input type="hidden" name="pieces_per_box" value="1">
                    <input type="hidden" name="boxes_quantity" id="qb_boxes" value="0">
                    <input type="hidden" name="loose_pieces" value="0">
                    <input type="hidden" name="sale_price_per_box" id="qb_sale_box" value="0">
                    <input type="hidden" name="purchase_price_per_piece" id="qb_purch_pc" value="0">
                    <input type="hidden" name="wholesale_price" value="0">
                    <input type="hidden" name="weight_per_piece" value="0">
                    <input type="hidden" name="alert_carton_quantity" value="0">
                    <input type="hidden" name="alert_quantity" value="0">
                </div>

                <div class="modal-footer bg-light border-top pt-2 pb-2 px-3">
                    <a href="{{ route('store') }}" target="_blank" class="btn btn-link btn-sm text-decoration-none text-primary me-auto p-0">
                        <i class="fas fa-external-link-alt me-1"></i>Open full product form
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnQBSubmit">
                        <i class="fas fa-check-circle me-1"></i>Save and add to sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    (function($) {
        var qbDataLoaded = false;

        function qbBarcode() {
            return Math.floor(100000 + Math.random() * 900000).toString();
        }

        function qbUnitLabel() {
            return $('#qbUnit').val() === 'by_cartons' ? 'Carton' : 'Pcs';
        }

        function qbSyncUnit() {
            var isCarton = $('#qbUnit').val() === 'by_cartons';
            $('#qbGrid').toggleClass('carton', isCarton);
            $('#qbStockUnitLabel').text(isCarton ? '(Ctns)' : '(Pcs)');
            $('#qbVariantsBody .qb-vunit').val(isCarton ? 'Carton' : 'Pcs');
            if (!isCarton) {
                $('#qbVariantsBody .qb-ctn').val(1);
            }
        }

        function qbAddRow(isBase) {
            var isBase = !!isBase;
            var idx = $('#qbVariantsBody .qb-row').length + 1;
            if (isBase && $('#qbVariantsBody .qb-row').length > 0) isBase = false;

            var $row = $('<div>', { 'class': 'qb-row' + (isBase ? ' is-base' : '') });
            var nameVal = isBase ? 'Base' : '';
            var action = isBase
                ? '<span class="badge bg-primary px-2 py-1" style="font-size:10px;">Base</span>'
                : '<button type="button" class="btn btn-outline-danger qb-del" title="Remove variant"><i class="fas fa-trash"></i></button>';

            $row.html(
                '<div class="qb-name-cell">' +
                    '<input type="text" class="form-control qb-name fw-bold" name="variant_name[]" value="' + nameVal + '" placeholder="Variant ' + idx + '" required>' +
                '</div>' +
                '<div><input type="text" class="form-control" name="variant_size[]" placeholder="Size"></div>' +
                '<div class="qb-ctn-col"><input type="number" class="form-control qb-ctn" name="variant_conv_factor[]" step="any" min="1" value="1" placeholder="e.g. 6"></div>' +
                '<div><input type="number" class="form-control qb-purch" name="variant_purchase_price[]" step="any" min="0" value="0" placeholder="0.00" required></div>' +
                '<div><input type="number" class="form-control qb-sale" name="variant_sale_price[]" step="any" min="0" value="0" placeholder="0.00" required></div>' +
                '<div><input type="number" class="form-control qb-stock" name="variant_stock[]" step="any" min="0" value="0" placeholder="0"></div>' +
                '<div class="text-center">' + action + '</div>' +
                '<input type="hidden" name="variant_color[]" value="-">' +
                '<input type="hidden" name="variant_unit[]" class="qb-vunit" value="' + qbUnitLabel() + '">' +
                '<input type="hidden" name="variant_wholesale_price[]" value="0">' +
                '<input type="hidden" name="variant_alert_qty[]" value="0">' +
                '<input type="hidden" name="variant_barcode[]" class="qb-barcode" value="' + qbBarcode() + '">' +
                '<input type="hidden" name="variant_weight_per_piece[]" value="0">' +
                '<input type="hidden" name="variant_is_base[]" class="qb-isbase" value="' + (isBase ? 1 : 0) + '">'
            );
            $('#qbVariantsBody').append($row);
            return $row;
        }

        function qbResetForm() {
            var $form = $('#quickBuildProductForm');
            $form[0].reset();
            $('#qbVariantsBody').empty();
            $('#qbSubcategory').html('<option value="">Select Sub Category</option>');
            qbAddRow(true);
            qbSyncUnit();
        }

        function qbLoadDropdowns() {
            $.get("{{ url('/get-categories') }}").done(function(d) {
                (d || []).forEach(function(c) {
                    $('#qbCategory').append('<option value="' + c.id + '">' + c.name + '</option>');
                });
            });
            $.get("{{ url('/get-brands') }}").done(function(d) {
                (d || []).forEach(function(b) {
                    $('#qbBrand').append('<option value="' + b.id + '">' + b.name + '</option>');
                });
            });
        }

        function qbWarn(title, text) {
            if (typeof Swal !== 'undefined') Swal.fire(title, text, 'warning');
            else alert(title + ': ' + text);
        }

        function qbOpen() {
            if (!qbDataLoaded) {
                qbLoadDropdowns();
                qbDataLoaded = true;
            }
            qbResetForm();
        }

        // ---- Triggers: header button + from Quick Products drawer ----
        $(document).on('click', '#btnNewProductHeader, #btnNewProductFromDrawer', function(e) {
            e.preventDefault();
            var ocEl = document.getElementById('quickProductsOffcanvas');
            if (window.bootstrap && window.bootstrap.Offcanvas && ocEl) {
                var inst = bootstrap.Offcanvas.getInstance(ocEl);
                if (inst) inst.hide();
            }
            qbOpen();
            $('#quickBuildProductModal').modal('show');
        });

        // ---- Add variant row ----
        $(document).on('click', '#qbAddVariantBtn', function() {
            qbAddRow(false);
        });

        // ---- Remove variant row (never the Base row) ----
        $(document).on('click', '.qb-del', function() {
            var $row = $(this).closest('.qb-row');
            if ($row.find('.qb-isbase').val() === '1') return;
            $row.remove();
        });

        // ---- Unit change -> show Pcs/Ctn column, sync variant unit + conv factor ----
        $(document).on('change', '#qbUnit', qbSyncUnit);

        // ---- Category change -> load subcategories ----
        $(document).on('change', '#qbCategory', function() {
            var catId = $(this).val();
            var $sub = $('#qbSubcategory').html('<option value="">Select Sub Category</option>');
            if (!catId) return;
            $.get("{{ url('/get-subcategories') }}/" + catId).done(function(d) {
                (d || []).forEach(function(s) {
                    $sub.append('<option value="' + s.id + '">' + s.name + '</option>');
                });
            });
        });

        // ---- Submit: reuse POST /store-product (same as full Create Product form) ----
        $('#quickBuildProductForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('#btnQBSubmit');
            var origHtml = $btn.html();

            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            var $rows = $('#qbVariantsBody .qb-row');
            if ($rows.length === 0) {
                qbWarn('No Variant', 'Please add at least the base variant.');
                return;
            }

            var totalStock = 0;
            for (var i = 0; i < $rows.length; i++) {
                var $r = $($rows[i]);
                var nm = $r.find('.qb-name').val().trim();
                if (!nm) {
                    qbWarn('Variant name required', 'Every variant row needs a name.');
                    $r.find('.qb-name').focus();
                    return;
                }
                totalStock += parseFloat($r.find('.qb-stock').val()) || 0;
            }

            // Sync mode hidden fields with computed totals
            var mode = $('#qbUnit').val();
            var vSale = parseFloat($rows.first().find('.qb-sale').val()) || 0;
            var vPurch = parseFloat($rows.first().find('.qb-purch').val()) || 0;
            if (mode === 'by_cartons') {
                var ppb = parseInt($rows.first().find('.qb-ctn').val()) || 1;
                if (ppb < 1) ppb = 1;
                $('[name="pieces_per_box"]').val(ppb);
                $('#qb_boxes').val(totalStock);
                $('#qb_pieces').val(0);
            } else {
                $('[name="pieces_per_box"]').val(1);
                $('#qb_pieces').val(totalStock);
                $('#qb_boxes').val(0);
            }
            $('#qb_sale_box').val(vSale);
            $('#qb_purch_pc').val(vPurch);

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

            $.ajax({
                url: "{{ route('store-product') }}",
                method: "POST",
                data: $form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                success: function(res) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#quickBuildProductModal').modal('hide');

                    if (res && res.product_id) {
                        qbAddToSale(res.product_id, res.product_name || $('[name="product_name"]').val());
                    }

                    $form[0].reset();
                    $('#qbSubcategory').html('<option value="">Select Sub Category</option>');
                    $('#qbVariantsBody').empty();
                    qbAddRow(true);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Added!',
                            text: (res && res.message) ? res.message : 'Product created and added to sale.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Product added to sale!');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    var msg = 'Error adding product.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', msg, 'error');
                    } else {
                        alert('Error: ' + msg);
                    }
                }
            });
        });

        // ---- Add created product to the current sale (same flow as Quick Products "+") ----
        function qbAddToSale(prodId, prodName) {
            var $tmp = $('<div class="pos-product-card">' +
                '<div class="pos-product-name">' + (prodName || '') + '</div>' +
                '<button type="button" class="pos-product-add-btn add-product-direct-btn" data-id="' + prodId + '"></button>' +
                '</div>');
            $('body').append($tmp);
            try {
                $tmp.find('.add-product-direct-btn').trigger('click');
            } finally {
                $tmp.remove();
            }
            if (typeof window.updateGrandTotals === 'function') window.updateGrandTotals();
        }
    })(jQuery);
</script>
@endpush