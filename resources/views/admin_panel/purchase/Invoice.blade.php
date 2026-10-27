<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $purchase->purchase_type === 'purchase_order' ? 'Purchase Order' : 'Purchase Invoice' }} - {{ $purchase->invoice_no }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0f172a;
            --secondary-slate: #1e293b;
            --accent-blue: #0284c7;
            --border-soft: #e2e8f0;
            --bg-card: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-dark);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 28px 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            position: relative;
            border: 1px solid var(--border-soft);
        }

        /* Top Header Styling */
        .company-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary-navy);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .company-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .invoice-badge-wrap {
            text-align: right;
        }

        .invoice-title-pill {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 6px 18px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
        }

        .invoice-no-text {
            font-family: monospace;
            font-weight: 700;
            font-size: 14px;
            color: var(--accent-blue);
            margin-top: 6px;
        }

        /* Info Cards */
        .info-card {
            background-color: #ffffff;
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .info-card-header {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-soft);
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-card-body {
            padding: 10px 12px;
            flex-grow: 1;
            font-size: 12px;
            color: #334155;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            min-width: 65px;
            display: inline-block;
        }

        /* Desktop Invoice Table */
        .invoice-table-wrap {
            overflow-x: auto;
            margin-top: 18px;
            border: 1px solid var(--border-soft);
            border-radius: 8px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .invoice-table thead th {
            background-color: var(--secondary-slate);
            color: #ffffff;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 10px 10px;
            border: none;
            vertical-align: middle;
        }

        .invoice-table tbody td {
            border-top: 1px solid var(--border-soft);
            border-bottom: 1px solid var(--border-soft);
            padding: 10px 10px;
            vertical-align: middle;
            font-size: 12px;
            color: #1e293b;
        }

        .invoice-table tbody tr:nth-of-type(even) {
            background-color: #fafbfd;
        }

        .invoice-table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .invoice-table tfoot td {
            background-color: var(--bg-card);
            border-top: 2px solid #cbd5e1;
            padding: 10px;
            font-weight: 700;
            font-size: 12px;
        }

        /* Totals Card */
        .totals-card {
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .totals-table td {
            padding: 7px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
        }

        .totals-table .highlight-payable {
            background: #eff6ff !important;
            border-top: 1px solid #bfdbfe;
            border-bottom: 1px solid #bfdbfe;
        }

        .totals-table .highlight-payable td {
            font-size: 13px;
            font-weight: 800;
            color: #1e3a8a;
            padding: 9px 12px;
        }

        .totals-table .expense-row {
            background: #f0fdf4 !important;
            border-top: 1px dashed #bbf7d0;
            border-bottom: 1px dashed #bbf7d0;
        }

        .totals-table .total-closing-row {
            background: #fef2f2 !important;
            border-top: 2px solid #fecaca;
        }

        .totals-table .total-closing-row td {
            font-size: 14px;
            font-weight: 800;
            color: #991b1b;
            padding: 10px 12px;
        }

        .signature-area {
            margin-top: 45px;
            border-top: 1.5px solid #64748b;
            width: 200px;
            text-align: center;
            padding-top: 6px;
            font-weight: 700;
            font-size: 11px;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Action bar */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        /* Mobile Item Cards View (< 768px) */
        .mobile-invoice-items {
            display: none;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }

        .mob-item-card {
            background: #ffffff;
            border: 1px solid var(--border-soft);
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .mob-item-hdr {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .mob-item-title {
            font-weight: 700;
            font-size: 0.92rem;
            color: #0f172a;
        }

        .mob-item-code {
            font-family: monospace;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .mob-item-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.84rem;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
        }

        /* Print Media Queries */
        @media print {
            body {
                background: #ffffff !important;
                margin: 0;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }

            .invoice-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 12px 16px !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .invoice-table-wrap {
                display: block !important;
                border: 1px solid #cbd5e1 !important;
            }

            .invoice-table thead th {
                background-color: #1e293b !important;
                color: #ffffff !important;
            }

            .invoice-table tbody tr:nth-of-type(even) {
                background-color: #f8fafc !important;
            }

            .totals-table .highlight-payable {
                background: #eff6ff !important;
            }

            .totals-table .expense-row {
                background: #f0fdf4 !important;
            }

            .totals-table .total-closing-row {
                background: #fef2f2 !important;
            }

            .mobile-invoice-items {
                display: none !important;
            }

            @page {
                margin: 6mm 8mm;
            }
        }

        /* Mobile Breakpoints (< 768px) */
        @media (max-width: 768px) {
            body {
                background-color: #ffffff;
            }
            .invoice-container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                border: none;
                padding: 14px;
            }
            .company-header {
                flex-direction: column;
                gap: 12px;
            }
            .invoice-badge-wrap {
                text-align: left;
            }
            .invoice-table-wrap {
                display: none !important;
            }
            .mobile-invoice-items {
                display: flex !important;
            }
            .info-card {
                margin-bottom: 8px;
            }
        }
    </style>
</head>

<body>

    <!-- Sticky Responsive Action Bar -->
    <div class="action-bar no-print">
        <div class="container-fluid d-flex align-items-center justify-content-between gap-2 px-2 px-md-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white fw-bold px-2 py-1" style="font-size: 0.75rem; white-space: nowrap;">#{{ $purchase->invoice_no }}</span>
                <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size: 0.75rem;">
                    {{ $purchase->purchase_type === 'purchase_order' ? 'Purchase Order' : 'Purchase Invoice' }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-1" style="border-radius: 6px; font-size: 0.82rem; white-space: nowrap;">
                    <i class="bi bi-printer-fill me-1"></i> Print
                </button>
                <a href="{{ $purchase->purchase_type === 'purchase_order' ? route('purchase_orders.index') : route('Purchase.home') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold text-nowrap" style="border-radius: 6px; font-size: 0.82rem;">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="invoice-container">
        <!-- Company & Invoice Header -->
        <div class="company-header">
            <div>
                <div class="company-name">{{ \App\Models\Setting::get('company_name', 'Three Stars Medical') }} - {{ date('Y') }}</div>
                <div class="company-subtitle">
                    <div><i class="bi bi-geo-alt me-1 text-muted"></i>{{ \App\Models\Setting::get('company_address', 'Hyderabad, Pakistan') }}</div>
                    @if(\App\Models\Setting::get('company_phone'))
                        <div><i class="bi bi-telephone me-1 text-muted"></i>{{ \App\Models\Setting::get('company_phone') }}</div>
                    @endif
                </div>
            </div>
            <div class="invoice-badge-wrap">
                <div class="invoice-title-pill">
                    {{ $purchase->purchase_type === 'purchase_order' ? 'Purchase Order' : 'Purchase Invoice' }}
                </div>
                <div class="invoice-no-text">{{ $purchase->invoice_no }}</div>
            </div>
        </div>

        <!-- 3 Info Grid Cards -->
        <div class="row g-2 mb-3">
            <!-- Box 1: Vendor Details -->
            <div class="col-12 col-md-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-person-badge text-primary"></i> Vendor Details
                    </div>
                    <div class="info-card-body">
                        <div style="font-size: 14px; font-weight: 800; color: #0f172a;">
                            {{ $purchase->vendor->name ?? 'Walk-in Vendor' }}
                        </div>
                        @if(!empty($purchase->vendor->phone))
                            <div class="text-dark small"><i class="bi bi-telephone me-1 text-muted"></i>{{ $purchase->vendor->phone }}</div>
                        @endif
                        @if(!empty($purchase->vendor->address))
                            <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $purchase->vendor->address }}</div>
                        @endif
                        @if(!empty($purchase->vendor->email))
                            <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $purchase->vendor->email }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Box 2: Purchase Details -->
            <div class="col-12 col-md-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-info-circle text-info"></i> Purchase Details
                    </div>
                    <div class="info-card-body">
                        <div>
                            <span class="info-label">Status:</span>
                            @php
                                $status = strtolower($purchase->status_purchase ?? 'approved');
                                $badgeClass = match($status) {
                                    'approved', 'confirmed', 'received' => 'bg-success-subtle text-success border border-success-subtle',
                                    'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                {{ ucfirst($purchase->status_purchase ?? 'Approved') }}
                            </span>
                        </div>
                        <div>
                            <span class="info-label">Warehouse:</span>
                            <span class="fw-semibold text-dark">{{ $purchase->warehouse->warehouse_name ?? 'Main Warehouse' }}</span>
                        </div>
                        @if(!empty($purchase->created_by_user))
                            <div>
                                <span class="info-label">Created By:</span>
                                <span>{{ $purchase->created_by_user->name ?? 'Admin' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Box 3: Reference Info -->
            <div class="col-12 col-md-4">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-receipt text-secondary"></i> Reference & Dates
                    </div>
                    <div class="info-card-body">
                        <div>
                            <span class="info-label">Invoice #:</span>
                            <strong class="font-monospace text-primary">{{ $purchase->invoice_no }}</strong>
                        </div>
                        <div>
                            <span class="info-label">Date:</span>
                            <strong>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</strong>
                        </div>
                        @if(!empty($purchase->purchase_order_no))
                            <div>
                                <span class="info-label">PO #:</span>
                                <strong class="font-monospace">{{ $purchase->purchase_order_no }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks / Notes -->
        @if ($purchase->note)
            <div class="p-2 mb-3 rounded-2 border bg-light text-muted fst-italic" style="font-size: 12px; border-color: #e2e8f0 !important;">
                <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-primary"></i>Note:</strong> {{ $purchase->note }}
            </div>
        @endif

        @php
            $sumCartons = 0;
            $sumLoosePieces = 0;
            $sumTotalQty = 0;
            $hasCartonMode = false;
            $unitsFound = [];

            foreach ($purchase->items as $it) {
                $rawU = strtolower(trim($it->unit ?? ''));
                if (!empty($rawU)) {
                    $unitsFound[$rawU] = true;
                }
                $itQty = (float) $it->qty;
                $sumTotalQty += $itQty;

                $itPPB = (float) ($it->pieces_per_box > 0 ? $it->pieces_per_box : ($it->product->pieces_per_box ?? 1));
                if (!empty($it->color)) {
                    $dec = base64_decode($it->color, true);
                    $vD = ($dec !== false) ? json_decode($dec, true) : json_decode($it->color, true);
                    if (is_array($vD)) {
                        if (!empty($vD['conv_factor']) && (float)$vD['conv_factor'] > 0) {
                            $itPPB = (float)$vD['conv_factor'];
                        }
                        if (empty($rawU) && !empty($vD['unit'])) {
                            $rawU = strtolower(trim($vD['unit']));
                            $unitsFound[$rawU] = true;
                        }
                    }
                }
                if ($itPPB <= 0) $itPPB = 1;

                $isCtn = in_array($rawU, ['carton', 'ctn', 'box']) || ($it->size_mode === 'by_cartons');

                if ($isCtn) {
                    $hasCartonMode = true;
                    if ($it->boxes_qty > 0 || $it->loose_qty > 0) {
                        $b = (int) $it->boxes_qty;
                        $l = (int) $it->loose_qty;
                    } else {
                        [$b, $l] = \App\Http\Controllers\PurchaseController::parseCartonQty($it->qty);
                    }
                    $sumCartons += $b;
                    $sumLoosePieces += $l;
                }
            }

            $formattedTotalQty = (float)$sumTotalQty == (int)$sumTotalQty ? (int)$sumTotalQty : rtrim(rtrim(number_format((float)$sumTotalQty, 3), '0'), '.');

            if ($hasCartonMode && $sumCartons > 0) {
                if ($sumLoosePieces > 0) {
                    $cartonPcsDisplay = "{$sumCartons} Ctn + {$sumLoosePieces} Pcs";
                    $cartonPcsShort = "{$sumCartons} Ctn + {$sumLoosePieces} Pcs";
                } else {
                    $cartonPcsDisplay = "{$sumCartons} Cartons";
                    $cartonPcsShort = "{$sumCartons} Cartons";
                }
            } elseif (count($unitsFound) === 1) {
                $singleUnit = ucfirst(array_key_first($unitsFound));
                $cartonPcsDisplay = "{$formattedTotalQty} {$singleUnit}";
                $cartonPcsShort = "{$formattedTotalQty} {$singleUnit}";
            } else {
                $cartonPcsDisplay = "{$formattedTotalQty} Units";
                $cartonPcsShort = "{$formattedTotalQty} Units";
            }
        @endphp

        <!-- Desktop & Print Table View -->
        <div class="invoice-table-wrap">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 12%">Code</th>
                        <th class="text-start" style="width: 36%">Description</th>
                        <th class="text-center" style="width: 10%">Qty</th>
                        <th class="text-center" style="width: 10%">UOM</th>
                        <th class="text-end" style="width: 11%">Price</th>
                        <th class="text-end" style="width: 9%">Disc</th>
                        <th class="text-end" style="width: 12%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        @php
                            $height = $item->length ?? 0;
                            $width = $item->width ?? 0;

                            $piecesPerBox = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : ($item->product->pieces_per_box ?? 1));
                            if (!empty($item->color)) {
                                $decColor = base64_decode($item->color, true);
                                $vColorData = ($decColor !== false) ? json_decode($decColor, true) : json_decode($item->color, true);
                                if (is_array($vColorData) && !empty($vColorData['conv_factor']) && (float)$vColorData['conv_factor'] > 0) {
                                    $piecesPerBox = (float)$vColorData['conv_factor'];
                                }
                            }
                            if ($piecesPerBox <= 0) $piecesPerBox = 1;

                            $rawUnit = strtolower(trim($item->unit ?? ''));
                            $isCarton = in_array($rawUnit, ['carton', 'ctn', 'box']) || ($item->size_mode === 'by_cartons');
                            $isPiece = in_array($rawUnit, ['pcs', 'pc', 'piece']);
                            $isWeight = in_array($rawUnit, ['kg', 'gm', 'g']);

                            // Format raw qty cleanly without 8 ugly trailing decimals
                            $rawQtyNum = (float) $item->qty;
                            $formattedItemQty = $rawQtyNum == (int)$rawQtyNum ? (int)$rawQtyNum : rtrim(rtrim(number_format($rawQtyNum, 3), '0'), '.');
                            $subQtyText = '';

                            if ($isCarton) {
                                if ($item->boxes_qty > 0 || $item->loose_qty > 0) {
                                    $boxes = (int) $item->boxes_qty;
                                    $loosePieces = (int) $item->loose_qty;
                                } else {
                                    [$boxes, $loosePieces] = \App\Http\Controllers\PurchaseController::parseCartonQty($item->qty);
                                }
                                $totalPieces = ($boxes * $piecesPerBox) + $loosePieces;
                                $uomDisplay = 'Carton';
                                if ($boxes > 0 && $loosePieces > 0) {
                                    $qtyDisplay = "{$boxes} Ctn + {$loosePieces} Pcs";
                                } elseif ($boxes > 0) {
                                    $qtyDisplay = ($boxes == 1 ? '1 Carton' : ($boxes . ' Cartons'));
                                } elseif ($loosePieces > 0) {
                                    $qtyDisplay = "{$loosePieces} Pcs";
                                } else {
                                    $qtyDisplay = '0 Cartons';
                                }
                                $subQtyText = '(' . $totalPieces . ' pcs)';
                            } elseif ($isPiece) {
                                $uomDisplay = 'Pcs';
                                $qtyDisplay = $formattedItemQty;
                                if ($piecesPerBox > 1) {
                                    $subQtyText = '(' . $formattedItemQty . ' pcs)';
                                }
                            } elseif ($isWeight) {
                                $uomDisplay = !empty($item->unit) ? $item->unit : 'Kg';
                                $qtyDisplay = $formattedItemQty;
                            } else {
                                $uomDisplay = !empty($item->unit) ? $item->unit : 'Pcs';
                                $qtyDisplay = $formattedItemQty;
                            }

                            $sizeMode = $item->size_mode ?? ($item->product->size_mode ?? 'by_pieces');
                            $baseProductName = $item->product->item_name ?? 'Item';
                            $variantNameDisplay = '';
                            $variantDetails = [];

                            if (!empty($item->color)) {
                                $decodedColor = base64_decode($item->color, true);
                                $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                                if (empty($vData) || !is_array($vData)) {
                                    $vData = json_decode($item->color, true);
                                }
                                if (!empty($vData) && is_array($vData)) {
                                    $vName = trim($vData['name'] ?? ($vData['variant_name'] ?? ''));
                                    $vColorName = trim($vData['color'] ?? '');
                                    $vSizeName = trim($vData['size'] ?? '');

                                    if ($vName !== '' && strcasecmp($vName, $baseProductName) !== 0) {
                                        $variantNameDisplay = $vName;
                                    }

                                    if ($vSizeName !== '' && $vSizeName !== '-') {
                                        $variantDetails[] = 'Size: ' . $vSizeName;
                                    }
                                    if ($vColorName !== '' && $vColorName !== '-') {
                                        $variantDetails[] = 'Color: ' . $vColorName;
                                    }
                                } elseif (is_string($item->color) && trim($item->color) !== '' && trim($item->color) !== '-') {
                                    $variantDetails[] = trim($item->color);
                                }
                            }

                            if ($variantNameDisplay !== '') {
                                if (stripos($variantNameDisplay, $baseProductName) !== false) {
                                    $fullItemTitle = $variantNameDisplay;
                                } else {
                                    $fullItemTitle = $baseProductName . ' — ' . $variantNameDisplay;
                                }
                            } else {
                                $fullItemTitle = $baseProductName;
                            }

                            if (!empty($variantDetails)) {
                                $fullItemTitle .= ' (' . implode(' | ', $variantDetails) . ')';
                            }
                        @endphp
                        <tr>
                            <td class="text-center font-monospace" style="font-size: 11px; font-weight: 700; color: #475569;">
                                <span class="badge bg-light text-dark border border-secondary-subtle px-2 py-1">
                                    {{ $item->product->item_code ?? '-' }}
                                </span>
                            </td>

                            <td class="text-start">
                                <div style="font-weight: 700; font-size: 13px; color: #0f172a; margin-bottom: 2px;">
                                    {{ $fullItemTitle }}
                                </div>
                                @if ($sizeMode == 'by_size' && ($height > 0 || $width > 0))
                                    <div style="font-size: 11px; color: #64748b;">
                                        Dims: {{ $width }}x{{ $height }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-center">
                                <div style="font-weight: 800; font-size: 13px; color: #0f172a;">
                                    {{ $qtyDisplay }}
                                </div>
                                @if (!empty($subQtyText))
                                    <div class="text-muted" style="font-size: 10px;">{{ $subQtyText }}</div>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-dark border px-2 py-1" style="font-size: 11px; font-weight: 600;">
                                    {{ $uomDisplay }}
                                </span>
                            </td>

                            <td class="text-end font-monospace" style="font-size: 12px;">
                                {{ number_format($item->price, 2) }}
                            </td>

                            <td class="text-end">
                                @if ($item->item_discount > 0)
                                    @php
                                        $grossLine = $item->line_total + $item->item_discount;
                                        $discPercent = $grossLine > 0 ? ($item->item_discount / $grossLine) * 100 : 0;
                                    @endphp
                                    <div style="font-size: 10px; color: #dc2626;">{{ number_format($discPercent, 1) }}%</div>
                                    <div class="font-monospace fw-bold text-danger" style="font-size: 11px;">{{ number_format($item->item_discount, 2) }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-end fw-bold font-monospace" style="font-size: 13px; color: #0f172a;">
                                {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end text-uppercase" style="font-size: 11px; color: #475569;">Total Quantity:</td>
                        <td class="text-center" style="font-size: 13px; font-weight: 800; color: #0284c7;">
                            {{ $cartonPcsShort }}
                        </td>
                        <td></td>
                        <td></td>
                        <td class="text-end text-danger font-monospace">
                            @php
                                $totalInlineDisc = $purchase->items->sum('item_discount');
                            @endphp
                            @if ($totalInlineDisc > 0)
                                -{{ number_format($totalInlineDisc, 2) }}
                            @endif
                        </td>
                        <td class="text-end font-monospace" style="font-size: 14px; font-weight: 800; color: #0f172a;">
                            Rs. {{ number_format($purchase->subtotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Items View (< 768px) -->
        <div class="mobile-invoice-items">
            @foreach ($purchase->items as $item)
                @php
                    $rawUnit = strtolower(trim($item->unit ?? ''));
                    $isCarton = in_array($rawUnit, ['carton', 'ctn', 'box']) || ($item->size_mode === 'by_cartons');
                    $rawQtyNum = (float) $item->qty;
                    $formattedItemQty = $rawQtyNum == (int)$rawQtyNum ? (int)$rawQtyNum : rtrim(rtrim(number_format($rawQtyNum, 3), '0'), '.');

                    if ($isCarton) {
                        if ($item->boxes_qty > 0 || $item->loose_qty > 0) {
                            $boxes = (int) $item->boxes_qty;
                            $loosePieces = (int) $item->loose_qty;
                        } else {
                            [$boxes, $loosePieces] = \App\Http\Controllers\PurchaseController::parseCartonQty($item->qty);
                        }
                        if ($boxes > 0 && $loosePieces > 0) {
                            $qtyDisplay = "{$boxes} Ctn + {$loosePieces} Pcs";
                        } elseif ($boxes > 0) {
                            $qtyDisplay = ($boxes == 1 ? '1 Carton' : ($boxes . ' Cartons'));
                        } elseif ($loosePieces > 0) {
                            $qtyDisplay = "{$loosePieces} Pcs";
                        } else {
                            $qtyDisplay = '0 Cartons';
                        }
                    } else {
                        $qtyDisplay = $formattedItemQty . ' ' . (!empty($item->unit) ? $item->unit : 'Pcs');
                    }

                    $baseProductName = $item->product->item_name ?? 'Item';
                @endphp
                <div class="mob-item-card">
                    <div class="mob-item-hdr">
                        <div class="mob-item-title">{{ $baseProductName }}</div>
                        <span class="mob-item-code">#{{ $item->product->item_code ?? '—' }}</span>
                    </div>

                    <div class="mob-item-details">
                        <div>
                            <span class="fw-bold text-dark">{{ $qtyDisplay }}</span>
                            <span class="text-muted ms-1">@ Rs. {{ number_format($item->price, 2) }}</span>
                        </div>
                        <div class="text-end">
                            @if ($item->item_discount > 0)
                                <div class="text-danger small" style="font-size: 0.72rem;">Disc: Rs. {{ number_format($item->item_discount, 2) }}</div>
                            @endif
                            <div class="fw-bold text-dark font-monospace" style="font-size: 0.95rem;">Rs. {{ number_format($item->line_total, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mob-item-card bg-light border-primary-subtle">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Total Quantity:</span>
                    <span class="fw-bold text-primary font-monospace">{{ $cartonPcsDisplay }}</span>
                </div>
            </div>
        </div>

        <!-- Footer / Totals Section -->
        <div class="row mt-4 align-items-end">
            <!-- Left Column: Signatures & Notes -->
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div class="p-2 mb-3 rounded-2 bg-light border border-dashed" style="font-size: 11px; color: #64748b; max-width: 320px;">
                        <div><i class="bi bi-shield-check text-success me-1"></i> Computer Generated Document</div>
                        <div><i class="bi bi-clock-history me-1"></i> Printed on: {{ date('d M Y, h:i A') }}</div>
                    </div>

                    <div>
                        <div class="signature-area">
                            Authorized Signature & Stamp
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Structured Totals Card -->
            <div class="col-12 col-md-6">
                <div class="totals-card shadow-sm">
                    <table class="totals-table">
                        <tr>
                            <td class="text-muted fw-semibold">Total Quantity:</td>
                            <td class="text-end fw-bold text-primary font-monospace">{{ $cartonPcsDisplay }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Subtotal</td>
                            <td class="text-end font-monospace fw-semibold">Rs. {{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        @if ($purchase->additional_discount > 0)
                            <tr>
                                <td class="text-secondary">
                                    Additional Discount
                                    @php
                                        $billDiscPercent = $purchase->subtotal > 0 ? ($purchase->additional_discount / $purchase->subtotal) * 100 : 0;
                                    @endphp
                                    <span class="text-muted small">({{ number_format($billDiscPercent, 1) }}%)</span>
                                </td>
                                <td class="text-end text-danger font-monospace">-Rs. {{ number_format($purchase->additional_discount, 2) }}</td>
                            </tr>
                        @endif
                        @if ($purchase->extra_cost > 0)
                            <tr>
                                <td class="text-secondary">Extra Cost (Vendor Bill)</td>
                                <td class="text-end font-monospace">+Rs. {{ number_format($purchase->extra_cost, 2) }}</td>
                            </tr>
                        @endif

                        {{-- Prominent Vendor Net Payable --}}
                        <tr class="highlight-payable">
                            <td>Vendor Net Payable</td>
                            <td class="text-end font-monospace">Rs. {{ number_format($purchase->net_amount, 2) }}</td>
                        </tr>

                        {{-- Separate Purchase Expense (Internal Carriage/Labour) --}}
                        @if ((float)($purchase->purchase_expense ?? 0) > 0)
                            <tr class="expense-row">
                                <td class="fw-bold text-success">
                                    <i class="bi bi-wallet2 me-1"></i> Purchase Expense
                                </td>
                                <td class="text-end font-monospace text-success fw-bold">
                                    +Rs. {{ number_format($purchase->purchase_expense, 2) }}
                                </td>
                            </tr>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <td class="text-dark fw-bold" style="font-size: 11.5px;">
                                    Total Purchase Cost
                                </td>
                                <td class="text-end fw-bold font-monospace" style="font-size: 12px; color: #0f172a;">
                                    Rs. {{ number_format($purchase->net_amount + $purchase->purchase_expense, 2) }}
                                </td>
                            </tr>
                        @endif

                        {{-- Settlement & Vendor Balances (Non-PO) --}}
                        @if($purchase->purchase_type !== 'purchase_order')
                            <tr>
                                <td class="text-secondary">Paid to Vendor</td>
                                <td class="text-end text-success fw-bold font-monospace">Rs. {{ number_format($purchase->paid_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-dark">Current Bill Due</td>
                                <td class="text-end fw-bold font-monospace text-danger">
                                    Rs. {{ number_format($purchase->net_amount - $purchase->paid_amount, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-secondary">Previous Vendor Balance</td>
                                <td class="text-end font-monospace text-dark">Rs. {{ number_format($previousBalance, 2) }}</td>
                            </tr>
                            <tr class="total-closing-row">
                                <td>Vendor Closing Balance</td>
                                <td class="text-end font-monospace">
                                    Rs. {{ number_format($currentBalance, 2) }}
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
