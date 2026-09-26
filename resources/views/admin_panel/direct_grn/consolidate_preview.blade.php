@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">

<style>
    :root {
        --pos-bg: #f8fafc;
        --pos-card-bg: #ffffff;
        --pos-border: #e2e8f0;
        --pos-border-focus: #3b82f6;
        --pos-primary: #0284c7;
        --pos-primary-hover: #0369a1;
        --pos-success: #10b981;
        --pos-success-hover: #059669;
        --pos-danger: #ef4444;
        --pos-text-main: #0f172a;
        --pos-text-muted: #64748b;
        --pos-radius: 8px;
        --pos-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
    }

    .main-container {
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        box-shadow: var(--pos-shadow) !important;
        background-color: var(--pos-card-bg) !important;
        padding: 14px 18px !important;
        max-width: 100%;
    }

    .meta-label {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #475569 !important;
        margin-bottom: 4px !important;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .top-info-card {
        background-color: #f8fafc !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        padding: 12px 14px !important;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }

    .form-control, .form-select {
        border: 1px solid var(--pos-border) !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        font-weight: 500 !important;
        color: var(--pos-text-main) !important;
        background-color: #ffffff !important;
        transition: all 0.15s ease-in-out !important;
        height: 34px !important;
        font-size: 0.82rem !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--pos-border-focus) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        outline: none !important;
        background-color: #ffffff !important;
    }

    .input-readonly {
        background-color: #f1f5f9 !important;
        border-color: var(--pos-border) !important;
        color: #334155 !important;
        font-weight: 600 !important;
        cursor: default !important;
    }

    .table-items-edit thead th {
        background: #f1f5f9 !important;
        color: #334155 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.5px !important;
        padding: 8px 10px !important;
        border-bottom: 2px solid #cbd5e1 !important;
    }

    .table-items-edit tbody td {
        vertical-align: middle !important;
        padding: 6px 8px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .btn-top-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        height: 38px !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
        font-size: 0.85rem !important;
        transition: all 0.15s ease !important;
    }
    .btn-top-save:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-1px);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35) !important;
    }

    .summary-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        font-size: 0.82rem;
        border-bottom: 1px dashed #e2e8f0;
    }
    .summary-row:last-child {
        border-bottom: none;
    }
</style>

