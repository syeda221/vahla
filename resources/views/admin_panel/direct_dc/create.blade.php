@extends('admin_panel.layout.app')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Create Direct Delivery Challan</h3>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('direct-dc.store') }}" method="POST">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Customer</label>
                                <select name="customer_id" class="form-control select2" required>
                                    <option value="">Select...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>DC Date</label>
                                <input type="date" name="dc_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>DC Number</label>
                                <input type="text" name="dc_number" class="form-control" value="{{ $nextDcNumber }}" required readonly>
                            </div>
                            <input type="hidden" name="warehouse_id" value="{{ auth()->user()->warehouse_id ?? 1 }}">
                            <div class="col-md-8 form-group">
                                <label>Remarks</label>
                                <input type="text" name="remarks" class="form-control">
                            </div>
                        </div>

                        <h5>Items</h5>
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Product</th>
                                    <th style="width: 12%;">Stock</th>
                                    <th style="width: 10%;">Qty</th>
                                    <th style="width: 12%;">Price</th>
                                    <th style="width: 10%;">Discount %</th>
                                    <th style="width: 15%;">Amount</th>
                                    <th style="width: 6%;"><i class="fas fa-trash"></i></th>
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
                                    <td><input type="text" class="form-control stock-display text-center" readonly tabindex="-1"></td>
                                    <td>
                                        <input type="text" class="form-control display-qty-input" required value="1">
                                        <input type="hidden" name="qty[]" class="real-qty-hidden" value="1">
                                        <input type="hidden" name="loose_qty[]" class="real-loose-hidden" value="0">
                                    </td>
                                    <td><input type="number" step="any" name="price[]" class="form-control price-input" required value="0"></td>
                                    <td><input type="number" step="any" class="form-control disc-input" value="0"></td>
                                    <td><input type="text" class="form-control amount-display text-end" readonly tabindex="-1"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-2 text-end">
                            <strong>Total Amount: <span id="gridTotal">0.00</span></strong>
                        </div>
                        <button type="button" id="addRow" class="btn btn-info btn-sm mt-2">Add Row</button>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save DC</button>
                            <a href="{{ route('direct-dc.index') }}" class="btn btn-secondary">Cancel</a>
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
            '<td><input type="text" class="form-control stock-display text-center" readonly tabindex="-1"></td>' +
            '<td><input type="text" class="form-control display-qty-input" required value="1">' +
            '<input type="hidden" name="qty[]" class="real-qty-hidden" value="1">' +
            '<input type="hidden" name="loose_qty[]" class="real-loose-hidden" value="0"></td>' +
            '<td><input type="number" step="any" name="price[]" class="form-control price-input" required value="0"></td>' +
            '<td><input type="number" step="any" class="form-control disc-input" value="0"></td>' +
            '<td><input type="text" class="form-control amount-display text-end" readonly tabindex="-1"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>' +
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
