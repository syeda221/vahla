@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">

<style>
    /* ================= MODERN PROFESSIONAL POS & CONSOLIDATION UI ================= */
    :root {
        --pos-bg: #f8fafc;
        --pos-card-bg: #ffffff;
        --pos-border: #e2e8f0;
        --pos-border-focus: #3b82f6;
        --pos-primary: #0284c7;
        --pos-primary-hover: #0369a1;
        --pos-success: #10b981;
        --pos-success-hover: #059669;
        --pos-danger: #ef4444;
        --pos-text-main: #0f172a;
        --pos-text-muted: #64748b;
        --pos-radius: 8px;
        --pos-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
    }

    .main-container {
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        box-shadow: var(--pos-shadow) !important;
        background-color: var(--pos-card-bg) !important;
        padding: 12px 16px !important;
        max-width: 100%;
    }

    .meta-label {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #475569 !important;
        margin-bottom: 4px !important;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .top-info-card {
        background-color: #f8fafc !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        padding: 10px 12px !important;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }

    .form-control, .form-select {
        border: 1px solid var(--pos-border) !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        font-weight: 500 !important;
        color: var(--pos-text-main) !important;
        background-color: #ffffff !important;
        transition: all 0.15s ease-in-out !important;
        height: 34px !important;
        font-size: 0.82rem !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--pos-border-focus) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        outline: none !important;
        background-color: #ffffff !important;
    }

    .input-readonly {
        background-color: #f1f5f9 !important;
        border-color: var(--pos-border) !important;
        color: #334155 !important;
        font-weight: 600 !important;
        cursor: default !important;
    }

    /* Invoice Series Input Group (Matching Sale Page) */
    .invoice-group {
        position: relative;
        display: flex;
        width: 100%;
    }
    .invoice-group .btn-prefix {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
        border: 1px solid #0284c7 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-top-left-radius: 6px !important;
        border-bottom-left-radius: 6px !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        height: 34px !important;
        padding: 0 10px !important;
        font-size: 0.78rem !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        box-shadow: 0 1px 2px rgba(2, 132, 199, 0.2) !important;
        white-space: nowrap;
    }
    .invoice-group .btn-prefix:hover,
    .invoice-group .btn-prefix:focus {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.35) !important;
    }
    .invoice-group .btn-prefix::after {
        display: none !important;
    }

    .invoice-group #displayConsolidateInvoiceNo {
        border-radius: 0 !important;
        border-left: none !important;
        border-right: none !important;
        flex: 1 1 auto;
        min-width: 0;
    }

    .invoice-group .btn-refresh {
        background: #f1f5f9 !important;
        border: 1px solid var(--pos-border) !important;
        border-left: none !important;
        color: #475569 !important;
        border-top-right-radius: 6px !important;
        border-bottom-right-radius: 6px !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        height: 34px !important;
        padding: 0 10px !important;
        transition: all 0.15s;
    }
    .invoice-group .btn-refresh:hover {
        background: #e2e8f0 !important;
        color: var(--pos-primary) !important;
    }

    .invoice-group .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        margin-top: 4px !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        border: 1px solid var(--pos-border) !important;
    }

    /* Green Save Button */
    .btn-top-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        height: 34px !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
        font-size: 0.82rem !important;
        transition: all 0.15s ease !important;
    }
    .btn-top-save:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-1px);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35) !important;
    }

    .card-panel {
        background-color: #ffffff !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        padding: 12px !important;
        box-shadow: var(--pos-shadow) !important;
    }
</style>

