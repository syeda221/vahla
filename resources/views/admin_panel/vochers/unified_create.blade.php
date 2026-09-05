@extends('admin_panel.layout.app')
@section('content')

{{-- FontAwesome & Bootstrap Icons CDN (Failsafe) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

<style>
:root {
    --voucher-primary: #2563eb;
    --voucher-primary-hover: #1d4ed8;
    --voucher-primary-light: rgba(37,99,235,0.08);
    --voucher-bg: #f8fafc;
    --voucher-card-bg: #ffffff;
    --voucher-border: #d1d5db;
    --voucher-border-focus: #2563eb;
    --voucher-text: #1e293b;
    --voucher-text-muted: #64748b;
    --voucher-input-bg: #ffffff;
    --voucher-input-border: #b0b7c3;
    --voucher-success: #16a34a;
    --voucher-danger: #dc2626;
    --voucher-card-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
}

.main-content {
    padding-bottom: 40px;
}

/* ========= VOUCHER TYPE CARDS ========= */
.voucher-types {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 991px) {
    .voucher-types {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}

@media (max-width: 576px) {
    .voucher-types {
        grid-template-columns: repeat(1, 1fr);
        gap: 10px;
    }
}

.voucher-type-btn {
    position: relative;
    background: var(--voucher-card-bg);
    border: 2px solid var(--voucher-border);
    border-radius: 12px;
    padding: 22px 14px 18px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--voucher-text-muted);
    user-select: none;
}

.voucher-type-btn:hover {
    border-color: var(--voucher-primary);
    color: var(--voucher-text);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.12);
}

.voucher-type-btn.active {
    border-color: var(--voucher-primary);
    background: var(--voucher-primary-light);
    color: var(--voucher-primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
}

.voucher-type-btn .v-icon {
    font-size: 28px;
    display: block;
    margin-bottom: 8px;
    color: var(--voucher-text-muted);
    transition: color 0.2s ease;
}

.voucher-type-btn.active .v-icon {
    color: var(--voucher-primary);
}

.voucher-type-btn .v-label {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: block;
}

/* ========= FORM CARD ========= */
.voucher-form-card {
    background: var(--voucher-card-bg);
    border: 1px solid var(--voucher-border);
    border-radius: 12px;
    padding: 26px 30px;
    box-shadow: var(--voucher-card-shadow);
}

.voucher-form-card .card-title {
    color: var(--voucher-text);
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--voucher-border);
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ========= FORM SECTIONS ========= */
.voucher-form-section {
    display: none;
}

.voucher-form-section.active {
    display: block;
}

/* ========= SECTION HEADINGS ========= */
.sub-heading {
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.sub-heading-primary {
    color: var(--voucher-primary);
}

.sub-heading-danger {
    color: #dc2626;
}

/* ========= FORM CONTROLS ========= */
.form-label {
    color: var(--voucher-text);
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 6px;
    letter-spacing: 0.2px;
}

.form-control, .form-select {
    background: var(--voucher-input-bg);
    border: 1px solid var(--voucher-input-border);
    color: var(--voucher-text);
    border-radius: 8px;
    padding: 9px 13px;
    font-size: 13.5px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--voucher-primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    background: var(--voucher-input-bg);
    color: var(--voucher-text);
    outline: none;
}

.form-control::placeholder {
    color: #94a3b8;
}

.form-control[readonly] {
    background: #f8fafc;
    color: var(--voucher-text-muted);
    cursor: default;
}

/* ========= SELECT2 CUSTOM STYLING ========= */
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--single {
    background: var(--voucher-input-bg) !important;
    border: 1px solid var(--voucher-input-border) !important;
    border-radius: 8px !important;
    height: 42px !important;
    padding: 6px 12px;
    display: flex;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--voucher-text) !important;
    line-height: 28px !important;
    font-size: 13.5px !important;
    padding-left: 0 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
    right: 8px !important;
}

.select2-dropdown {
    background: #ffffff !important;
    border: 1px solid var(--voucher-input-border) !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;
    z-index: 9999 !important;
}

.select2-results__option {
    color: var(--voucher-text) !important;
    padding: 8px 12px !important;
    font-size: 13.5px !important;
}

.select2-results__option--highlighted {
    background: var(--voucher-primary) !important;
    color: #fff !important;
}

/* ========= PARTY TYPE RADIOS ========= */
.party-type-group {
    display: inline-flex;
    gap: 4px;
    background: #f1f5f9;
    border: 1px solid var(--voucher-input-border);
    border-radius: 10px;
    padding: 4px;
}

.party-type-option {
    position: relative;
}

.party-type-option input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.party-type-option label {
    display: block;
    padding: 7px 18px;
    border-radius: 7px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--voucher-text-muted);
    transition: all 0.2s ease;
    white-space: nowrap;
    margin-bottom: 0;
}

.party-type-option input:checked + label {
    background: var(--voucher-primary);
    color: #fff;
    box-shadow: 0 2px 8px rgba(37,99,235,0.25);
}

/* ========= BUTTONS ========= */
.btn-voucher {
    background: var(--voucher-primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 30px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
}

.btn-voucher:hover {
    background: var(--voucher-primary-hover);
    color: #fff;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

.btn-add-row {
    border: 1px dashed var(--voucher-input-border) !important;
    color: var(--voucher-text-muted) !important;
    background: #fff !important;
    border-radius: 8px !important;
    padding: 8px 20px !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    transition: all 0.2s ease !important;
}

.btn-add-row:hover {
    border-color: var(--voucher-primary) !important;
    color: var(--voucher-primary) !important;
    background: var(--voucher-primary-light) !important;
}

/* ========= LOADING SPINNER ========= */
.spinner-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.45);
    z-index: 99999;
    justify-content: center;
    align-items: center;
}

.spinner-overlay.show {
    display: flex;
}

.spinner-box {
    background: var(--voucher-card-bg);
    border-radius: 12px;
    padding: 32px 44px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.spinner-box .spinner {
    width: 38px;
    height: 38px;
    border: 3px solid #e2e8f0;
    border-top-color: var(--voucher-primary);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    margin: 0 auto 12px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.spinner-box p {
    color: var(--voucher-text-muted);
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

/* ========= SECTION CARDS ========= */
.section-card {
    background: #f8fafc;
    border: 1px solid var(--voucher-border);
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .voucher-types {
        grid-template-columns: repeat(1, 1fr);
        gap: 10px;
    }
    .voucher-form-card {
        padding: 16px;
    }
}
</style>

<div class="main-content">
    <div class="container-fluid px-3 px-md-4">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color:var(--voucher-text);">Create Voucher</h3>
                <p class="text-muted mb-0" style="font-size:13px;">Select a voucher type to begin</p>
            </div>
            <div>
                <a href="{{ route('voucher.history') }}" class="btn btn-outline-secondary px-3 py-2" style="border-radius:8px; font-size:13px; font-weight:600;">
                    <i class="fas fa-list-check me-1"></i> All Vouchers History
                </a>
            </div>
        </div>

        {{-- Voucher Type Toggle Cards (4 Main Vouchers) --}}
        <div class="voucher-types" id="voucherTypeSelector">
            <div class="voucher-type-btn active" data-type="expense">
                <i class="fas fa-file-invoice-dollar v-icon"></i>
                <span class="v-label">Expense</span>
            </div>
            <div class="voucher-type-btn" data-type="payment_in">
                <i class="fas fa-arrow-down v-icon"></i>
                <span class="v-label">Payment In</span>
            </div>
            <div class="voucher-type-btn" data-type="payment_out">
                <i class="fas fa-arrow-up v-icon"></i>
                <span class="v-label">Payment Out</span>
            </div>
            <div class="voucher-type-btn" data-type="party_transfer">
                <i class="fas fa-right-left v-icon"></i>
                <span class="v-label">Party To Party</span>
            </div>
        </div>

        {{-- Form Container Card --}}
        <div class="voucher-form-card">
            <div id="formTitle" class="card-title">
                <i class="fas fa-file-invoice-dollar text-primary"></i> <span>Expense Voucher</span>
            </div>

            {{-- ==================== 1. EXPENSE VOUCHER ==================== --}}
            <div class="voucher-form-section active" id="form-expense">
                <form class="voucher-form" data-action="{{ route('store_expense_vochers') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="vendor_type" value="account">
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label">EVID</label>
                            <input type="text" class="form-control" value="{{ $nextEvid ?? 'EVID-Auto' }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Source Account (Cash/Bank) <span class="text-danger">*</span></label>
                            <select name="vendor_id" class="form-select select2-account" required>
                                <option value="">Search account...</option>
                                @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" data-code="{{ $acc->account_code }}" data-balance="{{ $acc->current_balance ?? $acc->opening_balance }}">
                                    {{ $acc->account_code }} - {{ $acc->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Account Code</label>
                            <input type="text" name="tel" class="form-control" readonly placeholder="Auto Code">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Balance</label>
                            <input type="text" id="expBalance" class="form-control" readonly placeholder="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Memo / Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered text-center align-middle" id="expenseTable">
                            <thead class="table-light">
                            <tr>
                                <th class="text-start" style="width:65%;">EXPENSE CATEGORY</th>
                                <th style="width:25%;">AMOUNT</th>
                                <th style="width:10%;">ACTION</th>
                            </tr>
                            </thead>
                            <tbody id="expenseRows">
                                <tr>
                                    <td>
                                        <select name="row_account_id[]" class="form-select rowAccountSub select2-cat" required>
                                            <option value="">Select Expense Category</option>
                                            @foreach($expenseCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name ?? $cat->title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="narration_id[]" value="">
                                        <input type="hidden" name="narration_text[]" value="Expense">
                                    </td>
                                    <td><input name="amount[]" type="number" step="0.01" class="form-control text-end exp-amount" placeholder="0.00" required></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th class="text-end">Total:</th>
                                    <th class="text-end"><span id="expenseTotal">0.00</span><input type="hidden" name="total_amount" id="expenseTotalInput" value="0"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <button type="button" class="btn btn-add-row add-expense-row">
                                <i class="fas fa-plus me-1"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2 btn-open-cat-modal" style="border-radius:8px; padding:8px 16px;">
                                <i class="fas fa-folder-plus me-1"></i> Add Category
                            </button>
                        </div>
                        <button type="submit" class="btn btn-voucher">
                            <i class="fas fa-check-circle me-1"></i> Save Expense
                        </button>
                    </div>
                </form>
            </div>

            {{-- ==================== 2. PAYMENT IN VOUCHER ==================== --}}
            <div class="voucher-form-section" id="form-payment_in">
                <form class="voucher-form" data-action="{{ route('store_rec_vochers') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="receipt_date" id="pi_receipt_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="entry_date" value="{{ date('Y-m-d') }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="$('#pi_receipt_date').val(this.value)" required>
                        </div>
                    </div>

                    {{-- Party Section --}}
                    <div class="section-card">
                        <h6 class="sub-heading sub-heading-primary"><i class="fas fa-user-check"></i> Received From</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <div class="party-type-group">
                                    <div class="party-type-option">
                                        <input type="radio" name="vendor_type" id="pi_customer" value="customer" class="pi-party-type" checked>
                                        <label for="pi_customer">Customer</label>
                                    </div>
                                    <div class="party-type-option">
                                        <input type="radio" name="vendor_type" id="pi_vendor" value="vendor" class="pi-party-type">
                                        <label for="pi_vendor">Vendor</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div id="pi_customer_wrapper">
                                    <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                                    <select name="vendor_id" id="pi_customer_select" class="form-select select2-customer" required>
                                        <option value="">Search customer...</option>
                                        @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }} @if(!empty($c->mobile)) ({{ $c->mobile }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="pi_vendor_wrapper" style="display:none;">
                                    <label class="form-label">Select Vendor <span class="text-danger">*</span></label>
                                    <select name="vendor_id_vendor" id="pi_vendor_select" class="form-select select2-vendor" disabled>
                                        <option value="">Search vendor...</option>
                                        @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} @if(!empty($v->phone)) ({{ $v->phone }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Deposit To (Cash/Bank Account) <span class="text-danger">*</span></label>
                            <select name="row_account_id[]" class="form-select select2-account" required>
                                <option value="">Search cash/bank account...</option>
                                @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount[]" id="pi_amount" class="form-control" step="0.01" min="1" required placeholder="Enter amount" oninput="$('#pi_total_amount').val(this.value)">
                            <input type="hidden" name="total_amount" id="pi_total_amount" value="">
                            <input type="hidden" name="narration_id[]" value="">
                            <input type="hidden" name="narration_text[]" value="Payment Received">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Optional details">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-voucher">
                            <i class="fas fa-check-circle me-1"></i> Save Payment In
                        </button>
                    </div>
                </form>
            </div>

            {{-- ==================== 3. PAYMENT OUT VOUCHER ==================== --}}
            <div class="voucher-form-section" id="form-payment_out">
                <form class="voucher-form" data-action="{{ route('store_Pay_vochers') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="receipt_date" id="po_receipt_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="entry_date" value="{{ date('Y-m-d') }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" onchange="$('#po_receipt_date').val(this.value)" required>
                        </div>
                    </div>

                    {{-- Party Section --}}
                    <div class="section-card">
                        <h6 class="sub-heading sub-heading-danger"><i class="fas fa-user-tag"></i> Pay To</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <div class="party-type-group">
                                    <div class="party-type-option">
                                        <input type="radio" name="vendor_type_choice" id="po_vendor" value="vendor" class="po-party-type" checked>
                                        <label for="po_vendor">Vendor</label>
                                    </div>
                                    <div class="party-type-option">
                                        <input type="radio" name="vendor_type_choice" id="po_customer" value="customer" class="po-party-type">
                                        <label for="po_customer">Customer</label>
                                    </div>
                                </div>
                                <input type="hidden" name="vendor_type[]" id="po_vendor_type" value="vendor">
                            </div>
                            <div class="col-md-8">
                                <div id="po_vendor_wrapper">
                                    <label class="form-label">Select Vendor <span class="text-danger">*</span></label>
                                    <select name="vendor_id[]" id="po_vendor_select" class="form-select select2-vendor" required>
                                        <option value="">Search vendor...</option>
                                        @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} @if(!empty($v->phone)) ({{ $v->phone }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="po_customer_wrapper" style="display:none;">
                                    <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                                    <select name="vendor_id_cust" id="po_customer_select" class="form-select select2-customer" disabled>
                                        <option value="">Search customer...</option>
                                        @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }} @if(!empty($c->mobile)) ({{ $c->mobile }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Pay From (Cash/Bank Account) <span class="text-danger">*</span></label>
                            <select name="header_account_id" class="form-select select2-account" required>
                                <option value="">Search cash/bank account...</option>
                                @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount[]" id="po_amount" class="form-control" step="0.01" min="1" required placeholder="Enter amount" oninput="$('#po_total_amount').val(this.value)">
                            <input type="hidden" name="total_amount" id="po_total_amount" value="">
                            <input type="hidden" name="narration_id[]" value="">
                            <input type="hidden" name="narration_text[]" value="Payment Made">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Optional details">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-voucher">
                            <i class="fas fa-check-circle me-1"></i> Save Payment Out
                        </button>
                    </div>
                </form>
            </div>

            {{-- ==================== 4. PARTY TO PARTY TRANSFER ==================== --}}
            <div class="voucher-form-section" id="form-party_transfer">
                <form class="voucher-form" data-action="{{ route('store_party_transfer') }}" method="POST" novalidate>
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Transfer Date <span class="text-danger">*</span></label>
                            <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Voucher ID</label>
                            <input type="text" class="form-control" value="{{ $nextTvid ?? 'TVID-001' }}" readonly>
                        </div>
                    </div>

                    {{-- Two Party Selection Cards (Side-by-side) --}}
                    <div class="row g-3 mb-3">
                        {{-- Source Party (Deduct From) --}}
                        <div class="col-md-6">
                            <div class="section-card h-100" style="border-left: 3px solid #dc2626;">
                                <h6 class="sub-heading sub-heading-danger">
                                    <i class="fas fa-minus-circle text-danger"></i> Source Party (Deduct From)
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                    <div class="party-type-group w-100 d-flex">
                                        <div class="party-type-option flex-fill text-center">
                                            <input type="radio" name="source_party_type_choice" id="pt_src_customer" value="customer" class="pt-src-party-type" checked>
                                            <label for="pt_src_customer" class="w-100">Customer</label>
                                        </div>
                                        <div class="party-type-option flex-fill text-center">
                                            <input type="radio" name="source_party_type_choice" id="pt_src_vendor" value="vendor" class="pt-src-party-type">
                                            <label for="pt_src_vendor" class="w-100">Vendor</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="source_party_type" id="pt_source_party_type" value="customer">
                                </div>

                                <div id="pt_src_customer_wrapper">
                                    <label class="form-label">Select Party <span class="text-danger">*</span></label>
                                    <select name="source_party_id" id="pt_src_customer_select" class="form-select select2-customer" required>
                                        <option value="">Search customer...</option>
                                        @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }} @if(!empty($c->mobile)) ({{ $c->mobile }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="pt_src_vendor_wrapper" style="display:none;">
                                    <label class="form-label">Select Party <span class="text-danger">*</span></label>
                                    <select name="source_party_id_vendor" id="pt_src_vendor_select" class="form-select select2-vendor" disabled>
                                        <option value="">Search vendor...</option>
                                        @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} @if(!empty($v->phone)) ({{ $v->phone }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Destination Party (Transfer To) --}}
                        <div class="col-md-6">
                            <div class="section-card h-100" style="border-left: 3px solid #2563eb;">
                                <h6 class="sub-heading sub-heading-primary">
                                    <i class="fas fa-plus-circle text-primary"></i> Destination Party (Transfer To)
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                    <div class="party-type-group w-100 d-flex">
                                        <div class="party-type-option flex-fill text-center">
                                            <input type="radio" name="destination_party_type_choice" id="pt_dst_vendor" value="vendor" class="pt-dst-party-type" checked>
                                            <label for="pt_dst_vendor" class="w-100">Vendor</label>
                                        </div>
                                        <div class="party-type-option flex-fill text-center">
                                            <input type="radio" name="destination_party_type_choice" id="pt_dst_customer" value="customer" class="pt-dst-party-type">
                                            <label for="pt_dst_customer" class="w-100">Customer</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="destination_party_type" id="pt_destination_party_type" value="vendor">
                                </div>

                                <div id="pt_dst_vendor_wrapper">
                                    <label class="form-label">Select Party <span class="text-danger">*</span></label>
                                    <select name="destination_party_id" id="pt_dst_vendor_select" class="form-select select2-vendor" required>
                                        <option value="">Search vendor...</option>
                                        @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} @if(!empty($v->phone)) ({{ $v->phone }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="pt_dst_customer_wrapper" style="display:none;">
                                    <label class="form-label">Select Party <span class="text-danger">*</span></label>
                                    <select name="destination_party_id_customer" id="pt_dst_customer_select" class="form-select select2-customer" disabled>
                                        <option value="">Search customer...</option>
                                        @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }} @if(!empty($c->mobile)) ({{ $c->mobile }}) @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Amount and Remarks --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="pt_amount" class="form-control" placeholder="Enter amount" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-voucher px-4 py-2">
                            <i class="fas fa-check-circle me-1"></i> Process Transfer
                        </button>
                    </div>
                </form>
            </div>

        </div>{{-- /voucher-form-card --}}
    </div>
</div>

{{-- Loading Spinner Overlay --}}
<div class="spinner-overlay" id="loadingOverlay">
    <div class="spinner-box">
        <div class="spinner"></div>
        <p>Saving voucher...</p>
    </div>
</div>

{{-- ==================== ADD EXPENSE CATEGORY MODAL ==================== --}}
<div class="modal fade" id="expenseCategoryModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 10555;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="addExpenseCategoryForm">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title text-white fw-bold mb-0"><i class="fas fa-folder-plus me-2"></i> New Expense Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <label class="form-label fw-bold">Category Title <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Fuel Expense, Tea/Coffee, Rent">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i> Save Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
    function initVoucherApp() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initVoucherApp, 50);
            return;
        }

        var $ = jQuery;

        const formTitles = {
            expense:          '<i class="fas fa-file-invoice-dollar text-primary"></i> <span>Expense Voucher</span>',
            payment_in:       '<i class="fas fa-arrow-down text-primary"></i> <span>Payment In Voucher</span>',
            payment_out:      '<i class="fas fa-arrow-up text-danger"></i> <span>Payment Out Voucher</span>',
            party_transfer:   '<i class="fas fa-right-left text-primary"></i> <span>Party-to-Party Transfer</span>'
        };

        // ============== VOUCHER TYPE TOGGLE ==============
        $(document).on('click', '#voucherTypeSelector .voucher-type-btn', function(e) {
            e.preventDefault();
            var type = $(this).data('type');

            $('#voucherTypeSelector .voucher-type-btn').removeClass('active');
            $(this).addClass('active');

            $('.voucher-form-section').removeClass('active').hide();
            $('#form-' + type).addClass('active').show();

            $('#formTitle').html(formTitles[type] || '');
            initSelect2();
        });

        // ============== SELECT2 INITIALIZE ==============
        function initSelect2() {
            var $active = $('.voucher-form-section.active');
            $active.find('.select2-account, .select2-vendor, .select2-customer, .select2-cat').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
                $(this).select2({ width: '100%', dropdownParent: $(this).parent() });
            });
        }

        // ============== OPEN CATEGORY MODAL (CROSS BOOTSTRAP 4 & 5) ==============
        $(document).on('click', '.btn-open-cat-modal', function(e) {
            e.preventDefault();
            $('#expenseCategoryModal').modal('show');
        });

        // ============== PAYMENT IN: PARTY TYPE TOGGLE ==============
        $(document).on('change', '.pi-party-type', function() {
            var val = $(this).val();
            if (val === 'customer') {
                $('#pi_customer_wrapper').show();
                $('#pi_vendor_wrapper').hide();
                $('#pi_customer_select').prop('disabled', false).prop('required', true).attr('name', 'vendor_id');
                $('#pi_vendor_select').prop('disabled', true).prop('required', false).attr('name', 'vendor_id_disabled');
            } else {
                $('#pi_customer_wrapper').hide();
                $('#pi_vendor_wrapper').show();
                $('#pi_customer_select').prop('disabled', true).prop('required', false).attr('name', 'vendor_id_disabled');
                $('#pi_vendor_select').prop('disabled', false).prop('required', true).attr('name', 'vendor_id');
            }
            initSelect2();
        });

        // ============== PAYMENT OUT: PARTY TYPE TOGGLE ==============
        $(document).on('change', '.po-party-type', function() {
            var val = $(this).val();
            $('#po_vendor_type').val(val);
            if (val === 'vendor') {
                $('#po_vendor_wrapper').show();
                $('#po_customer_wrapper').hide();
                $('#po_vendor_select').prop('disabled', false).prop('required', true).attr('name', 'vendor_id[]');
                $('#po_customer_select').prop('disabled', true).prop('required', false).attr('name', 'vendor_id_disabled[]');
            } else {
                $('#po_vendor_wrapper').hide();
                $('#po_customer_wrapper').show();
                $('#po_vendor_select').prop('disabled', true).prop('required', false).attr('name', 'vendor_id_disabled[]');
                $('#po_customer_select').prop('disabled', false).prop('required', true).attr('name', 'vendor_id[]');
            }
            initSelect2();
        });

        // ============== PARTY TRANSFER: SOURCE PARTY TOGGLE ==============
        $(document).on('change', '.pt-src-party-type', function() {
            var val = $(this).val();
            $('#pt_source_party_type').val(val);
            if (val === 'customer') {
                $('#pt_src_customer_wrapper').show();
                $('#pt_src_vendor_wrapper').hide();
                $('#pt_src_customer_select').prop('disabled', false).prop('required', true).attr('name', 'source_party_id');
                $('#pt_src_vendor_select').prop('disabled', true).prop('required', false).attr('name', 'source_party_id_disabled');
            } else {
                $('#pt_src_customer_wrapper').hide();
                $('#pt_src_vendor_wrapper').show();
                $('#pt_src_customer_select').prop('disabled', true).prop('required', false).attr('name', 'source_party_id_disabled');
                $('#pt_src_vendor_select').prop('disabled', false).prop('required', true).attr('name', 'source_party_id');
            }
            initSelect2();
        });

        // ============== PARTY TRANSFER: DESTINATION PARTY TOGGLE ==============
        $(document).on('change', '.pt-dst-party-type', function() {
            var val = $(this).val();
            $('#pt_destination_party_type').val(val);
            if (val === 'vendor') {
                $('#pt_dst_vendor_wrapper').show();
                $('#pt_dst_customer_wrapper').hide();
                $('#pt_dst_vendor_select').prop('disabled', false).prop('required', true).attr('name', 'destination_party_id');
                $('#pt_dst_customer_select').prop('disabled', true).prop('required', false).attr('name', 'destination_party_id_disabled');
            } else {
                $('#pt_dst_vendor_wrapper').hide();
                $('#pt_dst_customer_wrapper').show();
                $('#pt_dst_vendor_select').prop('disabled', true).prop('required', false).attr('name', 'destination_party_id_disabled');
                $('#pt_dst_customer_select').prop('disabled', false).prop('required', true).attr('name', 'destination_party_id');
            }
            initSelect2();
        });

        // ============== EXPENSE: SOURCE ACCOUNT SELECT ==============
        $(document).on('change', '#form-expense select[name="vendor_id"]', function() {
            var $sel = $(this).find(':selected');
            var code = $sel.data('code') || '';
            var balance = $sel.data('balance');
            $('#form-expense input[name="tel"]').val(code);
            if (balance !== undefined && balance !== '') {
                $('#expBalance').val(parseFloat(balance).toFixed(2));
            } else {
                $('#expBalance').val('0.00');
            }
        });

        // ============== EXPENSE: DYNAMIC ROWS & CALCULATION ==============
        function calcExpenseTotal() {
            var total = 0;
            $('#expenseTable .exp-amount').each(function() {
                var val = parseFloat($(this).val()) || 0;
                total += val;
            });
            $('#expenseTotal').text(total.toFixed(2));
            $('#expenseTotalInput').val(total.toFixed(2));
        }

        $(document).on('input', '#expenseTable .exp-amount', calcExpenseTotal);

        $('.add-expense-row').on('click', function() {
            var options = $('#expenseTable tbody tr:first select.rowAccountSub').html();
            var row = `<tr>
                <td>
                    <select name="row_account_id[]" class="form-select rowAccountSub select2-cat" required>
                        ${options}
                    </select>
                    <input type="hidden" name="narration_id[]" value="">
                    <input type="hidden" name="narration_text[]" value="Expense">
                </td>
                <td><input name="amount[]" type="number" step="0.01" class="form-control text-end exp-amount" placeholder="0.00" required></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
            </tr>`;
            $('#expenseTable tbody').append(row);
            initSelect2();
        });

        $(document).on('click', '.removeRow', function() {
            if ($('#expenseTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calcExpenseTotal();
            } else {
                Swal.fire({ icon: 'info', title: 'Notice', text: 'At least one category row is required!' });
            }
        });

        // ============== ADD EXPENSE CATEGORY (AJAX) ==============
        $('#addExpenseCategoryForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ route("expense_categories.store") }}',
                method: 'POST',
                data: $form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(resp) {
                    var catName = $form.find('input[name="name"]').val();
                    var catId = resp.id || (resp.category ? resp.category.id : '');
                    $('.rowAccountSub').append('<option value="' + catId + '">' + catName + '</option>');
                    $('#expenseCategoryModal').modal('hide');
                    $form[0].reset();
                    Swal.fire({ icon: 'success', title: 'Added!', text: 'Expense category created successfully.', timer: 1500, showConfirmButton: false });
                },
                error: function(xhr) {
                    var msg = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html: msg });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Category');
                }
            });
        });

        // ============== FORM SUBMISSION (AJAX) ==============
        $('.voucher-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var action = $form.data('action');
            var formData = new FormData(this);

            $('#loadingOverlay').addClass('show');
            $form.find('button[type="submit"]').prop('disabled', true);

            $.ajax({
                url: action,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#loadingOverlay').removeClass('show');
                    $form.find('button[type="submit"]').prop('disabled', false);

                    var msg = response.message || response.success || 'Voucher saved successfully!';

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: msg,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        denyButtonColor: '#059669',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fas fa-print me-1"></i> Print Voucher',
                        denyButtonText: '<i class="fas fa-list me-1"></i> View Listing',
                        cancelButtonText: 'Create Another'
                    }).then((result) => {
                        if (result.isConfirmed && (response.print_url || response.voucher_id)) {
                            var printUrl = response.print_url || ('/print/' + response.voucher_id);
                            window.open(printUrl, '_blank');
                            window.location.reload();
                        } else if (result.isDenied && response.all_vouchers_url) {
                            window.location.href = response.all_vouchers_url;
                        } else {
                            window.location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    $('#loadingOverlay').removeClass('show');
                    $form.find('button[type="submit"]').prop('disabled', false);

                    var resp = xhr.responseJSON;
                    if (resp && resp.errors) {
                        var msg = '';
                        $.each(resp.errors, function(key, errs) {
                            msg += errs.join('<br>') + '<br>';
                        });
                        Swal.fire({ icon: 'error', title: 'Validation Error', html: msg });
                    } else if (resp && resp.error) {
                        Swal.fire({ icon: 'error', title: 'Error', text: resp.error });
                    } else if (resp && resp.message) {
                        Swal.fire({ icon: 'error', title: 'Error', text: resp.message });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
                    }
                }
            });
        });

        // Initialize on load
        initSelect2();
        calcExpenseTotal();
        $('#form-expense select[name="vendor_id"]').trigger('change');

        // URL param check to switch tab on load if requested
        var urlParams = new URLSearchParams(window.location.search);
        var reqType = urlParams.get('type');
        if (reqType && $('#voucherTypeSelector .voucher-type-btn[data-type="' + reqType + '"]').length) {
            $('#voucherTypeSelector .voucher-type-btn[data-type="' + reqType + '"]').trigger('click');
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initVoucherApp();
    } else {
        document.addEventListener('DOMContentLoaded', initVoucherApp);
    }
})();
</script>

@endsection
