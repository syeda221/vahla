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
        color: #dc2626 !important; /* Receivable / Dr */
    }
    .balance-negative {
        color: #16a34a !important; /* Advance / Cr */
    }

    .badge-main-cust {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    .badge-sub-cust {
        background-color: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    /* Select2 Modern Dropdown Row-to-Row Styling */
    .select2-container--default .select2-dropdown {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        overflow: hidden !important;
        background: #ffffff !important;
    }

    .select2-container--default .select2-results > .select2-results__options {
        max-height: 280px !important;
    }

    .select2-container--default .select2-results__option {
        padding: 9px 14px !important;
        border-bottom: 1px solid #e2e8f0 !important;
        transition: all 0.15s ease !important;
        font-size: 0.83rem !important;
        color: #1e293b !important;
    }

    .select2-container--default .select2-results__option:last-child {
        border-bottom: none !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        font-weight: 700 !important;
    }

    .select2-container--default .select2-search--dropdown {
        padding: 8px 10px !important;
        background: #f8fafc !important;
        border-bottom: 1px solid #cbd5e1 !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
        font-size: 0.83rem !important;
        outline: none !important;
    }

    @media print {
        body { background: #ffffff !important; font-size: 11px; }
        .no-print, header, .sidebar, .navbar, footer { display: none !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; margin-bottom: 10px !important; }
    }
</style>

<div class="sale-report-container">

    {{-- DESKTOP FILTER HEADER CARD --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="ledgerFormDesk">
                
                {{-- Top Section: Left Title, Mid Dates, Last Buttons --}}
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
                        <div class="form-check form-switch d-inline-flex align-items-center gap-1 mb-0">
                            <input class="form-check-input includeSubCheck" type="checkbox" id="include_sub_desk" checked style="cursor:pointer;">
                            <label class="form-check-label small fw-bold text-dark mb-0" for="include_sub_desk" style="cursor:pointer; font-size: 11px;">Include Sub-Customers</label>
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
                    <div class="col-md-3">
                        <label for="zone_id_desk" class="sale-filter-label mb-1">Zone:</label>
                        <select name="zone_id" id="zone_id_desk" class="form-select form-select-sm select2 zoneSelect">
                            <option value="">-- All Zones --</option>
                            @foreach ($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->zone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="customer_id_desk" class="sale-filter-label mb-1">Customer / Sub-Customer:</label>
                        <select name="customer_id" id="customer_id_desk" class="form-select form-select-sm select2 customerSelect">
                            <option value="all" data-zone="">-- All Customers --</option>
                            @php
                                $parents = $customers->where('parent_id', null);
                            @endphp
                            @foreach ($parents as $parent)
                                @php
                                    $children = $customers->where('parent_id', $parent->id);
                                @endphp
                                <option value="{{ $parent->id }}" data-zone="{{ $parent->zone }}">
                                    {{ $parent->customer_name }} ({{ $parent->customer_id }}) {{ $children->count() > 0 ? '— [Main Customer]' : '' }}
                                </option>
                                @foreach ($children as $child)
                                    <option value="{{ $child->id }}" data-zone="{{ $child->zone }}">
                                        &nbsp;&nbsp;&nbsp;&nbsp;— Sub: {{ $child->customer_name }} ({{ $child->customer_id }})
                                    </option>
                                @endforeach
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

    {{-- MOBILE OFFCANVAS TRIGGER BUTTON --}}
    <div class="d-md-none mb-2 no-print">
        <button class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterDrawer" aria-controls="mobileFilterDrawer" style="border-radius: 8px; font-weight: 700; font-size: 13px;">
            <i class="fas fa-sliders-h me-2"></i> Open Report Filters
        </button>
    </div>

    {{-- MOBILE OFFCANVAS DRAWER --}}
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileFilterDrawer" aria-labelledby="mobileFilterDrawerLabel">
        <div class="offcanvas-header bg-light border-bottom py-2">
            <h6 class="offcanvas-title fw-bold text-dark" id="mobileFilterDrawerLabel" style="font-size: 14px;">
                <i class="fas fa-filter text-primary me-2"></i>Filter Options
            </h6>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            <form id="ledgerFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Customer Ledger
                        </span>
                    </div>

                    {{-- Zone --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Zone</label>
                        <select name="zone_id" class="form-select form-select-sm select2 zoneSelect">
                            <option value="">-- All Zones --</option>
                            @foreach ($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Customer --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm select2 customerSelect">
                            <option value="all" data-zone="">-- All Customers --</option>
                            @foreach ($parents as $parent)
                                @php $children = $customers->where('parent_id', $parent->id); @endphp
                                <option value="{{ $parent->id }}" data-zone="{{ $parent->zone }}">
                                    {{ $parent->customer_name }} ({{ $parent->customer_id }}) {{ $children->count() > 0 ? '— [Main Customer]' : '' }}
                                </option>
                                @foreach ($children as $child)
                                    <option value="{{ $child->id }}" data-zone="{{ $child->zone }}">
                                        &nbsp;&nbsp;&nbsp;&nbsp;— Sub: {{ $child->customer_name }} ({{ $child->customer_id }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    {{-- Sub-Customer toggle --}}
                    <div class="col-12 mb-2">
                        <div class="form-check form-switch pt-1">
                            <input class="form-check-input includeSubCheck" type="checkbox" id="include_sub_mob" checked>
                            <label class="form-check-label small fw-bold text-dark" for="include_sub_mob">Include Sub-Customers</label>
                        </div>
                    </div>

                    {{-- Quick Filter --}}
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

                    {{-- Start & End Date --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Start Date</label>
                        <input type="date" name="start_date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="2000-01-01" style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">End Date</label>
                        <input type="date" name="end_date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>

                    {{-- Generate Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Generate Report
                        </button>
                    </div>

                    {{-- Reset & Print Actions --}}
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

    {{-- DESKTOP SUMMARY METRIC PILL BAR --}}
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

    {{-- MOBILE SUMMARY METRIC GRID --}}
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

        {{-- SUB-CUSTOMERS BREAKDOWN TABLE (D-NONE BY DEFAULT, SHOWN WHEN SUB-CUSTOMERS EXIST) --}}
        <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none" id="subCustomerBreakdownCard">
            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary" style="font-size: 0.85rem;"><i class="fas fa-sitemap me-2"></i>Sub-Customers Balance Breakdown</h6>
                <span class="badge bg-primary px-2 py-1" id="subCustomerCountBadge">0 Sub-Accounts</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle" style="font-size: 0.8rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Customer / Sub-Customer Name</th>
                            <th class="text-end">Opening Balance</th>
                            <th class="text-end">Period Debit (Sales)</th>
                            <th class="text-end">Period Credit (Receipts)</th>
                            <th class="text-end">Period Closing</th>
                            <th class="text-end">Current Balance</th>
                        </tr>
                    </thead>
                    <tbody id="subCustomerBreakdownBody"></tbody>
                </table>
            </div>
        </div>

        {{-- DESKTOP TABLE VIEW --}}
        <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none d-md-block">
            <div class="card-body p-0">
                <div class="sale-table-wrap">
                    <table class="table table-bordered table-hover align-middle mb-0 report-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Date</th>
                                <th style="width: 11%;">Ref / Invoice</th>
                                <th style="width: 18%;">Customer / Sub-Customer</th>
                                <th>Description</th>
                                <th style="width: 12%;" class="text-end">Debit (Dr)</th>
                                <th style="width: 12%;" class="text-end">Credit (Cr)</th>
                                <th style="width: 13%;" class="text-end">Running Balance</th>
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
            $('.includeSubCheck').on('change', function() { $('.includeSubCheck').prop('checked', $(this).is(':checked')); });
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
                $('.includeSubCheck').prop('checked', true);
                $('.quickFilterSelect').val('custom');
                loadLedger();
            });

            $('.btnPrintReport').on('click', () => window.print());

            function loadLedger() {
                let zid = $(".zoneSelect").val();
                let cid = $(".customerSelect").val();
                let start = $(".startDateInput").val() || '2000-01-01';
                let end = $(".endDateInput").val() || '{{ date("Y-m-d") }}';
                let includeSub = $("#include_sub_desk").is(':checked') ? 1 : 0;

                $("#loader").show();
                $("#ledgerBox").hide();
                $("#subCustomerBreakdownCard").addClass('d-none');

                $.get("{{ route('report.customer.ledger.fetch') }}", {
                    zone_id: zid,
                    customer_id: cid || 'all',
                    start_date: start,
                    end_date: end,
                    include_sub: includeSub
                }, function(res) {
                    $("#loader").hide();
                    $("#ledgerBox").show();

                    let displayStart = formatDisplayDate(start);
                    let displayEnd = formatDisplayDate(end);

                    let titleBadge = '';
                    if (res.is_consolidated) {
                        titleBadge = '<span class="badge-main-cust ms-2"><i class="fas fa-sitemap me-1"></i> Consolidated (All Sub-Customers Included)</span>';
                    }

                    // Build Header
                    $("#ledgerHeader").html(`
                        <div>
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center flex-wrap gap-1">
                                <i class="fas fa-user text-primary me-1"></i> ${res.customer.customer_name} ${titleBadge}
                            </h6>
                            <small class="text-muted">Period: <strong>${displayStart}</strong> to <strong>${displayEnd}</strong></small>
                        </div>
                        <div>
                             <span class="badge bg-primary text-white px-3 py-2 shadow-sm font-monospace">Statement of Account</span>
                        </div>
                    `);

                    // Render Sub-Customer Breakdown table if applicable
                    if (res.sub_customer_breakdown && res.sub_customer_breakdown.length > 1) {
                        let bkHtml = '';
                        res.sub_customer_breakdown.forEach(function(b) {
                            bkHtml += `
                                <tr style="${b.is_main ? 'background-color: #f8fafc;' : ''}">
                                    <td>
                                        ${b.is_main ? '<span class="badge-main-cust"><i class="fas fa-star text-warning me-1"></i> ' + b.name + '</span>' : '<span class="ms-3 fw-semibold text-dark"><i class="fas fa-code-branch text-info me-1"></i> Sub: ' + b.name + '</span>'}
                                    </td>
                                    <td class="text-end font-monospace">Rs ${Math.abs(b.opening_balance).toLocaleString(undefined, {minimumFractionDigits: 2})} <small class="text-muted">${b.opening_balance >= 0 ? 'Dr' : 'Cr'}</small></td>
                                    <td class="text-end font-monospace text-success fw-bold">Rs ${b.total_debit.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-danger fw-bold">Rs ${b.total_credit.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace fw-bold text-dark">Rs ${Math.abs(b.closing_balance).toLocaleString(undefined, {minimumFractionDigits: 2})} <small class="text-muted">${b.closing_balance >= 0 ? 'Dr' : 'Cr'}</small></td>
                                    <td class="text-end font-monospace fw-bold text-primary">Rs ${Math.abs(b.current_total_balance).toLocaleString(undefined, {minimumFractionDigits: 2})} <small class="text-muted">${b.current_total_balance >= 0 ? 'Dr' : 'Cr'}</small></td>
                                </tr>
                            `;
                        });
                        $('#subCustomerBreakdownBody').html(bkHtml);
                        $('#subCustomerCountBadge').text(res.sub_customer_breakdown.length + ' Sub-Accounts');
                        $('#subCustomerBreakdownCard').removeClass('d-none');
                    } else {
                        $('#subCustomerBreakdownCard').addClass('d-none');
                    }

                    let totalDebit = 0;
                    let totalCredit = 0;
                    let lastBalance = parseFloat(res.opening_balance);

                    // Update Top Metrics (Desktop & Mobile)
                    let formattedOpening = 'Rs ' + lastBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    $('#pillOpeningBalance, #mobPillOpeningBalance').text(formattedOpening);

                    // Desktop Opening Row
                    let html = `
                        <tr class="bg-light fw-bold">
                            <td class="text-center small">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-start">Opening Balance (B/F)</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end text-dark font-monospace">
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
                        let debit = t.debit && t.debit > 0 ? parseFloat(t.debit) : 0;
                        let credit = t.credit && t.credit > 0 ? parseFloat(t.credit) : 0;
                        totalDebit += debit;
                        totalCredit += credit;
                        lastBalance = parseFloat(t.balance);

                        let balLabel = lastBalance >= 0 ? 'Dr' : 'Cr';
                        let balClass = lastBalance >= 0 ? 'balance-positive' : 'balance-negative';
                        let partyBadge = '';
                        if (t.is_sub) {
                            partyBadge = `<span class="badge-sub-cust"><i class="fas fa-code-branch me-1"></i> Sub: ${t.party_name}</span>`;
                        } else {
                            partyBadge = `<span class="badge-main-cust"><i class="fas fa-user me-1"></i> ${t.party_name || t.customer_name || 'Main Customer'}</span>`;
                        }

                        // Desktop Row
                        html += `
                            <tr>
                                <td class="text-center small text-nowrap">${t.date}</td>
                                <td class="text-center"><span class="badge bg-light text-primary border font-monospace">${t.invoice ?? '-'}</span></td>
                                <td>${partyBadge}</td>
                                <td class="text-start">${t.description}</td>
                                <td class="text-end text-success fw-bold font-monospace">${debit > 0 ? 'Rs ' + debit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</td>
                                <td class="text-end text-danger fw-bold font-monospace">${credit > 0 ? 'Rs ' + credit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</td>
                                <td class="text-end fw-bold font-monospace ${balClass}">
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
                                    <div class="mb-1">${partyBadge}</div>
                                    <small class="text-muted d-block" style="font-size: 11px;">${t.description}</small>
                                </div>
                                <div class="border-top pt-2 mt-1">
                                    <div class="row g-1 text-center" style="font-size: 11px;">
                                        <div class="col-4 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Debit (Dr)</span>
                                            <strong class="text-success">${debit > 0 ? 'Rs ' + debit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</strong>
                                        </div>
                                        <div class="col-4 border-end">
                                            <span class="text-muted d-block" style="font-size: 10px;">Credit (Cr)</span>
                                            <strong class="text-danger">${credit > 0 ? 'Rs ' + credit.toLocaleString(undefined, {minimumFractionDigits: 2}) : '-'}</strong>
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
                            <td class="text-end text-success font-monospace">Rs ${totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td class="text-end text-danger font-monospace">Rs ${totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                            <td class="text-end font-monospace ${lastBalance >= 0 ? 'balance-positive' : 'balance-negative'}">Rs ${Math.abs(lastBalance).toLocaleString(undefined, {minimumFractionDigits: 2})} ${lastBalance >= 0 ? 'Dr' : 'Cr'}</td>
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