@php
    $grandTotal = 0; 
    $totalPieces = 0;
    foreach($mergedItems as $mi) {
        $amount = $mi['amount'] ?? ($mi['display_qty'] * $mi['price']);
        $grandTotal += $amount;
        $totalPieces += ($mi['display_qty'] ?? $mi['delivered_qty']);
    }
    $prevBalance = $customer->previous_balance ?? 0;
    $balType = $prevBalance >= 0 ? 'Dr' : 'Cr';
    $netBalance = $prevBalance + $grandTotal;
    $netBalType = $netBalance >= 0 ? 'Dr' : 'Cr';

    $sList = $seriesList ?? [];
    if (empty($sList)) {
        $exclude = ['PO', 'PINV', 'DC', 'DDC', 'GRN', 'DGRN', 'SR', 'PRET', 'QUO', 'SO', 'SQ', 'INVSLE'];
        $dbSeries = \App\Models\InvoiceSeries::whereNotIn('prefix', $exclude)->orderByRaw("CASE WHEN prefix = 'INV' THEN 0 ELSE 1 END, is_default DESC, id ASC")->get();
        $standardDefs = \App\Models\InvoiceSeries::standardDefinitions();
        foreach ($dbSeries as $s) {
            $label = $standardDefs[$s->prefix]['name'] ?? ($s->prefix . ' Invoice');
            $sList[$s->prefix] = [
                'label' => $label,
                'next_no' => \App\Models\InvoiceSeries::generateNextNo($s->prefix)
            ];
        }
        if (empty($sList)) {
            $sList = [
                'INV' => ['label' => 'Sale Invoice', 'next_no' => \App\Models\InvoiceSeries::generateNextNo('INV')],
                'TAX' => ['label' => 'Tax Invoice', 'next_no' => \App\Models\InvoiceSeries::generateNextNo('TAX')],
                'CO'  => ['label' => 'Company Invoice', 'next_no' => \App\Models\InvoiceSeries::generateNextNo('CO')],
            ];
        }
    }
    $initialPrefix = isset($sList['INV']) ? 'INV' : (array_key_first($sList) ?? 'INV');
    $initialInvoiceNo = $sList[$initialPrefix]['next_no'] ?? \App\Models\InvoiceSeries::generateNextNo($initialPrefix);
@endphp

