<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if ($sale->sale_type === 'quotation')
            Quotation - {{ $sale->invoice_no ?: ('QUO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}
        @elseif ($sale->sale_type === 'sales_order' && $sale->sale_status !== 'posted')
            Sales Order - {{ $sale->invoice_no ?: ('SO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}
        @else
            Quotation/Estimate - {{ $sale->invoice_no }}
        @endif
    </title>
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
            max-width: 210mm;
            min-height: 148mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            color: var(--pad-text);
        }

        /* Header Layout */
        .pad-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--pad-border);
            padding-bottom: 15px;
        }

        .logo-section {
            width: 45%;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            margin-bottom: 8px;
        }

        .invoice-badge {
            background-color: #3b82f6;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            padding: 4px 10px;
            display: inline-block;
            letter-spacing: 2px;
            width: 80%;
            border-radius: 2px;
        }

        .company-details-section {
            width: 50%;
            font-size: 11px;
            line-height: 1.5;
            color: var(--pad-text);
            text-align: right;
        }

        .deals-in {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11.5px;
        }
        
        .deals-in span {
            color: #0284c7;
        }

        .contact-line {
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            margin-bottom: 4px;
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
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 13px;
        }
        
        .meta-left {
            width: 60%;
        }
        
        .meta-row {
            margin-bottom: 8px;
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
            margin-bottom: 20px;
            border: 1px solid var(--pad-border);
        }

        .pad-table thead th {
            background-color: #374151;
            color: #ffffff;
            text-align: center;
            padding: 8px;
            font-size: 12px;
            border: 1px solid var(--pad-border);
            text-transform: uppercase;
        }

        .pad-table tbody td {
            border: 1px solid var(--pad-border);
            padding: 6px 8px;
            height: 28px;
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
            margin-top: 30px;
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
            padding-top: 30px;
            border-top: 1px solid transparent;
            color: var(--pad-text);
        }

        @media print {
            body { background: #fff; }
            .action-bar { display: none !important; }
            .invoice-page { 
                margin: 0; 
                box-shadow: none; 
                padding: 10mm;
                max-width: 100%;
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
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="action-bar no-print d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark fs-6">
                <i class="fa-solid fa-file-invoice me-1 text-primary"></i>
                Quotation Preview
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-print"></i> Print Quotation
            </button>
            <a href="javascript:history.back()" class="btn btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="invoice-page">
        <!-- Header -->
        <div class="pad-header">
            <div class="logo-section">
                @if(\App\Models\Setting::get('company_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('company_logo')) }}" alt="Company Logo" class="logo-img">
                @else
                    <h2 style="margin:0; font-weight:900; font-size:28px; color: #1e40af;">{{ \App\Models\Setting::get('company_name', 'VAHLA') }}</h2>
                    <div style="font-size:12px; font-weight:bold; letter-spacing:1px; margin-bottom:8px; color: #1e40af;">INDUSTRIAL SOLUTIONS</div>
                @endif
                <div class="invoice-badge">QUOTATION</div>
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
                    <div class="meta-label" style="width:60px;">S. No.</div>
                    <div class="meta-value" style="color: #1e40af;">
                        @if ($sale->sale_type === 'quotation')
                            {{ $sale->invoice_no ?: ('QUO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}
                        @elseif ($sale->sale_type === 'sales_order' && $sale->sale_status !== 'posted')
                            {{ $sale->invoice_no ?: ('SO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}
                        @else
                            {{ $sale->invoice_no }}
                        @endif
                    </div>
                </div>
                <div class="meta-row mt-2">
                    <div class="meta-label">M/s.</div>
                    <div class="meta-value" style="color: #1e40af;">{{ $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walk-in Customer') }}</div>
                </div>
                
            </div>
            <div class="meta-right">
                <div class="meta-row">
                    <div class="meta-label" style="width:40px;">Date:</div>
                    <div class="meta-value" style="color: #1e40af;">{{ $sale->created_at ? $sale->created_at->format('d-m-Y') : date('d-m-Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="pad-table">
            <thead>
                                <tr>
                    <th style="width: 5%;">S.NO</th>
                    <th style="width: 50%; text-align: left;">DESCRIPTION</th>
                    <th style="width: 15%;">QTY</th>
                    <th style="width: 15%;">Unit</th>
                    <th style="width: 15%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $emptyRows = max(0, 15 - count($saleItems)); 
                @endphp

                @foreach ($saleItems as $index => $item)
                    @php
                        $piecesPerBox = (int)($item['pieces_per_box'] ?? 1);
                        if ($piecesPerBox <= 0) $piecesPerBox = 1;
                        
                        $totalPieces = (int)($item['total_pieces'] ?? 0);
                        $boxes = floor($totalPieces / $piecesPerBox);
                        $loosePieces = $totalPieces % $piecesPerBox;
                        $sizeMode = $item['size_mode'] ?? 'by_size';
                        $variantUnit = strtolower($item['variant_unit'] ?? '');
                        
                        // Disp Qty
                        $dispQty = $totalPieces;
                        $dispUnit = 'Pcs';
                        if ($sizeMode === 'by_kg' || $sizeMode === 'by_gm') {
                            $kgWt = (float)($item['qty_box'] ?? $item['qty'] ?? 0);
                            $wtConv = (float)($item['pieces_per_box'] ?? 0);
                            if ($wtConv <= 0) $wtConv = 1;
                            if (in_array($variantUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                                $dispQty = $kgWt > 0 ? $kgWt / $wtConv : 0;
                                $dispUnit = 'Pcs';
                            } elseif (in_array($variantUnit, ['gm', 'g'])) {
                                $dispQty = $kgWt * 1000;
                                $dispUnit = 'Gm';
                            } else {
                                $dispQty = $kgWt;
                                $dispUnit = 'Kg';
                            }
                        } else {
                            if ($sizeMode == 'by_pieces') {
                                $dispQty = $totalPieces;
                                $dispUnit = 'Pcs';
                            } else {
                                if ($boxes > 0 && $loosePieces == 0) {
                                    $dispQty = $boxes;
                                    $dispUnit = $sizeMode == 'by_cartons' ? 'Ctn' : 'Box';
                                } elseif ($boxes == 0 && $loosePieces > 0) {
                                    $dispQty = $loosePieces;
                                    $dispUnit = 'Pcs';
                                } else {
                                    $dispQty = $totalPieces;
                                    $dispUnit = 'Pcs';
                                }
                            }
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
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: left; font-weight: 500;">{{ $productTitle }}</td>
                        <td style="text-align: center;">{{ ($dispQty == (int)$dispQty) ? (int)$dispQty : number_format($dispQty, 3) }}</td>
                        <td style="text-align: center;">{{ $dispUnit }}</td>
                        <td style="text-align: right;">{{ number_format((float)($item['total'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                
                @for($i=0; $i<$emptyRows; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
                
                
            <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; padding-right: 15px;">TOTAL</td>
                    <td style="text-align: right; font-weight: bold; color: #1e40af;">{{ number_format($sale->total_net, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="pad-footer">
            <div class="footer-note">
                Goods once sold can not be exchange<br>or taken back without receipt
            </div>
            <div class="footer-sign">
                For {{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}
            </div>
        </div>
    </div>
</body>
</html>

