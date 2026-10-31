@extends('admin_panel.layout.app')

@section('title', 'Recovery Report')

@section('content')
<style>
    /* Standardized Sale Report Pattern Styling */
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
        gap: 6px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .stat-pill {
        flex: 1 1 0px;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .stat-pill .stat-label {
        font-size: .60rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 1px;
    }
    .stat-pill .stat-val {
        font-size: .88rem;
        font-weight: 800;
        line-height: 1.2;
    }

    /* Mobile Cards Styling */
    .mob-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .mob-metric-card {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 10px 6px;
        text-align: center;
        height: 100%;
    }
    .mob-metric-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
    }
    .mob-metric-val {
        font-size: 14px;
        font-weight: 800;
    }

    /* Table Styling with Sticky Header */
    .sale-table-wrap {
        height: calc(100vh - 250px);
        max-height: calc(100vh - 250px);
        min-height: 380px;
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

    @media print {
        body { background: #ffffff !important; font-size: 11px; }
        .no-print, header, .sidebar, .navbar, footer { display: none !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; margin-bottom: 10px !important; }
    }
</style>

<div class="sale-report-container">

    {{-- DESKTOP FILTER HEADER CARD (d-none d-md-block Standard Pattern) --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="recoveryFormDesk">
                
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    
                    {{-- Left Title --}}
                    <div class="d-flex align-items-center me-3">
                        <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                            <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Customer Recovery Report
                        </span>
                    </div>

                    {{-- Mid Dates with Explicit Spacing Gap --}}
                    <div class="d-flex align-items-center me-auto flex-wrap" style="gap: 16px !important;">
                        <div class="d-flex align-items-center gap-1">
                            <label for="start_date_desk" class="sale-filter-label mb-0 ms-1 me-1">Start:</label>
                            <input type="date" name="start_date" id="start_date_desk" class="form-control form-control-sm fw-bold startDateInput" value="{{ date('Y-m-01') }}" style="height: 32px; width: 135px; font-size: .78rem; border-radius: 6px;">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <label for="end_date_desk" class="sale-filter-label mb-0 ms-2 me-1">End:</label>
                            <input type="date" name="end_date" id="end_date_desk" class="form-control form-control-sm fw-bold endDateInput" value="{{ date('Y-m-d') }}" style="height: 32px; width: 135px; font-size: .78rem; border-radius: 6px;">
                        </div>
                    </div>

                    {{-- Last Buttons with X-Axis Gap --}}
                    <div class="d-flex align-items-center ms-auto" style="gap: 10px !important;">
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold d-inline-flex align-items-center btnSearchTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                            <i class="fas fa-filter me-1"></i> Generate
                        </button>
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold text-secondary d-inline-flex align-items-center btnResetTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold d-inline-flex align-items-center btnPrintReport" style="height: 32px; border-radius: 6px; font-size: .78rem;">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- MOBILE FILTER HEADER CARD (d-md-none With Top Margin) --}}
    <div class="card border-0 shadow-sm mb-3 no-print d-md-none mt-2" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form id="recoveryFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Customer Recovery
                        </span>
                    </div>

                    {{-- 1. Start & End Date --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Start Date</label>
                        <input type="date" name="start_date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="{{ date('Y-m-01') }}" style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">End Date</label>
                        <input type="date" name="end_date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>

                    {{-- 2. Full Width Generate Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Generate Report
                        </button>
                    </div>

                    {{-- 3. Centralized Reset & Print Actions With Horizontal Gap --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-center gap-2 pt-1" style="gap: 10px !important;">
                            <button type="button" class="btn btn-light border btn-sm flex-fill fw-bold text-secondary btnResetTrigger" style="font-size: 11px; margin-right: 8px !important;">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill fw-bold btnPrintReport" style="font-size: 11px;">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DESKTOP SUMMARY METRIC PILL BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-2 d-none d-md-block" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body p-2">
            <div class="summary-pill-bar">
                
                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-muted">Total Opening</div>
                    <div class="stat-val text-dark" id="pillOpening">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info">Total Sales</div>
                    <div class="stat-val text-primary" id="pillSales">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">Cash Received</div>
                    <div class="stat-val text-success" id="pillReceived">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Total Final Balance</div>
                    <div class="stat-val text-danger" id="pillFinal">Rs 0.00</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-wallet text-muted me-1"></i>Opening</span>
                <div class="mob-metric-val text-dark mt-1" id="mobPillOpening">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-shopping-cart text-primary me-1"></i>Sales</span>
                <div class="mob-metric-val text-primary mt-1" id="mobPillSales">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-money-bill-wave text-success me-1"></i>Cash Received</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillReceived">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-balance-scale text-danger me-1"></i>Final Balance</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillFinal">Rs 0.00</div>
            </div>
        </div>
    </div>

    {{-- Loader --}}
    <div id="loader" style="display:none; text-align:center; padding: 20px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="small text-muted mt-2">Fetching Recovery Data…</div>
    </div>

    <div id="reportBox">
        
        {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
        <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none d-md-block">
            <div class="card-body p-0">
                <div class="sale-table-wrap">
                    <table class="table table-bordered table-hover align-middle mb-0 report-table" id="recoveryTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th>PARTY / CUSTOMER NAME</th>
                                <th class="text-end" style="width: 140px;">OPENING</th>
                                <th class="text-end" style="width: 140px;">SALES</th>
                                <th class="text-end" style="width: 150px;">CASH RECEIVED</th>
                                <th class="text-end" style="width: 160px;">FINAL BALANCE</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRows"></tbody>
                        <tfoot id="tfootTotals" class="bg-light fw-bold" style="font-size: 12px;"></tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- MOBILE CARDS CONTAINER (d-md-none) --}}
        <div class="d-md-none" id="mobRecoveryContainer">
        </div>

    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        
        // Sync Inputs between Desktop & Mobile
        $('.startDateInput').on('change', function() { $('.startDateInput').val($(this).val()); });
        $('.endDateInput').on('change', function() { $('.endDateInput').val($(this).val()); });

        function fmt(amount) {
            let val = parseFloat(amount || 0);
            return 'Rs ' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function loadReport() {
            let start = $(".startDateInput").val() || '2000-01-01';
            let end = $(".endDateInput").val() || '{{ date("Y-m-d") }}';
            
            $("#loader").show();
            $("#reportBox").hide();

            $.get("{{ route('report.recovery.fetch') }}", { start_date: start, end_date: end }, function(res) {
                $("#loader").hide();
                $("#reportBox").show();

                let rowsHtml = "";
                let mobHtml = "";

                if (res.rows && res.rows.length > 0) {
                    res.rows.forEach(function(row) {
                        rowsHtml += `
                            <tr>
                                <td class="text-center text-muted fw-bold">${row.sr}</td>
                                <td class="fw-semibold text-dark">${row.party}</td>
                                <td class="text-end">${row.opening != 0 ? fmt(row.opening) : '-'}</td>
                                <td class="text-end text-primary fw-semibold">${row.sales != 0 ? fmt(row.sales) : '-'}</td>
                                <td class="text-end text-success fw-semibold">${row.received != 0 ? fmt(row.received) : '-'}</td>
                                <td class="text-end fw-bold ${row.final >= 0 ? 'text-dark' : 'text-danger'}">${fmt(row.final)}</td>
                            </tr>
                        `;

                        mobHtml += `
                            <div class="mob-card p-2.5 p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-light text-muted border" style="font-size: 10px;">#${row.sr}</span>
                                        <strong class="text-dark" style="font-size: 12.5px;">${row.party}</strong>
                                    </div>
                                    <strong class="fw-bold ${row.final >= 0 ? 'text-dark' : 'text-danger'}" style="font-size: 12px;">Final: ${fmt(row.final)}</strong>
                                </div>
                                <div class="border-top pt-2 mt-1">
                                    <div class="row g-1 text-center" style="font-size: 11px;">
                                        <div class="col-4 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Opening</span>
                                            <strong class="text-dark">${row.opening != 0 ? fmt(row.opening) : '-'}</strong>
                                        </div>
                                        <div class="col-4 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Sales</span>
                                            <strong class="text-primary">${row.sales != 0 ? fmt(row.sales) : '-'}</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-muted d-block" style="font-size: 10px;">Received</span>
                                            <strong class="text-success">${row.received != 0 ? fmt(row.received) : '-'}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    $("#tbodyRows").html(rowsHtml);
                    $("#mobRecoveryContainer").html(mobHtml);

                    // Totals
                    if (res.totals) {
                        let t = res.totals;
                        let totalsHtml = `
                            <tr>
                                <td colspan="2" class="text-end text-dark">TOTALS:</td>
                                <td class="text-end text-dark">${fmt(t.opening)}</td>
                                <td class="text-end text-primary">${fmt(t.sales)}</td>
                                <td class="text-end text-success">${fmt(t.received)}</td>
                                <td class="text-end text-dark">${fmt(t.final)}</td>
                            </tr>
                        `;
                        $("#tfootTotals").html(totalsHtml);

                        // Update Stat Pills (Desktop & Mobile)
                        $('#pillOpening, #mobPillOpening').text(fmt(t.opening));
                        $('#pillSales, #mobPillSales').text(fmt(t.sales));
                        $('#pillReceived, #mobPillReceived').text(fmt(t.received));
                        $('#pillFinal, #mobPillFinal').text(fmt(t.final));
                    }
                } else {
                    $("#tbodyRows").html(`<tr><td colspan="6" class="text-center text-muted py-4">No data found in this period.</td></tr>`);
                    $("#mobRecoveryContainer").html('<div class="card border-0 shadow-sm rounded-3 text-center py-4 bg-white"><div class="card-body py-4 text-muted"><i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i><p class="small fw-bold mb-0">No Recovery Data Found</p></div></div>');
                    $("#tfootTotals").empty();

                    $('#pillOpening, #mobPillOpening').text('Rs 0.00');
                    $('#pillSales, #mobPillSales').text('Rs 0.00');
                    $('#pillReceived, #mobPillReceived').text('Rs 0.00');
                    $('#pillFinal, #mobPillFinal').text('Rs 0.00');
                }
            });
        }

        $(".btnSearchTrigger").click(function() {
            loadReport();
        });

        $(".btnResetTrigger").click(function() {
            $(".startDateInput").val('{{ date("Y-m-01") }}');
            $(".endDateInput").val('{{ date("Y-m-d") }}');
            loadReport();
        });

        $(".btnPrintReport").click(function() {
            document.title = 'Recovery Report';
            window.print();
        });

        window.addEventListener('beforeprint', function() {
            document.title = 'Recovery Report';
        });

        // Load initially
        loadReport();
    });
</script>
@endsection
