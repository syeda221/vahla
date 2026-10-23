<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $sale->invoice_no }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        :root {
            --pad-blue: #1e40af;
            --pad-light-blue: #e0f2fe;
            --pad-border: #3b82f6;
            --pad-text: #1e3a8a;
        }

        body {
            background-color: #f1f5f9;
            color: #000;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .challan-container {
            max-width: 290mm;
            margin: 20px auto;
            padding: 0 10px;
        }

        .challan-sheet {
            margin-bottom: 25px;
        }

        .invoice-page {
            width: 285mm;
            max-width: 100%;
            height: 100mm;
            min-height: 100mm;
            max-height: 102mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 4mm 8mm 3mm 8mm;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            color: var(--pad-text);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }

        /* Dotted cut line between original and duplicate */
        .cut-divider {
            display: none;
            width: 285mm;
            max-width: 100%;
            margin: 2mm auto;
            align-items: center;
            justify-content: center;
            height: 4mm;
            position: relative;
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
        }

        .cut-divider::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            border-top: 1.5px dashed #94a3b8;
            z-index: 1;
        }

        .cut-divider span {
            background: #f1f5f9;
            padding: 0 10px;
            position: relative;
            z-index: 2;
        }

        /* Default: hide duplicate */
        .copy-duplicate {
            display: none;
        }

        /* When two-copies-mode is enabled */
        body.two-copies-mode .copy-duplicate {
            display: flex;
        }

        body.two-copies-mode .cut-divider {
            display: flex;
        }

        body.two-copies-mode .cut-divider span {
            background: #ffffff;
        }

        /* Header Layout */
        .pad-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            border-bottom: 2px solid var(--pad-border);
            padding-bottom: 4px;
        }

        .logo-section {
            width: 42%;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            max-width: 120px;
            height: auto;
            max-height: 46px;
            object-fit: contain;
        }

        .logo-text-box {
            display: flex;
            flex-direction: column;
        }

        .company-title {
            margin: 0;
            font-weight: 900;
            font-size: 18px;
            color: #1e40af;
            line-height: 1.1;
        }

        .company-subtitle {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.8px;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .badge-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .invoice-badge {
            background-color: #3b82f6;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 2px 8px;
            display: inline-block;
            letter-spacing: 1.2px;
            border-radius: 2px;
        }

        .copy-type-tag {
            font-size: 9.5px;
            font-weight: bold;
            color: #1e40af;
            border: 1px solid var(--pad-border);
            padding: 1px 6px;
            border-radius: 2px;
            background: var(--pad-light-blue);
        }

        .company-details-section {
            width: 56%;
            font-size: 10px;
            line-height: 1.35;
            color: var(--pad-text);
            text-align: right;
        }

        .deals-in {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .deals-in span {
            color: #0284c7;
        }

        .contact-line {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 1px;
            font-size: 9.5px;
        }

        .contact-line i {
            color: #0ea5e9;
            margin-left: 5px;
            font-size: 9.5px;
            width: 12px;
            text-align: center;
        }

        /* Meta Section */
        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 11.5px;
        }
        
        .meta-left {
            width: 65%;
        }
        
        .meta-row {
            margin-bottom: 2px;
            display: flex;
            align-items: baseline;
        }
        
        .meta-label {
            width: 45px;
            font-size: 11px;
        }
        
        .meta-value {
            border-bottom: 1px solid var(--pad-border);
            flex-grow: 1;
            padding-left: 5px;
            color: #1e40af;
            font-size: 11.5px;
        }

        .meta-right {
            width: 32%;
        }

        /* Pad Table */
        .pad-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
            border: 1px solid var(--pad-border);
        }

        .pad-table thead th {
            background-color: #374151;
            color: #ffffff;
            text-align: center;
            padding: 3px 6px;
            font-size: 10.5px;
            border: 1px solid var(--pad-border);
            text-transform: uppercase;
        }

        .pad-table tbody td {
            border: 1px solid var(--pad-border);
            padding: 2.5px 6px;
            height: 19px;
            color: #000;
            font-size: 11px;
            vertical-align: middle;
        }
        
        .pad-table tbody tr:nth-child(even) {
            background-color: rgba(224, 242, 254, 0.35);
        }

        /* Footer */
        .pad-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 2px;
            font-size: 10.5px;
            font-weight: bold;
        }

        .footer-note {
            width: 55%;
            font-size: 9.5px;
            color: var(--pad-text);
            line-height: 1.25;
        }

        .footer-sign {
            width: 40%;
            text-align: right;
            position: relative;
            color: var(--pad-text);
            font-size: 10.5px;
        }

        .footer-stamp {
            width: 95px;
            height: auto;
            position: absolute;
            right: 15px;
            bottom: 10px;
            opacity: 0.85;
            transform: rotate(-10deg);
            mix-blend-mode: multiply;
            pointer-events: none;
        }

        .hide-rate .rate-col {
            display: none !important;
        }
        
        .hide-rate .desc-col {
            width: 65% !important;
        }

        /* Print Media Rules */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body { 
                background: #ffffff !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }
            .action-bar, .no-print { 
                display: none !important; 
            }
            .challan-container {
                margin: 0 !important;
                padding: 0 !important;
                max-width: 297mm !important;
                width: 297mm !important;
            }
            .challan-sheet {
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
                height: 210mm !important;
                max-height: 210mm !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
            }
            .challan-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .invoice-page { 
                margin: 0 auto !important; 
                box-shadow: none !important; 
                border: 1px solid var(--pad-border) !important;
                padding: 3mm 7mm 2.5mm 7mm !important;
                width: 285mm !important;
                max-width: 285mm !important;
                height: 100mm !important;
                max-height: 102mm !important;
                min-height: 100mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            /* Single copy mode */
            body:not(.two-copies-mode) .challan-sheet {
                height: auto !important;
            }
            body:not(.two-copies-mode) .invoice-page {
                page-break-after: always !important;
                break-after: page !important;
            }
            body:not(.two-copies-mode) .invoice-page:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            /* Two copies mode */
            body.two-copies-mode .invoice-page {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            body.two-copies-mode .cut-divider {
                display: flex !important;
                height: 5mm !important;
            }

            .pad-table tbody tr:nth-child(even) {
                background-color: rgba(224, 242, 254, 0.45) !important;
            }
            .invoice-badge {
                background-color: #3b82f6 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .pad-table thead th {
                background-color: #374151 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="action-bar no-print d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark fs-6">
                <i class="fa-solid fa-file-invoice me-1 text-primary"></i>
                Delivery Challan Preview
            </span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 ms-2" style="font-size: 11px;">
                <i class="fa-solid fa-print me-1"></i> A4 Half Landscape
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="toggleCopies()" class="btn btn-outline-primary shadow-sm" id="toggleCopiesBtn">
                <i class="fa-solid fa-copy me-1"></i> <span id="copiesBtnText">Print 2 Copies (A4 Full)</span>
            </button>
            <button onclick="toggleRate()" class="btn btn-warning shadow-sm" id="toggleRateBtn">
                <i class="fa-solid fa-eye-slash me-1"></i> Hide Rate
            </button>
            <button onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Print DC
            </button>
            <a href="javascript:history.back()" class="btn btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @php
        $saleItemsColl = collect($saleItems);
        $totalItems = $saleItemsColl->count();
        // 1-6 items fit on 1 half-page; 7+ items use 5 per page
        $chunkSize = $totalItems <= 6 ? 6 : 5;
        $itemChunks = $saleItemsColl->chunk($chunkSize);
        $totalPages = $itemChunks->count();
        $customerName = $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walk-in Customer');
    @endphp

    <div class="challan-container">
        @foreach ($itemChunks as $pageIndex => $chunk)
            <div class="challan-sheet">
                @foreach (['ORIGINAL', 'DUPLICATE'] as $copyType)
                    @if ($copyType === 'DUPLICATE')
                        <div class="cut-divider">
                            <span><i class="fa-solid fa-scissors me-1"></i> CUT HERE</span>
                        </div>
                    @endif

                    <div class="invoice-page {{ $copyType === 'DUPLICATE' ? 'copy-duplicate' : 'copy-original' }}">
                        <div class="invoice-content-top">
                            <!-- Header -->
                            <div class="pad-header">
                                <div class="logo-section">
                                    @if(\App\Models\Setting::get('company_logo'))
                                        <img src="{{ asset(\App\Models\Setting::get('company_logo')) }}" alt="Company Logo" class="logo-img">
                                    @endif
                                    <div class="logo-text-box">
                                        <h2 class="company-title">{{ \App\Models\Setting::get('company_name', 'VAHLA') }}</h2>
                                        <div class="company-subtitle">INDUSTRIAL SOLUTIONS</div>
                                        <div class="badge-wrapper">
                                            <span class="invoice-badge">DELIVERY CHALLAN</span>
                                            <span class="copy-type-tag">{{ $copyType }}</span>
                                            @if($totalPages > 1)
                                                <span class="copy-type-tag">Page {{ $pageIndex + 1 }}/{{ $totalPages }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="company-details-section">
                                    <div class="deals-in">
                                        <span>DEALS IN:</span> {{ \App\Models\Setting::get('deals_in', 'Fasteners, Bearing, Lifting Equipments, V-belt, PPE\'s, Hand & Cutting Etc') }}
                                    </div>
                                    <div class="contact-line">
                                        <span dir="ltr">{{ \App\Models\Setting::get('company_address', 'Shop No. 91/1, Opp: Fg Girls High School, Saddar Bazar Hyderabad Sindh') }}</span>
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="contact-line">
                                        <span dir="ltr">{{ \App\Models\Setting::get('company_phone', '+92-022-2781224 - 2730663') }}</span>
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    @if(\App\Models\Setting::get('company_email'))
                                    <div class="contact-line">
                                        <span dir="ltr">{{ \App\Models\Setting::get('company_email', 'vahlamillstore@hotmail.com') }}</span>
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Meta -->
                            <div class="meta-section">
                                <div class="meta-left">
                                    <div class="meta-row">
                                        <div class="meta-label">No:</div>
                                        <div class="meta-value">{{ $sale->invoice_no ?: ('DC-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}</div>
                                    </div>
                                    <div class="meta-row">
                                        <div class="meta-label">M/s.</div>
                                        <div class="meta-value">{{ $customerName }}</div>
                                    </div>
                                    <div style="font-size: 10.5px; color: #1e40af; font-weight: bold; margin-top: 2px;">
                                        Please Receive the following goods, your order No. <span style="border-bottom: 1px solid var(--pad-border); display: inline-block; width: 140px; color: #000; font-weight: normal; text-align: center;">{{ $sale->reference ?? '' }}</span>
                                    </div>
                                </div>
                                <div class="meta-right">
                                    <div class="meta-row">
                                        <div class="meta-label" style="width:40px;">Date:</div>
                                        <div class="meta-value">{{ \Carbon\Carbon::parse($sale->sale_date ?? now())->format('d-m-Y') }}</div>
                                    </div>
                                    @if($totalPages > 1)
                                    <div class="meta-row">
                                        <div class="meta-label" style="width:40px;">Page:</div>
                                        <div class="meta-value">{{ $pageIndex + 1 }} of {{ $totalPages }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Table -->
                            <table class="pad-table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">S.NO</th>
                                        <th class="desc-col" style="width: 52%; text-align: left;">DESCRIPTION</th>
                                        <th style="width: 14%;">DELIVERED QTY</th>
                                        <th class="rate-col" style="width: 14%;">RATE</th>
                                        <th style="width: 15%;">UNIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($chunk as $item)
                                        @php
                                            $sNo = ($pageIndex * $chunkSize) + $loop->iteration;
                                            $piecesPerBox = (float)($item['pieces_per_box'] ?? 1);
                                            if ($piecesPerBox <= 0) $piecesPerBox = 1;
                                            
                                            $totalPieces = (float)($item['total_pieces'] ?? 0);
                                            $boxes = floor($totalPieces / $piecesPerBox);
                                            $loosePieces = fmod($totalPieces, $piecesPerBox);
                                            $sizeMode = $item['size_mode'] ?? 'by_size';
                                            $variantUnit = strtolower($item['variant_unit'] ?? '');
                                            
                                            // Disp Qty & Unit
                                            $rawSoldQty = (float)($item['qty_box'] ?? 0);
                                            $convFactor = (float)($item['pieces_per_box'] ?? 1);
                                            if ($convFactor <= 0) $convFactor = 1;
                                            $vUnitRaw = trim($item['variant_unit'] ?? '');
                                            $vUnitLower = strtolower($vUnitRaw);

                                            $dispQty = $totalPieces;
                                            $dispUnit = 'Pcs';

                                            if (!empty($vUnitRaw) && !in_array($vUnitLower, ['standard', 'default', '-'])) {
                                                if ($rawSoldQty > 0) {
                                                    $dispQty = $rawSoldQty;
                                                } elseif ($convFactor > 0 && abs($convFactor - 1.0) > 0.00001) {
                                                    $dispQty = round($totalPieces / $convFactor, 4);
                                                } else {
                                                    $dispQty = $totalPieces;
                                                }
                                                $dispUnit = ucfirst($vUnitRaw);
                                            } elseif ($sizeMode === 'by_kg' || $sizeMode === 'by_gm') {
                                                $kgWt = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                                                if ($vUnitLower === 'gm' || $vUnitLower === 'g' || $sizeMode === 'by_gm') {
                                                    $dispQty = $kgWt * 1000;
                                                    $dispUnit = 'Gm';
                                                } else {
                                                    $dispQty = $kgWt;
                                                    $dispUnit = 'Kg';
                                                }
                                            } elseif ($sizeMode === 'by_meter') {
                                                $dispQty = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                                                $dispUnit = 'Mtr';
                                            } elseif ($sizeMode === 'by_feet') {
                                                $dispQty = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                                                $dispUnit = 'Ft';
                                            } elseif ($sizeMode === 'by_pieces') {
                                                $dispQty = $totalPieces;
                                                $dispUnit = 'Pcs';
                                            } elseif ($sizeMode === 'by_cartons' || $sizeMode === 'by_size') {
                                                $piecesPerBox = (int)$convFactor;
                                                if ($piecesPerBox <= 0) $piecesPerBox = 1;
                                                $boxes = floor($totalPieces / $piecesPerBox);
                                                $loosePieces = fmod($totalPieces, $piecesPerBox);

                                                if ($boxes > 0 && $loosePieces == 0) {
                                                    $dispQty = $boxes;
                                                    $dispUnit = $sizeMode == 'by_cartons' ? 'Ctn' : 'Box';
                                                } elseif ($boxes == 0 && $loosePieces > 0) {
                                                    $dispQty = $loosePieces;
                                                    $dispUnit = 'Pcs';
                                                } elseif ($boxes > 0 && $loosePieces > 0) {
                                                    $dispQty = "{$boxes}.{$loosePieces}";
                                                    $dispUnit = $sizeMode == 'by_cartons' ? 'Ctn' : 'Box';
                                                } else {
                                                    $dispQty = $totalPieces;
                                                    $dispUnit = 'Pcs';
                                                }
                                            } else {
                                                $dispQty = $rawSoldQty > 0 ? $rawSoldQty : $totalPieces;
                                                $dispUnit = !empty($vUnitRaw) ? ucfirst($vUnitRaw) : 'Pcs';
                                            }

                                            // Format product title
                                            $vName = $item['variant_name'] ?? '';
                                            $vSize = (!empty($item['size_val']) && $item['size_val'] !== '-') ? $item['size_val'] : '';
                                            $vColor = (!empty($item['color_val']) && $item['color_val'] !== '-') ? $item['color_val'] : '';
                                            
                                            $vExtra = [];
                                            if ($vSize) $vExtra[] = $vSize;
                                            if ($vColor) $vExtra[] = $vColor;
                                            $vExtraStr = count($vExtra) > 0 ? ' (' . implode(', ', $vExtra) . ')' : '';

                                            $productTitle = $item['item_name'];
                                            if ($vName && strtolower(trim($vName)) !== strtolower(trim($productTitle))) {
                                                $productTitle .= ' - ' . $vName;
                                            }
                                            $productTitle .= $vExtraStr;
                                        @endphp

                                        <tr>
                                            <td style="text-align: center;">{{ $sNo }}</td>
                                            <td class="desc-col" style="font-weight: 500;">
                                                {{ $productTitle }}
                                                @if($sizeMode === 'by_kg' && !in_array(strtolower($dispUnit), ['kg', 'gm', 'g']) && $totalPieces > 0)
                                                    <div style="font-size: 10px; color: #64748b; font-weight: normal;">
                                                        (Weight: {{ ($totalPieces == (int)$totalPieces) ? (int)$totalPieces : number_format($totalPieces, 2) }} Kg)
                                                    </div>
                                                @elseif($sizeMode === 'by_meter' && !in_array(strtolower($dispUnit), ['m', 'mtr', 'meter', 'mm']) && $totalPieces > 0)
                                                    <div style="font-size: 10px; color: #64748b; font-weight: normal;">
                                                        (Length: {{ ($totalPieces == (int)$totalPieces) ? (int)$totalPieces : number_format($totalPieces, 2) }} Mtr)
                                                    </div>
                                                @elseif($sizeMode === 'by_feet' && !in_array(strtolower($dispUnit), ['ft', 'feet', 'in', 'inch', 'inches']) && $totalPieces > 0)
                                                    <div style="font-size: 10px; color: #64748b; font-weight: normal;">
                                                        (Length: {{ ($totalPieces == (int)$totalPieces) ? (int)$totalPieces : number_format($totalPieces, 2) }} Ft)
                                                    </div>
                                                @endif
                                                @if(!empty($item['description']))
                                                    <div style="font-size: 10px; color: #475569; font-weight: normal;">
                                                        {{ $item['description'] }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="text-align: center; font-weight: bold;">{{ ($dispQty == (int)$dispQty) ? (int)$dispQty : number_format($dispQty, 3) }}</td>
                                            <td class="rate-col" style="text-align: right;">{{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                                            <td style="text-align: center;">{{ $dispUnit }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bottom Area -->
                        <div class="invoice-content-bottom">
                            @if ($loop->parent->last && !empty($sale->remarks))
                                <div style="font-size: 10px; margin-bottom: 2px;">
                                    <strong>Remarks:</strong> {{ $sale->remarks }}
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="pad-footer">
                                <div class="footer-note">
                                    Received the above goods in good order & condition<br><br><i>Receiver's Signature</i>
                                </div>
                                <div class="footer-sign">
                                    <img src="{{ asset('assets/images/stamp.png') }}" class="footer-stamp" alt="Stamp">
                                    For {{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    
    <script>
        function toggleRate() {
            document.body.classList.toggle('hide-rate');
            const btn = document.getElementById('toggleRateBtn');
            if (document.body.classList.contains('hide-rate')) {
                btn.innerHTML = '<i class="fa-solid fa-eye me-1"></i> Show Rate';
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-success');
            } else {
                btn.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i> Hide Rate';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-warning');
            }
        }

        function toggleCopies() {
            document.body.classList.toggle('two-copies-mode');
            const btnText = document.getElementById('copiesBtnText');
            const btn = document.getElementById('toggleCopiesBtn');
            if (document.body.classList.contains('two-copies-mode')) {
                btnText.innerText = 'Single Copy Mode';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btnText.innerText = 'Print 2 Copies (A4 Full)';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            }
        }
    </script>
</body>
</html>
