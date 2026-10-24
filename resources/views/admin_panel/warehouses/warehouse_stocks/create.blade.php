@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('warehouse_stocks.index') }}" class="text-decoration-none text-muted">Stock Inventory</a></li>
                    <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Opening Stock Entry</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes-stacked me-2 text-primary"></i> Add Opening / Inward Stock</h4>
            <p class="text-muted small mb-0">Record initial opening inventory or increment stock for your Main Shop or Storage Godowns</p>
        </div>
        <div>
            <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Back to Inventory
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

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Please correct the following errors:</div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i> Stock Entry Form</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">Decimal & Multi-Unit Ready</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('warehouse_stocks.store') }}" method="POST" id="openingStockForm">
                        @csrf

                        <!-- Location / Warehouse -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">
                                <i class="fas fa-warehouse text-primary me-1"></i> Select Location / Warehouse <span class="text-danger">*</span>
                            </label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select form-select-lg rounded-3" required>
                                <option value="">-- Choose Shop or Godown --</option>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}" {{ (old('warehouse_id', session('last_warehouse_id')) == $w->id || ($w->is_shop && !old('warehouse_id') && !session('last_warehouse_id'))) ? 'selected' : '' }}>
                                        {{ $w->is_shop ? '🏪 [Main Shop] ' : '🏢 [Storage Godown] ' }}{{ $w->warehouse_name }}
                                        @if($w->location) ({{ $w->location }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Select whether this opening stock is at your <strong>Main Shop</strong> counter or a <strong>Godown</strong>.</div>
                        </div>

                        <!-- Product Selector -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">
                                <i class="fas fa-box text-primary me-1"></i> Select Product <span class="text-danger">*</span>
                            </label>
                            <select name="product_id" id="product_id" class="form-control form-control-lg" required>
                                <option value="">Search product by name or code...</option>
                            </select>
                            <div class="form-text small">Type product name or item code to search.</div>
                        </div>

                        <!-- Live Product Details Banner (Appears on Product Select) -->
                        <div id="productDetailsCard" class="card bg-light border rounded-3 p-3 mb-4 d-none">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 bg-white p-2 border me-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                    <img id="prodImage" src="" alt="Product" class="rounded" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <i id="prodDefaultIcon" class="fas fa-cube fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-dark" id="prodName">--</h6>
                                    <div class="d-flex flex-wrap gap-3 small text-muted">
                                        <span>Code: <strong class="text-dark font-monospace" id="prodCode">--</strong></span>
                                        <span>Unit: <strong class="badge bg-secondary-subtle text-dark" id="prodUnit">Pcs</strong></span>
                                        <span>Current Stock in Selected Location: <strong class="badge bg-info text-white" id="currentStockBadge">0 Pcs</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Input (Supports Decimals e.g. 0.2, 1.5, 100) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">
                                <i class="fas fa-calculator text-primary me-1"></i> Quantity to Enter <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="any" min="0" name="quantity" id="quantity" 
                                       class="form-control form-control-lg fw-bold text-primary font-monospace" 
                                       placeholder="e.g. 0.2, 5, 25.5" 
                                       value="{{ old('quantity') }}" required>
                                <span class="input-group-text fw-bold bg-white text-muted" id="qtyUnitLabel">Units</span>
                            </div>
                            <div class="form-text small text-muted">Supports decimal values (e.g. <code>0.2</code>, <code>1.5</code>, <code>25.75</code>) for KG, MTR, FT, or whole numbers for PCS.</div>
                        </div>

                        <!-- Entry Mode: Increment vs Set Exact -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">
                                <i class="fas fa-cog text-primary me-1"></i> Stock Update Mode:
                            </label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="entry_mode" id="mode_increment" value="increment" checked>
                                    <label class="form-check-label fw-bold text-dark" for="mode_increment">
                                        <i class="fas fa-plus text-success me-1"></i> Increment / Add to Existing Stock <span class="badge bg-success-subtle text-success small ms-1">Recommended</span>
                                        <div class="small fw-normal text-muted">Adds this quantity on top of whatever stock is already recorded.</div>
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="entry_mode" id="mode_set" value="set">
                                    <label class="form-check-label fw-bold text-dark" for="mode_set">
                                        <i class="fas fa-equals text-primary me-1"></i> Set Exact Stock Balance
                                        <div class="small fw-normal text-muted">Replaces the stock count in this warehouse with this exact quantity.</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Live Stock Calculation Preview -->
                        <div id="calcPreviewCard" class="alert alert-info py-2 px-3 small rounded-3 mb-4 d-none">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2 fs-5"></i>
                                <div id="calcPreviewText">
                                    Stock Calculation: <strong>0</strong> + <strong>0</strong> = <strong>0</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks / Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">
                                <i class="fas fa-sticky-note text-primary me-1"></i> Remarks / Reason (Optional)
                            </label>
                            <textarea name="remarks" id="remarks" class="form-control rounded-3" rows="2" 
                                      placeholder="e.g. Initial Opening Inventory, manual count verification...">{{ old('remarks') }}</textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex align-items-center gap-3 pt-2">
                            <button type="submit" name="action" value="save_and_add_another" class="btn btn-success btn-lg px-4 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-plus-circle me-1"></i> Save & Add Another
                            </button>
                            <button type="submit" name="action" value="save_and_return" class="btn btn-primary btn-lg px-4 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-check me-1"></i> Save & View Stock List
                            </button>
                            <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill ms-auto">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Instructions & Recent Activity -->
        <div class="col-12 col-xl-5">
            <!-- Guidelines Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fas fa-lightbulb text-warning me-2"></i> Opening Stock Guidelines
                    </h5>
                    <ul class="mb-0 small ps-3" style="line-height: 1.8; opacity: 0.9;">
                        <li><strong>Decimals Supported:</strong> If an item is measured in KG, Mtr, Ft (e.g. <code>0.2</code>, <code>1.5</code>, <code>10.75</code>), you can type decimals directly.</li>
                        <li><strong>Automatic Identification:</strong> If stock already exists in this warehouse, it will identify it automatically and increment/add smoothly.</li>
                        <li><strong>Separation:</strong> Keep your <strong>🏪 Main Shop</strong> stock separate from <strong>🏢 Storage Godowns</strong> so transfers and sales stay 100% accurate.</li>
                        <li><strong>Audit Trail:</strong> Every addition is automatically recorded with an audit movement log.</li>
                    </ul>
                </div>
            </div>

            <!-- Recent Opening Stock Entries Table -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i> Recent Opening Stock Entries</span>
                    <span class="badge bg-light text-muted border">Last 10 Records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th>Item</th>
                                    <th>Location</th>
                                    <th class="text-end">Added Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMovements as $rm)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $rm->item_name }}</div>
                                            <span class="font-monospace text-muted small">{{ $rm->item_code }}</span>
                                        </td>
                                        <td>
                                            @if($rm->is_shop)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                    <i class="fas fa-store me-1"></i> {{ $rm->warehouse_name }}
                                                </span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                                    <i class="fas fa-warehouse me-1"></i> {{ $rm->warehouse_name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-success">
                                            +{{ rtrim(rtrim(number_format((float)$rm->qty, 2, '.', ''), '0'), '.') }} 
                                            <span class="text-muted fw-normal small">{{ $rm->unit_name ?: 'Pcs' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No recent opening stock records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Select2 -->
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

<script>
    let currentStockVal = 0.0;
    let selectedUnit = 'Pcs';

    $(document).ready(function() {
        // Initialize Select2 for product AJAX search
        $('#product_id').select2({
            placeholder: "Search product name or code...",
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
        });

        // When product selected
        $('#product_id').on('select2:select', function(e) {
            let data = e.params.data;
            $('#productDetailsCard').removeClass('d-none');
            $('#prodName').text(data.item_name);
            $('#prodCode').text(data.item_code);
            selectedUnit = data.unit_name || 'Pcs';
            $('#prodUnit').text(selectedUnit);
            $('#qtyUnitLabel').text(selectedUnit);

            if (data.image) {
                $('#prodImage').attr('src', data.image).show();
                $('#prodDefaultIcon').hide();
            } else {
                $('#prodImage').hide();
                $('#prodDefaultIcon').show();
            }

            fetchCurrentStock();
        });

        // When warehouse changed
        $('#warehouse_id').on('change', function() {
            if ($('#product_id').val()) {
                fetchCurrentStock();
            }
        });

        // When quantity or mode changed
        $('#quantity, input[name="entry_mode"]').on('input change', function() {
            updateCalculationPreview();
        });
    });

    function fetchCurrentStock() {
        let whId = $('#warehouse_id').val();
        let prodId = $('#product_id').val();

        if (!whId || !prodId) return;

        $.get("{{ route('warehouse_stock.get-stock') }}", {
            warehouse_id: whId,
            product_id: prodId
        }, function(res) {
            currentStockVal = parseFloat(res.total_pieces) || 0.0;
            let formatted = res.formatted_stock || '0';
            selectedUnit = res.unit_name || selectedUnit;
            $('#prodUnit').text(selectedUnit);
            $('#qtyUnitLabel').text(selectedUnit);
            $('#currentStockBadge').text(formatted + ' ' + selectedUnit);

            updateCalculationPreview();
        });
    }

    function updateCalculationPreview() {
        let enterQty = parseFloat($('#quantity').val()) || 0.0;
        let mode = $('input[name="entry_mode"]:checked').val();

        if (enterQty > 0 || currentStockVal > 0) {
            $('#calcPreviewCard').removeClass('d-none');
            let newTotal = mode === 'set' ? enterQty : (currentStockVal + enterQty);
            let formattedCurrent = currentStockVal.toFixed(2).replace(/\.?0+$/, '');
            let formattedEnter   = enterQty.toFixed(2).replace(/\.?0+$/, '');
            let formattedNew     = newTotal.toFixed(2).replace(/\.?0+$/, '');

            if (mode === 'increment') {
                $('#calcPreviewText').html(`Stock Increment: Current <strong>${formattedCurrent} ${selectedUnit}</strong> + Entering <strong>${formattedEnter} ${selectedUnit}</strong> = <span class="badge bg-success fs-6">New Total: ${formattedNew} ${selectedUnit}</span>`);
            } else {
                $('#calcPreviewText').html(`Exact Stock Set: Current was <strong>${formattedCurrent} ${selectedUnit}</strong> &rarr; <span class="badge bg-primary fs-6">Setting Exact Total: ${formattedEnter} ${selectedUnit}</span>`);
            }
        } else {
            $('#calcPreviewCard').addClass('d-none');
        }
    }
</script>
@endsection
