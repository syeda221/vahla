<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Challan - {{ $dc->dc_number }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('assets/js/html2pdf.bundle.min.js') }}"></script>
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
            height: 295mm;
            max-height: 295mm;
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
            page-break-after: avoid !important;
            break-after: avoid !important;
        }

        /* Equal Halves: Top & Bottom */
        .half {
            position: relative;
            width: 210mm;
            height: 147mm;
            max-height: 147mm;
            overflow: hidden;
            box-sizing: border-box;
            background: #ffffff;
            contain: paint;
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
            top: 147mm;
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

        /* Wrapper inside Half to provide 6mm padding on all sides */
        .half-wrapper {
            position: absolute;
            left: 6mm;
            top: 6mm;
            width: 198mm;
            height: 135mm;
            overflow: visible;
            box-sizing: border-box;
        }

        /* Rotated A5 Portrait Copy (135mm x 198mm rotated 90deg to fill 198mm x 135mm wrapper) */
        .copy {
            position: absolute;
            left: 198mm;
            top: 0;
            width: 135mm;
            height: 198mm;
            transform-origin: 0 0;
            transform: rotate(90deg);
            box-sizing: border-box;
            padding: 2mm 3.5mm;
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

        /* Counter-clockwise rotation if toggled */
        body.rotate-counter .copy {
            left: 0;
            top: 135mm;
            transform-origin: 0 0;
            transform: rotate(-90deg);
        }

        .invoice-content-top {
            flex-shrink: 0;
        }

        /* Header Layout (from saleinvoice) */
        .pad-header {
            display: flex;
            justify-content: space-between;
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
            max-width: 100%;
            height: auto;
            max-height: 38px;
            object-fit: contain;
            margin-bottom: 2px;
        }

        .invoice-badge-wrap {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 1px;
        }

        .invoice-badge {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            text-align: center;
            font-weight: bold;
            font-size: 9.5px;
            padding: 2px 10px;
            display: inline-block;
            letter-spacing: 1px;
            border-radius: 2px;
            text-transform: uppercase;
        }

        .copy-type-tag {
            font-size: 7.5px;
            font-weight: 700;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            border-radius: 3px;
            padding: 1px 4px;
            letter-spacing: 0.5px;
        }

        .company-details-section {
            width: 55%;
            font-size: 7.5px;
            line-height: 1.25;
            color: var(--pad-text);
            text-align: right;
        }

        .deals-in {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 8px;
        }

        .deals-in span {
            color: #0284c7;
        }

        .contact-line {
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            margin-bottom: 1px;
        }

        .contact-line i {
            color: #0ea5e9;
            margin-left: 5px;
            margin-top: 1px;
            font-size: 8px;
            width: 10px;
            text-align: center;
        }

        /* Meta Section (S.No, Date, etc - from saleinvoice) */
        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 8px;
            color: var(--pad-text);
        }

        .meta-left {
            width: 60%;
        }

        .meta-right {
            width: 38%;
        }

        .meta-row {
            margin-bottom: 2px;
            display: flex;
            align-items: flex-end;
        }

        .meta-label {
            width: 42px;
            flex-shrink: 0;
            color: var(--pad-text);
            font-size: 8px;
        }

        .meta-value {
            border-bottom: 1px solid var(--pad-border);
            flex-grow: 1;
            padding-left: 4px;
            color: var(--pad-text);
            font-weight: bold;
            font-size: 8px;
            min-height: 13px;
            line-height: 1.1;
        }

        /* Pad Table (from saleinvoice) */
        .pad-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 2px;
            border: 1.2px solid var(--pad-border);
        }

        .pad-table thead th {
            background-color: #374151 !important;
            color: #ffffff !important;
            text-align: center;
            padding: 3.5px 4px;
            font-size: 8px;
            border: 1px solid var(--pad-border);
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .pad-table tbody td {
            border: 1px solid var(--pad-border);
            padding: 3px 4px;
            color: #000;
            vertical-align: middle;
            font-size: 8.5px;
            line-height: 1.25;
        }

        .pad-table tbody tr:nth-child(even) {
            background-color: rgba(224, 242, 254, 0.4);
        }

        .pad-table tfoot td {
            border: 1px solid var(--pad-border);
            padding: 2.5px 4px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        /* Footer (from saleinvoice) */
        .pad-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 2px;
            flex-shrink: 0;
        }

        .footer-note {
            width: 55%;
            font-size: 7.5px;
            font-weight: bold;
            color: var(--pad-text);
            line-height: 1.25;
        }

        .footer-sign {
            width: 42%;
            text-align: right;
            color: var(--pad-text);
            position: relative;
        }

        .footer-stamp {
            width: 52px;
            height: auto;
            margin-bottom: -4px;
            margin-right: 8px;
            display: inline-block;
            opacity: 0.9;
            transform: rotate(-10deg);
            mix-blend-mode: multiply;
        }

        .for-company {
            font-size: 8px;
            font-weight: bold;
            color: var(--pad-text);
            position: relative;
            z-index: 5;
        }
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
            @page {
                size: 210mm 297mm;
                margin: 0;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                width: 210mm !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: hidden !important;
            }

            .action-bar {
                display: none !important;
            }

            .no-print {
                display: none !important;
            }

            #whatsappPdfContainer {
                display: none !important;
            }

            .a4-sheet {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                width: 210mm !important;
                height: 295mm !important;
                max-height: 295mm !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .a4-sheet:not(:last-child) {
                page-break-after: always !important;
                break-after: page !important;
            }

            .a4-sheet:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .half {
                width: 210mm !important;
                height: 147mm !important;
                max-height: 147mm !important;
                contain: paint !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }

            .half-wrapper {
                position: absolute !important;
                left: 6mm !important;
                top: 6mm !important;
                width: 198mm !important;
                height: 135mm !important;
                overflow: visible !important;
            }

            .cut-divider {
                top: 147mm !important;
            }

            .copy {
                position: absolute !important;
                left: 198mm !important;
                top: 0 !important;
                width: 135mm !important;
                height: 198mm !important;
                transform-origin: 0 0 !important;
                transform: rotate(90deg) !important;
                overflow: hidden !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            body.rotate-counter .copy {
                left: 0 !important;
                top: 135mm !important;
                transform-origin: 0 0 !important;
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
                padding: 3.5px 4px !important;
                font-size: 8px !important;
            }

            .pad-table tbody td {
                padding: 3px 4px !important;
                font-size: 8.5px !important;
                line-height: 1.25 !important;
            }

            .pad-table tbody tr:nth-child(even) {
                background-color: rgba(224, 242, 254, 0.4) !important;
            }

            .pad-table tfoot td {
                padding: 2.5px 4px !important;
                font-size: 8.5px !important;
            }

            .pad-footer {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* Half Sheet (Single Copy Cut Paper) */
        body.single-copy-mode .a4-sheet {
            height: 147mm !important;
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
                $itemsPerHalf = 20;
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
            <button type="button" onclick="downloadWhatsappPdf()" class="btn btn-success shadow-sm" id="whatsappDownloadBtn">
                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp PDF
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
                <div class="half-wrapper">
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
                <div class="half-wrapper" id="duplicateWrap" style="display: none;">
                    <div class="copy" id="duplicateCopy">
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
                    <div class="half-wrapper">
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
                </div>

                @if ($secondHalf)
                    <!-- CUT / CONTINUATION DIVIDER -->
                    <div class="cut-divider">
                        <span><i class="fa-solid fa-arrow-down me-1"></i> CONTINUED ON NEXT HALF</span>
                    </div>

                    <!-- BOTTOM HALF: Remaining Products on Next Half -->
                    <div class="half half-bottom">
                        <div class="half-wrapper">
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

    <!-- WhatsApp Upright Portrait Container (Downloaded via html2pdf) -->
    <div id="whatsappPdfContainer" style="display: none; width: 190mm; background: #ffffff;">
        @php
            $whatsappChunks = $dcItemsColl->chunk($itemsPerHalf);
            $totalWhatsappPages = $whatsappChunks->count();
        @endphp

        @foreach ($whatsappChunks as $wIndex => $wItems)
            @php
                $wPartNum = $wIndex + 1;
                $wIsFirst = ($wIndex === 0);
                $wIsLast = ($wPartNum === $totalWhatsappPages);
                $wStartIndex = ($wIndex * $itemsPerHalf) + 1;
                $wCopyTag = ($totalWhatsappPages > 1) ? "PAGE {$wPartNum}/{$totalWhatsappPages}" : '';
            @endphp
            <div class="whatsapp-page" style="width: 190mm; min-height: 270mm; margin: 0 auto; background: #ffffff; padding: 10mm 12mm; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; color: var(--pad-text); position: relative; display: flex; flex-direction: column; justify-content: space-between; {{ !$wIsLast ? 'page-break-after: always;' : '' }}">
                <div class="whatsapp-top" style="flex-shrink: 0;">
                    <!-- Header (Upright Portrait) -->
                    <div class="pad-header" style="display: flex; justify-content: space-between; margin-bottom: 6px; border-bottom: 2px solid var(--pad-border); padding-bottom: 6px;">
                        <div class="logo-section" style="width: 44%; display: flex; flex-direction: column; align-items: flex-start;">
                            @if(!empty($companyLogo) && file_exists(public_path($companyLogo)))
                                <img src="{{ asset($companyLogo) }}" alt="Company Logo" class="logo-img" style="max-height: 48px; width: auto; object-fit: contain; margin-bottom: 4px;">
                            @elseif(\App\Models\Setting::get('company_logo') && file_exists(public_path(\App\Models\Setting::get('company_logo'))))
                                <img src="{{ asset(\App\Models\Setting::get('company_logo')) }}" alt="Company Logo" class="logo-img" style="max-height: 48px; width: auto; object-fit: contain; margin-bottom: 4px;">
                            @else
                                <h2 style="margin:0; font-weight:900; font-size:22px; color: #1e40af;">{{ \App\Models\Setting::get('company_name', 'VAHLA') }}</h2>
                                <div style="font-size:9px; font-weight:bold; letter-spacing:1px; margin-bottom:4px; color: #1e40af;">INDUSTRIAL SOLUTIONS</div>
                            @endif
                            <div class="invoice-badge-wrap" style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                <span class="invoice-badge pill-title-text" style="background-color: #3b82f6 !important; color: #ffffff !important; font-weight: bold; font-size: 11px; padding: 3px 12px; letter-spacing: 1.5px; border-radius: 2px; text-transform: uppercase;">DELIVERY CHALLAN</span>
                                @if(!empty($wCopyTag))
                                    <span class="copy-type-tag" style="font-size: 8.5px; font-weight: 700; color: #3b82f6; border: 1px solid #bfdbfe; background: #eff6ff; border-radius: 3px; padding: 1.5px 6px;">{{ $wCopyTag }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="company-details-section" style="width: 55%; font-size: 8.5px; line-height: 1.35; color: var(--pad-text); text-align: right;">
                            <div class="deals-in" style="font-weight: bold; margin-bottom: 3px; font-size: 9px;">
                                <span style="color: #0284c7;">DEALS IN:</span> {{ \App\Models\Setting::get('deals_in') ?: 'Fasteners, Bearing, Lifting Equipments, V-belt, PPE\'s, Hand & Cutting Etc' }}
                            </div>
                            <div class="contact-line" style="margin-bottom: 2px;">
                                <span dir="ltr">{{ \App\Models\Setting::get('company_address') ?: 'SHOP NO.91/1, OPP; FG GIRLS SCHOOL, SADDAR BAZAR HYDERABAD SINDH' }}</span>
                                <i class="fa-solid fa-location-dot" style="color: #0ea5e9; margin-left: 5px;"></i>
                            </div>
                            <div class="contact-line" style="margin-bottom: 2px;">
                                <span dir="ltr">{{ \App\Models\Setting::get('company_phone') ?: '+92-022-2781224 / 03003077299' }}</span>
                                <i class="fa-solid fa-phone" style="color: #0ea5e9; margin-left: 5px;"></i>
                            </div>
                            @if(\App\Models\Setting::get('company_email') || \App\Models\Setting::get('website_link'))
                            <div class="contact-line">
                                <span dir="ltr">{{ \App\Models\Setting::get('company_email') ?: 'vahlamillstore@hotmail.com' }}</span>
                                <i class="fa-solid fa-envelope" style="color: #0ea5e9; margin-left: 5px;"></i>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Section -->
                    <div class="meta-section" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: bold; font-size: 9px; color: var(--pad-text);">
                        <div class="meta-left" style="width: 58%;">
                            <div class="meta-row" style="margin-bottom: 4px; display: flex; align-items: flex-end;">
                                <div class="meta-label" style="width: 48px; flex-shrink: 0;">S. No.</div>
                                <div class="meta-value" style="border-bottom: 1.5px solid var(--pad-border); flex-grow: 1; padding-left: 6px; font-weight: bold; min-height: 15px;">{{ $sale ? $sale->invoice_no : ($dcNumbers ?? 'DC') }}</div>
                            </div>
                            <div class="meta-row" style="margin-bottom: 4px; display: flex; align-items: flex-end;">
                                <div class="meta-label" style="width: 48px; flex-shrink: 0;">LPO</div>
                                <div class="meta-value" style="border-bottom: 1.5px solid var(--pad-border); flex-grow: 1; padding-left: 6px; font-weight: bold; min-height: 15px;">{{ $lpoReference }}</div>
                            </div>
                            @if(!empty(optional($sale)->transport_name) || !empty(optional($sale)->vehicle_number))
                            <div class="meta-row" style="margin-bottom: 4px; display: flex; align-items: flex-end;">
                                <div class="meta-label" style="width: 48px; flex-shrink: 0;">T/P</div>
                                <div class="meta-value" style="border-bottom: 1.5px solid var(--pad-border); flex-grow: 1; padding-left: 6px; font-weight: bold; min-height: 15px;">{{ optional($sale)->transport_name ?? (optional($sale)->vehicle_number ?? '') }}</div>
                            </div>
                            @endif
                            <div class="meta-row" style="margin-bottom: 4px; display: flex; align-items: flex-end;">
                                <div class="meta-label" style="width: 48px; flex-shrink: 0;">M/s</div>
                                <div class="meta-value" style="border-bottom: 1.5px solid var(--pad-border); flex-grow: 1; padding-left: 6px; font-weight: bold; min-height: 15px;">{{ $customerName }}</div>
                            </div>
                        </div>
                        <div class="meta-right" style="width: 38%;">
                            <div class="meta-row" style="margin-bottom: 4px; display: flex; align-items: flex-end;">
                                <div class="meta-label" style="width: 50px; flex-shrink: 0;">Date:</div>
                                <div class="meta-value" style="border-bottom: 1.5px solid var(--pad-border); flex-grow: 1; padding-left: 6px; font-weight: bold; min-height: 15px;">{{ \Carbon\Carbon::parse(optional($sale)->sale_date ?? (optional($sale)->created_at ?? now()))->format('d-m-Y') }}</div>
                            </div>
                            @if(!empty($dcNumbers) && $dcNumbers !== ($sale ? $sale->invoice_no : ''))
                            <div class="meta-row" style="margin-bottom: 4px; display: flex; align-items: flex-end;">
                                <div class="meta-label" style="width: 50px; flex-shrink: 0;">D. No:</div>
                                <div class="meta-value" style="border-bottom: 1.5px solid var(--pad-border); flex-grow: 1; padding-left: 6px; font-weight: bold; min-height: 15px;">{{ $dcNumbers }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Table -->
                    <table class="pad-table" style="width: 100%; border-collapse: collapse; margin-bottom: 6px; border: 1.5px solid var(--pad-border); font-size: 8.5px;">
                        <thead>
                            <tr style="background-color: #374151; color: #ffffff;">
                                <th style="width: 6%; padding: 4px 5px; border: 1px solid var(--pad-border); text-align: center;">S.NO</th>
                                <th class="desc-col" style="width: 44%; padding: 4px 5px; border: 1px solid var(--pad-border); text-align: left;">DESCRIPTION</th>
                                <th style="width: 10%; padding: 4px 5px; border: 1px solid var(--pad-border); text-align: center;">QTY</th>
                                <th class="rate-col" style="width: 13%; padding: 4px 5px; border: 1px solid var(--pad-border); text-align: right;">RATE</th>
                                <th style="width: 11%; padding: 4px 5px; border: 1px solid var(--pad-border); text-align: center;">UNIT</th>
                                <th class="rate-col" style="width: 16%; padding: 4px 5px; border: 1px solid var(--pad-border); text-align: right;">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wItems as $wItem)
                                @php
                                    $wSNo = $wStartIndex + $loop->index;
                                    $wProdName = 'Item';
                                    if (is_array($wItem)) {
                                        $wProdName = $wItem['product_name'] ?? ($wItem['item_name'] ?? 'Item');
                                        $wQty = $wItem['quantity'] ?? 1;
                                        $wRate = $wItem['sale_price'] ?? ($wItem['price'] ?? 0);
                                        $wUnit = $wItem['unit'] ?? 'Pcs';
                                        $wLineTotal = $wQty * $wRate;
                                    } else {
                                        $wProdName = $wItem->product ? $wItem->product->item_name : ($wItem->description ?? 'Item');
                                        $wQty = $wItem->delivered_qty ?? ($wItem->quantity ?? 1);
                                        $wRate = $wItem->sale_price ?? ($wItem->price ?? 0);
                                        $wUnit = $wItem->unit ?? 'Kg';
                                        $wLineTotal = $wQty * $wRate;
                                    }
                                    $wRowBg = ($loop->index % 2 === 1) ? 'background-color: rgba(224, 242, 254, 0.4);' : '';
                                @endphp
                                <tr style="{{ $wRowBg }}">
                                    <td style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: center;">{{ $wSNo }}</td>
                                    <td class="desc-col" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; font-weight: 500;">{{ $wProdName }}</td>
                                    <td style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: center; font-weight: bold;">{{ ($wQty == (int)$wQty) ? (int)$wQty : number_format($wQty, 2) }}</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right;">{{ number_format($wRate, 2) }}</td>
                                    <td style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: center;">{{ $wUnit }}</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold;">{{ number_format($wLineTotal, 2) }}</td>
                                </tr>
                            @endforeach

                            @if ($wIsLast)
                                @if(optional($sale)->total_extradiscount > 0)
                                <tr class="rate-col">
                                    <td colspan="5" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold; color: #dc2626;">DISCOUNT:</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold; color: #dc2626;">-{{ number_format($sale->total_extradiscount, 2) }}</td>
                                </tr>
                                @endif
                                @if(optional($sale)->shipping_charges > 0)
                                <tr class="rate-col">
                                    <td colspan="5" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold; color: #0284c7;">SHIPPING CHARGES:</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold; color: #0284c7;">+{{ number_format($sale->shipping_charges, 2) }}</td>
                                </tr>
                                @endif
                                @if(optional($sale)->cutting_charges > 0)
                                <tr class="rate-col">
                                    <td colspan="5" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold; color: #d97706;">CUTTING CHARGES:</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 2.5px 5px; text-align: right; font-weight: bold; color: #d97706;">+{{ number_format($sale->cutting_charges, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="rate-col">
                                    <td colspan="5" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: right; font-weight: bold;">TOTAL:</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: right; font-weight: bold; background-color: rgba(224, 242, 254, 0.5);">{{ number_format($finalTotal, 2) }}</td>
                                </tr>
                                <tr class="rate-col">
                                    <td colspan="5" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: right; font-weight: bold;">PAID:</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: right; font-weight: bold; color: #16a34a;">{{ number_format($paidAmount, 2) }}</td>
                                </tr>
                                <tr class="rate-col">
                                    <td colspan="5" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: right; font-weight: bold;">DUE:</td>
                                    <td class="rate-col" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: right; font-weight: bold; color: #dc2626;">{{ number_format($dueAmount, 2) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="6" style="border: 1px solid var(--pad-border); padding: 3px 5px; text-align: center; font-weight: bold; font-size: 8px; color: #1e40af; background: #eff6ff;">
                                        --- CONTINUED ON NEXT PAGE ---
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="pad-footer" style="flex-shrink: 0; display: flex; justify-content: space-between; align-items: flex-end; margin-top: auto; padding-top: 6px;">
                    <div class="footer-note" style="width: 55%; font-size: 8.5px; font-weight: bold; line-height: 1.35; color: var(--pad-text);">
                        Goods once sold can not be exchange<br>
                        or taken back without receipt
                    </div>
                    <div class="footer-sign" style="width: 42%; text-align: right; position: relative;">
                        <img src="{{ asset('assets/images/stamp.png') }}" class="footer-stamp" alt="Stamp" style="width: 65px; height: auto; margin-bottom: -4px; margin-right: 12px; display: inline-block; transform: rotate(-10deg); mix-blend-mode: multiply;"><br>
                        <span class="for-company" style="font-size: 9px; font-weight: bold; color: var(--pad-text);">For {{ \App\Models\Setting::get('company_name', 'VAHLA MILL STORE') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function toggleDuplicate() {
            const dupWrap = document.getElementById('duplicateWrap');
            const notice = document.getElementById('blankNotice');
            const btn = document.getElementById('duplicateBtnText');
            const btnEl = document.getElementById('toggleDuplicateBtn');
            if (!dupWrap) return;

            if (dupWrap.style.display === 'none') {
                dupWrap.style.display = 'block';
                if (notice) notice.style.display = 'none';
                btn.innerText = 'Showing 2 Copies';
                btnEl.classList.remove('btn-outline-primary');
                btnEl.classList.add('btn-primary');
            } else {
                dupWrap.style.display = 'none';
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

        function downloadWhatsappPdf() {
            const btn = document.getElementById('whatsappDownloadBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Generating PDF...';
            btn.disabled = true;

            const element = document.getElementById('whatsappPdfContainer');
            if (document.body.classList.contains('hide-rate')) {
                element.classList.add('hide-rate');
            } else {
                element.classList.remove('hide-rate');
            }

            const currentTitle = document.getElementById('titleBtnText') ? document.getElementById('titleBtnText').innerText.replace('Title: ', '').trim() : 'DELIVERY CHALLAN';
            element.querySelectorAll('.pill-title-text').forEach(p => p.innerText = currentTitle);

            element.style.display = 'block';

            const filename = '{{ !empty($dc->dc_number) ? $dc->dc_number : "Delivery_Challan" }}_WhatsApp.pdf';

            const opt = {
                margin:       [0, 0, 0, 0],
                filename:     filename,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                element.style.display = 'none';
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }).catch((err) => {
                console.error(err);
                element.style.display = 'none';
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                alert('PDF download error: ' + err);
            });
        }
    </script>
</body>
</html>
