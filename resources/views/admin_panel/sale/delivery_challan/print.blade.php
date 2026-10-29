<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $dc->dc_number }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style id="dynamicPageSize">
        @page {
            size: A4 portrait;
            margin: 4mm 6mm;
        }
    </style>
    <style>
        :root {
            --pad-blue: #1e40af;
            --pad-header-bg: #0284c7;
            --pad-light-blue: #eff6ff;
            --pad-alt-row: #f0f9ff;
            --pad-border: #3b82f6;
            --pad-text: #1e3a8a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            color: #000;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10.5px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Action Bar */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* A4 Sheet Container */
        .a4-sheet {
            width: 202mm;
            max-width: 100%;
            margin: 15px auto;
            background: transparent;
            padding: 0;
            box-sizing: border-box;
        }

        /* Each Half Sheet (Top Half & Bottom Half) */
        .invoice-half {
            width: 100%;
            height: 139mm;
            max-height: 140mm;
            min-height: 137mm;
            background: #ffffff;
            border: 1.5px solid var(--pad-border);
            border-radius: 4px;
            padding: 3.5mm 6mm 2.5mm 6mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
            margin-bottom: 0;
        }

        /* Cut Divider Line between 2 Halves */
        .cut-divider {
            width: 100%;
            height: 4mm;
            margin: 1.5mm 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            font-size: 9px;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.5px;
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
            padding: 0 8px;
            position: relative;
            z-index: 2;
        }

        /* Header Layout */
        .pad-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3px;
            border-bottom: 1.5px solid var(--pad-border);
            padding-bottom: 3px;
        }

        .logo-section {
            width: 44%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo-img {
            max-width: 130px;
            height: auto;
            max-height: 38px;
            object-fit: contain;
            margin-bottom: 2px;
        }

        .logo-text-box {
            display: flex;
            align-items: baseline;
            gap: 5px;
            margin-bottom: 2px;
        }

        .logo-title {
            font-size: 20px;
            font-weight: 900;
            color: #1e40af;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .logo-subtitle {
            font-size: 8.5px;
            font-weight: 700;
            color: #0284c7;
            letter-spacing: 1px;
        }

        .badge-wrapper {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px;
        }

        .invoice-badge {
            background-color: var(--pad-header-bg);
            color: #ffffff;
            text-align: center;
            font-weight: 800;
            font-size: 11px;
            padding: 2.5px 12px;
            display: inline-block;
            letter-spacing: 1.2px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .copy-type-tag {
            font-size: 9px;
            font-weight: 700;
            color: #1e40af;
            border: 1px solid var(--pad-border);
            padding: 1px 6px;
            border-radius: 2px;
            background: var(--pad-light-blue);
        }

        .company-details-section {
            width: 55%;
            font-size: 9px;
            line-height: 1.35;
            color: var(--pad-text);
            text-align: right;
        }

        .deals-in {
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 9px;
        }
        
        .deals-in span {
            color: #0284c7;
        }

        .contact-line {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 1px;
            font-size: 8.5px;
        }

        .contact-line i {
            color: #0ea5e9;
            margin-left: 5px;
            font-size: 9px;
            width: 12px;
            text-align: center;
        }

        /* Meta Section */
        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .meta-left {
            width: 60%;
        }
        
        .meta-row {
            margin-bottom: 1.5px;
            display: flex;
            align-items: baseline;
        }
        
        .meta-label {
            width: 48px;
            font-size: 9.5px;
            color: var(--pad-text);
        }
        
        .meta-value {
            border-bottom: 1px solid var(--pad-border);
            flex-grow: 1;
            padding-left: 5px;
            color: #1e40af;
            font-size: 10px;
            min-height: 13px;
        }

        .meta-right {
            width: 38%;
        }

        /* Pad Table */
        .pad-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
            border: 1px solid var(--pad-border);
        }

        .pad-table thead th {
            background-color: var(--pad-header-bg);
            color: #ffffff;
            text-align: center;
            padding: 3px 5px;
            font-size: 9px;
            border: 1px solid var(--pad-border);
            text-transform: uppercase;
            font-weight: 700;
        }

        .pad-table tbody td {
            border: 1px solid var(--pad-border);
            padding: 2.5px 5px;
            height: 18px;
            color: #000;
            font-size: 9.5px;
            vertical-align: middle;
        }
        
        .pad-table tbody tr:nth-child(even) {
            background-color: var(--pad-alt-row);
        }

        .totals-label {
            text-align: right;
            font-weight: bold;
            padding: 2px 5px;
            font-size: 9.5px;
            border-top: 1px solid var(--pad-border);
        }

        .totals-val {
            text-align: right;
            font-weight: bold;
            padding: 2px 5px;
            font-size: 9.5px;
            border-top: 1px solid var(--pad-border);
        }

        /* Hide Rate toggle */
        .hide-rate .rate-col {
            display: none !important;
        }
        
        .hide-rate .desc-col {
            width: 60% !important;
        }

        /* Footer */
        .pad-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 2px;
            font-size: 9px;
            font-weight: bold;
            padding-top: 1px;
            position: relative;
        }

        .footer-note {
            width: 50%;
            font-size: 8.5px;
            color: var(--pad-text);
            line-height: 1.25;
        }

        .footer-sign {
            width: 45%;
            text-align: right;
            position: relative;
            color: var(--pad-text);
            font-size: 9.5px;
            padding-right: 5px;
        }

        .footer-stamp {
            width: 70px;
            height: auto;
            position: absolute;
            right: 120px;
            bottom: -5px;
            opacity: 0.85;
            transform: rotate(-10deg);
            mix-blend-mode: multiply;
            pointer-events: none;
        }

        /* Single Copy Mode (hides bottom half on screen & print) */
        body.single-copy-mode .invoice-half.bottom-half,
        body.single-copy-mode .cut-divider,
        body.single-copy-mode .continuation-divider {
            display: none !important;
        }

        /* Print Media Rules */
        @media print {
            body { 
                background: #ffffff !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .action-bar, .no-print { 
                display: none !important; 
            }

            .a4-sheet {
                margin: 0 auto !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                page-break-after: always !important;
                break-after: page !important;
                height: 286mm !important;
                max-height: 288mm !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }

            .a4-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .invoice-half { 
                margin: 0 auto !important; 
                box-shadow: none !important; 
                border: 1.5px solid var(--pad-border) !important;
                padding: 3.5mm 6mm 2.5mm 6mm !important;
                width: 100% !important;
                max-width: 198mm !important;
                height: 139mm !important;
                max-height: 140mm !important;
                min-height: 137mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .cut-divider {
                display: flex !important;
                height: 4mm !important;
                margin: 1.5mm 0 !important;
            }

            .cut-divider span {
                background: #ffffff !important;
            }

            .invoice-badge {
                background-color: #0284c7 !important;
                color: #ffffff !important;
            }

            .pad-table thead th {
                background-color: #0284c7 !important;
                color: #ffffff !important;
            }

            .pad-table tbody tr:nth-child(even) {
                background-color: #f0f9ff !important;
            }

            body.single-copy-mode .a4-sheet {
                height: auto !important;
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
                <i class="fa-solid fa-print me-1"></i> <span id="modeBadgeText">A4 Portrait (2 Halves / Copies)</span>
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="toggleSingleOrTwoCopies()" class="btn btn-outline-primary shadow-sm" id="toggleCopiesBtn">
                <i class="fa-solid fa-copy me-1"></i> <span id="copiesBtnText">Print 1 Copy (Half Sheet)</span>
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
        $dcItemsColl = $dc->items;
        $totalItems = $dcItemsColl->count();
        $itemsPerHalf = 5;
        $isTwoCopies = $totalItems <= $itemsPerHalf;

        $sale = $dc->sale;
        $customerName = 'Walk-in Customer';
        if ($sale) {
            $customerName = $sale->walkin_name ?? (optional($sale->customer_relation)->customer_name ?? 'Walk-in Customer');
        } elseif ($dc->customer_id) {
            $customerName = optional($dc->customer)->customer_name ?? 'Customer';
        }

        $lpoReference = $dc->reference_no ?: (optional($sale)->reference ?? '');
        $dcNumbers = $dc->dc_number;

        // Calculate totals
        $dcTotalAmount = 0;
        foreach ($dcItemsColl as $dci) {
            $r = $dci->saleItem ? (float)$dci->saleItem->price : (float)($dci->price ?? 0);
            $q = (float)($dci->delivered_qty ?? 0);
            $dcTotalAmount += ($q * $r);
        }

        $paidAmount = $sale ? ((float)($sale->cash ?? 0) + (float)($sale->card ?? 0)) : 0;
        $finalTotal = $sale ? (float)($sale->total_net ?? $dcTotalAmount) : $dcTotalAmount;
        $dueAmount = max(0, $finalTotal - $paidAmount);

        // Logo resolution
        $companyLogo = null;
        $possibleLogos = [
            'storage/settings/wyGOxkWbD91UardqrGhOkinftnF5Kq49l5eWo3Ur.png',
            'storage/settings/fDqhIpEeIMkyRwN8po7SyXQwIG8VoN5csDC2D82n.png',
            'uploads/settings/1786018001_logo.png',
            \App\Models\Setting::get('company_logo'),
        ];
        foreach ($possibleLogos as $pl) {
            if (!empty($pl) && file_exists(public_path($pl))) {
                $companyLogo = $pl;
                break;
            }
        }
    @endphp

    @if ($isTwoCopies)
        {{-- CASE 1: Few Items (<= 5) -> Two identical copies (Original & Duplicate) on 1 single A4 sheet --}}
        <div class="a4-sheet">
            <!-- TOP HALF: Original Copy -->
            <div class="invoice-half top-half">
                @include('admin_panel.sale.partials.dc_half_body', [
                    'copyType' => 'ORIGINAL',
                    'chunkItems' => $dcItemsColl,
                    'isFirstPart' => true,
                    'isLastPart' => true,
                    'startIndex' => 1,
                    'companyLogo' => $companyLogo,
                    'sale' => $sale,
                    'customerName' => $customerName,
                    'lpoReference' => $lpoReference,
                    'dcNumbers' => $dcNumbers,
                    'finalTotal' => $finalTotal,
                    'paidAmount' => $paidAmount,
                    'dueAmount' => $dueAmount,
                ])
            </div>

            <!-- CUT DIVIDER -->
            <div class="cut-divider">
                <span><i class="fa-solid fa-scissors me-1"></i> CUT HERE</span>
            </div>

            <!-- BOTTOM HALF: Duplicate Copy -->
            <div class="invoice-half bottom-half">
                @include('admin_panel.sale.partials.dc_half_body', [
                    'copyType' => 'DUPLICATE',
                    'chunkItems' => $dcItemsColl,
                    'isFirstPart' => true,
                    'isLastPart' => true,
                    'startIndex' => 1,
                    'companyLogo' => $companyLogo,
                    'sale' => $sale,
                    'customerName' => $customerName,
                    'lpoReference' => $lpoReference,
                    'dcNumbers' => $dcNumbers,
                    'finalTotal' => $finalTotal,
                    'paidAmount' => $paidAmount,
                    'dueAmount' => $dueAmount,
                ])
            </div>
        </div>
    @else
        {{-- CASE 2: More Items (> 5) -> Table continues on the bottom half of the same A4 sheet --}}
        @php
            $halves = $dcItemsColl->chunk($itemsPerHalf);
            $totalHalves = $halves->count();
            $sheets = $halves->chunk(2);
        @endphp

        @foreach ($sheets as $sheetIndex => $sheetHalves)
            <div class="a4-sheet">
                @php
                    $firstHalf = $sheetHalves->first();
                    $secondHalf = $sheetHalves->count() > 1 ? $sheetHalves->last() : null;
                    
                    $firstHalfIndex = $sheetIndex * 2;
                    $firstStartIndex = ($firstHalfIndex * $itemsPerHalf) + 1;
                    $isFirstHalfLast = ($firstHalfIndex + 1) === $totalHalves;

                    $secondHalfIndex = ($sheetIndex * 2) + 1;
                    $secondStartIndex = ($secondHalfIndex * $itemsPerHalf) + 1;
                    $isSecondHalfLast = ($secondHalfIndex + 1) === $totalHalves;
                @endphp

                <!-- TOP HALF -->
                <div class="invoice-half top-half">
                    @include('admin_panel.sale.partials.dc_half_body', [
                        'copyType' => 'PAGE ' . ($firstHalfIndex + 1) . '/' . $totalHalves,
                        'chunkItems' => $firstHalf,
                        'isFirstPart' => ($firstHalfIndex === 0),
                        'isLastPart' => $isFirstHalfLast,
                        'startIndex' => $firstStartIndex,
                        'companyLogo' => $companyLogo,
                        'sale' => $sale,
                        'customerName' => $customerName,
                        'lpoReference' => $lpoReference,
                        'dcNumbers' => $dcNumbers,
                        'finalTotal' => $finalTotal,
                        'paidAmount' => $paidAmount,
                        'dueAmount' => $dueAmount,
                    ])
                </div>

                @if ($secondHalf)
                    <!-- CUT / CONTINUATION DIVIDER -->
                    <div class="cut-divider">
                        <span><i class="fa-solid fa-arrow-down me-1"></i> CONTINUED ON NEXT HALF</span>
                    </div>

                    <!-- BOTTOM HALF -->
                    <div class="invoice-half bottom-half">
                        @include('admin_panel.sale.partials.dc_half_body', [
                            'copyType' => 'PAGE ' . ($secondHalfIndex + 1) . '/' . $totalHalves,
                            'chunkItems' => $secondHalf,
                            'isFirstPart' => false,
                            'isLastPart' => $isSecondHalfLast,
                            'startIndex' => $secondStartIndex,
                            'companyLogo' => $companyLogo,
                            'sale' => $sale,
                            'customerName' => $customerName,
                            'lpoReference' => $lpoReference,
                            'dcNumbers' => $dcNumbers,
                            'finalTotal' => $finalTotal,
                            'paidAmount' => $paidAmount,
                            'dueAmount' => $dueAmount,
                        ])
                    </div>
                @endif
            </div>
        @endforeach
    @endif

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

        function toggleSingleOrTwoCopies() {
            document.body.classList.toggle('single-copy-mode');
            const btnText = document.getElementById('copiesBtnText');
            const badgeText = document.getElementById('modeBadgeText');
            const dynStyle = document.getElementById('dynamicPageSize');

            if (document.body.classList.contains('single-copy-mode')) {
                btnText.innerText = 'Print 2 Halves (Full A4)';
                badgeText.innerText = 'A5 Landscape (Single Half)';
                dynStyle.innerHTML = '@page { size: A5 landscape; margin: 4mm; }';
            } else {
                btnText.innerText = 'Print 1 Copy (Half Sheet)';
                badgeText.innerText = 'A4 Portrait (2 Halves / Copies)';
                dynStyle.innerHTML = '@page { size: A4 portrait; margin: 4mm 6mm; }';
            }
        }
    </script>
</body>
</html>