<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $sale->invoice_no }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --pad-blue: #1e40af;
            --pad-light-blue: #e0f2fe;
            --pad-border: #3b82f6;
            --pad-text: #1e3a8a;
        }

        body {
            background-color: #f8fafc;
            color: #000;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .invoice-page {
            width: 210mm;
            max-width: 210mm;
            height: 148mm;
            min-height: 148mm;
            max-height: 148mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 10px 18px 8px 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            color: var(--pad-text);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* Header Layout */
        .pad-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 2px solid var(--pad-border);
            padding-bottom: 8px;
        }

        .logo-section {
            width: 45%;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            max-height: 105px;
            margin-bottom: 8px;
        }

        .invoice-badge {
            background-color: #3b82f6;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            padding: 3px 10px;
            display: inline-block;
            letter-spacing: 1.5px;
            width: 70%;
            border-radius: 2px;
        }

        .company-details-section {
            width: 50%;
            font-size: 11px;
            line-height: 1.4;
            color: var(--pad-text);
            text-align: right;
        }

        .deals-in {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 11.5px;
        }
        
        .deals-in span {
            color: #0284c7;
        }

        .contact-line {
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            margin-bottom: 2px;
        }

        .contact-line i {
            color: #0ea5e9;
            margin-left: 8px;
            margin-top: 2px;
            font-size: 12px;
            width: 14px;
            text-align: center;
        }

        /* Meta Section (S.No, Date, etc) */
        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .meta-left {
            width: 60%;
        }
        
        .meta-row {
            margin-bottom: 4px;
            display: flex;
        }
        
        .meta-label {
            width: 50px;
        }
        
        .meta-value {
            border-bottom: 1px solid var(--pad-border);
            flex-grow: 1;
            padding-left: 5px;
            color: #000;
        }

        .meta-right {
            width: 35%;
        }

        /* Pad Table */
        .pad-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid var(--pad-border);
        }

        .pad-table thead th {
            background-color: #374151;
            color: #ffffff;
            text-align: center;
            padding: 6px 8px;
            font-size: 12px;
            border: 1px solid var(--pad-border);
            text-transform: uppercase;
        }

        .pad-table tbody td {
            border: 1px solid var(--pad-border);
            padding: 4px 8px;
            height: 24px;
            color: #000;
            vertical-align: middle;
        }
        
        .pad-table tbody tr:nth-child(even) {
            background-color: rgba(224, 242, 254, 0.4);
        }

        /* Footer */
        .pad-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .footer-note {
            width: 60%;
            font-size: 11px;
            color: var(--pad-text);
        }

        .footer-sign {
            width: 35%;
            text-align: right;
            padding-top: 10px;
            border-top: 1px solid transparent;
            color: var(--pad-text);
        }

        @media print {
            body { 
                background: #fff !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }
            .action-bar, .no-print { display: none !important; }
            .invoice-page { 
                margin: 0 auto !important; 
                box-shadow: none !important; 
                padding: 4mm 8mm 3mm 8mm !important;
                width: 210mm !important;
                max-width: 210mm !important;
                height: 148mm !important;
                max-height: 148mm !important;
                min-height: 148mm !important;
                box-sizing: border-box !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                overflow: hidden !important;
            }
            .invoice-page:last-child {
                page-break-after: auto !important;
            }
            .pad-table tbody tr:nth-child(even) {
                background-color: rgba(224, 242, 254, 0.5) !important;
            }
            .invoice-badge {
                background-color: #3b82f6 !important;
                color: #ffffff !important;
            }
            .pad-table thead th {
                background-color: #374151 !important;
                color: #ffffff !important;
            }
        }

        @page {
            size: A5 landscape;
            margin: 0;
        }

        .hide-rate .rate-col {
            display: none !important;
        }
        
        .hide-rate .desc-col {
            width: 65% !important;
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
                <i class="fa-solid fa-print me-1"></i> A4 Half / A5 Landscape
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="toggleRate()" class="btn btn-warning shadow-sm" id="toggleRateBtn">
                <i class="fa-solid fa-eye-slash"></i> Hide Rate
            </button>
            <button onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-print"></i> Print DC
            </button>
            <a href="javascript:history.back()" class="btn btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @php
        $saleItemsColl = collect($saleItems);
        $totalItems = $saleItemsColl->count();
        // If 6 or fewer items, fit on 1 half-page; if more, 5 items per half-page
        $chunkSize = $totalItems <= 6 ? 6 : 5;
        $itemChunks = $saleItemsColl->chunk($chunkSize);
        $totalPages = $itemChunks->count();
        $customerName = $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walk-in Customer');
    @endphp

    @foreach ($itemChunks as $pageIndex => $chunk)
        <div class="invoice-page">
            <div class="invoice-content-top">
                <!-- Header -->
                <div class="pad-header">
                    <div class="logo-section">
                        @if(\App\Models\Setting::get('company_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('company_logo')) }}" alt="Company Logo" class="logo-img">
                        @else
                            <h2 style="margin:0; font-weight:900; font-size:24px; color: #1e40af;">{{ \App\Models\Setting::get('company_name', 'VAHLA') }}</h2>
                            <div style="font-size:11px; font-weight:bold; letter-spacing:1px; margin-bottom:6px; color: #1e40af;">INDUSTRIAL SOLUTIONS</div>
                        @endif
                        <div class="invoice-badge">
                            DELIVERY CHALLAN
                            @if($totalPages > 1)
                                <span style="font-size: 11px; font-weight: normal; letter-spacing: 0;">(Page {{ $pageIndex + 1 }}/{{ $totalPages }})</span>
                            @endif
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
                            <div class="meta-value" style="color: #1e40af;">{{ $sale->invoice_no }}</div>
                        </div>
                        <div class="meta-row mt-1">
                            <div class="meta-label">M/s.</div>
                            <div class="meta-value" style="color: #1e40af;">{{ $customerName }}</div>
                        </div>
                        <div class="mt-2" style="font-size: 11px; color: #1e40af; font-weight: bold;">
                            Please Receive the following goods, your order No. <span style="border-bottom: 1px solid var(--pad-border); display: inline-block; width: 150px; color: #000; font-weight: normal; text-align: center;">{{ $sale->reference ?? '' }}</span>
                        </div>
                    </div>
                    <div class="meta-right">
                        <div class="meta-row">
                            <div class="meta-label" style="width:40px;">Date:</div>
                            <div class="meta-value" style="color: #1e40af;">{{ $sale->created_at ? $sale->created_at->format('d-m-Y') : date('d-m-Y') }}</div>
                        </div>
                        @if($totalPages > 1)
                        <div class="text-end mt-2" style="font-size: 11px; color: #1e40af; font-weight: bold;">
                            Page {{ $pageIndex + 1 }} of {{ $totalPages }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Table -->
                <table class="pad-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">S.NO</th>
                            <th class="desc-col" style="width: 50%; text-align: left;">DESCRIPTION</th>
                            <th style="width: 15%;">QTY</th>
                            <th class="rate-col" style="width: 15%;">RATE</th>
                            <th style="width: 15%;">UNIT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($chunk as $item)
                            @php
                                $sNo = ($pageIndex * $chunkSize) + $loop->iteration;
                                $piecesPerBox = (int)($item['pieces_per_box'] ?? 1);
                                if ($piecesPerBox <= 0) $piecesPerBox = 1;
                                
                                $totalPieces = (float)($item['total_pieces'] ?? 0);
                                $boxes = floor($totalPieces / $piecesPerBox);
                                $loosePieces = fmod($totalPieces, $piecesPerBox);
                                $sizeMode = $item['size_mode'] ?? 'by_size';
                                $variantUnit = strtolower($item['variant_unit'] ?? '');
                                
                                // Disp Qty & Unit
                                $rawSoldQty = (float)($item['qty_box'] ?? 0);
                                $totalPieces = (float)($item['total_pieces'] ?? 0);
                                $convFactor = (float)($item['pieces_per_box'] ?? 1);
                                if ($convFactor <= 0) $convFactor = 1;
                                $vUnitRaw = trim($item['variant_unit'] ?? '');
                                $vUnitLower = strtolower($vUnitRaw);

                                $dispQty = $totalPieces;
                                $dispUnit = 'Pcs';

                                if (!empty($vUnitRaw) && !in_array($vUnitLower, ['standard', 'default', '-'])) {
                                    // Variant has its own specified unit (e.g. Ft, Mtr, Kg, Gm, Pcs, Box, Roll, etc.)
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
                                        <div style="font-size: 11px; color: #64748b; font-weight: normal; margin-top: 1px;">
                                            (Weight: {{ ($totalPieces == (int)$totalPieces) ? (int)$totalPieces : number_format($totalPieces, 2) }} Kg)
                                        </div>
                                    @elseif($sizeMode === 'by_meter' && !in_array(strtolower($dispUnit), ['m', 'mtr', 'meter', 'mm']) && $totalPieces > 0)
                                        <div style="font-size: 11px; color: #64748b; font-weight: normal; margin-top: 1px;">
                                            (Length: {{ ($totalPieces == (int)$totalPieces) ? (int)$totalPieces : number_format($totalPieces, 2) }} Mtr)
                                        </div>
                                    @elseif($sizeMode === 'by_feet' && !in_array(strtolower($dispUnit), ['ft', 'feet', 'in', 'inch', 'inches']) && $totalPieces > 0)
                                        <div style="font-size: 11px; color: #64748b; font-weight: normal; margin-top: 1px;">
                                            (Length: {{ ($totalPieces == (int)$totalPieces) ? (int)$totalPieces : number_format($totalPieces, 2) }} Ft)
                                        </div>
                                    @endif
                                    @if(!empty($item['description']))
                                        <div style="font-size: 11px; color: #475569; font-weight: normal; margin-top: 2px;">
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
                <!-- Footer -->
                <div class="pad-footer">
                    <div class="footer-note">
                        Received the above goods in good order & condition<br><br><i>Receiver's Signature</i>
                    </div>
                    <div class="footer-sign">
                        <img src="{{ asset('assets/images/stamp.png') }}" style="width: 135px; height: auto; margin-top: -45px; margin-bottom: 3px; margin-right: 10px; display: inline-block; opacity: 0.85; transform: rotate(-12deg); mix-blend-mode: multiply;" alt="Stamp"><br>
                        For {{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    
    <script>
        function toggleRate() {
            document.body.classList.toggle('hide-rate');
            const btn = document.getElementById('toggleRateBtn');
            if (document.body.classList.contains('hide-rate')) {
                btn.innerHTML = '<i class="fa-solid fa-eye"></i> Show Rate';
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-success');
            } else {
                btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Hide Rate';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-warning');
            }
        }
    </script>
</body>
</html>

