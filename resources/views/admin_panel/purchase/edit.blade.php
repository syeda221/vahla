@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    body {
        background-color: #f4f7f6;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    .main-wrapper {
        padding: 10px;
        max-width: 99%;
        margin: 0 auto;
    }
    .card-custom {
        background-color: #ffffff;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    /* Header Styles */
    .header-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .btn-back {
        color: #0d6efd;
        border: 1px solid #0d6efd;
        background: transparent;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 16px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-back:hover {
        background: #f0f4f8;
        color: #0b5ed7;
    }
    .header-title-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
    }
    .bg-blue { background-color: #0d6efd; }
    .bg-cyan { background-color: #0dcaf0; }
    .header-title {
        color: #0f172a;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
    }
    .header-date {
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Form Styles */
    .form-label {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.85rem;
        margin-bottom: 6px;
    }
    .input-with-icon {
        position: relative;
    }
    .input-with-icon .bi {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
        z-index: 4;
        pointer-events: none;
    }
        .input-with-icon .form-select {
        padding-left: 38px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        height: 42px;
        font-size: 0.9rem;
    }
    .input-with-icon .form-control {
        padding-left: 38px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        height: 42px;
        font-size: 0.9rem;
    }
    .input-with-icon .select2-container--default .select2-selection--single {
        padding-left: 30px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        height: 42px;
        display: flex;
        align-items: center;
    }
    .input-with-icon .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .form-control:focus, .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .input-readonly {
        background-color: #f1f5f9 !important;
        color: #64748b;
    }

    /* Table Styles */
    .card-title-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .card-title {
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        font-size: 1.1rem;
    }
    .card-subtitle {
        color: #64748b;
        font-size: 0.85rem;
        margin: 0;
    }
    .btn-add-row {
        background-color: #0d6efd;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 16px;
        border: none;
    }
    .btn-add-row:hover {
        background-color: #0b5ed7;
    }
    
    .table-container {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 20px;
    }
    .sales-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: collapse;
    }
    .sales-table thead th {
        background-color: #f8fafc;
        color: #0f172a;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 12px 10px;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
    }
    .sales-table tbody td {
        padding: 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
    }
    
    /* Inputs inside table */
    .sales-table .form-control, .sales-table .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        height: 36px;
        font-size: 0.85rem;
        text-align: center;
    }
    .sales-table .product-col .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        height: 36px;
        display: flex;
        align-items: center;
        text-align: left;
        padding-left: 30px;
    }
    .sales-table .product-col .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }
    
    .remove-row {
        color: #64748b;
        background: transparent;
        border: none;
        font-weight: bold;
        font-size: 1.1rem;
    }
    .remove-row:hover { color: #ef4444; }
    
    .action-btn {
        background: transparent;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #64748b;
        padding: 4px 8px;
    }
    
    .unit-toggle-btn {
        background-color: #ecfdf5;
        color: #10b981;
        border: 1px solid #10b981;
        border-radius: 6px;
        padding: 6px 10px;
        font-weight: 600;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .unit-toggle-btn.btn-outline-info { background-color: #eff6ff; color: #3b82f6; border-color: #3b82f6; }
    .unit-toggle-btn.btn-outline-primary { background-color: #f5f3ff; color: #8b5cf6; border-color: #8b5cf6; }
    
    .disc-wrapper {
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        overflow: hidden;
    }
    .disc-wrapper input {
        border: none !important;
        border-radius: 0 !important;
        width: 100%;
        height: 34px !important;
    }
    .disc-addon {
        background-color: #f1f5f9;
        color: #64748b;
        padding: 0 8px;
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        height: 34px;
        border-left: 1px solid #e2e8f0;
    }
    
    .amount-cell {
        background-color: #f0f9ff !important;
        color: #0369a1 !important;
        font-weight: 600;
        border: 1px solid #bae6fd !important;
    }
    
    .total-amount-row {
        background-color: #f8fafc;
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        border-top: 1px solid #e2e8f0;
    }
    .total-amount-label {
        font-weight: 700;
        color: #0f172a;
    }
    .total-amount-value {
        font-weight: 800;
        color: #1e3a8a;
        font-size: 1.25rem;
    }
    
    /* Bottom Cards */
    .summary-box {
        background-color: #f0f9ff;
        border-radius: 10px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }
    .summary-box-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: #0369a1;
    }
    .summary-box-value {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.25rem;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.9rem;
        color: #475569;
        align-items: center;
    }
    .summary-row:last-child {
        border-bottom: none;
    }
    .summary-row.fw-bold {
        color: #0f172a;
    }

    .btn-submit {
        background-color: #10b981;
        color: white;
        font-weight: 700;
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-size: 1rem;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
    }
    .btn-submit:hover {
        background-color: #059669;
    }
</style>

<div class="main-wrapper">
    <form id="purchaseForm" action="{{ route('purchase.update', $purchase->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <!-- Header Card -->
        <div class="card-custom header-card mb-3">
            <a href="{{ route('Purchase.home') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            
            <div class="header-title-container">
                <div class="icon-box bg-blue">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h1 class="header-title">Edit Purchase #{{ $purchase->invoice_no }}</h1>
            </div>
            
            <div class="header-date">
                <i class="bi bi-calendar3"></i>
                <span id="entryDate">Date: {{ date('m/d/Y', strtotime($purchase->purchase_date ?? now())) }}</span>
            </div>
        </div>

        <!-- General Info Card -->
        <div class="card-custom mb-3">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">System No.</label>
                    <div class="input-with-icon">
                        <i class="bi bi-file-earmark"></i>
                        <input type="text" class="form-control input-readonly" name="invoice_no" value="{{ $purchase->invoice_no }}" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Vendor Inv#</label>
                    <div class="input-with-icon">
                        <i class="bi bi-file-earmark-text"></i>
                        <input type="text" class="form-control" name="purchase_order_no" placeholder="Manual Ref" value="{{ $purchase->purchase_order_no }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Select Vendor</label>
                    <div class="input-with-icon">
                        <i class="bi bi-building"></i>
                        <select class="form-select select2" id="vendorSelect" name="vendor_id">
                            <option value="" selected disabled>Select Vendor</option>
                            @foreach ($Vendor as $v)
                                <option value="{{ $v->id }}" data-phone="{{ $v->phone }}" data-address="{{ $v->address }}" {{ $purchase->vendor_id == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <div class="input-with-icon">
                        <i class="bi bi-calendar"></i>
                        <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">M.Bill / Remarks</label>
                    <div class="input-with-icon">
                        <i class="bi bi-chat-left-text"></i>
                        <input type="text" class="form-control" name="note" id="remarks" placeholder="Optional notes..." value="{{ $purchase->note }}">
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <label class="form-label">Warehouse</label>
                    <div class="input-with-icon">
                        <i class="bi bi-shop"></i>
                        <select name="warehouse_id" class="form-control select2">
                            @foreach ($Warehouse as $w)
                                <option value="{{ $w->id }}"
                                    {{ $w->id == $purchase->warehouse_id ? 'selected' : '' }}>
                                    {{ $w->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Items Card -->
        <div class="card-custom mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="card-title-container">
                    <div class="icon-box bg-cyan">
                        <i class="bi bi-cart3"></i>
                    </div>
                    <div>
                        <h3 class="card-title">Purchase Items</h3>
                        <p class="card-subtitle">Manage items for this purchase</p>
                    </div>
                </div>
                <button type="button" class="btn-add-row shadow-sm" onclick="addBlankRow()">
                    <i class="bi bi-plus-lg"></i> Add Row
                </button>
            </div>

            <div class="table-container">
                <table class="sales-table" id="purchaseTable">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th class="text-start ps-3" style="width: 32%;">PRODUCT & VARIANT</th>
                            <th style="width: 10%;">UNIT</th>
                            <th style="width: 9%;">QTY</th>
                            <th style="width: 11%;">PURCHASE PRICE</th>
                            <th style="width: 10%;">DISC %</th>
                            <th style="width: 10%;">DISC AMT</th>
                            <th style="width: 11%;">AMOUNT</th>
                            <th style="width: 4%;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseTableBody">
                        @foreach ($purchase->items as $item)
                            @php
                                $sizeMode = $item->size_mode ?? 'by_pieces';
                                $ppb = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : 1);
                                $unitName = !empty($item->unit) ? $item->unit : ($item->product->unit->name ?? 'Pcs');
                                $uVal = strtolower($unitName ?? 'pcs');
                                $isCtn = in_array($uVal, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']);
                                $isKg = in_array($uVal, ['kg', 'gm', 'g']);

                                $displayQty = (float) $item->qty;
                                if ($isCtn && ($item->loose_qty > 0 || $item->boxes_qty > 0)) {
                                    $b = (int) $item->boxes_qty;
                                    $l = (int) $item->loose_qty;
                                    if ($l > 0) {
                                        $displayQty = $b . '.' . $l;
                                    } else {
                                        $displayQty = $b;
                                    }
                                }

                                $baseProductName = $item->product->item_name ?? 'Product';
                                $variantNameDisplay = $baseProductName;
                                $variantInfo = '';
                                $rawVariantData = $item->color ?? '';

                                if (!empty($item->color)) {
                                    $decodedColor = base64_decode($item->color, true);
                                    $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                                    if (!$vData) {
                                        $vData = json_decode($item->color, true);
                                    }
                                    if (is_array($vData)) {
                                        $vName = trim($vData['name'] ?? ($vData['variant_name'] ?? ''));
                                        $vColorName = trim($vData['color'] ?? '');
                                        $vSize = trim($vData['size'] ?? '');
                                        if (empty($item->unit) && !empty($vData['unit'])) {
                                            $unitName = $vData['unit'];
                                        }
                                        $vParts = [];
                                        $sStr = ($vSize !== '' && $vSize !== '-') ? " {$vSize}" : '';
                                        $cStr = ($vColorName !== '' && $vColorName !== '-') ? " ({$vColorName})" : '';

                                        if ($vName !== '') {
                                            if (stripos($vName, $baseProductName) !== false) {
                                                $variantNameDisplay = $vName;
                                            } else {
                                                $variantNameDisplay = $baseProductName . ' â€” ' . $vName;
                                            }
                                        } else {
                                            $variantNameDisplay = $baseProductName;
                                        }

                                        if ($sStr !== '' && stripos($variantNameDisplay, trim($vSize)) === false) {
                                            $variantNameDisplay .= $sStr;
                                        }
                                        if ($cStr !== '' && stripos($variantNameDisplay, trim($vColorName)) === false) {
                                            $variantNameDisplay .= $cStr;
                                        }

                                        if ($vColorName && $vColorName !== '-') {
                                            $vParts[] = 'Color: ' . $vColorName;
                                        }
                                        if ($vSize && $vSize !== '-') {
                                            $vParts[] = 'Size: ' . $vSize;
                                        }
                                        if (!empty($vParts)) {
                                            $variantInfo = implode(' | ', $vParts);
                                        }
                                    } elseif (is_string($item->color) && trim($item->color) !== '' && trim($item->color) !== '-') {
                                        $variantNameDisplay = $baseProductName . ' (' . trim($item->color) . ')';
                                        $variantInfo = trim($item->color);
                                    }
                                }

                                $optionVal = $item->product_id;
                                if (!empty($rawVariantData)) {
                                    $encodedVar = (base64_decode($rawVariantData, true) !== false) ? $rawVariantData : base64_encode($rawVariantData);
                                    $optionVal = $item->product_id . '|variant|' . $encodedVar;
                                }

                                $gross = $item->line_total + $item->item_discount;
                                $dPct = $gross > 0 ? ($item->item_discount / $gross) * 100 : 0;
                            @endphp
                            <tr data-sizemode="{{ $sizeMode }}" data-pieces_per_m2="{{ $item->pieces_per_m2 }}">
                                <td class="text-center fw-bold text-muted">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="product-col text-start ps-3">
                                    <div class="input-with-icon">
                                        <i class="bi bi-box-seam" style="z-index:100; color: #64748b;"></i>
                                        <select class="form-select product-select2" name="product_id[]">
                                            <option value="{{ $optionVal }}" selected>
                                                {{ $variantNameDisplay }} ({{ $item->product->item_code ?? 'SKU' }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="variant-badge-wrapper px-2 py-1 small text-muted d-flex gap-2 align-items-center {{ empty($variantInfo) ? 'd-none' : '' }}">
                                        <span class="badge bg-light text-dark border variant-badge">{{ $variantInfo }}</span>
                                    </div>
                                    {{-- Snapshots --}}
                                    <input type="hidden" name="size_mode[]" class="hidden-size-mode" value="{{ $sizeMode }}">
                                    <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="{{ $ppb }}">
                                    <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="{{ $item->pieces_per_m2 }}">
                                    <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="{{ $item->boxes_qty ?? 0 }}">
                                    <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="{{ $item->loose_qty ?? 0 }}">
                                    <input type="hidden" name="length[]" class="hidden-length" value="{{ $item->length }}">
                                    <input type="hidden" name="width[]" class="hidden-width" value="{{ $item->width }}">
                                    <input type="hidden" name="color[]" class="hidden-variant-data" value="{{ $rawVariantData }}">
                                </td>
                                <td>
                                    @php
                                        $btnClass = $isCtn ? 'btn-outline-success' : ($isKg ? 'btn-outline-primary' : 'btn-outline-info');
                                    @endphp
                                    <button type="button" class="unit-toggle-btn {{ $btnClass }}" data-unit="{{ $unitName }}" title="Toggle unit">
                                        <i class="bi bi-box"></i> <span class="unit-text ms-1">{{ $unitName }}</span> <i class="bi bi-chevron-down ms-1" style="font-size:0.7rem"></i>
                                    </button>
                                    <input type="hidden" name="unit[]" class="unit-input-val" value="{{ $unitName }}">
                                </td>
                                <td>
                                    <input type="number" step="any" min="0.0001" name="qty[]" class="form-control main-qty-input" value="{{ $displayQty }}">
                                </td>
                                <td>
                                    <input type="number" name="price[]" class="form-control price" step="0.01" value="{{ (float) $item->price }}">
                                </td>
                                <td>
                                    <div class="disc-wrapper">
                                        <input type="number" name="item_discount[]" class="form-control item-disc-percent" step="0.01" value="{{ round($dPct, 2) }}">
                                        <span class="disc-addon">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount-cell item-disc-amt" value="{{ (float) $item->item_discount }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount-cell row-total" value="{{ (float) $item->line_total }}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="action-btn remove-row text-danger border-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="total-amount-row">
                    <span class="total-amount-label">Total Amount:</span>
                    <span class="total-amount-value" id="totalAmount">0.00</span>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Payment Card -->
            <div class="col-lg-7">
                <div class="card-custom h-100">
                    <div class="card-title-container mb-4">
                        <div class="icon-box bg-blue">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <h3 class="card-title">Payment / Receipt Voucher</h3>
                            <p class="card-subtitle">Payment details and receipt voucher information</p>
                        </div>
                    </div>
                    
                    <div id="paymentWrapper">
                        @if (isset($existingPayments) && $existingPayments->isNotEmpty())
                            @foreach ($existingPayments as $pIndex => $pDetail)
                                <div class="row g-3 payment-row mb-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Payment Method</label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-box"></i>
                                            <select class="form-select rv-account" name="payment_account_id[]">
                                                <option value="" disabled>Select Account</option>
                                                @foreach ($accounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ $acc->id == $pDetail->account_id ? 'selected' : '' }}>
                                                        {{ $acc->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Amount</label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-cash"></i>
                                            <input type="number" class="form-control payment-amount" name="payment_amount[]" value="{{ (float) $pDetail->credit }}" placeholder="Amount" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pb-1">
                                        @if ($loop->first)
                                            <button type="button" class="btn btn-outline-primary w-100 h-100" id="btnAddPayment" style="height: 42px;">
                                                <i class="bi bi-plus"></i> Add
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-danger remove-payment w-100 h-100" style="height: 42px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="row g-3 payment-row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">Payment Method</label>
                                    <div class="input-with-icon">
                                        <i class="bi bi-box"></i>
                                        <select class="form-select rv-account" name="payment_account_id[]">
                                            <option value="" selected disabled>Select Account</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Amount</label>
                                    <div class="input-with-icon">
                                        <i class="bi bi-cash"></i>
                                        <input type="number" class="form-control payment-amount" name="payment_amount[]" placeholder="Enter amount..." step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-2 pb-1">
                                    <button type="button" class="btn btn-outline-primary w-100" id="btnAddPayment" style="height: 42px;">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="text-end mt-3 d-none">
                        <span class="me-2 fw-bold text-muted">Total Paid:</span>
                        <span class="fw-bold fs-6 text-success" id="totalPaid">0.00</span>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="col-lg-5">
                <div class="card-custom h-100">
                    <div class="card-title-container mb-3">
                        <div class="icon-box bg-cyan">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h3 class="card-title">Summary</h3>
                    </div>
                    
                    <div class="summary-row">
                        <span>Total Qty / Pieces</span>
                        <span id="tQty" class="fw-bold">0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Sub-Total</span>
                        <span id="tSub" class="fw-bold">0.00</span>
                        <input type="hidden" name="subtotal" id="subtotalInput">
                    </div>
                    <div class="summary-row">
                        <span>Bill Discount</span>
                        <div class="d-flex gap-2" style="width: 140px;">
                            @php
                                $inlineVal = $purchase->items->sum('item_discount');
                                $bSub = (float) $purchase->subtotal + $inlineVal;
                                $bDisc = (float) $purchase->discount + $inlineVal;
                                $bPct = $bSub > 0 ? ($bDisc / $bSub) * 100 : 0;
                            @endphp
                            <input type="number" class="form-control form-control-sm text-center" id="billDiscountPct" value="{{ round($bPct, 2) }}" placeholder="%" step="0.01">
                            <input type="number" class="form-control form-control-sm text-end" id="billDiscount" value="{{ (float) $bDisc }}" step="0.01">
                            <input type="hidden" name="discount" id="discountInput" value="{{ (float) $purchase->discount }}">
                        </div>
                    </div>
                    <div class="summary-row">
                        <span>Extra Cost</span>
                        <div style="width: 140px;">
                            <input type="number" class="form-control form-control-sm text-end" name="extra_cost" id="extraCost" value="{{ (float) $purchase->extra_cost }}">
                        </div>
                    </div>
                    
                    <div class="summary-box">
                        <div class="summary-box-label">
                            <i class="bi bi-coin fs-4"></i>
                            Total Amount (PKR)
                        </div>
                        <div class="summary-box-value" id="tPayable">0.00</div>
                        <input type="hidden" name="net_amount" id="netAmountInput">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check2-circle me-2"></i> Update Purchase
            </button>
        </div>

    </form>
</div>
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            // Init Global Select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize existing product selects
            $('.product-select2').each(function() {
                initProductSelect2($(this));
            });

            // Recalc rows & payments on initial load
            $('#purchaseTableBody tr').each(function() {
                recalcRow($(this));
            });
            recalcPayments();
            recalcAll();
                updateRowNumbers();

            // Unit Toggle Handler (Carton â†” Pcs / Kg â†” Gm)
            $(document).on('click', '.unit-toggle-btn', function() {
                const $btn = $(this);
                const $row = $btn.closest('tr');
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const packQty = parseFloat($row.find('.hidden-pieces-per-box').val()) || parseFloat($row.data('pieces_per_box')) || 1;
                let currentUnit = ($btn.attr('data-unit') || $btn.text() || '').trim();
                const $priceInp = $row.find('.price');
                let curPrice = parseFloat($priceInp.val()) || 0;

                const isCartonOrPcs = (['by_cartons', 'by_bandal'].includes(sizeMode) || packQty > 1 || ['carton', 'ctn', 'pcs', 'pc', 'piece'].includes(currentUnit.toLowerCase()));

                if (isCartonOrPcs) {
                    if (['carton', 'ctn', 'bandal', 'bundal', 'bndl'].includes(currentUnit.toLowerCase())) {
                        // Switch from Carton to Pcs
                        currentUnit = 'Pcs';
                        $btn.removeClass('btn-outline-success btn-outline-primary').addClass('btn-outline-info').attr('data-unit', 'Pcs');
                        $btn.find('.unit-text').text('Pcs');
                        $row.find('.unit-input-val').val('Pcs');

                        if (packQty > 1 && curPrice > 0) {
                            let piecePrice = curPrice / packQty;
                            $priceInp.val(piecePrice % 1 === 0 ? piecePrice : piecePrice.toFixed(2));
                        }
                    } else {
                        // Switch from Pcs to Carton
                        currentUnit = (sizeMode === 'by_bandal') ? 'Bundal' : 'Carton';
                        $btn.removeClass('btn-outline-info btn-outline-primary').addClass('btn-outline-success').attr('data-unit', currentUnit);
                        $btn.find('.unit-text').text(currentUnit);
                        $row.find('.unit-input-val').val(currentUnit);

                        if (packQty > 1 && curPrice > 0) {
                            let cartonPrice = curPrice * packQty;
                            $priceInp.val(cartonPrice % 1 === 0 ? cartonPrice : cartonPrice.toFixed(2));
                        }
                    }
                    recalcRow($row);
                    recalcAll();
                updateRowNumbers();
                } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
                    if (currentUnit.toLowerCase() === 'kg') {
                        currentUnit = 'Gm';
                        $btn.removeClass('btn-outline-primary').addClass('btn-outline-info').attr('data-unit', 'Gm');
                        $btn.find('.unit-text').text('Gm');
                    } else {
                        currentUnit = 'Kg';
                        $btn.removeClass('btn-outline-info').addClass('btn-outline-primary').attr('data-unit', 'Kg');
                        $btn.find('.unit-text').text('Kg');
                    }
                    $row.find('.unit-input-val').val(currentUnit);
                    recalcRow($row);
                    recalcAll();
                updateRowNumbers();
                }
            });

            // Add Row
            window.addBlankRow = function() {
                const html = `
                <tr>
                    <td class="text-center fw-bold text-muted row-number">#</td>
                    <td class="product-col text-start ps-3">
                        <div class="input-with-icon">
                            <i class="bi bi-box-seam" style="z-index:100; color: #64748b;"></i>
                            <select class="form-select product-select2" name="product_id[]"></select>
                        </div>
                        <div class="variant-badge-wrapper px-2 py-1 small text-muted d-flex gap-2 align-items-center d-none">
                            <span class="badge bg-light text-dark border variant-badge"></span>
                        </div>
                        <input type="hidden" name="size_mode[]" class="hidden-size-mode">
                        <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                        <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                        <input type="hidden" name="price_per_carton[]" class="hidden-price-per-carton" value="0">
                        <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="0">
                        <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="0">
                        <input type="hidden" name="length[]" class="hidden-length">
                        <input type="hidden" name="width[]" class="hidden-width">
                        <input type="hidden" name="color[]" class="hidden-variant-data">
                    </td>
                    <td>
                        <button type="button" class="unit-toggle-btn btn-outline-info" data-unit="Pcs" title="Toggle unit">
                            <i class="bi bi-box"></i> <span class="unit-text ms-1">Pcs</span> <i class="bi bi-chevron-down ms-1" style="font-size:0.7rem"></i>
                        </button>
                        <input type="hidden" name="unit[]" class="unit-input-val" value="Pcs">
                    </td>
                    <td>
                        <input type="number" step="any" min="0.0001" name="qty[]" class="form-control main-qty-input" value="1">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="price[]" class="form-control price" value="0">
                    </td>
                    <td>
                        <div class="disc-wrapper">
                            <input type="number" step="0.01" name="item_discount[]" class="form-control item-disc-percent" value="0">
                            <span class="disc-addon">%</span>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control amount-cell item-disc-amt" value="0.00" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control amount-cell row-total" value="0.00" readonly>
                    </td>
                    <td>
                        <button type="button" class="action-btn remove-row text-danger border-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
                const $row = $(html);
                $('#purchaseTableBody').append($row);
                initProductSelect2($row.find('.product-select2'));
                recalcRow($row);
                recalcAll();
                updateRowNumbers();
            };

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcAll();
                updateRowNumbers();
            });

            // Inputs -> Calc
            $('#purchaseTableBody').on('input', '.main-qty-input, .price, .item-disc-percent', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
                updateRowNumbers();
            });

            $('#billDiscount, #billDiscountPct, #extraCost').on('input', function() {
                recalcAll();
                updateRowNumbers();
            });

            function normalizeDiscountInput() {
                let totalInlineDiscount = 0;
                $('#purchaseTableBody tr').each(function() {
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;
                    totalInlineDiscount += rowDiscAmt;
                });

                let billDiscVal = parseFloat($('#billDiscount').val());
                if (isNaN(billDiscVal) || billDiscVal < totalInlineDiscount) {
                    $('#billDiscount').val(totalInlineDiscount.toFixed(2));
                }
                recalcAll();
                updateRowNumbers();
            }

            $('#billDiscount, #billDiscountPct').on('blur', function() {
                normalizeDiscountInput();
            });

            $('#purchaseForm').on('submit', function() {
                normalizeDiscountInput();
            });

            // --- Payment Section Logic ---
            $('#btnAddPayment').on('click', function() {
                const row = `
                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                    <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                        <option value="" selected disabled>Select Account</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px" step="0.01">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`;
                $('#paymentWrapper').append(row);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                recalcPayments();
            });

            $(document).on('input', '.payment-amount', function() {
                recalcPayments();
            });

            function recalcPayments() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
            }

            function recalcRow($row) {
                const qtyStr = ($row.find('.main-qty-input').val() || '').toString();
                const qty = parseFloat(qtyStr) || 0;
                const price = parseFloat($row.find('.price').val()) || 0;
                const discPct = parseFloat($row.find('.item-disc-percent').val()) || 0;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const unitVal = ($row.find('.unit-input-val').val() || '').toLowerCase();
                const pieces_per_m2 = parseFloat($row.data('pieces_per_m2')) || parseFloat($row.find('.hidden-pieces-per-m2').val()) || 0;

                const ppb = parseFloat($row.find('.hidden-pieces-per-box').val()) || parseFloat($row.data('pieces_per_box')) || 1;

                let gross = 0;
                const isPiece = (unitVal === 'pcs' || unitVal === 'pc' || unitVal === 'piece');
                const isCarton = (unitVal === 'carton' || unitVal === 'ctn' || unitVal === 'box' || (!isPiece && sizeMode === 'by_cartons'));

                if (sizeMode === 'by_size') {
                    gross = (pieces_per_m2 || 1) * qty * price;
                } else if (unitVal === 'gm' || unitVal === 'g') {
                    gross = (qty / 1000.0) * price;
                } else if (isPiece) {
                    gross = qty * price;
                    const bQty = ppb > 1 ? Math.floor(qty / ppb) : 0;
                    $row.find('.hidden-boxes-qty').val(bQty);
                    $row.find('.hidden-loose-qty').val(qty);
                } else if (isCarton) {
                    let s = qtyStr.trim();
                    if (s.startsWith('.')) s = '0' + s;
                    if (s.includes('.')) {
                        const parts = s.split('.');
                        const boxes = parseInt(parts[0]) || 0;
                        const loose = parseInt(parts[1]) || 0;
                        const piecePrice = ppb > 0 ? (price / ppb) : price;
                        gross = (boxes * price) + (loose * piecePrice);
                        $row.find('.hidden-boxes-qty').val(boxes);
                        $row.find('.hidden-loose-qty').val(loose);
                    } else {
                        const boxes = parseInt(s) || 0;
                        gross = boxes * price;
                        $row.find('.hidden-boxes-qty').val(boxes);
                        $row.find('.hidden-loose-qty').val(0);
                    }
                } else {
                    gross = qty * price;
                }

                const discAmt = gross * (discPct / 100);
                const lineTotal = Math.max(0, gross - discAmt);

                $row.find('.item-disc-amt').val(discAmt.toFixed(2));
                $row.find('.row-total').val(lineTotal.toFixed(2));
            }

                        function updateRowNumbers() {
                $('#purchaseTableBody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            function recalcAll() {
                let totalQty = 0;
                let subtotal = 0;
                let totalInlineDiscount = 0;

                $('#purchaseTableBody tr').each(function() {
                    const qty = parseFloat($(this).find('.main-qty-input').val()) || 0;
                    const total = parseFloat($(this).find('.row-total').val()) || 0;
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;

                    totalQty += qty;
                    subtotal += total;
                    totalInlineDiscount += rowDiscAmt;
                });

                const grossSubtotal = subtotal + totalInlineDiscount;

                $('#tQty').text(totalQty.toFixed(2));
                $('#tSub').text(subtotal.toFixed(2));
                $('#subtotalInput').val(subtotal.toFixed(2));
                $('#totalAmount').text(subtotal.toFixed(2));

                let additionalDiscount = parseFloat($('#discountInput').val()) || 0;
                let billDiscVal = parseFloat($('#billDiscount').val());

                if ($(document.activeElement).is('#billDiscount') || $(document.activeElement).is('#billDiscountPct')) {
                    if ($(document.activeElement).is('#billDiscountPct')) {
                        const pct = parseFloat($('#billDiscountPct').val()) || 0;
                        billDiscVal = grossSubtotal * (pct / 100);
                        $('#billDiscount').val(billDiscVal.toFixed(2));
                    }
                    if (!isNaN(billDiscVal)) {
                        additionalDiscount = Math.max(0, billDiscVal - totalInlineDiscount);
                    } else {
                        additionalDiscount = 0;
                    }
                } else {
                    billDiscVal = totalInlineDiscount + additionalDiscount;
                    $('#billDiscount').val(billDiscVal.toFixed(2));
                }
                
                const pct = grossSubtotal > 0 ? (billDiscVal / grossSubtotal) * 100 : 0;
                $('#billDiscountPct').val(pct.toFixed(2));

                $('#discountInput').val(additionalDiscount.toFixed(2));

                const extraCost = parseFloat($('#extraCost').val()) || 0;

                const net = subtotal - additionalDiscount + extraCost;

                $('#tPayable').text(net.toFixed(2));
                $('#netAmountInput').val(net.toFixed(2));
            }

            function initProductSelect2($el) {
                $el.select2({
                    placeholder: 'Search Product (Name / SKU / Barcode / Variant)...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '{{ route('products.ajax.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results || [],
                                pagination: {
                                    more: (data.pagination && data.pagination.more) ? true : false
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: formatProduct,
                    templateSelection: formatSelection
                });

                $el.on('select2:select', function(e) {
                    const data = e.params.data;
                    const $row = $(this).closest('tr');

                    let unitName = data.unit_name || 'Pcs';
                    const ppb = parseFloat(data.pieces_per_box || data.ppb) || 1;
                    const isCartonMode = (['by_cartons', 'by_bandal'].includes(data.size_mode) || unitName.toLowerCase() === 'carton' || unitName.toLowerCase() === 'ctn' || ppb > 1);

                    // Dynamic Unit & Style
                    if (isCartonMode) {
                        unitName = (data.size_mode === 'by_bandal') ? 'Bundal' : 'Carton';
                        $row.find('.unit-toggle-btn')
                            .removeClass('btn-outline-primary btn-outline-info')
                            .addClass('btn-outline-success')
                            .attr('data-unit', unitName)
                            .find('.unit-text').text(unitName);
                        $row.find('.unit-input-val').val(unitName);
                    } else if (data.size_mode === 'by_kg' || data.size_mode === 'by_gm') {
                        unitName = 'Kg';
                        $row.find('.unit-toggle-btn')
                            .removeClass('btn-outline-info btn-outline-success')
                            .addClass('btn-outline-primary')
                            .attr('data-unit', 'Kg')
                            .find('.unit-text').text('Kg');
                        $row.find('.unit-input-val').val('Kg');
                    } else {
                        $row.find('.unit-toggle-btn')
                            .removeClass('btn-outline-primary btn-outline-success')
                            .addClass('btn-outline-info')
                            .attr('data-unit', unitName)
                            .find('.unit-text').text(unitName);
                        $row.find('.unit-input-val').val(unitName);
                    }

                    // Variant Info Display (Size, Color)
                    let variantBadgeText = '';
                    if (data.variant_data) {
                        try {
                            const vObj = JSON.parse(atob(data.variant_data));
                            const parts = [];
                            if (vObj.size && vObj.size !== '-') parts.push('Size: ' + vObj.size);
                            if (vObj.color && vObj.color !== '-') parts.push('Color: ' + vObj.color);
                            if (parts.length > 0) {
                                variantBadgeText = parts.join(' | ');
                            }
                        } catch (err) {}
                    }
                    
                    if (variantBadgeText) {
                        $row.find('.variant-badge').text(variantBadgeText);
                        $row.find('.variant-badge-wrapper').removeClass('d-none');
                    } else {
                        $row.find('.variant-badge-wrapper').addClass('d-none');
                    }

                    // Prices
                    const pPiece = parseFloat(data.purchase_price_per_piece) || parseFloat(data.trade_price) || 0;
                    const pBox = parseFloat(data.purchase_price_per_box) || (pPiece * ppb);
                    const pM2 = parseFloat(data.purchase_price_per_m2) || 0;
                    const sizeMode = data.size_mode || 'std';

                    // Populate Snapshots
                    $row.find('.hidden-size-mode').val(data.size_mode || '');
                    $row.find('.hidden-pieces-per-box').val(ppb);
                    $row.find('.hidden-pieces-per-m2').val(data.pieces_per_m2 || 0);
                    $row.find('.hidden-price-per-carton').val(pBox);
                    $row.find('.hidden-length').val(data.length || '');
                    $row.find('.hidden-width').val(data.width || '');
                    $row.find('.hidden-variant-data').val(data.variant_data || '');

                    // Set default discount
                    $row.find('.item-disc-percent').val(data.purchase_discount_percent || 0);

                    // Set Price based on unit mode
                    let finalPrice = pPiece;
                    if (sizeMode === 'by_size') {
                        finalPrice = pM2;
                    } else if (isCartonMode) {
                        finalPrice = pBox > 0 ? pBox : (pPiece * ppb);
                    } else {
                        finalPrice = pPiece;
                    }

                    $row.find('.price').val(finalPrice % 1 === 0 ? finalPrice : finalPrice.toFixed(2));

                    // Data Attributes
                    $row.data('sizemode', sizeMode);
                    $row.data('pieces_per_m2', Number(data.pieces_per_m2) || 0);
                    $row.data('p_price_piece', pPiece);
                    $row.data('p_price_box', pBox);
                    $row.data('pieces_per_box', ppb);

                    // Qty default
                    let curQty = parseFloat($row.find('.main-qty-input').val()) || 0;
                    if (curQty <= 0) {
                        $row.find('.main-qty-input').val(1);
                    }

                    $row.find('.main-qty-input').focus().select();
                    recalcRow($row);
                    recalcAll();
                updateRowNumbers();
                });
            }

            function formatProduct(repo) {
                if (repo.loading) return repo.text;
                let stock = repo.stock !== undefined ? repo.stock : 0;
                let sku = repo.sku || 'N/A';
                let unit = repo.unit_name || 'Pcs';
                let stockVal = parseFloat(repo.stock_pieces !== undefined ? repo.stock_pieces : repo.stock) || 0;
                let badgeClass = stockVal > 0 ? 'bg-success' : 'bg-secondary';
                let buyPrice = parseFloat(repo.purchase_price_per_piece || repo.trade_price || 0);

                return $(`
                <div class="clearfix py-1">
                    <div class="float-start">
                        <div class="fw-bold text-dark">${repo.name || repo.text}</div>
                        <small class="text-muted">SKU: ${sku} | Unit: ${unit} | Buy Price: Rs. ${buyPrice.toFixed(2)}</small>
                    </div>
                    <div class="float-end">
                        <span class="badge ${badgeClass} rounded-pill">Stock: ${stock}</span>
                    </div>
                </div>`);
            }

            function formatSelection(repo) {
                return repo.name || repo.text;
            }
        });
    </script>
@endsection







