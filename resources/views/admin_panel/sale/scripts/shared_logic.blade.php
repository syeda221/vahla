<style>
    .last-sale-badge-container {
        margin-top: 2px;
        padding-right: 2px;
    }
    .last-sale-pill {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        white-space: nowrap !important;
        line-height: 1.3 !important;
        transition: all 0.15s ease-in-out !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
    }
    .last-sale-pill:hover {
        background: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
        box-shadow: 0 2px 5px rgba(37, 99, 235, 0.25) !important;
        transform: translateY(-1px);
    }
    .last-sale-pill:hover i {
        color: #ffffff !important;
    }
    .col-price-p {
        min-width: 105px !important;
    }
    /* Restrain Product & Guarantee Item Code Column Width */
    .sales-table {
        min-width: 960px;
    }
    .sales-table thead th.col-product,
    .sales-table tbody td.col-product {
        width: 250px !important;
        min-width: 220px !important;
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
    }
    /* Expand Select2 dropdown results popup so full names show cleanly without ellipsis */
    .select2-container--open .select2-dropdown {
        min-width: 360px !important;
        max-width: 520px !important;
        box-shadow: 0 10px 25px -5px rgba(15,23,42,0.18) !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        z-index: 1060 !important;
    }
    .select2-container--open .select2-dropdown .select2-results__option {
        padding: 6px 12px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .select2-container--open .select2-dropdown .select2-results__option:last-child {
        border-bottom: none !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        color: #ffffff !important;
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
        cursor: pointer !important;
        background-color: #f8fafc !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        transition: all 0.15s ease-in-out !important;
    }
    .sales-table tbody td.col-stock input.stock:hover {
        background-color: #eef2ff !important;
        border-color: #818cf8 !important;
        color: #4338ca !important;
    }
    .sales-table thead th.col-description,
    .sales-table tbody td.col-description {
        width: 130px !important;
        min-width: 110px !important;
    }
    .sales-table tbody td.col-description input.item-description {
        font-size: 0.78rem !important;
        padding: 2px 6px !important;
    }
</style>

<script>
    let lastSelectedPriceMode = 'retail';
    /* =========================================
       SHARED SALES LOGIC (Add/Edit)
       ========================================= */

    // --- Helpers ---
    function pad(n) {
        return n < 10 ? '0' + n : n
    }

    function setNowStamp() {
        if ($('#entryDateTime').length) {
            const d = new Date();
            const dt =
                `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${String(d.getFullYear()).slice(-2)} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
            const dOnly = `${pad(d.getDate())}-${pad(d.getMonth()+1)}-${String(d.getFullYear()).slice(-2)}`;
            $('#entryDateTime').text('Entry Date_Time: ' + dt);
            $('#entryDate').text('Date: ' + dOnly);
        }
    }

    setNowStamp();
    setInterval(setNowStamp, 60 * 1000);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });

    function showAlert(type, msg) {
        const el = $('#alertBox');
        if (el.length) {
            el.removeClass('d-none alert-success alert-danger alert-warning').addClass('alert-' + type).text(msg)
                .show();
            setTimeout(() => el.addClass('d-none'), 3000);
        } else {
            // fallback if alertBox missing
            if (type === 'error') Swal.fire('Error', msg, 'error');
            else if (type === 'warning') Swal.fire('Warning', msg, 'warning');
            else Swal.fire('Info', msg, 'info');
        }
    }

    function toNum(v) {
        if (typeof v === 'number') return v;
        if (!v) return 0;
        // Handle thousands separator (comma)
        const str = v.toString().replace(/,/g, '');
        return parseFloat(str) || 0;
    }

    /* =========================================
       PRODUCT SELECT2
       ========================================= */
    function initProductSelect2($el) {
        $el.select2({
            placeholder: 'Search Product (Name / SKU / Barcode)',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route('products.ajax.search') }}',
                dataType: 'json',
                delay: 150,
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
            templateResult: formatProduct,
            templateSelection: formatSelection,
            escapeMarkup: function(m) { return m; },
            language: {
                noResults: function() {
                    var term = '';
                    var $openContainer = $('.select2-container--open');
                    var $searchField = $openContainer.find('.select2-search__field');
                    if ($searchField.length) {
                        term = $searchField.val().trim();
                    }
                    var createUrl = "{{ route('store') }}?name=" + encodeURIComponent(term) + "&return_to=sale&return_url=" + encodeURIComponent(window.location.href);
                    var safeTerm = term ? $('<div>').text(term).html() : '';

                    return `
                        <div class="py-2 px-1 text-center select2-create-option-wrapper">
                            <div class="text-muted small mb-2"><i class="fas fa-search me-1"></i> No results found ${safeTerm ? 'for "<strong>' + safeTerm + '</strong>"' : ''}</div>
                            <a href="${createUrl}" class="btn btn-sm btn-primary w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1 btn-create-product-from-search" style="font-size: 11px; padding: 6px 10px; background-color: #2563eb !important; border: none; border-radius: 6px; color: #ffffff !important; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-plus-circle"></i> Create "${safeTerm || 'New Product'}"
                            </a>
                        </div>
                    `;
                }
            }
        });
    }

    function formatProduct(repo) {
        if (repo.loading) return repo.text;
        if (repo.is_set || (repo.id && repo.id.toString().startsWith('set_'))) {
            let code = repo.sku || repo.item_code || 'SET';
            let price = parseFloat(repo.retail_price || 0);
            return `
                <div class="py-1" style="line-height: 1.35;">
                    <div style="font-weight: 600; font-size: 0.85rem; color: inherit; white-space: normal; word-break: break-word;">
                        <i class="fas fa-layer-group me-1 text-primary"></i> ${repo.name || repo.text}
                        <span class="badge bg-primary text-white ms-1" style="font-size: 10px; font-weight: 700;">SET</span>
                    </div>
                    <div style="font-size: 0.75rem; opacity: 0.85; color: inherit; margin-top: 2px;">
                        SKU: ${code}
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge rounded-pill" style="background: #00c853; color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 2px 8px;">Total: SET</span>
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-0" style="font-size: 0.72rem; font-weight: 600;">Rs. ${price.toLocaleString()}</span>
                    </div>
                </div>
            `;
        }
        let stock = repo.stock !== undefined ? repo.stock : 0;
        let sku = repo.sku || repo.item_code || 'N/A';
        let stockVal = parseFloat(repo.stock_pieces !== undefined ? repo.stock_pieces : repo.stock) || 0;
        let badgeBg = stockVal > 0 ? '#00c853' : '#dc2626';

        return `
        <div class="py-1" style="line-height: 1.35;">
            <div style="font-weight: 600; font-size: 0.85rem; color: inherit; white-space: normal; word-break: break-word;">
                ${repo.name || repo.text}
            </div>
            <div style="font-size: 0.75rem; opacity: 0.85; color: inherit; margin-top: 2px;">
                SKU: ${sku}
            </div>
            <div class="mt-1">
                <span class="badge rounded-pill" style="background: ${badgeBg}; color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 2px 8px;">Total: ${stock}</span>
            </div>
        </div>
        `;
    }

    function formatSelection(repo) {
        return repo.name || repo.text;
    }


    /* =========================================
       CORE LOGIC
       ========================================= */

    function updateRowIndexes() {
        $('#salesTableBody tr').each(function(index) {
            $(this).find('.row-index').text(index + 1);
        });
    }

    function addNewRow() {
        const isWholesale = lastSelectedPriceMode === 'wholesale';
        const btnText = isWholesale ? 'W' : 'R';
        const btnClass = isWholesale ? 'btn-outline-info' : 'btn-outline-success';
        const btnTitle = isWholesale ? 'Wholesale Mode' : 'Retail Mode';

        const rowHtml = `
  <tr>
    <!-- # ROW INDEX -->
    <td class="text-center fw-bold text-muted row-index" style="vertical-align:middle; font-size:0.75rem;">1</td>

    <!-- PRODUCT -->
    <td class="col-product" style="width: 220px; min-width: 180px; max-width: 250px;">
      <select class="form-select product" style="width:100%">
        <option value=""></option>
      </select>
      <input type="hidden" class="product-id-hidden" name="product_id[]">
      <input type="hidden" class="product-set-id-hidden" name="product_set_id[]" value="">
      <input type="hidden" class="is-set-hidden" name="is_set[]" value="0">
      <input type="hidden" class="variant-data-hidden" name="color[]">
      <input type="hidden" class="size-h">
      <input type="hidden" class="size-w">
      <input type="hidden" class="size-mode-text">
    </td>

    <!-- ITEM CODE -->
    <td class="col-code" style="width: 110px; min-width: 105px;">
      <input type="text" class="form-control item-code-display text-center input-readonly" readonly placeholder="Code" tabindex="-1" style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; font-weight: 600;">
    </td>

    <!-- LOCATION (Warehouse / Shop) -->
    <td class="col-wh" style="width: 160px; min-width: 150px;">
      <div class="d-flex align-items-center gap-1">
        <select class="form-select form-select-sm warehouse row-warehouse-select fw-semibold" name="warehouse_id[]" style="font-size:0.75rem; flex: 1; min-width: 0;">
          @foreach(\App\Models\Warehouse::orderByRaw("is_shop DESC, id ASC")->get() as $wh)
              <option value="{{ $wh->id }}">
                  {{ $wh->is_shop ? '🏪 ' : '🏢 ' }}{{ $wh->warehouse_name }}
              </option>
          @endforeach
        </select>
        <button type="button" class="btn btn-xs btn-outline-primary btn-split-wh px-1 py-0" title="Sell from Multiple Locations (Shop & Warehouse)" data-bs-toggle="tooltip">
            <i class="fas fa-boxes" style="font-size: 0.72rem;"></i>
        </button>
      </div>
      <input type="hidden" class="warehouse-breakdown-input" name="warehouse_breakdown[]" value="">
      <div class="mwh-applied-badge-wrap mt-1" style="display: none;"></div>
      <div class="wh-stock-hint text-muted mt-1" style="font-size: 0.65rem; line-height: 1.1; display: none;" title="Click to allocate across warehouses"></div>
    </td>

    <!-- STOCK -->
    <td class="col-stock" style="width: 80px; min-width: 75px;">
      <input type="text" class="form-control stock text-center input-readonly" readonly tabindex="-1" style="font-size: 0.75rem;" title="Click to view warehouse stock breakdown">
      <input type="hidden" class="variant-stock-value">
    </td>

    <!-- Qty cell with Sub-Unit toggle on Right and Left-Aligned Cursor -->
    <td style="width:95px;min-width:95px;" class="col-qty-wrapper">
      <div class="d-flex align-items-center gap-1">
        <input type="number" step="any" class="form-control carton-qty text-start" name="carton_qty[]" placeholder="0" min="0" value="" style="flex: 1; min-width: 0; height: 26px; font-size: 0.85rem; padding-left: 6px;">
        <button type="button" class="btn btn-sm btn-outline-primary qty-unit-toggle px-1 py-0 d-none" 
                data-unit-mode="main" title="Toggle Unit (Ctn ↔ Pcs / Kg ↔ Gm / Ft ↔ In)" style="font-size: 0.65rem; height: 26px; min-width: 28px; font-weight: 700; border-radius: 4px; flex-shrink: 0;">
          Kg
        </button>
      </div>
      <input type="hidden" class="hidden-sub-unit-mode" name="sub_unit_mode[]" value="main">
    </td>

    <!-- Loose Pieces -->
    <td style="width:70px;min-width:70px;" class="d-none">
      <input type="number" class="form-control loose-pcs-input text-end" name="loose_qty[]" placeholder="" min="0" value="">
    </td>

    <!-- Description -->
    <td class="col-description" style="width:130px; min-width:110px;">
      <input type="text" class="form-control item-description" name="description[]" placeholder="Description" style="font-size:0.78rem;">
    </td>

    <!-- Size -->
    <td class="col-size">
       <input type="text" class="form-control size-display text-center" name="size_display[]" placeholder="-">
       <input type="hidden" class="pack-qty" name="pack_qty[]" value="1">
    </td>

    <!-- Color (Display - readonly) -->
    <td class="col-color">
      <input type="text" class="form-control color-display text-center input-readonly" readonly tabindex="-1" placeholder="-">
    </td>

    <!-- Total Pieces (Calculated) -->
    <td class="col-pieces">
      <input type="text" class="form-control total-pieces text-end input-readonly" name="total_pieces[]" readonly placeholder="0" tabindex="-1">
      <!-- Hidden qty field for backend compatibility -->
      <input type="hidden" class="sales-qty" name="qty[]" value="0">
    </td>
 
    <!-- Price/Piece (EDITABLE) -->
    <td class="col-price-p">
      <div class="d-flex align-items-center gap-1">
        <input type="text" class="form-control visible-price text-end" name="visible_price[]" placeholder="0" style="flex: 1; min-width: 0;">
        <button type="button" class="btn btn-sm ${btnClass} price-mode-row-toggle px-1 py-0" 
                data-mode="${lastSelectedPriceMode}" title="${btnTitle}" style="font-size: 0.65rem; height: 24px; min-width: 20px; font-weight: bold;">
          ${btnText}
        </button>
      </div>
      <input type="hidden" class="price-per-piece" name="price_per_piece[]">
      <input type="hidden" class="retail-price">
      <input type="hidden" class="wholesale-price">
      <input type="hidden" class="weight-per-piece">
    </td>

    <!-- SINGLE DISCOUNT COLUMN -->
    <td class="col-disc">
      <div class="discount-wrapper">
        <input type="number"
               class="form-control discount-value text-end"
               name="item_disc[]"
               placeholder="0">
        <input type="hidden" class="discount-type-hidden" name="discount_type[]" value="percent">
        <button type="button"
                class="btn btn-outline-secondary discount-toggle"
                data-type="percent" tabindex="-1">%</button>
      </div>
      <input type="hidden" class="discount-amount" value="0">
    </td>

    <!-- NET AMOUNT -->
    <td class="col-amount">
      <input type="text" class="form-control sales-amount text-end input-readonly" name="total[]" value="0" readonly tabindex="-1">
      <input type="hidden" class="gross-amount" name="gross_amount[]">
    </td>

    <!-- ACTION -->
    <td class="col-action text-center">
      <button type="button" class="btn btn-sm btn-outline-danger del-row" tabindex="-1">&times;</button>
    </td>
  </tr>`;

        const $row = $(rowHtml);
        const currentWh = $('#warehouseSelect').val();
        if (currentWh) {
            $row.find('.warehouse').val(currentWh);
        }
        $('#salesTableBody').append($row);
        initProductSelect2($row.find('.product'));
        updateRowIndexes();
    }

    // Global helper for clickable badge
    window.applyLastSalePrice = function(el, price) {
        let $row = $(el).is('tr') ? $(el) : $(el).closest('tr');
        $row.find('.visible-price').val(price);
        $row.find('.price-per-piece').val(price);
        computeRow($row);

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Applied recent price: Rs. ' + Number(price).toLocaleString(),
                showConfirmButton: false,
                timer: 1800
            });
        }
    };

    // Check & Notify Recent Sold Price to Selected Customer
    function checkCustomerLastPrice($row, productId, customerId, promptUser = true) {
        if (!productId || !customerId) {
            $row.find('.last-sale-badge-container').remove();
            return;
        }

        let pid = productId.toString().split('|')[0];
        
        $.get('{{ route('get-price') }}', {
            product_id: pid,
            customer_id: customerId,
            _t: new Date().getTime()
        }).done(function(pRes) {
            $row.find('.last-sale-badge-container').remove();

            if (pRes && pRes.last_sale_info && pRes.last_sale_info.price > 0) {
                const info = pRes.last_sale_info;
                const lastPrice = parseFloat(info.price);
                const currentVal = parseFloat($row.find('.visible-price').val()) || 0;

                // 1. Append sleek, compact clickable pill badge under price column
                const badgeHtml = `
                <div class="last-sale-badge-container d-flex justify-content-end">
                    <span class="last-sale-pill" 
                          onclick="applyLastSalePrice(this, ${lastPrice})"
                          title="Sold on ${info.date} ${info.invoice_no ? '(' + info.invoice_no + ')' : ''} - Click to apply Rs. ${lastPrice.toLocaleString()}">
                        <i class="fas fa-history text-primary"></i> Last: <b>Rs ${lastPrice.toLocaleString()}</b>
                    </span>
                </div>`;
                $row.find('.col-price-p').append(badgeHtml);

                // 2. Show SweetAlert2 popup if promptUser is true
                if (promptUser && typeof Swal !== 'undefined') {
                    let customerName = $('#customerSelect option:selected').text() || 'this customer';
                    if (customerName.includes(' — ')) {
                        customerName = customerName.split(' — ')[1] || customerName;
                    }

                    Swal.fire({
                        title: 'Recent Sale Price Found!',
                        html: `
                            <div style="text-align: left; font-size: 0.95rem; line-height: 1.6;">
                                <p class="mb-2">Recently sold this product to <strong class="text-primary">${customerName}</strong> for:</p>
                                <div class="p-2 mb-2 rounded bg-light text-center border">
                                    <span class="text-success fw-bold" style="font-size: 1.35rem;">Rs. ${lastPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    <div class="text-muted small mt-1"><i class="far fa-calendar-alt me-1"></i> Sold on: <strong>${info.date}</strong> ${info.invoice_no ? '(' + info.invoice_no + ')' : ''}</div>
                                </div>
                                <div class="small text-muted text-center">Standard Current Rate: <strong>Rs. ${currentVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong></div>
                                <p class="mt-3 mb-0 text-center fw-semibold">Would you like to apply the previous rate (Rs. ${lastPrice.toLocaleString()})?</p>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: `<i class="fas fa-check me-1"></i> Apply Rs. ${lastPrice.toLocaleString()}`,
                        cancelButtonText: `Keep Standard (Rs. ${currentVal.toLocaleString()})`,
                        reverseButtons: true,
                        focusConfirm: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            applyLastSalePrice($row, lastPrice);
                        }
                    });
                }
            }
        }).fail(function(err) {
            console.error('Customer recent price check failed', err);
        });
    }

    // --- Loading Data for Rows ---

    function fetchProductPrice($row, productId, callback = null) {
        let pid = productId ? productId.toString().split('|')[0] : '';
        if (!pid) return;

        $.get('{{ route('get-price') }}', {
            product_id: pid,
            customer_id: $('#customerSelect').val(),
            _t: new Date().getTime()
        }).done(function(pRes) {
            $row.find('.item-code-display').val(pRes.item_code || '');
            $row.find('.retail-price').val(pRes.retail_price || 0);
            $row.find('.wholesale-price').val(pRes.wholesale_price || 0);
            $row.find('.weight-per-piece').val(pRes.weight_per_piece || 0);
             
            let rowMode = $row.find('.price-mode-row-toggle').attr('data-mode') || 'retail';
            let wsPrice = parseFloat(pRes.wholesale_price) || 0;
            let rate = (rowMode === 'wholesale' && wsPrice > 0) ? wsPrice : (pRes.retail_price || 0);

            const ppb = parseFloat(pRes.pieces_per_box) || 1;
            const unitMode = $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'ctn';
            if (pRes.size_mode == "by_cartons" || pRes.size_mode == "by_box") {
                let piecePrice = parseFloat(pRes.sale_price_per_piece || rate || 0);
                let cartonPrice = ppb > 1 ? (piecePrice * ppb) : piecePrice;
                $row.find('.visible-price').val(unitMode === 'pcs' ? piecePrice : cartonPrice);
                $row.find('.price-per-piece').val(unitMode === 'pcs' ? piecePrice : cartonPrice);
            } else if (pRes.size_mode == "by_pieces" || pRes.size_mode == "by_kg" || pRes.size_mode == "by_gm" || pRes.size_mode == "by_meter" || pRes.size_mode == "by_feet") {
                $row.find('.visible-price').val(pRes.sale_price_per_piece || rate || 0);
                $row.find('.price-per-piece').val($row.find('.visible-price').val() || 0);
            } else {
                $row.find('.visible-price').val(pRes.price_per_m2 || rate || 0);
                $row.find('.price-per-piece').val($row.find('.visible-price').val() || 0);
            }

            $row.find('.pack-qty').val(pRes.pieces_per_box || 1);
            $row.find('.size-h').val(pRes.height || '-');
            $row.find('.size-w').val(pRes.width || '-');
            $row.find('.size-mode-text').val(pRes.size_mode || '-');

            $row.find('.discount-value').val(pRes.sale_discount_percent || 0);

            $row.data('size_mode', pRes.size_mode);
            $row.data('pieces_per_box', pRes.pieces_per_box || 1);
            $row.data('price_per_m2', pRes.price_per_m2 || 0);

            setupRowQtyToggle($row, pRes.size_mode);
            computeRow($row);
            updateGrandTotals();
            refreshPostedState();

            // Check and notify recent price
            const custId = $('#customerSelect').val();
            if (custId) {
                checkCustomerLastPrice($row, pid, custId, true);
            }

            if (typeof callback === 'function') {
                callback(pRes);
            }
        }).fail(function(err) {
            console.error('Price fetch failed', err);
        });
    }

    function loadWarehousesForProduct($row, productId, preSelectId = null) {
        var $whInput = $row.find('.warehouse');
        var currentHeaderWh = $('#warehouseSelect').val();
        if (!$whInput.val() && currentHeaderWh) {
            $whInput.val(currentHeaderWh);
        } else if (!$whInput.val()) {
            $whInput.val("{{ auth()->user()->warehouse_id ?? 1 }}");
        }

        $.get('{{ route('warehouses.get') }}', {
                product_id: productId
            })
            .done(function(warehouses) {
                var targetWhId = preSelectId || $whInput.val() || currentHeaderWh || "{{ auth()->user()->warehouse_id ?? 1 }}";
                var validWarehouses = Array.isArray(warehouses) ? warehouses : [];
                $row.data('product_warehouses', validWarehouses);
                var matched = validWarehouses.find(function(w) { return w.warehouse_id == targetWhId; });

                // Resolve conversion factor and variant unit for this row
                let convFactor = 0;
                let variantUnit = '';
                const vdEncoded = $row.find('.variant-data-hidden').val();
                if (vdEncoded) {
                    try {
                        let vd;
                        try { vd = JSON.parse(atob(vdEncoded)); } catch(e) { vd = JSON.parse(vdEncoded); }
                        if (vd) {
                            variantUnit = (vd.unit || '').toLowerCase().trim();
                            if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                                convFactor = parseFloat(vd.conv_factor);
                            } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                                convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                            }
                        }
                    } catch(ex) {}
                }
                if (convFactor <= 0) {
                    const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                    if (wpp > 0) convFactor = wpp / 1000.0;
                }

                const unitMode = ($row.find('.qty-unit-toggle').attr('data-unit-mode') || variantUnit || 'ctn').toLowerCase();

                if (matched) {
                    $whInput.val(matched.warehouse_id);
                    let disp;
                    const ppb = parseFloat(matched.ppb) || 1;

                    if ((matched.size_mode === 'by_cartons' || matched.size_mode === 'by_box' || matched.size_mode === 'by_size') && ppb > 1) {
                        if (unitMode === 'pcs' || unitMode === 'pc') {
                            disp = matched.stock;
                        } else {
                            const boxes = Math.floor(matched.boxes || 0);
                            const loose = matched.stock % ppb;
                            disp = loose > 0 ? `${boxes}.${loose}` : boxes;
                        }
                    } else if (matched.size_mode === 'by_kg') {
                        const s = parseFloat(matched.stock) || 0;
                        if ((unitMode === 'pcs' || unitMode === 'pc') && convFactor > 0) {
                            const pcs = Math.round(s / convFactor);
                            disp = `${pcs.toLocaleString()} Pcs (${s.toFixed(2)} Kg)`;
                        } else if (s > 0 && s < 1) {
                            const gm = (s * 1000).toFixed(1);
                            disp = `${s.toFixed(2)} Kg (${gm} Gm)`;
                        } else {
                            disp = `${s.toFixed(2)} Kg`;
                        }
                    } else if (matched.size_mode === 'by_meter') {
                        const s = parseFloat(matched.stock) || 0;
                        if (s > 0 && s < 1) {
                            const mm = (s * 1000).toFixed(0);
                            disp = `${s.toFixed(2)} Mtr (${mm} mm)`;
                        } else {
                            disp = `${s.toFixed(2)} Mtr`;
                        }
                    } else if (matched.size_mode === 'by_feet') {
                        const s = parseFloat(matched.stock) || 0;
                        if (s > 0 && s < 1) {
                            const inches = (s * 12).toFixed(1);
                            disp = `${s.toFixed(2)} Ft (${inches} in)`;
                        } else {
                            disp = `${s.toFixed(2)} Ft`;
                        }
                    } else {
                        disp = matched.stock;
                    }
                    $row.find('.stock').val(disp);
                }

                // Surface multi-warehouse stock breakdown hint on the row
                if (validWarehouses.length > 1) {
                    let hintItems = [];
                    validWarehouses.forEach(function(w) {
                        let icon = w.is_shop ? '🏪' : '🏢';
                        let sName = (w.warehouse_name || '').replace('WAREHOUSE', 'WH').trim();
                        let sVal = parseFloat(w.stock) || 0;
                        let colorClass = sVal > 0 ? 'text-primary font-monospace' : 'text-danger font-monospace';
                        let labelVal = sVal;

                        if (matched && matched.size_mode === 'by_kg') {
                            if ((unitMode === 'pcs' || unitMode === 'pc') && convFactor > 0) {
                                let whPcs = Math.round(sVal / convFactor);
                                labelVal = `${whPcs.toLocaleString()} Pcs <small class="text-muted">(${sVal.toFixed(2)} Kg)</small>`;
                            } else {
                                labelVal = `${sVal.toFixed(2)} Kg`;
                            }
                        } else if (matched && (matched.size_mode === 'by_cartons' || matched.size_mode === 'by_box') && (parseFloat(matched.ppb) || 1) > 1) {
                            let ppb = parseFloat(matched.ppb) || 1;
                            if (unitMode === 'ctn') {
                                let boxes = Math.floor(sVal / ppb);
                                let loose = sVal % ppb;
                                let ctnStr = loose > 0 ? `${boxes}.${loose} Ctn` : `${boxes} Ctn`;
                                labelVal = `${ctnStr} <small class="text-muted">(${sVal} Pcs)</small>`;
                            } else {
                                labelVal = `${sVal} Pcs`;
                            }
                        }

                        hintItems.push(`${icon} ${sName}: <span class="${colorClass}">${labelVal}</span>`);
                    });
                    $row.find('.wh-stock-hint').html(hintItems.join(' | ')).show();
                } else {
                    $row.find('.wh-stock-hint').hide();
                }
            })
            .fail(function(xhr) {
                console.error('Warehouse fetch error:', xhr);
            });
    }

    // /* row-warehouse-change-listener */
    $(document).on('change', '.row-warehouse-select, .warehouse', function() {
        var $row = $(this).closest('tr');
        var selWhId = $(this).val();
        var pid = $row.find('.product').val() || $row.find('.product-id-hidden').val();
        if (pid) {
            loadWarehousesForProduct($row, pid, selWhId);
        }
    });

    // Sync all rows when header Dispatch Location changes
    $(document).on('change', '#warehouseSelect', function() {
        var newWhId = $(this).val();
        $('#salesTableBody tr').each(function() {
            var $row = $(this);
            $row.find('.warehouse').val(newWhId);
            var pid = $row.find('.product').val();
            if (pid) {
                $row.find('.stock').val('');
                $row.find('.variant-stock-value').val('');
                loadWarehousesForProduct($row, pid, newWhId);
            }
        });
    });

    // Stock Breakdown Modal on clicking STOCK column
    $(document).on('click', '.col-stock, .col-stock input.stock', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var pid = $row.find('.product-id-hidden').val() || $row.find('.product').val();

        if (!pid) {
            return;
        }

        var isSet = $row.find('.is-set-hidden').val() == '1';
        var pName = ($row.find('.product option:selected').text() || $row.data('product_info')?.name || 'Product Stock').trim();
        var pCode = $row.find('.item-code-display').val() || $row.data('product_info')?.sku || '';
        var currentStock = $row.find('.stock').val() || '0';

        // Ensure Modal exists in DOM
        if ($('#stockBreakdownModal').length === 0) {
            $('body').append(`
                <div class="modal fade" id="stockBreakdownModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                        <div class="modal-content" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(15,23,42,0.12); overflow: hidden;">
                            <div class="modal-header py-2 px-3" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 28px; height: 28px; border-radius: 6px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <h6 class="modal-title fw-bold text-dark mb-0" style="font-size: 14px;">Stock Location Breakdown</h6>
                                </div>
                                <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 22px; line-height: 1; color: #64748b; cursor: pointer; padding: 0 4px;">&times;</button>
                            </div>
                            <div class="modal-body p-3">
                                <div class="mb-3 pb-2 border-bottom">
                                    <div class="fw-bold text-dark" id="modalProductName" style="font-size: 13px; line-height: 1.4;">-</div>
                                    <div class="text-muted small mt-1" id="modalProductCode" style="font-size: 11px;">SKU: -</div>
                                </div>
                                <div id="modalStockList" class="d-flex flex-column gap-2">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Loading stock details...
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer py-2 px-3 d-flex justify-content-between align-items-center" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                                <div class="small fw-semibold text-dark">
                                    Total Available: <span id="modalTotalStockBadge" class="badge rounded-pill px-2 py-1 ms-1" style="background: #0f172a; color: #ffffff; font-weight: 700; font-size: 12px;">0</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary px-3" data-dismiss="modal" data-bs-dismiss="modal" style="font-size: 12px; border-radius: 6px;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        var cleanName = pName.replace(/\(SKU:.*?\)/gi, '').trim();
        $('#modalProductName').text(cleanName || pName);
        $('#modalProductCode').text(pCode ? 'SKU: ' + pCode : '');

        var numCurrent = parseFloat(currentStock);
        var dispCurrent = (!isNaN(numCurrent) && !currentStock.toString().includes('(') && !currentStock.toString().includes(' '))
            ? Number(numCurrent.toFixed(4)).toString()
            : currentStock;
        $('#modalTotalStockBadge').text(dispCurrent);
        $('#modalStockList').html('<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-1"></i> Loading details...</div>');

        // Show Modal safely for Bootstrap 4 / 5 and jQuery
        var $modal = $('#stockBreakdownModal');
        try {
            if (typeof $modal.modal === 'function') {
                $modal.modal('show');
            } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                    bootstrap.Modal.getOrCreateInstance($modal[0]).show();
                } else if (typeof bootstrap.Modal === 'function') {
                    new bootstrap.Modal($modal[0]).show();
                }
            } else {
                $modal.show();
            }
        } catch (mErr) {
            console.warn('Modal open fallback:', mErr);
            $modal.show();
        }

        if (isSet) {
            $('#modalStockList').html(`
                <div class="p-3 rounded text-center" style="background: #eef2ff; border: 1px solid #c7d2fe;">
                    <i class="fas fa-layer-group text-primary mb-2" style="font-size: 20px;"></i>
                    <div class="fw-bold text-primary" style="font-size: 13px;">Set / Bundle Item</div>
                    <div class="small text-muted mt-1">This is a packaged bundle set. Stock is dynamically linked to member items.</div>
                </div>
            `);
            return;
        }

        function renderBreakdown(warehouses) {
            if (!warehouses || warehouses.length === 0) {
                $('#modalStockList').html('<div class="text-center text-muted py-3 small">No warehouse stock data available.</div>');
                return;
            }

            var html = '';
            var totalPieces = 0;
            var rowSizeMode = $row.data('size_mode') || '';
            var rowPpb = parseFloat($row.data('pieces_per_box')) || 1;
            var unitMode = ($row.find('.qty-unit-toggle').attr('data-unit-mode') || 'ctn').toLowerCase();

            // Resolve convFactor from variant
            let convFactor = 0;
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try { vd = JSON.parse(atob(vdEncoded)); } catch(e) { vd = JSON.parse(vdEncoded); }
                    if (vd) {
                        if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                            convFactor = parseFloat(vd.conv_factor);
                        } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                            convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                        }
                    }
                } catch(ex) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 1000.0;
            }

            warehouses.forEach(function(w) {
                var isShop = (w.is_shop == 1);
                var whName = w.name || w.warehouse_name || 'Warehouse';
                var rawPieces = parseFloat(w.stock_pieces !== undefined ? w.stock_pieces : w.stock) || 0;
                totalPieces += rawPieces;

                var sizeMode = w.size_mode || rowSizeMode;
                var ppb = parseFloat(w.ppb) || rowPpb || 1;
                var stockDisplay = '';

                if ((sizeMode === 'by_cartons' || sizeMode === 'by_box') && ppb > 1) {
                    if (unitMode === 'pcs' || unitMode === 'pc') {
                        stockDisplay = `${rawPieces} Pcs`;
                    } else {
                        var boxes = Math.floor(w.boxes !== undefined ? w.boxes : (rawPieces / ppb));
                        var loose = Math.round(rawPieces - (boxes * ppb));
                        if (boxes > 0 && loose > 0) {
                            stockDisplay = `${boxes}.${loose} Ctn (${rawPieces} Pcs)`;
                        } else if (boxes > 0) {
                            stockDisplay = `${boxes} Ctn (${rawPieces} Pcs)`;
                        } else {
                            stockDisplay = `${loose} Pcs`;
                        }
                    }
                } else if (sizeMode === 'by_kg') {
                    if ((unitMode === 'pcs' || unitMode === 'pc') && convFactor > 0) {
                        var whPcs = Math.round(rawPieces / convFactor);
                        stockDisplay = `${whPcs.toLocaleString()} Pcs (${rawPieces.toFixed(2)} Kg)`;
                    } else if (rawPieces > 0 && rawPieces < 1) {
                        var gm = (rawPieces * 1000).toFixed(1);
                        stockDisplay = `${rawPieces.toFixed(2)} Kg (${gm} Gm)`;
                    } else {
                        stockDisplay = `${rawPieces.toFixed(2)} Kg`;
                    }
                } else if (sizeMode === 'by_meter') {
                    if (rawPieces > 0 && rawPieces < 1) {
                        var mm = (rawPieces * 1000).toFixed(0);
                        stockDisplay = `${rawPieces.toFixed(2)} Mtr (${mm} mm)`;
                    } else {
                        stockDisplay = `${rawPieces.toFixed(2)} Mtr`;
                    }
                } else if (sizeMode === 'by_feet') {
                    if (rawPieces > 0 && rawPieces < 1) {
                        var inches = (rawPieces * 12).toFixed(1);
                        stockDisplay = `${rawPieces.toFixed(2)} Ft (${inches} in)`;
                    } else {
                        stockDisplay = `${rawPieces.toFixed(2)} Ft`;
                    }
                } else {
                    var numStock = parseFloat(rawPieces);
                    stockDisplay = (!isNaN(numStock) && !w.stock.toString().includes('(') && !w.stock.toString().includes(' '))
                        ? Number(numStock.toFixed(4)).toString()
                        : (w.stock || '0');
                }
                var hasStock = rawPieces > 0;

                var icon = isShop 
                    ? '<i class="fas fa-store text-primary" style="font-size:14px;"></i>' 
                    : '<i class="fas fa-warehouse text-secondary" style="font-size:14px;"></i>';

                var typeLabel = isShop 
                    ? '<span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:10px; font-weight:600; padding:2px 6px;">Main Store / Shop</span>' 
                    : '<span class="badge" style="background:#f1f5f9; color:#475569; font-size:10px; font-weight:600; padding:2px 6px;">Godown / Warehouse</span>';

                var badgeStyle = hasStock
                    ? 'background:#0f172a; color:#ffffff; font-weight:700; font-size:12px; padding:3px 10px; border-radius:999px; letter-spacing:0.2px;'
                    : 'background:#f1f5f9; color:#94a3b8; font-weight:600; font-size:12px; padding:3px 10px; border-radius:999px; border:1px dashed #cbd5e1;';

                html += `
                    <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: ${isShop ? '#faf5ff' : '#f8fafc'}; border: 1px solid ${isShop ? '#e9d5ff' : '#e2e8f0'};">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #fff; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                                ${icon}
                            </div>
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 12px; line-height: 1.2;">${whName}</div>
                                <div class="mt-1">${typeLabel}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill" style="${badgeStyle}">
                                ${stockDisplay}
                            </span>
                        </div>
                    </div>
                `;
            });

            $('#modalStockList').html(html);

            if ((rowSizeMode === 'by_cartons' || rowSizeMode === 'by_box') && rowPpb > 1) {
                var totalBoxes = Math.floor(totalPieces / rowPpb);
                var totalLoose = Math.round(totalPieces - (totalBoxes * rowPpb));
                if (unitMode === 'pcs' || unitMode === 'pc') {
                    $('#modalTotalStockBadge').text(`${totalPieces} Pcs`);
                } else {
                    var totalDisp = (totalBoxes > 0 && totalLoose > 0) 
                        ? `${totalBoxes}.${totalLoose} Ctn (${totalPieces} Pcs)` 
                        : (totalBoxes > 0 ? `${totalBoxes} Ctn (${totalPieces} Pcs)` : `${totalLoose} Pcs`);
                    $('#modalTotalStockBadge').text(totalDisp);
                }
            } else if (rowSizeMode === 'by_kg' && (unitMode === 'pcs' || unitMode === 'pc') && convFactor > 0) {
                var totalPcs = Math.round(totalPieces / convFactor);
                $('#modalTotalStockBadge').text(`${totalPcs.toLocaleString()} Pcs (${totalPieces.toFixed(2)} Kg)`);
            } else if (rowSizeMode === 'by_kg') {
                $('#modalTotalStockBadge').text(`${totalPieces.toFixed(2)} Kg`);
            } else if ($row.find('.stock').val()) {
                $('#modalTotalStockBadge').text($row.find('.stock').val());
            } else {
                var numTotal = parseFloat(totalPieces);
                var dispTotal = (!isNaN(numTotal)) ? Number(numTotal.toFixed(4)).toString() : totalPieces;
                $('#modalTotalStockBadge').text(dispTotal);
            }
        }

        var cached = $row.data('product_warehouses');
        if (cached && cached.length > 0) {
            renderBreakdown(cached);
        } else {
            $.get('{{ route('warehouses.get') }}', { product_id: pid })
                .done(function(res) {
                    var whs = Array.isArray(res) ? res : [];
                    $row.data('product_warehouses', whs);
                    renderBreakdown(whs);
                })
                .fail(function() {
                    $('#modalStockList').html('<div class="text-center text-danger py-2 small">Failed to load stock breakdown.</div>');
                });
        }
    });

    // Close Stock Breakdown Modal safely
    $(document).on('click', '#stockBreakdownModal [data-dismiss="modal"], #stockBreakdownModal [data-bs-dismiss="modal"], #stockBreakdownModal .close', function(e) {
        e.preventDefault();
        var $modal = $('#stockBreakdownModal');
        if (typeof $modal.modal === 'function') {
            $modal.modal('hide');
        } else {
            $modal.hide();
        }
    });


    // --- Calculation ---

    function computeRow($row, isManual = false) {
        const rp = toNum($row.find('.retail-price').val()); // Box Price
        const visiblePrice = toNum($row.find('.visible-price').val());
        const weightPerPiece = parseFloat($row.find('.weight-per-piece').val()) || 0;

        const m2_per_piece = parseFloat($row.find('.size-h').val() * $row.find('.size-w').val() / 10000);

        const sizeMode = $row.data('size_mode') || $row.find('.size-mode-text').val();
        const packQty = parseFloat($row.find('.pack-qty').val()) || 1;

        const rawQtyStr = ($row.find('.carton-qty').val() || '').toString().trim();
        const rawQty = parseFloat(rawQtyStr) || 0;
        const unitMode = $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'main';

        // Unit Price from visible-price
        let unitPrice = toNum($row.find('.visible-price').val());

        let baseQty = rawQty;
        let pcsDisplay = rawQty;
        let gross = 0;

        if (sizeMode === 'by_cartons' || sizeMode === 'by_box') {
            if (unitMode === 'pcs') {
                // Selling in Pieces: unitPrice is price per piece
                pcsDisplay = rawQty;
                baseQty = packQty > 0 ? (rawQty / packQty) : rawQty;
                gross = rawQty * unitPrice;
                $row.find('.loose-pcs-input').val(0);
            } else {
                // Selling in Cartons: unitPrice is price per carton
                if (rawQtyStr.includes('.')) {
                    const parts = rawQtyStr.split('.');
                    const boxes = parseInt(parts[0]) || 0;
                    const loose = parseInt(parts[1]) || 0;
                    pcsDisplay = (boxes * packQty) + loose;
                    baseQty = packQty > 0 ? (pcsDisplay / packQty) : rawQty;
                    const piecePrice = packQty > 0 ? (unitPrice / packQty) : unitPrice;
                    gross = (boxes * unitPrice) + (loose * piecePrice);
                    $row.find('.loose-pcs-input').val(loose);
                } else {
                    pcsDisplay = rawQty * packQty;
                    baseQty = rawQty;
                    gross = rawQty * unitPrice;
                    $row.find('.loose-pcs-input').val(0);
                }
            }
        } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
            let convFactor = 0;
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try { vd = JSON.parse(atob(vdEncoded)); } catch(e) { vd = JSON.parse(vdEncoded); }
                    if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                        convFactor = parseFloat(vd.conv_factor);
                    } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                        convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                    }
                } catch(e) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 1000.0;
            }
            if (convFactor <= 0) convFactor = 1.0;

            if (unitMode === 'gm') {
                baseQty = rawQty / 1000.0;
                pcsDisplay = baseQty;
                gross = rawQty * unitPrice; // unitPrice is per Gm
            } else if (unitMode === 'kg') {
                baseQty = rawQty;
                pcsDisplay = rawQty;
                gross = rawQty * unitPrice; // unitPrice is per Kg
            } else {
                // Non-base variant unit: Ft, Meter, Pcs, Box, Roll, etc.
                baseQty = rawQty;
                pcsDisplay = (rawQty * convFactor); // Base warehouse weight (KG) to deduct
                gross = rawQty * unitPrice; // unitPrice is per that variant unit
            }
        } else if (sizeMode === 'by_meter') {
            let convFactor = 0;
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try { vd = JSON.parse(atob(vdEncoded)); } catch(e) { vd = JSON.parse(vdEncoded); }
                    if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                        convFactor = parseFloat(vd.conv_factor);
                    } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                        convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                    }
                } catch(e) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 1000.0;
            }
            if (convFactor <= 0) convFactor = 1.0;

            if (unitMode === 'mm') {
                baseQty = rawQty / 1000.0;
                pcsDisplay = baseQty;
                gross = rawQty * unitPrice; // unitPrice is per mm
            } else if (unitMode === 'm' || unitMode === 'meter') {
                baseQty = rawQty;
                pcsDisplay = rawQty;
                gross = rawQty * unitPrice; // unitPrice is per Meter
            } else {
                // Non-base variant unit: Kg, Ft, Pcs, Roll, etc.
                baseQty = rawQty;
                pcsDisplay = (rawQty * convFactor); // Base warehouse length (Meters) to deduct
                gross = rawQty * unitPrice; // unitPrice is per that variant unit
            }
        } else if (sizeMode === 'by_feet') {
            let convFactor = 0;
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try { vd = JSON.parse(atob(vdEncoded)); } catch(e) { vd = JSON.parse(vdEncoded); }
                    if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                        convFactor = parseFloat(vd.conv_factor);
                    } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                        convFactor = parseFloat(vd.weight_per_piece) / 12.0;
                    }
                } catch(e) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 12.0;
            }
            if (convFactor <= 0) convFactor = 1.0;

            if (unitMode === 'in' || unitMode === 'inch' || unitMode === 'inches') {
                baseQty = rawQty / 12.0;
                pcsDisplay = baseQty;
                gross = rawQty * unitPrice; // unitPrice is per inch
            } else if (unitMode === 'ft' || unitMode === 'feet') {
                baseQty = rawQty;
                pcsDisplay = rawQty;
                gross = rawQty * unitPrice; // unitPrice is per Feet
            } else {
                // Non-base variant unit: Meter, Kg, Pcs, Roll, etc.
                baseQty = rawQty;
                pcsDisplay = (rawQty * convFactor); // Base warehouse length (Feet) to deduct
                gross = rawQty * unitPrice; // unitPrice is per that variant unit
            }
        } else if (sizeMode === 'by_size') {
            gross = m2_per_piece * baseQty * unitPrice;
            if (!m2_per_piece) gross = 0;
        } else {
            gross = baseQty * unitPrice;
        }

        // Sync hidden qty and total-pieces field for backend
        $row.find('.sales-qty').val(baseQty);
        $row.find('.total-pieces').val(pcsDisplay);
        $row.find('.price-per-piece').val(unitPrice);

        const discValue = toNum($row.find('.discount-value').val());
        const discType = $row.find('.discount-type-hidden').val() || $row.find('.discount-toggle').data('type') || $row.find('.discount-toggle').attr('data-type') || 'percent';
        let dam = 0;

        // Discount Calculation
        if (discType === 'percent') {
            dam = discValue > 0 ? (gross * discValue) / 100 : 0;
        } else {
            dam = discValue > 0 ? discValue : 0;
        }
        dam = Math.min(gross, dam);
        $row.find('.discount-amount').val(dam.toFixed(2));

        const netRow = Math.max(0, gross - dam);
        $row.find('.gross-amount').val(gross.toFixed(2));
        $row.find('.sales-amount').val(netRow.toFixed(2));

        if (typeof updateGrandTotals === 'function') {
            updateGrandTotals();
        }
    }

    function updateGrandTotals() {
        let tQty = 0;
        let tGross = 0;
        let tLineDisc = 0;
        let tNet = 0;

        $('#salesTableBody tr').each(function() {
            const $r = $(this);
            let gross = toNum($r.find('.gross-amount').val());
            const net = toNum($r.find('.sales-amount').val());
            const dam = toNum($r.find('.discount-amount').val());
            
            if (gross <= 0 && net > 0) gross = net + dam;

            // Piece calc: use total-pieces field (already computed)
            const pieces = parseFloat($r.find('.total-pieces').val()) || 0;

            tQty += pieces;
            tGross += gross;
            tLineDisc += dam;
            tNet += net;
        });

        const overallDiscVal = toNum($('#walkinDiscountRs').val());
        const overallDiscType = $('#overallDiscountType').val() || $('#overallDiscountToggle').data('type') || $('#overallDiscountToggle').attr('data-type') || 'pkr';
        let orderDisc = 0;

        if (overallDiscType === 'percent') {
            orderDisc = overallDiscVal > 0 ? (tNet * overallDiscVal) / 100 : 0;
        } else {
            orderDisc = overallDiscVal > 0 ? overallDiscVal : 0;
        }
        orderDisc = Math.min(tNet, orderDisc);

        // Keep hidden input updated for form submission
        $('#hiddenTotalExtraCost').val(orderDisc.toFixed(2));

        const shippingCharges = toNum($('#shippingChargesInput').val() || $('#bottomShippingChargesInput').val() || $('.shipping-charges-input').val());
        const cuttingCharges = toNum($('#cuttingChargesInput').val() || $('#bottomCuttingChargesInput').val() || $('.cutting-charges-input').val());
        const prev = toNum($('#previousBalance').val());
        const receipts = toNum($('#receiptsTotal').text());
        const currentInvoiceTotal = Math.max(0, (tNet - orderDisc) + shippingCharges + cuttingCharges);
        const payable = Math.max(0, currentInvoiceTotal + prev - receipts);

        $('#tQty').text(tQty % 1 === 0 ? tQty.toFixed(0) : tQty.toFixed(3).replace(/\.?0+$/, ''));
        $('#tGross').text(tGross.toFixed(2));
        $('#tLineDisc').text(tLineDisc.toFixed(2));
        $('#tOrderDisc').text(orderDisc.toFixed(2));
        $('#tSub').text(currentInvoiceTotal.toFixed(2));
        $('#tPrev').text(prev.toFixed(2));
        $('#tPayable').text(payable.toFixed(2));
        $('#totalAmount').text(currentInvoiceTotal.toFixed(2));
        $('#walkinNetTotal').text(currentInvoiceTotal.toFixed(2));
        $('#bottomPaymentsTotal').text(receipts.toFixed(2));
        $('#receiptsTotalBadge').text(receipts.toFixed(2));
        $('#itemsRowCount').text($('#salesTableBody tr').length);

        // Display current bill total after all discounts
        $('#tCurrentBill').text(currentInvoiceTotal.toFixed(2));

        const walkinPaid = receipts;
        const change = walkinPaid - currentInvoiceTotal;
        const changeStr = change.toFixed(2);
        $('#walkinChange').text(changeStr);
        $('#bottomChangeVal').text(changeStr);
        $('#backendChange').val(change > 0 ? change.toFixed(2) : 0);
        if (change > 0) {
            $('#walkinChange, #bottomChangeVal').removeClass('text-warning text-danger').addClass('text-success');
            $('#changeAccountRow, #bottomChangeAccountWrapper, #editChangeAccountContainer').show().css('display', 'flex');
        } else if (change === 0) {
            $('#walkinChange, #bottomChangeVal').removeClass('text-warning text-danger').addClass('text-success');
            $('#changeAccountRow, #bottomChangeAccountWrapper, #editChangeAccountContainer').hide();
        } else {
            $('#walkinChange, #bottomChangeVal').removeClass('text-success text-warning').addClass('text-danger');
            $('#changeAccountRow, #bottomChangeAccountWrapper, #editChangeAccountContainer').hide();
        }

        $('#subTotal1').val(tGross.toFixed(2));
        $('#subTotal2').val(tNet.toFixed(2));
        $('#discountAmount').val(orderDisc.toFixed(2));
        $('#totalBalance').val(currentInvoiceTotal.toFixed(2));
        $('input[name="cash"]').val(receipts.toFixed(2));

        // Update Premium Horizontal Card Values
        // 1. Customer Name
        let customerName = "Select Customer";
        const customerVal = $('#customerSelect').val();
        if (customerVal) {
            let customerData = null;
            if ($('#customerSelect').hasClass('select2-hidden-accessible') || $('#customerSelect').data('select2')) {
                try {
                    customerData = $('#customerSelect').select2('data');
                } catch(e) {
                    customerData = null;
                }
            }
            if (customerData && customerData.length > 0 && customerData[0].customer) {
                customerName = customerData[0].customer.customer_name;
            } else {
                const text = $('#customerSelect').find(':selected').text() || '';
                const parts = text.split(' — ');
                customerName = parts.length > 1 ? parts[1] : (text || "Select Customer");
            }
        }
        $('#cc_customer_name').text(customerName);

        // 2. Previous Balance
        const prevAbs = Math.abs(prev);
        const prevSuffix = prev >= 0 ? 'Dr' : 'Cr';
        $('#cc_prev_bal_val').text('Rs ' + prevAbs.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
        $('#cc_prev_bal_suffix').text(prevSuffix);
        if (prev >= 0) {
            $('#cc_prev_bal_val, #cc_prev_bal_suffix').removeClass('text-success').addClass('text-danger');
        } else {
            $('#cc_prev_bal_val, #cc_prev_bal_suffix').removeClass('text-danger').addClass('text-success');
        }

        // 3. Current Bill
        $('#cc_current_bill').text('Rs ' + currentInvoiceTotal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

        // 4. Paid Now
        $('#cc_paid_now').text('Rs ' + receipts.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

        // 5. Closing Balance
        const actualClosingBal = currentInvoiceTotal + prev - receipts;
        const closingAbs = Math.abs(actualClosingBal);
        const closingSuffix = actualClosingBal >= 0 ? 'Dr' : 'Cr';
        $('#cc_closing_bal_val').text('Rs ' + closingAbs.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
        $('#cc_closing_bal_suffix').text(closingSuffix);
        if (actualClosingBal >= 0) {
            $('#cc_closing_bal_val, #cc_closing_bal_suffix').removeClass('text-success').addClass('text-danger');
        } else {
            $('#cc_closing_bal_val, #cc_closing_bal_suffix').removeClass('text-danger').addClass('text-success');
        }

        if (typeof window.syncAdvanceSettlementUI === 'function') {
            window.syncAdvanceSettlementUI();
        }

        if (typeof refreshPostedState === 'function') {
            refreshPostedState();
        }
    }


    /* =========================================
       VALIDATION & SAVE
       ========================================= */

    function serializeForm() {
        return $('#saleForm').serialize();
    }

    function canPost() {
        // Fast-path: If invoice already has a positive calculated Net Total, it's definitely postable
        const currentNet = toNum($('#walkinNetTotal').text()) || toNum($('#totalBalance').val()) || toNum($('#posCartTotal').text());
        if (currentNet > 0) {
            return true;
        }

        let ok = false;
        $('#salesTableBody tr').each(function() {
            const $r = $(this);
            const pid = $r.find('.product-id-hidden').val() 
                     || $r.find('.product').val()
                     || $r.find('.product-set-id-hidden').val()
                     || ($r.find('.product option:selected').val())
                     || $r.find('.item-code-display').val();

            const cartons = toNum($r.find('.carton-qty').val());
            const loose   = toNum($r.find('.loose-pcs-input').val());
            const pieces  = toNum($r.find('.total-pieces').val());
            const qty     = toNum($r.find('.sales-qty').val());
            const amount  = toNum($r.find('.sales-amount').val());

            if ((cartons > 0 || loose > 0 || pieces > 0 || qty > 0 || amount > 0) && (pid || amount > 0)) {
                ok = true;
                return false;
            }
        });
        return ok;
    }

    function refreshPostedState() {
        const state = canPost();
        $('#btnPosted, #btnHeaderPosted, #btnSaveAndComplete, #btnSaveAndComplete2, #btnSaveAndPrint2').prop('disabled', !state);
        $('#btnDraft, #btnHeaderDraftSale, #btnSaveDraft, #btnSaveDraft2').prop('disabled', !state);
    }

    function ensureSaved() {
        return new Promise(function(resolve, reject) {
            const existing = $('#booking_id').val();
            let url = '{{ route('sales.store') }}';
            let method = 'POST';
            if (existing) {
                url = '{{ route('sales.update', ':id') }}'.replace(':id', existing);
                method = 'PUT';
            }

            $('#btnSave, #btnQuotation, #btnHeaderPosted, #btnPosted, #btnDraft, #btnHeaderDraftSale, #btnSaveDraft, #btnSaveDraft2, #btnSaveAndComplete, #btnSaveAndComplete2, #btnSaveAndPrint2').prop('disabled', true);

            let formData = new FormData($('#saleForm')[0]);
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
                method = 'POST'; // Laravel spoofing
            }

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#btnSave, #btnQuotation, #btnHeaderPosted, #btnPosted, #btnDraft, #btnHeaderDraftSale, #btnSaveDraft, #btnSaveDraft2, #btnSaveAndComplete, #btnSaveAndComplete2, #btnSaveAndPrint2').prop('disabled', false);
                    if (res?.ok) {
                        const bid = res.booking_id || existing;
                        $('#booking_id').val(bid);
                        if ($('#action').val() === 'draft') {
                            Swal.fire({
                                title: 'Saved',
                                text: 'Sale saved as draft successfully',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = res.redirect_url || "{{ route('sale.index', ['status' => 'draft']) }}";
                            });
                        } else if ($('#action').val() === 'booking') {
                            Swal.fire({
                                title: 'Saved',
                                text: 'Sale saved as booking successfully',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = res.redirect_url || "{{ route('sale.index') }}";
                            });
                        } else if ($('#action').val() === 'quotation') {
                            Swal.fire({
                                title: 'Saved',
                                text: 'Quotation created successfully!',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = res.redirect_url || "{{ route('sale.index') }}";
                            });
                        } else if ($('#action').val() === 'sales_order' || $('#sale_type').val() === 'sales_order') {
                            Swal.fire({
                                title: 'Saved',
                                text: 'Sales Order created successfully!',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = res.redirect_url || "{{ route('sales_orders.index') }}";
                            });
                        }
                        resolve(res);
                    } else {
                        Swal.fire('Error', res.msg || 'Save failed', 'error');
                        reject(res);
                    }
                },
                error: function(xhr) {
                    $('#btnSave, #btnHeaderPosted, #btnPosted').prop('disabled', false);
                    let errMsg = 'Save error';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            let resp = JSON.parse(xhr.responseText);
                            if (resp.message) errMsg = resp.message;
                        } catch(e) {}
                    }
                    Swal.fire('Error', errMsg, 'error');
                    reject(xhr);
                }
            });
        });
    }

    function postNow() {
        let formData = $('#saleForm').serializeArray();
        formData = formData.filter(item => item.name !== '_method');

        $.post('{{ route('sales.post_final') }}', $.param(formData))
            .done(function(res) {
                if (res?.ok) {
                    Swal.fire({
                        title: 'Success!',
                        text: res.msg || 'Sale saved successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.href = res.redirect_url || "{{ route('sale.index') }}", 1500);
                } else {
                    Swal.fire('Post Failed', res.msg || 'Post failed', 'error');
                }
            })
            .fail(function(xhr) {
                let errMsg = 'Post error';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        let resp = JSON.parse(xhr.responseText);
                        if (resp.message) errMsg = resp.message;
                    } catch(e) {}
                }
                Swal.fire('Error', errMsg, 'error');
            });
    }

    // Validation Utils
    function markInvalid($el) {
        $el.addClass('invalid-input invalid-select');
        $el.closest('td').addClass('invalid-cell');
    }

    function clearInvalid($el) {
        $el.removeClass('invalid-input invalid-select');
        $el.closest('td').removeClass('invalid-cell');
    }

    function clearAllInvalids() {
        $('.invalid-input, .invalid-select').removeClass('invalid-input invalid-select');
        $('.invalid-cell').removeClass('invalid-cell');
    }

    function cleanupEmptyRows() {
        $('#salesTableBody tr').each(function() {
            const $r = $(this);
            const prod = $r.find('.product-id-hidden').val() || $r.find('.product').val();

            // A row is truly empty ONLY if NO product is selected
            if (!prod || prod === '') {
                if ($('#salesTableBody tr').length > 1) {
                    $r.remove();
                } else {
                    // clear single remaining row
                    $r.find('.product').val(null).trigger('change');
                    $r.find('.product-id-hidden').val('');
                    $r.find('.item-code-display').val('');
                    $r.find('.carton-qty').val('');
                    $r.find('.loose-pcs-input').val('');
                    $r.find('.stock').val('');
                    $r.find('.variant-stock-value').val('');
                    $r.find('.visible-price').val('');
                    $r.find('.price-per-piece').val('');
                    $r.find('.sales-amount').val('0');
                    $r.find('.gross-amount').val('0');
                    $r.find('.total-pieces').val('');
                    $r.find('.sales-qty').val('0');
                }
            }
        });
        if ($('#salesTableBody tr').length === 0) addNewRow();
    }

    function validateHeader() {
        let ok = true;
        let firstMessage = null;
        let firstEl = null;

        const isWalkin = $('#is_walkin').val() === '1';

        if (isWalkin) {
            const walkinName = $('#walkinNameInput').val().trim();
            if (!walkinName) {
                ok = false;
                firstMessage = 'Please enter a Walk-in Customer Name';
                firstEl = $('#walkinNameInput');
                markInvalid($('#walkinNameInput'));
            } else {
                $('#walkinNameInput').removeClass('is-invalid border-danger');
            }
        } else {
            const cust = $('#customerSelect').val();
            if (!cust) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please select a Customer';
                    firstEl = $('#customerSelect');
                }
                markInvalid($('#customerSelect'));
            }
        }

        return {
            ok,
            firstMessage,
            firstEl
        };
    }

    function validateRows() {
        let ok = true;
        let firstMessage = null;
        let firstEl = null;

        $('#salesTableBody tr').each(function(rowIndex) {
            const $row = $(this);
            const $wh = $row.find('.warehouse');
            const $prod = $row.find('.product');
            const $cartonQtyInput = $row.find('.carton-qty');
            const $loosePcsInput = $row.find('.loose-pcs-input');
            const hiddenPid = $row.find('.product-id-hidden').val();
            const prodVal = $prod.val();

            if (!$wh.val()) {
                $wh.val("{{ auth()->user()->warehouse_id ?? 1 }}");
            }

            // If this row has no product selected:
            if (!prodVal && !hiddenPid) {
                // If there are other items in the table, remove this unselected row automatically
                if ($('#salesTableBody tr').length > 1) {
                    $row.remove();
                    return;
                }
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Bara-e-karam sale mein item select karein (Row ' + (rowIndex + 1) + ')';
                    firstEl = $prod;
                }
                markInvalid($prod);
                return;
            }

            const qtyVal = (parseFloat($cartonQtyInput.val()) || 0) + (parseFloat($loosePcsInput.val()) || 0);
            if (qtyVal <= 0) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Qty daalen row ' + (rowIndex + 1) + ' mein';
                    firstEl = $cartonQtyInput;
                }
                markInvalid($cartonQtyInput);
            }
        });

        return {
            ok,
            firstMessage,
            firstEl
        };
    }

    function validateReceipts() {
        let ok = true;
        let firstMessage = null;
        let firstEl = null;

        $('#rvWrapper .rv-row').each(function(i) {
            const $row = $(this);
            const $acc = $row.find('.rv-account');
            const $amt = $row.find('.rv-amount');
            const amtVal = parseFloat($amt.val() || '0') || 0;

            if (amtVal > 0 && (!$acc.val() || $acc.val() === "")) {
                ok = false;
                if (!firstMessage) {
                    firstMessage = 'Please select Account for receipt row ' + (i + 1);
                    firstEl = $acc;
                }
                markInvalid($acc);
            }
        });

        return {
            ok,
            firstMessage,
            firstEl
        };
    }

    function validateFormAll() {
        clearAllInvalids();

        const h = validateHeader();
        if (!h.ok) return {
            ok: false,
            message: h.firstMessage,
            el: h.firstEl
        };

        const r = validateRows();
        if (!r.ok) return {
            ok: false,
            message: r.firstMessage,
            el: r.firstEl
        };

        const rec = validateReceipts();
        if (!rec.ok) return {
            ok: false,
            message: rec.firstMessage,
            el: rec.firstEl
        };

        return {
            ok: true
        };
    }


    /* =========================================
       EVENT BINDINGS
    ========================================= */

    // Customer Type change logic -> controls Walk-in behavior
    function handleCustomerTypeChange() {
        const type = $('#partyTypeSelect').val();
        const isWalkin = (type === 'Walking Customer');
        
        // Update hidden field value
        $('#is_walkin').val(isWalkin ? '1' : '0');
        
        if (isWalkin) {
            $('#customerSelect').addClass('d-none').next('.select2-container').addClass('d-none');
            $('#walkinNameInput').removeClass('d-none');
            $('#btnOpenAddCustomerModal').hide();
            
            $('#receiptVouchersSection').hide();
            $('#totalsSection').removeClass('col-lg-5').addClass('col-lg-12');
            $('#totalsCustomerView').addClass('d-none').removeClass('d-flex');
            $('#totalsWalkinView').removeClass('d-none').addClass('d-flex');
            $('#rvWrapper').appendTo('#walkinReceiptsContainer');
        } else {
            $('#walkinNameInput').addClass('d-none');
            $('#customerSelect').removeClass('d-none').next('.select2-container').removeClass('d-none');
            $('#btnOpenAddCustomerModal').show();
            
            $('#receiptVouchersSection').show();
            $('#totalsSection').removeClass('col-lg-12').addClass('col-lg-5');
            $('#totalsWalkinView').addClass('d-none').removeClass('d-flex');
            $('#totalsCustomerView').removeClass('d-none').addClass('d-flex');
            $('#rvWrapper').appendTo('#receiptVouchersSection .card-body');
        }
        if (typeof updateGrandTotals === 'function') updateGrandTotals();
    }

    $(document).on('change', '#partyTypeSelect', function() {
        handleCustomerTypeChange();
    });


    $(document).ready(function() {

        // Walk-in UI Event Bindings
        $(document).on('input', '#walkinDiscountRs, #shippingChargesInput, #bottomShippingChargesInput, .shipping-charges-input, #cuttingChargesInput, #bottomCuttingChargesInput, .cutting-charges-input', function() {
            if ($(this).hasClass('shipping-charges-input') || $(this).attr('id') === 'shippingChargesInput' || $(this).attr('id') === 'bottomShippingChargesInput') {
                const val = $(this).val();
                $('#shippingChargesInput, #bottomShippingChargesInput, .shipping-charges-input').not(this).val(val);
            }
            if ($(this).hasClass('cutting-charges-input') || $(this).attr('id') === 'cuttingChargesInput' || $(this).attr('id') === 'bottomCuttingChargesInput') {
                const val = $(this).val();
                $('#cuttingChargesInput, #bottomCuttingChargesInput, .cutting-charges-input').not(this).val(val);
            }
            updateGrandTotals();
        });
        
        // Ensure initial UI setup on page load
        handleCustomerTypeChange();

        // Remove invalid classes on input
        $(document).on('input change', 'select, input, textarea', function() {
            clearInvalid($(this));
        });

        // ========================================================
        // PRODUCT SETS / COMBO PAIRING LOGIC
        // ========================================================
        window.isPopulatingSet = false;

        // Check if selected product belongs to any Product Set (DISABLED: Sets are now selected directly from product search without popups)
        window.checkProductSetAndPrompt = function($row, pid, productData) {
            return; // Completely disabled to prevent unwanted popups

            const currentWarehouseId = $row.find('.warehouse').val() || $('#warehouseSelect').val() || 1;
            const checkUrl = "{{ url('product-sets/check-product') }}/" + pid;

            $.get(checkUrl, {
                warehouse_id: currentWarehouseId,
                _t: new Date().getTime()
            }).done(function(setRes) {
                if (setRes && setRes.has_set && setRes.sets && setRes.sets.length > 0) {
                    promptUserForSet(setRes.sets, $row, productData);
                }
            }).fail(function(err) {
                console.error('Error checking product set:', err);
            });
        };

        // Prompt user: Sell Individually or Sell Complete Set
        window.promptUserForSet = function(sets, $row, productData) {
            if (!sets || sets.length === 0) return;

            let selectedSet = sets[0];

            function renderItemsHtml(set) {
                let html = '';
                set.items.forEach(function(item) {
                    let stockBadge = (item.stock_pieces > 0)
                        ? `<span class="badge bg-success text-white px-2 py-0" style="font-size:10px;">Stock: ${item.stock_pieces}</span>`
                        : `<span class="badge bg-danger text-white px-2 py-0" style="font-size:10px;">Out of stock</span>`;

                    let weightInfo = '';
                    if (item.weight_per_piece && parseFloat(item.weight_per_piece) > 0) {
                        weightInfo = `<span class="badge bg-info-subtle text-info border border-info-subtle px-1" style="font-size:10px;" title="Weight per piece"><i class="fas fa-weight-hanging me-1"></i>${parseFloat(item.weight_per_piece)}g / pc</span>`;
                    }

                    let unitLabel = item.variant_unit || item.unit_name || '';

                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                            <div>
                                <i class="fas fa-cube text-primary me-2"></i>
                                <strong class="text-dark">${item.variant_name || item.item_name}</strong>
                                ${item.item_code ? '<small class="text-muted ms-1 font-monospace">[' + item.item_code + ']</small>' : ''}
                                ${weightInfo ? '<div class="mt-1">' + weightInfo + '</div>' : ''}
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white font-monospace px-2 py-1" style="font-size:11px;">
                                    Qty: ${item.set_qty} ${unitLabel}
                                </span>
                                ${stockBadge}
                            </div>
                        </li>
                    `;
                });
                return html;
            }

            let setSelectHtml = '';
            if (sets.length > 1) {
                let options = '';
                sets.forEach(function(s, idx) {
                    options += `<option value="${idx}">${s.name} (${s.items_count} items)</option>`;
                });
                setSelectHtml = `
                    <div class="mb-2 text-start">
                        <label class="form-label small fw-bold text-muted mb-1">Select Set / Combo:</label>
                        <select id="swalSetSelector" class="form-select form-select-sm fw-bold border-primary text-primary">
                            ${options}
                        </select>
                    </div>
                `;
            }

            const prodDisplayName = productData ? (productData.name || productData.text || 'This item') : 'This item';

            Swal.fire({
                title: '<div class="d-flex align-items-center justify-content-center gap-2 text-primary" style="font-size: 1.2rem;"><i class="fas fa-layer-group"></i> Product Combo / Set Found!</div>',
                html: `
                    <div style="text-align: left; font-size: 0.92rem; line-height: 1.5;">
                        <p class="mb-2 text-dark">
                            Product <strong class="text-primary">${prodDisplayName}</strong> is part of a paired Product Set.
                        </p>

                        ${setSelectHtml}

                        <div class="p-2 mb-3 rounded-3 bg-light border border-primary-subtle shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                <span class="fw-bold text-primary" id="swalCurrentSetName"><i class="fas fa-box-open me-1"></i> ${selectedSet.name}</span>
                                <span class="badge bg-info text-white" id="swalCurrentSetCount">${selectedSet.items_count} Products</span>
                            </div>
                            <div class="small fw-semibold text-secondary mb-1">Components included in 1 Set:</div>
                            <ul class="list-group list-group-flush rounded bg-white border" id="swalSetItemsList" style="max-height: 180px; overflow-y: auto;">
                                ${renderItemsHtml(selectedSet)}
                            </ul>

                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="fw-bold text-dark small"><i class="fas fa-calculator text-primary me-1"></i> How many sets to add?</span>
                                <div class="input-group input-group-sm" style="width: 130px;">
                                    <span class="input-group-text bg-white fw-bold">Qty</span>
                                    <input type="number" id="swalSetQtyInput" class="form-control text-center fw-bold text-primary fs-6" value="1" min="1" step="1">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.8rem;">
                            <i class="fas fa-info-circle me-1"></i> <strong>Pura Set:</strong> Tamam items unki set quantity ke mutabiq alag alag rows mein invoice mein add hongay aur stock perfectly deduct hoga.
                        </div>
                    </div>
                `,
                didOpen: () => {
                    if (sets.length > 1) {
                        $('#swalSetSelector').on('change', function() {
                            const idx = parseInt($(this).val()) || 0;
                            selectedSet = sets[idx];
                            $('#swalCurrentSetName').html('<i class="fas fa-box-open me-1"></i> ' + selectedSet.name);
                            $('#swalCurrentSetCount').text(selectedSet.items_count + ' Products');
                            $('#swalSetItemsList').html(renderItemsHtml(selectedSet));
                        });
                    }
                },
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#2563eb', // Primary
                denyButtonColor: '#10b981',    // Emerald
                cancelButtonColor: '#64748b',  // Gray
                confirmButtonText: '<i class="fas fa-boxes me-1"></i> Sell Complete Set (پورا سیٹ)',
                denyButtonText: '<i class="fas fa-cube me-1"></i> Sell Individually (صرف یہی آئٹم)',
                cancelButtonText: 'Cancel',
                reverseButtons: false,
                focusConfirm: true,
                width: '540px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User chose Complete Set
                    const setsQty = Math.max(1, parseInt($('#swalSetQtyInput').val()) || 1);
                    populateSetToGrid(selectedSet, setsQty, $row);
                } else if (result.isDenied) {
                    // User chose Individual
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: `Selling "${prodDisplayName}" individually`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } else {
                    // Cancelled: clear the row
                    $row.find('.product').val(null).trigger('change');
                    $row.find('.product-id-hidden').val('');
                    $row.find('.item-code-display').val('');
                    $row.find('.stock').val('');
                    $row.find('.carton-qty').val('');
                    $row.find('.total-pieces').val('');
                    $row.find('.visible-price').val('');
                    $row.find('.sales-amount').val('0');
                    updateGrandTotals();
                    refreshPostedState();
                }
            });
        };

        // Populate all components of a set into the sales invoice grid
        window.populateSetToGrid = function(set, setsCount, $triggerRow) {
            if (!set || !set.items || set.items.length === 0) return;

            window.isPopulatingSet = true;

            set.items.forEach(function(item, idx) {
                let $targetRow = null;
                if (idx === 0 && $triggerRow && $triggerRow.length) {
                    $targetRow = $triggerRow;
                } else {
                    let $lastRow = $('#salesTableBody tr:last');
                    let lastPid = $lastRow.find('.product-id-hidden').val() || $lastRow.find('.product').val();
                    if (!lastPid && $lastRow.length && $lastRow[0] !== ($triggerRow ? $triggerRow[0] : null)) {
                        $targetRow = $lastRow;
                    } else {
                        addNewRow();
                        $targetRow = $('#salesTableBody tr:last');
                    }
                }

                const calculatedQty = (parseFloat(item.set_qty) || 1) * setsCount;
                const itemDisplayName = item.variant_name || item.item_name;
                const displayText = itemDisplayName + (item.item_code ? ' [' + item.item_code + ']' : '');
                
                let optionVal = item.product_id;
                if (item.variant_data) {
                    optionVal = item.product_id + '|variant|' + item.variant_data;
                }

                const itemUnit = item.variant_unit || item.unit_name || 'Pcs';
                const stockVal = (item.stock !== undefined && item.stock !== null) ? item.stock : (item.stock_pieces || 0);

                const selectData = {
                    id: optionVal,
                    text: displayText,
                    name: displayText,
                    sku: item.item_code || '',
                    stock: stockVal,
                    stock_pieces: item.stock_pieces || 0,
                    retail_price: item.retail_price || item.sale_price_per_piece || 0,
                    trade_price: item.retail_price || item.sale_price_per_piece || 0,
                    wholesale_price: item.wholesale_price || 0,
                    size_mode: item.size_mode || 'by_pieces',
                    pieces_per_box: item.pieces_per_box || 1,
                    unit_name: itemUnit,
                    variant_data: item.variant_data || '',
                    weight_per_piece: item.weight_per_piece || 0
                };

                const $select = $targetRow.find('.product');
                let newOption = new Option(displayText, optionVal, true, true);
                $(newOption).data('data', selectData);
                $select.empty().append(newOption).trigger('change');

                $targetRow.find('.product-id-hidden').val(item.product_id);
                $targetRow.find('.item-code-display').val(item.item_code || '');
                $targetRow.find('.stock').val(stockVal);
                $targetRow.find('.variant-stock-value').val(stockVal);
                $targetRow.find('.carton-qty').val(calculatedQty);
                $targetRow.find('.total-pieces').val(calculatedQty);
                $targetRow.find('.sales-qty').val(calculatedQty);
                $targetRow.find('.pack-qty').val(item.pieces_per_box || 1);
                $targetRow.find('.retail-price').val(item.retail_price || item.sale_price_per_piece || 0);
                $targetRow.find('.wholesale-price').val(item.wholesale_price || 0);

                let rowMode = $targetRow.find('.price-mode-row-toggle').attr('data-mode') || 'retail';
                let rate = (rowMode === 'wholesale' && item.wholesale_price > 0) ? item.wholesale_price : (item.retail_price || item.sale_price_per_piece || 0);
                $targetRow.find('.visible-price').val(rate);
                $targetRow.find('.price-per-piece').val(rate);

                // Set variant attributes
                $targetRow.find('.variant-data-hidden').val(item.variant_data || '');
                $targetRow.find('.weight-per-piece').val(item.weight_per_piece || 0);
                $targetRow.find('.size-mode-text').val(item.size_mode || 'by_pieces');

                // Parse variant data for size/color display
                let variantSize = '-';
                let variantColor = '-';
                if (item.variant_data) {
                    try {
                        let vd;
                        try {
                            vd = JSON.parse(atob(item.variant_data));
                        } catch(e) {
                            vd = JSON.parse(item.variant_data);
                        }
                        variantSize = (vd.size && vd.size !== '-') ? vd.size : '-';
                        variantColor = (vd.color && vd.color !== '-') ? vd.color : '-';
                    } catch(ex) {}
                }
                $targetRow.find('.size-display').val(variantSize !== '-' ? variantSize : '');
                $targetRow.find('.color-display').val(variantColor);

                $targetRow.data('size_mode', item.size_mode || 'by_pieces');
                $targetRow.data('pieces_per_box', item.pieces_per_box || 1);

                setupRowQtyToggle($targetRow, item.size_mode || 'by_pieces', itemUnit);
                computeRow($targetRow);
            });

            updateGrandTotals();
            refreshPostedState();

            window.isPopulatingSet = false;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Added ${setsCount} Set(s) of "${set.name}"!`,
                    showConfirmButton: false,
                    timer: 2200
                });
            }
        };

        // Product change
        $('#salesTableBody').on('select2:select', '.product', function(e) {
            console.log("product change in shared logic");
            if (window.isEditModeLoading) return; // Block during edit load
            
            const data = e.params.data;
            if (!data.id) return;
            
            const $row = $(this).closest('tr');

            // Handle Product Set / Combo selection directly
            const isSet = data.is_set || (data.id && data.id.toString().startsWith('set_'));
            if (isSet) {
                const setId = data.set_id || data.id.toString().replace('set_', '');
                $row.find('.product-id-hidden').val('set_' + setId);
                if ($row.find('.product-set-id-hidden').length === 0) {
                    $row.find('.col-product').append(`<input type="hidden" class="product-set-id-hidden" name="product_set_id[]" value="${setId}">`);
                    $row.find('.col-product').append(`<input type="hidden" class="is-set-hidden" name="is_set[]" value="1">`);
                } else {
                    $row.find('.product-set-id-hidden').val(setId);
                    $row.find('.is-set-hidden').val('1');
                }

                $row.find('.variant-data-hidden').val('');
                $row.find('.size-display').val('-');
                $row.find('.color-display').val('-');
                $row.find('.stock').val('SET');
                $row.find('.variant-stock-value').val('SET');
                $row.find('.item-code-display').val(data.sku || data.item_code || '');

                const setPrice = parseFloat(data.retail_price || data.trade_price || 0);
                $row.find('.retail-price').val(setPrice);
                $row.find('.wholesale-price').val(setPrice);
                $row.find('.visible-price').val(setPrice);
                $row.find('.price-per-piece').val(setPrice);
                $row.find('.weight-per-piece').val(0);

                if (!$row.find('.carton-qty').val() || parseFloat($row.find('.carton-qty').val()) <= 0) {
                    $row.find('.carton-qty').val(1);
                }
                $row.find('.total-pieces').val($row.find('.carton-qty').val() || 1);

                $row.find('.qty-unit-toggle').addClass('d-none').attr('data-unit-mode', 'main').text('Set');
                $row.data('size_mode', 'by_pieces');
                $row.data('pieces_per_box', 1);

                computeRow($row);
            updateGrandTotals();
            refreshPostedState();
                return;
            }

            // Normal product selected: clear set hidden fields
            $row.find('.product-set-id-hidden').val('');
            $row.find('.is-set-hidden').val('0');
            
            let pid = data.id.toString().split('|')[0];
            $row.find('.product-id-hidden').val(pid);
            $row.find('.variant-data-hidden').val(data.variant_data || '');
            $row.data('product_info', {
                name: data.name || data.text || '',
                sku: data.sku || data.item_code || '',
                unit: data.unit_name || ''
            });
            if (data.warehouses && data.warehouses.length > 0) {
                $row.data('product_warehouses', data.warehouses);
            }
            
            // Parse variant data for size/color display
            let variantSize = '-';
            let variantColor = '-';
            let variantStock = null;
            if (data.variant_data) {
                try {
                    const vd = JSON.parse(atob(data.variant_data));
                    variantSize = (vd.size && vd.size !== '-') ? vd.size : '-';
                    variantColor = (vd.color && vd.color !== '-') ? vd.color : '-';
                    // Prefer vd.current_stock from parsed variant_data (highly reliable), fallback to data.stock or vd.stock
                    variantStock = vd.current_stock !== undefined ? vd.current_stock : (data.stock !== undefined ? data.stock : (vd.stock !== undefined ? vd.stock : null));
                } catch(ex) {}
            }
            
            // Set size and color display
            $row.find('.size-display').val(variantSize !== '-' ? variantSize : '');
            $row.find('.color-display').val(variantColor);
            
            // Store variant stock for later use (after warehouse loads)
            if (variantStock !== null) {
                $row.find('.variant-stock-value').val(variantStock);
                $row.find('.stock').val(variantStock);
            } else {
                $row.find('.variant-stock-value').val('');
            }
            
            // Set properties directly
            $row.find('.item-code-display').val(data.sku || data.item_code || '');
            $row.find('.retail-price').val(data.retail_price || data.trade_price || 0);
            $row.find('.wholesale-price').val(data.wholesale_price || 0);
            $row.find('.weight-per-piece').val(data.weight_per_piece || 0);
            
            $row.find('.pack-qty').val(data.pieces_per_box || 1);
            $row.find('.size-h').val(data.height || '-');
            $row.find('.size-w').val(data.width || '-');
            $row.find('.size-mode-text').val(data.size_mode || '-');
            
            $row.find('.discount-value').val(data.sale_discount_percent || 0);
            
            $row.data('size_mode', data.size_mode);
            $row.data('pieces_per_box', data.pieces_per_box || 1);
            
            let variantUnit = null;
            if (data.variant_data) {
                try {
                    const vd = JSON.parse(atob(data.variant_data));
                    variantUnit = vd.unit || null;
                } catch(e) {}
            }
            if (!variantUnit && data.unit_name) {
                variantUnit = data.unit_name;
            }

            setupRowQtyToggle($row, data.size_mode, variantUnit);

            let rowMode = $row.find('.price-mode-row-toggle').attr('data-mode') || 'retail';
            let wsPrice = parseFloat(data.wholesale_price) || 0;
            let rate = (rowMode === 'wholesale' && wsPrice > 0) ? wsPrice : (data.retail_price || data.trade_price || 0);

            const ppb = parseFloat(data.pieces_per_box) || 1;
            const unitMode = $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'ctn';
            if (data.size_mode === 'by_cartons' || data.size_mode === 'by_box') {
                let cartonPrice = ppb > 1 ? (rate * ppb) : rate;
                $row.find('.visible-price').val(unitMode === 'pcs' ? rate : cartonPrice);
                $row.find('.price-per-piece').val(unitMode === 'pcs' ? rate : cartonPrice);
            } else {
                $row.find('.visible-price').val(rate);
                $row.find('.price-per-piece').val(rate);
            }

            loadWarehousesForProduct($row, pid);

            computeRow($row);
            updateGrandTotals();
            refreshPostedState();

            // Check & Notify Customer's Recent Sold Price
            const currentSelectedCustId = $('#customerSelect').val();
            if (currentSelectedCustId && pid) {
                checkCustomerLastPrice($row, pid, currentSelectedCustId, true);
            }

            // NOTE: Annoying popup checkProductSetAndPrompt disabled as per user instruction.
            // When selling individual products, sell individually without interruption.
            if (!window.suppressLinkedProductPrompt && pid && !isSet) {
                checkAndPromptLinkedProducts(pid, $row);
            }
        });

        // Customer selection change -> re-check recent sale prices for all loaded products
        $(document).on('change select2:select', '#customerSelect', function() {
            const newCustId = $(this).val();
            $('#salesTableBody tr').each(function() {
                const $r = $(this);
                const pVal = $r.find('.product-id-hidden').val() || $r.find('.product').val();
                if (pVal && newCustId) {
                    checkCustomerLastPrice($r, pVal, newCustId, false);
                } else {
                    $r.find('.last-sale-badge-container').remove();
                }
            });
        });

    function setupRowQtyToggle($row, sizeMode, variantUnit = null) {
        const $toggleBtn = $row.find('.qty-unit-toggle');
        const vUnitNorm = (variantUnit || '').toLowerCase().trim();
        if (sizeMode === 'by_cartons' || sizeMode === 'by_box') {
            const isPcs = (vUnitNorm === 'pcs' || vUnitNorm === 'pc' || vUnitNorm === 'piece');
            $toggleBtn.removeClass('d-none')
                      .attr('data-unit-mode', isPcs ? 'pcs' : 'ctn')
                      .text(isPcs ? 'Pcs' : 'Ctn')
                      .removeClass('btn-outline-primary btn-outline-info btn-outline-warning btn-outline-success')
                      .addClass(isPcs ? 'btn-outline-info' : 'btn-outline-success');
            $row.find('.hidden-sub-unit-mode').val(isPcs ? 'pcs' : 'ctn');
            $row.find('.carton-qty').attr('placeholder', '0');
        } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
            if (vUnitNorm === 'pcs' || vUnitNorm === 'pc' || vUnitNorm === 'piece') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'pcs')
                          .text('Pcs')
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val('pcs');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'ft' || vUnitNorm === 'feet' || vUnitNorm === 'foot') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'ft')
                          .text('Ft')
                          .removeClass('btn-outline-primary btn-outline-info btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('ft');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'meter' || vUnitNorm === 'mtr' || vUnitNorm === 'm') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'm')
                          .text('Mtr')
                          .removeClass('btn-outline-primary btn-outline-info btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('m');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'box' || vUnitNorm === 'carton' || vUnitNorm === 'dozen' || vUnitNorm === 'roll') {
                const uLabel = variantUnit || 'Unit';
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', vUnitNorm)
                          .text(uLabel)
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val(vUnitNorm);
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (sizeMode === 'by_gm' || vUnitNorm === 'gm' || vUnitNorm === 'g') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'gm')
                          .text('Gm')
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val('gm');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'kg')
                          .text('Kg')
                          .removeClass('btn-outline-info btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-primary');
                $row.find('.hidden-sub-unit-mode').val('kg');
                $row.find('.carton-qty').attr('placeholder', '0');
            }
        } else if (sizeMode === 'by_feet') {
            if (vUnitNorm === 'pcs' || vUnitNorm === 'pc' || vUnitNorm === 'piece') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'pcs')
                          .text('Pcs')
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val('pcs');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'meter' || vUnitNorm === 'mtr' || vUnitNorm === 'm') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'm')
                          .text('Mtr')
                          .removeClass('btn-outline-primary btn-outline-info btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('m');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'kg' || vUnitNorm === 'kgs') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'kg')
                          .text('Kg')
                          .removeClass('btn-outline-info btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('kg');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'box' || vUnitNorm === 'carton' || vUnitNorm === 'roll' || vUnitNorm === 'dozen') {
                const uLabel = variantUnit || 'Unit';
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', vUnitNorm)
                          .text(uLabel)
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val(vUnitNorm);
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'in' || vUnitNorm === 'inch' || vUnitNorm === 'inches') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'in')
                          .text('In')
                          .removeClass('btn-outline-primary btn-outline-info btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('in');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'ft')
                          .text('Ft')
                          .removeClass('btn-outline-info btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-primary');
                $row.find('.hidden-sub-unit-mode').val('ft');
                $row.find('.carton-qty').attr('placeholder', '0');
            }
        } else if (sizeMode === 'by_meter') {
            if (vUnitNorm === 'pcs' || vUnitNorm === 'pc' || vUnitNorm === 'piece') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'pcs')
                          .text('Pcs')
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val('pcs');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'kg' || vUnitNorm === 'kgs') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'kg')
                          .text('Kg')
                          .removeClass('btn-outline-info btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('kg');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'ft' || vUnitNorm === 'feet' || vUnitNorm === 'foot') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'ft')
                          .text('Ft')
                          .removeClass('btn-outline-primary btn-outline-info btn-outline-success')
                          .addClass('btn-outline-warning');
                $row.find('.hidden-sub-unit-mode').val('ft');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'box' || vUnitNorm === 'carton' || vUnitNorm === 'roll' || vUnitNorm === 'dozen') {
                const uLabel = variantUnit || 'Unit';
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', vUnitNorm)
                          .text(uLabel)
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val(vUnitNorm);
                $row.find('.carton-qty').attr('placeholder', '0');
            } else if (vUnitNorm === 'mm') {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'mm')
                          .text('mm')
                          .removeClass('btn-outline-primary btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-info');
                $row.find('.hidden-sub-unit-mode').val('mm');
                $row.find('.carton-qty').attr('placeholder', '0');
            } else {
                $toggleBtn.removeClass('d-none')
                          .attr('data-unit-mode', 'm')
                          .text('Mtr')
                          .removeClass('btn-outline-info btn-outline-warning btn-outline-success')
                          .addClass('btn-outline-primary');
                $row.find('.hidden-sub-unit-mode').val('m');
                $row.find('.carton-qty').attr('placeholder', '0');
            }
        } else {
            $toggleBtn.addClass('d-none').attr('data-unit-mode', 'main');
            $row.find('.hidden-sub-unit-mode').val('main');
            $row.find('.carton-qty').attr('placeholder', '0');
        }
    }

    // Toggle Qty Sub-Unit (Ctn ↔ Pcs / Kg ↔ Gm / Ft ↔ In)
    $(document).on('click', '.qty-unit-toggle', function () {
        const $btn = $(this);
        const $row = $btn.closest('tr');
        const sizeMode = $row.data('size_mode') || $row.find('.size-mode-text').val();
        let currentMode = $btn.attr('data-unit-mode');

        if (sizeMode === 'by_cartons' || sizeMode === 'by_box') {
            const packQty = parseFloat($row.find('.pack-qty').val()) || 1;
            const $priceInp = $row.find('.visible-price');
            let curPrice = parseFloat($priceInp.val()) || 0;
            let retailPrice = parseFloat($row.find('.retail-price').val()) || 0;
            let wholesalePrice = parseFloat($row.find('.wholesale-price').val()) || 0;
            let rowMode = $row.find('.price-mode-row-toggle').attr('data-mode') || 'retail';
            let basePiecePrice = (rowMode === 'wholesale' && wholesalePrice > 0) ? wholesalePrice : retailPrice;

            if (currentMode === 'ctn') {
                currentMode = 'pcs';
                $btn.attr('data-unit-mode', 'pcs').text('Pcs').removeClass('btn-outline-success').addClass('btn-outline-info');
                $row.find('.carton-qty').attr('placeholder', '0');

                // Convert Quantity from Ctn (dot notation) to Pcs
                const curQtyStr = ($row.find('.carton-qty').val() || '').toString().trim();
                if (curQtyStr !== '') {
                    if (curQtyStr.includes('.')) {
                        const parts = curQtyStr.split('.');
                        const boxes = parseInt(parts[0]) || 0;
                        const loose = parseInt(parts[1]) || 0;
                        const totalPcs = (boxes * packQty) + loose;
                        $row.find('.carton-qty').val(totalPcs);
                    } else {
                        const boxes = parseFloat(curQtyStr) || 0;
                        $row.find('.carton-qty').val(boxes * packQty);
                    }
                }

                if (packQty > 1 && curPrice > 0) {
                    let piecePrice = curPrice / packQty;
                    $priceInp.val(piecePrice % 1 === 0 ? piecePrice : piecePrice.toFixed(2));
                } else if (basePiecePrice > 0) {
                    $priceInp.val(basePiecePrice);
                }
                $row.find('.price-per-piece').val($priceInp.val());
                const validWhs = $row.data('product_warehouses') || [];
                const curWhId = $row.find('.warehouse').val();
                const curWh = validWhs.find(w => w.warehouse_id == curWhId);
                if (curWh) {
                    $row.find('.stock').val(curWh.stock);
                }
            } else {
                currentMode = 'ctn';
                $btn.attr('data-unit-mode', 'ctn').text('Ctn').removeClass('btn-outline-info').addClass('btn-outline-success');
                $row.find('.carton-qty').attr('placeholder', '0');

                // Convert Quantity from Pcs to Ctn (dot notation)
                const curPcs = parseFloat($row.find('.carton-qty').val()) || 0;
                if (curPcs > 0 && packQty > 1) {
                    const boxes = Math.floor(curPcs / packQty);
                    const loose = Math.round(curPcs % packQty);
                    $row.find('.carton-qty').val(loose > 0 ? `${boxes}.${loose}` : boxes);
                }

                if (packQty > 1 && curPrice > 0) {
                    let cartonPrice = curPrice * packQty;
                    $priceInp.val(cartonPrice % 1 === 0 ? cartonPrice : cartonPrice.toFixed(2));
                } else if (basePiecePrice > 0) {
                    let cartonPrice = packQty > 1 ? (basePiecePrice * packQty) : basePiecePrice;
                    $priceInp.val(cartonPrice % 1 === 0 ? cartonPrice : cartonPrice.toFixed(2));
                }
                $row.find('.price-per-piece').val($priceInp.val());
                const validWhs = $row.data('product_warehouses') || [];
                const curWhId = $row.find('.warehouse').val();
                const curWh = validWhs.find(w => w.warehouse_id == curWhId);
                if (curWh) {
                    const ppb = parseFloat(curWh.ppb) || 1;
                    const boxes = Math.floor(curWh.boxes || 0);
                    const loose = curWh.stock % ppb;
                    $row.find('.stock').val(loose > 0 ? `${boxes}.${loose}` : boxes);
                }
            }
            $row.find('.hidden-sub-unit-mode').val(currentMode);
            computeRow($row);
            updateGrandTotals();
            refreshPostedState();
        } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
            const $priceInp = $row.find('.visible-price');
            let curPrice = parseFloat($priceInp.val()) || 0;
            const curQty = parseFloat($row.find('.carton-qty').val()) || 0;

            let convFactor = 0;
            let variantUnit = '';
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try {
                        vd = JSON.parse(atob(vdEncoded));
                    } catch(e) {
                        vd = JSON.parse(vdEncoded);
                    }
                    if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                        convFactor = parseFloat(vd.conv_factor);
                    } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                        convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                    }
                    if (vd.unit) variantUnit = vd.unit;
                } catch(e) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 1000.0;
            }

            if (currentMode === 'kg') {
                if (convFactor > 0 && convFactor !== 1.0) {
                    const uNorm = (variantUnit || 'pcs').toLowerCase().trim();
                    currentMode = uNorm;
                    const uLabel = variantUnit || 'Pcs';
                    $btn.attr('data-unit-mode', currentMode).text(uLabel).removeClass('btn-outline-primary btn-outline-info btn-outline-warning').addClass('btn-outline-info');
                    $row.find('.carton-qty').attr('placeholder', '0');
                    if (curQty > 0) {
                        $row.find('.carton-qty').val(parseFloat((curQty / convFactor).toFixed(4)).toString());
                    }
                    if (curPrice > 0) {
                        let unitP = curPrice * convFactor;
                        $priceInp.val(unitP % 1 === 0 ? unitP : unitP.toFixed(2));
                        $row.find('.price-per-piece').val($priceInp.val());
                    }
                } else {
                    currentMode = 'gm';
                    $btn.attr('data-unit-mode', 'gm').text('Gm').removeClass('btn-outline-primary btn-outline-info').addClass('btn-outline-info');
                    $row.find('.carton-qty').attr('placeholder', 'Gm');
                    if (curQty > 0) {
                        $row.find('.carton-qty').val(Math.round(curQty * 1000));
                    }
                    if (curPrice > 0) {
                        let gmPrice = curPrice / 1000;
                        $priceInp.val(gmPrice.toFixed(4).replace(/\.?0+$/, ''));
                        $row.find('.price-per-piece').val($priceInp.val());
                    }
                }
            } else if (currentMode === 'gm') {
                currentMode = 'kg';
                $btn.attr('data-unit-mode', 'kg').text('Kg').removeClass('btn-outline-info').addClass('btn-outline-primary');
                $row.find('.carton-qty').attr('placeholder', 'Kg');
                if (curQty > 0) {
                    $row.find('.carton-qty').val(curQty / 1000);
                }
                if (curPrice > 0) {
                    let kgPrice = curPrice * 1000;
                    $priceInp.val(kgPrice % 1 === 0 ? kgPrice : kgPrice.toFixed(2));
                    $row.find('.price-per-piece').val($priceInp.val());
                }
            } else {
                // Currently in variant unit (Ft, Mtr, Pcs, etc.) -> toggle to Kg
                currentMode = 'kg';
                $btn.attr('data-unit-mode', 'kg').text('Kg').removeClass('btn-outline-info btn-outline-warning').addClass('btn-outline-primary');
                $row.find('.carton-qty').attr('placeholder', 'Kg');
                if (curQty > 0 && convFactor > 0) {
                    $row.find('.carton-qty').val(parseFloat((curQty * convFactor).toFixed(4)).toString());
                }
                if (curPrice > 0 && convFactor > 0) {
                    let kgPrice = curPrice / convFactor;
                    $priceInp.val(kgPrice % 1 === 0 ? kgPrice : kgPrice.toFixed(2));
                    $row.find('.price-per-piece').val($priceInp.val());
                }
            }
        } else if (sizeMode === 'by_meter') {
            const $priceInp = $row.find('.visible-price');
            let curPrice = parseFloat($priceInp.val()) || 0;
            const curQty = parseFloat($row.find('.carton-qty').val()) || 0;

            let convFactor = 0;
            let variantUnit = '';
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try {
                        vd = JSON.parse(atob(vdEncoded));
                    } catch(e) {
                        vd = JSON.parse(vdEncoded);
                    }
                    if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                        convFactor = parseFloat(vd.conv_factor);
                    } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                        convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                    }
                    if (vd.unit) variantUnit = vd.unit;
                } catch(e) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 1000.0;
            }

            if (currentMode === 'm') {
                if (convFactor > 0 && convFactor !== 1.0) {
                    const uNorm = (variantUnit || 'pcs').toLowerCase().trim();
                    currentMode = uNorm;
                    const uLabel = variantUnit || 'Pcs';
                    $btn.attr('data-unit-mode', currentMode).text(uLabel).removeClass('btn-outline-primary btn-outline-info btn-outline-warning').addClass('btn-outline-info');
                    $row.find('.carton-qty').attr('placeholder', '0');
                    if (curQty > 0) {
                        $row.find('.carton-qty').val(parseFloat((curQty / convFactor).toFixed(4)).toString());
                    }
                    if (curPrice > 0) {
                        let unitP = curPrice * convFactor;
                        $priceInp.val(unitP % 1 === 0 ? unitP : unitP.toFixed(2));
                        $row.find('.price-per-piece').val($priceInp.val());
                    }
                } else {
                    currentMode = 'mm';
                    $btn.attr('data-unit-mode', 'mm').text('mm').removeClass('btn-outline-primary btn-outline-info').addClass('btn-outline-info');
                    $row.find('.carton-qty').attr('placeholder', 'mm');
                    if (curQty > 0) {
                        $row.find('.carton-qty').val(Math.round(curQty * 1000));
                    }
                    if (curPrice > 0) {
                        let mmPrice = curPrice / 1000;
                        $priceInp.val(mmPrice.toFixed(4).replace(/\.?0+$/, ''));
                        $row.find('.price-per-piece').val($priceInp.val());
                    }
                }
            } else if (currentMode === 'mm') {
                currentMode = 'm';
                $btn.attr('data-unit-mode', 'm').text('Mtr').removeClass('btn-outline-info').addClass('btn-outline-primary');
                $row.find('.carton-qty').attr('placeholder', 'Mtr');
                if (curQty > 0) {
                    $row.find('.carton-qty').val(curQty / 1000);
                }
                if (curPrice > 0) {
                    let mPrice = curPrice * 1000;
                    $priceInp.val(mPrice % 1 === 0 ? mPrice : mPrice.toFixed(2));
                    $row.find('.price-per-piece').val($priceInp.val());
                }
            } else {
                // Currently in variant unit (Kg, Ft, Pcs, etc.) -> toggle to Mtr
                currentMode = 'm';
                $btn.attr('data-unit-mode', 'm').text('Mtr').removeClass('btn-outline-info btn-outline-warning').addClass('btn-outline-primary');
                $row.find('.carton-qty').attr('placeholder', 'Mtr');
                if (curQty > 0 && convFactor > 0) {
                    $row.find('.carton-qty').val(parseFloat((curQty * convFactor).toFixed(4)).toString());
                }
                if (curPrice > 0 && convFactor > 0) {
                    let mPrice = curPrice / convFactor;
                    $priceInp.val(mPrice % 1 === 0 ? mPrice : mPrice.toFixed(2));
                    $row.find('.price-per-piece').val($priceInp.val());
                }
            }
        } else if (sizeMode === 'by_feet') {
            const $priceInp = $row.find('.visible-price');
            let curPrice = parseFloat($priceInp.val()) || 0;
            const curQty = parseFloat($row.find('.carton-qty').val()) || 0;

            let convFactor = 0;
            let variantUnit = '';
            const vdEncoded = $row.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try {
                        vd = JSON.parse(atob(vdEncoded));
                    } catch(e) {
                        vd = JSON.parse(vdEncoded);
                    }
                    if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                        convFactor = parseFloat(vd.conv_factor);
                    } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                        convFactor = parseFloat(vd.weight_per_piece) / 12.0;
                    }
                    if (vd.unit) variantUnit = vd.unit;
                } catch(e) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($row.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 12.0;
            }

            if (currentMode === 'ft') {
                if (convFactor > 0 && convFactor !== 1.0) {
                    const uNorm = (variantUnit || 'pcs').toLowerCase().trim();
                    currentMode = uNorm;
                    const uLabel = variantUnit || 'Pcs';
                    $btn.attr('data-unit-mode', currentMode).text(uLabel).removeClass('btn-outline-primary btn-outline-info btn-outline-warning').addClass('btn-outline-info');
                    $row.find('.carton-qty').attr('placeholder', '0');
                    if (curQty > 0) {
                        $row.find('.carton-qty').val(parseFloat((curQty / convFactor).toFixed(4)).toString());
                    }
                    if (curPrice > 0) {
                        let unitP = curPrice * convFactor;
                        $priceInp.val(unitP % 1 === 0 ? unitP : unitP.toFixed(2));
                        $row.find('.price-per-piece').val($priceInp.val());
                    }
                } else {
                    currentMode = 'in';
                    $btn.attr('data-unit-mode', 'in').text('In').removeClass('btn-outline-primary btn-outline-info').addClass('btn-outline-warning');
                    $row.find('.carton-qty').attr('placeholder', 'In');
                    if (curQty > 0) {
                        $row.find('.carton-qty').val(Math.round(curQty * 12));
                    }
                    if (curPrice > 0) {
                        let inPrice = curPrice / 12;
                        $priceInp.val(inPrice.toFixed(4).replace(/\.?0+$/, ''));
                        $row.find('.price-per-piece').val($priceInp.val());
                    }
                }
            } else if (currentMode === 'in') {
                currentMode = 'ft';
                $btn.attr('data-unit-mode', 'ft').text('Ft').removeClass('btn-outline-warning btn-outline-info').addClass('btn-outline-primary');
                $row.find('.carton-qty').attr('placeholder', 'Ft');
                if (curQty > 0) {
                    $row.find('.carton-qty').val(curQty / 12);
                }
                if (curPrice > 0) {
                    let ftPrice = curPrice * 12;
                    $priceInp.val(ftPrice % 1 === 0 ? ftPrice : ftPrice.toFixed(2));
                    $row.find('.price-per-piece').val($priceInp.val());
                }
            } else {
                // Currently in variant unit (Meter, Kg, Pcs, etc.) -> toggle to Ft
                currentMode = 'ft';
                $btn.attr('data-unit-mode', 'ft').text('Ft').removeClass('btn-outline-info btn-outline-warning').addClass('btn-outline-primary');
                $row.find('.carton-qty').attr('placeholder', 'Ft');
                if (curQty > 0 && convFactor > 0) {
                    $row.find('.carton-qty').val(parseFloat((curQty * convFactor).toFixed(4)).toString());
                }
                if (curPrice > 0 && convFactor > 0) {
                    let ftPrice = curPrice / convFactor;
                    $priceInp.val(ftPrice % 1 === 0 ? ftPrice : ftPrice.toFixed(2));
                    $row.find('.price-per-piece').val($priceInp.val());
                }
            }
        }

        $btn.attr('data-unit-mode', currentMode);
        $row.find('.hidden-sub-unit-mode').val(currentMode);
        computeRow($row);
        if (typeof updateGrandTotals === 'function') updateGrandTotals();
        if (typeof refreshPostedState === 'function') refreshPostedState();
    });

        // Warehouse change handled globally via loadWarehousesForProduct

        // Inputs -> Calc
        // --- Helper for Box Conversion ---
        window.normalizeQtyInput = function($input, $row) {
            const val = $input.val();
            const sizeMode = $row.data('size_mode');
            const ppb = parseFloat($row.find('.pack-qty').val()) || 1;

            if ((sizeMode === 'by_cartons' || sizeMode === 'by_box' || sizeMode === 'by_size') && ppb > 1 && val.includes('.')) {
                const parts = val.split('.');
                const boxes = parseInt(parts[0]) || 0;
                const looseStr = parts[1];

                if (looseStr && looseStr !== '') {
                    const loose = parseInt(looseStr);
                    if (loose >= ppb) {
                        const extraBoxes = Math.floor(loose / ppb);
                        const newLoose = loose % ppb;
                        const newBoxes = boxes + extraBoxes;

                        let newVal = newBoxes.toString();
                        if (newLoose > 0) {
                            newVal += '.' + newLoose;
                        } // re-run
                        $input.val(newVal);
                    }
                }
            }
        };

        // $(document).ready(function() {

        // ... existing bindings ...

        // Inputs -> Calc
        $(document).on('input change keyup blur paste', '.carton-qty, .loose-pcs-input, .pack-qty, .discount-value, .visible-price, .sales-qty',
            function() {
                // If user manually changes the visible price, also update the hidden price-per-piece and normalized base rate
                if ($(this).hasClass('visible-price')) {
                    const $row = $(this).closest('tr');
                    const newPrice = toNum($(this).val());
                    $row.find('.price-per-piece').val(newPrice);

                    const sizeMode = $row.data('size_mode') || $row.find('.size-mode-text').val();
                    const packQty = parseFloat($row.find('.pack-qty').val()) || 1;
                    const unitMode = $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'main';
                    const rowMode = $row.find('.price-mode-row-toggle').attr('data-mode') || 'retail';

                    let baseRate = newPrice;
                    if (sizeMode === 'by_cartons' || sizeMode === 'by_box') {
                        baseRate = (unitMode === 'pcs' || packQty <= 1) ? newPrice : (newPrice / packQty);
                    } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
                        baseRate = (unitMode === 'gm') ? (newPrice * 1000) : newPrice;
                    } else if (sizeMode === 'by_meter') {
                        baseRate = (unitMode === 'mm') ? (newPrice * 1000) : newPrice;
                    } else if (sizeMode === 'by_feet') {
                        baseRate = (unitMode === 'in') ? (newPrice * 12) : newPrice;
                    }

                    if (rowMode === 'wholesale') {
                        $row.find('.wholesale-price').val(baseRate);
                    } else {
                        $row.find('.retail-price').val(baseRate);
                    }
                }

                computeRow($(this).closest('tr'));
                updateGrandTotals();
                refreshPostedState();
            });

        $(document).on('input', '.discount-amount', function() {
            computeRow($(this).closest('tr'), true);
            updateGrandTotals();
            refreshPostedState();
        });

        // Size Display Input Sync
        $(document).on('input change', '.size-display', function() {
            const $row = $(this).closest('tr');
            const newSize = $(this).val().trim();
            let variantData = {};
            const existingVd = $row.find('.variant-data-hidden').val();
            if (existingVd) {
                try {
                    const decoded = atob(existingVd);
                    variantData = JSON.parse(decoded);
                } catch(e) {
                    try {
                        variantData = JSON.parse(existingVd);
                    } catch(e2) {
                        variantData = { color: $row.find('.color-display').val() || '-' };
                    }
                }
            } else {
                variantData = { color: $row.find('.color-display').val() || '-' };
            }
            if (typeof variantData !== 'object' || variantData === null) {
                variantData = { color: $row.find('.color-display').val() || '-' };
            }
            variantData.size = newSize;
            $row.find('.variant-data-hidden').val(btoa(JSON.stringify(variantData)));
        });

        // Delete Row
        $(document).on('click', '.del-row', function() {
            if ($('#salesTableBody tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowIndexes();
                updateGrandTotals();
                refreshPostedState();
            }
        });

        // Add Row Button
        $('#btnAdd').click(addNewRow);

        // Row Pricing Mode Toggle Handler
        $(document).on('click', '.price-mode-row-toggle', function() {
            const $btn = $(this);
            const $row = $btn.closest('tr');
            const currentMode = $btn.attr('data-mode') || 'retail';
            const newMode = currentMode === 'retail' ? 'wholesale' : 'retail';
            
            $btn.attr('data-mode', newMode);
            if (newMode === 'wholesale') {
                $btn.removeClass('btn-outline-success').addClass('btn-outline-info').text('W').attr('title', 'Wholesale Mode');
            } else {
                $btn.removeClass('btn-outline-info').addClass('btn-outline-success').text('R').attr('title', 'Retail Mode');
            }
            
            lastSelectedPriceMode = newMode;
            
            let retailPrice = parseFloat($row.find('.retail-price').val()) || 0;
            let wholesalePrice = parseFloat($row.find('.wholesale-price').val()) || 0;
            
            let pieceRate = (newMode === 'wholesale' && wholesalePrice > 0) ? wholesalePrice : retailPrice;
            const sizeMode = $row.data('size_mode') || $row.find('.size-mode-text').val();
            const packQty = parseFloat($row.find('.pack-qty').val()) || 1;
            const unitMode = $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'ctn';

            if (sizeMode === 'by_cartons' || sizeMode === 'by_box') {
                let cartonPrice = packQty > 1 ? (pieceRate * packQty) : pieceRate;
                let finalPrice = (unitMode === 'pcs') ? pieceRate : cartonPrice;
                $row.find('.visible-price').val(finalPrice % 1 === 0 ? finalPrice : finalPrice.toFixed(2));
                $row.find('.price-per-piece').val($row.find('.visible-price').val());
            } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
                if (unitMode === 'gm') {
                    $row.find('.visible-price').val((pieceRate / 1000).toFixed(4).replace(/\.?0+$/, ''));
                } else {
                    $row.find('.visible-price').val(pieceRate);
                }
                $row.find('.price-per-piece').val($row.find('.visible-price').val());
            } else if (sizeMode === 'by_meter') {
                if (unitMode === 'mm') {
                    $row.find('.visible-price').val((pieceRate / 1000).toFixed(4).replace(/\.?0+$/, ''));
                } else {
                    $row.find('.visible-price').val(pieceRate);
                }
                $row.find('.price-per-piece').val($row.find('.visible-price').val());
            } else if (sizeMode === 'by_feet') {
                if (unitMode === 'in') {
                    $row.find('.visible-price').val((pieceRate / 12).toFixed(4).replace(/\.?0+$/, ''));
                } else {
                    $row.find('.visible-price').val(pieceRate);
                }
                $row.find('.price-per-piece').val($row.find('.visible-price').val());
            } else {
                $row.find('.visible-price').val(pieceRate);
                $row.find('.price-per-piece').val(pieceRate);
            }
            
            computeRow($row);
            updateGrandTotals();
            refreshPostedState();
            updateGrandTotals();
        });

        // Enter on any editable input -> compute row, add new row & open product select
        $('#salesTableBody').on('keydown', '.carton-qty, .item-description, .loose-pcs-input, .discount-value, .discount-amount, .visible-price', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                const $current = $(this).closest('tr');
                computeRow($current);
                updateGrandTotals();

                // Add new row and open product dropdown
                addNewRow();
                setTimeout(() => $('#salesTableBody tr:last-child .product').select2('open'), 50);
            }
        });

        // Discount Toggle: % <-> PKR
        $(document).on('click', '.discount-toggle', function() {
            const $btn = $(this);
            const currentType = $btn.data('type') || $btn.attr('data-type') || $btn.closest('.discount-wrapper').find('.discount-type-hidden').val() || 'percent';
            const newType = currentType === 'percent' ? 'pkr' : 'percent';
            $btn.data('type', newType).attr('data-type', newType).text(newType === 'percent' ? '%' : 'PKR');
            // Sync hidden input so form submission carries correct type
            $btn.closest('.discount-wrapper').find('.discount-type-hidden').val(newType);
            computeRow($btn.closest('tr'));
            updateGrandTotals();
        });

        $(document).on('click', '#overallDiscountToggle', function() {
            const $btn = $(this);
            const currentType = $btn.data('type') || $btn.attr('data-type') || $('#overallDiscountType').val() || 'pkr';
            const newType = currentType === 'pkr' ? 'percent' : 'pkr';
            $btn.data('type', newType).attr('data-type', newType).text(newType === 'percent' ? '%' : 'PKR');
            $('#overallDiscountType').val(newType);
            $('#overallDiscountLabel').text(newType === 'percent' ? 'Discount (%)' : 'Discount (Rs.)');
            updateGrandTotals();
        });

        // Buttons: Draft
        $('#btnDraft, #btnHeaderDraftSale, #btnSaveDraft, #btnSaveDraft2').off('click').on('click', function() {
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }

            if (!canPost()) {
                showAlert('warning', 'Please add at least one valid product item to save draft.');
                return;
            }

            $('#action').val('draft');
            window.showConfirmPopup({
                title: 'Save as Draft?',
                text: 'Are you sure you want to save this document as a Draft?',
                confirmBtnText: '<i class="fas fa-file-alt me-1"></i> Yes, Save Draft'
            }, function() {
                ensureSaved();
            });
        });

        // Buttons: Booking (Save)
        $('#btnSave').off('click').on('click', function() {
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }
            $('#action').val('booking');
            window.showConfirmPopup({
                title: 'Save Booking?',
                text: 'Are you sure you want to save this booking order?',
                confirmBtnText: '<i class="fas fa-check me-1"></i> Yes, Save Booking'
            }, function() {
                ensureSaved();
            });
        });

        // Buttons: Quotation
        $('#btnQuotation').off('click').on('click', function() {
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }
            $('#action').val('quotation');
            window.showConfirmPopup({
                title: 'Save Quotation?',
                text: 'Are you sure you want to save this Quotation?',
                confirmBtnText: '<i class="fas fa-file-invoice me-1"></i> Yes, Save Quotation'
            }, function() {
                ensureSaved();
            });
        });

                $('#btnSaveAndPrint2').off('click').on('click', function() {
            if ($('#sale_type').val() === 'quotation') {
                $('#action').val('quotation');
            } else if ($('#sale_type').val() === 'sales_order') {
                $('#action').val('sales_order');
            } else {
                $('#action').val('sale');
            }
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }

            const isWalkin = $('#is_walkin').val() === '1';
            const invoiceNet = toNum($('#totalBalance').val());
            const paidNow = toNum($('#receiptsTotal').text());
            const isConvertToSale = $('input[name="convert_to_sale"]').val() === '1' || (new URLSearchParams(window.location.search).get('convert_to_sale') === '1');
            const isConvertToSO = $('input[name="convert_to_so"]').val() === '1' || (new URLSearchParams(window.location.search).get('convert_to_so') === '1');
            const currentSaleType = isConvertToSale ? 'direct_sale' : ($('#sale_type').val() || (new URLSearchParams(window.location.search).get('type')) || '');

            if (isWalkin && currentSaleType !== 'quotation' && currentSaleType !== 'sales_order') {
                if (paidNow < (invoiceNet - 0.05)) {
                    Swal.fire({
                        icon: 'error',
                        title: '100% Payment Required',
                        text: 'Walk-in customers do not have a ledger. You must receive full payment to post this sale.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }

            const rangeBal = toNum($('#rangeBalance').val());
            if (rangeBal > 0) {
                const prevBal = toNum($('#previousBalance').val());
                const projectedBalance = prevBal + invoiceNet - paidNow;
                if (projectedBalance > rangeBal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Credit Limit Exceeded',
                        html: `Projected Balance exceeds Credit Limit`,
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }

            if (!canPost()) {
                showAlert('warning', 'No valid item lines to post.');
                return;
            }

            window.showConfirmPopup({
                title: 'Confirm & Print?',
                text: 'Are you sure you want to save this sale and print directly?',
                confirmBtnText: '<i class="fas fa-print me-1"></i> Yes, Save & Print'
            }, function() {
                ensureSaved().then(function(res) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Saved successfully, opening print...',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    const bid = res.booking_id || $('#booking_id').val();
                    window.open('{{ url("sales") }}/' + bid + '/recepit', '_blank');
                    setTimeout(() => window.location.href = (res && res.redirect_url) ? res.redirect_url : "{{ route('sale.index') }}", 1500);
                });
            });
        });

        // Buttons: Sale (Post)
        $('#btnPosted, #btnHeaderPosted, #btnSaveAndComplete, #btnSaveAndComplete2').off('click').on('click', function() {
            if ($('#sale_type').val() === 'quotation') {
                $('#action').val('quotation');
            } else if ($('#sale_type').val() === 'sales_order') {
                $('#action').val('sales_order');
            } else {
                $('#action').val('sale');
            }
            cleanupEmptyRows();
            updateGrandTotals();
            refreshPostedState();

            const v = validateFormAll();
            if (!v.ok) {
                showAlert('warning', v.message);
                if (v.el && v.el.length) {
                    v.el.focus();
                    if (v.el.hasClass('js-customer')) v.el.select2?.('open');
                }
                return;
            }

            // Walk-in 100% Payment Validation Check (Only for actual posted Sales, not Quotations or Sales Orders)
            const isWalkin = $('#is_walkin').val() === '1';
            const invoiceNet = toNum($('#totalBalance').val());
            const paidNow = toNum($('#receiptsTotal').text());
            const isConvertToSale = $('input[name="convert_to_sale"]').val() === '1' || (new URLSearchParams(window.location.search).get('convert_to_sale') === '1');
            const isConvertToSO = $('input[name="convert_to_so"]').val() === '1' || (new URLSearchParams(window.location.search).get('convert_to_so') === '1');
            const currentSaleType = isConvertToSale ? 'direct_sale' : ($('#sale_type').val() || (new URLSearchParams(window.location.search).get('type')) || '');

            if (isWalkin && currentSaleType !== 'quotation' && currentSaleType !== 'sales_order') {
                // For walk-in direct sales (or when converting quotation to sale), they must pay 100% upfront (with a small floating point tolerance)
                if (paidNow < (invoiceNet - 0.05)) {
                    Swal.fire({
                        icon: 'error',
                        title: '100% Payment Required',
                        text: 'Walk-in customers do not have a ledger. You must receive full payment to post this sale.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }

            // Credit Limit Check
            const rangeBal = toNum($('#rangeBalance').val());
            if (rangeBal > 0) {
                const prevBal = toNum($('#previousBalance').val());
                const invoiceNet = toNum($('#totalBalance').val());
                const paidNow = toNum($('#receiptsTotal').text());
                const projectedBalance = prevBal + invoiceNet - paidNow;

                if (projectedBalance > rangeBal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Credit Limit Exceeded',
                        html: `Projected Balance (<strong>${projectedBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>) exceeds Credit Limit (<strong>${rangeBal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>)`,
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }

            if (!canPost()) {
                showAlert('warning', 'No valid item lines to post.');
                return;
            }

            // Determine Title and Confirmation Text
            let confirmTitle = 'Confirm & Post Sale?';
            let confirmText = 'Are you sure you want to complete and post this Sale Invoice?';
            let confirmBtnText = '<i class="fas fa-check-circle me-1"></i> Yes, Post Sale';

            if (isConvertToSO) {
                confirmTitle = 'Convert to Sales Order?';
                confirmText = 'Are you sure you want to convert this Quotation into a Sales Order?';
                confirmBtnText = '<i class="fas fa-check-circle me-1"></i> Yes, Convert to SO';
            } else if (isConvertToSale) {
                confirmTitle = 'Convert & Post Sale?';
                confirmText = 'Are you sure you want to convert and post this into a Sale Invoice?';
                confirmBtnText = '<i class="fas fa-check-circle me-1"></i> Yes, Post Sale';
            } else if (currentSaleType === 'quotation') {
                confirmTitle = 'Save Quotation?';
                confirmText = 'Are you sure you want to save this Quotation?';
                confirmBtnText = '<i class="fas fa-file-invoice me-1"></i> Yes, Save Quotation';
            } else if (currentSaleType === 'sales_order') {
                confirmTitle = 'Save Sales Order?';
                confirmText = 'Are you sure you want to save this Sales Order?';
                confirmBtnText = '<i class="fas fa-shopping-cart me-1"></i> Yes, Save Sales Order';
            }

            window.showConfirmPopup({
                title: confirmTitle,
                text: confirmText,
                confirmBtnText: confirmBtnText
            }, function() {
                ensureSaved().then(function(res) {
                    Swal.fire({
                        title: 'Success!',
                        text: (res && res.msg) ? res.msg : 'Saved successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => window.location.href = (res && res.redirect_url) ? res.redirect_url : "{{ route('sale.index') }}", 1500);
                });
            });
        });

        // Receipts Logic
        $(document).on('input', '.rv-amount', function() {
            if (typeof window.recomputeReceipts === 'function') window.recomputeReceipts();
        });

        $('#btnAddRV').on('click', function() {
            const row = `
              <div class="d-flex gap-2 align-items-center mb-2 rv-row">
                <select class="form-select rv-account" name="receipt_account_id[]" style="max-width:320px">
                  <option value="">Select account</option>
                </select>
                <input type="text" class="form-control text-end rv-amount" name="receipt_amount[]" placeholder="0.00" style="max-width:160px">
                <button type="button" class="btn btn-outline-danger btn-sm btnRemRV">&times;</button>
              </div>`;
            $('#rvWrapper').append(row);
            loadAccountsInto($('#rvWrapper .rv-account:last'));
        });

        $(document).on('click', '.btnRemRV', function() {
            $(this).closest('.rv-row').remove();
            if (typeof window.recomputeReceipts === 'function') window.recomputeReceipts();
        });

        // Sync Change Account Dropdowns
        $(document).on('change', '#changeAccountId', function() {
            const val = $(this).val();
            $('#bottomChangeAccountId, #editChangeAccountId').val(val);
        });
        $(document).on('change', '#bottomChangeAccountId', function() {
            const val = $(this).val();
            $('#changeAccountId, #editChangeAccountId').val(val);
        });
        $(document).on('change', '#editChangeAccountId', function() {
            const val = $(this).val();
            $('#changeAccountId, #bottomChangeAccountId').val(val);
        });


        // --- Customers & Accounts ---
        // We leave accountData here as a helper if available, but parent should ideally provide it.
        const accountData =
            @if (isset($accounts))
                @json($accounts)
            @else
                []
            @endif ;

        function loadAccountsInto($select, customerId) {
            const currentVal = $select.val();
            let options = '<option value="">Select account</option>';
            accountData.forEach(acc => {
                options += `<option value="${acc.id}">${acc.title}</option>`;
            });
            $select.html(options);
            if (currentVal) $select.val(currentVal);
        }

        window.recomputeReceipts = function() {
            let sum = 0;
            $('.rv-amount').each(function() {
                sum += toNum($(this).val());
            });
            $('#receiptsTotal').text(sum.toFixed(2));
            updateGrandTotals();
        }

        // --- Sidebar Direct Add Product ---
        $(document).on('click', '.add-product-direct-btn', function() {
            const prodId = $(this).data('id');
            if (!prodId) return;

            let $targetRow = $('#salesTableBody tr:last');
            const lastProdVal = $targetRow.find('.product').val();

            if (!$targetRow.length || lastProdVal) {
                addNewRow();
                $targetRow = $('#salesTableBody tr:last');
            }

            const $select = $targetRow.find('.product');
            const prodName = $(this).closest('.pos-product-card').find('.pos-product-name').text();
            if ($select.find(`option[value="${prodId}"]`).length === 0) {
                $select.append(new Option(prodName, prodId, true, true));
            } else {
                $select.val(prodId).trigger('change.select2');
            }
            $targetRow.find('.product-id-hidden').val(prodId);
            loadWarehousesForProduct($targetRow, prodId);
            fetchProductPrice($targetRow, prodId);
            if (!window.isPopulatingSet && !window.isEditModeLoading && prodId) {
                checkProductSetAndPrompt($targetRow, prodId, { name: prodName, text: prodName });
            }
        });

        // --- Sidebar Live Search ---
        $('#sidebarProductSearch').on('input', function() {
            const q = $(this).val().trim();
            if (q.length < 1) return;
            $.get('{{ route('search-product-name') }}', { q: q }).done(function(res) {
                let html = '';
                if (res && res.length > 0) {
                    res.forEach(p => {
                        const stockText = p.wh_stock ? `${p.wh_stock} Pcs` : '0 Pcs';
                        const priceText = p.retail_price ? parseFloat(p.retail_price).toFixed(2) : '0.00';
                        html += `
                            <div class="pos-product-card">
                                <div class="pos-product-img">
                                    <i class="fas fa-box text-secondary fs-5"></i>
                                </div>
                                <div class="pos-product-info">
                                    <div class="pos-product-name" title="${p.item_name}">${p.item_name}</div>
                                    <div class="pos-product-sub">
                                        <span class="badge-stock-green">${stockText}</span> SKU: ${p.item_code || '-'}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pos-product-price">${priceText}<br><small class="text-muted fs-7">PCS</small></div>
                                    <button type="button" class="pos-product-add-btn add-product-direct-btn" data-id="${p.id}" title="Add to Grid"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>`;
                    });
                } else {
                    html = '<div class="text-center text-muted py-3" style="font-size:0.75rem;">No products found</div>';
                }
                $('#sidebarProductContainer').html(html);
            });
        });

        // --- Save & Complete (F9) Handler ---
        $('#btnSaveAndComplete, #btnHeaderSaveSale').on('click', function() {
            $('#btnPosted').trigger('click');
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'F9') {
                e.preventDefault();
                $('#btnSaveAndComplete').trigger('click');
            }
        });

        // Failsafe trigger for Quick Add Product Modal
        $(document).on('click', '[data-bs-target="#quickAddProductModal"], [data-target="#quickAddProductModal"]', function(e) {
            e.preventDefault();
            $('#quickAddProductModal').modal('show');
        });

        // Add newly created product with its variants to Sale Table Grid
        window.addProductToSaleGrid = function(product) {
            if (!product || !$('#salesTableBody').length) return;

            let variants = [];
            if (product.color) {
                try {
                    let parsed = typeof product.color === 'string' ? JSON.parse(product.color) : product.color;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        variants = parsed;
                    }
                } catch(e) {}
            }

            if (variants.length === 0) {
                variants = [{
                    name: 'Base',
                    size: '-',
                    color: '-',
                    stock: product.total_stock_qty || 0,
                    sale_price: product.sale_price_per_piece || product.sale_price_per_box || 0,
                    purch_price: product.purchase_price_per_piece || 0,
                    unit: product.unit ? product.unit.name : 'Pcs',
                    conv_factor: 1,
                    is_base_variant: 1
                }];
            }

            variants.forEach(function(variant, idx) {
                let $targetRow = null;
                $('#salesTableBody tr').each(function() {
                    let pVal = $(this).find('.product-id-hidden').val();
                    let pSelectVal = $(this).find('.product').val();
                    if (!pVal && !pSelectVal && !$targetRow) {
                        $targetRow = $(this);
                    }
                });

                if (!$targetRow) {
                    if (typeof addNewRow === 'function') {
                        addNewRow();
                        $targetRow = $('#salesTableBody tr:last');
                    } else {
                        return;
                    }
                }

                let sizeStr = (variant.size && variant.size !== '-') ? ' ' + variant.size : '';
                let variantNameStr = (variant.name && variant.name !== 'Base') ? ' (' + variant.name + ')' : '';
                let displayText = product.item_name + sizeStr + variantNameStr;
                let optionVal = product.id + '|' + (variant.name || 'Base');

                let variantDataEncoded = '';
                try {
                    variantDataEncoded = btoa(unescape(encodeURIComponent(JSON.stringify(variant))));
                } catch(e) {
                    variantDataEncoded = btoa(JSON.stringify(variant));
                }

                let selectData = {
                    id: optionVal,
                    text: displayText,
                    name: displayText,
                    sku: product.item_code || '',
                    stock: variant.stock !== undefined ? variant.stock : (product.total_stock_qty || 0),
                    stock_pieces: variant.stock !== undefined ? variant.stock : (product.total_stock_qty || 0),
                    retail_price: variant.sale_price || product.sale_price_per_piece || product.sale_price_per_box || 0,
                    trade_price: variant.sale_price || product.sale_price_per_piece || product.sale_price_per_box || 0,
                    wholesale_price: variant.wholesale_price || product.wholesale_price || 0,
                    purchase_price: variant.purch_price || product.purchase_price_per_piece || 0,
                    size_mode: product.size_mode || 'by_pieces',
                    pieces_per_box: product.pieces_per_box || 1,
                    variant_data: variantDataEncoded
                };

                let $select = $targetRow.find('.product');
                let newOption = new Option(displayText, optionVal, true, true);
                $(newOption).data('data', selectData);
                $select.empty().append(newOption);

                $select.trigger({
                    type: 'select2:select',
                    params: {
                        data: selectData
                    }
                });

                if (idx === 0) {
                    setTimeout(function() {
                        $targetRow.find('.carton-qty').focus().select();
                    }, 100);
                }
            });

            if (typeof updateGrandTotals === 'function') {
                updateGrandTotals();
            }
        };

        // Initialize Posted Button State
        refreshPostedState();

        // ========================================================
        // ========================================================
        // LINKED / COMPLEMENTARY PRODUCTS PROMPT ON SALE SCREEN
        // ========================================================
        window.suppressLinkedProductPrompt = false;
        window.lastPromptParentRow = null;
        window.currentLinkedProductsList = [];

        function recalcPromptTotals() {
            let totalAmount = 0;
            let selectedCount = 0;

            $('#lpPromptTableBody tr').each(function() {
                let $tr = $(this);
                let isChecked = $tr.find('.lp-item-check').is(':checked');
                let idx = parseInt($tr.attr('data-idx'));
                let item = window.currentLinkedProductsList[idx];

                if (item) {
                    let price = parseFloat(item.retail_price) || 0;
                    let qty = parseFloat($tr.find('.lp-item-qty').val()) || 0;
                    let lineTotal = price * qty;
                    $tr.find('.lp-item-line-total').text('Rs. ' + lineTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    if (isChecked && qty > 0) {
                        totalAmount += lineTotal;
                        selectedCount++;
                    }
                }
            });

            $('#lp_selected_count_label').text(selectedCount + (selectedCount === 1 ? ' item' : ' items'));
            $('#lp_selected_total_label').text('Rs. ' + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#btnAddLinkedProductsToSale').prop('disabled', selectedCount === 0);
        }

        window.checkAndPromptLinkedProducts = function(pid, $row) {
            if (!pid || window.suppressLinkedProductPrompt) return;

            let whId = $row.find('.warehouse').val() || $('#globalWarehouseSelect').val() || 1;
            $.get('/sales/check-linked-products/' + pid, { warehouse_id: whId }, function(res) {
                if (!res || !res.has_links || !res.linked_products || res.linked_products.length === 0) {
                    return;
                }

                // Check existing product IDs in table to avoid re-prompting already added items
                let existingPids = [];
                $('#salesTableBody tr').each(function() {
                    let curPid = $(this).find('.product-id-hidden').val();
                    if (curPid) existingPids.push(curPid.toString());
                });

                let newLinkedItems = res.linked_products.filter(function(item) {
                    return !existingPids.includes(item.product_id.toString());
                });

                if (newLinkedItems.length === 0) {
                    return; // All linked products already added
                }

                window.lastPromptParentRow = $row;
                window.currentLinkedProductsList = newLinkedItems;

                $('#lp_prompt_parent_name').text(res.main_product ? res.main_product.item_name : '-');
                $('#lp_prompt_parent_code').text(res.main_product ? res.main_product.item_code : '-');
                $('#lp_prompt_items_count').text(newLinkedItems.length);

                let tbodyHtml = '';
                newLinkedItems.forEach(function(item, idx) {
                    let price = parseFloat(item.retail_price) || 0;
                    let qty = parseFloat(item.default_qty) || 1;
                    let total = price * qty;
                    let rawStock = parseFloat(item.stock_pieces) || 0;
                    let stockBadge = '';

                    if (rawStock > 0) {
                        stockBadge = `<span class="badge" style="background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-weight: 700; padding: 3px 6px; font-size: 0.72rem;">${item.stock}</span>`;
                    } else {
                        stockBadge = `<span class="badge" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; font-weight: 600; padding: 3px 6px; font-size: 0.72rem;">0 In Stock</span>`;
                    }

                    tbodyHtml += `
                        <tr class="lp-prompt-row" data-idx="${idx}">
                            <td class="text-center" style="vertical-align: middle;">
                                <input type="checkbox" class="lp-item-check" checked style="cursor: pointer; transform: scale(1.15);">
                            </td>
                            <td style="vertical-align: middle;">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">${item.item_name}</div>
                                <small class="font-monospace text-muted" style="font-size: 0.72rem;">
                                    <i class="fas fa-barcode text-primary me-1"></i>${item.item_code || '-'}
                                </small>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                ${stockBadge}
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark" style="vertical-align: middle;">
                                Rs. ${price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </td>
                            <td style="vertical-align: middle;">
                                <div class="input-group input-group-sm mx-auto" style="max-width: 110px;">
                                    <input type="number" step="any" min="0.01" class="form-control form-control-sm text-center fw-bold lp-item-qty" value="${qty}">
                                    <span class="input-group-text bg-light text-muted fw-bold" style="font-size: 0.70rem;">${(item.unit_name || 'Pcs').toUpperCase()}</span>
                                </div>
                            </td>
                            <td class="text-end font-monospace fw-bold text-success lp-item-line-total" style="vertical-align: middle;">
                                Rs. ${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </td>
                        </tr>
                    `;
                });

                $('#lpPromptTableBody').html(tbodyHtml);
                $('#lp_check_all').prop('checked', true);
                recalcPromptTotals();

                $('#linkedProductsModal').modal('show');
            });
        };

        // Recalculate totals on quantity change or checkbox toggle
        $(document).on('input change', '.lp-item-qty', function() {
            recalcPromptTotals();
        });

        $(document).on('change', '.lp-item-check', function() {
            let allChecked = $('.lp-item-check:not(:checked)').length === 0;
            $('#lp_check_all').prop('checked', allChecked);
            recalcPromptTotals();
        });

        $(document).on('change', '#lp_check_all', function() {
            let isChecked = $(this).is(':checked');
            $('.lp-item-check').prop('checked', isChecked);
            recalcPromptTotals();
        });

        // Add single linked product item to sales table
        window.addLinkedProductItemToSale = function(item) {
            if (!item || !$('#salesTableBody').length) return;

            let $targetRow = null;
            $('#salesTableBody tr').each(function() {
                let pVal = $(this).find('.product-id-hidden').val();
                let pSelectVal = $(this).find('.product').val();
                if (!pVal && !pSelectVal && !$targetRow) {
                    $targetRow = $(this);
                }
            });

            if (!$targetRow) {
                if (typeof addNewRow === 'function') {
                    addNewRow();
                    $targetRow = $('#salesTableBody tr:last');
                } else {
                    return;
                }
            }

            let displayText = item.item_name + (item.unit_name ? ' [' + item.unit_name + ']' : '');
            let optionVal = item.product_id + '|' + (item.variant_name || item.variant_key || 'Base');

            let selectData = {
                id: optionVal,
                text: displayText,
                name: displayText,
                sku: item.item_code || '',
                stock: item.stock !== undefined ? item.stock : 0,
                stock_pieces: item.stock_pieces !== undefined ? item.stock_pieces : 0,
                retail_price: item.retail_price || 0,
                trade_price: item.retail_price || 0,
                wholesale_price: item.wholesale_price || 0,
                purchase_price: 0,
                size_mode: item.size_mode || 'by_pieces',
                pieces_per_box: item.pieces_per_box || 1,
                variant_data: item.variant_data || ''
            };

            let $select = $targetRow.find('.product');
            let newOption = new Option(displayText, optionVal, true, true);
            $(newOption).data('data', selectData);
            $select.empty().append(newOption);

            $select.trigger({
                type: 'select2:select',
                params: {
                    data: selectData
                }
            });

            let finalQty = parseFloat(item.quantity) || parseFloat(item.default_qty) || 1;
            $targetRow.find('.carton-qty').val(finalQty).trigger('input');
            if (typeof computeRow === 'function') {
                computeRow($targetRow);
            }
        };

        // Add Selected Linked Products to Invoice
        $('#btnAddLinkedProductsToSale').on('click', function() {
            window.suppressLinkedProductPrompt = true;

            let addedCount = 0;
            $('.lp-prompt-row').each(function() {
                let $tr = $(this);
                let isChecked = $tr.find('.lp-item-check').is(':checked');
                if (isChecked) {
                    let idx = parseInt($tr.attr('data-idx'));
                    let item = window.currentLinkedProductsList[idx];
                    if (item) {
                        item.quantity = parseFloat($tr.find('.lp-item-qty').val()) || item.default_qty || 1;
                        addLinkedProductItemToSale(item);
                        addedCount++;
                    }
                }
            });

            window.suppressLinkedProductPrompt = false;

            if (typeof updateGrandTotals === 'function') {
                updateGrandTotals();
            }

            $('#linkedProductsModal').modal('hide');

            if (addedCount > 0 && typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: addedCount + ' linked product(s) added to invoice!',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            }
        });

        // Refocus on parent row when modal is closed
        $('#linkedProductsModal').on('hidden.bs.modal', function() {
            if (window.lastPromptParentRow && window.lastPromptParentRow.length) {
                setTimeout(function() {
                    window.lastPromptParentRow.find('.carton-qty').focus().select();
                }, 100);
            }
        });

        // Enter key to add, Esc to skip
        $('#linkedProductsModal').on('keydown', function(e) {
            if (e.which === 13) { // Enter
                e.preventDefault();
                $('#btnAddLinkedProductsToSale').click();
            }
        });

        // SALE DRAFT PRESERVATION & AUTO-ADD NEWLY CREATED PRODUCT
        // ========================================================

        // Save current sale draft state before navigating to Create Product page
        window.saveCurrentSaleDraftState = function() {
            let state = {
                customer_id: $('#customerSelect').val(),
                customer_text: $('#customerSelect option:selected').text(),
                date: $('#saleDate').val(),
                warehouse_id: $('#warehouseSelect').val(),
                booking_id: $('#booking_id').val(),
                sale_type: $('#sale_type').val(),
                invoice_prefix: $('#activePrefixLabel').text(),
                rows: []
            };

            $('#salesTableBody tr').each(function() {
                let $row = $(this);
                let pid = $row.find('.product-id-hidden').val();
                let prodVal = $row.find('.product').val();
                let prodText = $row.find('.product option:selected').text();
                let selectData = $row.find('.product option:selected').data('data');

                if (pid || (prodVal && prodVal !== '')) {
                    state.rows.push({
                        product_id: pid || prodVal,
                        option_val: prodVal,
                        display_text: prodText,
                        select_data: selectData,
                        item_code: $row.find('.item-code-display').val(),
                        stock: $row.find('.stock').val(),
                        carton_qty: $row.find('.carton-qty').val(),
                        description: $row.find('.item-description').val(),
                        qty_unit_mode: $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'main',
                        loose_qty: $row.find('.loose-pcs-input').val(),
                        size_display: $row.find('.size-display').val(),
                        color_display: $row.find('.color-display').val(),
                        pack_qty: $row.find('.pack-qty').val(),
                        total_pieces: $row.find('.total-pieces').val(),
                        visible_price: $row.find('.visible-price').val(),
                        price_mode: $row.find('.price-mode-row-toggle').attr('data-mode') || 'retail',
                        disc_percent: $row.find('.discount-percent').val(),
                        disc_amount: $row.find('.discount-amount').val(),
                        variant_data: $row.find('.variant-data-hidden').val()
                    });
                }
            });

            try {
                sessionStorage.setItem('sale_page_preserved_draft', JSON.stringify(state));
            } catch(e) {
                console.error('Could not save draft state', e);
            }
        };

        // Click handler for Create Product button from Select2 search results
        $(document).on('mousedown click', '.btn-create-product-from-search', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.saveCurrentSaleDraftState === 'function') {
                window.saveCurrentSaleDraftState();
            }
            var targetUrl = $(this).attr('href');
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });

        // Restore sale draft if returning from Create Product page
        function restoreSaleDraftState() {
            let savedDraft = sessionStorage.getItem('sale_page_preserved_draft');
            if (!savedDraft) return;
            sessionStorage.removeItem('sale_page_preserved_draft');

            try {
                let draft = JSON.parse(savedDraft);
                if (draft.customer_id && $('#customerSelect').length) {
                    let opt = new Option(draft.customer_text, draft.customer_id, true, true);
                    $('#customerSelect').empty().append(opt).trigger('change');
                }
                if (draft.date && $('#saleDate').length) {
                    $('#saleDate').val(draft.date);
                }
                if (draft.warehouse_id && $('#warehouseSelect').length) {
                    $('#warehouseSelect').val(draft.warehouse_id).trigger('change');
                }

                if (draft.rows && draft.rows.length > 0) {
                    // If the table currently only has 1 empty row, clear it
                    let $firstRow = $('#salesTableBody tr:first');
                    let firstPid = $firstRow.find('.product-id-hidden').val();
                    let firstProd = $firstRow.find('.product').val();
                    if ($('#salesTableBody tr').length === 1 && !firstPid && !firstProd) {
                        $('#salesTableBody').empty();
                    }

                    draft.rows.forEach(function(rData) {
                        if (typeof addNewRow === 'function') {
                            addNewRow();
                            let $newRow = $('#salesTableBody tr:last');
                            let $select = $newRow.find('.product');
                            let newOption = new Option(rData.display_text, rData.option_val, true, true);
                            if (rData.select_data) {
                                $(newOption).data('data', rData.select_data);
                            }
                            $select.empty().append(newOption);
                            $newRow.find('.product-id-hidden').val(rData.product_id);
                            $newRow.find('.variant-data-hidden').val(rData.variant_data || '');
                            $newRow.find('.item-code-display').val(rData.item_code || '');
                            $newRow.find('.stock').val(rData.stock || '');
                            $newRow.find('.carton-qty').val(rData.carton_qty || '');
                            $newRow.find('.item-description').val(rData.description || '');
                            $newRow.find('.loose-pcs-input').val(rData.loose_qty || '');
                            $newRow.find('.size-display').val(rData.size_display || '');
                            $newRow.find('.color-display').val(rData.color_display || '');
                            $newRow.find('.pack-qty').val(rData.pack_qty || '1');
                            $newRow.find('.visible-price').val(rData.visible_price || '0');
                            $newRow.find('.discount-percent').val(rData.disc_percent || '0');
                            $newRow.find('.discount-amount').val(rData.disc_amount || '0');

                            if (typeof computeRow === 'function') {
                                computeRow($newRow, false);
                            }
                        }
                    });

                    if (typeof updateGrandTotals === 'function') {
                        updateGrandTotals();
                    }
                }
            } catch(e) {
                console.error('Failed to restore draft state', e);
            }
        }

        restoreSaleDraftState();

        // Auto-add product created from Create Product page (if Save & Continue was clicked)
        let newProductToAutoAdd = sessionStorage.getItem('sale_auto_add_product');
        if (newProductToAutoAdd) {
            sessionStorage.removeItem('sale_auto_add_product');
            try {
                let prod = JSON.parse(newProductToAutoAdd);
                if (prod && typeof window.addProductToSaleGrid === 'function') {
                    setTimeout(function() {
                        window.addProductToSaleGrid(prod);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Product Added!',
                                text: (prod.item_name || 'Product') + ' has been created and added to your sale invoice.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }, 250);
                }
            } catch(e) {
                console.error('Error auto-adding product to sale', e);
            }
        }

        // Notice if user chose 'Save' only (without auto-add)
        let saleNotice = sessionStorage.getItem('sale_product_created_notice');
        if (saleNotice) {
            sessionStorage.removeItem('sale_product_created_notice');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Created!',
                    text: saleNotice,
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        }
    }); // Close $(document).ready
