@extends('admin_panel.layout.app')

@section('content')
<style>
    .form-control, .form-select, .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        border-radius: 6px !important;
    }
    .table-bordered th, .table-bordered td, .table-bordered {
        border: 1px solid #dee2e6 !important;
    }
    .meta-label {
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #475569 !important;
        margin-bottom: 4px !important;
    }
</style>

<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="page-title mb-1 fw-bold text-dark">
                <i class="fas fa-truck-loading text-primary me-2"></i> Create Direct Goods Receiving Note (GRN)
            </h4>
            <p class="text-muted mb-0 small">Record physical warehouse inward receiving without a prior Purchase Order</p>
        </div>
        <a href="{{ route('direct-grn.index') }}" class="btn btn-outline-secondary btn-sm fw-bold px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to GRNs
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('direct-grn.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Basic Information Card -->
                <div class="bg-light p-3 rounded-3 border mb-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="meta-label">Vendor <span class="text-danger">*</span></label>
                            <select name="vendor_id" class="form-select select2" required>
                                <option value="">Select Vendor...</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="meta-label">Warehouse</label>
                            <input type="hidden" name="warehouse_id" value="1">
                            <input type="text" class="form-control fw-bold bg-light text-dark" value="Main Store" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="meta-label">Receiving Date <span class="text-danger">*</span></label>
                            <input type="date" name="grn_date" class="form-control fw-bold" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="meta-label">GRN Number</label>
                            <input type="text" name="grn_number" id="inputGrnNumber" class="form-control font-monospace fw-bold" value="{{ $nextGrnNumber }}" placeholder="e.g. DGRN-0001" title="Aap custom GRN number bhi enter kar sakte hain">
                        </div>
                        <div class="col-md-6">
                            <label class="meta-label">Carrier / Truck / Bilty Info</label>
                            <input type="text" name="carrier_info" class="form-control" placeholder="e.g. Truck # LES-1234 / Driver Aslam">
                        </div>
                        <div class="col-md-6">
                            <label class="meta-label">Remarks / Receiving Notes</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Any quality check or warehouse note...">
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-boxes text-primary me-1"></i> Receiving Items Details</h6>
                    <button type="button" id="addRow" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="fas fa-plus me-1"></i> Add Row
                    </button>
                </div>
                
                <div class="table-responsive mb-3">
                    <table class="table table-hover table-bordered align-middle mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr class="text-uppercase small fw-bold text-muted">
                                <th style="width: 40%;">Product</th>
                                <th style="width: 12%;" class="text-center">Current Stock</th>
                                <th style="width: 12%;" class="text-center">Receiving Qty</th>
                                <th style="width: 15%;" class="text-end">Purch. Price</th>
                                <th style="width: 15%;" class="text-end">Amount</th>
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
                                    <input type="number" step="any" min="0.01" name="qty[]" class="form-control display-qty-input text-center fw-bold" required value="1">
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" name="price[]" class="form-control price-input text-end fw-bold" required value="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control amount-display text-end bg-light fw-bold text-primary" readonly tabindex="-1">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-row" style="width: 28px; height: 28px; padding: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end align-items-center mb-4 p-3 bg-light rounded-2 border">
                    <h5 class="mb-0 fw-bold text-dark">Total Amount: <span id="gridTotal" class="text-primary ms-2 font-monospace">0.00</span></h5>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('direct-grn.index') }}" class="btn btn-outline-secondary px-4 fw-bold">Cancel</a>
                    <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Save Direct GRN & Add Stock
                    </button>
                </div>
            </form>
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
        let badgeClass = stockVal > 0 ? 'bg-success' : 'bg-secondary';

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
        var qty = parseFloat(tr.find('.display-qty-input').val()) || 0;
        var price = parseFloat(tr.find('.price-input').val()) || 0;
        var amount = qty * price;

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
            '<td><input type="number" step="any" min="0.01" name="qty[]" class="form-control display-qty-input text-center fw-bold" required value="1"></td>' +
            '<td><input type="number" step="any" min="0" name="price[]" class="form-control price-input text-end fw-bold" required value="0"></td>' +
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

    $(document).on('keyup change', '.display-qty-input, .price-input', function() {
        calculateRow($(this).closest('tr'));
    });

    $(document).on('select2:select', '.product-select', function(e) {
        var tr = $(this).closest('tr');
        var data = e.params.data;
        
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
                if (res) {
                    var price = res.purchase_price || res.retail_price || 0;
                    if (variantB64) {
                        try {
                            var vData = JSON.parse(atob(variantB64));
                            if (vData.purch_price) price = vData.purch_price;
                            else if (vData.purchase_price) price = vData.purchase_price;
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
                if (res) {
                    var price = res.purchase_price || res.retail_price || 0;
                    tr.find('.price-input').val(price);
                    calculateRow(tr);
                }
            });
        }
    });
});
</script>
@endsection
