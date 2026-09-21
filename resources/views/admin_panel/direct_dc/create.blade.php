@extends('admin_panel.layout.app')
@section('content')
<style>
    .form-control, .form-select, .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da !important;
        box-shadow: none !important;
    }
    .table-bordered th, .table-bordered td, .table-bordered {
        border: 1px solid #dee2e6 !important;
    }
</style>
<div class="content-wrapper">
    <!-- Modern Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="page-title mb-1 fw-bold text-dark">
                <i class="fas fa-truck-loading text-primary me-2"></i> Create Direct Delivery Challan
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Fill in the details to generate a new direct delivery challan</p>
        </div>
        <a href="{{ route('direct-dc.index') }}" class="btn btn-sm btn-light px-3 fw-bold rounded-2 border">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card border border-light-subtle rounded-3 shadow-sm">
                <div class="card-body p-3">
                    <form action="{{ route('direct-dc.store') }}" method="POST">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <h6 class="fw-bold text-dark mb-2 pb-1 border-bottom"><i class="fas fa-info-circle text-primary me-1"></i> Basic Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-4 form-group mb-2">
                                <label class="fw-bold text-muted mb-1" style="font-size: 0.8rem;">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control select2" required>
                                    <option value="">Select...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label class="fw-bold text-muted mb-1" style="font-size: 0.8rem;">DC Date <span class="text-danger">*</span></label>
                                <input type="date" name="dc_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label class="fw-bold text-muted mb-1" style="font-size: 0.8rem;">DC Number</label>
                                <input type="text" name="dc_number" class="form-control bg-light text-muted" value="{{ $nextDcNumber }}" required readonly>
                            </div>
                            <input type="hidden" name="warehouse_id" value="{{ auth()->user()->warehouse_id ?? 1 }}">
                            <div class="col-md-12 form-group mb-2">
                                <label class="fw-bold text-muted mb-1" style="font-size: 0.8rem;">Remarks</label>
                                <input type="text" name="remarks" class="form-control" placeholder="Any additional notes or instructions...">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-boxes text-primary me-1"></i> Items Details</h6>
                            <button type="button" id="addRow" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" style="padding-top: 2px; padding-bottom: 2px;"><i class="fas fa-plus me-1"></i> Add Row</button>
                        </div>
                        
                        <div class="table-responsive mb-2">
                            <table class="table table-hover table-bordered align-middle" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%;" class="fw-bold text-dark">Product</th>
                                        <th style="width: 12%;" class="fw-bold text-dark text-center">Stock</th>
                                        <th style="width: 10%;" class="fw-bold text-dark text-center">Qty</th>
                                        <th style="width: 12%;" class="fw-bold text-dark text-end">Price</th>
                                        <th style="width: 10%;" class="fw-bold text-dark text-center">Disc %</th>
                                        <th style="width: 15%;" class="fw-bold text-dark text-end">Amount</th>
                                        <th style="width: 6%;" class="text-center"><i class="fas fa-trash text-danger"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-select product-select" style="width: 100%;">
                                                <option value="">Select Product...</option>
                                            </select>
                                            <input type="hidden" name="product_id[]" class="product-id-hidden">
                                            <input type="hidden" name="color[]" class="variant-data-hidden">
                                            <input type="hidden" class="size-mode-hidden">
                                            <input type="hidden" class="pack-qty-hidden" value="1">
                                        </td>
                                        <td><input type="text" class="form-control stock-display text-center bg-light" readonly tabindex="-1"></td>
                                        <td>
                                            <input type="text" class="form-control display-qty-input text-center fw-bold" required value="1">
                                            <input type="hidden" name="qty[]" class="real-qty-hidden" value="1">
                                            <input type="hidden" name="loose_qty[]" class="real-loose-hidden" value="0">
                                        </td>
                                        <td><input type="number" step="any" name="price[]" class="form-control price-input text-end fw-bold" required value="0"></td>
                                        <td><input type="number" step="any" class="form-control disc-input text-center" value="0"></td>
                                        <td><input type="text" class="form-control amount-display text-end bg-light fw-bold text-primary" readonly tabindex="-1"></td>
                                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-row" style="width: 28px; height: 28px; padding: 0;"><i class="fas fa-times"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end align-items-center mb-3 p-2 bg-light rounded-2 border">
                            <h5 class="mb-0 fw-bold text-dark">Total Amount: <span id="gridTotal" class="text-primary ms-2">0.00</span></h5>
                        </div>
                        
                        <div class="text-end">
                            <a href="{{ route('direct-dc.index') }}" class="btn btn-light px-4 fw-bold rounded-pill border me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill"><i class="fas fa-save me-1"></i> Save DC</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
