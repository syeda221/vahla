@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --rp-primary: #059669;
        --rp-primary-dark: #047857;
        --rp-accent: #3b82f6;
        --rp-border: #e2e8f0;
        --rp-bg-subtle: #f8fafc;
        --rp-card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }

    .rp-container {
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 60px;
    }

    .rp-header-card {
        background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        border: 1px solid #dcfce7;
        border-radius: 14px;
        box-shadow: var(--rp-card-shadow);
        padding: 20px 24px;
        margin-bottom: 20px;
    }

    .rp-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .rp-badge-draft {
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

    .rp-badge-voucher {
        background: #ecfdf5;
        color: #065f46;
        font-weight: 700;
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 6px;
        border: 1px solid #a7f3d0;
        font-family: monospace;
    }

    .rp-section-card {
        background: #ffffff;
        border: 1px solid var(--rp-border);
        border-radius: 12px;
        box-shadow: var(--rp-card-shadow);
        padding: 22px;
        margin-bottom: 20px;
    }

    .rp-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }

    .rp-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rp-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }

    .rp-label .req {
        color: #ef4444;
        margin-left: 2px;
    }

    .rp-input, .rp-select {
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 13px;
        color: #0f172a;
        width: 100%;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .rp-input:focus, .rp-select:focus {
        border-color: var(--rp-primary);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        outline: none;
    }

    .rp-amount-hero {
        font-size: 20px;
        font-weight: 700;
        color: #047857;
        background: #f0fdf4;
        border: 2px solid #a7f3d0;
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
    .metric-card.blue {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1e40af;
    }
    .metric-card.emerald {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }
    .metric-card.amber {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
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

    /* Invoices Table */
    .rp-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .rp-table th {
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
    .rp-table td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .rp-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .rp-table tbody tr.row-allocated {
        background-color: #f0fdf4 !important;
    }
    .rp-table tbody tr.row-disabled {
        opacity: 0.45;
        background-color: #f8fafc;
    }

    .alloc-input {
        width: 140px;
        text-align: right;
        font-weight: 700;
        font-size: 14px;
        color: #047857;
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
        background: #ffffff;
        transition: all 0.2s;
    }
    .alloc-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
        outline: none;
    }
    .alloc-input:disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .row-allocated .alloc-input {
        border-color: #10b981;
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
        color: #059669;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Checkbox styling */
    .custom-row-chk {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #059669;
    }

    /* Action buttons */
    .btn-pay-full {
        font-size: 11px;
        padding: 5px 10px;
        font-weight: 700;
        border-radius: 6px;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .btn-pay-full:hover {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    /* Empty state */
    .empty-invoices {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }
    .empty-invoices i {
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
    <div class="rp-container">

        <!-- Top Header Card -->
        <div class="rp-header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="rp-title">
                        <i class="fa-solid fa-file-invoice-dollar text-success"></i> Receive Payment
                    </h1>
                    <span class="rp-badge-voucher">{{ $nextRvid }}</span>
                    <span class="rp-badge-draft">Draft</span>
                </div>
                <p class="text-muted mb-0 small">
                    Customer Invoice Settlement with Specific Multi-Select, Equal & FIFO Distribution.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('all_recepit_vochers') }}" class="btn btn-sm btn-outline-secondary px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-list me-1"></i> Receipts History
                </a>
                <a href="{{ route('vouchers.create') }}" class="btn btn-sm btn-outline-primary px-3 py-2" style="border-radius: 8px;">
                    <i class="fa-solid fa-sliders me-1"></i> All Vouchers
                </a>
            </div>
        </div>

        <form id="receivePaymentForm" method="POST" action="{{ route('vouchers.receive_payment.store') }}">
            @csrf
            <input type="hidden" name="rvid" value="{{ $nextRvid }}">

            <!-- Primary Payment Details -->
            <div class="rp-section-card">
                <div class="rp-section-header">
                    <h3 class="rp-section-title">
                        <i class="fa-solid fa-user-check text-primary"></i> Customer & Payment Details
                    </h3>
                    <div id="customerBalBadge" class="d-none">
                        <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 13px;">
                            Current Due: <strong id="lblCustomerBal" class="text-danger">PKR 0.00</strong>
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Customer Selection -->
                    <div class="col-md-4">
                        <label class="rp-label">Customer <span class="req">*</span></label>
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

                    <!-- Payment Date -->
                    <div class="col-md-2">
                        <label class="rp-label">Payment Date <span class="req">*</span></label>
                        <input type="date" name="payment_date" id="paymentDate" class="form-control rp-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Payment Mode -->
                    <div class="col-md-2">
                        <label class="rp-label">Payment Mode <span class="req">*</span></label>
                        <select name="payment_mode" id="paymentMode" class="form-select rp-select" required>
                            <option value="Cash" selected>Cash</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>

                    <!-- Deposit To Account -->
                    <div class="col-md-4">
                        <label class="rp-label">Deposit To Account (Cash/Bank) <span class="req">*</span></label>
                        <select name="deposit_account_id" id="depositAccountId" class="form-select rp-select select2" required>
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
                        <label class="rp-label">Reference / Cheque #</label>
                        <input type="text" name="reference_no" id="referenceNo" class="form-control rp-input" placeholder="e.g. CHQ-99081 / TR-88123">
                    </div>

                    <!-- Total Received Amount -->
                    <div class="col-md-4">
                        <label class="rp-label">Total Received Amount (PKR) <span class="req">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white fw-bold">PKR</span>
                            <input type="number" step="0.01" min="0.01" name="total_amount" id="totalAmount" 
                                   class="form-control rp-input rp-amount-hero text-end" 
                                   placeholder="0.00" required autocomplete="off">
                        </div>
                        <small class="text-muted" style="font-size: 11px;" id="lblAmountHint">Amount will be distributed strictly among checked/selected invoices.</small>
                    </div>

                    <!-- Remarks / Narration -->
                    <div class="col-md-4">
                        <label class="rp-label">Remarks / Narration</label>
                        <input type="text" name="remarks" id="remarks" class="form-control rp-input" placeholder="e.g. Received via Cheque against invoice dues">
                    </div>
                </div>
            </div>

            <!-- Summary Cards Bar -->
            <div class="row g-3 mb-3">
                <div class="col-md-3 col-6">
                    <div class="metric-card blue">
                        <div class="metric-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div>
                            <div class="metric-label">Total Invoiced</div>
                            <div class="metric-value" id="cardTotalInvoiced">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="metric-card amber">
                        <div class="metric-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div>
                            <div class="metric-label">Outstanding Due</div>
                            <div class="metric-value" id="cardTotalDue">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="metric-card emerald">
                        <div class="metric-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                        <div>
                            <div class="metric-label">Allocated For Invoices</div>
                            <div class="metric-value" id="cardTotalAllocated">PKR 0.00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
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
                            <span class="badge bg-success ms-2" id="badgeSelectedCount" style="font-size: 11px;">0 / 0 Selected</span>
                        </h3>
                        <div class="text-muted small">Check specific invoices to allocate payment only to them, or select all.</div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Mode Selector (Equal vs FIFO) -->
                        <div class="distribution-mode-group me-1">
                            <button type="button" class="mode-btn active" data-mode="equal" id="modeBtnEqual" title="Equally distribute total amount among selected invoices">
                                <i class="fa-solid fa-scale-balanced"></i> Equal Share
                            </button>
                            <button type="button" class="mode-btn" data-mode="fifo" id="modeBtnFifo" title="FIFO: Selected oldest invoices get paid first in order">
                                <i class="fa-solid fa-arrow-down-1-9"></i> FIFO (Oldest First)
                            </button>
                        </div>

                        <!-- Quick Action Buttons -->
                        <button type="button" class="btn btn-sm btn-outline-success fw-bold" id="btnApplyEqual" title="Equally split amount among selected invoices">
                            <i class="fa-solid fa-scale-balanced me-1"></i> Distribute
                        </button>
                        <button type="button" class="btn btn-sm btn-success text-white fw-bold" id="btnPayAllDues" title="Pay 100% outstanding balance of selected invoices">
                            <i class="fa-solid fa-check-double me-1"></i> Pay Selected Dues
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearAllocations" title="Clear all allocations">
                            <i class="fa-solid fa-eraser me-1"></i> Clear
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="rp-table" id="invoicesTable">
                        <thead>
                            <tr>
                                <th style="width: 35px;" class="text-center">
                                    <input type="checkbox" id="checkAllInvoices" class="custom-row-chk" checked title="Select / Deselect All">
                                </th>
                                <th style="width: 35px;">#</th>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Invoice Total</th>
                                <th class="text-end">Already Paid</th>
                                <th class="text-end text-danger">Outstanding Due</th>
                                <th class="text-end" style="width: 170px;">Payment (PKR)</th>
                                <th class="text-end">Remaining Due</th>
                                <th class="text-center" style="width: 100px;">Action</th>
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
                                <td colspan="5" class="text-end">Total Summary (Selected Dues):</td>
                                <td class="text-end" id="footTotalPaid">0.00</td>
                                <td class="text-end text-danger" id="footTotalDue">0.00</td>
                                <td class="text-end text-success" id="footTotalAlloc">0.00</td>
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
                    <a href="{{ route('all_recepit_vochers') }}" class="btn btn-light px-4 py-2 border">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </a>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="action" value="save" class="btn btn-success px-4 py-2 text-white fw-bold shadow-sm" id="btnSubmitSave" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Payment
                    </button>
                    <button type="submit" name="action" value="save_and_print" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" id="btnSubmitPrint" style="border-radius: 8px;">
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
            $('#lblAmountHint').text('Equal Mode: Entered amount will be distributed equally among selected invoices.');
            runEqualAllocation();
        } else {
            $('#lblAmountHint').text('FIFO Mode: Entered amount will settle selected oldest invoices first.');
            runFifoAllocation();
        }
    });

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
                    <td class="text-center">
                        <input type="checkbox" class="custom-row-chk row-chk" data-id="${inv.id}" checked title="Include this invoice in payment">
                    </td>
                    <td>${idx + 1}</td>
                    <td>
                        <strong class="text-dark">${inv.invoice_no}</strong>
                    </td>
                    <td><small class="text-muted">${inv.date}</small></td>
                    <td>
                        <small class="text-muted">${inv.due_date || '-'}</small>
                        ${inv.days_old ? `<span class="badge bg-light text-secondary ms-1">${inv.days_old}d</span>` : ''}
                    </td>
                    <td class="text-end">${parseFloat(inv.raw_total).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end">${parseFloat(inv.raw_paid).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end fw-bold text-danger inv-due-cell">${parseFloat(inv.raw_due).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end">
                        <input type="number" step="0.01" min="0" max="${inv.raw_due}" 
                               name="allocations[${inv.id}]" 
                               class="alloc-input form-control-sm" 
                               value="0.00" 
                               data-due="${inv.raw_due}">
                    </td>
                    <td class="text-end fw-bold inv-rem-cell text-muted">${parseFloat(inv.raw_due).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td class="text-center">
                        <button type="button" class="btn-pay-full btnPayFull" title="Pay full outstanding amount for this invoice">
                            Pay Full
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
            let chk = $(this).find('.row-chk');
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

    // Amount change in Total Received Amount triggers distribution strictly across selected invoices
    $(document).on('input keyup change paste', '#totalAmount', function() {
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

    // Pay Selected Dues Button (Allocates full dues for currently checked invoices and sets totalAmount)
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
        $('#totalAmount').val(totalDueSum.toFixed(2));
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

    // Equal Allocation Function (Strictly among SELECTED/CHECKED invoices)
    function runEqualAllocation() {
        let totalAmt = parseFloat($('#totalAmount').val()) || 0;
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

    // FIFO Allocation Function (Strictly among SELECTED/CHECKED invoices)
    function runFifoAllocation() {
        let totalAmt = parseFloat($('#totalAmount').val()) || 0;
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

        let totalRecAmt = parseFloat($('#totalAmount').val()) || 0;
        let excessOrAdvance = Math.max(0, totalRecAmt - sumAllocated);

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

        let custId = $('#customerId').val();
        let depAccId = $('#depositAccountId').val();
        let totalAmt = parseFloat($('#totalAmount').val()) || 0;

        if (!custId) {
            Swal.fire({ icon: 'warning', title: 'Customer Required', text: 'Please select a customer.' });
            return;
        }
        if (!depAccId) {
            Swal.fire({ icon: 'warning', title: 'Account Required', text: 'Please select the Deposit Account (Cash/Bank).' });
            return;
        }
        if (totalAmt <= 0) {
            Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Please enter a valid Total Received Amount.' });
            return;
        }

        let custName = $('#customerId').find(':selected').text().trim();
        let clickedBtn = $(document.activeElement);
        let actionVal = clickedBtn.val() || 'save_and_print';
        let formData = $(this).serialize();

        window.showConfirmPopup({
            title: 'Receive & Settle Payment?',
            text: `Are you sure you want to receive PKR ${totalAmt.toLocaleString('en-US', {minimumFractionDigits: 2})} from ${custName} and settle invoice dues?`,
            confirmBtnText: '<i class="fa-solid fa-check-circle me-1"></i> Yes, Post Payment'
        }, function() {
            let $submitBtns = $('#btnSubmitSave, #btnSubmitPrint');
            $submitBtns.prop('disabled', true);

            Swal.fire({
                title: 'Posting Payment...',
                text: 'Settling invoices and updating customer ledger.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('vouchers.receive_payment.store') }}",
                type: "POST",
                data: formData,
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
                let err = xhr.responseJSON ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'An error occurred.';
                Swal.fire({ icon: 'error', title: 'Failed to Save', text: err });
            }
        });
    });
});
</script>
@endsection
