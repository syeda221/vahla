@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">

<style>
    /* ================= MODERN PROFESSIONAL POS & CONSOLIDATION UI ================= */
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
        padding: 12px 16px !important;
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
        padding: 10px 12px !important;
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

    /* Invoice Series Input Group (Matching Sale Page) */
    .invoice-group {
        position: relative;
        display: flex;
        width: 100%;
    }
    .invoice-group .btn-prefix {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
        border: 1px solid #0284c7 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-top-left-radius: 6px !important;
        border-bottom-left-radius: 6px !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        height: 34px !important;
        padding: 0 12px !important;
        font-size: 0.78rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        box-shadow: 0 1px 2px rgba(2, 132, 199, 0.2) !important;
        white-space: nowrap;
        user-select: none;
    }

    .invoice-group #displayConsolidateInvoiceNo {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-left: none !important;
        flex: 1 1 auto;
        min-width: 0;
    }

    /* Green Save Button */
    .btn-top-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        height: 34px !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25) !important;
        font-size: 0.82rem !important;
        transition: all 0.15s ease !important;
    }
    .btn-top-save:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-1px);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35) !important;
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
                        Purchase Bill Preview (GRN Consolidation)
                    </h5>
                    <small class="text-muted" style="font-size: 0.72rem;">
                        Consolidating goods receiving items into a single purchase bill
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
                    Prev Balance: <strong class="text-danger">{{ number_format($prevBalance ?? 0, 2) }} Cr</strong>
                </span>
                <span class="badge bg-light text-primary border px-2 py-1" style="font-size: 0.75rem;">
                    Bill Amount: <strong>+{{ number_format($grandTotal ?? $totalNet, 2) }}</strong>
                </span>
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                    Net Balance: <strong class="text-danger">{{ number_format($netBalance ?? 0, 2) }} Cr</strong>
                </span>
            </div>
        </div>

        {{-- TOP INFORMATION PANEL FORM --}}
        <form action="{{ route('direct-grn.consolidate.store') }}" method="POST">
            @csrf
            @foreach($grns as $g)
                <input type="hidden" name="grn_ids[]" value="{{ $g->id }}">
            @endforeach
            <input type="hidden" name="invoice_prefix" value="PINV">

            <div class="top-info-card mb-3">
                <div class="row g-2 align-items-end w-100 m-0">
                    <!-- 1. Vendor Name -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="fas fa-user-circle text-primary"></i> Vendor
                        </label>
                        <input type="text" class="form-control input-readonly fw-bold" value="{{ optional($vendor)->name ?: 'Vendor #' . optional($vendor)->id }}" readonly title="{{ optional($vendor)->name }}">
                    </div>

                    <!-- 2. Invoice Series & Number (Sale Matching Style, Locked to PINV) -->
                    <div class="col-sm-6 col-md-3">
                        <label class="meta-label">
                            <i class="fas fa-receipt text-primary"></i> Invoice Series &amp; No.
                        </label>
                        <div class="input-group input-group-sm invoice-group">
                            <span class="btn-prefix d-flex align-items-center">
                                <span>PINV</span>
                            </span>
                            <input type="text" 
                                   id="displayConsolidateInvoiceNo" 
                                   class="form-control text-center font-monospace fw-bold bg-white text-dark" 
                                   value="{{ $nextInvoiceNo }}" 
                                   readonly>
                        </div>
                    </div>

                    <!-- 3. Bill Date -->
                    <div class="col-sm-6 col-md-2">
                        <label class="meta-label">
                            <i class="far fa-calendar-alt text-primary"></i> Bill Date
                        </label>
                        <input type="date" name="invoice_date" class="form-control fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- 4. Vendor Bill Ref # -->
                    <div class="col-sm-6 col-md-2">
                        <label class="meta-label">
                            <i class="fas fa-file-invoice text-primary"></i> Vendor Bill Ref #
                        </label>
                        <input type="text" name="vendor_bill_no" class="form-control" placeholder="e.g. INV-9876">
                    </div>

                    <!-- 5. Credit Days -->
                    <div class="col-sm-6 col-md-2">
                        <label class="meta-label">
                            <i class="fas fa-clock text-primary"></i> Credit Days
                        </label>
                        <input type="number" name="credit_days" class="form-control fw-bold text-center" value="0" min="0">
                    </div>
                </div>
            </div>

            <!-- CONSOLIDATED ITEMS TABLE -->
            <div class="table-responsive mb-3 border rounded-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-muted small fw-bold" style="font-size: 11px;">
                            <th class="ps-3" style="width: 40%;">Product Description</th>
                            <th style="width: 20%;">Variant / Color</th>
                            <th class="text-center" style="width: 15%;">Total Received Qty</th>
                            <th class="text-end" style="width: 12%;">Purch. Rate (Rs.)</th>
                            <th class="text-end pe-3" style="width: 13%;">Line Total (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-dark">{{ $cItem['product_name'] }}</strong>
                                    @if($cItem['product_code'])
                                        <small class="text-muted font-monospace d-block">Code: {{ $cItem['product_code'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $varDisplay }}</span>
                                </td>
                                <td class="text-center font-monospace fw-bold text-primary fs-6">{{ number_format($cItem['received_qty'], 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format($cItem['price'], 2) }}</td>
                                <td class="text-end font-monospace fw-bold text-dark pe-3">Rs. {{ number_format($cItem['line_total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light border-top">
                        <tr>
                            <td colspan="4" class="text-end fw-bold text-uppercase" style="font-size: 12px;">Gross Total:</td>
                            <td class="text-end fw-bold font-monospace fs-6 text-primary pe-3">Rs. {{ number_format($totalNet, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- BOTTOM SUMMARY & ACTIONS -->
            <div class="row g-3 align-items-center">
                <div class="col-md-7">
                    <label class="meta-label"><i class="fas fa-sticky-note text-primary"></i> Notes / Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Any additional notes for this consolidated bill...">
                </div>
                <div class="col-md-5 d-flex justify-content-end align-items-end">
                    <button type="submit" class="btn btn-top-save px-4 shadow-sm d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle"></i> Confirm &amp; Post Consolidated Purchase Bill
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