</script>

<script>
</script>

<script>
    // =========================================================================
    // MULTI-WAREHOUSE STOCK DISPATCH & ALLOCATION LOGIC (SHOP & GODOWN SPLIT)
    // Gathered in a SINGLE row (no clone rows created)
    // =========================================================================
    window.$activeMwhRow = null;

    function showMwhModal() {
        const modalEl = document.getElementById('multiWarehouseModal');
        if (!modalEl) {
            console.error('#multiWarehouseModal element not found in DOM!');
            return;
        }
        if (typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
        } else if (window.bootstrap && window.bootstrap.Modal) {
            const m = window.bootstrap.Modal.getOrCreateInstance(modalEl);
            m.show();
        }
    }

    function hideMwhModal() {
        const modalEl = document.getElementById('multiWarehouseModal');
        if (!modalEl) return;
        if (typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('hide');
        } else if (window.bootstrap && window.bootstrap.Modal) {
            const m = window.bootstrap.Modal.getInstance(modalEl);
            if (m) m.hide();
        }
    }

    $(document).on('click', '.btn-split-wh, .wh-stock-hint, .mwh-applied-badge', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $row = $(this).closest('tr');
        const pid = $row.find('.product-id-hidden').val() || $row.find('.product').val();

        if (!pid) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pehle Product Select Karein',
                    text: 'Pehle is row mein product select karein taake warehouse stock allocate kiya ja sake.'
                });
            } else {
                alert('Pehle is row mein product select karein taake warehouse stock allocate kiya ja sake.');
            }
            return;
        }

        openMultiWarehouseModal($row, pid);
    });

    function openMultiWarehouseModal($row, pid) {
        window.$activeMwhRow = $row;

        const prodSelect = $row.find('.product');
        let prodText = prodSelect.find('option:selected').text().trim() 
            || $row.find('.select2-selection__rendered').text().trim() 
            || $row.data('product_info')?.name 
            || 'Selected Product';
        
        let prodCode = $row.find('.item-code-display').val() || '—';
        let price = parseFloat($row.find('.visible-price').val()) || parseFloat($row.find('.price-per-piece').val()) || 0;
        let currentQty = parseFloat($row.find('.carton-qty').val()) || 0;
        let unitMode = $row.find('.qty-unit-toggle').attr('data-unit-mode') || 'Pcs';
        let currentWhId = $row.find('.row-warehouse-select').val();

        // Check if existing breakdown exists on this row
        let existingBreakdown = null;
        let bVal = $row.find('.warehouse-breakdown-input').val();
        if (bVal) {
            try {
                existingBreakdown = typeof bVal === 'string' ? JSON.parse(bVal) : bVal;
            } catch(e) {}
        }
        if (!existingBreakdown || !Array.isArray(existingBreakdown)) {
            existingBreakdown = $row.data('warehouse_breakdown') || null;
        }

        // If breakdown exists, calculate sum of breakdown quantities
        if (existingBreakdown && Array.isArray(existingBreakdown) && existingBreakdown.length > 0) {
            let bSum = 0;
            existingBreakdown.forEach(function(a) { bSum += parseFloat(a.qty) || 0; });
            if (bSum > 0 && (currentQty <= 0 || Math.abs(currentQty - bSum) < 0.0001)) {
                currentQty = bSum;
            }
        }

        $('#mwhProductName').text(prodText);
        $('#mwhProductCode').text(prodCode);
        $('#mwhProductMeta').text('Price: Rs. ' + price.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' | Mode: ' + unitMode.toUpperCase());
        $('#mwhTotalQty').val(currentQty > 0 ? currentQty : 1);
        $('#mwhUnitLabel').text(unitMode.toUpperCase());

        // Check if warehouses are already cached on the row
        const cachedWhs = $row.data('product_warehouses');
        if (cachedWhs && Array.isArray(cachedWhs) && cachedWhs.length > 0) {
            renderMwhModalRows(cachedWhs, currentWhId, currentQty, unitMode, existingBreakdown);
        } else {
            // Load warehouses dynamically
            $('#mwhWarehousesBody').html('<tr><td colspan="5" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Checking warehouse stocks...</td></tr>');
            showMwhModal();

            $.get('{{ route('warehouses.get') }}', { product_id: pid })
                .done(function(warehouses) {
                    const validWhs = Array.isArray(warehouses) ? warehouses : [];
                    $row.data('product_warehouses', validWhs);
                    renderMwhModalRows(validWhs, currentWhId, currentQty, unitMode, existingBreakdown);
                })
                .fail(function() {
                    $('#mwhWarehousesBody').html('<tr><td colspan="5" class="text-center py-3 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Failed to fetch warehouse stocks.</td></tr>');
                });
        }
    }

    function renderMwhModalRows(warehouses, currentWhId, currentQty, unitMode, existingBreakdown = null) {
        let html = '';
        if (!warehouses || warehouses.length === 0) {
            html = '<tr><td colspan="5" class="text-center py-3 text-muted">No warehouses found in system.</td></tr>';
            $('#mwhWarehousesBody').html(html);
            showMwhModal();
            return;
        }

        // Resolve active row details (convFactor, sizeMode, etc.)
        let convFactor = 0;
        let sizeMode = '';
        let ppb = 1;
        const $activeRow = window.$activeMwhRow;
        if ($activeRow && $activeRow.length > 0) {
            sizeMode = $activeRow.data('size_mode') || $activeRow.find('.size-mode-text').val() || '';
            ppb = parseFloat($activeRow.data('pieces_per_box')) || parseFloat($activeRow.find('.pack-qty').val()) || 1;
            
            const vdEncoded = $activeRow.find('.variant-data-hidden').val();
            if (vdEncoded) {
                try {
                    let vd;
                    try { vd = JSON.parse(atob(vdEncoded)); } catch(e) { vd = JSON.parse(vdEncoded); }
                    if (vd) {
                        if (vd.conv_factor && parseFloat(vd.conv_factor) > 0) {
                            convFactor = parseFloat(vd.conv_factor);
                        } else if (vd.weight_per_piece && parseFloat(vd.weight_per_piece) > 0) {
                            convFactor = parseFloat(vd.weight_per_piece) / 1000.0;
                        }
                    }
                } catch(ex) {}
            }
            if (convFactor <= 0) {
                const wpp = parseFloat($activeRow.find('.weight-per-piece').val()) || 0;
                if (wpp > 0) convFactor = wpp / 1000.0;
            }
        }

        // Sort: Shops (is_shop == 1) first, then Godowns
        const sortedWhs = [...warehouses].sort((a, b) => (b.is_shop || 0) - (a.is_shop || 0));

        sortedWhs.forEach(function(wh, idx) {
            const isShop = wh.is_shop == 1;
            const icon = isShop ? '🏪' : '🏢';
            const typeBadge = isShop 
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-1" style="font-size: 0.65rem;">SHOP / STORE</span>' 
                : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1" style="font-size: 0.65rem;">GODOWN / WH</span>';
            const rawStock = parseFloat(wh.stock) || 0;
            const whSizeMode = wh.size_mode || sizeMode;
            const whPpb = parseFloat(wh.ppb) || ppb || 1;

            let allocStock = rawStock;
            let stockBadgeHtml = '';

            const uModeLower = (unitMode || '').toLowerCase();

            if (whSizeMode === 'by_kg') {
                if ((uModeLower === 'pcs' || uModeLower === 'pc') && convFactor > 0) {
                    allocStock = Math.round(rawStock / convFactor);
                    stockBadgeHtml = `
                        <span class="badge ${rawStock > 0 ? 'bg-success' : 'bg-danger'} text-white fw-bold px-2 py-1" style="font-size: 0.78rem;">
                            ${allocStock.toLocaleString()} PCS
                        </span>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(${rawStock.toFixed(2)} Kg)</small>
                    `;
                } else {
                    allocStock = rawStock;
                    stockBadgeHtml = `
                        <span class="badge ${rawStock > 0 ? 'bg-success' : 'bg-danger'} text-white fw-bold px-2 py-1" style="font-size: 0.78rem;">
                            ${rawStock.toFixed(2)} KG
                        </span>
                    `;
                }
            } else if ((whSizeMode === 'by_cartons' || whSizeMode === 'by_box') && whPpb > 1) {
                if (uModeLower === 'ctn') {
                    const boxes = Math.floor(wh.boxes !== undefined ? wh.boxes : (rawStock / whPpb));
                    const loose = rawStock % whPpb;
                    allocStock = boxes;
                    const ctnDisp = loose > 0 ? `${boxes}.${loose}` : boxes;
                    stockBadgeHtml = `
                        <span class="badge ${rawStock > 0 ? 'bg-success' : 'bg-danger'} text-white fw-bold px-2 py-1" style="font-size: 0.78rem;">
                            ${ctnDisp} CTN
                        </span>
                        <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(${rawStock} Pcs)</small>
                    `;
                } else {
                    allocStock = rawStock;
                    stockBadgeHtml = `
                        <span class="badge ${rawStock > 0 ? 'bg-success' : 'bg-danger'} text-white fw-bold px-2 py-1" style="font-size: 0.78rem;">
                            ${rawStock} PCS
                        </span>
                    `;
                }
            } else {
                allocStock = rawStock;
                stockBadgeHtml = `
                    <span class="badge ${rawStock > 0 ? 'bg-success' : 'bg-danger'} text-white fw-bold px-2 py-1" style="font-size: 0.78rem;">
                        ${rawStock} ${unitMode.toUpperCase()}
                    </span>
                `;
            }

            // Pre-fill initial allocation:
            let initialVal = '';
            if (existingBreakdown && Array.isArray(existingBreakdown) && existingBreakdown.length > 0) {
                let matchAlloc = existingBreakdown.find(function(a) {
                    return (a.wh_id == wh.warehouse_id || a.warehouse_id == wh.warehouse_id);
                });
                if (matchAlloc && parseFloat(matchAlloc.qty) > 0) {
                    initialVal = matchAlloc.qty;
                }
            } else {
                initialVal = (wh.warehouse_id == currentWhId && currentQty > 0) ? currentQty : '';
            }

            html += `
                <tr data-wh-id="${wh.warehouse_id}">
                    <td class="text-center fw-bold text-muted small">${idx + 1}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size: 1.25rem;">${icon}</span>
                            <div>
                                <strong class="text-dark d-block" style="font-size: 0.85rem;">${wh.warehouse_name}</strong>
                                ${typeBadge}
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        ${stockBadgeHtml}
                    </td>
                    <td class="text-center">
                        <div class="input-group input-group-sm justify-content-center" style="max-width: 140px; margin: 0 auto;">
                            <input type="number" step="any" min="0" class="form-control text-center fw-bold mwh-wh-input" 
                                   data-wh-id="${wh.warehouse_id}" 
                                   data-wh-name="${wh.warehouse_name}" 
                                   data-wh-stock="${allocStock}" 
                                   data-raw-stock="${rawStock}" 
                                   value="${initialVal}" 
                                   placeholder="0"
                                   style="border-color: #cbd5e1; font-size: 0.88rem;">
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-primary mwh-btn-max px-2 py-1" data-stock="${allocStock}" title="Fill available stock">
                            Max
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#mwhWarehousesBody').html(html);
        updateMwhCalculation();
        showMwhModal();
    }

    // Live update calculations inside multi-warehouse modal
    function updateMwhCalculation() {
        const needed = parseFloat($('#mwhTotalQty').val()) || 0;
        let allocated = 0;

        $('.mwh-wh-input').each(function() {
            allocated += parseFloat($(this).val()) || 0;
        });

        const remaining = needed - allocated;

        $('#mwhDispNeeded').text(needed.toFixed(2));
        $('#mwhDispAllocated').text(allocated.toFixed(2));
        $('#mwhDispRemaining').text(remaining.toFixed(2));

        const $box = $('#mwhAllocationStatusBox');
        const $badge = $('#mwhStatusBadge');

        if (needed <= 0) {
            $box.css({ 'background': '#f8fafc', 'border-color': '#cbd5e1' });
            $badge.attr('class', 'badge bg-secondary text-white px-3 py-2').html('<i class="fas fa-info-circle me-1"></i> Enter Needed Quantity');
            $('#mwhBtnApply').prop('disabled', true);
        } else if (Math.abs(remaining) < 0.0001) {
            $box.css({ 'background': '#f0fdf4', 'border-color': '#86efac' });
            $badge.attr('class', 'badge bg-success text-white px-3 py-2').html('<i class="fas fa-check-circle me-1"></i> Balanced &amp; Ready!');
            $('#mwhBtnApply').prop('disabled', false);
        } else if (remaining > 0) {
            $box.css({ 'background': '#fffbeb', 'border-color': '#fde68a' });
            $badge.attr('class', 'badge bg-warning text-dark px-3 py-2').html(`<i class="fas fa-exclamation-circle me-1"></i> Short by ${remaining.toFixed(2)}`);
            $('#mwhBtnApply').prop('disabled', false);
        } else {
            $box.css({ 'background': '#fef2f2', 'border-color': '#fca5a5' });
            $badge.attr('class', 'badge bg-danger text-white px-3 py-2').html(`<i class="fas fa-exclamation-triangle me-1"></i> Over by ${Math.abs(remaining).toFixed(2)}`);
            $('#mwhBtnApply').prop('disabled', false);
        }
    }

    $(document).on('input change', '.mwh-wh-input, #mwhTotalQty', function() {
        updateMwhCalculation();
    });

    // Auto Allocate Button (Shop First, then Godowns)
    $(document).on('click', '#mwhBtnAutoDistribute', function() {
        let needed = parseFloat($('#mwhTotalQty').val()) || 0;
        if (needed <= 0) {
            needed = 1;
            $('#mwhTotalQty').val(1);
        }

        let remaining = needed;
        $('.mwh-wh-input').each(function() {
            const stock = parseFloat($(this).data('wh-stock')) || 0;
            if (remaining > 0 && stock > 0) {
                const give = Math.min(stock, remaining);
                $(this).val(give);
                remaining -= give;
            } else {
                $(this).val('');
            }
        });

        if (remaining > 0) {
            const $first = $('.mwh-wh-input').first();
            const cur = parseFloat($first.val()) || 0;
            $first.val(cur + remaining);
        }

        updateMwhCalculation();
    });

    // Max Button on row in modal
    $(document).on('click', '.mwh-btn-max', function() {
        const stock = parseFloat($(this).data('stock')) || 0;
        const $input = $(this).closest('tr').find('.mwh-wh-input');
        $input.val(stock > 0 ? stock : 0).trigger('input');
    });

    // Apply Multi-Warehouse Allocation: GATHER IN SINGLE ROW (NO DUPLICATE ROWS!)
    $(document).on('click', '#mwhBtnApply', function() {
        if (!window.$activeMwhRow || window.$activeMwhRow.length === 0) {
            hideMwhModal();
            return;
        }

        let $row = window.$activeMwhRow;
        let allocations = [];
        let totalAllocated = 0;

        $('.mwh-wh-input').each(function() {
            let qty = parseFloat($(this).val()) || 0;
            let whId = $(this).data('wh-id');
            let whName = $(this).data('wh-name');
            let whStock = parseFloat($(this).data('wh-stock')) || 0;
            if (qty > 0) {
                allocations.push({
                    wh_id: whId,
                    warehouse_id: whId,
                    wh_name: whName,
                    warehouse_name: whName,
                    wh_stock: whStock,
                    qty: qty
                });
                totalAllocated += qty;
            }
        });

        if (allocations.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Quantity Required',
                    text: 'Kam az kam aik warehouse / location mein sale quantity enter karein.'
                });
            } else {
                alert('Kam az kam aik warehouse / location mein sale quantity enter karein.');
            }
            return;
        }

        // Set primary warehouse to the first allocated warehouse
        const firstAlloc = allocations[0];
        $row.find('.row-warehouse-select').val(firstAlloc.wh_id);
        
        // Gather ALL quantities into this SINGLE row!
        $row.find('.carton-qty').val(totalAllocated);

        if (allocations.length > 1) {
            // Multi-location: Save JSON breakdown on row
            const jsonStr = JSON.stringify(allocations);
            $row.find('.warehouse-breakdown-input').val(jsonStr);
            $row.data('warehouse_breakdown', allocations);

            let badgesHtml = allocations.map(function(a) {
                let sName = (a.wh_name || '').replace('WAREHOUSE', 'WH').trim();
                return `<span class="badge bg-primary text-white border me-1 px-1 py-0" style="font-size:0.65rem;">${sName}: <b>${a.qty}</b></span>`;
            }).join('');

            let badgeContainer = `
                <div class="mwh-applied-badge d-flex flex-wrap align-items-center gap-1 mt-1 p-1 bg-light rounded border border-primary-subtle" style="font-size:0.68rem; cursor:pointer;" title="Click to view/edit multi-location allocation">
                    <span class="text-primary fw-bold"><i class="fas fa-boxes me-1"></i>Split:</span>
                    ${badgesHtml}
                </div>
            `;
            $row.find('.mwh-applied-badge-wrap').html(badgeContainer).show();
            $row.find('.wh-stock-hint').hide();
        } else {
            // Single location allocation
            $row.find('.warehouse-breakdown-input').val('');
            $row.removeData('warehouse_breakdown');
            $row.find('.mwh-applied-badge-wrap').html('').hide();
            $row.find('.wh-stock-hint').show();
        }

        // Recompute row totals for the single gathered row
        computeRow($row);
        updateGrandTotals();
        refreshPostedState();
        hideMwhModal();

        if (typeof Swal !== 'undefined') {
            let summaryText = allocations.map(a => `${a.qty} from ${a.wh_name}`).join(' + ');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Gathered ${totalAllocated} in Row (${summaryText})`,
                showConfirmButton: false,
                timer: 3500
            });
        }
    });

    // If user manually changes warehouse dropdown on the row, clear breakdown
    $(document).on('change', '.row-warehouse-select', function() {
        const $row = $(this).closest('tr');
        if ($row.find('.warehouse-breakdown-input').val()) {
            $row.find('.warehouse-breakdown-input').val('');
            $row.removeData('warehouse_breakdown');
            $row.find('.mwh-applied-badge-wrap').html('').hide();
            $row.find('.wh-stock-hint').show();
        }
    });
</script>