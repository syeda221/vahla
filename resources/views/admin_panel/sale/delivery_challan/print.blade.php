<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $dc->dc_number }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
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
                <div class="invoice-badge">DELIVERY CHALLAN</div>
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
                    <div class="meta-value" style="color: #1e40af;">{{ $dc->dc_number }} (Order: {{ $dc->sale->invoice_no }})</div>
                </div>
                <div class="meta-row mt-2">
                    <div class="meta-label">M/s.</div>
                    <div class="meta-value" style="color: #1e40af;">{{ $dc->sale->walkin_name ?? ($dc->sale->customer_relation->customer_name ?? 'Walk-in Customer') }}</div>
                </div>
                <div class="mt-3" style="font-size: 11px; color: #1e40af; font-weight: bold;">
                    Please Receive the following goods, your order No. <span style="border-bottom: 1px solid var(--pad-border); display: inline-block; width: 150px; color: #000; font-weight: normal; text-align: center;">{{ $dc->sale->reference ?? '' }}</span>
                </div>
            </div>
            <div class="meta-right">
                <div class="meta-row">
                    <div class="meta-label" style="width:40px;">Date:</div>
                    <div class="meta-value" style="color: #1e40af;">{{ \Carbon\Carbon::parse($dc->dc_date)->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="pad-table">
            <thead>
                <tr>
                    <th style="width: 5%;">S.NO</th>
                    <th class="desc-col" style="width: 50%; text-align: left;">DESCRIPTION</th>
                    <th style="width: 15%;">DELIVERED QTY</th>
                    <th class="rate-col" style="width: 15%;">RATE</th>
                    <th style="width: 15%;">UNIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $emptyRows = max(0, 15 - count($dc->items)); 
                @endphp

                @foreach ($dc->items as $item)
                    @php
                        $productTitle = optional($item->product)->item_name ?? 'Unknown Item';
                        $saleItem = $item->saleItem;
                        $rate = $saleItem ? $saleItem->price : 0;
                        $unit = optional($item->product)->unit->name ?? 'Pcs';
                    @endphp

                    <tr>
                            @php
                                $variant = [];
                                $saleItem = $item->saleItem;
                                if ($saleItem && !empty($saleItem->color)) {
                                    $b64 = base64_decode($saleItem->color, true);
                                    if ($b64 !== false) $variant = json_decode($b64, true) ?: [];
                                    if (empty($variant)) $variant = json_decode($saleItem->color, true) ?: [];
                                }
                                $sizeMode = $saleItem->size_mode ?? optional($item->product)->size_mode ?? 'by_size';
                                $vUnit = strtolower($variant['unit'] ?? optional(optional($item->product)->unit)->name ?? '');
                                
                                $dispQtyFactor = 1;
                                $dispUnit = 'Pcs';
                                if (in_array($sizeMode, ['by_kg', 'by_gm'])) {
                                    if (in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                                        $dispUnit = 'Pcs';
                                        $wtConv = (float)($variant['conv_factor'] ?? $saleItem->pieces_per_box ?? 1);
                                        if ($wtConv <= 0) $wtConv = 1;
                                        $dispQtyFactor = 1 / $wtConv;
                                    } elseif (in_array($vUnit, ['gm', 'g'])) {
                                        $dispUnit = 'Gm';
                                        $dispQtyFactor = 1000;
                                    } else {
                                        $dispUnit = 'Kg';
                                    }
                                } elseif ($sizeMode === 'by_cartons') {
                                    $dispUnit = 'Ctn';
                                } elseif ($sizeMode === 'by_boxes') {
                                    $dispUnit = 'Box';
                                }

                                $rawQty = (float) $item->delivered_qty * $dispQtyFactor;
                                if ($item->delivered_qty == 0 && $item->qty > 0) {
                                    $rawQty = (float) $item->qty * $dispQtyFactor;
                                }
                                $dispQty = $rawQty == (int)$rawQty ? (int)$rawQty : number_format($rawQty, 3, '.', '');
                            @endphp
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td class="desc-col" style="font-weight: 500;">{{ $productTitle }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $dispQty }}</td>
                            <td class="rate-col" style="text-align: right;">{{ number_format((float)$rate, 2) }}</td>
                            <td style="text-align: center;">{{ $dispUnit }}</td>
                        
                    </tr>
                @endforeach
                
                @for($i=0; $i<$emptyRows; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td class="desc-col"></td>
                        <td></td>
                        <td class="rate-col"></td>
                        <td></td>
                        </tr>
                @endfor
                
                
            </tbody>
        </table>

        <!-- Footer -->
        <div class="pad-footer">
            <div class="footer-note">
                Received the above goods in good order & condition<br><br><br><br><i>Receiver's Signature</i>
            </div>
            <div class="footer-sign">
                For {{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}
            </div>
        </div>
    </div>
    
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