<div class="container-fluid py-2 px-2">
    <div class="main-container bg-white border mx-auto p-3 rounded-3">

        {{-- TOP HEADER BAR --}}
        <div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('direct-dc.index') }}" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Back to DCs">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                        <i class="fas fa-file-invoice text-primary"></i> 
                        Invoice Preview (DC Consolidation)
                    </h5>
                    <small class="text-muted" style="font-size: 0.72rem;">
                        Consolidating delivery items into a single invoice
                    </small>
                </div>
                <div class="d-flex align-items-center gap-1 ms-3">
                    <span class="text-muted small fw-bold" style="font-size: 0.72rem;">From DCs:</span>
                    @foreach($dcs as $dc)
                        <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-truck text-muted me-1"></i>{{ $dc->dc_number }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                    Prev Balance: <strong class="{{ $prevBalance >= 0 ? 'text-danger' : 'text-success' }}">{{ number_format(abs($prevBalance), 2) }} {{ $balType }}</strong>
                </span>
                <span class="badge bg-light text-primary border px-2 py-1" style="font-size: 0.75rem;">
                    Bill Amount: <strong>+{{ number_format($grandTotal, 2) }}</strong>
                </span>
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                    Net Balance: <strong class="{{ $netBalance >= 0 ? 'text-danger' : 'text-success' }}">{{ number_format(abs($netBalance), 2) }} {{ $netBalType }}</strong>
                </span>
            </div>
        </div>

        {{-- TOP INFORMATION PANEL FORM --}}
        <form action="{{ route('direct-dc.consolidate.store') }}" method="POST">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id ?? ($customerId ?? '') }}">
            @foreach($dcIds as $did)
                <input type="hidden" name="dc_ids[]" value="{{ $did }}">
            @endforeach

            <div class="top-info-card mb-3">
                <div class="row g-2 align-items-end w-100 m-0">
                    <!-- 1. Customer Name -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="fas fa-user-circle text-primary"></i> Customer
                        </label>
                        <input type="text" class="form-control input-readonly" value="{{ $customer->customer_name ?? 'Walk-in Customer' }}" readonly title="{{ $customer->customer_name ?? 'Walk-in Customer' }}">
                    </div>

                    <!-- 2. Invoice Series & Number Dropdown Input Group -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="fas fa-receipt text-primary"></i> Invoice Series &amp; No.
                        </label>
                        <div class="input-group input-group-sm invoice-group">
                            <button class="btn btn-prefix dropdown-toggle d-flex align-items-center gap-1" 
                                    type="button" 
                                    id="btnConsolidatePrefix" 
                                    data-bs-toggle="dropdown" 
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    title="Click to choose series">
                                <span id="activeConsolidatePrefixLabel">{{ $initialPrefix }}</span>
                                <i class="fas fa-caret-down" style="font-size: 0.72rem; opacity: 0.85;"></i>
                            </button>
                            <ul class="dropdown-menu shadow-lg p-1 border-0" id="dropdownConsolidateSeriesList" style="min-width: 230px; font-size: 0.82rem; z-index: 1050;">
                                <li class="dropdown-header py-1 text-uppercase fw-bold text-muted small" style="font-size: 10px; letter-spacing: 0.5px;">Choose Invoice Series</li>
                                @foreach($sList as $prefix => $sData)
                                <li>
                                    <div class="d-flex align-items-center justify-content-between px-1 {{ $prefix === $initialPrefix ? 'bg-light' : '' }}">
                                        <a class="dropdown-item fw-bold py-2 px-2 d-flex align-items-center justify-content-between flex-grow-1 {{ $prefix === $initialPrefix ? 'text-primary active' : 'text-dark' }}" href="javascript:void(0)" data-prefix="{{ $prefix }}" data-no="{{ $sData['next_no'] }}" style="border-radius: 4px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="{{ $prefix === $initialPrefix ? 'fas fa-check-circle text-primary' : 'far fa-circle text-muted' }}" style="font-size: 11px;"></i>
                                                <div>
                                                    <span class="badge bg-primary text-white font-monospace px-2 py-1 me-1">{{ $prefix }}</span>
                                                    <span class="small fw-semibold">{{ $sData['label'] }}</span>
                                                </div>
                                            </div>
                                            <span class="text-muted small font-monospace">({{ $sData['next_no'] }})</span>
                                        </a>
                                        @if(!in_array($prefix, ['INV', 'TAX', 'CO']))
                                            <button type="button" class="btn btn-sm btn-link text-danger p-1 ms-1 btn-delete-series border-0 text-decoration-none" data-prefix="{{ $prefix }}" title="Delete {{ $prefix }} series" style="opacity: 0.6; line-height: 1;">
                                                <i class="fas fa-trash-alt" style="font-size: 0.72rem;"></i>
                                            </button>
                                        @endif
                                    </div>
                                </li>
                                @endforeach
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item fw-bold py-2 px-3 text-success text-center" href="javascript:void(0)" id="btnOpenAddSeriesModal">
                                        <i class="fas fa-plus me-1"></i> Add Series
                                    </a>
                                </li>
                            </ul>

                            <input type="hidden" name="prefix" id="inputConsolidatePrefix" value="{{ $initialPrefix }}">
                            <input type="text" class="form-control text-center fw-bold bg-white" name="invoice_no" id="displayConsolidateInvoiceNo" value="{{ $initialInvoiceNo }}" placeholder="e.g. INV-0001" style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;" title="Aap custom invoice number bhi enter kar sakte hain">

                            <button class="btn btn-refresh" 
                                    type="button" 
                                    id="btnRefreshConsolidateInvoiceNo" 
                                    title="Refresh Next Invoice Number">
                                <i class="fas fa-sync-alt" id="iconRefreshConsolidateInvoice"></i>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Invoice Date (Supports Backdating) -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="far fa-calendar-alt text-primary"></i> Invoice Date
                        </label>
                        <input type="date" name="sale_date" class="form-control text-center fw-bold" value="{{ date('Y-m-d') }}" required style="font-size: 0.82rem;">
                    </div>

                    <!-- 4. Confirm & Generate Invoice Button -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label d-none d-md-block" style="visibility: hidden;">Action</label>
                        <button type="submit" class="btn btn-top-save w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size: 0.82rem;">
                            <i class="fas fa-check-circle"></i> Confirm &amp; Generate Invoice
                        </button>
                    </div>
                </div>
            </div>
            </div>
            
            {{-- DELIVERIES ITEMS TABLE --}}
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <span class="fw-bold text-dark" style="font-size: 0.82rem;">
                        <i class="fas fa-boxes-stacked text-primary me-1"></i> Delivery Items to be Invoiced
                    </span>
                    <span class="badge bg-primary text-white font-monospace px-2 py-1" style="font-size: 0.75rem;">
                        {{ count($mergedItems) }} {{ count($mergedItems) == 1 ? 'Item' : 'Items' }} &bull; Total Units: <span id="lblTotalUnits">{{ $totalPieces }}</span>
                    </span>
                </div>

                <div class="table-responsive border rounded-2">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;" id="tblItems">
                        <thead style="background-color: #f1f5f9;">
                            <tr>
                                <th class="py-2 px-3 fw-bold text-secondary text-uppercase" style="font-size: 11px;">Item Description</th>
                                <th class="py-2 px-3 text-center fw-bold text-secondary text-uppercase" style="font-size: 11px; width: 12%;">Delivered Qty</th>
                                <th class="py-2 px-3 text-center fw-bold text-secondary text-uppercase" style="font-size: 11px; width: 8%;">Unit</th>
                                <th class="py-2 px-3 text-end fw-bold text-secondary text-uppercase" style="font-size: 11px; width: 14%;">Unit Rate</th>
                                <th class="py-2 px-3 text-end fw-bold text-secondary text-uppercase" style="font-size: 11px; width: 14%;">Discount</th>
                                <th class="py-2 px-3 text-end fw-bold text-secondary text-uppercase" style="font-size: 11px; width: 16%;">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mergedItems as $key => $mi)
                            @php 
                                $amount = $mi['amount'] ?? ($mi['display_qty'] * $mi['price']);
                                $displayQty = $mi['display_qty'] ?? $mi['delivered_qty'];
                                $qtyText = ((float)$displayQty == (int)$displayQty) ? (int)$displayQty : number_format((float)$displayQty, 2);
                                
                                $vName = '';
                                if(!empty($mi['color'])) {
                                    $decoded = base64_decode($mi['color'], true);
                                    $vData = $decoded !== false ? json_decode($decoded, true) : null;
                                    if(!is_array($vData)) {
                                        $vData = is_string($mi['color']) ? json_decode($mi['color'], true) : $mi['color'];
                                    }
                                    if(is_array($vData) && !empty($vData['name'])) {
                                        $vName = ' <span class="badge bg-light text-secondary border px-2 py-0 ms-1">' . $vData['name'] . '</span>';
                                    }
                                }
                            @endphp
                            <tr class="item-row">
                                <td class="px-3">
                                    <div class="fw-bold text-dark">{{ $mi['product']->item_name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{!! $vName !!}</div>
                                    <input type="hidden" name="items[{{ $key }}][product_id]" value="{{ $mi['product_id'] }}">
                                    <input type="hidden" name="items[{{ $key }}][warehouse_id]" value="{{ $mi['warehouse_id'] }}">
                                    <input type="hidden" name="items[{{ $key }}][color]" value="{{ $mi['color'] }}">
                                    <input type="hidden" name="items[{{ $key }}][size_mode]" value="{{ $mi['size_mode'] }}">
                                    <input type="hidden" name="items[{{ $key }}][disp_factor]" value="{{ $mi['disp_factor'] }}">
                                </td>
                                <td class="text-center fw-bold text-primary">
                                    <input type="number" step="any" class="form-control form-control-sm text-center fw-bold font-monospace input-qty" name="items[{{ $key }}][display_qty]" value="{{ $displayQty }}" min="0">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 small">
                                        {{ $mi['unit'] ?? 'Pcs' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <input type="number" step="any" class="form-control form-control-sm text-end font-monospace input-price" name="items[{{ $key }}][price]" value="{{ $mi['price'] }}" min="0">
                                </td>
                                <td class="text-end">
                                    <input type="number" step="any" class="form-control form-control-sm text-end font-monospace text-danger input-discount" name="items[{{ $key }}][discount_amount]" value="0" min="0" placeholder="0.00">
                                </td>
                                <td class="text-end fw-bold font-monospace text-dark px-3 line-total">
                                    {{ number_format($amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background-color: #f8fafc; border-top: 2px solid #cbd5e1;">
                            <tr>
                                <td colspan="5" class="text-end fw-bold text-dark text-uppercase py-2 px-3" style="font-size: 0.85rem;">
                                    Total Invoice Bill Amount:
                                </td>
                                <td class="text-end fw-bold font-monospace text-success fs-6 py-2 px-3" id="lblGrandTotal">
                                    PKR {{ number_format($grandTotal, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnConsolidatePrefix');
        const list = document.getElementById('dropdownConsolidateSeriesList');
        const refreshBtn = document.getElementById('btnRefreshConsolidateInvoiceNo');
        const refreshIcon = document.getElementById('iconRefreshConsolidateInvoice');
        let currentPrefix = '{{ $initialPrefix }}';

        function fetchNextNo(prefix) {
            if (refreshIcon) refreshIcon.classList.add('fa-spin');
            
            // Check preloaded numbers or fetch via AJAX
            fetch("{{ route('invoice_series.generate_no') }}?prefix=" + encodeURIComponent(prefix))
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                    if (data && data.invoice_no) {
                        document.getElementById('displayConsolidateInvoiceNo').value = data.invoice_no;
                    }
                })
                .catch(function() {
                    if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                });
        }

        if (btn && list) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                list.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.invoice-group')) {
                    list.classList.remove('show');
                }
            });

            // Delegate click for series items
            list.addEventListener('click', function(e) {
                const item = e.target.closest('a[data-prefix]');
                if (!item) return;
                e.preventDefault();
                const prefix = item.getAttribute('data-prefix');
                const nextNo = item.getAttribute('data-no');
                if (!prefix) return;

                currentPrefix = prefix;
                document.getElementById('inputConsolidatePrefix').value = prefix;
                document.getElementById('activeConsolidatePrefixLabel').textContent = prefix;
                
                if (nextNo) {
                    document.getElementById('displayConsolidateInvoiceNo').value = nextNo;
                }
                fetchNextNo(prefix);

                // Update active styles
                list.querySelectorAll('a[data-prefix]').forEach(function(el) {
                    el.classList.remove('text-primary', 'active');
                    el.classList.add('text-dark');
                    const parentDiv = el.closest('div');
                    if (parentDiv) parentDiv.classList.remove('bg-light');
                    const icon = el.querySelector('i');
                    if (icon) {
                        icon.className = 'far fa-circle text-muted';
                        icon.style.fontSize = '11px';
                    }
                });

                item.classList.remove('text-dark');
                item.classList.add('text-primary', 'active');
                const activeParent = item.closest('div');
                if (activeParent) activeParent.classList.add('bg-light');
                const activeIcon = item.querySelector('i');
                if (activeIcon) {
                    activeIcon.className = 'fas fa-check-circle text-primary';
                    activeIcon.style.fontSize = '';
                }

                list.classList.remove('show');
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchNextNo(currentPrefix);
            });
        }

        // Open Add Series Modal
        $(document).on('click', '#btnOpenAddSeriesModal', function(e) {
            e.preventDefault();
            if (list) list.classList.remove('show');
            $('#modalAddInvoiceSeries').modal('show');
        });

        // Submit Add Series Form via AJAX
        $('#formAddInvoiceSeries').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let saveBtn = $('#btnSaveSeries');

            saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('invoice_series.store') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    saveBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save &amp; Select');
                    
                    if (res.success) {
                        $('#modalAddInvoiceSeries').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                        $('#formAddInvoiceSeries')[0].reset();

                        currentPrefix = res.prefix;
                        $('#activeConsolidatePrefixLabel').text(res.prefix);
                        $('#inputConsolidatePrefix').val(res.prefix);
                        $('#displayConsolidateInvoiceNo').val(res.invoice_no);

                        // Update or insert item in dropdown list instantly
                        let existingItem = $(`#dropdownConsolidateSeriesList a[data-prefix="${res.prefix}"]`);
                        if (existingItem.length > 0) {
                            existingItem.attr('data-no', res.invoice_no);
                            existingItem.find('span.font-monospace:last').text(`(${res.invoice_no})`);
                        } else {
                            let newItemHtml = `<li>
                                <div class="d-flex align-items-center justify-content-between px-1 bg-light">
                                    <a class="dropdown-item fw-bold py-2 px-2 d-flex align-items-center justify-content-between flex-grow-1 text-primary active" 
                                       href="javascript:void(0)" 
                                       data-prefix="${res.prefix}" 
                                       data-no="${res.invoice_no}"
                                       style="border-radius: 4px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check-circle text-primary"></i>
                                            <div>
                                                <span class="badge bg-primary text-white font-monospace px-2 py-1 me-1">${res.prefix}</span>
                                                <span class="small fw-semibold">${res.prefix} Series</span>
                                            </div>
                                        </div>
                                        <span class="text-muted small font-monospace">(${res.invoice_no})</span>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-1 ms-1 btn-delete-series border-0 text-decoration-none" data-prefix="${res.prefix}" title="Delete ${res.prefix} series" style="opacity: 0.6; line-height: 1;">
                                        <i class="fas fa-trash-alt" style="font-size: 0.72rem;"></i>
                                    </button>
                                </div>
                            </li>`;
                            $('#dropdownConsolidateSeriesList li:has(hr)').before(newItemHtml);
                        }

                        // Update active state
                        $('#dropdownConsolidateSeriesList a[data-prefix]').not(`[data-prefix="${res.prefix}"]`)
                            .removeClass('text-primary active')
                            .addClass('text-dark')
                            .closest('div')
                            .removeClass('bg-light')
                            .find('i.fa-check-circle')
                            .removeClass('fas fa-check-circle text-primary')
                            .addClass('far fa-circle text-muted')
                            .css('font-size', '11px');

                        let activeItem = $(`#dropdownConsolidateSeriesList a[data-prefix="${res.prefix}"]`);
                        activeItem.removeClass('text-dark').addClass('text-primary active');
                        activeItem.closest('div').addClass('bg-light');
                        activeItem.find('i').removeClass('far fa-circle text-muted').addClass('fas fa-check-circle text-primary').css('font-size', '');
                    }
                },
                error: function(xhr) {
                    saveBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save &amp; Select');
                    let msg = 'Failed to save series';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });

        // Delete Series Handler
        $(document).on('click', '.btn-delete-series', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let prefixToDelete = $(this).data('prefix');
            if (!prefixToDelete) return;

            if (!confirm(`Are you sure you want to delete "${prefixToDelete}" series?`)) {
                return;
            }

            let btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" style="font-size: 0.72rem;"></i>');

            $.ajax({
                url: "{{ route('invoice_series.delete') }}",
                type: "POST",
                data: { prefix: prefixToDelete },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        btn.closest('li').remove();
                        // If the currently selected prefix was the deleted one, switch to INV
                        if (currentPrefix === prefixToDelete) {
                            let fallbackItem = $('#dropdownConsolidateSeriesList a[data-prefix="INV"]');
                            if (fallbackItem.length === 0) {
                                fallbackItem = $('#dropdownConsolidateSeriesList a[data-prefix]').first();
                            }
                            if (fallbackItem.length > 0) {
                                fallbackItem.trigger('click');
                            }
                        }
                    } else {
                        btn.prop('disabled', false).html('<i class="fas fa-trash-alt" style="font-size: 0.72rem;"></i>');
                        alert(res.message || 'Could not delete series');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-trash-alt" style="font-size: 0.72rem;"></i>');
                    let msg = 'Failed to delete series';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });
    });

    // Dynamic Calculation Logic
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('tblItems');
        if (!table) return;

        function updateTotals() {
            let grandTotal = 0;
            let totalUnits = 0;
            const rows = table.querySelectorAll('.item-row');
            
            rows.forEach(row => {
                const qtyInput = row.querySelector('.input-qty');
                const priceInput = row.querySelector('.input-price');
                const discountInput = row.querySelector('.input-discount');
                const totalCell = row.querySelector('.line-total');
                
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                
                let amount = (qty * price) - discount;
                if (amount < 0) amount = 0;
                
                totalCell.textContent = amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                grandTotal += amount;
                totalUnits += qty;
            });
            
            document.getElementById('lblGrandTotal').textContent = 'PKR ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('lblTotalUnits').textContent = totalUnits;
        }

        table.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-qty') || 
                e.target.classList.contains('input-price') || 
                e.target.classList.contains('input-discount')) {
                updateTotals();
            }
        });
    });
