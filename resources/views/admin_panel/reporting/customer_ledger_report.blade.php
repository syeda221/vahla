@extends('admin_panel.layout.app')

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

    /* Table Styling with Sticky Header - Statement Accounting Theme */
    .sale-table-wrap {
        height: calc(100vh - 250px);
        max-height: calc(100vh - 250px);
        min-height: 400px;
        overflow-y: auto;
        border: 1px solid #000000;
        border-radius: 4px;
        background: #ffffff;
    }
    .report-table {
        font-size: .80rem;
        margin-bottom: 0;
        color: #000000;
        border-collapse: collapse;
    }
    .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #000000 !important;
        color: #ffffff !important;
        font-size: .78rem;
        font-weight: 700;
        padding: 9px 10px;
        border: 1px solid #222222;
        white-space: nowrap;
        letter-spacing: 0.2px;
    }
    .report-table tbody td {
        padding: 7px 10px;
        border: 1px solid #e5e7eb;
        color: #000000;
        font-size: .80rem;
    }
    .report-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .balance-text {
        font-weight: 700;
        color: #000000;
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
            <form id="ledgerFormDesk">
                
                {{-- Top Section: Left Title, Mid Dates with Gap, Last Buttons --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2 pb-2 border-bottom">
                    
                    {{-- Left Title --}}
                    <div class="d-flex align-items-center me-3">
                        <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Customer Ledger Report
                        </span>
                    </div>

                    {{-- Mid Dates with Explicit Spacing Gap --}}
                    <div class="d-flex align-items-center me-auto flex-wrap" style="gap: 16px !important;">
                        <div class="d-flex align-items-center gap-1">
                            <label for="start_date_desk" class="sale-filter-label mb-0 ms-1 me-1">Start:</label>
                            <input type="date" name="start_date" id="start_date_desk" class="form-control form-control-sm fw-bold startDateInput" value="2000-01-01" style="height: 32px; width: 135px; font-size: .78rem; border-radius: 6px;">
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

                {{-- Bottom Section: Zone, Customer, Quick Filter --}}
                <div class="row g-2">
                    <div class="col-md-4">
                        <label for="zone_id_desk" class="sale-filter-label mb-1">Zone:</label>
                        <select name="zone_id" id="zone_id_desk" class="form-select form-select-sm select2 zoneSelect">
                            <option value="">-- All Zones --</option>
                            @foreach ($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->zone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="customer_id_desk" class="sale-filter-label mb-1">Customer:</label>
                        <select name="customer_id" id="customer_id_desk" class="form-select form-select-sm select2 customerSelect">
                            <option value="all" data-zone="">-- All Customers --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" data-zone="{{ $c->zone }}">{{ $c->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="quick_filter_desk" class="sale-filter-label mb-1 d-block">Quick Filter:</label>
                        <select id="quick_filter_desk" class="form-control form-control-sm quickFilterSelect" style="height: 34px !important; font-size: .78rem; border-radius: 6px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important;">
                            <option value="custom">Custom Range</option>
                            <option value="daily">Daily (Today)</option>
                            <option value="weekly">Weekly (This Week)</option>
                            <option value="monthly">Monthly (This Month)</option>
                            <option value="yearly">Yearly (This Year)</option>
                        </select>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- MOBILE FILTER HEADER CARD (d-md-none With Top Margin to Prevent Navbar Overlap) --}}
    <div class="card border-0 shadow-sm mb-3 no-print d-md-none mt-2" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form id="ledgerFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Customer Ledger
                        </span>
                    </div>

                    {{-- 1. Zone --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Zone</label>
                        <select name="zone_id" class="form-select form-select-sm select2 zoneSelect">
                            <option value="">-- All Zones --</option>
                            @foreach ($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Customer --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm select2 customerSelect">
                            <option value="all" data-zone="">-- All Customers --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" data-zone="{{ $c->zone }}">{{ $c->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Quick Filter --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Quick Filter</label>
                        <select class="form-control form-control-sm quickFilterSelect" style="font-size: 11px; height: 34px !important; border-radius: 6px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important;">
                            <option value="custom">Custom Range</option>
                            <option value="daily">Daily (Today)</option>
                            <option value="weekly">Weekly (This Week)</option>
                            <option value="monthly">Monthly (This Month)</option>
                            <option value="yearly">Yearly (This Year)</option>
                        </select>
                    </div>

                    {{-- 4. Start & End Date --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Start Date</label>
                        <input type="date" name="start_date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="2000-01-01" style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">End Date</label>
                        <input type="date" name="end_date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>

                    {{-- 5. Full Width Generate Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Generate Report
                        </button>
                    </div>

                    {{-- 6. Centralized Reset & Print Actions With Horizontal Gap --}}
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
                    <div class="stat-label text-muted">Opening Balance</div>
                    <div class="stat-val text-dark" id="pillOpeningBalance">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Total Debit (Dr)</div>
                    <div class="stat-val text-danger" id="pillTotalDebit">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">Total Credit (Cr)</div>
                    <div class="stat-val text-success" id="pillTotalCredit">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info">Net Closing Balance</div>
                    <div class="stat-val text-primary" id="pillClosingBalance">Rs 0.00</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-wallet text-muted me-1"></i>Opening</span>
                <div class="mob-metric-val text-dark mt-1" id="mobPillOpeningBalance">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-up text-danger me-1"></i>Debit (Dr)</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillTotalDebit">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-down text-success me-1"></i>Credit (Cr)</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillTotalCredit">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-balance-scale text-primary me-1"></i>Closing</span>
                <div class="mob-metric-val text-primary mt-1" id="mobPillClosingBalance">Rs 0.00</div>
            </div>
        </div>
    </div>

    {{-- Report Content Box --}}
    <div id="loader" style="display:none; text-align:center; padding: 20px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="small text-muted mt-2">Generating Customer Ledger Report…</div>
    </div>

    <div id="ledgerBox" style="display:none;">
        
        {{-- Report Sub-Header --}}
        <div class="card border-0 shadow-sm mb-2 rounded-3 bg-white">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2" id="ledgerHeader">
            </div>
        </div>

        {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
        <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none d-md-block">
            <div class="card-body p-0">
                <div class="sale-table-wrap">
                    <table class="table table-bordered table-hover align-middle mb-0 report-table">
                        <thead>
                            <tr>
                                <th style="width: 9%;" class="text-center">Date</th>
                                <th style="width: 12%;">Details</th>
                                <th style="width: 14%;">Bank Name</th>
                                <th style="width: 18%;">Ref No.</th>
                                <th style="width: 9%;" class="text-center">V No.</th>
                                <th style="width: 8%;" class="text-center">Quantity</th>
                                <th style="width: 10%;" class="text-end">Debit</th>
                                <th style="width: 10%;" class="text-end">Credit</th>
                                <th style="width: 10%;" class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody id="ledgerBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MOBILE CARDS CONTAINER (d-md-none) --}}
        <div class="d-md-none" id="ledgerMobileContainer">
        </div>

    </div>

</div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if ($('.select2').length > 0) {
                $('.select2').select2({ width: '100%' });
            }

            let allCustomers = $('.customerSelect option').clone();

            // Sync Inputs between Desktop & Mobile
            $('.startDateInput').on('change', function() { $('.startDateInput').val($(this).val()); });
            $('.endDateInput').on('change', function() { $('.endDateInput').val($(this).val()); });
            $('.zoneSelect').on('change', function() {
                let selectedZone = $(this).val();
                $('.zoneSelect').val(selectedZone);

                $('.customerSelect').empty();
                allCustomers.each(function() {
                    let optionZone = $(this).attr('data-zone');
                    if ($(this).val() === 'all' || selectedZone === "" || optionZone == selectedZone) {
                        $('.customerSelect').append($(this).clone());
                    }
                });
                $('.customerSelect').val('all').trigger('change.select2');
            });
            $('.customerSelect').on('change', function() { $('.customerSelect').val($(this).val()); });
            $('.quickFilterSelect').on('change', function() {
                let val = $(this).val();
                $('.quickFilterSelect').val(val);
                let today = new Date();
                let start = new Date();
                let end = new Date();

                if (val === 'daily') {
                } else if (val === 'weekly') {
                    let day = today.getDay();
                    let diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    start.setDate(diff);
                } else if (val === 'monthly') {
                    start.setDate(1);
                } else if (val === 'yearly') {
                    start.setMonth(0, 1);
                } else if (val === 'custom') {
                    return;
                }

                let formatDate = (d) => {
                    let m = '' + (d.getMonth() + 1), day = '' + d.getDate(), y = d.getFullYear();
                    if (m.length < 2) m = '0' + m;
                    if (day.length < 2) day = '0' + day;
                    return [y, m, day].join('-');
                };

                $('.startDateInput').val(formatDate(start));
                $('.endDateInput').val(formatDate(end));
                loadLedger();
            });

            // Auto-load ledger on page load
            loadLedger();

            $(document).on('click', '.btnSearchTrigger', function() {
                loadLedger();
            });

            $('.btnResetTrigger').on('click', function() {
                $('.startDateInput').val('2000-01-01');
                $('.endDateInput').val('{{ date("Y-m-d") }}');
                $('.zoneSelect').val('').trigger('change');
                $('.customerSelect').val('all').trigger('change');
                $('.quickFilterSelect').val('custom');
                loadLedger();
            });

            $('.btnPrintReport').on('click', () => window.print());

            function loadLedger() {
                let zid = $(".zoneSelect").val();
                let cid = $(".customerSelect").val();
                let start = $(".startDateInput").val() || '2000-01-01';
                let end = $(".endDateInput").val() || '{{ date("Y-m-d") }}';

                $("#loader").show();
                $("#ledgerBox").hide();

                $.get("{{ route('report.customer.ledger.fetch') }}", {
                    zone_id: zid,
                    customer_id: cid || 'all',
                    start_date: start,
                    end_date: end
                }, function(res) {
                    $("#loader").hide();
                    $("#ledgerBox").show();

                    let displayStart = formatDisplayDate(start);
                    let displayEnd = formatDisplayDate(end);

                    // Build Header
                    $("#ledgerHeader").html(`
                        <div>
                            <h6 class="fw-bold text-dark mb-0">${res.customer.customer_name}</h6>
                            <small class="text-muted">Period: <strong>${displayStart}</strong> to <strong>${displayEnd}</strong></small>
                        </div>
                        <div>
                             <span class="badge bg-primary text-white p-2 shadow-sm font-monospace">Customer Ledger</span>
                        </div>
                    `);

                    let totalDebit = 0;
                    let totalCredit = 0;
                    let totalQty = 0;
                    let lastBalance = parseFloat(res.opening_balance);

                    // Update Top Metrics (Desktop & Mobile)
                    let formattedOpening = 'Rs ' + lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    $('#pillOpeningBalance, #mobPillOpeningBalance').text(formattedOpening);

                    // Desktop Opening Row
                    let html = `
                        <tr class="bg-light fw-bold">
                            <td class="text-center">-</td>
                            <td>Opening Balance</td>
                            <td class="text-center">-</td>
                            <td>Opening Balance (B/F)</td>
                            <td class="text-center">-</td>
                            <td class="text-center">0</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end text-dark">
                                ${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </td>
                        </tr>
                    `;

                    let mobHtml = `
                        <div class="mob-card p-2.5 p-2 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="text-dark" style="font-size: 12.5px;">Opening Balance (B/F)</strong>
                                <strong class="text-dark" style="font-size: 13px;">Rs ${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                            </div>
                        </div>
                    `;

                    res.transactions.forEach((t, i) => {
                        let debit = t.debit && t.debit > 0 ? parseFloat(t.debit) : 0;
                        let credit = t.credit && t.credit > 0 ? parseFloat(t.credit) : 0;
                        let qty = t.quantity ? parseFloat(t.quantity) : 0;
                        totalDebit += debit;
                        totalCredit += credit;
                        totalQty += qty;
                        lastBalance = parseFloat(t.balance);

                        let balLabel = lastBalance >= 0 ? 'Dr' : 'Cr';
                        let balClass = lastBalance >= 0 ? 'balance-positive' : 'balance-negative';
                        let custName = t.customer_name || '-';

                        // Desktop Row
                        html += `
                            <tr>
                                <td class="text-center small text-nowrap">${t.date}</td>
                                <td><span class="fw-semibold text-dark">${t.details || '-'}</span></td>
                                <td class="small text-dark">${t.bank_name && t.bank_name !== '-' ? t.bank_name : ''}</td>
                                <td class="small text-break text-dark">${t.ref_no || ''}</td>
                                <td class="text-center font-monospace fw-semibold text-dark">${t.v_no && t.v_no !== '-' ? t.v_no : (t.invoice && t.invoice !== '-' ? t.invoice : '')}</td>
                                <td class="text-center fw-semibold text-dark">${qty !== 0 ? qty.toLocaleString() : '0'}</td>
                                <td class="text-end text-dark">${debit > 0 ? debit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : ''}</td>
                                <td class="text-end text-dark">${credit > 0 ? credit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : ''}</td>
                                <td class="text-end fw-bold text-dark">
                                    ${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                </td>
                            </tr>
                        `;

                        // Mobile Card
                        mobHtml += `
                            <div class="mob-card p-2.5 p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-light text-dark border font-monospace">${t.v_no || t.invoice || 'REF'}</span>
                                    <small class="text-muted" style="font-size: 10.5px;">${t.date}</small>
                                </div>
                                <div class="mb-1">
                                    <strong class="text-dark d-block" style="font-size: 12.5px;">${t.details || 'Transaction'} ${t.bank_name && t.bank_name !== '-' ? '('+t.bank_name+')' : ''}</strong>
                                    <small class="text-muted d-block" style="font-size: 11px;">${t.ref_no || '-'}</small>
                                </div>
                                <div class="border-top pt-2 mt-1">
                                    <div class="row g-1 text-center" style="font-size: 11px;">
                                        <div class="col-3 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Qty</span>
                                            <strong class="text-dark">${qty !== 0 ? qty.toLocaleString() : '0'}</strong>
                                        </div>
                                        <div class="col-3 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Debit</span>
                                            <strong class="text-dark">${debit > 0 ? debit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</strong>
                                        </div>
                                        <div class="col-3 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Credit</span>
                                            <strong class="text-dark">${credit > 0 ? credit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</strong>
                                        </div>
                                        <div class="col-3">
                                            <span class="text-muted d-block" style="font-size: 10px;">Balance</span>
                                            <strong class="text-dark">${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2})}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Totals Row (Matching Reference Statement)
                    html += `
                        <tr class="fw-bold bg-white" style="border-top: 2px solid #000000 !important; border-bottom: 2px solid #000000 !important;">
                            <td colspan="5" class="text-end fw-bold text-dark"></td>
                            <td class="text-center fw-bold text-dark">${totalQty.toLocaleString()}</td>
                            <td class="text-end fw-bold text-dark">${totalDebit > 0 ? totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : ''}</td>
                            <td class="text-end fw-bold text-dark">${totalCredit > 0 ? totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : ''}</td>
                            <td class="text-end fw-bold text-dark">${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        </tr>
                    `;

                    $("#ledgerBody").html(html);
                    $("#ledgerMobileContainer").html(mobHtml);

                    // Update Top Summary Pills (Desktop & Mobile)
                    let formattedDebit   = 'Rs ' + totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    let formattedCredit  = 'Rs ' + totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    let formattedClosing = 'Rs ' + Math.abs(lastBalance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + (lastBalance >= 0 ? 'Dr' : 'Cr');

                    $('#pillTotalDebit, #mobPillTotalDebit').text(formattedDebit);
                    $('#pillTotalCredit, #mobPillTotalCredit').text(formattedCredit);
                    $('#pillClosingBalance, #mobPillClosingBalance').text(formattedClosing);
                }).fail(function() {
                    $("#loader").hide();
                    alert("Error loading report data.");
                });
            }

            function formatDisplayDate(dateStr) {
                if (!dateStr) return '-';
                let d = new Date(dateStr);
                let months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                return d.getDate().toString().padStart(2, '0') + '-' + months[d.getMonth()] + '-' + d.getFullYear();
            }
        });
    </script>
@endsection
