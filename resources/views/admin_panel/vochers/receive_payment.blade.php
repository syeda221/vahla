@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --rp-primary: #059669;
        --rp-primary-hover: #047857;
        --rp-primary-light: #ecfdf5;
        --rp-primary-border: #a7f3d0;
        --rp-text-main: #0f172a;
        --rp-text-muted: #64748b;
        --rp-border: #e2e8f0;
        --rp-bg-subtle: #f8fafc;
        --rp-card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
        --rp-card-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }

    .rp-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 10px 15px 60px 15px;
    }

    /* Top Page Header */
    .rp-header-card {
        background: #ffffff;
        border: 1px solid var(--rp-border);
        border-radius: 14px;
        box-shadow: var(--rp-card-shadow);
        padding: 18px 24px;
        margin-bottom: 20px;
    }

    .rp-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--rp-text-main);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .rp-title-icon {
        width: 38px;
        height: 38px;
        background: var(--rp-primary-light);
        color: var(--rp-primary);
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .rp-badge-voucher {
        background: #ecfdf5;
        color: #065f46;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid #a7f3d0;
        font-family: monospace;
    }

    .rp-badge-draft {
        background: #fef3c7;
        color: #92400e;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 3px 9px;
        border-radius: 6px;
        border: 1px solid #fde68a;
        text-transform: uppercase;
    }

    /* Section Cards */
    .rp-section-card {
        background: #ffffff;
        border: 1px solid var(--rp-border);
        border-radius: 14px;
        box-shadow: var(--rp-card-shadow);
        padding: 22px 24px;
        margin-bottom: 22px;
    }

    .rp-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }

    .rp-section-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--rp-text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Form Fields Styling */
    .rp-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 7px;
        display: block;
    }

    .rp-label .req {
        color: #ef4444;
        margin-left: 2px;
        font-weight: 700;
    }

    .rp-input, .rp-select {
        height: 42px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13.5px;
        color: #0f172a;
        width: 100%;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .rp-input:focus, .rp-select:focus {
        border-color: var(--rp-primary);
        box-shadow: 0 0 0 3.5px rgba(5, 150, 105, 0.15);
        outline: none;
    }

    .rp-file-input {
        padding: 6px 12px;
        font-size: 12.5px;
    }

    /* Select2 Alignment Fixes */
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
        transition: all 0.2s ease;
    }
    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--rp-primary) !important;
        box-shadow: 0 0 0 3.5px rgba(5, 150, 105, 0.15) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        font-size: 13px !important;
        color: #0f172a !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 10px !important;
    }

    /* Calculation Panel */
    .rp-calc-panel {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 18px;
    }

    .rp-amount-group .input-group-text {
        height: 42px;
        font-size: 12px;
        font-weight: 700;
        padding: 0 12px;
        border: 1.5px solid #cbd5e1;
        border-right: none;
        border-radius: 8px 0 0 8px;
    }

    .rp-amount-group .rp-input-amount {
        height: 42px;
        border-radius: 0 8px 8px 0 !important;
        font-weight: 700;
        font-size: 15px;
    }

    .rp-addon-primary {
        background: #ecfdf5 !important;
        color: #047857 !important;
        border-color: #a7f3d0 !important;
    }
    .rp-amount-group:has(.rp-addon-primary) .rp-input-amount {
        border-color: #a7f3d0;
        color: #065f46;
        background: #ffffff;
    }

    .rp-addon-warning {
        background: #fffbeb !important;
        color: #92400e !important;
        border-color: #fde68a !important;
    }
    .rp-amount-group:has(.rp-addon-warning) .rp-input-amount {
        border-color: #fde68a;
        color: #92400e;
    }

    .rp-addon-danger {
        background: #fef2f2 !important;
        color: #b91c1c !important;
        border-color: #fecaca !important;
    }
    .rp-amount-group:has(.rp-addon-danger) .rp-input-amount {
        border-color: #fecaca;
        color: #b91c1c;
    }

    .rp-settlement-badge {
        height: 42px;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        border-radius: 8px;
        padding: 4px 16px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
    }

    .rp-settlement-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        color: #ffffff;
    }

    .rp-settlement-sub {
        font-size: 11px;
        font-weight: 600;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .rp-settlement-value {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 0.3px;
    }

    .rp-sub-hint {
        font-size: 11px;
        color: #64748b;
    }

    /* Metric Summary KPI Cards */
    .metric-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--rp-card-shadow);
        transition: all 0.2s ease;
        height: 100%;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--rp-card-shadow-md);
    }
    .metric-card.blue {
        border-color: #bfdbfe;
    }
    .metric-card.blue .metric-icon {
        background: #eff6ff;
        color: #2563eb;
    }
    .metric-card.amber {
        border-color: #fde68a;
    }
    .metric-card.amber .metric-icon {
        background: #fffbeb;
        color: #b45309;
    }
    .metric-card.emerald {
        border-color: #a7f3d0;
    }
    .metric-card.emerald .metric-icon {
        background: #ecfdf5;
        color: #059669;
    }
    .metric-card.purple {
        border-color: #e9d5ff;
    }
    .metric-card.purple .metric-icon {
        background: #faf5ff;
        color: #7c3aed;
    }

    .metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .metric-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .metric-value {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* Invoices Table Styling */
    .rp-table-wrapper {
        border: 1px solid var(--rp-border);
        border-radius: 10px;
        overflow: hidden;
    }

    .rp-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }
    .rp-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .rp-table td {
        padding: 11px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        transition: background-color 0.15s ease;
    }
    .rp-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .rp-table tbody tr.row-allocated td {
        background-color: #f0fdf4 !important;
    }
    .rp-table tbody tr.row-disabled {
        opacity: 0.45;
    }
    .rp-table tbody tr.row-disabled td {
        background-color: #fafafa;
    }

    .rp-inv-code {
        font-family: monospace;
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
    }

    .alloc-input {
        width: 130px;
        height: 36px;
        text-align: right;
        font-weight: 700;
        font-size: 13.5px;
        color: #047857;
        border: 1.5px solid #cbd5e1;
        border-radius: 7px;
        padding: 4px 10px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .alloc-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        outline: none;
    }
    .alloc-input:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .row-allocated .alloc-input {
        border-color: #34d399;
        background-color: #ffffff;
    }

    /* Segmented Mode Control */
    .distribution-mode-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 9px;
        border: 1px solid #e2e8f0;
    }
    .distribution-mode-group .mode-btn {
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 7px;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .distribution-mode-group .mode-btn.active {
        background: #ffffff;
        color: var(--rp-primary);
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Checkbox styling */
    .custom-row-chk {
        width: 17px;
        height: 17px;
        cursor: pointer;
        accent-color: #059669;
        vertical-align: middle;
    }

    /* Action buttons */
    .btn-pay-full {
        font-size: 11px;
        padding: 5px 11px;
        font-weight: 700;
        border-radius: 6px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
    }
    .btn-pay-full:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }

    /* Empty state */
    .empty-invoices {
        text-align: center;
        padding: 45px 20px;
        color: #64748b;
    }
    .empty-invoices i {
        font-size: 36px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    .btn-action-tool {
        height: 35px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
</style>

<div class="content-wrapper">
    <div class="rp-container">

        <!-- Top Header Card -->
        <div class="rp-header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rp-title-icon">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h1 class="rp-title">Receive Payment</h1>
                        <span class="rp-badge-voucher">{{ $nextRvid }}</span>
                        <span class="rp-badge-draft">Draft</span>
                    </div>
                    <p class="text-muted mb-0 small">
                        Customer invoice settlement with specific multi-select, Equal & FIFO auto distribution.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('all_recepit_vochers') }}" class="btn btn-sm btn-outline-secondary px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Receipts History
                </a>
                <a href="{{ route('vouchers.create') }}" class="btn btn-sm btn-outline-primary px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-sliders me-1"></i> All Vouchers
                </a>
            </div>
        </div>

        <form id="receivePaymentForm" method="POST" action="{{ route('vouchers.receive_payment.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="rvid" value="{{ $nextRvid }}">

            <!-- Primary Payment Details Section -->
            <div class="rp-section-card">
                <div class="rp-section-header">
                    <h3 class="rp-section-title">
                        <i class="fa-solid fa-user-check text-success"></i> Customer & Payment Details
                    </h3>
                    <div id="customerBalBadge" class="d-none">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill" style="font-size: 13px;">
                            <i class="fa-solid fa-scale-unbalanced me-1"></i> Current Due: <strong id="lblCustomerBal" class="ms-1">PKR 0.00</strong>
                        </span>
                    </div>
                </div>

                <!-- Basic Fields Grid -->
                <div class="row g-3">
                    <!-- Customer Selection -->
                    <div class="col-md-4" id="customerIdCol">
                        <label class="rp-label"><i class="fa-solid fa-user-tie text-muted me-1"></i> Customer <span class="req">*</span></label>
                        <select name="customer_id" id="customerId" class="form-select rp-select select2" required>
                            <option value="">-- Choose Customer --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" 
                                        data-phone="{{ $c->mobile }}" 
                                        data-balance="{{ $c->current_balance }}"
                                        {{ (isset($selectedCustomerId) && $selectedCustomerId == $c->id) ? 'selected' : '' }}>
                                    {{ $c->customer_name }} (C-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}) 
                                    @if($c->current_balance > 0)
                                        - Due: PKR {{ number_format($c->current_balance, 2) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Deposit To Account -->
                    <div class="col-md-4" id="depositAccountCol">
                        <label class="rp-label" id="lblDepositAccount"><i class="fa-solid fa-building-columns text-muted me-1"></i> Deposit To Account (Cash/Bank) <span class="req">*</span></label>
                        <select name="deposit_account_id" id="depositAccountId" class="form-select rp-select select2" required>
                            <option value="">-- Select Cash/Bank Account --</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->title }} ({{ $acc->account_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Mode -->
                    <div class="col-md-2" id="paymentModeCol">
                        <label class="rp-label"><i class="fa-solid fa-wallet text-muted me-1"></i> Payment Mode <span class="req">*</span></label>
                        <select name="payment_mode" id="paymentMode" class="form-select rp-select" required>
                            <option value="Cash" selected>Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <!-- Payment Date -->
                    <div class="col-md-2" id="paymentDateCol">
                        <label class="rp-label"><i class="fa-regular fa-calendar text-muted me-1"></i> Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date" id="paymentDate" class="form-control rp-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Reference / Receipt No -->
                    <div class="col-md-4" id="referenceNoCol">
                        <label class="rp-label" id="lblReferenceNo"><i class="fa-solid fa-receipt text-muted me-1"></i> Reference / Receipt #</label>
                        <input type="text" name="reference_no" id="referenceNo" class="form-control rp-input" placeholder="e.g. Slip # / Memo / Receipt #">
                    </div>

                    <!-- Remarks / Narration -->
                    <div class="col-md-5" id="remarksCol">
                        <label class="rp-label"><i class="fa-regular fa-comment-dots text-muted me-1"></i> Remarks / Narration</label>
                        <input type="text" name="remarks" id="remarks" class="form-control rp-input" placeholder="e.g. Received payment against invoice dues">
                    </div>

                    <!-- Attachment / Slip -->
                    <div class="col-md-3" id="attachmentCol">
                        <label class="rp-label"><i class="fa-solid fa-paperclip text-muted me-1"></i> Attachment (Slip / PDF)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control rp-input rp-file-input" accept="image/*,.pdf,.doc,.docx">
                    </div>
                </div>

                <!-- Cheque Details Section (Shown when Payment Mode = Cheque) -->
                <div class="row g-3 mt-2 p-3 d-none rounded-3 border" id="chequeDetailsRow" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                    <div class="col-12 pb-1 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-money-check-dollar text-success"></i>
                            <strong class="text-success small text-uppercase">Cheque Details</strong>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">Cheque Mode Active</span>
                    </div>
                    <div class="col-md-4">
                        <label class="rp-label">Cheque # <span class="req">*</span></label>
                        <input type="text" name="cheque_no" id="chequeNo" class="form-control rp-input" placeholder="e.g. CHQ-990812">
                    </div>
                    <div class="col-md-4">
                        <label class="rp-label">Cheque Date <span class="req">*</span></label>
                        <input type="date" name="cheque_date" id="chequeDate" class="form-control rp-input" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="rp-label">Customer's Bank Name</label>
                        <input type="text" name="cheque_bank" id="chequeBank" class="form-control rp-input" placeholder="e.g. Meezan Bank / HBL / UBL">
                    </div>
                </div>

                <!-- Financial Settlement Calculation Bar -->
                <div class="rp-calc-panel mt-3">
                    <div class="row g-3 align-items-center">
                        <!-- Cash / Bank Received Amount -->
                        <div class="col-lg-3 col-md-6">
                            <label class="rp-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-hand-holding-dollar text-success me-1"></i> Received Cash / Bank <span class="req">*</span>
                            </label>
                            <div class="input-group rp-amount-group">
                                <span class="input-group-text rp-addon-primary">PKR</span>
                                <input type="number" step="0.01" min="0" name="total_amount" id="totalAmount" 
                                       class="form-control rp-input rp-input-amount text-end" 
                                       placeholder="0.00" required autocomplete="off">
                            </div>
                            <small class="rp-sub-hint d-block mt-1">Actual money received in cash/bank</small>
                        </div>

                        <!-- Tax / WHT Deduction -->
                        <div class="col-lg-3 col-md-6">
                            <label class="rp-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-percent text-warning me-1"></i> (+) Tax / WHT (PKR)
                            </label>
                            <div class="input-group rp-amount-group">
                                <span class="input-group-text rp-addon-warning">PKR</span>
                                <input type="number" step="0.01" min="0" name="tax_amount" id="taxAmount" 
                                       class="form-control rp-input rp-input-amount text-end" 
                                       placeholder="0.00" autocomplete="off" value="0.00">
                            </div>
                            <small class="rp-sub-hint d-block mt-1">Customer advance tax deduction</small>
                        </div>

                        <!-- Discount Given -->
                        <div class="col-lg-3 col-md-6">
                            <label class="rp-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-tag text-danger me-1"></i> (+) Discount Given (PKR)
                            </label>
                            <div class="input-group rp-amount-group">
                                <span class="input-group-text rp-addon-danger">PKR</span>
                                <input type="number" step="0.01" min="0" name="discount_amount" id="discountAmount" 
                                       class="form-control rp-input rp-input-amount text-end" 
                                       placeholder="0.00" autocomplete="off" value="0.00">
                            </div>
                            <small class="rp-sub-hint d-block mt-1">Discount allowed on settlement</small>
                        </div>

                        <!-- Total Settlement Amount Display -->
                        <div class="col-lg-3 col-md-6">
                            <label class="rp-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-calculator text-success me-1"></i> (=) Total Settlement
                            </label>
                            <div class="rp-settlement-badge">
                                <div class="rp-settlement-content">
                                    <span class="rp-settlement-sub">Cash + Tax + Disc:</span>
                                    <span class="rp-settlement-value" id="lblTotalSettlementDisplay">PKR 0.00</span>
                                </div>
                            </div>
                            <small class="rp-sub-hint d-block mt-1 text-end">Total amount distributed to invoices</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="metric-card blue">
                        <div class="metric-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div>
                            <div class="metric-label">Total Invoiced</div>
                            <div class="metric-value" id="cardTotalInvoiced">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="metric-card amber">
                        <div class="metric-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div>
                            <div class="metric-label">Outstanding Due</div>
                            <div class="metric-value text-danger" id="cardTotalDue">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="metric-card emerald">
                        <div class="metric-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                        <div>
                            <div class="metric-label">Allocated For Invoices</div>
                            <div class="metric-value text-success" id="cardTotalAllocated">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="metric-card purple">
                        <div class="metric-icon"><i class="fa-solid fa-vault"></i></div>
                        <div>
                            <div class="metric-label">Unallocated / Advance</div>
                            <div class="metric-value" id="cardRemainingExcess">PKR 0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Settlement Table Card -->
            <div class="rp-section-card">
                <div class="rp-section-header">
                    <div>
                        <h3 class="rp-section-title">
                            <i class="fa-solid fa-list-check text-success"></i> Unpaid Customer Invoices
                            <span class="badge bg-success ms-2 rounded-pill px-2 py-1" id="badgeSelectedCount" style="font-size: 11px;">0 / 0 Selected</span>
                        </h3>
                        <div class="text-muted small mt-1">Check specific invoices to allocate payment only to them, or select all.</div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Mode Selector (Equal vs FIFO) -->
                        <div class="distribution-mode-group me-1">
                            <button type="button" class="mode-btn active" data-mode="equal" id="modeBtnEqual" title="Equally distribute total amount among selected invoices">
                                <i class="fa-solid fa-scale-balanced"></i> Equal Share
                            </button>
                            <button type="button" class="mode-btn" data-mode="fifo" id="modeBtnFifo" title="FIFO: Selected oldest invoices get paid first in order">
                                <i class="fa-solid fa-arrow-down-1-9"></i> FIFO (Oldest)
                            </button>
                        </div>

                        <!-- Quick Action Buttons -->
                        <button type="button" class="btn btn-outline-success btn-action-tool" id="btnApplyEqual" title="Equally split amount among selected invoices">
                            <i class="fa-solid fa-scale-balanced"></i> Distribute
                        </button>
                        <button type="button" class="btn btn-success text-white btn-action-tool shadow-sm" id="btnPayAllDues" title="Pay 100% outstanding balance of selected invoices">
                            <i class="fa-solid fa-check-double"></i> Pay Selected Dues
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-action-tool" id="btnClearAllocations" title="Clear all allocations">
                            <i class="fa-solid fa-eraser"></i> Clear
                        </button>
                    </div>
                </div>

                <div class="table-responsive rp-table-wrapper">
                    <table class="rp-table" id="invoicesTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="checkAllInvoices" class="custom-row-chk" checked title="Select / Deselect All">
                                </th>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Invoice Total</th>
                                <th class="text-end">Already Paid</th>
                                <th class="text-end text-danger">Outstanding Due</th>
                                <th class="text-end" style="width: 160px;">Payment (PKR)</th>
                                <th class="text-end">Remaining Due</th>
                                <th class="text-center" style="width: 110px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="invoicesTableBody">
                            <tr>
                                <td colspan="11" class="empty-invoices">
                                    <i class="fa-solid fa-arrow-pointer d-block"></i>
                                    Please select a customer above to load unpaid invoices.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot id="invoicesTableFoot" class="d-none">
                            <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0;">
                                <td colspan="5" class="text-end py-3">Total Summary (Selected Dues):</td>
                                <td class="text-end py-3" id="footTotalPaid">0.00</td>
                                <td class="text-end text-danger py-3" id="footTotalDue">0.00</td>
                                <td class="text-end text-success py-3" id="footTotalAlloc">0.00</td>
                                <td class="text-end py-3" id="footTotalRem">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Bottom Action Footer -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-3 border-top">
                <div>
                    <a href="{{ route('all_recepit_vochers') }}" class="btn btn-light px-4 py-2 border rounded-3 text-secondary fw-semibold">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="save" class="btn btn-outline-success px-4 py-2 fw-bold" id="btnSubmitSave" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Payment
                    </button>
                    <button type="submit" name="action" value="save_and_print" class="btn btn-success px-4 py-2 text-white fw-bold shadow-sm" id="btnSubmitPrint" style="border-radius: 8px;">
                        <i class="fa-solid fa-print me-1"></i> Save & Print Receipt
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    let currentInvoices = [];
    let distributionMode = 'equal'; // 'equal' or 'fifo'

    $('.distribution-mode-group .mode-btn').on('click', function() {
        $('.distribution-mode-group .mode-btn').removeClass('active');
        $(this).addClass('active');
        distributionMode = $(this).data('mode');

        if (distributionMode === 'equal') {
            runEqualAllocation();
        } else {
            runFifoAllocation();
        }
    });

    function handlePaymentModeChange() {
        let mode = $('#paymentMode').val();
        if (mode === 'Cheque') {
            $('#chequeDetailsRow').removeClass('d-none');
            $('#depositAccountCol').addClass('d-none');
            $('#depositAccountId').prop('required', false);
            $('#customerIdCol').removeClass('col-md-4').addClass('col-md-8');
            $('#paymentDateCol').removeClass('col-md-3').addClass('col-md-2');
            $('#paymentModeCol').removeClass('col-md-3').addClass('col-md-2');
            $('#lblReferenceNo').html('<i class="fa-solid fa-receipt text-muted me-1"></i> Additional Reference #');
            $('#referenceNo').attr('placeholder', 'e.g. Deposit Slip # / Memo');
            $('#chequeNo').prop('required', true);
        } else if (mode === 'Bank') {
            $('#chequeDetailsRow').addClass('d-none');
            $('#depositAccountCol').removeClass('d-none');
            $('#depositAccountId').prop('required', true);
            $('#customerIdCol').removeClass('col-md-8').addClass('col-md-4');
            $('#paymentDateCol').removeClass('col-md-3').addClass('col-md-2');
            $('#paymentModeCol').removeClass('col-md-3').addClass('col-md-2');
            $('#lblDepositAccount').html('<i class="fa-solid fa-building-columns text-muted me-1"></i> Deposit To Bank Account <span class="req">*</span>');
            $('#lblReferenceNo').html('<i class="fa-solid fa-receipt text-muted me-1"></i> Transaction / Ref #');
            $('#referenceNo').attr('placeholder', 'e.g. Online Transfer / TR-88123');
            $('#chequeNo').prop('required', false);
        } else {
            $('#chequeDetailsRow').addClass('d-none');
            $('#depositAccountCol').removeClass('d-none');
            $('#depositAccountId').prop('required', true);
            $('#customerIdCol').removeClass('col-md-8').addClass('col-md-4');
            $('#paymentDateCol').removeClass('col-md-3').addClass('col-md-2');
            $('#paymentModeCol').removeClass('col-md-3').addClass('col-md-2');
            $('#lblDepositAccount').html('<i class="fa-solid fa-building-columns text-muted me-1"></i> Deposit To Account (Cash/Bank) <span class="req">*</span>');
            $('#lblReferenceNo').html('<i class="fa-solid fa-receipt text-muted me-1"></i> Reference / Receipt #');
            $('#referenceNo').attr('placeholder', 'e.g. Slip # / Memo / Receipt #');
            $('#chequeNo').prop('required', false);
        }
    }

    $(document).on('change', '#paymentMode', function() {
        handlePaymentModeChange();
    });

    // Run on load in case Cheque is restored or default
    handlePaymentModeChange();

    function handleCustomerChange() {
        let customerId = $('#customerId').val();
        let opt = $('#customerId').find(':selected');
        let bal = parseFloat(opt.data('balance')) || 0;

        if (customerId) {
            $('#customerBalBadge').removeClass('d-none');
            $('#lblCustomerBal').text('PKR ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            loadCustomerInvoices(customerId);
        } else {
            $('#customerBalBadge').addClass('d-none');
            $('#badgeSelectedCount').text('0 / 0 Selected');
            renderEmptyInvoices('Please select a customer above to load unpaid invoices.');
            currentInvoices = [];
            recalcSummary();
        }
    }

    // Support standard change and Select2 events
    $(document).on('change select2:select', '#customerId', function() {
        handleCustomerChange();
    });

    // Initial calculations on load
    updateTotalSettlementDisplay();

    // Auto-trigger if preselected
    if ($('#customerId').val()) {
        handleCustomerChange();
    }

    function loadCustomerInvoices(customerId) {
        $('#invoicesTableBody').html(`
            <tr>
                <td colspan="11" class="empty-invoices">
                    <i class="fa-solid fa-spinner fa-spin d-block text-success"></i>
                    Fetching unpaid invoices...
                </td>
            </tr>
        `);

        let url = "{{ url('/vouchers/customer-unpaid-invoices') }}/" + customerId;
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.invoices && res.invoices.length > 0) {
                    currentInvoices = res.invoices;
                    renderInvoicesTable(currentInvoices);
                    $('#invoicesTableFoot').removeClass('d-none');
                    updateSelectedBadge();
                    
                    let enteredAmount = parseFloat($('#totalAmount').val()) || 0;
                    if (enteredAmount > 0) {
                        applyCurrentDistribution();
                    } else {
                        recalcSummary();
                    }
                } else {
                    currentInvoices = [];
                    $('#badgeSelectedCount').text('0 / 0 Selected');
                    renderEmptyInvoices('No outstanding unpaid invoices found for this customer.');
                    $('#invoicesTableFoot').addClass('d-none');
                    recalcSummary();
                }
            },
            error: function(xhr) {
                console.error("Failed to load customer invoices:", xhr);
                renderEmptyInvoices('Failed to load invoices. Please try again.');
                $('#invoicesTableFoot').addClass('d-none');
            }
        });
    }

    function renderEmptyInvoices(msg) {
        $('#invoicesTableBody').html(`
            <tr>
                <td colspan="11" class="empty-invoices">
                    <i class="fa-solid fa-circle-info d-block"></i>
                    ${msg}
                </td>
            </tr>
        `);
    }

    function renderInvoicesTable(invoices) {
        let html = '';
        invoices.forEach(function(inv, idx) {
            html += `
                <tr class="invoice-row" data-id="${inv.id}" data-due="${inv.raw_due}" data-total="${inv.raw_total}" data-paid="${inv.raw_paid}">
                    <td class="text-center align-middle">
                        <input type="checkbox" class="custom-row-chk row-chk" data-id="${inv.id}" checked title="Include this invoice in payment">
                    </td>
                    <td class="text-center align-middle text-muted fw-semibold">${idx + 1}</td>
                    <td class="align-middle">
                        <span class="rp-inv-code">${inv.invoice_no}</span>
                    </td>
                    <td class="align-middle"><span class="text-secondary">${inv.date}</span></td>
                    <td class="align-middle">
                        <span class="text-secondary">${inv.due_date || '-'}</span>
                        ${inv.days_old ? `<span class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">${inv.days_old}d</span>` : ''}
                    </td>
                    <td class="text-end align-middle fw-medium">${parseFloat(inv.raw_total).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end align-middle text-muted">${parseFloat(inv.raw_paid).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end align-middle fw-bold text-danger inv-due-cell">${parseFloat(inv.raw_due).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end align-middle">
                        <input type="number" step="0.01" min="0" max="${inv.raw_due}" 
                               name="allocations[${inv.id}]" 
                               class="alloc-input" 
                               value="0.00" 
                               data-due="${inv.raw_due}">
                    </td>
                    <td class="text-end align-middle fw-bold inv-rem-cell text-muted">${parseFloat(inv.raw_due).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn-pay-full btnPayFull" title="Pay full outstanding amount for this invoice">
                            <i class="fa-solid fa-check me-1"></i> Pay Full
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#invoicesTableBody').html(html);
        $('#checkAllInvoices').prop('checked', true);
    }

    function updateSelectedBadge() {
        let total = $('.row-chk').length;
        let selected = $('.row-chk:checked').length;
        $('#badgeSelectedCount').text(`${selected} / ${total} Selected`);
    }

    // Master Checkbox Toggle
    $('#checkAllInvoices').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.row-chk').prop('checked', isChecked);
        $('.invoice-row').each(function() {
            let input = $(this).find('.alloc-input');
            if (isChecked) {
                $(this).removeClass('row-disabled');
                input.prop('disabled', false);
            } else {
                $(this).addClass('row-disabled').removeClass('row-allocated');
                input.val('0.00').prop('disabled', true);
                let due = parseFloat($(this).data('due')) || 0;
                $(this).find('.inv-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            }
        });
        updateSelectedBadge();
        applyCurrentDistribution();
    });

    // Individual Row Checkbox Toggle
    $(document).on('change', '.row-chk', function() {
        let row = $(this).closest('.invoice-row');
        let isChecked = $(this).is(':checked');
        let input = row.find('.alloc-input');

        if (isChecked) {
            row.removeClass('row-disabled');
            input.prop('disabled', false);
        } else {
            row.addClass('row-disabled').removeClass('row-allocated');
            input.val('0.00').prop('disabled', true);
            let due = parseFloat(row.data('due')) || 0;
            row.find('.inv-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        }

        let allCount = $('.row-chk').length;
        let checkedCount = $('.row-chk:checked').length;
        $('#checkAllInvoices').prop('checked', allCount === checkedCount);
        updateSelectedBadge();
        applyCurrentDistribution();
    });

    function applyCurrentDistribution() {
        if (distributionMode === 'equal') {
            runEqualAllocation();
        } else {
            runFifoAllocation();
        }
    }

    function getTotalSettlementAmount() {
        let cash = parseFloat($('#totalAmount').val()) || 0;
        let tax = parseFloat($('#taxAmount').val()) || 0;
        let disc = parseFloat($('#discountAmount').val()) || 0;
        return Math.max(0, cash + tax + disc);
    }

    function updateTotalSettlementDisplay() {
        let total = getTotalSettlementAmount();
        $('#lblTotalSettlementDisplay').text('PKR ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    // Amount, Tax or Discount change triggers total settlement update and distribution
    $(document).on('input keyup change paste', '#totalAmount, #taxAmount, #discountAmount', function() {
        updateTotalSettlementDisplay();
        applyCurrentDistribution();
    });

    // Manual Allocation Input in Table Rows
    $(document).on('input keyup change', '.alloc-input', function() {
        let row = $(this).closest('.invoice-row');
        let due = parseFloat(row.data('due')) || 0;
        let val = parseFloat($(this).val()) || 0;

        if (val > due) {
            val = due;
            $(this).val(val.toFixed(2));
        } else if (val < 0) {
            val = 0;
            $(this).val('0.00');
        }

        let rem = Math.max(0, due - val);
        row.find('.inv-rem-cell').text(rem.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        if (val > 0) {
            row.addClass('row-allocated');
            row.find('.row-chk').prop('checked', true);
            row.removeClass('row-disabled');
            $(this).prop('disabled', false);
        } else {
            row.removeClass('row-allocated');
        }

        updateSelectedBadge();

        // Check sum of allocations, sync totalAmount if manual edit exceeds
        let sumAllocated = 0;
        $('.alloc-input').each(function() {
            if (!$(this).prop('disabled')) {
                sumAllocated += (parseFloat($(this).val()) || 0);
            }
        });

        let currentSettlement = getTotalSettlementAmount();
        if (currentSettlement === 0 || sumAllocated > currentSettlement) {
            let tax = parseFloat($('#taxAmount').val()) || 0;
            let disc = parseFloat($('#discountAmount').val()) || 0;
            let requiredCash = Math.max(0, sumAllocated - tax - disc);
            $('#totalAmount').val(requiredCash.toFixed(2));
            updateTotalSettlementDisplay();
        }

        recalcSummary();
    });

    // Pay Full Button on a single Row
    $(document).on('click', '.btnPayFull', function() {
        let row = $(this).closest('.invoice-row');
        let due = parseFloat(row.data('due')) || 0;
        row.find('.row-chk').prop('checked', true).trigger('change');
        row.find('.alloc-input').val(due.toFixed(2)).trigger('change');
    });

    // Distribute Equally Button
    $('#btnApplyEqual').on('click', function() {
        $('#modeBtnEqual').trigger('click');
        runEqualAllocation();
    });

    // Pay Selected Dues Button (Allocates full dues for currently checked invoices and sets cash received)
    $('#btnPayAllDues').on('click', function() {
        let totalDueSum = 0;
        $('.invoice-row').each(function() {
            let isChecked = $(this).find('.row-chk').is(':checked');
            if (isChecked) {
                let due = parseFloat($(this).data('due')) || 0;
                totalDueSum += due;
                $(this).find('.alloc-input').val(due.toFixed(2)).prop('disabled', false);
                $(this).addClass('row-allocated');
                $(this).find('.inv-rem-cell').text('0.00');
            } else {
                $(this).find('.alloc-input').val('0.00').prop('disabled', true);
                $(this).removeClass('row-allocated');
            }
        });
        let tax = parseFloat($('#taxAmount').val()) || 0;
        let disc = parseFloat($('#discountAmount').val()) || 0;
        let netCash = Math.max(0, totalDueSum - tax - disc);
        $('#totalAmount').val(netCash.toFixed(2));
        updateTotalSettlementDisplay();
        recalcSummary();
    });

    // Clear Allocations Button
    $('#btnClearAllocations').on('click', function() {
        $('.alloc-input').val('0.00');
        $('.invoice-row').removeClass('row-allocated');
        $('.invoice-row').each(function() {
            let due = parseFloat($(this).data('due')) || 0;
            $(this).find('.inv-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        });
        recalcSummary();
    });

    // Equal Allocation Function (Strictly among SELECTED/CHECKED invoices based on TOTAL SETTLEMENT)
    function runEqualAllocation() {
        let totalAmt = getTotalSettlementAmount();
        let selectedRows = $('.invoice-row').filter(function() {
            return $(this).find('.row-chk').is(':checked');
        });

        // Unselected rows get 0
        $('.invoice-row').not(selectedRows).each(function() {
            $(this).find('.alloc-input').val('0.00');
            $(this).removeClass('row-allocated');
            let due = parseFloat($(this).data('due')) || 0;
            $(this).find('.inv-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        });

        if (selectedRows.length === 0) {
            recalcSummary();
            return;
        }

        if (totalAmt <= 0) {
            selectedRows.each(function() {
                $(this).find('.alloc-input').val('0.00');
                $(this).removeClass('row-allocated');
                let due = parseFloat($(this).data('due')) || 0;
                $(this).find('.inv-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            });
            recalcSummary();
            return;
        }

        let unallocatedRows = [];
        selectedRows.each(function() {
            let due = parseFloat($(this).data('due')) || 0;
            unallocatedRows.push({
                row: $(this),
                due: due,
                allocated: 0
            });
        });

        let remaining = totalAmt;
        let activeRows = [...unallocatedRows];

        while (remaining > 0.009 && activeRows.length > 0) {
            let equalShare = remaining / activeRows.length;
            let stillActive = [];
            let allocatedInThisRound = 0;

            for (let item of activeRows) {
                let currentAlloc = item.allocated;
                let needed = item.due - currentAlloc;

                if (needed <= 0.009) {
                    continue;
                }

                let give = Math.min(equalShare, needed);
                item.allocated += give;
                allocatedInThisRound += give;

                if (item.allocated < item.due - 0.009) {
                    stillActive.push(item);
                }
            }

            if (allocatedInThisRound === 0) {
                break;
            }

            remaining = Math.max(0, remaining - allocatedInThisRound);
            activeRows = stillActive;
        }

        // Apply allocated values to selected row inputs
        for (let item of unallocatedRows) {
            let alloc = item.allocated;
            let due = item.due;
            let rem = Math.max(0, due - alloc);

            if (alloc > 0) {
                item.row.find('.alloc-input').val(alloc.toFixed(2));
                item.row.addClass('row-allocated');
            } else {
                item.row.find('.alloc-input').val('0.00');
                item.row.removeClass('row-allocated');
            }

            item.row.find('.inv-rem-cell').text(rem.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        }

        recalcSummary();
    }

    // FIFO Allocation Function (Strictly among SELECTED/CHECKED invoices based on TOTAL SETTLEMENT)
    function runFifoAllocation() {
        let totalAmt = getTotalSettlementAmount();
        let remaining = totalAmt;

        $('.invoice-row').each(function() {
            let isChecked = $(this).find('.row-chk').is(':checked');
            let due = parseFloat($(this).data('due')) || 0;

            if (isChecked && remaining > 0) {
                let alloc = Math.min(remaining, due);
                if (alloc > 0) {
                    $(this).find('.alloc-input').val(alloc.toFixed(2));
                    $(this).addClass('row-allocated');
                } else {
                    $(this).find('.alloc-input').val('0.00');
                    $(this).removeClass('row-allocated');
                }
                let rem = Math.max(0, due - alloc);
                $(this).find('.inv-rem-cell').text(rem.toLocaleString('en-US', { minimumFractionDigits: 2 }));
                remaining = Math.max(0, remaining - alloc);
            } else {
                $(this).find('.alloc-input').val('0.00');
                $(this).removeClass('row-allocated');
                $(this).find('.inv-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            }
        });

        recalcSummary();
    }

    function recalcSummary() {
        let sumTotalNet = 0;
        let sumTotalPaid = 0;
        let sumTotalDue = 0;
        let sumAllocated = 0;

        $('.invoice-row').each(function() {
            let total = parseFloat($(this).data('total')) || 0;
            let paid = parseFloat($(this).data('paid')) || 0;
            let due = parseFloat($(this).data('due')) || 0;
            let alloc = parseFloat($(this).find('.alloc-input').val()) || 0;

            sumTotalNet += total;
            sumTotalPaid += paid;
            sumTotalDue += due;
            sumAllocated += alloc;
        });

        let totalSettlement = getTotalSettlementAmount();
        let excessOrAdvance = Math.max(0, totalSettlement - sumAllocated);

        // Update Top Metric Cards
        $('#cardTotalInvoiced').text('PKR ' + sumTotalNet.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#cardTotalDue').text('PKR ' + sumTotalDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#cardTotalAllocated').text('PKR ' + sumAllocated.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#cardRemainingExcess').text('PKR ' + excessOrAdvance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Update Table Foot
        $('#footTotalPaid').text(sumTotalPaid.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#footTotalDue').text(sumTotalDue.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#footTotalAlloc').text(sumAllocated.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        $('#footTotalRem').text(Math.max(0, sumTotalDue - sumAllocated).toLocaleString('en-US', { minimumFractionDigits: 2 }));
    }

    // Form Submission Handler
    $('#receivePaymentForm').on('submit', function(e) {
        e.preventDefault();

        let customerId = $('#customerId').val();
        let depositAccId = $('#depositAccountId').val();
        let cashAmt = parseFloat($('#totalAmount').val()) || 0;
        let taxAmt = parseFloat($('#taxAmount').val()) || 0;
        let discAmt = parseFloat($('#discountAmount').val()) || 0;
        let totalSettlement = cashAmt + taxAmt + discAmt;

        if (!customerId) {
            Swal.fire({ icon: 'warning', title: 'Customer Required', text: 'Please select a customer.' });
            return;
        }
        let payMode = $('#paymentMode').val();

        if (payMode !== 'Cheque' && !depositAccId) {
            Swal.fire({ icon: 'warning', title: 'Account Required', text: 'Please select the Deposit To Account (Cash/Bank).' });
            return;
        }
        if (totalSettlement <= 0) {
            Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Please enter received amount, tax, or discount.' });
            return;
        }

        if (payMode === 'Cheque') {
            let chqNo = $('#chequeNo').val().trim();
            if (!chqNo) {
                Swal.fire({ icon: 'warning', title: 'Cheque # Required', text: 'Please enter the Cheque number for Cheque payment mode.' });
                $('#chequeNo').focus();
                return;
            }
        }

        let customerName = $('#customerId').find(':selected').text().trim();
        let clickedBtn = $(document.activeElement);
        let actionVal = clickedBtn.val() || 'save_and_print';
        let formElem = document.getElementById('receivePaymentForm');
        let formData = new FormData(formElem);

        let confirmDesc = `Received Cash/Bank: PKR ${cashAmt.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        if (taxAmt > 0) confirmDesc += ` | Tax: PKR ${taxAmt.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        if (discAmt > 0) confirmDesc += ` | Discount: PKR ${discAmt.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        confirmDesc += ` (Total Settlement: PKR ${totalSettlement.toLocaleString('en-US', {minimumFractionDigits: 2})}) from ${customerName}?`;

        window.showConfirmPopup({
            title: 'Receive & Settle Invoices?',
            text: confirmDesc,
            confirmBtnText: '<i class="fa-solid fa-check-circle me-1"></i> Yes, Post Receipt'
        }, function() {
            let $submitBtns = $('#btnSubmitSave, #btnSubmitPrint');
            $submitBtns.prop('disabled', true);

            Swal.fire({
                title: 'Posting Receipt...',
                text: 'Settling customer invoices and updating ledger.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('vouchers.receive_payment.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Payment received and invoices settled successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (actionVal === 'save_and_print' && response.print_url) {
                            window.open(response.print_url, '_blank');
                            window.location.href = "{{ route('all_recepit_vochers') }}";
                        } else {
                            window.location.href = "{{ route('all_recepit_vochers') }}";
                        }
                    });
                } else {
                    $submitBtns.prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to save voucher.' });
                }
            },
            error: function(xhr) {
                $submitBtns.prop('disabled', false);
                let err = 'An error occurred while saving the voucher.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        err = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        err = xhr.responseJSON.error;
                    }
                    if (xhr.responseJSON.errors) {
                        let fieldErrors = [];
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            fieldErrors.push(Array.isArray(val) ? val.join(' ') : val);
                        });
                        if (fieldErrors.length > 0) {
                            err = fieldErrors.join('<br>');
                        }
                    }
                }
                Swal.fire({ icon: 'error', title: 'Failed to Save', html: err });
            }
        });
        });
    });
});
</script>
@endsection