<div class="container-fluid py-2 px-2">
    <div class="main-container bg-white border mx-auto p-3 rounded-3">

        {{-- TOP HEADER BAR --}}
        <div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('direct-grn.index') }}" class="btn btn-sm btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Back to GRNs">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <div>
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                        <i class="fas fa-file-invoice text-primary"></i> 
                        Convert GRN to Purchase Bill
                    </h5>
                    <small class="text-muted" style="font-size: 0.72rem;">
                        Edit document number, dates, quantities, rates, discounts, and payments before creating invoice
                    </small>
                </div>
                <div class="d-flex align-items-center gap-1 ms-3">
                    <span class="text-muted small fw-bold" style="font-size: 0.72rem;">From GRNs:</span>
                    @foreach($grns as $g)
                        <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-boxes text-muted me-1"></i>{{ $g->grn_number }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                    Vendor Prev Bal: <strong class="text-danger" id="headerPrevBal">{{ number_format($prevBalance ?? 0, 2) }} Cr</strong>
                </span>
                <span class="badge bg-light text-primary border px-2 py-1" style="font-size: 0.75rem;">
                    Bill Amount: <strong id="headerBillTotal">+{{ number_format($grandTotal ?? $totalNet, 2) }}</strong>
                </span>
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                    Final Balance: <strong class="text-danger" id="headerNetBal">{{ number_format($netBalance ?? 0, 2) }} Cr</strong>
                </span>
            </div>
        </div>

        {{-- CONSOLIDATION / INVOICE FORM --}}
        <form action="{{ route('direct-grn.consolidate.store') }}" method="POST" id="formConsolidateInvoice">
            @csrf
            @foreach($grns as $g)
                <input type="hidden" name="grn_ids[]" value="{{ $g->id }}">
            @endforeach

            <!-- 1. TOP INFORMATION HEADER -->
            <div class="top-info-card mb-3">
                <div class="row g-2 align-items-end w-100 m-0">
                    <!-- Vendor Name -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="fas fa-user-circle text-primary"></i> Vendor
                        </label>
                        <input type="text" class="form-control input-readonly fw-bold" value="{{ optional($vendor)->name ?: 'Vendor #' . optional($vendor)->id }}" readonly title="{{ optional($vendor)->name }}">
                    </div>

                    <!-- Invoice Number (Editable) -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="fas fa-receipt text-primary"></i> Invoice #
                        </label>
                        <input type="hidden" name="invoice_prefix" value="PINV">
                        <input type="text" 
                               id="inputInvoiceNo" 
                               name="invoice_no"
                               class="form-control text-center font-monospace fw-bold bg-white text-dark" 
                               value="{{ $nextInvoiceNo }}" 
                               placeholder="e.g. PINV-0001"
                               title="Aap custom invoice number bhi enter kar sakte hain"
                               required>
                    </div>

                    <!-- Bill Date -->
                    <div class="col-sm-6 col-md-2">
                        <label class="meta-label">
                            <i class="far fa-calendar-alt text-primary"></i> Bill Date
                        </label>
                        <input type="date" name="invoice_date" class="form-control fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Vendor Bill Ref # -->
                    <div class="col-sm-6 col-md-2">
                        <label class="meta-label">
                            <i class="fas fa-file-invoice text-primary"></i> Vendor Bill Ref #
                        </label>
                        <input type="text" name="vendor_bill_no" class="form-control" placeholder="Manual Inv # / Ref">
                    </div>

                    <!-- Credit Days -->
                    <div class="col-sm-6 col-md-2">
                        <label class="meta-label">
                            <i class="fas fa-clock text-primary"></i> Credit Days
                        </label>
                        <input type="number" name="credit_days" class="form-control fw-bold text-center" value="0" min="0">
                    </div>
                </div>
            </div>

            <!-- 2. EDITABLE ITEMS TABLE -->
            <div class="table-responsive mb-3 border rounded-3 bg-white">
                <table class="table table-hover table-items-edit align-middle mb-0" id="itemsTable">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 4%;">#</th>
                            <th style="width: 32%;">Product Description</th>
                            <th style="width: 14%;">Variant</th>
                            <th class="text-center" style="width: 12%;">Invoicing Qty</th>
                            <th class="text-end" style="width: 13%;">Rate / Price (Rs.)</th>
                            <th class="text-end" style="width: 10%;">Item Disc. (Rs.)</th>
                            <th class="text-end" style="width: 11%;">Line Total (Rs.)</th>
                            <th class="text-center pe-3" style="width: 4%;"><i class="fas fa-trash-alt text-muted"></i></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @php $rowIdx = 0; @endphp
                        @foreach($consolidatedItems as $cItem)
                            @php
                                $variant = null;
                                if (!empty($cItem['color'])) {
                                    $decColor = base64_decode($cItem['color'], true);
                                    if ($decColor !== false) {
                                        $variant = json_decode($decColor, true);
                                    }
                                    if (!is_array($variant)) {
                                        $variant = json_decode($cItem['color'], true);
                                    }
                                }
                                $varDetails = [];
                                if (is_array($variant)) {
                                    if (!empty($variant['name']) && $variant['name'] !== '-') $varDetails[] = $variant['name'];
                                    if (!empty($variant['color']) && $variant['color'] !== '-') $varDetails[] = $variant['color'];
                                    if (!empty($variant['size']) && $variant['size'] !== '-') $varDetails[] = $variant['size'];
                                } elseif (!empty($cItem['color']) && !str_starts_with($cItem['color'], 'ey')) {
                                    $varDetails[] = $cItem['color'];
                                }
                                $varDisplay = count($varDetails) > 0 ? implode(' / ', $varDetails) : '-';
                            @endphp
                            <tr class="item-row">
                                <td class="ps-3 text-muted fw-bold text-center row-index">{{ $rowIdx + 1 }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $rowIdx }}][product_id]" value="{{ $cItem['product_id'] }}">
                                    <input type="hidden" name="items[{{ $rowIdx }}][color]" value="{{ $cItem['color'] }}">
                                    <strong class="text-dark d-block" style="font-size: 0.85rem;">{{ $cItem['product_name'] }}</strong>
                                    @if($cItem['product_code'])
                                        <small class="text-muted font-monospace">Code: {{ $cItem['product_code'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $varDisplay }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-center">
                                        <input type="text" 
                                               name="items[{{ $rowIdx }}][qty]" 
                                               class="form-control text-center fw-bold item-qty form-control-sm input-readonly bg-light text-primary" 
                                               value="{{ $cItem['received_qty'] }}" 
                                               readonly 
                                               style="cursor: default;"
                                               title="GRN Received Quantity (Fixed)">
                                    </div>
                                </td>
                                <td>
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           name="items[{{ $rowIdx }}][price]" 
                                           class="form-control text-end fw-bold item-price form-control-sm font-monospace" 
                                           value="{{ $cItem['price'] }}" 
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           name="items[{{ $rowIdx }}][discount]" 
                                           class="form-control text-end item-discount form-control-sm font-monospace" 
                                           value="0" 
                                           placeholder="0.00">
                                </td>
                                <td>
                                    <input type="text" 
                                           class="form-control text-end font-monospace fw-bold item-line-total form-control-sm bg-light text-dark" 
                                           value="{{ number_format($cItem['line_total'], 2, '.', '') }}" 
                                           readonly>
                                </td>
                                <td class="text-center pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-danger p-1 rounded-circle btn-remove-row" style="width: 26px; height: 26px;" title="Remove this item">
                                        <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </button>
                                </td>
                            </tr>
                            @php $rowIdx++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 3. BOTTOM FINANCIAL SUMMARY & PAYMENTS -->
            <div class="row g-3">
                <!-- Left Details: Notes & Instant Payment -->
                <div class="col-md-7">
                    <div class="p-3 bg-light border rounded-3 h-100">
                        <div class="mb-3">
                            <label class="meta-label"><i class="fas fa-sticky-note text-primary"></i> Notes / Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Any additional notes or PO reference...">
                        </div>

                        <div class="border-top pt-2">
                            <h6 class="fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                                <i class="fas fa-money-bill-wave text-success me-1"></i> Immediate Payment on Bill (Optional)
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="meta-label">Paid Amount (Rs.)</label>
                                    <input type="number" step="any" min="0" name="paid_amount" id="paidAmountInput" class="form-control fw-bold text-success font-monospace" value="0.00" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="meta-label">Payment Account</label>
                                    <select name="payment_account_id" id="paymentAccountSelect" class="form-select fw-semibold">
                                        <option value="">Select Cash / Bank Account...</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->title }} ({{ optional($acc->head)->name ? ucfirst(optional($acc->head)->name) : 'Account' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Details: Bill Calculation Box -->
                <div class="col-md-5">
                    <div class="summary-card shadow-sm">
                        <div class="summary-row">
                            <span class="text-muted fw-bold">Items Gross Subtotal:</span>
                            <span class="font-monospace fw-bold text-dark" id="displaySubtotal">Rs. {{ number_format($totalNet, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted fw-bold">Total Items Discount:</span>
                            <span class="font-monospace text-danger" id="displayLineDiscount">-Rs. 0.00</span>
                        </div>
                        <div class="summary-row align-items-center py-1">
                            <span class="text-muted fw-bold">Overall Bill Discount:</span>
                            <div style="max-width: 130px;">
                                <input type="number" step="any" min="0" name="discount" id="billDiscountInput" class="form-control form-control-sm text-end font-monospace fw-bold" value="0.00">
                            </div>
                        </div>
                        <div class="summary-row align-items-center py-1">
                            <span class="text-muted fw-bold">Carriage / Extra Cost:</span>
                            <div style="max-width: 130px;">
                                <input type="number" step="any" min="0" name="extra_cost" id="extraCostInput" class="form-control form-control-sm text-end font-monospace fw-bold" value="0.00">
                            </div>
                        </div>
                        <div class="summary-row bg-white p-2 rounded border mt-2">
                            <span class="fw-bold text-dark fs-6">Net Payable Bill:</span>
                            <span class="font-monospace fw-bold text-primary fs-5" id="displayNetAmount">Rs. {{ number_format($totalNet, 2) }}</span>
                        </div>
                        <div class="summary-row py-1">
                            <span class="text-muted fw-bold">Paid on Spot:</span>
                            <span class="font-monospace fw-bold text-success" id="displayPaidAmount">Rs. 0.00</span>
                        </div>
                        <div class="summary-row py-1">
                            <span class="text-muted fw-bold">Remaining Due on Bill:</span>
                            <span class="font-monospace fw-bold text-danger" id="displayDueAmount">Rs. {{ number_format($totalNet, 2) }}</span>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-top-save w-100 shadow d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-check-circle"></i> Confirm &amp; Post Purchase Bill
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const vendorPrevBalance = {{ (float)($prevBalance ?? 0) }};

        // Recalculate Totals Function
        function recalculateBillTotals() {
            let totalGross = 0;
            let totalLineDiscount = 0;

            $('#itemsTableBody tr.item-row').each(function() {
                const qty = parseFloat($(this).find('.item-qty').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val()) || 0;
                const discount = parseFloat($(this).find('.item-discount').val()) || 0;

                const lineGross = qty * price;
                const lineTotal = Math.max(0, lineGross - discount);

                $(this).find('.item-line-total').val(lineTotal.toFixed(2));

                totalGross += lineGross;
                totalLineDiscount += discount;
            });

            const billDiscount = parseFloat($('#billDiscountInput').val()) || 0;
            const extraCost = parseFloat($('#extraCostInput').val()) || 0;
            const paidAmount = parseFloat($('#paidAmountInput').val()) || 0;

            const totalDiscount = totalLineDiscount + billDiscount;
            const netAmount = Math.max(0, totalGross - totalDiscount + extraCost);
            const dueAmount = Math.max(0, netAmount - paidAmount);
            const finalVendorBal = vendorPrevBalance + dueAmount;

            // Update UI Summary Elements
            $('#displaySubtotal').text('Rs. ' + totalGross.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#displayLineDiscount').text('-Rs. ' + totalLineDiscount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#displayNetAmount').text('Rs. ' + netAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#displayPaidAmount').text('Rs. ' + paidAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#displayDueAmount').text('Rs. ' + dueAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // Update Header Badges
            $('#headerBillTotal').text('+' + netAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#headerNetBal').text(finalVendorBal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Cr');
        }

        // Event Listeners for live recalculations
        $(document).on('input change', '.item-qty, .item-price, .item-discount, #billDiscountInput, #extraCostInput, #paidAmountInput', function() {
            recalculateBillTotals();
        });

        // Delete / Remove Item Row
        $(document).on('click', '.btn-remove-row', function() {
            if ($('#itemsTableBody tr.item-row').length <= 1) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Remove',
                        text: 'Bill mein kam az kam aik item hona zaroori hai.'
                    });
                } else {
                    alert('Bill mein kam az kam aik item hona zaroori hai.');
                }
                return;
            }

            $(this).closest('tr.item-row').remove();
            
            // Re-index remaining rows
            $('#itemsTableBody tr.item-row').each(function(index) {
                $(this).find('.row-index').text(index + 1);
            });

            recalculateBillTotals();
        });

        // Form Validation on Submit
        $('#formConsolidateInvoice').on('submit', function(e) {
            if ($('#itemsTableBody tr.item-row').length === 0) {
                e.preventDefault();
                alert('Please keep at least one item in the bill.');
                return false;
            }

            const paidVal = parseFloat($('#paidAmountInput').val()) || 0;
            const paymentAcc = $('#paymentAccountSelect').val();

            if (paidVal > 0 && !paymentAcc) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Payment Account Required',
                        text: 'Aap ne Paid Amount enter ki hai, barah-e-karam Payment Account (Cash / Bank) select karein.'
                    });
                } else {
                    alert('Please select a payment account since Paid Amount is greater than 0.');
                }
                return false;
            }
        });

        // Initial Calculation
        recalculateBillTotals();
    });
</script>
@endpush
@endsection
