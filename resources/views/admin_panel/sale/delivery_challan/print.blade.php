<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $dc->dc_number }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style id="dynamicPageSize">
        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
    <style>
        :root {
            --pad-blue: #1e40af;
            --pad-light-blue: #e0f2fe;
            --pad-border: #3b82f6;
            --pad-text: #1e3a8a;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            color: var(--pad-text);
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9.5px;
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

        /* A4 Page Container: 210mm x 297mm */
        .a4-sheet {
            width: 210mm;
            height: 297mm;
            max-height: 297mm;
            margin: 15px auto;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .a4-sheet:not(:last-child) {
            page-break-after: always !important;
            break-after: page !important;
        }

        .a4-sheet:last-child {
            page-break-after: auto !important;
            break-after: auto !important;
        }

        /* Equal Halves: Top (210mm x 148.5mm) & Bottom (210mm x 148.5mm) */
        .half {
            position: relative;
            width: 210mm;
            height: 148.5mm;
            max-height: 148.5mm;
            overflow: hidden;
            box-sizing: border-box;
            background: #ffffff;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Blank Half Notice (Only in preview) */
        .blank-half-notice {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: #fafafa;
            border: 1px dashed #cbd5e1;
            margin: 4mm;
            border-radius: 4px;
        }

        /* Cut Divider Line between 2 Halves */
        .cut-divider {
            position: absolute;
            top: 148.5mm;
            left: 0;
            width: 210mm;
            height: 0;
            border-top: 1.5px dashed #94a3b8;
            z-index: 100;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cut-divider span {
            transform: translateY(-50%);
            background: #ffffff;
            padding: 0 10px;
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            letter-spacing: 1px;
        }

        /* Rotated A5 Portrait Copy (136mm x 194mm rotated 90deg centered inside 210mm x 148.5mm half) */
        .copy {
            position: absolute;
            left: 37mm;
            top: -22.75mm;
            width: 136mm;
            height: 194mm;
            transform-origin: center center;
            transform: rotate(90deg);
            box-sizing: border-box;
            padding: 3mm 5mm 2.5mm 5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
            overflow: hidden;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--pad-text);
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .invoice-content-top {
            flex-shrink: 0;
        }

        /* Counter-clockwise rotation if toggled */
        body.rotate-counter .copy {
            transform: rotate(-90deg);
        }

        /* Header Layout (from saleinvoice) */
        .pad-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            border-bottom: 2px solid var(--pad-border);
            padding-bottom: 8px;
        }

        .logo-section {
            width: 44%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            max-height: 52px;
            object-fit: contain;
            margin-bottom: 4px;
        }

        .invoice-badge-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .invoice-badge {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 3px 12px;
            display: inline-block;
            letter-spacing: 1.5px;
            border-radius: 2px;
            text-transform: uppercase;
        }

        .copy-type-tag {
            font-size: 8px;
            font-weight: 700;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            border-radius: 3px;
            padding: 1px 6px;
            letter-spacing: 0.5px;
        }

        .company-details-section {
            width: 55%;
            font-size: 8.5px;
            line-height: 1.35;
            color: var(--pad-text);
            text-align: right;
        }

        .deals-in {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 9px;
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
            margin-left: 6px;
            margin-top: 2px;
            font-size: 9px;
            width: 12px;
            text-align: center;
        }

        /* Meta Section (S.No, Date, etc - from saleinvoice) */
        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 9.5px;
            color: var(--pad-text);
        }

        .meta-left {
            width: 60%;
        }

        .meta-right {
            width: 36%;
        }

        .meta-row {
            margin-bottom: 5px;
            display: flex;
            align-items: flex-end;
        }

        .meta-label {
            width: 48px;
            flex-shrink: 0;
            color: var(--pad-text);
            font-size: 9.5px;
        }

        .meta-value {
            border-bottom: 1.5px solid var(--pad-border);
            flex-grow: 1;
            padding-left: 5px;
            color: var(--pad-text);
            font-weight: bold;
            font-size: 9.5px;
            min-height: 16px;
            line-height: 1.2;
        }

        /* Pad Table (from saleinvoice) */
        .pad-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 4px;
            border: 1.5px solid var(--pad-border);
        }

        .pad-table thead th {
            background-color: #374151 !important;
            color: #ffffff !important;
            text-align: center;
            padding: 4px 5px;
            font-size: 8.5px;
            border: 1px solid var(--pad-border);
            text-transform: uppercase;
            font-weight: bold;
        }

        .pad-table tbody td {
            border: 1px solid var(--pad-border);
            padding: 3px 5px;
            height: 19px;
            color: #000;
            vertical-align: middle;
            font-size: 8.5px;
        }

        .pad-table tbody tr:nth-child(even) {
            background-color: rgba(224, 242, 254, 0.4);
        }

        .pad-table tfoot td {
            border: 1px solid var(--pad-border);
            padding: 2.5px 5px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        /* Footer (from saleinvoice) */
        .pad-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 4px;
            flex-shrink: 0;
        }

        .footer-note {
            width: 55%;
            font-size: 8.5px;
            font-weight: bold;
            color: var(--pad-text);
            line-height: 1.35;
        }

        .footer-sign {
            width: 42%;
            text-align: right;
            color: var(--pad-text);
            position: relative;
        }

        .footer-stamp {
            width: 65px;
            height: auto;
            margin-bottom: -4px;
            margin-right: 12px;
            display: inline-block;
            opacity: 0.9;
            transform: rotate(-10deg);
            mix-blend-mode: multiply;
        }

        .for-company {
            font-size: 9px;
            font-weight: bold;
            color: var(--pad-text);
            position: relative;
            z-index: 5;
        }

        /* Hide Rate behavior */
        .hide-rate .rate-col {
            display: none !important;
        }

        .hide-rate .desc-col {
            width: 73% !important;
        }

        .pad-table, .pad-table tr, .pad-table tbody, .pad-header, .pad-footer {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .pad-table thead {
            display: table-row-group !important;
        }

        /* Print Media Styles */
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                width: 210mm !important;
                height: 297mm !important;
            }

            .action-bar {
                display: none !important;
            }

            .no-print {
                display: none !important;
            }

            .a4-sheet {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                width: 210mm !important;
                height: 297mm !important;
                max-height: 297mm !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .a4-sheet:not(:last-child) {
                page-break-after: always !important;
                break-after: page !important;
            }

            .a4-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .half {
                width: 210mm !important;
                height: 148.5mm !important;
                max-height: 148.5mm !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .cut-divider {
                top: 148.5mm !important;
            }

            .copy {
                position: absolute !important;
                left: 37mm !important;
                top: -22.75mm !important;
                width: 136mm !important;
                height: 194mm !important;
                transform-origin: center center !important;
                transform: rotate(90deg) !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            body.rotate-counter .copy {
                transform: rotate(-90deg) !important;
            }

            .cut-divider span {
                background: #ffffff !important;
            }

            .invoice-badge {
                background-color: #3b82f6 !important;
                color: #ffffff !important;
            }

            .pad-table thead th {
                background-color: #374151 !important;
                color: #ffffff !important;
            }

            .pad-table tbody tr:nth-child(even) {
                background-color: rgba(224, 242, 254, 0.4) !important;
            }
        }

        /* Half Sheet (Single Copy Cut Paper) */
        body.single-copy-mode .a4-sheet {
            height: 148.5mm !important;
            margin: 10px auto !important;
        }

        body.single-copy-mode .half-bottom,
        body.single-copy-mode .cut-divider {
            display: none !important;
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
                <i class="fa-solid fa-print me-1"></i> <span id="modeBadgeText">1 Print (Half Sheet Layout)</span>
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="toggleTitlePill()" class="btn btn-outline-primary shadow-sm" id="toggleTitleBtn">
                <i class="fa-solid fa-tag me-1"></i> <span id="titleBtnText">Title: DELIVERY CHALLAN</span>
            </button>
            <button type="button" onclick="toggleRotation()" class="btn btn-outline-secondary shadow-sm" id="toggleRotateBtn">
                <i class="fa-solid fa-rotate me-1"></i> <span id="rotateBtnText">Rotate: 90°</span>
            </button>
            @php
                $dcItemsColl = $dc->items;
                $totalItems = $dcItemsColl->count();
                $itemsPerHalf = 11;
                $isSingleHalf = $totalItems <= $itemsPerHalf;
            @endphp
            @if ($isSingleHalf)
            <button type="button" onclick="toggleDuplicate()" class="btn btn-outline-primary shadow-sm" id="toggleDuplicateBtn">
                <i class="fa-solid fa-copy me-1"></i> <span id="duplicateBtnText">Print 2 Copies</span>
            </button>
            @endif
            <button type="button" onclick="toggleSingleOrTwoCopies()" class="btn btn-outline-secondary shadow-sm" id="toggleCopiesBtn">
                <i class="fa-solid fa-scissors me-1"></i> <span id="copiesBtnText">Paper: Full A4</span>
            </button>
            <button type="button" onclick="toggleRate()" class="btn btn-warning shadow-sm" id="toggleRateBtn">
                <i class="fa-solid fa-eye-slash me-1"></i> Hide Rate
            </button>
            <button type="button" onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Print DC
            </button>
            <a href="javascript:history.back()" class="btn btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @php
        $sale = $dc->sale;
        $customerName = 'Without Customer';
        if ($sale) {
            $customerName = $sale->walkin_name ?? (optional($sale->customer_relation)->customer_name ?? 'Without Customer');
        } elseif ($dc->customer_id) {
            $customerName = optional($dc->customer)->customer_name ?? 'Without Customer';
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

    @if ($isSingleHalf)
        {{-- CASE 1: Few Items (<= 6) -> BY DEFAULT 1 PRINT on Top Half --}}
        <div class="a4-sheet" id="sheetSingle">
            <!-- TOP HALF: The 1 Print -->
            <div class="half half-top">
                <div class="copy">
                    @include('admin_panel.sale.partials.dc_half_body', [
                        'copyType' => '',
                        'badgeTitle' => 'DELIVERY CHALLAN',
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

            <!-- CUT DIVIDER -->
            <div class="cut-divider">
                <span><i class="fa-solid fa-scissors me-1"></i> CUT LINE</span>
            </div>

            <!-- BOTTOM HALF: Blank by default (Only 1 print). Toggled to Duplicate if user clicks button -->
            <div class="half half-bottom" id="bottomHalfWrap">
                <!-- Blank notice shown in preview only -->
                <div class="blank-half-notice no-print" id="blankNotice">
                    <div class="text-muted text-center py-4">
                        <i class="fa-solid fa-file-lines fa-2x mb-2 text-secondary"></i><br>
                        <strong>Bottom Half Blank</strong> (Single Print Mode)<br>
                        <small class="text-secondary">Agar duplicate copy chahiye to upar "Print 2 Copies" dabayein</small>
                    </div>
                </div>

                <!-- Duplicate copy (Hidden by default) -->
                <div class="copy" id="duplicateCopy" style="display: none;">
                    @include('admin_panel.sale.partials.dc_half_body', [
                        'copyType' => 'DUPLICATE',
                        'badgeTitle' => 'DELIVERY CHALLAN',
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
        </div>
    @else
        {{-- CASE 2: More Items (> 6) -> Take remaining products to next half paper --}}
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

                <!-- TOP HALF (Chunk 1) -->
                <div class="half half-top">
                    <div class="copy">
                        @include('admin_panel.sale.partials.dc_half_body', [
                            'copyType' => 'PAGE ' . ($firstHalfIndex + 1) . '/' . $totalHalves,
                            'badgeTitle' => 'DELIVERY CHALLAN',
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
                </div>

                @if ($secondHalf)
                    <!-- CUT / CONTINUATION DIVIDER -->
                    <div class="cut-divider">
                        <span><i class="fa-solid fa-arrow-down me-1"></i> CONTINUED ON NEXT HALF</span>
                    </div>

                    <!-- BOTTOM HALF: Remaining Products on Next Half -->
                    <div class="half half-bottom">
                        <div class="copy">
                            @include('admin_panel.sale.partials.dc_half_body', [
                                'copyType' => 'PAGE ' . ($secondHalfIndex + 1) . '/' . $totalHalves,
                                'badgeTitle' => 'DELIVERY CHALLAN',
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
                    </div>
                @else
                    <!-- CUT DIVIDER -->
                    <div class="cut-divider">
                        <span><i class="fa-solid fa-scissors me-1"></i> CUT LINE</span>
                    </div>

                    <!-- Blank bottom half if odd number of chunks -->
                    <div class="half half-bottom">
                        <div class="blank-half-notice no-print">
                            <div class="text-muted text-center py-4">
                                <i class="fa-solid fa-check text-success mb-2 fa-2x"></i><br>
                                <strong>Challan Completed on Top Half</strong>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <script>
        function toggleDuplicate() {
            const dup = document.getElementById('duplicateCopy');
            const notice = document.getElementById('blankNotice');
            const btn = document.getElementById('duplicateBtnText');
            const btnEl = document.getElementById('toggleDuplicateBtn');
            if (!dup) return;

            if (dup.style.display === 'none') {
                dup.style.display = 'flex';
                if (notice) notice.style.display = 'none';
                btn.innerText = 'Showing 2 Copies';
                btnEl.classList.remove('btn-outline-primary');
                btnEl.classList.add('btn-primary');
            } else {
                dup.style.display = 'none';
                if (notice) notice.style.display = 'block';
                btn.innerText = 'Print 2 Copies';
                btnEl.classList.remove('btn-primary');
                btnEl.classList.add('btn-outline-primary');
            }
        }

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
            const dynStyle = document.getElementById('dynamicPageSize');

            if (document.body.classList.contains('single-copy-mode')) {
                btnText.innerText = 'Paper: Half Sheet';
                dynStyle.innerHTML = '@page { size: 210mm 148.5mm; margin: 0; }';
            } else {
                btnText.innerText = 'Paper: Full A4';
                dynStyle.innerHTML = '@page { size: A4 portrait; margin: 0; }';
            }
        }

        function toggleRotation() {
            document.body.classList.toggle('rotate-counter');
            const btnText = document.getElementById('rotateBtnText');
            if (document.body.classList.contains('rotate-counter')) {
                btnText.innerText = 'Rotate: -90°';
            } else {
                btnText.innerText = 'Rotate: 90°';
            }
        }

        function toggleTitlePill() {
            const pills = document.querySelectorAll('.pill-title-text');
            const btnText = document.getElementById('titleBtnText');
            pills.forEach(p => {
                if (p.innerText.trim() === 'DELIVERY CHALLAN') {
                    p.innerText = 'INVOICE / BILL';
                    btnText.innerText = 'Title: INVOICE / BILL';
                } else {
                    p.innerText = 'DELIVERY CHALLAN';
                    btnText.innerText = 'Title: DELIVERY CHALLAN';
                }
            });
        }
    </script>
</body>
</html>
