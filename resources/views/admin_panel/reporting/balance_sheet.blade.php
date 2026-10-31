@extends('admin_panel.layout.app')

@section('title', 'Balance Sheet')

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
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .stat-pill {
        flex: 1 1 0px;
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .stat-pill .stat-label {
        font-size: .62rem;
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

    /* Statement Card Styling */
    .statement-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .statement-header {
        padding: 12px 16px;
        background: #1e293b;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .statement-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: .92rem;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        color: #ffffff !important;
    }
    .statement-header .badge {
        font-size: 10px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .header-badge-assets {
        background: rgba(56, 189, 248, 0.18) !important;
        color: #38bdf8 !important;
        border: 1px solid rgba(56, 189, 248, 0.4) !important;
    }
    .header-badge-liab {
        background: rgba(251, 113, 133, 0.18) !important;
        color: #fb7185 !important;
        border: 1px solid rgba(251, 113, 133, 0.4) !important;
    }

    .statement-body {
        padding: 14px;
        flex-grow: 1;
    }

    /* Table inside Balance Sheet */
    .bs-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 0.82rem;
        border-collapse: separate;
        border-spacing: 0;
    }
    .bs-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .bs-section-title {
        font-weight: 800;
        font-size: 0.78rem;
        text-transform: uppercase;
        color: #334155;
        background: #f8fafc;
        padding: 6px 10px !important;
        border-radius: 6px;
        margin-top: 10px;
        margin-bottom: 4px;
    }
    .bs-sub-row {
        padding-left: 20px !important;
        color: #475569;
    }
    .bs-amount {
        text-align: right;
        font-weight: 700;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.86rem;
    }
    .bs-subtotal-row {
        font-weight: 700;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0 !important;
        border-bottom: 1px dashed #cbd5e1 !important;
    }
    .bs-subtotal-row td {
        padding: 8px 10px !important;
    }

    .statement-footer-banner {
        padding: 12px 16px;
        background: #1e293b;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 800;
        font-size: 0.95rem;
    }

    /* Mobile Responsive Card Layout */
    .mob-metric-card {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 8px 6px;
        text-align: center;
        height: 100%;
    }
    .mob-metric-label {
        font-size: 10px;
        color: #64748b;
        font-weight: 600;
    }
    .mob-metric-val {
        font-size: 12.5px;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media print {
        body { background: #ffffff !important; font-size: 11px; }
        .no-print, header, .sidebar, .navbar, footer { display: none !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; }
        .statement-card { border: 1px solid #cbd5e1 !important; box-shadow: none !important; margin-bottom: 15px !important; }
        .statement-header, .statement-footer-banner { background: #0f172a !important; color: #fff !important; -webkit-print-color-adjust: exact; }
    }
</style>

<div class="sale-report-container">

    {{-- DESKTOP FILTER HEADER CARD (d-none d-md-block Standard Pattern) --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="bsFormDesk">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    
                    {{-- Left Title --}}
                    <div class="d-flex align-items-center me-3">
                        <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                            <i class="fa-solid fa-scale-balanced text-primary me-2"></i>Balance Sheet Statement
                        </span>
                    </div>

                    {{-- Mid Date Input --}}
                    <div class="d-flex align-items-center me-auto flex-wrap" style="gap: 16px !important;">
                        <div class="d-flex align-items-center gap-1">
                            <label for="bs_date_desk" class="sale-filter-label mb-0 ms-1 me-1">As-of Date:</label>
                            <input type="date" id="bs_date_desk" class="form-control form-control-sm fw-bold bsDateInput" value="{{ date('Y-m-d') }}" style="height: 32px; width: 145px; font-size: .78rem; border-radius: 6px;">
                        </div>
                    </div>

                    {{-- Right Action Buttons --}}
                    <div class="d-flex align-items-center ms-auto" style="gap: 10px !important;">
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold d-inline-flex align-items-center btnGenerateTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                            <i class="fas fa-arrows-rotate me-1"></i> Generate
                        </button>
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold text-secondary d-inline-flex align-items-center btnResetTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                            <i class="fas fa-undo me-1"></i> Today
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
            <form id="bsFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fa-solid fa-scale-balanced text-primary me-2"></i>Balance Sheet Statement
                        </span>
                    </div>

                    {{-- As-of Date --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">As-of Date</label>
                        <input type="date" id="bs_date_mob" class="form-control form-control-sm bsDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>

                    {{-- Full Width Generate Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnGenerateTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-arrows-rotate me-1"></i> Generate Statement
                        </button>
                    </div>

                    {{-- Reset & Print Actions --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-center gap-2 pt-1" style="gap: 10px !important;">
                            <button type="button" class="btn btn-light border btn-sm flex-fill fw-bold text-secondary btnResetTrigger" style="font-size: 11px; margin-right: 8px !important;">
                                <i class="fas fa-undo me-1"></i> Reset Today
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

    {{-- DESKTOP EXECUTIVE SUMMARY PILLS BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-3 d-none d-md-block" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body p-2">
            <div class="summary-pill-bar">
                
                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info"><i class="fa-solid fa-building-columns me-1"></i>Total Assets</div>
                    <div class="stat-val text-primary" id="pillAssets">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger"><i class="fa-solid fa-hand-holding-dollar me-1"></i>Total Liabilities</div>
                    <div class="stat-val text-danger" id="pillLiabilities">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success"><i class="fa-solid fa-chart-pie me-1"></i>Owner's Equity</div>
                    <div class="stat-val text-success" id="pillEquity">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-secondary"><i class="fa-solid fa-check-double me-1"></i>Balance Status</div>
                    <div class="stat-val text-dark" id="pillStatus" style="font-size: .80rem;"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i>Balanced</span></div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #f0f9ff; border-color: #bae6fd;">
                <span class="mob-metric-label text-info"><i class="fas fa-building-columns text-primary me-1"></i>Total Assets</span>
                <div class="mob-metric-val text-primary mt-1" id="mobPillAssets">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #fef2f2; border-color: #fca5a5;">
                <span class="mob-metric-label text-danger"><i class="fas fa-hand-holding-dollar text-danger me-1"></i>Total Liabilities</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillLiabilities">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #f0fdf4; border-color: #86efac;">
                <span class="mob-metric-label text-success"><i class="fas fa-chart-pie text-success me-1"></i>Owner's Equity</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillEquity">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #f8fafc; border-color: #cbd5e1;">
                <span class="mob-metric-label text-secondary"><i class="fas fa-check-double text-secondary me-1"></i>Balance Status</span>
                <div class="mob-metric-val text-dark mt-1" id="mobPillStatus"><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size:10px;"><i class="fas fa-check-circle me-1"></i>Balanced</span></div>
            </div>
        </div>
    </div>

    {{-- Loader --}}
    <div id="loader" style="display:none; text-align:center; padding: 30px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="small text-muted mt-2">Computing Financial Balance Sheet…</div>
    </div>

    {{-- MAIN STATEMENT CONTAINER --}}
    <div id="bsContent">
        
        {{-- Statement Date Subheader --}}
        <div class="d-flex align-items-center justify-content-between mb-2 px-1">
            <span class="fw-bold text-secondary small">
                <i class="far fa-calendar-alt text-primary me-1"></i><span id="reportDateBadge">As of {{ date('d M, Y') }}</span>
            </span>
            <span class="badge bg-white text-dark border px-2 py-1 fw-semibold small shadow-sm">
                Currency: PKR (Rs)
            </span>
        </div>

        {{-- 2-COLUMN DUAL FINANCIAL STATEMENT (Side-by-Side on Desktop, Stacked on Mobile) --}}
        <div class="row g-3">
            
            {{-- LEFT COLUMN: ASSETS --}}
            <div class="col-12 col-md-6">
                <div class="statement-card">
                    <div class="statement-header" style="border-bottom: 3px solid #0284c7;">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-coins fs-5 me-2" style="color: #38bdf8;"></i>
                            <h6 class="text-white mb-0 fw-bold">1. ASSETS</h6>
                        </div>
                        <span class="badge header-badge-assets">Resource Holdings</span>
                    </div>

                    <div class="statement-body">
                        
                        {{-- CURRENT ASSETS SECTION --}}
                        <div class="bs-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-wallet text-primary me-1"></i>Current Assets</span>
                            <span class="text-muted" style="font-size: 10px;">Liquid & Inventories</span>
                        </div>

                        <table class="bs-table">
                            <tbody id="cashBankRows">
                                {{-- Dynamically Loaded Cash & Bank Accounts --}}
                            </tbody>
                            <tbody>
                                <tr>
                                    <td class="bs-sub-row"><i class="fas fa-users-line text-info me-2"></i>Accounts Receivable</td>
                                    <td class="bs-amount text-primary" id="lblReceivables">Rs 0.00</td>
                                </tr>
                                <tr>
                                    <td class="bs-sub-row"><i class="fas fa-boxes-stacked text-warning me-2"></i>Inventory on Hand</td>
                                    <td class="bs-amount text-dark" id="lblInventory">Rs 0.00</td>
                                </tr>
                                <tr class="bs-subtotal-row">
                                    <td class="fw-bold text-dark">Total Current Assets</td>
                                    <td class="bs-amount text-primary fs-6" id="lblCurrentAssetsTotal">Rs 0.00</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- FIXED ASSETS SECTION --}}
                        <div class="bs-section-title d-flex justify-content-between align-items-center mt-3">
                            <span><i class="fas fa-building text-secondary me-1"></i>Fixed Assets</span>
                            <span class="text-muted" style="font-size: 10px;">Property & Equipment</span>
                        </div>

                        <table class="bs-table">
                            <tbody>
                                <tr>
                                    <td class="bs-sub-row text-muted italic" style="font-size: 11px;">Property, Plant & Equipment</td>
                                    <td class="bs-amount text-muted">Rs 0.00</td>
                                </tr>
                                <tr class="bs-subtotal-row">
                                    <td class="fw-bold text-dark">Total Fixed Assets</td>
                                    <td class="bs-amount text-secondary" id="lblFixedAssetsTotal">Rs 0.00</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    {{-- ASSETS GRAND TOTAL BANNER --}}
                    <div class="statement-footer-banner">
                        <span>TOTAL ASSETS</span>
                        <span class="text-info fs-5" id="lblAssetsGrandTotal">Rs 0.00</span>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: LIABILITIES & EQUITY --}}
            <div class="col-12 col-md-6">
                <div class="statement-card">
                    <div class="statement-header" style="border-bottom: 3px solid #e11d48;">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-scale-balanced fs-5 me-2" style="color: #fb7185;"></i>
                            <h6 class="text-white mb-0 fw-bold">2. LIABILITIES & EQUITY</h6>
                        </div>
                        <span class="badge header-badge-liab">Claims & Ownership</span>
                    </div>

                    <div class="statement-body">
                        
                        {{-- CURRENT LIABILITIES SECTION --}}
                        <div class="bs-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-invoice-dollar text-danger me-1"></i>Current Liabilities</span>
                            <span class="text-muted" style="font-size: 10px;">Short-Term Payables</span>
                        </div>

                        <table class="bs-table">
                            <tbody>
                                <tr>
                                    <td class="bs-sub-row"><i class="fas fa-truck-field text-danger me-2"></i>Accounts Payable (Vendors)</td>
                                    <td class="bs-amount text-danger" id="lblPayables">Rs 0.00</td>
                                </tr>
                                <tr class="bs-subtotal-row">
                                    <td class="fw-bold text-dark">Total Current Liabilities</td>
                                    <td class="bs-amount text-danger fs-6" id="lblLiabTotal">Rs 0.00</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- OWNER'S EQUITY SECTION --}}
                        <div class="bs-section-title d-flex justify-content-between align-items-center mt-3">
                            <span><i class="fas fa-chart-pie text-success me-1"></i>Owner's Equity</span>
                            <span class="text-muted" style="font-size: 10px;">Net Capital Base</span>
                        </div>

                        <table class="bs-table">
                            <tbody>
                                <tr>
                                    <td class="bs-sub-row"><i class="fas fa-user-shield text-success me-2"></i>Owner's Capital & Retained Earnings</td>
                                    <td class="bs-amount text-success" id="lblEquity">Rs 0.00</td>
                                </tr>
                                <tr class="bs-subtotal-row">
                                    <td class="fw-bold text-dark">Total Owner's Equity</td>
                                    <td class="bs-amount text-success fs-6" id="lblEquityTotal">Rs 0.00</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    {{-- LIABILITIES & EQUITY GRAND TOTAL BANNER --}}
                    <div class="statement-footer-banner">
                        <span>TOTAL LIABILITIES & EQUITY</span>
                        <span class="text-success fs-5" id="lblLiabEquityGrandTotal">Rs 0.00</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        
        // Sync Date Inputs between Desktop & Mobile
        $('.bsDateInput').on('change', function() {
            $('.bsDateInput').val($(this).val());
        });

        function fmt(n) {
            let val = parseFloat(n || 0);
            return 'Rs ' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function loadData() {
            let date = $(".bsDateInput").val() || '{{ date("Y-m-d") }}';
            
            $("#loader").show();
            $("#bsContent").hide();

            $.get("{{ route('report.balance_sheet.fetch') }}", { date: date }, function(res) {
                $("#loader").hide();
                $("#bsContent").show();
                
                if (res.date) {
                    $("#reportDateBadge").text("As of " + res.date);
                }

                // 1. Assets Calculation & Render
                let cbHtml = '';
                if (res.assets && res.assets.cash_bank && res.assets.cash_bank.length > 0) {
                    res.assets.cash_bank.forEach(a => {
                        let icon = a.name.toLowerCase().includes('bank') ? 'fa-building-columns text-primary' : 'fa-money-bill-1 text-success';
                        cbHtml += `<tr>
                            <td class="bs-sub-row"><i class="fa-solid ${icon} me-2"></i>${a.name}</td>
                            <td class="bs-amount text-dark">${fmt(a.balance)}</td>
                        </tr>`;
                    });
                } else {
                    cbHtml = `<tr><td class="bs-sub-row text-muted italic" colspan="2">No cash/bank accounts found.</td></tr>`;
                }
                $("#cashBankRows").html(cbHtml);

                let receivables = res.assets ? (res.assets.receivables || 0) : 0;
                let inventory = res.assets ? (res.assets.inventory || 0) : 0;
                let currentAssetsTotal = res.assets ? (res.assets.current_total || 0) : 0;
                let fixedAssetsTotal = res.assets ? (res.assets.fixed_total || 0) : 0;
                let assetsTotal = res.assets ? (res.assets.total || 0) : 0;

                $("#lblReceivables").text(fmt(receivables));
                $("#lblInventory").text(fmt(inventory));
                $("#lblCurrentAssetsTotal").text(fmt(currentAssetsTotal));
                $("#lblFixedAssetsTotal").text(fmt(fixedAssetsTotal));
                $("#lblAssetsGrandTotal").text(fmt(assetsTotal));

                // 2. Liabilities & Equity Calculation & Render
                let payables = res.liabilities ? (res.liabilities.payables || 0) : 0;
                let liabTotal = res.liabilities ? (res.liabilities.current_total || 0) : 0;
                let equity = res.liabilities ? (res.liabilities.equity || 0) : 0;
                let liabEquityTotal = res.liabilities ? (res.liabilities.total || 0) : 0;

                $("#lblPayables").text(fmt(payables));
                $("#lblLiabTotal").text(fmt(liabTotal));
                $("#lblEquity").text(fmt(equity));
                $("#lblEquityTotal").text(fmt(equity));
                $("#lblLiabEquityGrandTotal").text(fmt(liabEquityTotal));

                // 3. Update Stat Pills (Desktop & Mobile)
                $("#pillAssets, #mobPillAssets").text(fmt(assetsTotal));
                $("#pillLiabilities, #mobPillLiabilities").text(fmt(liabTotal));
                $("#pillEquity, #mobPillEquity").text(fmt(equity));

                // Equation Status Check (Assets = Liabilities + Equity)
                let diff = Math.abs(assetsTotal - liabEquityTotal);
                if (diff < 0.01) {
                    $("#pillStatus, #mobPillStatus").html('<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i>Balanced</span>');
                } else {
                    $("#pillStatus, #mobPillStatus").html('<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i>Unbalanced</span>');
                }
            });
        }

        $(".btnGenerateTrigger").click(function() {
            loadData();
        });

        $(".btnResetTrigger").click(function() {
            let today = '{{ date("Y-m-d") }}';
            $(".bsDateInput").val(today);
            loadData();
        });

        $(".btnPrintReport").click(function() {
            document.title = 'Balance Sheet';
            window.print();
        });

        window.addEventListener('beforeprint', function() {
            document.title = 'Balance Sheet';
        });

        // Load statement on page load
        loadData();
    });
</script>
@endsection
