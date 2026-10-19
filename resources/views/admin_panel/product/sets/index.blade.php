@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ========================================================
       PRODUCT SETS - EXECUTIVE DARK-BORDER & POPPING UI
       ======================================================== */
    #setModal .modal-dialog {
        max-width: 860px;
        margin: 2rem auto;
    }
    #setModal .modal-content {
        border: 2px solid #0f172a !important;
        border-radius: 10px !important;
        overflow: visible !important;
        position: relative !important;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.28) !important;
        background-color: #ffffff;
    }
    #setModal .modal-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border-bottom: 2px solid #334155 !important;
        border-top-left-radius: 8px !important;
        border-top-right-radius: 8px !important;
    }
    #setModal .modal-body {
        position: relative !important;
        overflow: visible !important;
        background: #f8fafc;
    }

    /* Clean Dark Border Form Controls */
    #setModal .form-control-dark {
        border: 1.5px solid #334155 !important;
        border-radius: 6px !important;
        color: #0f172a !important;
        font-weight: 600 !important;
        background-color: #ffffff !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    #setModal .form-control-dark:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.18) !important;
        outline: 0 !important;
    }

    /* Repeater Table Container */
    #setModal .set-items-box-container {
        border: 1.5px solid #334155 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        overflow: visible !important;
    }
    #setModal #setItemsTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100%;
        overflow: visible !important;
        margin-bottom: 0;
    }
    #setModal #setItemsTable thead th {
        background: #0f172a !important;
        color: #f8fafc !important;
        font-weight: 800 !important;
        font-size: 0.76rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        border: none !important;
        border-bottom: 2px solid #1e293b !important;
        padding: 9px 12px !important;
    }
    #setModal #setItemsTable thead th:first-child {
        border-top-left-radius: 6px !important;
    }
    #setModal #setItemsTable thead th:last-child {
        border-top-right-radius: 6px !important;
    }
    #setModal #setItemsTable tbody tr {
        border-bottom: 1.5px solid #334155 !important;
        background-color: #ffffff;
        transition: background-color 0.15s ease;
    }
    #setModal #setItemsTable tbody tr:hover {
        background-color: #f8fafc;
    }
    #setModal #setItemsTable tbody tr:last-child {
        border-bottom: none !important;
    }
    #setModal #setItemsTable td {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1.5px solid #334155 !important;
        padding: 10px 10px !important;
        vertical-align: top;
        position: relative;
    }
    #setModal #setItemsTable td:last-child {
        border-right: none;
    }

    /* Select2 Container with Clean Dark Borders */
    #setModal .select2-container {
        width: 100% !important;
        display: block !important;
    }
    #setModal .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid #334155 !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    #setModal .select2-container--default.select2-container--open .select2-selection--single,
    #setModal .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0f172a !important;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.18) !important;
    }
    #setModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 10px !important;
        font-size: 0.88rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        letter-spacing: 0.2px;
    }
    #setModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
    /* Select2 Dropdown */
    #setModal .select2-dropdown {
        z-index: 99999 !important;
        border: 2px solid #0f172a !important;
        border-radius: 8px !important;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.25) !important;
        background-color: #ffffff !important;
        overflow: hidden;
    }
    #setModal .select2-search--dropdown {
        padding: 7px !important;
        background: #f1f5f9;
        border-bottom: 1.5px solid #cbd5e1;
    }
    #setModal .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #334155 !important;
        border-radius: 5px !important;
        padding: 7px 10px !important;
        font-size: 0.84rem !important;
        font-weight: 600 !important;
    }
    #setModal .select2-results__option {
        font-size: 0.84rem !important;
        padding: 7px 12px !important;
        font-weight: 600 !important;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    #setModal .select2-results__option--highlighted[aria-selected] {
        background-color: #0f172a !important;
        color: #38bdf8 !important;
    }

    /* Single-Line High-Impact Tags Bar */
    .variant-info-bar {
        display: inline-flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 6px;
        background: #0f172a;
        border: 1.5px solid #1e293b;
        border-radius: 6px;
        padding: 4px 8px;
        margin-top: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }
    .variant-info-bar::-webkit-scrollbar {
        height: 3px;
    }
    .variant-info-bar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 3px;
    }
    .v-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.3px;
        padding: 2px 7px;
        border-radius: 4px;
        line-height: 1.25;
        white-space: nowrap;
        font-family: 'JetBrains Mono', 'Segoe UI', monospace;
    }
    .v-chip i {
        font-size: 0.72rem;
    }
    .v-chip-code {
        background: #1e293b;
        color: #f8fafc;
        border: 1px solid #475569;
    }
    .v-chip-unit {
        background: #1e293b;
        color: #38bdf8;
        border: 1px solid #0284c7;
    }
    .v-chip-weight {
        background: #064e3b;
        color: #4ade80;
        border: 1px solid #16a34a;
    }
    .v-chip-rate {
        background: #451a03;
        color: #fbbf24;
        border: 1px solid #d97706;
    }
    .v-chip-conv {
        background: #3b0764;
        color: #c084fc;
        border: 1px solid #9333ea;
    }

    /* Quantity Input with Dark Borders & Popping Font */
    #setModal .row-qty-input {
        height: 38px !important;
        border: 1.5px solid #334155 !important;
        border-radius: 6px !important;
        font-size: 1.05rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        text-align: center !important;
        background: #ffffff !important;
        font-family: 'JetBrains Mono', 'Segoe UI', monospace !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    #setModal .row-qty-input:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.18) !important;
        outline: 0 !important;
    }
    #setModal .row-unit-badge {
        display: inline-block;
        background: #0f172a;
        color: #38bdf8;
        border: 1px solid #0ea5e9;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Sleek Remove Button */
    #setModal .btn-remove-item-row {
        width: 36px;
        height: 38px;
        border: 1.5px solid #dc2626 !important;
        color: #dc2626 !important;
        background-color: #fef2f2 !important;
        border-radius: 6px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.92rem;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    #setModal .btn-remove-item-row:hover {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.35);
    }

    /* Add Product Button */
    #btnAddSetItemRow {
        background: #0f172a !important;
        color: #ffffff !important;
        border: 1.5px solid #0f172a !important;
        border-radius: 6px !important;
        font-weight: 800 !important;
        font-size: 0.78rem !important;
        padding: 4px 12px !important;
        letter-spacing: 0.3px;
        transition: all 0.15s ease;
    }
    #btnAddSetItemRow:hover {
        background: #1e293b !important;
        border-color: #1e293b !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.2);
    }
