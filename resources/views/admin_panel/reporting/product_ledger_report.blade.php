@extends('admin_panel.layout.app')

@section('content')
<style>
    .sale-report-container {
        padding: 10px 14px;
        background: #f1f5f9;
        min-height: calc(100vh - 75px);
    }
    .sale-filter-label {
        margin-right: 4px !important;
        margin-bottom: 0 !important;
        white-space: nowrap;
        font-weight: 700;
        font-size: .78rem;
        color: #475569;
    }
    .summary-pill-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .stat-pill {
        flex: 1 1 0px;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .stat-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);
    }
    .stat-pill .stat-label {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .stat-pill .stat-val {
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .sale-table-wrap {
        height: calc(100vh - 300px);
        max-height: calc(100vh - 300px);
        min-height: 420px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
    }
    .report-table {
        font-size: .78rem;
        margin-bottom: 0;
    }
    .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-size: .75rem;
        font-weight: 700;
        padding: 9px 10px;
        border-bottom: 2px solid #334155;
        white-space: nowrap;
    }

    .badge-inward {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-outward {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        body { background: #ffffff !important; font-size: 11px; color: #000; padding: 0 !important; }
        .no-print, header, .sidebar, .navbar, .rt_nav_header, footer, .sale-report-container > .card.no-print { display: none !important; }
        .app-page-body { padding-top: 0 !important; margin-top: 0 !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; min-height: auto !important; margin-top: 0 !important; }
        .sale-table-wrap { height: auto !important; max-height: none !important; overflow: visible !important; border: none !important; }
        .report-table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; }
        .report-table th, .report-table td { border: 1px solid #000 !important; padding: 5px 6px !important; color: #000 !important; background: transparent !important; }
        .report-table thead th { position: static !important; background-color: #f1f5f9 !important; color: #000 !important; border-bottom: 2px solid #000 !important; font-size: 11px !important; }
        .badge { border: 1px solid #888 !important; color: #000 !important; padding: 1px 4px !important; background: transparent !important; }
        .print-header-block { display: block !important; }
    }
    .print-header-block { display: none; }
</style>

<div class="sale-report-container">

    {{-- PRINT HEADER (Shown only on Print) --}}
    <div class="print-header-block mb-3 text-center">
        <h3 class="fw-bold mb-1" style="text-transform: uppercase; letter-spacing: 1px;">{{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}</h3>
        <h5 class="fw-bold text-secondary mb-1">PRODUCT STOCK MOVEMENT LEDGER</h5>
        <div class="small text-muted" id="printMetaDetails">Period: — | Product: —</div>
        <hr class="my-2 border-dark">
    </div>

    {{-- DESKTOP FILTER HEADER CARD --}}
    <div class="card border-0 shadow-sm mb-2 no-print" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="productLedgerFilterForm">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 pb-2 border-bottom">
                    {{-- Title --}}
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                            <i class="fas fa-boxes-stacked text-primary me-2"></i>Product Stock Movement Ledger
                        </span>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" id="btnPresetAllTime" title="View entire stock history">
                            <i class="fas fa-infinity me-1"></i> All Time
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" id="btnPresetThisMonth" title="View this month movements">
                            <i class="fas fa-calendar-alt me-1"></i> This Month
                        </button>
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" id="btnFetchLedger">
                            <i class="fas fa-sync-alt me-1"></i> Generate Ledger
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm px-3 fw-bold" id="btnPrintLedger">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm px-3 fw-bold" id="btnExportCSV">
                            <i class="fas fa-file-excel me-1"></i> CSV
                        </button>
                    </div>
                </div>

                {{-- Filter Inputs Row --}}
                <div class="row g-2 align-items-end">
                    {{-- Product Selection (Select2 AJAX) --}}
                    <div class="col-md-5 col-lg-5">
                        <label class="sale-filter-label mb-1 d-flex align-items-center gap-1">
                            <i class="fas fa-box text-primary"></i> Select Product: <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm" id="ledgerProductSelect" name="product_id" style="width: 100%;">
                            @if($selectedProduct)
                                <option value="{{ $selectedProduct->id }}" selected>
                                    {{ ($selectedProduct->item_code ? '[' . $selectedProduct->item_code . '] ' : '') . $selectedProduct->item_name . ' (' . ($selectedProduct->unit->name ?? 'Pcs') . ')' }}
                                </option>
                            @else
                                <option value="">-- Type to search Product Name or Code... --</option>
                                @foreach($initialProducts as $ip)
                                    <option value="{{ $ip->id }}">
                                        {{ ($ip->item_code ? '[' . $ip->item_code . '] ' : '') . $ip->item_name . ' (' . ($ip->unit->name ?? 'Pcs') . ')' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="col-6 col-md-2 col-lg-2">
                        <label for="startDateInput" class="sale-filter-label mb-1">From Date:</label>
                        <input type="date" name="start_date" id="startDateInput" class="form-control form-control-sm fw-bold" value="2000-01-01" style="height: 32px; font-size: .8rem; border-radius: 6px;">
                    </div>
                    <div class="col-6 col-md-2 col-lg-2">
                        <label for="endDateInput" class="sale-filter-label mb-1">To Date:</label>
                        <input type="date" name="end_date" id="endDateInput" class="form-control form-control-sm fw-bold" value="{{ date('Y-m-d') }}" style="height: 32px; font-size: .8rem; border-radius: 6px;">
                    </div>

                    {{-- Movement Type Filter --}}
                    <div class="col-md-3 col-lg-3">
                        <label for="movementTypeSelect" class="sale-filter-label mb-1">Movement Type:</label>
                        <select class="form-select form-select-sm fw-bold" id="movementTypeSelect" name="movement_type" style="height: 32px; font-size: .8rem; border-radius: 6px;">
                            <option value="all">All Movements (Complete History)</option>
                            <option value="sale">Sales (Outward)</option>
                            <option value="purchase">Purchases / GRN (Inward)</option>
                            <option value="return">Returns (Sale &amp; Purchase)</option>
                            <option value="adjustment">Stock Adjustments / Audits</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- PRODUCT MASTER DETAIL STRIP (Shown once product is loaded) --}}
    <div id="productDetailStrip" class="card border-0 shadow-sm mb-2 d-none" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white font-monospace px-2 py-1" id="dispProductCode">CODE</span>
                <span class="fw-bold text-dark fs-6" id="dispProductName">Product Name</span>
                <span class="badge bg-info text-white rounded-pill px-2" id="dispProductUnit">Unit: Pcs</span>
            </div>
            <div class="d-flex align-items-center gap-3 text-muted small">
                <span>Category: <strong class="text-dark" id="dispProductCategory">—</strong></span>
                <span>Brand: <strong class="text-dark" id="dispProductBrand">—</strong></span>
                <span>Master Stock (DB): <strong class="text-primary font-monospace" id="dispMasterStock">0.00</strong></span>
            </div>
        </div>
    </div>

    {{-- SUMMARY KPI STAT-PILL BAR --}}
    <div id="kpiSummaryCard" class="card border-0 shadow-sm mb-2 d-none" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <div class="summary-pill-bar">
                {{-- Opening Stock --}}
                <div class="stat-pill">
                    <div class="stat-label text-secondary"><i class="fas fa-hourglass-start me-1"></i>Opening Stock</div>
                    <div class="stat-val text-dark font-monospace" id="kpiOpeningStock">0.0000</div>
                    <small class="text-muted kpiUnitLabel" style="font-size: 10px;">Base Unit</small>
                </div>

                {{-- Total Inward --}}
                <div class="stat-pill" style="border-color: #86efac; background: #f0fdf4;">
                    <div class="stat-label text-success"><i class="fas fa-arrow-down me-1"></i>Total Inward (+)</div>
                    <div class="stat-val text-success font-monospace" id="kpiTotalInward">0.0000</div>
                    <small class="text-success kpiUnitLabel" style="font-size: 10px;">Base Unit</small>
                </div>

                {{-- Total Outward --}}
                <div class="stat-pill" style="border-color: #fca5a5; background: #fef2f2;">
                    <div class="stat-label text-danger"><i class="fas fa-arrow-up me-1"></i>Total Outward (-)</div>
                    <div class="stat-val text-danger font-monospace" id="kpiTotalOutward">0.0000</div>
                    <small class="text-danger kpiUnitLabel" style="font-size: 10px;">Base Unit</small>
                </div>

                {{-- Net Movement --}}
                <div class="stat-pill">
                    <div class="stat-label text-primary"><i class="fas fa-exchange-alt me-1"></i>Net Movement</div>
                    <div class="stat-val text-primary font-monospace" id="kpiNetMovement">0.0000</div>
                    <small class="text-primary kpiUnitLabel" style="font-size: 10px;">Base Unit</small>
                </div>

                {{-- Calculated Closing Stock --}}
                <div class="stat-pill" style="border-color: #93c5fd; background: #eff6ff;">
                    <div class="stat-label text-primary"><i class="fas fa-calculator me-1"></i>Closing Stock</div>
                    <div class="stat-val text-primary font-monospace fs-6" id="kpiClosingStock">0.0000</div>
                    <small class="text-primary kpiUnitLabel" style="font-size: 10px;">Base Unit</small>
                </div>
            </div>
        </div>
    </div>

    {{-- LEDGER TABLE CONTAINER --}}
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-body p-0">
            
            {{-- Loading State --}}
            <div id="ledgerLoading" class="text-center py-5 d-none">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="text-muted fw-bold mt-2" style="font-size: 0.85rem;">Calculating stock movements &amp; running balance...</div>
            </div>

            {{-- Initial / No Product Selected State --}}
            <div id="ledgerPrompt" class="text-center py-5">
                <div class="text-muted mb-2">
                    <i class="fas fa-search-plus fa-3x text-secondary" style="opacity: 0.5;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Select a Product to View its Stock Ledger</h6>
                <p class="text-muted small mb-0">Use the search box above to choose a product and see all chronological movements.</p>
            </div>

            {{-- Table Wrapper --}}
            <div id="ledgerTableWrapper" class="sale-table-wrap d-none">
                <table class="table table-hover report-table" id="productLedgerTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
                            <th style="width: 140px;">DATE &amp; TIME</th>
                            <th style="width: 120px;">TYPE</th>
                            <th style="width: 120px;">VOUCHER / REF</th>
                            <th style="width: 160px;">PARTY / SOURCE</th>
                            <th style="width: 110px;" class="text-end text-success">INWARD (+)</th>
                            <th style="width: 110px;" class="text-end text-danger">OUTWARD (-)</th>
                            <th style="width: 130px;" class="text-end text-primary">RUNNING BAL</th>
                            <th style="width: 150px;">ADJUSTMENT INFO</th>
                            <th>REMARKS / NOTE</th>
                        </tr>
                    </thead>
                    <tbody id="productLedgerTableBody">
                        {{-- Injected via JavaScript --}}
                    </tbody>
                </table>
            </div>

            {{-- Empty Records in Date Range --}}
            <div id="ledgerEmpty" class="text-center py-5 d-none">
                <i class="fas fa-calendar-times fa-3x text-muted mb-2" style="opacity: 0.4;"></i>
                <h6 class="fw-bold text-dark mb-1">No Movements Found in this Period</h6>
                <p class="text-muted small mb-0">Try expanding the date range or clicking "All Time" to view full history.</p>
            </div>

        </div>
    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        
        // 1. Initialize Select2 with live AJAX search
        $('#ledgerProductSelect').select2({
            placeholder: '-- Type product name or code to search... --',
            allowClear: true,
            ajax: {
                url: "{{ route('report.product.ledger.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data.results };
                },
                cache: true
            }
        });

        // Current loaded ledger data store
        window.currentLedgerData = null;

        // Auto fetch if product is already selected
        var initProdId = $('#ledgerProductSelect').val();
        if (initProdId) {
            fetchLedger();
        }

        // On product selection change
        $('#ledgerProductSelect').on('change', function() {
            var val = $(this).val();
            if (val) {
                fetchLedger();
            } else {
                $('#productDetailStrip, #kpiSummaryCard, #ledgerTableWrapper, #ledgerEmpty').addClass('d-none');
                $('#ledgerPrompt').removeClass('d-none');
                window.currentLedgerData = null;
            }
        });

        // Date Presets
        $('#btnPresetAllTime').on('click', function() {
            $('#startDateInput').val('2000-01-01');
            $('#endDateInput').val('{{ date('Y-m-d') }}');
            if ($('#ledgerProductSelect').val()) fetchLedger();
        });

        $('#btnPresetThisMonth').on('click', function() {
            $('#startDateInput').val('{{ date('Y-m-01') }}');
            $('#endDateInput').val('{{ date('Y-m-d') }}');
            if ($('#ledgerProductSelect').val()) fetchLedger();
        });

        // Trigger Fetch
        $('#btnFetchLedger, #movementTypeSelect').on('click change', function(e) {
            if (e.type === 'click') e.preventDefault();
            if ($('#ledgerProductSelect').val()) {
                fetchLedger();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Product',
                    text: 'Please select a product first to view its stock ledger.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });

        // Main AJAX Fetch Function
        function fetchLedger() {
            var prodId = $('#ledgerProductSelect').val();
            if (!prodId) return;

            var startDate = $('#startDateInput').val();
            var endDate = $('#endDateInput').val();
            var mType = $('#movementTypeSelect').val();

            $('#ledgerPrompt, #ledgerTableWrapper, #ledgerEmpty').addClass('d-none');
            $('#ledgerLoading').removeClass('d-none');

            $.ajax({
                url: "{{ route('report.product.ledger.fetch') }}",
                type: 'GET',
                data: {
                    product_id: prodId,
                    start_date: startDate,
                    end_date: endDate,
                    movement_type: mType
                },
                dataType: 'json',
                success: function(res) {
                    $('#ledgerLoading').addClass('d-none');
                    if (!res.success) {
                        alert(res.message || 'Failed to fetch ledger.');
                        return;
                    }

                    window.currentLedgerData = res;
                    renderLedger(res);
                },
                error: function(xhr) {
                    $('#ledgerLoading').addClass('d-none');
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error fetching ledger data.';
                    alert(msg);
                }
            });
        }

        // Render Ledger UI
        function renderLedger(data) {
            var prod = data.product;
            var kpi = data.kpi;
            var uName = prod.base_unit || 'Pcs';

            // 1. Update Product Master Strip
            $('#dispProductCode').text(prod.item_code);
            $('#dispProductName').text(prod.item_name);
            $('#dispProductUnit').text('Base Unit: ' + uName);
            $('#dispProductCategory').text(prod.category);
            $('#dispProductBrand').text(prod.brand);
            $('#dispMasterStock').text(parseFloat(prod.master_stock).toFixed(4) + ' ' + uName);
            $('#productDetailStrip').removeClass('d-none');

            // 2. Update KPI Cards
            $('.kpiUnitLabel').text(uName);
            $('#kpiOpeningStock').text(parseFloat(kpi.opening_stock).toFixed(4) + ' ' + uName);
            $('#kpiTotalInward').text('+' + parseFloat(kpi.total_inward).toFixed(4) + ' ' + uName);
            $('#kpiTotalOutward').text('-' + parseFloat(kpi.total_outward).toFixed(4) + ' ' + uName);
            $('#kpiNetMovement').text((kpi.net_movement >= 0 ? '+' : '') + parseFloat(kpi.net_movement).toFixed(4) + ' ' + uName);
            $('#kpiClosingStock').text(parseFloat(kpi.closing_stock).toFixed(4) + ' ' + uName);
            $('#kpiSummaryCard').removeClass('d-none');

            // Print Meta
            $('#printMetaDetails').text('Product: [' + prod.item_code + '] ' + prod.item_name + ' | Base Unit: ' + uName + ' | Period: ' + data.start_date + ' to ' + data.end_date);

            // 3. Render Table Rows
            var tbody = $('#productLedgerTableBody');
            tbody.empty();

            // Opening Balance Row
            var openRow = '<tr class="table-light fw-bold" style="background-color: #f8fafc;">' +
                '<td class="text-center text-muted">0</td>' +
                '<td class="text-muted">' + data.start_date + '</td>' +
                '<td><span class="badge badge-secondary px-2 py-1"><i class="fas fa-hourglass-start me-1"></i>OPENING</span></td>' +
                '<td class="font-monospace text-muted">INIT</td>' +
                '<td class="text-muted">Opening Stock</td>' +
                '<td class="text-end text-muted font-monospace">—</td>' +
                '<td class="text-end text-muted font-monospace">—</td>' +
                '<td class="text-end font-monospace fw-bold text-dark">' + parseFloat(kpi.opening_stock).toFixed(4) + ' <span class="small text-muted">' + uName + '</span></td>' +
                '<td class="text-muted small">—</td>' +
                '<td class="text-muted small">Opening stock balance before ' + data.start_date + '</td>' +
            '</tr>';
            tbody.append(openRow);

            if (data.rows.length === 0) {
                $('#ledgerTableWrapper').removeClass('d-none');
                return;
            }

            data.rows.forEach(function(r) {
                var inText = r.in_qty > 0 ? '<span class="text-success fw-bold font-monospace">+' + parseFloat(r.in_qty).toFixed(4) + '</span> <span class="small text-muted">' + uName + '</span>' : '<span class="text-muted">—</span>';
                var outText = r.out_qty > 0 ? '<span class="text-danger fw-bold font-monospace">-' + parseFloat(r.out_qty).toFixed(4) + '</span> <span class="small text-muted">' + uName + '</span>' : '<span class="text-muted">—</span>';
                
                var voucherHtml = r.voucher_no;
                if (r.voucher_url) {
                    voucherHtml = '<a href="' + r.voucher_url + '" target="_blank" class="fw-bold text-primary font-monospace text-decoration-none" title="Click to view voucher">' + r.voucher_no + ' <i class="fas fa-external-link-alt" style="font-size: 9px;"></i></a>';
                }

                var badgeClass = 'badge-' + r.type_badge;
                if (r.type_badge === 'primary') badgeClass = 'badge-primary';
                if (r.type_badge === 'success') badgeClass = 'badge-success';
                if (r.type_badge === 'danger') badgeClass = 'badge-danger';
                if (r.type_badge === 'warning') badgeClass = 'badge-warning';
                if (r.type_badge === 'info') badgeClass = 'badge-info';
                if (r.type_badge === 'secondary') badgeClass = 'badge-secondary';

                var adjHtml = '—';
                if (r.adj_mode) {
                    adjHtml = '<span class="badge badge-warning text-dark px-2 py-0 fw-bold" style="font-size: 10px;">' + r.adj_mode + '</span>';
                }

                var variantBadge = '';
                if (r.variant_detail) {
                    variantBadge = '<br><span class="badge badge-light border text-secondary px-1 py-0" style="font-size: 9px;"><i class="fas fa-cubes me-1"></i>' + r.variant_detail + '</span>';
                }

                var tr = '<tr>' +
                    '<td class="text-center text-muted" style="vertical-align: middle;">' + r.index + '</td>' +
                    '<td style="vertical-align: middle;"><span class="fw-semibold text-dark">' + r.date + '</span><br><small class="text-muted" style="font-size: 10px;">' + r.time + '</small></td>' +
                    '<td style="vertical-align: middle;"><span class="badge ' + badgeClass + ' px-2 py-1">' + r.type_label + '</span></td>' +
                    '<td style="vertical-align: middle;">' + voucherHtml + '</td>' +
                    '<td style="vertical-align: middle;" class="fw-semibold text-dark">' + (r.party_name || '—') + '</td>' +
                    '<td class="text-end font-monospace" style="vertical-align: middle;">' + inText + (r.in_qty > 0 ? variantBadge : '') + '</td>' +
                    '<td class="text-end font-monospace" style="vertical-align: middle;">' + outText + (r.out_qty > 0 ? variantBadge : '') + '</td>' +
                    '<td class="text-end font-monospace fw-bold fs-7 text-dark" style="vertical-align: middle;">' + parseFloat(r.running_balance).toFixed(4) + ' <span class="small text-muted">' + uName + '</span></td>' +
                    '<td style="vertical-align: middle;">' + adjHtml + '</td>' +
                    '<td class="small text-muted" style="vertical-align: middle;">' + (r.note || '—') + '</td>' +
                '</tr>';

                tbody.append(tr);
            });

            $('#ledgerTableWrapper').removeClass('d-none');
        }

        // Print Action
        $('#btnPrintLedger').on('click', function() {
            if (!window.currentLedgerData) {
                alert('Please generate product ledger first before printing.');
                return;
            }
            window.print();
        });

        // CSV Export Action
        $('#btnExportCSV').on('click', function() {
            if (!window.currentLedgerData || !window.currentLedgerData.rows) {
                alert('Please generate product ledger first to export.');
                return;
            }

            var d = window.currentLedgerData;
            var csv = [];
            csv.push(['Product Stock Movement Ledger']);
            csv.push(['Product', d.product.item_name, 'Code', d.product.item_code, 'Base Unit', d.product.base_unit]);
            csv.push(['Period', d.start_date + ' to ' + d.end_date]);
            csv.push(['Opening Stock', d.kpi.opening_stock, 'Total Inward', d.kpi.total_inward, 'Total Outward', d.kpi.total_outward, 'Closing Stock', d.kpi.closing_stock]);
            csv.push([]);
            csv.push(['#', 'Date', 'Time', 'Type', 'Voucher', 'Party/Source', 'Inward (+)', 'Outward (-)', 'Running Balance (' + d.product.base_unit + ')', 'Adjustment Info', 'Note']);

            // Opening Row
            csv.push([0, d.start_date, '00:00', 'OPENING', 'INIT', 'Opening Stock', '0', '0', d.kpi.opening_stock, '-', 'Opening Balance']);

            d.rows.forEach(function(r) {
                csv.push([
                    r.index,
                    r.date,
                    r.time,
                    r.type_label,
                    r.voucher_no,
                    '"' + (r.party_name || '').replace(/"/g, '""') + '"',
                    r.in_qty,
                    r.out_qty,
                    r.running_balance,
                    '"' + (r.adj_mode || '').replace(/"/g, '""') + '"',
                    '"' + (r.note || '').replace(/"/g, '""') + '"'
                ]);
            });

            var csvContent = "data:text/csv;charset=utf-8," + csv.map(e => e.join(",")).join("\n");
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "Product_Ledger_" + (d.product.item_code || 'Stock') + "_" + new Date().toISOString().slice(0,10) + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

    });
</script>
@endsection
