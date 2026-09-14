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

    .balance-positive {
        color: #16a34a;
        font-weight: bold;
    }
    .balance-negative {
        color: #dc2626;
        font-weight: bold;
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
                            <i class="fas fa-user-tie text-primary me-2"></i>Agent Ledger Report
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

                {{-- Bottom Section: Agent, Quick Filter --}}
                <div class="row g-2">
                    <div class="col-md-7">
                        <label for="agent_id_desk" class="sale-filter-label mb-1">Agent:</label>
                        <select name="agent_id" id="agent_id_desk" class="form-select form-select-sm select2 agentSelect">
                            <option value="all">-- All Agents --</option>
                            @foreach ($agents as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
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
                            <i class="fas fa-user-tie text-primary me-2"></i>Agent Ledger
                        </span>
                    </div>

                    {{-- 1. Agent --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Agent</label>
                        <select name="agent_id" class="form-select form-select-sm select2 agentSelect">
                            <option value="all">-- All Agents --</option>
                            @foreach ($agents as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Quick Filter --}}
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

                    {{-- 3. Start & End Date --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Start Date</label>
                        <input type="date" name="start_date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="2000-01-01" style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">End Date</label>
                        <input type="date" name="end_date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>

                    {{-- 4. Full Width Generate Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Generate Report
                        </button>
                    </div>

                    {{-- 5. Centralized Reset & Print Actions With Horizontal Gap --}}
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

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">Total Payment (Dr)</div>
                    <div class="stat-val text-success" id="pillTotalDebit">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Total Commission (Cr)</div>
                    <div class="stat-val text-danger" id="pillTotalCredit">Rs 0.00</div>
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
                <span class="mob-metric-label"><i class="fas fa-arrow-up text-success me-1"></i>Payment (Dr)</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillTotalDebit">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-down text-danger me-1"></i>Commission (Cr)</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillTotalCredit">Rs 0.00</div>
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
        <div class="small text-muted mt-2">Generating Agent Ledger Report…</div>
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
                                <th style="width: 10%;">Date</th>
                                <th style="width: 12%;">Ref / Invoice</th>
                                <th style="width: 18%;">Agent</th>
                                <th>Description</th>
                                <th style="width: 12%;">Payment (Dr)</th>
                                <th style="width: 12%;">Commission (Cr)</th>
                                <th style="width: 14%;">Balance</th>
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

            // Sync Inputs between Desktop & Mobile
            $('.startDateInput').on('change', function() { $('.startDateInput').val($(this).val()); });
            $('.endDateInput').on('change', function() { $('.endDateInput').val($(this).val()); });
            $('.agentSelect').on('change', function() { $('.agentSelect').val($(this).val()); });
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
                $('.agentSelect').val('all').trigger('change');
                $('.quickFilterSelect').val('custom');
                loadLedger();
            });

            $('.btnPrintReport').on('click', () => window.print());

            function loadLedger() {
                let aid = $(".agentSelect").val();
                let start = $(".startDateInput").val() || '2000-01-01';
                let end = $(".endDateInput").val() || '{{ date("Y-m-d") }}';

                $("#loader").show();
                $("#ledgerBox").hide();

                $.get("{{ route('report.agent.ledger.fetch') }}", {
                    agent_id: aid || 'all',
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
                            <h6 class="fw-bold text-dark mb-0">${res.agent.name}</h6>
                            <small class="text-muted">Period: <strong>${displayStart}</strong> to <strong>${displayEnd}</strong></small>
                        </div>
                        <div>
                             <span class="badge bg-primary text-white p-2 shadow-sm font-monospace">Statement of Account</span>
                        </div>
                    `);

                    let totalPayment = 0;
                    let totalCommission = 0;
                    let lastBalance = parseFloat(res.opening_balance);

                    // Update Top Metrics (Desktop & Mobile)
                    let formattedOpening = 'Rs ' + lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    $('#pillOpeningBalance, #mobPillOpeningBalance').text(formattedOpening);

                    // Desktop Opening Row
                    let html = `
                        <tr class="bg-light fw-bold">
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-start">Opening Balance (B/F)</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end text-dark">
                                Rs ${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </td>
                        </tr>
                    `;

                    let mobHtml = `
                        <div class="mob-card p-2.5 p-2 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="text-dark" style="font-size: 12.5px;">Opening Balance (B/F)</strong>
                                <strong class="text-dark" style="font-size: 13px;">Rs ${lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2})}</strong>
                            </div>
                        </div>
                    `;

                    res.transactions.forEach((t, i) => {
                        let payment = t.debit && t.debit > 0 ? parseFloat(t.debit) : 0;
                        let commission = t.credit && t.credit > 0 ? parseFloat(t.credit) : 0;
                        totalPayment += payment;
                        totalCommission += commission;
                        lastBalance = parseFloat(t.balance);

                        let balLabel = lastBalance >= 0 ? 'Cr' : 'Dr';
                        let balClass = lastBalance >= 0 ? 'balance-negative' : 'balance-positive';
                        let agentName = t.agent_name || res.agent.name || '-';

                        // Desktop Row
                        html += `
                            <tr>
                                <td class="text-center small text-nowrap">${t.date}</td>
                                <td class="text-center"><span class="badge bg-light text-primary border font-monospace">${t.invoice ?? '-'}</span></td>
                                <td class="fw-bold text-dark">${agentName}</td>
                                <td class="text-start">${t.description}${t.reference && t.reference !== '-' ? ' <small class="text-muted">(' + t.reference + ')</small>' : ''}</td>
                                <td class="text-end text-success fw-semibold">${payment > 0 ? 'Rs ' + payment.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</td>
                                <td class="text-end text-danger fw-semibold">${commission > 0 ? 'Rs ' + commission.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</td>
                                <td class="text-end fw-bold ${balClass}">
                                    Rs ${Math.abs(lastBalance).toLocaleString(undefined, {minimumFractionDigits: 2})}
                                    <small class="text-muted" style="font-size:0.75em">${balLabel}</small>
                                </td>
                            </tr>
                        `;

                        // Mobile Card
                        mobHtml += `
                            <div class="mob-card p-2.5 p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-light text-primary border font-monospace">${t.invoice ?? 'REF'}</span>
                                    <small class="text-muted" style="font-size: 10.5px;">${t.date}</small>
                                </div>
                                <div class="mb-1">
                                    <strong class="text-dark d-block" style="font-size: 12.5px;">${agentName}</strong>
                                    <small class="text-muted d-block" style="font-size: 11px;">${t.description}${t.reference && t.reference !== '-' ? ' (' + t.reference + ')' : ''}</small>
                                </div>
                                <div class="border-top pt-2 mt-1">
                                    <div class="row g-1 text-center" style="font-size: 11px;">
                                        <div class="col-4 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Payment (Dr)</span>
                                            <strong class="text-success">${payment > 0 ? 'Rs ' + payment.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</strong>
                                        </div>
                                        <div class="col-4 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Comm. (Cr)</span>
                                            <strong class="text-danger">${commission > 0 ? 'Rs ' + commission.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</strong>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-muted d-block" style="font-size: 10px;">Balance</span>
                                            <strong class="${balClass}">Rs ${Math.abs(lastBalance).toLocaleString(undefined, {minimumFractionDigits: 2})} <small>${balLabel}</small></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Totals Row
                    html += `
                        <tr class="fw-bold bg-light">
                            <td colspan="4" class="text-end text-dark">Totals:</td>
                            <td class="text-end text-success">Rs ${totalPayment.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td class="text-end text-danger">Rs ${totalCommission.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td class="text-end ${lastBalance >= 0 ? 'balance-negative' : 'balance-positive'}">Rs ${Math.abs(lastBalance).toLocaleString(undefined, {minimumFractionDigits: 2})} ${lastBalance >= 0 ? 'Cr' : 'Dr'}</td>
                        </tr>
                    `;

                    $("#ledgerBody").html(html);
                    $("#ledgerMobileContainer").html(mobHtml);

                    // Update Top Summary Pills (Desktop & Mobile)
                    let formattedDebit   = 'Rs ' + totalPayment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    let formattedCredit  = 'Rs ' + totalCommission.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    let formattedClosing = 'Rs ' + Math.abs(lastBalance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + (lastBalance >= 0 ? 'Cr' : 'Dr');

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