</script>

<!-- Modal: Add / Manage Invoice Series -->
<div class="modal fade" id="modalAddInvoiceSeries" tabindex="-1" aria-labelledby="modalAddInvoiceSeriesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom bg-light px-3 py-2">
                <h6 class="modal-title fw-bold text-dark mb-0" id="modalAddInvoiceSeriesLabel">
                    <i class="fas fa-barcode text-success me-1"></i> Add Invoice Series
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddInvoiceSeries">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Prefix (e.g. SGST, POS, TAX, CO)</label>
                        <input type="text" name="prefix" id="seriesPrefixInput" class="form-control form-control-sm text-uppercase fw-bold" placeholder="e.g. SGST" required style="letter-spacing: 1px;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Starting Number (Counter)</label>
                        <input type="number" name="next_number" id="seriesNextNumInput" class="form-control form-control-sm fw-bold text-primary" placeholder="e.g. 1" min="1" value="1" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Padding Length (Zero Digits)</label>
                        <select name="padding" id="seriesPaddingSelect" class="form-select form-select-sm fw-bold">
                            <option value="3">3 Digits (e.g., 001)</option>
                            <option value="4" selected>4 Digits (e.g., 0001)</option>
                            <option value="6">6 Digits (e.g., 000001)</option>
                            <option value="8">8 Digits (e.g., 00000001)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-3" id="btnSaveSeries">
                        <i class="fas fa-save me-1"></i> Save &amp; Select
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