$(document).ready(function() {
    function formatProduct(repo) {
        if (repo.loading) return repo.text;
        let stock = repo.stock !== undefined ? repo.stock : 0;
        let sku = repo.sku || 'N/A';
        let stockVal = parseFloat(repo.stock_pieces !== undefined ? repo.stock_pieces : repo.stock) || 0;
        let badgeClass = stockVal > 0 ? 'bg-success' : 'bg-danger';

        return $(`
        <div class="clearfix">
            <div class="float-start">
                <div class="fw-bold">${repo.name || repo.text}</div>
                <small class="text-muted">SKU: ${sku}</small>
            </div>
            <div class="float-end">
                <span class="badge ${badgeClass} rounded-pill">Stock: ${stock}</span>
            </div>
        </div>
        `);
    }

    function formatSelection(repo) {
        return repo.name || repo.text;
    }

    function initProductSelect2($el) {
        $el.select2({
            placeholder: 'Search Product...',
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
            templateResult: formatProduct,
            templateSelection: formatSelection
        });
    }

    $('.select2').not('.product-select').select2();
    initProductSelect2($('.product-select'));

    function calculateRow(tr) {
        var qtyStr = (tr.find('.display-qty-input').val() || '').toString().trim();
        var rawQty = parseFloat(qtyStr) || 0;
        var price = parseFloat(tr.find('.price-input').val()) || 0;
        var disc = parseFloat(tr.find('.disc-input').val()) || 0;
        var sizeMode = tr.find('.size-mode-hidden').val();
        
        var realQty = rawQty;
        var looseQty = 0;
        var pcsDisplay = rawQty;
        var amount = 0;

        if (sizeMode === 'by_cartons') {
            var packQty = parseFloat(tr.find('.pack-qty-hidden').val()) || 1;
            if (qtyStr.includes('.')) {
                var parts = qtyStr.split('.');
                var boxes = parseInt(parts[0]) || 0;
                var loose = parseInt(parts[1]) || 0;
                realQty = boxes;
                looseQty = loose;
                var piecePrice = packQty > 0 ? (price / packQty) : price;
                amount = (boxes * price) + (loose * piecePrice);
            } else {
                realQty = rawQty;
                looseQty = 0;
                amount = rawQty * price;
            }
        } else {
            // For kg, gm, etc.
            realQty = rawQty;
            looseQty = 0;
            amount = rawQty * price;
        }
        
        if (disc > 0) {
            amount = amount - (amount * (disc / 100));
        }

        tr.find('.real-qty-hidden').val(realQty);
        tr.find('.real-loose-hidden').val(looseQty);
        tr.find('.amount-display').val(amount.toFixed(2));
        
        calculateTotal();
    }

    function calculateTotal() {
        var total = 0;
        $('.amount-display').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#gridTotal').text(total.toFixed(2));
    }

    $('#addRow').click(function() {
        var tr = '<tr>' +
            '<td><select class="form-select product-select" style="width: 100%;"></select>' +
            '<input type="hidden" name="product_id[]" class="product-id-hidden">' +
            '<input type="hidden" name="color[]" class="variant-data-hidden">' +
            '<input type="hidden" class="size-mode-hidden">' +
            '<input type="hidden" class="pack-qty-hidden" value="1"></td>' +
            '<td><input type="text" class="form-control stock-display text-center bg-light" readonly tabindex="-1"></td>' +
            '<td><input type="text" class="form-control display-qty-input text-center fw-bold" required value="1">' +
            '<input type="hidden" name="qty[]" class="real-qty-hidden" value="1">' +
            '<input type="hidden" name="loose_qty[]" class="real-loose-hidden" value="0"></td>' +
            '<td><input type="number" step="any" name="price[]" class="form-control price-input text-end fw-bold" required value="0"></td>' +
            '<td><input type="number" step="any" class="form-control disc-input text-center" value="0"></td>' +
            '<td><input type="text" class="form-control amount-display text-end bg-light fw-bold text-primary" readonly tabindex="-1"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-row" style="width: 28px; height: 28px; padding: 0;"><i class="fas fa-times"></i></button></td>' +
            '</tr>';
        var $tr = $(tr);
        $('#itemsTable tbody').append($tr);
        initProductSelect2($tr.find('.product-select'));
        calculateRow($tr);
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    $(document).on('keyup change', '.display-qty-input, .price-input, .disc-input', function() {
        calculateRow($(this).closest('tr'));
    });

    $(document).on('select2:select', '.product-select', function(e) {
        var tr = $(this).closest('tr');
        var data = e.params.data;
        
        // Populate stock display
        tr.find('.stock-display').val(data.stock !== undefined ? data.stock : 0);
        tr.find('.size-mode-hidden').val(data.size_mode || '');
        tr.find('.pack-qty-hidden').val(data.pieces_per_box || 1);
        
        var val = data.id;
        
        if (val && typeof val === 'string' && val.includes('|variant|')) {
            var parts = val.split('|variant|');
            var realId = parts[0];
            var variantB64 = parts[1] || '';
            
            tr.find('.product-id-hidden').val(realId);
            tr.find('.variant-data-hidden').val(variantB64);
            
            $.get('{{ route("get-price") }}', { product_id: realId }, function(res) {
                if (res && res.retail_price !== undefined) {
                    var price = res.retail_price;
                    if (variantB64) {
                        try {
                            var vData = JSON.parse(atob(variantB64));
                            if (vData.sale_price) price = vData.sale_price;
                        } catch(err) {}
                    }
                    tr.find('.price-input').val(price);
                    calculateRow(tr);
                }
            });
        } else if (val) {
            tr.find('.product-id-hidden').val(val);
            tr.find('.variant-data-hidden').val('');
            
            $.get('{{ route("get-price") }}', { product_id: val }, function(res) {
                if (res && res.retail_price !== undefined) {
                    tr.find('.price-input').val(res.retail_price);
                    calculateRow(tr);
                }
            });
        }
    });
});
</script>
@endsection
