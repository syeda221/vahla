@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

<style>
:root {
    --vh-primary: #2563eb;
    --vh-primary-hover: #1d4ed8;
    --vh-primary-light: rgba(37,99,235,0.08);
    --vh-bg: #f8fafc;
    --vh-card-bg: #ffffff;
    --vh-border: #e2e8f0;
    --vh-text: #0f172a;
    --vh-text-muted: #64748b;
    --vh-success: #10b981;
    --vh-danger: #ef4444;
    --vh-warning: #f59e0b;
    --vh-card-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
    --vh-card-shadow-hover: 0 4px 12px rgba(0,0,0,0.08);
}

.main-content {
    padding-bottom: 40px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    width: 100%;
}

/* Summary Cards */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
    width: 100%;
}

.summary-card {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: var(--vh-card-shadow);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-card:hover {
    box-shadow: var(--vh-card-shadow-hover);
    transform: translateY(-2px);
}

.summary-card .label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--vh-text-muted);
}

.summary-card .value {
    font-size: 22px;
    font-weight: 800;
    margin-top: 4px;
    line-height: 1.2;
}

.summary-card .icon {
    font-size: 28px;
    opacity: 0.25;
}

/* Filter Type Cards */
.filter-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-card-btn {
    background: var(--vh-card-bg);
    border: 1.5px solid var(--vh-border);
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 700;
    color: var(--vh-text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    letter-spacing: 0.2px;
}

.filter-card-btn:hover {
    border-color: var(--vh-primary);
    color: var(--vh-primary);
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(37,99,235,0.08);
}

.filter-card-btn.active {
    border-color: var(--vh-primary);
    background: var(--vh-primary);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37,99,235,0.25);
}

/* Filter Section */
.filter-section {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 24px;
    box-shadow: var(--vh-card-shadow);
}

.filter-section .form-label {
    color: var(--vh-text-muted);
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: 0.2px;
}

.filter-section .form-control,
.filter-section .form-select {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    color: var(--vh-text);
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.filter-section .form-control:focus,
.filter-section .form-select:focus {
    border-color: var(--vh-primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}

/* DataTable Card */
.data-card {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    border-radius: 16px;
    padding: 0;
    box-shadow: var(--vh-card-shadow);
    overflow: hidden;
}

.data-card .card-header-custom {
    padding: 16px 24px;
    border-bottom: 1px solid var(--vh-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}

.data-card .card-header-custom h5 {
    margin: 0;
    font-weight: 800;
    font-size: 15px;
    color: var(--vh-text);
    display: flex;
    align-items: center;
    gap: 8px;
}

.data-card .table-wrap {
    padding: 16px 24px 24px;
}

/* DataTable Overrides */
#voucherHistoryTable {
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0;
    width: 100% !important;
}

#voucherHistoryTable thead th {
    background: #f8fafc;
    color: var(--vh-text);
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 12px 10px;
    border-bottom: 2px solid var(--vh-border);
    white-space: nowrap;
}

#voucherHistoryTable tbody td {
    padding: 11px 10px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: var(--vh-text);
}

#voucherHistoryTable tbody tr:hover {
    background: #f8fafc;
}

#voucherHistoryTable .badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
}

/* Action Buttons */
.action-group {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.action-group .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 13px;
    transition: all 0.15s ease;
}

.action-group .btn:hover {
    transform: translateY(-2px);
}

/* Mobile Card Styling */
.vh-mcard {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    width: 100%;
    box-sizing: border-box;
}
.vh-mcard-header {
    padding-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
}

/* Desktop View Constraints (min-width: 992px) */
@media (min-width: 992px) {
    #mobileCardsWrap,
    .mobile-view-toggle,
    #mobilePaginationWrap,
    .mobile-search-box {
        display: none !important;
    }
    #desktopTableWrap {
        display: block !important;
    }
    #desktopTableWrap .table-responsive {
        display: block !important;
    }
}