</style>

<div class="main-content-inner">
    <div class="container-fluid py-3">
        <!-- Page Header -->
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h4 class="mb-1 text-dark fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-layer-group text-primary"></i> Product Sets & Bundles (پیئرنگ)
                </h4>
                <p class="text-muted small mb-0">Create paired combos (e.g. Bolt + Nut + Nail) that can be sold as a complete set or individually.</p>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" id="btnOpenCreateSetModal">
                    <i class="fas fa-plus-circle me-1"></i> Create New Set / Pairing
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Product Sets Table Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="productSetsTable">
                        <thead class="table-light text-uppercase text-secondary" style="font-size: 0.78rem;">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Set / Bundle Name</th>
                                <th>Set Code</th>
                                <th>Paired Products (Items & Quantities)</th>
                                <th class="text-center">Total Items</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 130px;">Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.85rem;">
                            @forelse($sets as $index => $set)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $set->name }}</div>
                                        @if($set->description)
                                            <small class="text-muted">{{ Str::limit($set->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            {{ $set->code ?: 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($set->items as $item)
                                                @if($item->product)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.76rem;">
                                                        <i class="fas fa-box text-primary"></i>
                                                        <strong>{{ $item->variant_name ?: $item->product->item_name }}</strong> 
                                                        <span class="badge bg-primary text-white ms-1">{{ (float)$item->quantity }} {{ $item->variant_unit ?: optional($item->product->unit)->name }}</span>
                                                        @if($item->weight_per_piece > 0)
                                                            <span class="badge bg-white text-secondary border ms-1" style="font-size: 0.68rem;" title="Piece Weight">
                                                                <i class="fas fa-weight-hanging text-muted me-1"></i>{{ (float)$item->weight_per_piece }}g/pc
                                                            </span>
                                                        @endif
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white rounded-pill px-2">
                                            {{ $set->items_count }} Items
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($set->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit-set" 
                                                    data-id="{{ $set->id }}" title="Edit Set">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('product-sets.destroy', $set->id) }}" method="POST" 
                                                  class="d-inline form-delete-set" 
                                                  onsubmit="return confirm('Are you sure you want to delete this Set?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete Set">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-layer-group fa-3x text-secondary opacity-50 mb-3 d-block"></i>
                                        <h6>No Product Sets or Pairings Created Yet</h6>
                                        <p class="small mb-3">Combine multiple products (like Bolt + Nut + Nail) into a set to sell together.</p>
                                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" onclick="$('#btnOpenCreateSetModal').click()">
                                            <i class="fas fa-plus-circle me-1"></i> Create First Set
                                        </button>
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

<!-- ================= CREATE / EDIT SET MODAL ================= -->
<div class="modal fade" id="setModal" tabindex="-1" aria-labelledby="setModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header py-2 px-3 text-white">
                <h5 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="setModalLabel" style="font-size: 0.98rem; letter-spacing: 0.3px;">
                    <i class="fas fa-layer-group text-primary"></i> <span id="modalTitleText">Create Product Set / Pairing</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.85; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="setForm" action="{{ route('product-sets.store') }}" method="POST">
                @csrf
                <input type="hidden" name="set_id" id="edit_set_id" value="">

                <div class="modal-body p-3">
                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Set / Bundle Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm form-control-dark" name="name" id="set_name" required placeholder="e.g. Bolt & Nut Combo Set (Fitting Kit)">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Set Code / SKU (Optional)</label>
                            <input type="text" class="form-control form-control-sm form-control-dark font-monospace" name="code" id="set_code" placeholder="e.g. SET-001">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.8rem;">Description / Notes</label>
                            <input type="text" class="form-control form-control-sm form-control-dark" name="description" id="set_description" placeholder="Optional details about this combination set">
                        </div>
                    </div>

                    <!-- Items Repeater Box Container with Clean Dark Borders -->
                    <div class="p-2 set-items-box-container">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <span class="fw-bold text-dark small d-flex align-items-center gap-1" style="font-size: 0.82rem; letter-spacing: 0.2px;">
                                <i class="fas fa-cubes text-primary"></i> Products in this Set:
                            </span>
                            <button type="button" class="btn btn-xs" id="btnAddSetItemRow">
                                <i class="fas fa-plus me-1"></i> Add Product
                            </button>
                        </div>

                        <div class="bg-white rounded" style="overflow: visible !important; border: 1.5px solid #334155;">
                            <table class="table table-sm mb-0 align-middle" id="setItemsTable" style="overflow: visible !important;">
                                <thead>
                                    <tr>
                                        <th style="min-width: 340px;">Select Product & Variant / Unit</th>
                                        <th class="text-center" style="width: 140px;">Quantity per Set</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="setItemsTableBody">
                                    <!-- Dynamic Rows inserted here -->
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted mt-2 d-flex align-items-center gap-1" style="font-size: 0.73rem;">
                            <i class="fas fa-info-circle text-primary"></i> Example: 1 Bolt (Pcs) + 2 Nuts (Pcs) + 4 Nails (Kg). When selling 1 set, these exact variants and quantities will be added to the invoice and deducted from stock.
                        </small>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-white" style="border-top: 1.5px solid #cbd5e1;">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 fw-bold" data-dismiss="modal" data-bs-dismiss="modal" style="border-width: 1.5px;">Cancel</button>
                    <button type="submit" class="btn btn-sm text-white fw-bold px-4 shadow-sm" style="background: #0f172a; border: 1.5px solid #0f172a; border-radius: 6px;">
                        <i class="fas fa-save me-1"></i> Save Product Set
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Product List for Dropdowns
    const productsList = @json($allProducts);

    function parseProductVariants(p) {
        if (!p || !p.color) return [];
        try {
            let parsed = typeof p.color === 'string' ? JSON.parse(p.color) : p.color;
            if (typeof parsed === 'string') parsed = JSON.parse(parsed);
            if (Array.isArray(parsed) && parsed.length > 0) return parsed;
        } catch(e) {}
        return [];
    }

    function escapeHtmlAttr(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function createProductRow(item = null) {
        const selectedPid = item ? (item.product_id || item.id) : '';
        const qty = item ? (parseFloat(item.quantity) || 1) : 1;
        const selectedVariantKey = item ? (item.variant_key || '') : '';
        const selectedVariantUnit = item ? (item.variant_unit || '') : '';
        const selectedVariantName = item ? (item.variant_name || '') : '';

        let options = '<option value="">-- Choose Product & Variant (Search "Pcs" or Name) --</option>';
        let preselectedMatched = false;

        productsList.forEach(p => {
            const variants = parseProductVariants(p);
            const itemCode = p.item_code ? p.item_code.toString().trim() : '';
            const codeStr = itemCode ? ` [${itemCode}]` : '';

            if (variants.length > 0) {
                variants.forEach((v, vIdx) => {
                    const vKey = (v.barcode || v.name || vIdx).toString();
                    const vUnit = v.unit || (p.unit ? p.unit.name : 'Pcs');
                    const vName = v.name || p.item_name;
                    const vWeight = (v.weight_per_piece !== undefined && v.weight_per_piece !== null && v.weight_per_piece !== '') ? parseFloat(v.weight_per_piece) : 0;
                    const vPrice = parseFloat(v.sale_price || p.sale_price_per_piece || 0);
                    const vConv = parseFloat(v.conv_factor || 1);

                    let variantDataEncoded = '';
                    try {
                        variantDataEncoded = btoa(unescape(encodeURIComponent(JSON.stringify(v))));
                    } catch(e) {
                        variantDataEncoded = btoa(JSON.stringify(v));
                    }

                    let isSelected = false;
                    if (selectedPid && p.id == selectedPid && !preselectedMatched) {
                        if (selectedVariantKey && (vKey === selectedVariantKey || (v.barcode && v.barcode === selectedVariantKey))) {
                            isSelected = true;
                            preselectedMatched = true;
                        } else if (selectedVariantName && vName.toLowerCase() === selectedVariantName.toLowerCase()) {
                            isSelected = true;
                            preselectedMatched = true;
                        } else if (selectedVariantUnit && vUnit.toLowerCase() === selectedVariantUnit.toLowerCase()) {
                            isSelected = true;
                            preselectedMatched = true;
                        } else if (!selectedVariantKey && vIdx === 0) {
                            isSelected = true;
                            preselectedMatched = true;
                        }
                    }

                    let weightLabel = vWeight > 0 ? ` | ${vWeight}g` : '';
                    let priceLabel = vPrice > 0 ? ` | Rs. ${vPrice}` : '';
                    let displayLabel = `${vName}${codeStr} [${vUnit}${weightLabel}${priceLabel}]`;

                    options += `<option value="${p.id}||${vIdx}" 
                        data-pid="${p.id}" 
                        data-code="${escapeHtmlAttr(itemCode)}" 
                        data-vidx="${vIdx}" 
                        data-vkey="${escapeHtmlAttr(vKey)}" 
                        data-vunit="${escapeHtmlAttr(vUnit)}" 
                        data-vname="${escapeHtmlAttr(vName)}" 
                        data-vweight="${vWeight}" 
                        data-vconv="${vConv}" 
                        data-vprice="${vPrice}" 
                        data-vdata="${variantDataEncoded}" 
                        ${isSelected ? 'selected' : ''}>
                        ${escapeHtmlAttr(displayLabel)}
                    </option>`;
                });
            } else {
                // Product without variants
                const stdUnit = p.unit ? p.unit.name : (p.size_mode === 'by_cartons' ? 'Carton' : 'Pcs');
                const stdPrice = parseFloat(p.sale_price_per_piece || p.sale_price_per_box || 0);
                const stdWeight = parseFloat(p.weight_per_piece || 0);

                let isSelected = false;
                if (selectedPid && p.id == selectedPid && !preselectedMatched) {
                    isSelected = true;
                    preselectedMatched = true;
                }

                let weightLabel = stdWeight > 0 ? ` | ${stdWeight}g` : '';
                let priceLabel = stdPrice > 0 ? ` | Rs. ${stdPrice}` : '';
                let displayLabel = `${p.item_name}${codeStr} [${stdUnit}${weightLabel}${priceLabel}]`;

                options += `<option value="${p.id}||base" 
                    data-pid="${p.id}" 
                    data-code="${escapeHtmlAttr(itemCode)}" 
                    data-vidx="base" 
                    data-vkey="base" 
                    data-vunit="${escapeHtmlAttr(stdUnit)}" 
                    data-vname="${escapeHtmlAttr(p.item_name)}" 
                    data-vweight="${stdWeight}" 
                    data-vconv="1" 
                    data-vprice="${stdPrice}" 
                    data-vdata="" 
                    ${isSelected ? 'selected' : ''}>
                    ${escapeHtmlAttr(displayLabel)}
                </option>`;
            }
        });

        return `
            <tr class="set-item-row" data-initial-pid="${selectedPid}">
                <td class="p-2" style="vertical-align: top;">
                    <select class="form-select form-select-sm set-variant-select" required style="width: 100%;">
                        ${options}
                    </select>

                    <!-- Dynamic Variant & Unit Info Box (Single Line) -->
                    <div class="variant-config-box"></div>

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" class="inp-product-id" name="product_id[]" value="${selectedPid}">
                    <input type="hidden" class="inp-variant-key" name="variant_key[]" value="${escapeHtmlAttr(item ? (item.variant_key || '') : '')}">
                    <input type="hidden" class="inp-variant-unit" name="variant_unit[]" value="${escapeHtmlAttr(item ? (item.variant_unit || '') : '')}">
                    <input type="hidden" class="inp-variant-name" name="variant_name[]" value="${escapeHtmlAttr(item ? (item.variant_name || '') : '')}">
                    <input type="hidden" class="inp-weight-per-piece" name="weight_per_piece[]" value="${item ? (item.weight_per_piece || '') : ''}">
                    <input type="hidden" class="inp-conv-factor" name="conv_factor[]" value="${item ? (item.conv_factor || '') : ''}">
                    <input type="hidden" class="inp-sale-price" name="sale_price[]" value="${item ? (item.sale_price || '') : ''}">
                    <input type="hidden" class="inp-variant-data" name="variant_data[]" value="${escapeHtmlAttr(item ? (item.variant_data || '') : '')}">
                </td>
                <td class="p-2 text-center" style="width: 140px; vertical-align: top;">
                    <input type="number" step="any" min="0.01" class="form-control form-control-sm row-qty-input" name="quantity[]" value="${qty}" required placeholder="Qty">
                    <div class="mt-1">
                        <span class="row-unit-badge">UNIT: -</span>
                    </div>
                </td>
                <td class="text-center p-2" style="width: 50px; vertical-align: top;">
                    <button type="button" class="btn btn-remove-item-row" title="Remove Item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function applySelectedVariantToRow($row) {
        const $select = $row.find('.set-variant-select');
        const $opt = $select.find('option:selected');
        const pid = $opt.attr('data-pid') || '';

        if (!pid) {
            $row.find('.inp-product-id').val('');
            $row.find('.inp-variant-key').val('');
            $row.find('.inp-variant-unit').val('');
            $row.find('.inp-variant-name').val('');
            $row.find('.inp-weight-per-piece').val('');
            $row.find('.inp-conv-factor').val('');
            $row.find('.inp-sale-price').val('');
            $row.find('.inp-variant-data').val('');
            $row.find('.row-unit-badge').text('UNIT: -');
            $row.find('.variant-config-box').empty();
            return;
        }

        const vKey = $opt.attr('data-vkey') || '';
        let vUnit = $opt.attr('data-vunit') || 'Pcs';
        let vName = $opt.attr('data-vname') || '';
        const itemCode = $opt.attr('data-code') || '';
        let vWeight = parseFloat($opt.attr('data-vweight') || 0);
        let vConv = parseFloat($opt.attr('data-vconv') || 1);
        let vPrice = parseFloat($opt.attr('data-vprice') || 0);
        const vData = $opt.attr('data-vdata') || '';

        // If base64 variant_data is available, extract verified fields
        if (vData) {
            try {
                const parsedVd = JSON.parse(atob(vData));
                if (parsedVd && parsedVd.name) vName = parsedVd.name;
                if (parsedVd && parsedVd.unit) vUnit = parsedVd.unit;
                if (parsedVd && parsedVd.conv_factor) vConv = parseFloat(parsedVd.conv_factor);
                if (parsedVd && parsedVd.weight_per_piece) vWeight = parseFloat(parsedVd.weight_per_piece);
                if (parsedVd && parsedVd.sale_price) vPrice = parseFloat(parsedVd.sale_price);
            } catch(e) {}
        }

        $row.find('.inp-product-id').val(pid);
        $row.find('.inp-variant-key').val(vKey);
        $row.find('.inp-variant-unit').val(vUnit);
        $row.find('.inp-variant-name').val(vName);
        $row.find('.inp-weight-per-piece').val(vWeight);
        $row.find('.inp-conv-factor').val(vConv);
        $row.find('.inp-sale-price').val(vPrice);
        $row.find('.inp-variant-data').val(vData);

        $row.find('.row-unit-badge').html(`UNIT: <b>${vUnit.toUpperCase()}</b>`);
        $row.find('.row-qty-input').attr('placeholder', `Qty (${vUnit})`);

        let chipsHtml = `
            <div class="variant-info-bar">
        `;
        if (itemCode) {
            chipsHtml += `
                <span class="v-chip v-chip-code" title="Item Code: ${itemCode}">
                    <i class="fas fa-barcode"></i> <span>CODE:</span> <b>${itemCode}</b>
                </span>
            `;
        }
        chipsHtml += `
                <span class="v-chip v-chip-unit" title="Selected Unit">
                    <i class="fas fa-tag"></i> <span>UNIT:</span> <b>${vUnit.toUpperCase()}</b>
                </span>
        `;
        if (vWeight > 0) {
            chipsHtml += `
                <span class="v-chip v-chip-weight" title="Weight per piece in Grams">
                    <i class="fas fa-balance-scale"></i> <span>WT:</span> <b>${vWeight}g / pc</b>
                </span>
            `;
        }
        if (vPrice > 0) {
            chipsHtml += `
                <span class="v-chip v-chip-rate" title="Sale Rate per unit">
                    <i class="fas fa-coins"></i> <span>RATE:</span> <b>Rs. ${vPrice.toLocaleString()}</b>
                </span>
            `;
        }
        if (vConv > 0 && vConv !== 1) {
            chipsHtml += `
                <span class="v-chip v-chip-conv" title="Conversion Factor">
                    <i class="fas fa-exchange-alt"></i> <span>CONV:</span> <b>${vConv}</b>
                </span>
            `;
        }
        chipsHtml += `</div>`;
        $row.find('.variant-config-box').html(chipsHtml);
    }

    // Function to safely initialize select2 on set variant selects
    function initSetSelect2($container) {
        if (typeof $.fn.select2 === 'undefined') return;

        const $rows = $container ? $container.find('.set-item-row') : $('#setItemsTableBody .set-item-row');

        $rows.each(function() {
            const $row = $(this);
            const $select = $row.find('.set-variant-select');

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                dropdownParent: $('#setModal .modal-body'),
                width: '100%',
                placeholder: '-- Choose Product & Variant (Search "Pcs" or Name) --'
            });

            // If an option is selected, apply its details
            if ($select.val()) {
                applySelectedVariantToRow($row);
            }
        });
    }

    $(document).ready(function() {
        // Change product & variant dropdown -> sync hidden fields and badge chips
        $(document).on('change', '.set-variant-select', function() {
            const $row = $(this).closest('tr');
            applySelectedVariantToRow($row);
        });

        // Open Create Modal
        $('#btnOpenCreateSetModal').on('click', function() {
            $('#setForm')[0].reset();
            $('#edit_set_id').val('');
            $('#modalTitleText').text('Create Product Set / Pairing');
            $('#setItemsTableBody').empty();

            // Add 2 initial rows
            $('#setItemsTableBody').append(createProductRow());
            $('#setItemsTableBody').append(createProductRow());

            $('#setModal').modal('show');
        });

        // Initialize Select2 whenever the modal finishes opening (ensures accurate positioning)
        $('#setModal').on('shown.bs.modal', function() {
            initSetSelect2($('#setItemsTableBody'));
        });

        // Clean up Select2 when modal is closed
        $('#setModal').on('hidden.bs.modal', function() {
            $('#setItemsTableBody .set-variant-select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
        });

        // Add Row Button
        $('#btnAddSetItemRow').on('click', function() {
            const $newRow = $(createProductRow());
            $('#setItemsTableBody').append($newRow);
            initSetSelect2($newRow);
        });

        // Remove Row
        $(document).on('click', '.btn-remove-item-row', function() {
            if ($('#setItemsTableBody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('A set must have at least one product.');
            }
        });

        // Edit Set
        $('.btn-edit-set').on('click', function() {
            const setId = $(this).data('id');
            const url = "{{ url('product-sets') }}/" + setId;

            $.get(url, function(data) {
                if (!data) return;

                $('#edit_set_id').val(data.id);
                $('#set_name').val(data.name);
                $('#set_code').val(data.code || '');
                $('#set_description').val(data.description || '');
                $('#modalTitleText').text('Edit Product Set: ' + data.name);

                $('#setItemsTableBody').empty();

                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        const $row = $(createProductRow(item));
                        $('#setItemsTableBody').append($row);
                    });
                } else {
                    $('#setItemsTableBody').append(createProductRow());
                }

                $('#setModal').modal('show');
                setTimeout(function() {
                    initSetSelect2($('#setItemsTableBody'));
                }, 100);
            }).fail(function() {
                alert('Could not fetch set details.');
            });
        });
    });
</script>
@endsection
