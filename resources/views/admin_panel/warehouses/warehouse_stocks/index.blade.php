@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Premium Modal & UI Styles */
        .premium-card {
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px 30px;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .modal-body {
            padding: 30px;
            background: #f8fafc;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .info-label {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        .big-input {
            font-size: 1.25rem;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
        }

        .details-card {
            background: white;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }

        .calc-box {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }

        .calc-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0369a1;
        }

        /* Select2 fixes for Modal */
        .select2-container {
            width: 100% !important;
            z-index: 9999;
        }

        .select2-dropdown {
            z-index: 9999;
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            display: flex;
            align-items: center;
            border-color: #e2e8f0;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-2">
        <div class="card premium-card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes-stacked me-2 text-success"></i> Warehouse Stock & Opening Inventory</h5>
                    <p class="text-muted mb-0 small">Separate visibility for <strong>Shop Stock</strong> vs <strong>Godowns Stock</strong>, with instant opening inventory entry</p>
                </div>
                @can('warehouse.stock.create')
                    <button onclick="openAddModal()" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold">
                        <i class="fas fa-plus-circle me-1"></i> Add Opening / Inward Stock
                    </button>
                @endcan
            </div>
            <div class="card-body">
                <!-- Location Stock Summary Cards -->
                <div class="row g-3 mb-4">
                    <!-- Shop Stock Card -->
                    <div class="col-12 col-md-4">
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'shop'])) }}" 
                           class="text-decoration-none">
                            <div class="card border {{ ($locationType ?? '') === 'shop' ? 'border-success shadow' : 'border-0' }} rounded-4 h-100"
                                 style="background: {{ ($locationType ?? '') === 'shop' ? 'linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%)' : '#f8fafc' }}; transition: all 0.2s;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small">
                                                <i class="fas fa-store me-1"></i> Main Shop Stock
                                            </span>
                                            @if(($locationType ?? '') === 'shop')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle small">Active</span>
                                            @endif
                                        </div>
                                        <h3 class="fw-bold mb-0 text-success">{{ number_format($shopPieces ?? 0) }} <span class="fs-6 fw-normal text-muted">Pcs</span></h3>
                                        <span class="text-muted small"><i class="fas fa-cubes me-1"></i> {{ number_format($shopCount ?? 0) }} Stock records</span>
                                    </div>
                                    <div class="rounded-circle p-3 bg-white shadow-sm text-success">
                                        <i class="fas fa-cash-register fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Godowns Stock Card -->
                    <div class="col-12 col-md-4">
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'godown'])) }}" 
                           class="text-decoration-none">
                            <div class="card border {{ ($locationType ?? '') === 'godown' ? 'border-primary shadow' : 'border-0' }} rounded-4 h-100"
                                 style="background: {{ ($locationType ?? '') === 'godown' ? 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)' : '#f8fafc' }}; transition: all 0.2s;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 small">
                                                <i class="fas fa-warehouse me-1"></i> Storage Godowns Stock
                                            </span>
                                            @if(($locationType ?? '') === 'godown')
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">Active</span>
                                            @endif
                                        </div>
                                        <h3 class="fw-bold mb-0 text-primary">{{ number_format($godownPieces ?? 0) }} <span class="fs-6 fw-normal text-muted">Pcs</span></h3>
                                        <span class="text-muted small"><i class="fas fa-dolly me-1"></i> {{ number_format($godownCount ?? 0) }} Stock records</span>
                                    </div>
                                    <div class="rounded-circle p-3 bg-white shadow-sm text-primary">
                                        <i class="fas fa-warehouse fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Total Overall Stock Card -->
                    <div class="col-12 col-md-4">
                        <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'all'])) }}" 
                           class="text-decoration-none">
                            <div class="card border {{ ($locationType ?? 'all') === 'all' ? 'border-dark shadow-sm' : 'border-0' }} rounded-4 h-100"
                                 style="background: {{ ($locationType ?? 'all') === 'all' ? 'linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%)' : '#f8fafc' }}; transition: all 0.2s;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 small">
                                                <i class="fas fa-globe me-1"></i> Total Inventory (All)
                                            </span>
                                            @if(($locationType ?? 'all') === 'all')
                                                <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle small">All Locations</span>
                                            @endif
                                        </div>
                                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalPiecesOverall ?? 0) }} <span class="fs-6 fw-normal text-muted">Pcs</span></h3>
                                        <span class="text-muted small"><i class="fas fa-layer-group me-1"></i> {{ number_format($totalCount ?? 0) }} Stock records</span>
                                    </div>
                                    <div class="rounded-circle p-3 bg-white shadow-sm text-dark">
                                        <i class="fas fa-boxes-stacked fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Filter Bar (Location + Type + Warehouse + Search) -->
                <div class="p-3 bg-light rounded-4 border mb-4">
                    <div class="row align-items-center g-3">
                        <!-- Location Filter Pills -->
                        <div class="col-12 col-lg-auto">
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted text-uppercase"><i class="fas fa-map-marker-alt text-danger me-1"></i> Location:</span>
                                <div class="btn-group p-1 bg-white rounded-pill border shadow-sm" role="group">
                                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'all'])) }}" 
                                       class="btn btn-sm rounded-pill px-3 fw-bold {{ ($locationType ?? 'all') === 'all' ? 'btn-dark shadow-sm' : 'btn-light text-muted' }}">
                                        All
                                    </a>
                                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'shop'])) }}" 
                                       class="btn btn-sm rounded-pill px-3 fw-bold {{ ($locationType ?? '') === 'shop' ? 'btn-success shadow-sm' : 'btn-light text-muted' }}">
                                        🏪 Main Shop
                                    </a>
                                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'location_type'), ['location_type' => 'godown'])) }}" 
                                       class="btn btn-sm rounded-pill px-3 fw-bold {{ ($locationType ?? '') === 'godown' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                                        🏢 Godowns
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Item Type Filter Pills -->
                        <div class="col-12 col-lg-auto">
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted text-uppercase"><i class="fas fa-tags text-primary me-1"></i> Item Type:</span>
                                <div class="btn-group p-1 bg-white rounded-pill border shadow-sm" role="group">
                                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'item_type'), ['item_type' => 'all'])) }}" 
                                       class="btn btn-sm rounded-pill px-3 fw-bold {{ ($itemType ?? 'all') === 'all' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                                        All ({{ number_format($totalCount ?? 0) }})
                                    </a>
                                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'item_type'), ['item_type' => 'finish_goods'])) }}" 
                                       class="btn btn-sm rounded-pill px-3 fw-bold {{ ($itemType ?? '') === 'finish_goods' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                                        Finished ({{ number_format($fgCount ?? 0) }})
                                    </a>
                                    <a href="{{ route('warehouse_stocks.index', array_merge(request()->except('page', 'item_type'), ['item_type' => 'raw_material'])) }}" 
                                       class="btn btn-sm rounded-pill px-3 fw-bold {{ ($itemType ?? '') === 'raw_material' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-muted' }}">
                                        Raw Material ({{ number_format($rmCount ?? 0) }})
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Warehouse & Search Form -->
                        <div class="col-12">
                            <form method="GET" action="{{ route('warehouse_stocks.index') }}" class="row g-2 align-items-center">
                                <input type="hidden" name="location_type" value="{{ $locationType ?? 'all' }}">
                                <input type="hidden" name="item_type" value="{{ $itemType ?? 'all' }}">

                                <div class="col-12 col-md-4">
                                    <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()" style="height: 38px; border-radius: 8px;">
                                        <option value="all">-- Filter By Specific Warehouse / Shop --</option>
                                        @if(isset($warehouses))
                                            @foreach($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ (string)($warehouseId ?? '') === (string)$w->id ? 'selected' : '' }}>
                                                    {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-12 col-md-5">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search product name, code, model..." style="height: 38px; border-radius: 8px 0 0 8px;">
                                        <button class="btn btn-primary btn-sm px-3" type="submit" style="height: 38px; border-radius: 0 8px 8px 0;">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 col-md-3 d-flex gap-2 justify-content-md-end">
                                    @if(($locationType ?? 'all') !== 'all' || ($itemType ?? 'all') !== 'all' || (($warehouseId ?? 'all') && $warehouseId !== 'all') || !empty($search))
                                        <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-sm btn-outline-danger d-flex align-items-center" style="height: 38px; border-radius: 8px;" title="Reset All Filters">
                                            <i class="fas fa-times me-1"></i> Reset
                                        </a>
                                    @endif
                                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary d-flex align-items-center" style="height: 38px; border-radius: 8px;">
                                        <i class="fas fa-print me-1"></i> Print
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="stockTable">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="text-uppercase small fw-bold">#</th>
                                <th class="text-uppercase small fw-bold">Warehouse / Location</th>
                                <th class="text-uppercase small fw-bold">Product Details</th>
                                <th class="text-uppercase small fw-bold text-center">Boxes</th>
                                <th class="text-uppercase small fw-bold text-center">Total Pieces</th>
                                <th class="text-uppercase small fw-bold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td>{{ $stocks->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if($stock->warehouse && $stock->warehouse->is_shop)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 font-monospace rounded-pill">
                                                <i class="fas fa-store me-1"></i> {{ $stock->warehouse->warehouse_name }} <span class="badge bg-success text-white ms-1">Shop</span>
                                            </span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 font-monospace rounded-pill">
                                                <i class="fas fa-warehouse me-1"></i> {{ $stock->warehouse->warehouse_name ?? 'N/A' }} <span class="badge bg-secondary text-white ms-1">Godown</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($stock->product && $stock->product->image)
                                                <img src="{{ asset('uploads/products/' . $stock->product->image) }}"
                                                    class="rounded me-2 border"
                                                    style="width: 34px; height: 34px; object-fit: cover;">
                                            @else
                                                <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center text-white"
                                                    style="width: 34px; height: 34px; font-size: 10px;">IMG</div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $stock->product->item_name ?? 'N/A' }}</div>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="small text-muted font-monospace">{{ $stock->product->item_code ?? '' }}</span>
                                                    @if($stock->product)
                                                        @if($stock->product->is_part)
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                                                <i class="fas fa-cog me-1"></i> Raw Material / Part
                                                            </span>
                                                        @else
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                                                <i class="fas fa-box me-1"></i> Finished Good
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-monospace fw-bold px-2.5 py-1" style="font-size: 0.85rem;">{{ number_format($stock->quantity) }} Box</span>
                                    </td>
                                    <td class="text-center fw-bold text-primary font-monospace" style="font-size: 0.95rem;">
                                        {{ number_format($stock->total_pieces) }} <span class="text-muted small fw-normal">{{ $stock->product->unit->name ?? "Pcs" }}</span>
                                    </td>
                                    <td class="text-end">
                                        @can('warehouse.stock.edit')
                                            <button onclick="editStock({{ $stock->id }})"
                                                class="btn btn-sm btn-icon btn-outline-primary rounded-circle" title="Edit Opening / Warehouse Stock">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan

                                        <form action="{{ route('warehouse_stocks.destroy', $stock->id) }}" method="POST"
                                            class="d-inline-block" onsubmit="return confirm('Are you sure you want to remove this stock record?');">
                                            @csrf @method('DELETE')
                                            @can('warehouse.stock.delete')
                                                <button type="submit"
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle ms-1" title="Delete Stock Record"><i
                                                        class="fas fa-trash"></i></button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-boxes-stacked fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h6>No stock records found matching your criteria.</h6>
                                        <p class="small text-muted mb-3">Add opening stock for your shop or warehouses using the button above.</p>
                                        @can('warehouse.stock.create')
                                            <button onclick="openAddModal()" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                                <i class="fas fa-plus me-1"></i> Add Opening Stock
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $stocks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Modal (Add & Edit) -->
    <div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"><i class="fas fa-dolly me-2"></i> Add Opening / Warehouse Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem; background: transparent; border: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary py-2 px-3 small rounded-3 mb-3 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <div>
                            <strong>Opening / Inward Stock Entry:</strong> Use this form to record opening inventory or adjust stock for your <strong>Main Shop</strong> or <strong>Storage Godowns</strong>.
                        </div>
                    </div>
                    <form id="stockForm">
                        @csrf
                        <input type="hidden" id="stock_id" name="stock_id">
                        <input type="hidden" id="method_field" name="_method" value="POST">

                        <div class="row g-3">
                            <!-- Warehouse (Col 1) -->
                            <div class="col-md-6">
                                <label class="info-label">Select Location / Warehouse <span class="text-danger">*</span></label>
                                <select id="warehouse_id" name="warehouse_id" class="form-control" required>
                                    <option value="">Select Warehouse or Shop</option>
                                    <!-- Populated via Ajax -->
                                </select>
                            </div>

                            <!-- Product (Col 2) -->
                            <div class="col-md-6">
                                <label class="info-label">Product <span class="text-danger">*</span></label>
                                <select id="product_id" name="product_id" class="form-control" disabled>
                                    <option value="">Select Warehouse First</option>
                                </select>
                            </div>
                        </div>

                        <!-- Product Info Card -->
                        <div id="product-details" class="details-card mt-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img id="prod-img" src=""
                                        style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; display: none;">
                                    <div id="prod-icon" class="text-secondary"><i class="fas fa-box fa-2x"></i></div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark" id="prod-name">--</div>
                                    <div class="d-flex small text-muted gap-3 mt-1">
                                        <span>Code: <b id="prod-code">--</b></span>
                                        <span>PPB: <b id="prod-ppb" class="text-primary">--</b></span>
                                        <span>Current: <b id="prod-current" class="text-success">--</b></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Input -->
                        <div class="row mt-3 align-items-end">
                            <div class="col-md-6">
                                <label class="info-label" id="qty-label">Total Pieces (Stock)</label>
                                <input type="number" step="any" name="quantity_input" id="quantity_input"
                                    class="form-control big-input" placeholder="0" min="0" disabled>
                                <small class="text-muted" id="qty-help">Enter total opening stock in pieces.</small>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="calc-box">
                                    <div class="text-uppercase small text-muted fw-bold mb-1">Calculated Packaging</div>
                                    <div class="calc-number" id="calc-result">0 Pcs</div>
                                    <input type="hidden" name="total_pieces" id="total_pieces" value="0">
                                    <input type="hidden" name="total_box" id="total_box" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="info-label">Remarks / Note</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="2" placeholder="e.g. Initial opening stock, manual count verification..."></textarea>
                        </div>

                        <div class="mt-4 d-grid">
                            <button type="submit" class="btn btn-success py-3 fw-bold shadow-sm" id="btn-save">
                                <span class="btn-text">Save Opening Stock</span>
                            </button>
                        </div>
                    </form>
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
        // Global State
        let piecesPerBox = 0;
        let selectedProduct = null;
        let isEditMode = false;

        $(document).ready(function() {
            initSelect2();

            // Modal Events
            const modalEl = document.getElementById('stockModal');
            modalEl.addEventListener('hidden.bs.modal', function() {
                resetForm();
            });

            // Product Selection Logic
            $('#product_id').on('select2:select', function(e) {
                handleProductSelect(e.params.data);
            });

            // Warehouse Change logic
            $('#warehouse_id').on('change', function() {
                // If product is already selected, fetch stock overlap
                if (selectedProduct && $(this).val()) {
                    fetchCurrentStock($(this).val(), selectedProduct.id);
                }
            });

            // Allow product selection immediately
            $('#product_id').prop('disabled', false);

            // Calculation
            $('#quantity_input').on('input', function() {
                calculateTotal();
            });
        });

        // --- Core Functions ---

        function initSelect2() {
            $('#warehouse_id').select2({
                dropdownParent: $('#stockModal'),
                placeholder: "Select Warehouse",
                allowClear: true,
                ajax: {
                    url: "{{ route('warehouse_stock.search-warehouses') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $('#product_id').select2({
                dropdownParent: $('#stockModal'),
                placeholder: "Select Product",
                allowClear: true,
                ajax: {
                    url: "{{ route('warehouse_stock.search-products') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
        }

        function openAddModal() {
            isEditMode = false;
            $('#modalTitle').html('<i class="fas fa-dolly me-2"></i> Add Opening / Warehouse Stock');
            $('#method_field').val('POST');
            $('#btn-save .btn-text').text('Save Opening Stock');

            // Reset state handled by hidden event, but ensure UI is ready
            var myModal = new bootstrap.Modal(document.getElementById('stockModal'));
            myModal.show();
        }

        function editStock(id) {
            isEditMode = true;
            $('#modalTitle').html('<i class="fas fa-edit me-2"></i> Update Stock Record');
            $('#method_field').val('PUT'); // Simulate PUT for update
            $('#btn-save .btn-text').text('Update Stock Record');
            $('#stock_id').val(id);

            let url = "{{ route('warehouse_stock.edit-data', ':id') }}";
            url = url.replace(':id', id);

            // Show Loaders or Disable UI here if needed
            $.get(url, function(data) {
                // Populate Form
                // Manually Create Option for Warehouse Select2 since it is now AJAX
                let whOption = new Option(data.warehouse_name, data.warehouse_id, true, true);
                $('#warehouse_id').append(whOption).trigger('change');

                // Manually Create Option for Product Select2
                let option = new Option(data.product_name, data.product_id, true, true);
                $('#product_id').append(option).trigger('change');

                // Trigger selection logic manually to setup variables
                // We construct a mock data object similar to what Select2 provides
                let mockData = {
                    id: data.product_id,
                    item_name: data.product_name,
                    item_code: data.product_code,
                    pieces_per_box: data.pieces_per_box,
                    image: data.image
                };
                handleProductSelect(mockData);

                // Populate Quantity - User enters total pieces
                $('#quantity_input').val(data.total_pieces);
                $('#total_pieces').val(data.total_pieces);

                // Calculate and display total boxes
                if (data.pieces_per_box > 0 && data.total_pieces > 0) {
                    let boxes = Math.floor(data.total_pieces / data.pieces_per_box);
                    let loose = data.total_pieces % data.pieces_per_box;
                    $('#calc-result').html(`<span class="text-primary">${boxes}</span>` + (loose > 0 ?
                        ` <span class="text-muted text-small">+ ${loose} Loose</span>` : ''));
                    $('#total_box').val(boxes);
                } else {
                    $('#calc-result').text(data.total_pieces + ' Pcs');
                    $('#total_box').val(0);
                }

                $('#remarks').val(data.remarks);

                // Show Modal
                var myModal = new bootstrap.Modal(document.getElementById('stockModal'));
                myModal.show();
            }).fail(function() {
                Swal.fire('Error', 'Could not fetch stock data.', 'error');
            });
        }

        function handleProductSelect(data) {
            selectedProduct = data;
            piecesPerBox = parseInt(data.pieces_per_box) || 0;

            // UI Updates
            $('#product-details').fadeIn();
            $('#prod-name').text(data.item_name);
            $('#prod-code').text(data.item_code || '--');
            $('#prod-ppb').text(piecesPerBox > 0 ? piecesPerBox : 'N/A (Loose)');

            if (data.image) {
                $('#prod-img').attr('src', data.image).show();
                $('#prod-icon').hide();
            } else {
                $('#prod-img').hide();
                $('#prod-icon').show();
            }

            // Input Configuration - Always enter Total Pieces as per user request
            $('#quantity_input').prop('disabled', false).focus();
            $('#qty-label').text('Total Pieces');
            $('#qty-help').text('Enter total quantity in pieces.');

            // Fetch Current Stock
            /* 
               Only fetch if Warehouse IS selected. 
               If not selected, we just show product details. 
               The Warehouse 'change' event will trigger the fetch later.
            */
            let whId = $('#warehouse_id').val();
            if (whId) {
                fetchCurrentStock(whId, data.id);
            } else {
                $('#prod-current').text('Select Warehouse');
            }

            calculateTotal();
        }

        function fetchCurrentStock(warehouseId, productId) {
            if (!isEditMode) {
                $.get("{{ route('warehouse_stock.get-stock') }}", {
                    warehouse_id: warehouseId,
                    product_id: productId
                }, function(res) {
                    let currentTotal = parseInt(res.total_pieces) || 0;
                    let displayStock = currentTotal + " Pcs";

                    if (piecesPerBox > 0) {
                        let b = Math.floor(currentTotal / piecesPerBox);
                        let l = currentTotal % piecesPerBox;
                        displayStock = `${b}` + (l > 0 ? ` + ${l} Loose` : '');
                    }
                    $('#prod-current').text(displayStock);
                });
            } else {
                // Edit mode fallback
                let currentTotal = parseInt($('#total_pieces').val()) || 0;
                let displayStock = currentTotal + " Pcs (Current Rec)";

                if (piecesPerBox > 0) {
                    let b = Math.floor(currentTotal / piecesPerBox);
                    let l = currentTotal % piecesPerBox;
                    displayStock = `${b}` + (l > 0 ? ` + ${l} Loose` : '') + " (Current Rec)";
                }
                $('#prod-current').text(displayStock);
            }
        }

        function calculateTotal() {
            let inputQty = parseFloat($('#quantity_input').val()) || 0;
            let total = inputQty;

            // Store total pieces (which is the input now)
            $('#total_pieces').val(total);

            // Display Box Estimation
            if (piecesPerBox > 0 && total > 0) {
                let boxes = Math.floor(total / piecesPerBox);
                let loose = Math.round((total % piecesPerBox) * 100) / 100;
                $('#calc-result').html(`<span class="text-primary">${boxes} Box</span>` + (loose > 0 ?
                    ` <span class="text-muted text-small">+ ${loose} Loose</span>` : ''));

                // Set hidden box input
                $('#total_box').val(boxes);
            } else {
                $('#calc-result').text(total + ' Pcs');
                $('#total_box').val(0);
            }
        }

        function resetForm() {
            // Clear state FIRST to avoid race conditions with events
            selectedProduct = null;
            piecesPerBox = 0;

            $('#stockForm')[0].reset();
            $('#warehouse_id').val(null).trigger('change');
            $('#product_id').val(null).trigger('change');
            $('#product-details').hide();
            $('#quantity_input').prop('disabled', true);
            $('#calc-result').text('0');
            $('#stock_id').val('');
        }

        // Submit Logic
        $('#stockForm').on('submit', function(e) {
            e.preventDefault();

            let url = "{{ route('warehouse_stocks.store') }}";
            if (isEditMode) {
                let id = $('#stock_id').val();
                url = "{{ route('warehouse_stocks.update', ':id') }}";
                url = url.replace(':id', id);
            }

            let formData = $(this).serialize();
            let btn = $('#btn-save');
            btn.prop('disabled', true);
            $.ajax({
                url: url,
                method: "POST", // Method spoofing is handled by _method field
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Simple reload to refresh table
                    });
                },
                error: function(xhr) {
                    let msg = 'Error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire('Error', msg, 'error');
                    btn.prop('disabled', false);
                }
            });
        });
    </script>
@endsection