/* Tablet & Mobile Responsive View (Zero Horizontal Scroll on Mobile) */
@media (max-width: 991.98px) {
    html, body {
        max-width: 100vw !important;
        width: 100% !important;
        overflow-x: hidden !important;
        position: relative;
    }
    .container-scroller,
    .app-page-body,
    .main-content,
    .container-fluid {
        max-width: 100vw !important;
        width: 100% !important;
        overflow-x: hidden !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
        box-sizing: border-box !important;
    }
    .page-header-box {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 12px !important;
        margin-bottom: 16px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .page-header-box h4 {
        font-size: 19px !important;
    }
    .page-header-box .btn,
    .page-header-box .dropdown,
    .page-header-box .dropdown-toggle {
        width: 100% !important;
    }
    .page-header-box .dropdown-toggle {
        justify-content: center !important;
    }
    .page-header-box .dropdown-menu {
        max-width: calc(100vw - 20px) !important;
        right: 0 !important;
        left: auto !important;
    }

    /* Summary Grid: prevent blowout using minmax(0, 1fr) */
    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
        margin-bottom: 14px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .summary-card {
        padding: 10px 12px !important;
        border-radius: 10px !important;
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .summary-card > div:first-child {
        min-width: 0 !important;
        overflow: hidden !important;
        flex: 1 1 auto !important;
    }
    .summary-card .label {
        font-size: 10px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .summary-card .value {
        font-size: 14px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        margin-top: 2px !important;
    }
    .summary-card .icon {
        font-size: 18px !important;
        margin-left: 6px !important;
        flex-shrink: 0 !important;
    }

    /* Filter Cards: flex-wrap with compact pills */
    .filter-cards {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        margin-bottom: 14px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .filter-card-btn {
        padding: 6px 8px !important;
        font-size: 11px !important;
        border-radius: 8px !important;
        flex: 1 1 calc(33.333% - 6px) !important;
        min-width: 0 !important;
        text-align: center !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Filter Section: tight margins to avoid horizontal overflow */
    .filter-section {
        padding: 12px 10px !important;
        border-radius: 12px !important;
        margin-bottom: 14px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .filter-section .row {
        margin-left: -4px !important;
        margin-right: -4px !important;
    }
    .filter-section .row > [class*="col-"] {
        padding-left: 4px !important;
        padding-right: 4px !important;
        min-width: 0 !important;
    }
    .filter-section .form-label {
        font-size: 11px !important;
        margin-bottom: 2px !important;
    }
    .filter-section .form-control,
    .filter-section .form-select {
        font-size: 12px !important;
        padding: 6px 8px !important;
        min-width: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Data Card */
    .data-card {
        border-radius: 12px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .data-card .card-header-custom {
        padding: 10px 12px !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .data-card .table-wrap {
        padding: 10px 8px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    /* Mobile Cards Container & Items */
    #mobileCardsWrap {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }
    .vh-mcard {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 12px !important;
        margin-bottom: 10px !important;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05) !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        word-break: break-word !important;
    }
    .vh-mcard-header {
        padding-bottom: 8px !important;
        border-bottom: 1px dashed #e2e8f0 !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
    }
    .vh-mcard-body {
        padding-top: 8px !important;
        width: 100% !important;
        overflow: hidden !important;
    }
    .vh-mcard-body .d-flex {
        width: 100% !important;
        min-width: 0 !important;
    }
    .vh-mcard-body .text-end {
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        max-width: 65% !important;
    }
    .vh-mcard-footer {
        padding-top: 8px !important;
        border-top: 1px solid #f1f5f9 !important;
        display: flex !important;
        justify-content: flex-end !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    .vh-mcard-footer .btn {
        font-size: 12px !important;
        padding: 4px 10px !important;
    }

    /* Desktop Table Container on Mobile */
    #desktopTableWrap {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }
    #desktopTableWrap.mobile-table-hidden {
        display: none !important;
    }
    #desktopTableWrap .table-responsive {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        margin-bottom: 10px !important;
    }
    #voucherHistoryTable {
        min-width: 700px !important;
        white-space: nowrap !important;
    }

    /* DataTables wrapper adjustments on mobile */
    .dataTables_wrapper {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
    }
    .dataTables_wrapper .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
    }
    .dataTables_wrapper [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 100% !important;
    }
    div.dataTables_wrapper div.dataTables_length,
    div.dataTables_wrapper div.dataTables_filter {
        text-align: left !important;
        margin-bottom: 8px !important;
        width: 100% !important;
    }
    div.dataTables_wrapper div.dataTables_filter input {
        width: 100% !important;
        margin-left: 0 !important;
        margin-top: 4px !important;
        box-sizing: border-box !important;
    }
    div.dataTables_wrapper div.dataTables_info {
        text-align: left !important;
        padding: 6px 0 !important;
        font-size: 12px !important;
    }
    div.dataTables_wrapper div.dataTables_paginate {
        text-align: center !important;
        padding: 8px 0 !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        width: 100% !important;
    }
    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: 2px !important;
    }
    .pagination .page-item .page-link {
        padding: 4px 8px !important;
        font-size: 12px !important;
    }
}
</style>

<div class="main-content">
    <div class="container-fluid px-2 px-md-3 px-lg-4 py-2 py-md-3">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 page-header-box">
            <div>
                <h4 class="mb-0" style="color:var(--vh-text);font-weight:800;font-size:22px;">All Vouchers</h4>
                <p class="text-muted mb-0 small">Unified history for Expense, Receipts (Payment In), and Payment Out vouchers</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle px-4 py-2" type="button" id="newVoucherDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:10px;font-weight:700;font-size:13px;background:var(--vh-primary);border:0;box-shadow:0 4px 12px rgba(37,99,235,0.25);">
                    <i class="fas fa-plus me-1"></i> New Voucher
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="newVoucherDropdown" style="border-radius:12px; font-size:13px; font-weight:600; padding:8px;">
                    <li><a class="dropdown-item py-2 rounded-2 fw-bold text-primary" href="{{ route('vouchers.create') }}"><i class="fas fa-plus-circle text-primary me-2"></i> Create Voucher (Unified)</a></li>
                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('vouchers.create') }}?type=party_transfer"><i class="fas fa-right-left text-primary me-2"></i> Add Party to Party Transfer</a></li>
                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('vouchers.create') }}?type=internal_transfer"><i class="fas fa-money-bill-transfer text-primary me-2"></i> Add Internal Transfer</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    @can('expense.voucher.create')
                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('expense_vochers') }}"><i class="fas fa-file-invoice text-danger me-2"></i> Add Expense Voucher</a></li>
                    @endcan
                    @can('receipts.voucher.create')
                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('recepit_vochers') }}"><i class="fas fa-receipt text-success me-2"></i> Add Receipt Voucher (Payment In)</a></li>
                    @endcan
                    @can('payment.voucher.create')
                        <li><a class="dropdown-item py-2 rounded-2" href="{{ route('Payment_vochers') }}"><i class="fas fa-hand-holding-dollar text-warning me-2"></i> Add Payment Voucher (Payment Out)</a></li>
                    @endcan
                </ul>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-grid" id="summaryCards">
            <div class="summary-card">
                <div>
                    <div class="label">Total Vouchers</div>
                    <div class="value" id="sumTotal" style="color:var(--vh-primary);">0.00</div>
                </div>
                <div class="icon"><i class="fas fa-receipt" style="color:var(--vh-primary);"></i></div>
            </div>
            <div class="summary-card">
                <div>
                    <div class="label">Expense</div>
                    <div class="value" id="sumExpense" style="color:var(--vh-danger);">0.00</div>
                </div>
                <div class="icon"><i class="fas fa-arrow-up" style="color:var(--vh-danger);"></i></div>
            </div>
            <div class="summary-card">
                <div>
                    <div class="label">Payment In</div>
                    <div class="value" id="sumPaymentIn" style="color:var(--vh-success);">0.00</div>
                </div>
                <div class="icon"><i class="fas fa-arrow-right" style="color:var(--vh-success);"></i></div>
            </div>
            <div class="summary-card">
                <div>
                    <div class="label">Payment Out</div>
                    <div class="value" id="sumPaymentOut" style="color:var(--vh-warning);">0.00</div>
                </div>
                <div class="icon"><i class="fas fa-arrow-left" style="color:var(--vh-warning);"></i></div>
            </div>
        </div>

        {{-- Filter Type Buttons --}}
        <div class="filter-cards" id="typeFilterGroup">
            <button class="filter-card-btn active" data-type="all"><i class="fas fa-list-ul me-1"></i> All Vouchers</button>
            <button class="filter-card-btn" data-type="expense"><i class="fas fa-arrow-up text-danger me-1"></i> Expense</button>
            <button class="filter-card-btn" data-type="payment_in"><i class="fas fa-arrow-right text-success me-1"></i> Payment In</button>
            <button class="filter-card-btn" data-type="payment_out"><i class="fas fa-arrow-left text-warning me-1"></i> Payment Out</button>
            <button class="filter-card-btn" data-type="party_transfer"><i class="fas fa-right-left text-primary me-1"></i> Party Transfer</button>
            <button class="filter-card-btn" data-type="internal_transfer"><i class="fas fa-money-bill-transfer text-info me-1"></i> Internal Transfer</button>
        </div>

        {{-- Advanced Filters --}}
        <div class="filter-section">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" id="filterFromDate">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" id="filterToDate">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Party Type</label>
                    <select class="form-select" id="filterPartyType">
                        <option value="">All Parties</option>
                        <option value="customer">Customer</option>
                        <option value="vendor">Vendor</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Account</label>
                    <select class="form-select" id="filterAccount">
                        <option value="">All Accounts</option>
                        @if(isset($accounts))
                            @foreach($accounts as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label">Min Amt</label>
                    <input type="number" step="0.01" class="form-control" id="filterMinAmount" placeholder="Min">
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label">Max Amt</label>
                    <input type="number" step="0.01" class="form-control" id="filterMaxAmount" placeholder="Max">
                </div>
                <div class="col-12 col-md-2 d-flex gap-1 mt-2 mt-md-0">
                    <button class="btn btn-primary btn-sm w-100" id="applyFilters" style="border-radius:8px;font-weight:700;background:var(--vh-primary);border:0;height:35px;">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                    <button class="btn btn-outline-secondary btn-sm w-100" id="resetFilters" style="border-radius:8px;font-weight:700;height:35px;">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- DataTable Card --}}
        <div class="data-card">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list text-primary"></i> Vouchers List</h5>
                
                {{-- Mobile View Toggle (Cards vs Table) --}}
                <div class="mobile-view-toggle">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnShowCards">
                            <i class="fas fa-th-large me-1"></i> Cards
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnShowTable">
                            <i class="fas fa-table me-1"></i> Table
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-wrap">

                {{-- Mobile Cards Container --}}
                <div id="mobileCardsWrap">
                    {{-- Mobile Search Box --}}
                    <div class="mobile-search-box mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="mobileSearchInput" class="form-control border-start-0" placeholder="Search vouchers...">
                            <button class="btn btn-outline-secondary" type="button" id="clearMobileSearch"><i class="fas fa-times"></i></button>
                        </div>
                    </div>

                    <div id="mobileVoucherCards" class="d-flex flex-column gap-2 mb-2">
                        <div class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading vouchers...
                        </div>
                    </div>

                    {{-- Mobile Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top" id="mobilePaginationWrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="mPrevBtn" disabled>
                            <i class="fas fa-chevron-left me-1"></i> Prev
                        </button>
                        <span class="small text-muted font-weight-bold" id="mPageInfo">Page 1</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="mNextBtn" disabled>
                            Next <i class="fas fa-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- Desktop Table Wrapper --}}
                <div id="desktopTableWrap" class="mobile-table-hidden">
                    <div class="table-responsive">
                        <table id="voucherHistoryTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Voucher No</th>
                                    <th class="text-center">Type</th>
                                    <th>Date</th>
                                    <th>Party</th>
                                    <th>Details</th>
                                    <th class="text-end">Amount</th>
                                    <th>Remarks</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function() {

    // Number formatter
    function numberFormat(v) {
        v = parseFloat(v) || 0;
        return v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Render mobile cards on each draw / dataSrc
    function renderMobileCards(data) {
        let $wrap = $('#mobileVoucherCards');
        if (!$wrap.length) return;
        $wrap.empty();

        if (!data || data.length === 0) {
            $wrap.html('<div class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-1"></i>No vouchers found matching criteria.</div>');
            return;
        }

        data.forEach(function(r) {
            let badge = 'bg-secondary';
            if (r.source === 'expense') badge = 'bg-danger text-white';
            else if (r.source === 'payment_in') badge = 'bg-success text-white';
            else if (r.source === 'payment_out') badge = 'bg-warning text-dark';
            else if (r.source === 'party_transfer') badge = 'bg-primary text-white';
            else if (r.source === 'internal_transfer') badge = 'bg-info text-dark';

            let partyHtml = r.party_name || '-';
            if (r.party_type_label && r.party_type_label !== '-') {
                partyHtml += ' <small class="text-muted">(' + r.party_type_label + ')</small>';
            }

            let actionBtns = '';
            if (r.print_url) {
                actionBtns += '<a href="' + r.print_url + '" target="_blank" class="btn btn-sm btn-outline-primary" title="Print"><i class="bi bi-printer me-1"></i> Print</a>';
            }
            if (r.delete_url) {
                actionBtns += '<button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-delete-url="' + r.delete_url + '" data-delete-method="' + (r.delete_method || 'DELETE') + '" data-voucher-no="' + (r.voucher_no || '') + ' (' + (r.type_label || '') + ')"><i class="bi bi-trash me-1"></i> Delete</button>';
            }

            let cardHtml = `
                <div class="vh-mcard">
                    <div class="vh-mcard-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center flex-wrap" style="gap: 4px; min-width: 0; max-width: 70%;">
                            <span class="fw-bold text-primary text-truncate" style="font-size: 0.95rem;">${r.voucher_no || '-'}</span>
                            <span class="badge ${badge} text-truncate">${r.type_label || '-'}</span>
                        </div>
                        <span class="text-muted small font-weight-bold flex-shrink-0">${r.date || '-'}</span>
                    </div>
                    <div class="vh-mcard-body pt-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small flex-shrink-0">Party:</span>
                            <span class="fw-semibold text-dark text-end ms-2">${partyHtml}</span>
                        </div>
                        ${r.detail && r.detail !== '-' ? `
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small flex-shrink-0">Details:</span>
                            <span class="text-dark small text-end ms-2">${r.detail}</span>
                        </div>` : ''}
                        ${r.remarks && r.remarks !== '-' ? `
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="text-muted small flex-shrink-0">Remarks:</span>
                            <span class="text-dark small text-end ms-2">${r.remarks}</span>
                        </div>` : ''}
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="text-muted small font-weight-bold">Amount:</span>
                            <span class="fw-bold" style="font-size: 1.05rem; color: var(--vh-primary);">Rs ${numberFormat(r.amount)}</span>
                        </div>
                    </div>
                    ${actionBtns ? `
                    <div class="vh-mcard-footer mt-2 pt-2">
                        ${actionBtns}
                    </div>` : ''}
                </div>
            `;
            $wrap.append(cardHtml);
        });
    }

    let table = $('#voucherHistoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('voucher.history.data') }}",
            data: function(d) {
                d.type = $('#typeFilterGroup .filter-card-btn.active').data('type') || 'all';
                d.from_date = $('#filterFromDate').val();
                d.to_date = $('#filterToDate').val();
                d.party_type = $('#filterPartyType').val();
                d.account_id = $('#filterAccount').val();
                d.min_amount = $('#filterMinAmount').val();
                d.max_amount = $('#filterMaxAmount').val();
            },
            dataSrc: function(json) {
                if (json.summary) {
                    $('#sumTotal').text(numberFormat(json.summary.total_amount));
                    $('#sumExpense').text(numberFormat(json.summary.total_expense));
                    $('#sumPaymentIn').text(numberFormat(json.summary.total_payment_in));
                    $('#sumPaymentOut').text(numberFormat(json.summary.total_payment_out));
                }
                renderMobileCards(json.data);
                return json.data;
            }
        },
        drawCallback: function(settings) {
            let api = this.api();
            let pageInfo = api.page.info();
            let totalPages = pageInfo.pages || 1;
            let currPage = pageInfo.page + 1;
            $('#mPageInfo').text('Page ' + currPage + ' of ' + totalPages + ' (' + pageInfo.recordsDisplay + ')');
            $('#mPrevBtn').prop('disabled', currPage <= 1);
            $('#mNextBtn').prop('disabled', currPage >= totalPages);
        },
        columns: [
            { 
                data: 'voucher_no',
                render: function(v, t, r) {
                    return '<span class="fw-bold text-primary">' + (v || '-') + '</span>';
                }
            },
            {
                data: 'type_label',
                className: 'text-center',
                render: function(v, t, r) {
                    let badge = 'bg-secondary';
                    if (r.source === 'expense') badge = 'bg-danger text-white';
                    else if (r.source === 'payment_in') badge = 'bg-success text-white';
                    else if (r.source === 'payment_out') badge = 'bg-warning text-dark';
                    else if (r.source === 'party_transfer') badge = 'bg-primary text-white';
                    else if (r.source === 'internal_transfer') badge = 'bg-info text-dark';
                    return '<span class="badge ' + badge + '">' + (v || '-') + '</span>';
                }
            },
            { data: 'date' },
            {
                data: null,
                render: function(r) {
                    let html = '<strong>' + (r.party_name || '-') + '</strong>';
                    if (r.party_type_label && r.party_type_label !== '-') {
                        html += ' <small class="text-muted d-block">(' + r.party_type_label + ')</small>';
                    }
                    return html;
                }
            },
            {
                data: 'detail',
                render: function(v) { return v || '-'; }
            },
            { 
                data: 'amount', 
                render: function(v) { return '<span class="fw-bold">Rs ' + numberFormat(v) + '</span>'; }, 
                className: 'text-end' 
            },
            { 
                data: 'remarks', 
                render: function(v) { return v || '-'; } 
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(r) {
                    var btns = [];
                    if (r.print_url) {
                        btns.push({ 
                            icon: 'bi-printer', 
                            cls: 'btn-outline-primary', 
                            url: r.print_url, 
                            title: 'Print Voucher', 
                            target: '_blank' 
                        });
                    }
                    if (r.delete_url) {
                        btns.push({ 
                            icon: 'bi-trash', 
                            cls: 'btn-outline-danger', 
                            url: r.delete_url, 
                            title: 'Delete Voucher', 
                            isDelete: true, 
                            deleteMethod: r.delete_method || 'DELETE',
                            voucherNo: (r.voucher_no || '') + ' (' + (r.type_label || '') + ')'
                        });
                    }
                    if (btns.length === 0) return '-';
                    var html = '<div class="action-group">';
                    $.each(btns, function(i, b) {
                        if (b.isDelete) {
                            html += '<button type="button" class="btn btn-sm ' + b.cls + '" title="' + b.title + '" data-delete-url="' + b.url + '" data-delete-method="' + b.deleteMethod + '" data-voucher-no="' + b.voucherNo + '"><i class="bi ' + b.icon + '"></i></button>';
                        } else {
                            var tgt = b.target ? ' target="' + b.target + '"' : '';
                            html += '<a href="' + b.url + '" class="btn btn-sm ' + b.cls + '" title="' + b.title + '"' + tgt + '><i class="bi ' + b.icon + '"></i></a>';
                        }
                    });
                    html += '</div>';
                    return html;
                }
            }
        ],
        order: [[2, 'desc']],
        language: {
            searchPlaceholder: 'Search vouchers...',
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
        }
    });

    // Mobile View Switcher (Cards vs Table)
    $('#btnShowCards').on('click', function() {
        $(this).addClass('active');
        $('#btnShowTable').removeClass('active');
        $('#mobileCardsWrap').show();
        $('#desktopTableWrap').addClass('mobile-table-hidden').hide();
    });

    $('#btnShowTable').on('click', function() {
        $(this).addClass('active');
        $('#btnShowCards').removeClass('active');
        $('#mobileCardsWrap').hide();
        $('#desktopTableWrap').removeClass('mobile-table-hidden').show();
        table.columns.adjust();
    });

    // Mobile Search with debounce
    let mobileSearchTimer = null;
    $('#mobileSearchInput').on('keyup input', function() {
        clearTimeout(mobileSearchTimer);
        let val = $(this).val();
        mobileSearchTimer = setTimeout(function() {
            table.search(val).draw();
        }, 350);
    });

    $('#clearMobileSearch').on('click', function() {
        $('#mobileSearchInput').val('');
        table.search('').draw();
    });

    // Mobile Pagination Prev / Next
    $('#mPrevBtn').on('click', function() {
        table.page('previous').draw('page');
    });

    $('#mNextBtn').on('click', function() {
        table.page('next').draw('page');
    });

    // Global Delete handler (works for both Desktop table and Mobile cards)
    $(document).on('click', '[data-delete-url]', function(e) {
        e.preventDefault();
        var url = $(this).data('delete-url');
        var method = $(this).data('delete-method') || 'DELETE';
        var label = $(this).data('voucher-no') || 'this voucher';
        
        Swal.fire({
            title: 'Delete Voucher?',
            html: 'Are you sure you want to delete <strong>' + label + '</strong>?<br>This will reverse related accounting entries.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: url,
                type: 'POST',
                data: { _method: method, _token: '{{ csrf_token() }}' },
                success: function(resp) {
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: resp.message || 'Voucher deleted successfully.', timer: 2000, showConfirmButton: false });
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to delete voucher.' });
                }
            });
        });
    });

    // Type filter buttons
    $('#typeFilterGroup').on('click', '.filter-card-btn', function() {
        $('#typeFilterGroup .filter-card-btn').removeClass('active');
        $(this).addClass('active');
        table.ajax.reload();
    });

    $('#applyFilters').on('click', function() { table.ajax.reload(); });
    $('#resetFilters').on('click', function() {
        $('#filterFromDate, #filterToDate, #filterMinAmount, #filterMaxAmount').val('');
        $('#filterPartyType, #filterAccount').val('');
        $('#mobileSearchInput').val('');
        table.search('').ajax.reload();
    });

    $('#filterFromDate, #filterToDate, #filterPartyType, #filterAccount, #filterMinAmount, #filterMaxAmount').on('keypress', function(e) {
        if (e.which === 13) { $('#applyFilters').click(); }
    });

});
</script>

@endsection
