@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --mp-primary: #4f46e5;
        --mp-primary-dark: #4338ca;
        --mp-accent: #06b6d4;
        --mp-border: #e2e8f0;
        --mp-bg-subtle: #f8fafc;
        --mp-card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }

    .mp-container {
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 60px;
    }

    .mp-header-card {
        background: linear-gradient(135deg, #ffffff 0%, #eef2ff 100%);
        border: 1px solid #e0e7ff;
        border-radius: 14px;
        box-shadow: var(--mp-card-shadow);
        padding: 20px 24px;
        margin-bottom: 20px;
    }

    .mp-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .mp-badge-draft {
        background: #fef3c7;
        color: #92400e;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #fde68a;
        text-transform: uppercase;
    }

    .mp-badge-voucher {
        background: #e0e7ff;
        color: #3730a3;
        font-weight: 700;
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 6px;
        border: 1px solid #c7d2fe;
        font-family: monospace;
    }

    .mp-section-card {
        background: #ffffff;
        border: 1px solid var(--mp-border);
        border-radius: 12px;
        box-shadow: var(--mp-card-shadow);
        padding: 22px;
        margin-bottom: 20px;
    }

    .mp-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }

    .mp-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .mp-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }

    .mp-label .req {
        color: #ef4444;
        margin-left: 2px;
    }

    .mp-input, .mp-select {
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: #0f172a;
        width: 100%;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .mp-input:focus, .mp-select:focus {
        border-color: var(--mp-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        outline: none;
    }

    .mp-amount-hero {
        font-size: 20px;
        font-weight: 700;
        color: #4338ca;
        background: #eef2ff;
        border: 2px solid #c7d2fe;
    }

    /* Metric Summary Badges */
    .metric-card {
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid transparent;
        transition: transform 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-2px);
    }
    .metric-card.indigo {
        background: #eef2ff;
        border-color: #c7d2fe;
        color: #3730a3;
    }
    .metric-card.amber {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }
    .metric-card.emerald {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .metric-card.purple {
        background: #faf5ff;
        border-color: #e9d5ff;
        color: #6b21a8;
    }
    .metric-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: rgba(255, 255, 255, 0.7);
    }
    .metric-value {
        font-size: 17px;
        font-weight: 800;
        line-height: 1.2;
    }
    .metric-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.85;
    }

    /* Bills Table */
    .mp-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .mp-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .mp-table td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .mp-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .mp-table tbody tr.row-allocated {
        background-color: #eef2ff !important;
    }
    .mp-table tbody tr.row-disabled {
        opacity: 0.45;
        background-color: #f8fafc;
    }

    .alloc-input {
        width: 140px;
        text-align: right;
        font-weight: 700;
        font-size: 14px;
        color: #4338ca;
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
        background: #ffffff;
        transition: all 0.2s;
    }
    .alloc-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        outline: none;
    }
    .alloc-input:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .row-allocated .alloc-input {
        border-color: #6366f1;
        background-color: #ffffff;
    }

    /* Segmented Mode Control */
    .distribution-mode-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 3px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .distribution-mode-group .mode-btn {
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .distribution-mode-group .mode-btn.active {
        background: #ffffff;
        color: #4f46e5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Checkbox styling */
    .custom-row-chk {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #4f46e5;
    }

    /* Action buttons */
    .btn-pay-full {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 700;
        border-radius: 6px;
        background: #ede9fe;
        color: #5b21b6;
        border: 1px solid #ddd6fe;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .btn-pay-full:hover {
        background: #6366f1;
        color: #ffffff;
        border-color: #6366f1;
    }

    /* Empty state */
    .empty-bills {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }
    .empty-bills i {
        font-size: 40px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        font-size: 13px !important;
        color: #0f172a !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
</style>

<div class="content-wrapper">
    <div class="mp-container">

        <!-- Top Header Card -->
        <div class="mp-header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="mp-title">
                        <i class="fa-solid fa-money-bill-transfer text-primary"></i> Make Payment / Pay Bills
                    </h1>
                    <span class="mp-badge-voucher">{{ $nextPvid }}</span>
                    <span class="mp-badge-draft">Draft</span>
                </div>
                <p class="text-muted mb-0 small">
                    Vendor Bill Settlement with Specific Multi-Select, Equal & FIFO Distribution.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('all_Payment_vochers') }}" class="btn btn-sm btn-outline-secondary px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-list me-1"></i> Payments History
                </a>
                <a href="{{ route('vouchers.create') }}" class="btn btn-sm btn-outline-primary px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-sliders me-1"></i> All Vouchers
                </a>
            </div>
        </div>

        <form id="makePaymentForm" method="POST" action="{{ route('vouchers.make_payment.store') }}">
            @csrf
            <input type="hidden" name="pvid" value="{{ $nextPvid }}">

            <!-- Primary Payment Details -->
            <div class="mp-section-card">
                <div class="mp-section-header">
                    <h3 class="mp-section-title">
                        <i class="fa-solid fa-truck-field text-primary"></i> Vendor & Payment Details
                    </h3>
                    <div id="vendorBalBadge" class="d-none">
                        <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 13px;">
                            Current Payable Due: <strong id="lblVendorBal" class="text-danger">PKR 0.00</strong>
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Vendor Selection -->
                    <div class="col-md-4">
                        <label class="mp-label">Vendor <span class="req">*</span></label>
                        <select name="vendor_id" id="vendorId" class="form-select mp-select select2" required>
                            <option value="">-- Choose Vendor --</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}" 
                                        data-phone="{{ $v->phone }}" 
                                        data-balance="{{ $v->current_balance }}"
                                        {{ (isset($selectedVendorId) && $selectedVendorId == $v->id) ? 'selected' : '' }}>
                                    {{ $v->name }} (V-{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}) 
                                    @if($v->current_balance > 0)
                                        - Due: PKR {{ number_format($v->current_balance, 2) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Date -->
                    <div class="col-md-2">
                        <label class="mp-label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date" id="paymentDate" class="form-control mp-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Payment Mode -->
                    <div class="col-md-2">
                        <label class="mp-label">Payment Mode <span class="req">*</span></label>
                        <select name="payment_mode" id="paymentMode" class="form-select mp-select" required>
                            <option value="Cash" selected>Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>

                    <!-- Paid From Account -->
                    <div class="col-md-4">
                        <label class="mp-label">Paid From Account (Cash/Bank) <span class="req">*</span></label>
                        <select name="paid_from_account_id" id="paidFromAccountId" class="form-select mp-select select2" required>
                            <option value="">-- Select Cash/Bank Account --</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->title }} ({{ $acc->account_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reference / Cheque No -->
                    <div class="col-md-4">
                        <label class="mp-label">Reference / Cheque #</label>
                        <input type="text" name="reference_no" id="referenceNo" class="form-control mp-input" placeholder="e.g. CHQ-88219 / TXN-0012">
                    </div>

                    <!-- Total Payment Amount -->
                    <div class="col-md-4">
                        <label class="mp-label">Total Payment Amount (PKR) <span class="req">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white fw-bold">PKR</span>
                            <input type="number" step="0.01" min="0.01" name="total_amount" id="totalAmount" 
                                   class="form-control mp-input mp-amount-hero text-end" 
                                   placeholder="0.00" required autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 11px;" id="lblAmountHint">Amount will be distributed strictly among checked/selected bills.</small>
                    </div>

                    <!-- Remarks / Narration -->
                    <div class="col-md-4">
                        <label class="mp-label">Remarks / Narration</label>
                        <input type="text" name="remarks" id="remarks" class="form-control mp-input" placeholder="e.g. Paid vendor bill dues via cheque">
                    </div>
                </div>
            </div>

            <!-- Summary Cards Bar -->
            <div class="row g-3 mb-3">
                <div class="col-md-3 col-6">
                    <div class="metric-card indigo">
                        <div class="metric-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div>
                            <div class="metric-label">Total Billed</div>
                            <div class="metric-value" id="cardTotalBilled">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="metric-card amber">
                        <div class="metric-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div>
                            <div class="metric-label">Payable Due</div>
                            <div class="metric-value" id="cardTotalDue">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="metric-card emerald">
                        <div class="metric-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                        <div>
                            <div class="metric-label">Allocated For Bills</div>
                            <div class="metric-value" id="cardTotalAllocated">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="metric-card purple">
                        <div class="metric-icon"><i class="fa-solid fa-vault"></i></div>
                        <div>
                            <div class="metric-label">Unallocated / Excess</div>
                            <div class="metric-value" id="cardRemainingExcess">PKR 0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bills Settlement Table Card -->
            <div class="mp-section-card">
                <div class="mp-section-header">
                    <div>
                        <h3 class="mp-section-title">
                            <i class="fa-solid fa-list-check text-primary"></i> Unpaid Vendor Bills
                            <span class="badge bg-primary ms-2" id="badgeSelectedCount" style="font-size: 11px;">0 / 0 Selected</span>
                        </h3>
                        <div class="text-muted small">Check specific bills to allocate payment only to them, or select all.</div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Mode Selector (Equal vs FIFO) -->
                        <div class="distribution-mode-group me-1">
                            <button type="button" class="mode-btn active" data-mode="equal" id="modeBtnEqual" title="Equally distribute total amount among selected bills">
                                <i class="fa-solid fa-scale-balanced"></i> Equal Share
                            </button>
                            <button type="button" class="mode-btn" data-mode="fifo" id="modeBtnFifo" title="FIFO: Selected oldest bills get paid first in order">
                                <i class="fa-solid fa-arrow-down-1-9"></i> FIFO (Oldest First)
                            </button>
                        </div>

                        <!-- Quick Action Buttons -->
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnApplyEqual" title="Equally split amount among selected bills">
                            <i class="fa-solid fa-scale-balanced me-1"></i> Distribute
                        </button>
                        <button type="button" class="btn btn-sm btn-primary text-white fw-bold" id="btnPayAllDues" title="Pay 100% outstanding balance of selected bills">
                            <i class="fa-solid fa-check-double me-1"></i> Pay Selected Dues
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearAllocations" title="Clear all allocations">
                            <i class="fa-solid fa-eraser me-1"></i> Clear
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="mp-table" id="billsTable">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center">
                                    <input type="checkbox" id="checkAllBills" class="custom-row-chk" checked title="Select / Deselect All">
                                </th>
                                <th style="width: 35px;">#</th>
                                <th>Bill #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Bill Total</th>
                                <th class="text-end">Already Paid</th>
                                <th class="text-end text-danger">Outstanding Due</th>
                                <th class="text-end" style="width: 170px;">Payment (PKR)</th>
                                <th class="text-end">Remaining Due</th>
                                <th class="text-center" style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="billsTableBody">
                            <tr>
                                <td colspan="11" class="empty-bills">
                                    <i class="fa-solid fa-arrow-pointer d-block"></i>
                                    Please select a vendor above to load unpaid bills.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot id="billsTableFoot" class="d-none">
                            <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0;">
                                <td colspan="5" class="text-end">Total Summary (Selected Dues):</td>
                                <td class="text-end" id="footTotalPaid">0.00</td>
                                <td class="text-end text-danger" id="footTotalDue">0.00</td>
                                <td class="text-end text-primary" id="footTotalAlloc">0.00</td>
                                <td class="text-end" id="footTotalRem">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Bottom Action Footer -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-3 border-top">
                <div>
                    <a href="{{ route('all_Payment_vochers') }}" class="btn btn-light px-4 py-2 border">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="save" class="btn btn-primary px-4 py-2 text-white fw-bold shadow-sm" id="btnSubmitSave" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Payment
                    </button>
                    <button type="submit" name="action" value="save_and_print" class="btn btn-indigo px-4 py-2 text-white fw-bold shadow-sm" id="btnSubmitPrint" style="border-radius: 8px; background: #4f46e5;">
                        <i class="fa-solid fa-print me-1"></i> Save & Print Voucher
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

    let currentBills = [];
    let distributionMode = 'equal'; // 'equal' or 'fifo'

    $('.distribution-mode-group .mode-btn').on('click', function() {
        $('.distribution-mode-group .mode-btn').removeClass('active');
        $(this).addClass('active');
        distributionMode = $(this).data('mode');

        if (distributionMode === 'equal') {
            $('#lblAmountHint').text('Equal Mode: Entered amount will be distributed equally among selected bills.');
            runEqualAllocation();
        } else {
            $('#lblAmountHint').text('FIFO Mode: Entered amount will settle selected oldest bills first.');
            runFifoAllocation();
        }
    });

    function handleVendorChange() {
        let vendorId = $('#vendorId').val();
        let opt = $('#vendorId').find(':selected');
        let bal = parseFloat(opt.data('balance')) || 0;

        if (vendorId) {
            $('#vendorBalBadge').removeClass('d-none');
            $('#lblVendorBal').text('PKR ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            loadVendorBills(vendorId);
        } else {
            $('#vendorBalBadge').addClass('d-none');
            $('#badgeBillCount').text('0 Bills');
            $('#badgeSelectedCount').text('0 / 0 Selected');
            renderEmptyBills('Please select a vendor above to load unpaid bills.');
            currentBills = [];
            recalcSummary();
        }
    }

    // Support standard change and Select2 events
    $(document).on('change select2:select', '#vendorId', function() {
        handleVendorChange();
    });

    // Auto-trigger if preselected
    if ($('#vendorId').val()) {
        handleVendorChange();
    }

    function loadVendorBills(vendorId) {
        $('#billsTableBody').html(`
            <tr>
                <td colspan="11" class="empty-bills">
                    <i class="fa-solid fa-spinner fa-spin d-block text-primary"></i>
                    Fetching unpaid purchase bills...
                </td>
            </tr>
        `);

        let url = "{{ url('/vouchers/vendor-unpaid-bills') }}/" + vendorId;
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.bills && res.bills.length > 0) {
                    currentBills = res.bills;
                    renderBillsTable(currentBills);
                    $('#billsTableFoot').removeClass('d-none');
                    updateSelectedBadge();
                    
                    let enteredAmount = parseFloat($('#totalAmount').val()) || 0;
                    if (enteredAmount > 0) {
                        applyCurrentDistribution();
                    } else {
                        recalcSummary();
                    }
                } else {
                    currentBills = [];
                    $('#badgeSelectedCount').text('0 / 0 Selected');
                    renderEmptyBills('No outstanding unpaid purchase bills found for this vendor.');
                    $('#billsTableFoot').addClass('d-none');
                    recalcSummary();
                }
            },
            error: function(xhr) {
                console.error("Failed to load vendor bills:", xhr);
                renderEmptyBills('Failed to load purchase bills. Please try again.');
                $('#billsTableFoot').addClass('d-none');
            }
        });
    }

    function renderEmptyBills(msg) {
        $('#billsTableBody').html(`
            <tr>
                <td colspan="11" class="empty-bills">
                    <i class="fa-solid fa-circle-info d-block"></i>
                    ${msg}
                </td>
            </tr>
        `);
    }

    function renderBillsTable(bills) {
        let html = '';
        bills.forEach(function(bill, idx) {
            html += `
                <tr class="bill-row" data-id="${bill.id}" data-due="${bill.raw_due}" data-total="${bill.raw_total}" data-paid="${bill.raw_paid}">
                    <td class="text-center">
                        <input type="checkbox" class="custom-row-chk row-chk" data-id="${bill.id}" checked title="Include this bill in payment">
                    </td>
                    <td>${idx + 1}</td>
                    <td>
                        <strong class="text-dark">${bill.bill_no}</strong>
                    </td>
                    <td><small class="text-muted">${bill.date}</small></td>
                    <td>
                        <small class="text-muted">${bill.due_date || '-'}</small>
                        ${bill.days_old ? `<span class="badge bg-light text-secondary ms-1">${bill.days_old}d</span>` : ''}
                    </td>
                    <td class="text-end">${parseFloat(bill.raw_total).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end">${parseFloat(bill.raw_paid).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end fw-bold text-danger bill-due-cell">${parseFloat(bill.raw_due).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end">
                        <input type="number" step="0.01" min="0" max="${bill.raw_due}" 
                               name="allocations[${bill.id}]" 
                               class="alloc-input form-control-sm" 
                               value="0.00" 
                               data-due="${bill.raw_due}">
                    </td>
                    <td class="text-end fw-bold bill-rem-cell text-muted">${parseFloat(bill.raw_due).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-center">
                        <button type="button" class="btn-pay-full btnPayFull" title="Pay full outstanding amount for this bill">
                            Pay Full
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#billsTableBody').html(html);
        $('#checkAllBills').prop('checked', true);
    }

    function updateSelectedBadge() {
        let total = $('.row-chk').length;
        let selected = $('.row-chk:checked').length;
        $('#badgeSelectedCount').text(`${selected} / ${total} Selected`);
    }

    // Master Checkbox Toggle
    $('#checkAllBills').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.row-chk').prop('checked', isChecked);
        $('.bill-row').each(function() {
            let input = $(this).find('.alloc-input');
            if (isChecked) {
                $(this).removeClass('row-disabled');
                input.prop('disabled', false);
            } else {
                $(this).addClass('row-disabled').removeClass('row-allocated');
                input.val('0.00').prop('disabled', true);
                let due = parseFloat($(this).data('due')) || 0;
                $(this).find('.bill-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            }
        });
        updateSelectedBadge();
        applyCurrentDistribution();
    });

    // Individual Row Checkbox Toggle
    $(document).on('change', '.row-chk', function() {
        let row = $(this).closest('.bill-row');
        let isChecked = $(this).is(':checked');
        let input = row.find('.alloc-input');

        if (isChecked) {
            row.removeClass('row-disabled');
            input.prop('disabled', false);
        } else {
            row.addClass('row-disabled').removeClass('row-allocated');
            input.val('0.00').prop('disabled', true);
            let due = parseFloat(row.data('due')) || 0;
            row.find('.bill-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        }

        let allCount = $('.row-chk').length;
        let checkedCount = $('.row-chk:checked').length;
        $('#checkAllBills').prop('checked', allCount === checkedCount);
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

    // Amount change in Total Payment Amount triggers distribution strictly across selected bills
    $(document).on('input keyup change paste', '#totalAmount', function() {
        applyCurrentDistribution();
    });

    // Manual Allocation Input in Table Rows
    $(document).on('input keyup change', '.alloc-input', function() {
        let row = $(this).closest('.bill-row');
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
        row.find('.bill-rem-cell').text(rem.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        if (val > 0) {
            row.addClass('row-allocated');
            row.find('.row-chk').prop('checked', true);
            row.removeClass('row-disabled');
            $(this).prop('disabled', false);
        } else {
            row.removeClass('row-allocated');
        }

        updateSelectedBadge();

        // Check sum of allocations, sync totalAmount if 0 or manual edit
        let sumAllocated = 0;
        $('.alloc-input').each(function() {
            if (!$(this).prop('disabled')) {
                sumAllocated += (parseFloat($(this).val()) || 0);
            }
        });

        let currentTotal = parseFloat($('#totalAmount').val()) || 0;
        if (currentTotal === 0 || sumAllocated > currentTotal) {
            $('#totalAmount').val(sumAllocated.toFixed(2));
        }

        recalcSummary();
    });

    // Pay Full Button on a single Row
    $(document).on('click', '.btnPayFull', function() {
        let row = $(this).closest('.bill-row');
        let due = parseFloat(row.data('due')) || 0;
        row.find('.row-chk').prop('checked', true).trigger('change');
        row.find('.alloc-input').val(due.toFixed(2)).trigger('change');
    });

    // Distribute Equally Button
    $('#btnApplyEqual').on('click', function() {
        $('#modeBtnEqual').trigger('click');
        runEqualAllocation();
    });

    // Pay Selected Dues Button (Allocates full dues for currently checked bills and sets totalAmount)
    $('#btnPayAllDues').on('click', function() {
        let totalDueSum = 0;
        $('.bill-row').each(function() {
            let isChecked = $(this).find('.row-chk').is(':checked');
            if (isChecked) {
                let due = parseFloat($(this).data('due')) || 0;
                totalDueSum += due;
                $(this).find('.alloc-input').val(due.toFixed(2)).prop('disabled', false);
                $(this).addClass('row-allocated');
                $(this).find('.bill-rem-cell').text('0.00');
            } else {
                $(this).find('.alloc-input').val('0.00').prop('disabled', true);
                $(this).removeClass('row-allocated');
            }
        });
        $('#totalAmount').val(totalDueSum.toFixed(2));
        recalcSummary();
    });

    // Clear Allocations Button
    $('#btnClearAllocations').on('click', function() {
        $('.alloc-input').val('0.00');
        $('.bill-row').removeClass('row-allocated');
        $('.bill-row').each(function() {
            let due = parseFloat($(this).data('due')) || 0;
            $(this).find('.bill-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        });
        recalcSummary();
    });

    // Equal Allocation Function (Strictly among SELECTED/CHECKED bills)
    function runEqualAllocation() {
        let totalAmt = parseFloat($('#totalAmount').val()) || 0;
        let selectedRows = $('.bill-row').filter(function() {
            return $(this).find('.row-chk').is(':checked');
        });

        // Unselected rows get 0
        $('.bill-row').not(selectedRows).each(function() {
            $(this).find('.alloc-input').val('0.00');
            $(this).removeClass('row-allocated');
            let due = parseFloat($(this).data('due')) || 0;
            $(this).find('.bill-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
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
                $(this).find('.bill-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
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

            item.row.find('.bill-rem-cell').text(rem.toLocaleString('en-US', { minimumFractionDigits: 2 }));
        }

        recalcSummary();
    }

    // FIFO Allocation Function (Strictly among SELECTED/CHECKED bills)
    function runFifoAllocation() {
        let totalAmt = parseFloat($('#totalAmount').val()) || 0;
        let remaining = totalAmt;

        $('.bill-row').each(function() {
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
                $(this).find('.bill-rem-cell').text(rem.toLocaleString('en-US', { minimumFractionDigits: 2 }));
                remaining = Math.max(0, remaining - alloc);
            } else {
                $(this).find('.alloc-input').val('0.00');
                $(this).removeClass('row-allocated');
                $(this).find('.bill-rem-cell').text(due.toLocaleString('en-US', { minimumFractionDigits: 2 }));
            }
        });

        recalcSummary();
    }

    function recalcSummary() {
        let sumTotalNet = 0;
        let sumTotalPaid = 0;
        let sumTotalDue = 0;
        let sumAllocated = 0;

        $('.bill-row').each(function() {
            let total = parseFloat($(this).data('total')) || 0;
            let paid = parseFloat($(this).data('paid')) || 0;
            let due = parseFloat($(this).data('due')) || 0;
            let alloc = parseFloat($(this).find('.alloc-input').val()) || 0;

            sumTotalNet += total;
            sumTotalPaid += paid;
            sumTotalDue += due;
            sumAllocated += alloc;
        });

        let totalRecAmt = parseFloat($('#totalAmount').val()) || 0;
        let excessOrAdvance = Math.max(0, totalRecAmt - sumAllocated);

        // Update Top Metric Cards
        $('#cardTotalBilled').text('PKR ' + sumTotalNet.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
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
    $('#makePaymentForm').on('submit', function(e) {
        e.preventDefault();

        let vendorId = $('#vendorId').val();
        let paidAccId = $('#paidFromAccountId').val();
        let totalAmt = parseFloat($('#totalAmount').val()) || 0;

        if (!vendorId) {
            Swal.fire({ icon: 'warning', title: 'Vendor Required', text: 'Please select a vendor.' });
            return;
        }
        if (!paidAccId) {
            Swal.fire({ icon: 'warning', title: 'Account Required', text: 'Please select the Paid From Account (Cash/Bank).' });
            return;
        }
        if (totalAmt <= 0) {
            Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Please enter a valid Total Payment Amount.' });
            return;
        }

        let vendorName = $('#vendorId').find(':selected').text().trim();
        let clickedBtn = $(document.activeElement);
        let actionVal = clickedBtn.val() || 'save_and_print';
        let formData = $(this).serialize();

        window.showConfirmPopup({
            title: 'Pay & Settle Bills?',
            text: `Are you sure you want to disburse PKR ${totalAmt.toLocaleString('en-US', {minimumFractionDigits: 2})} to ${vendorName} and settle purchase bills?`,
            confirmBtnText: '<i class="fa-solid fa-check-circle me-1"></i> Yes, Post Payment'
        }, function() {
            let $submitBtns = $('#btnSubmitSave, #btnSubmitPrint');
            $submitBtns.prop('disabled', true);

            Swal.fire({
                title: 'Posting Payment...',
                text: 'Settling purchase bills and updating vendor ledger.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('vouchers.make_payment.store') }}",
                type: "POST",
                data: formData,
                dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Payment made and bills settled successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (actionVal === 'save_and_print' && response.print_url) {
                            window.open(response.print_url, '_blank');
                            window.location.href = "{{ route('all_Payment_vochers') }}";
                        } else {
                            window.location.href = "{{ route('all_Payment_vochers') }}";
                        }
                    });
                } else {
                    $submitBtns.prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Failed to save voucher.' });
                }
            },
            error: function(xhr) {
                $submitBtns.prop('disabled', false);
                let err = xhr.responseJSON ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'An error occurred.';
                Swal.fire({ icon: 'error', title: 'Failed to Save', text: err });
            }
        });
    });
});
</script>
@endsection
