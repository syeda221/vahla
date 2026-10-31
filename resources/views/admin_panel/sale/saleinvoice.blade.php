<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
    $rawCustomerName = $sale->customer_relation?->customer_name ?? ($sale->walkin_name ?? 'Walking Customer');
    $cleanCustomerName = trim(preg_replace('/[\/\\:*?"<>|]/', ' ', $rawCustomerName));
    $cleanCustomerName = preg_replace('/\s+/', ' ', $cleanCustomerName);
    if (empty($cleanCustomerName)) {
        $cleanCustomerName = 'Customer';
    }
    $pageTitle = $cleanCustomerName . ' - ' . $sale->invoice_no;
@endphp
    <title>{{ $pageTitle }}</title>

    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-color: #000;
            --accent-color: #000;
            --border-color: #000;
            --text-color: #000;
        }

        body {
            background-color: #f8f9fa;
            color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Pure black override for all text elements */
        .text-primary, .text-danger, .text-success, .text-warning, .text-info, .text-secondary, .text-muted, .text-dark {
            color: #000 !important;
        }

        /* Screen Layout */
        .invoice-container {
            max-width: 210mm;
            margin: 10px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-height: 297mm;
            position: relative;
            color: #000;
        }

        .company-info {
            text-align: center;
            margin-bottom: 12px;
            color: #000;
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: #000;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            color: #000;
            margin: 12px 0 16px 0;
            letter-spacing: 1.5px;
        }

        .info-box {
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            height: 100%;
            border-radius: 6px;
            background-color: #fff;
            color: #000;
        }

        .info-box-header {
            font-weight: 700;
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 8px;
            padding-bottom: 4px;
            color: #000;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-label {
            font-weight: 600;
            color: #000;
            min-width: 65px;
            display: inline-block;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .invoice-table th {
            background-color: #fff;
            color: #000;
            font-weight: 700;
            font-size: 10px;
            padding: 5px 3px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .invoice-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            vertical-align: middle;
            font-size: 10px;
            color: #000;
        }

        .invoice-table tfoot td {
            border: 1px solid #000;
            padding: 4px 3px;
            font-weight: bold;
            font-size: 10px;
            color: #000;
        }

        .invoice-table tbody tr:nth-of-type(even) {
            background-color: #fff;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer-section {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }

        .terms-box {
            font-size: 11px;
            color: #000;
        }

        .terms-box ul {
            padding-left: 20px;
            margin-bottom: 0;
            color: #000;
        }

        .terms-box li {
            margin-bottom: 2px;
            color: #000;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            color: #000;
        }

        .totals-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #eee;
            color: #000;
        }

        .totals-table .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }

        .signature-area {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 180px;
            text-align: center;
            padding-top: 5px;
            color: #000;
        }

        .print-btn-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        /* Thermal Receipt */
        .receipt-container {
            display: none;
        }

        @media print {

            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
            }

            .print-btn-container,
            .no-print,
            .screen-only {
                display: none !important;
            }

            .receipt-container {
                display: block !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 auto !important;
                padding: 3mm 2mm 5mm 2mm !important;
                width: 76mm !important;
                max-width: 76mm !important;
                background: #fff !important;
                color: #000 !important;
                font-size: 11px !important;
                line-height: 1.3 !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }

            .receipt-container h1,
            .receipt-container h2,
            .receipt-container h3,
            .receipt-container p {
                margin: 0;
                padding: 0;
            }

            .receipt-container .company-name {
                font-size: 18px !important;
                font-weight: 700 !important;
                text-align: center !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                margin-bottom: 2px !important;
                color: #000 !important;
            }

            .receipt-container .company-info {
                font-size: 11px !important;
                text-align: center !important;
                color: #000 !important;
                font-weight: 400 !important;
                line-height: 1.35 !important;
                margin-bottom: 4px !important;
            }

            .receipt-container .policy-banner {
                background-color: #000 !important;
                color: #fff !important;
                text-align: center !important;
                font-size: 10px !important;
                font-weight: bold !important;
                padding: 3px 5px !important;
                margin: 4px 0 !important;
                border-radius: 3px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .receipt-container .divider {
                border-top: 1px dashed #000 !important;
                margin: 4px 0 !important;
            }

            .receipt-container .meta-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 2px !important;
                margin-bottom: 2px !important;
                color: #000 !important;
            }

            .receipt-container .meta-row {
                display: flex !important;
                justify-content: space-between !important;
                font-size: 10.5px !important;
                font-weight: 400 !important;
            }

            .receipt-container .items-table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 10.5px !important;
                color: #000 !important;
                margin: 3px 0 !important;
            }

            .receipt-container .items-table th {
                border-top: 1px dashed #000 !important;
                border-bottom: 1px dashed #000 !important;
                padding: 3px 1px !important;
                text-align: left !important;
                font-weight: 700 !important;
                font-size: 10px !important;
                text-transform: uppercase !important;
            }

            .receipt-container .items-table td {
                padding: 3px 1px !important;
                vertical-align: top !important;
                border-bottom: 1px dashed #ccc !important;
                font-size: 10.5px !important;
            }

            .receipt-container .item-name {
                font-weight: 600 !important;
                font-size: 10.5px !important;
                color: #000 !important;
                display: block !important;
            }

            .receipt-container .item-variant {
                font-size: 9.5px !important;
                color: #222 !important;
                font-weight: 400 !important;
                margin-top: 1px !important;
                display: block !important;
            }

            .receipt-container .totals-section {
                font-size: 10.5px !important;
                margin-top: 4px !important;
                color: #000 !important;
            }

            .receipt-container .tot-row {
                display: flex !important;
                justify-content: space-between !important;
                padding: 1.5px 0 !important;
            }

            .receipt-container .tot-row.grand-total {
                font-size: 12px !important;
                font-weight: 800 !important;
                margin: 3px 0 !important;
                padding: 4px 0 !important;
                border-top: 1.5px solid #000 !important;
                border-bottom: 1.5px solid #000 !important;
                color: #000 !important;
            }

            .receipt-container .balance-section {
                font-size: 10.5px !important;
                margin-top: 3px !important;
                border-top: 1px dashed #000 !important;
                padding-top: 3px !important;
            }

            .receipt-container .balance-section .tot-row.closing-bal {
                font-weight: 700 !important;
                background-color: #f8f9fa !important;
                padding: 3px 2px !important;
                border: 1px solid #000 !important;
                margin-top: 2px !important;
            }

            .receipt-container .footer {
                text-align: center !important;
                margin-top: 10px !important;
                color: #000 !important;
            }

            .receipt-container .footer p {
                font-size: 10px !important;
                font-weight: 600 !important;
                margin-bottom: 2px !important;
            }

            .receipt-container .footer .soft-credit {
                font-size: 8.5px !important;
                color: #444 !important;
                margin-top: 2px !important;
                font-weight: 400 !important;
            }
        }
    </style>
</head>

<body>
@php
    $showTaxColumns = \App\Models\SaleFieldConfiguration::isVisibleInPrint('print_tax_breakdown', true) && 
        (\App\Models\SaleFieldConfiguration::isEnabled('sales_tax', true) || (float)($sale->total_sales_tax ?? 0) > 0);
@endphp


    <!-- Print / Action Buttons -->
    <div class="print-btn-container no-print">
        <button onclick="printA4()" class="btn btn-primary btn-sm shadow fw-bold">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text me-1" viewBox="0 0 16 16">
                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2.5a1 1 0 0 0 1 1H13v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
            </svg>
            Print A4
        </button>
        <a href="{{ route('sales.receipt', $sale->id) }}" class="btn btn-warning btn-sm shadow ms-2 fw-bold text-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-receipt me-1" viewBox="0 0 16 16">
                <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 2.025l-.646.647a.5.5 0 0 1-.708 0L3 2.025l-.508.508a.5.5 0 0 1-.033-.067z"/>
                <path d="M3 4.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
            </svg>
            Thermal Print
        </a>
        <a href="javascript:void(0)" onclick="handleGoBack()" class="btn btn-secondary btn-sm shadow ms-2 fw-bold">Back</a>
    </div>


    <!-- ========================================== -->
    <!-- SCREEN VIEW -->
    <!-- ========================================== -->

    <div class="invoice-container screen-only">

        <!-- Company Header -->

        @if(!($isEstimate ?? false))

            <div class="company-info">

                <div class="company-name">
                    {{ \App\Models\Setting::get('company_name', 'prowave technogies') }}
                </div>

                <div style="font-size: 12px;">
                    {{ \App\Models\Setting::get('company_address', 'Hyderabad') }}
                </div>

                <p>
                    {{ \App\Models\Setting::get('company_phone', '0327-9226901') }}
                </p>

            </div>

        @endif


        <div class="invoice-title">
            {{ ($isEstimate ?? false) ? 'Estimate' : 'Sales Invoice' }}
        </div>


        <!-- Info Grid -->

        @if(!($isEstimate ?? false))

            <div class="row g-2 mb-2">

                <!-- Customer -->

                <div class="col-6">

                    <div class="info-box">

                        <div class="info-box-header">
                            Customer
                        </div>

                        @if($sale->customer_relation?->customer_id)

                            <div style="font-size: 11px; color: #555;">
                                Code:
                                <strong>
                                    {{ $sale->customer_relation->customer_id }}
                                </strong>
                            </div>

                        @endif

                        <div>
                            <span class="info-label">
                                Name:
                            </span>

                            <strong>
                                {{ $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walking Customer') }}
                            </strong>
                        </div>

                        <div>
                            <span class="info-label">
                                Address:
                            </span>

                            <span style="font-size:11px;">
                                {{ $sale->customer_relation->address ?? '—' }}
                            </span>
                        </div>

                        <div>
                            <span class="info-label">
                                Mob:
                            </span>

                            <span style="font-size:11px;">
                                {{ $sale->customer_relation->mobile ?? '—' }}
                            </span>
                        </div>

                        @php
                            $relCust = $sale->customer_relation;
                            $custNtn = $relCust ? ($relCust->getCustomValue('ntn_number') ?? $relCust->ntn_number ?? null) : null;
                            $custStrn = $relCust ? ($relCust->getCustomValue('strn_number') ?? $relCust->strn_number ?? null) : null;
                            $custCnic = $relCust ? ($relCust->getCustomValue('cnic') ?? $relCust->cnic ?? null) : null;
                        @endphp

                        @if(!empty($custNtn))
                            <div>
                                <span class="info-label">
                                    NTN:
                                </span>
                                <strong style="font-size:11px;">
                                    {{ $custNtn }}
                                </strong>
                            </div>
                        @endif

                        @if(!empty($custStrn))
                            <div>
                                <span class="info-label">
                                    STRN:
                                </span>
                                <strong style="font-size:11px;">
                                    {{ $custStrn }}
                                </strong>
                            </div>
                        @elseif(!empty($custCnic))
                            <div>
                                <span class="info-label">
                                    CNIC:
                                </span>
                                <span style="font-size:11px;">
                                    {{ $custCnic }}
                                </span>
                            </div>
                        @endif

                    </div>

                </div>


                <!-- Reference -->

                <div class="col-6">

                    <div class="info-box">

                        <div class="info-box-header">
                            Reference
                        </div>

                        <div>
                            <span class="info-label">
                                Inv #:
                            </span>

                            <strong>
                                {{ $sale->invoice_no }}
                            </strong>
                        </div>

                        <div>
                            <span class="info-label">
                                Date:
                            </span>

                            {{ $sale->created_at
                                ? $sale->created_at->format('d/m/Y')
                                : date('d/m/Y') }}
                        </div>

                        @if($sale->reference)

                            <div style="margin-top:4px; padding-top:4px; border-top:1px dashed #ddd;">

                                <span class="info-label">
                                    Remarks:
                                </span>

                                <span style="font-size:11px; color:#333;">
                                    {{ $sale->reference }}
                                </span>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @else

            <div class="row g-2 mb-2">

                <div class="col-12 text-end">

                    <div class="info-box">

                        <div>

                            <span class="info-label">
                                Date:
                            </span>

                            {{ $sale->created_at
                                ? $sale->created_at->format('d/m/Y')
                                : date('d/m/Y') }}

                        </div>

                    </div>

                </div>

            </div>

        @endif


        <!-- Return Note -->

        @if ($sale->return_note)

            <div class="row mb-2">

                <div class="col-12">

                    <div class="info-box"
                        style="min-height: auto; padding: 4px 8px; background-color: #f1f5f9; font-style: italic;">

                        <strong>
                            Note:
                        </strong>

                        {{ $sale->return_note }}

                    </div>

                </div>

            </div>

        @endif


        <!-- ========================================== -->
        <!-- MAIN INVOICE TABLE -->
        <!-- ========================================== -->

        <table class="invoice-table">

            <thead>

                <tr>

                    <th class="text-center" style="width: 4%">
                        S.No
                    </th>

                    <th class="text-start" style="width: 32%">
                        Description of Product
                    </th>

                    <th class="text-center" style="width: 12%">
                        Batch / Sr. No
                    </th>

                    <th class="text-center" style="width: 6%">
                        Quantity
                    </th>

                    <th class="text-end" style="width: 7%">
                        Rate
                    </th>

                    <th class="text-end" style="width: 8%">
                        Gross Amount
                    </th>

                    <th class="text-end" style="width: 5%">
                        Disc %
                    </th>

                    @if($showTaxColumns)
                    <th class="text-end" style="width: 8%">
                        Exclusive GST Amount
                    </th>

                    <th class="text-end" style="width: 6%">
                        Sales Tax
                    </th>

                    <th class="text-end" style="width: 6%">
                        Further Tax {{ ((float)($sale->total_further_tax ?? 0) > 0) ? '@3%' : '(0%)' }}
                    </th>
                    @endif

                    <th class="text-end" style="width: 6%">
                        Net Amount
                    </th>

                </tr>

            </thead>


            @php
                $sumTotQty = 0;
                $sumGrossAmt = 0;
                $sumExclGst = 0;
                $sumSalesTax = 0;
                $sumFurtherTax = 0;
                $sumNetTotal = 0;
            @endphp


            <tbody>

                @foreach ($saleItems as $item)

                    @php
                        $vName = $item['variant_name'] ?? '';
                        $vSize = (!empty($item['size_val']) && $item['size_val'] !== '-') ? $item['size_val'] : '';
                        $vColor = (!empty($item['color_val']) && $item['color_val'] !== '-') ? $item['color_val'] : '';
                        
                        $vExtra = [];
                        if ($vSize) $vExtra[] = $vSize;
                        if ($vColor) $vExtra[] = $vColor;
                        $vExtraStr = count($vExtra) > 0 ? ' (' . implode(', ', $vExtra) . ')' : '';

                        $productTitle = $item['item_name'];
                        if ($vName && strtolower(trim($vName)) !== strtolower(trim($productTitle))) {
                            $productTitle .= ' — ' . $vName;
                        }
                        $productTitle .= $vExtraStr;

                        $batchNo = $item['batch_no'] ?? '';
                        $packing = (float)($item['pieces_per_box'] ?? 1);
                        if ($packing <= 0) $packing = 1;

                        $itemQty = (float)($item['qty_box'] ?? $item['qty'] ?? $item['total_pieces'] ?? 0);
                        $itemPrice = (float)($item['price'] ?? 0);
                        
                        $itemGross = (float)($item['gross_amount'] ?? 0);
                        if ($itemGross <= 0) {
                            $itemGross = $itemQty * $itemPrice;
                        }

                        $discPct = (float)($item['discount_percent'] ?? 0);
                        $exclGst = (float)($item['exclusive_gst_amount'] ?? 0);
                        $salesTax = (float)($item['sales_tax_amount'] ?? 0);
                        $furtherTax = (float)($item['further_tax_amount'] ?? 0);
                        $netAmt = (float)($item['total'] ?? 0);

                        $sumTotQty += $itemQty;
                        $sumGrossAmt += $itemGross;
                        $sumExclGst += $exclGst;
                        $sumSalesTax += $salesTax;
                        $sumFurtherTax += $furtherTax;
                        $sumNetTotal += $netAmt;
                    @endphp


                    <tr>

                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td class="text-start">
                            <div style="font-weight: 700; color: #000; font-size: 11px;">{{ $productTitle }}</div>

                            @php
                                $subDescriptions = [];
                                $rawModel = $item['model'] ?? ($item['product']['model'] ?? null);
                                if (!empty($rawModel)) {
                                    $decodedModel = json_decode($rawModel, true);
                                    if (is_array($decodedModel)) {
                                        $subDescriptions = $decodedModel;
                                    } else {
                                        $subDescriptions = array_filter(array_map('trim', explode("\n", $rawModel)));
                                    }
                                }

                                $itemSerialsRaw = $item['serials'] ?? null;
                                $itemSerialsList = [];
                                if (!empty($itemSerialsRaw)) {
                                    if (is_array($itemSerialsRaw)) {
                                        $itemSerialsList = $itemSerialsRaw;
                                    } elseif (is_string($itemSerialsRaw)) {
                                        $decodedSerials = json_decode($itemSerialsRaw, true);
                                        if (is_array($decodedSerials)) {
                                            $itemSerialsList = $decodedSerials;
                                        } else {
                                            $itemSerialsList = array_filter(array_map('trim', explode(',', $itemSerialsRaw)));
                                        }
                                    }
                                }
                            @endphp

                            @if(!empty($itemSerialsList) && count($itemSerialsList) > 0)
                                <div style="font-size: 11px; color: #000; margin-top: 2px; font-weight: 800;">
                                    SN #: {{ implode(', ', $itemSerialsList) }}
                                </div>
                            @endif

                            @if(!empty($subDescriptions) && count($subDescriptions) > 0)
                                <div style="font-size: 10px; color: #1e293b; margin-top: 2px; padding-left: 2px;">
                                    @foreach($subDescriptions as $sd)
                                        @if(trim($sd))
                                            <div style="line-height: 1.4;"><span style="color: #000; margin-right: 4px; font-weight: bold;">•</span>{{ trim($sd) }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        <td class="text-center">
                            @if(!empty($batchNo))
                                {{ $batchNo }}
                            @elseif(!empty($itemSerialsList))
                                S/N
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-center">
                            {{ $itemQty == (int)$itemQty ? (int)$itemQty : number_format($itemQty, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($itemPrice, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($itemGross, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($discPct, 2) }}
                        </td>

                        @if($showTaxColumns)
                        <td class="text-end">
                            {{ number_format($exclGst, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($salesTax, 2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($furtherTax, 2) }}
                        </td>
                        @endif

                        <td class="text-end fw-bold">
                            {{ number_format($netAmt, 2) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>


            <tfoot>

                <tr class="fw-bold" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">

                    <td colspan="3" class="text-end fw-bold">
                        TOTAL:
                    </td>

                    <td class="text-center fw-bold">
                        {{ $sumTotQty == (int)$sumTotQty ? (int)$sumTotQty : number_format($sumTotQty, 2) }}
                    </td>

                    <td class="text-end fw-bold"></td>

                    <td class="text-end fw-bold">
                        {{ number_format($sumGrossAmt, 2) }}
                    </td>

                    <td class="text-end fw-bold">
                        0.00
                    </td>

                    @if($showTaxColumns)
                    <td class="text-end fw-bold">
                        {{ number_format($sumExclGst, 2) }}
                    </td>

                    <td class="text-end fw-bold">
                        {{ number_format($sumSalesTax, 2) }}
                    </td>

                    <td class="text-end fw-bold">
                        {{ number_format($sumFurtherTax, 2) }}
                    </td>
                    @endif

                    <td class="text-end fw-bold">
                        {{ number_format($sumNetTotal, 2) }}
                    </td>

                </tr>

            </tfoot>

        </table>

        @php
            $totalCartonsCount = 0;
            $totalLooseCount = 0;
            $totalPiecesCount = 0;

            foreach ($saleItems as $it) {
                $ppb = (float)($it['pieces_per_box'] ?? 1);
                if ($ppb <= 0) $ppb = 1;
                $tp = (float)($it['total_pieces'] ?? 0);
                $totalPiecesCount += $tp;

                $vU = strtolower($it['variant_unit'] ?? '');
                $sM = $it['size_mode'] ?? 'std';

                if (in_array($sM, ['by_cartons', 'by_bandal']) || $vU === 'carton' || $vU === 'ctn') {
                    $b = floor($tp / $ppb);
                    $l = $tp % $ppb;
                    $totalCartonsCount += $b;
                    $totalLooseCount += $l;
                }
            }
        @endphp


        <!-- ========================================== -->
        <!-- EXCHANGE RETURN -->
        <!-- ========================================== -->

        @php

            $exchangeReturn =
                \App\Models\SaleReturn::with('items.product')
                    ->where(
                        'remarks',
                        'LIKE',
                        '%Invoice #'.$sale->invoice_no.'%'
                    )
                    ->first();

            $exchangeReturnedAmount = 0;

            if ($exchangeReturn) {

                $exchangeReturnedAmount =
                    $exchangeReturn->items->sum('line_total');

            }

        @endphp


        @if($exchangeReturn && $exchangeReturn->items->count() > 0)

            <div class="mt-3">

                <h6 class="fw-bold mb-2">
                    Returned Items (Exchange)
                </h6>


                <table class="invoice-table">

                    <thead class="bg-light">

                        <tr>

                            <th class="text-start"
                                style="width: 5%">
                                S.
                            </th>

                            <th class="text-start"
                                style="width: 38%">
                                Description
                            </th>

                            <th class="text-center"
                                style="width: 14%">
                                Qty
                            </th>

                            <th class="text-center"
                                style="width: 10%">
                                UOM
                            </th>

                            <th class="text-end"
                                style="width: 10%">
                                Price
                            </th>

                            <th class="text-end"
                                style="width: 10%">
                                Disc
                            </th>

                            <th class="text-end"
                                style="width: 13%">
                                Net Amount
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($exchangeReturn->items as $retItem)

                            <tr>

                                <td class="text-start">
                                    {{ $loop->iteration }}
                                </td>


                                <td class="text-start">

                                    <div style="font-weight: bold; font-size: 12px; margin-bottom: 2px;">

                                        {{ $retItem->product->item_name ?? 'Unknown' }}

                                    </div>

                                    @php

                                        $retColorStr = '';

                                        if (!empty($retItem->color)) {

                                            $decoded =
                                                base64_decode(
                                                    $retItem->color,
                                                    true
                                                );

                                            if (
                                                $decoded !== false
                                                &&
                                                is_string($decoded)
                                                &&
                                                str_starts_with(
                                                    trim($decoded),
                                                    '{'
                                                )
                                            ) {

                                                $parsed =
                                                    json_decode(
                                                        $decoded,
                                                        true
                                                    );

                                                if (
                                                    json_last_error()
                                                    ===
                                                    JSON_ERROR_NONE
                                                    &&
                                                    is_array($parsed)
                                                ) {

                                                    $parts = [];

                                                    if (
                                                        !empty($parsed['size'])
                                                        &&
                                                        $parsed['size'] !== '-'
                                                    ) {

                                                        $parts[] =
                                                            $parsed['size'];

                                                    }

                                                    if (
                                                        !empty($parsed['color'])
                                                        &&
                                                        $parsed['color'] !== '-'
                                                    ) {

                                                        $parts[] =
                                                            $parsed['color'];

                                                    }

                                                    $retColorStr =
                                                        implode(
                                                            ' | ',
                                                            $parts
                                                        );

                                                }

                                            }

                                        }

                                    @endphp


                                    @if($retColorStr)

                                        <div style="font-size: 11px; color: #555;">

                                            <span class="badge bg-light text-dark border p-1"
                                                style="font-size: 9px;">

                                                {{ $retColorStr }}

                                            </span>

                                        </div>

                                    @endif

                                </td>


                                <td class="text-center fw-bold">

                                    {{ (float)$retItem->qty }}
                                    Pcs

                                </td>


                                <td class="text-center fw-bold">
                                    Pieces
                                </td>


                                <td class="text-end">

                                    {{ number_format($retItem->price, 2) }}

                                </td>


                                <td class="text-end text-muted">
                                    —
                                </td>


                                <td class="text-end fw-bold">

                                    -
                                    {{ number_format($retItem->line_total, 2) }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif


        <!-- ========================================== -->
        <!-- FOOTER / TOTALS -->
        <!-- ========================================== -->

        <div class="row mt-2">

            <div class="col-7">

                <div class="terms-box pt-2">

                    <p class="fw-bold mb-1">
                        Terms & Conditions:
                    </p>

                    <ul style="font-size: 10px;">

                        @php

                            $invoiceTerms =
                                \App\Models\Setting::get(
                                    'invoice_terms',
                                    "10% will be deducted on return of purchase goods within 7 days.\nLoose & Water Soak products will not be RETURNED.\nPlease bring this invoice for any returns or exchanges."
                                );

                            $termLines =
                                explode("\n", $invoiceTerms);

                        @endphp


                        @foreach($termLines as $line)

                            @if(trim($line))

                                <li>
                                    {{ trim($line) }}
                                </li>

                            @endif

                        @endforeach

                    </ul>

                </div>


                <div class="mt-4 pt-2">

                    <div class="signature-area">
                        Authorized Signature
                    </div>

                    <div class="small text-muted mt-1"
                        style="font-size: 10px;">

                        Printed on:
                        {{ date('d/m/Y h:i A') }}

                    </div>

                </div>

            </div>


            <div class="col-5">

                <div class="info-box"
                    style="border: none; padding: 0;">

                    <table class="totals-table">

                        @php

                            $grossTotal =
                                collect($saleItems)->sum('total');

                            $totalDisc =
                                collect($saleItems)->sum('discount_amount');

                            $extraDisc =
                                (float)($sale->total_extradiscount ?? 0);

                            $netBill =
                                $sale->total_net;

                            $paidAmount =
                                (float)($sale->cash ?? 0);

                            $finalBal =
                                $previousBalance
                                +
                                $netBill
                                -
                                $paidAmount;

                        @endphp


                        @if ($totalCartonsCount > 0)

                            <tr>

                                <td class="text-muted fw-bold">
                                    Total Cartons / Bundals
                                </td>

                                <td class="text-end fw-bold" style="color: var(--primary-color);">

                                    @if ($totalLooseCount > 0)
                                        {{ $totalCartonsCount }} Ctn/Bndl + {{ $totalLooseCount }} Pcs
                                    @else
                                        {{ $totalCartonsCount }} Ctn/Bndl
                                    @endif

                                </td>

                            </tr>

                        @endif


                        @if ($totalDisc > 0 || $extraDisc > 0)

                            <tr>

                                <td class="text-muted">
                                    Gross Total
                                </td>

                                <td class="text-end text-muted">

                                    {{
                                        number_format(
                                            $grossTotal + $totalDisc,
                                            2
                                        )
                                    }}

                                </td>

                            </tr>


                            <tr>

                                <td class="text-muted">
                                    Total Discount
                                </td>

                                <td class="text-end text-danger">

                                    -
                                    {{ number_format($totalDisc + $extraDisc, 2) }}

                                </td>

                            </tr>

                        @endif

                        @php
                            $sumExclusiveGst = (float)($sale->total_exclusive_gst ?? collect($saleItems)->sum('exclusive_gst_amount'));
                            $sumSalesTax = (float)($sale->total_sales_tax ?? collect($saleItems)->sum('sales_tax_amount'));
                            $sumFurtherTax = (float)($sale->total_further_tax ?? collect($saleItems)->sum('further_tax_amount'));
                        @endphp

                        @if ($sumExclusiveGst > 0)
                            <tr>
                                <td class="text-muted fw-bold">
                                    Exclusive GST Subtotal
                                </td>
                                <td class="text-end fw-bold">
                                    {{ number_format($sumExclusiveGst, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if ($sumSalesTax > 0)
                            <tr>
                                <td class="text-success fw-bold">
                                    Sales Tax (@18%)
                                </td>
                                <td class="text-end text-success fw-bold">
                                    + {{ number_format($sumSalesTax, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if ($sumFurtherTax > 0)
                            <tr>
                                <td class="text-primary fw-bold">
                                    Further Tax (@3%)
                                </td>
                                <td class="text-end text-primary fw-bold">
                                    + {{ number_format($sumFurtherTax, 2) }}
                                </td>
                            </tr>
                        @endif
                        
                        @if ($sale->freight_charges > 0)
                            <tr>
                                <td class="text-muted">
                                    Freight Charges {{ $sale->freight_type === 'add' ? '(+) ' : '(-) ' }}
                                </td>
                                <td class="text-end">
                                    {{ $sale->freight_type === 'add' ? '+' : '-' }}
                                    {{ number_format($sale->freight_charges, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if ($exchangeReturnedAmount > 0)

                            <tr>

                                <td class="text-muted">
                                    Return Value
                                </td>

                                <td class="text-end text-danger">

                                    -
                                    {{
                                        number_format(
                                            $exchangeReturnedAmount,
                                            2
                                        )
                                    }}

                                </td>

                            </tr>

                        @endif


                        @php

                            $finalPayable =
                                $netBill
                                -
                                $exchangeReturnedAmount;

                        @endphp


                        <tr>

                            <td class="fw-bold text-dark fs-6"
                                style="border-top: 2px solid #34495e; padding-top: 8px;">

                                {{
                                    $finalPayable < 0
                                        ? 'Refund To Customer'
                                        : 'Grand Total'
                                }}

                            </td>


                            <td class="text-end fw-bold text-dark fs-6"
                                style="border-top: 2px solid #34495e; padding-top: 8px;">

                                {{
                                    number_format(
                                        abs($finalPayable),
                                        2
                                    )
                                }}

                            </td>

                        </tr>




                    </table>

                </div>


                <div class="text-end mt-1">

                    <small class="text-muted fst-italic"
                        style="font-size: 10px;">

                        {{
                            Str::limit(
                                $sale->total_amount_Words,
                                60
                            )
                        }}

                    </small>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================== -->
    <!-- THERMAL RECEIPT -->
    <!-- ========================================== -->

    <div class="receipt-container">

        <!-- Header -->

        <div class="company-name">

            {{ \App\Models\Setting::get(
                'company_name',
                'Three Stars Medical'
            ) }}

        </div>


        <div class="company-info">

            <div>
                {{ \App\Models\Setting::get(
                    'company_address',
                    'Hyderabad'
                ) }}
            </div>

            <div>
                Ph:
                {{ \App\Models\Setting::get(
                    'company_phone',
                    '0327-9226901'
                ) }}
            </div>

        </div>


        @if(
            \App\Models\Setting::get('facebook_link')
            ||
            \App\Models\Setting::get('tiktok_link')
            ||
            \App\Models\Setting::get('instagram_link')
            ||
            \App\Models\Setting::get('website_link')
        )

            <div style="text-align: left; font-size: 10px; line-height: 1.4; word-wrap: break-word; overflow-wrap: break-word; margin-top: 4px;">

                @if(\App\Models\Setting::get('facebook_link'))

                    <div style="margin-bottom: 2px;">
                        <strong>Facebook:</strong>
                        {{ \App\Models\Setting::get('facebook_link') }}
                    </div>

                @endif


                @if(\App\Models\Setting::get('tiktok_link'))

                    <div style="margin-bottom: 2px;">
                        <strong>TikTok:</strong>
                        {{ \App\Models\Setting::get('tiktok_link') }}
                    </div>

                @endif


                @if(\App\Models\Setting::get('instagram_link'))

                    <div style="margin-bottom: 2px;">
                        <strong>Instagram:</strong>
                        {{ \App\Models\Setting::get('instagram_link') }}
                    </div>

                @endif


                @if(\App\Models\Setting::get('website_link'))

                    <div style="margin-bottom: 2px;">
                        <strong>Website:</strong>
                        {{ \App\Models\Setting::get('website_link') }}
                    </div>

                @endif

            </div>

        @endif


        <div class="policy-banner">
            No Return, Only Exchange in 3 days
        </div>


        <div class="divider"></div>


        <!-- Meta -->

        @php
            $isWalkin = empty($sale->customer_id);
        @endphp


        <div class="meta-grid">

            <div class="meta-row">

                <span class="meta-label">
                    Invoice #:
                </span>

                <span class="meta-value">
                    {{ $sale->invoice_no }}
                </span>

            </div>


            <div class="meta-row">

                <span class="meta-label">
                    Date:
                </span>

                <span class="meta-value">

                    {{
                        $sale->created_at
                            ? $sale->created_at->format('d/m/Y h:i A')
                            : date('d/m/Y h:i A')
                    }}

                </span>

            </div>


            <div class="meta-row">

                <span class="meta-label">
                    Customer:
                </span>

                <span class="meta-value">

                    {{
                        Str::limit(
                            $sale->walkin_name
                            ??
                            ($sale->customer_relation->customer_name
                                ?? 'Walking Customer'),
                            22
                        )
                    }}

                </span>

            </div>

            @if(!empty($custNtn))
                <div class="meta-row">
                    <span class="meta-label">NTN:</span>
                    <span class="meta-value">{{ $custNtn }}</span>
                </div>
            @endif

            @if(!empty($custStrn))
                <div class="meta-row">
                    <span class="meta-label">STRN:</span>
                    <span class="meta-value">{{ $custStrn }}</span>
                </div>
            @endif


            @if (auth()->check())

                <div class="meta-row">

                    <span class="meta-label">
                        Salesperson:
                    </span>

                    <span class="meta-value">
                        {{ auth()->user()->name }}
                    </span>

                </div>

            @endif


            @if($sale->reference)

                <div class="meta-row">

                    <span class="meta-label">
                        Remarks:
                    </span>

                    <span class="meta-value">
                        {{ $sale->reference }}
                    </span>

                </div>

            @endif

        </div>


        <div class="divider"></div>


        <!-- ========================================== -->
        <!-- THERMAL ITEMS -->
        <!-- ========================================== -->

        <table class="items-table">

            <thead>

                <tr>

                    <th style="width: 6%;">
                        S.
                    </th>

                    <th style="width: 53%;">
                        Description
                    </th>

                    <th style="width: 10%;"
                        class="text-center">
                        Qty
                    </th>

                    <th style="width: 13%;"
                        class="text-end">
                        Rate
                    </th>

                    <th style="width: 18%;"
                        class="text-end">
                        Total
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach ($saleItems as $item)

                    @php

                        $sizeMode =
                            $item['size_mode'] ?? 'std';

                        $totalPieces =
                            (int)$item['total_pieces'];

                        $variantUnit =
                            strtolower(
                                $item['variant_unit']
                                ?? ''
                            );

                        $weightGrams =
                            (float)(
                                $item['weight_per_piece']
                                ?? 0
                            );

                        $qtyDisplay =
                            $totalPieces . ' Pcs';


                        if (
                            $variantUnit === 'pcs'
                            ||
                            $variantUnit === 'piece'
                            ||
                            $variantUnit === 'pieces'
                        ) {

                            $qtyDisplay =
                                $totalPieces . ' Pcs';

                            if ($weightGrams > 0) {

                                $qtyDisplay .=
                                    ' (' .
                                    (
                                        $weightGrams == (int)$weightGrams
                                            ? (int)$weightGrams
                                            : $weightGrams
                                    )
                                    .
                                    'g)';

                            }

                        }

                        elseif (
                            in_array(
                                $sizeMode,
                                [
                                    'by_kg',
                                    'by_gm',
                                    'by_feet',
                                    'by_meter'
                                ]
                            )
                        ) {

                            $uomLabel = match($sizeMode) {

                                'by_kg' =>
                                    'Kg',

                                'by_gm' =>
                                    'Gm',

                                'by_feet' =>
                                    'Ft',

                                'by_meter' =>
                                    'Mtr',

                                default =>
                                    '',

                            };


                            $qtyVal =
                                (float)(
                                    $item['qty_box']
                                    ??
                                    $item['qty']
                                    ??
                                    $totalPieces
                                );


                            $qtyDisplay =
                                (
                                    $qtyVal == (int)$qtyVal
                                        ? (int)$qtyVal
                                        : number_format($qtyVal, 3)
                                )
                                .
                                ' '
                                .
                                $uomLabel;

                        }

                        elseif (
                            in_array($sizeMode, ['by_cartons', 'by_bandal'])
                            ||
                            $sizeMode == 'by_size'
                        ) {

                            $piecesPerBox =
                                (int)(
                                    $item['pieces_per_box']
                                    ?? 1
                                );


                            if ($piecesPerBox <= 0) {
                                $piecesPerBox = 1;
                            }


                            $boxes =
                                floor(
                                    $totalPieces
                                    /
                                    $piecesPerBox
                                );


                            $loose =
                                $totalPieces
                                %
                                $piecesPerBox;


                            if (
                                $boxes > 0
                                &&
                                $loose > 0
                            ) {

                                $qtyDisplay =
                                    "$boxes.$loose";

                            }

                            elseif ($boxes > 0) {

                                $qtyDisplay =
                                    $boxes;

                            }

                            else {

                                $qtyDisplay =
                                    $loose;

                            }

                        }

                    @endphp


                    <tr>

                        <td style="width: 6%;">
                            {{ $loop->iteration }}
                        </td>


                        <td style="width: 53%;">

                            @php
                                $tVName = $item['variant_name'] ?? '';
                                $tVSize = (!empty($item['size_val']) && $item['size_val'] !== '-') ? $item['size_val'] : '';
                                $tVColor = (!empty($item['color_val']) && $item['color_val'] !== '-') ? $item['color_val'] : '';
                                
                                $tVExtra = [];
                                if ($tVSize) $tVExtra[] = $tVSize;
                                if ($tVColor) $tVExtra[] = $tVColor;
                                $tVExtraStr = count($tVExtra) > 0 ? ' (' . implode(', ', $tVExtra) . ')' : '';

                                $tProductTitle = $item['item_name'];
                                if ($tVName && strtolower(trim($tVName)) !== strtolower(trim($tProductTitle))) {
                                    $tProductTitle .= ' — ' . $tVName;
                                }
                                $tProductTitle .= $tVExtraStr;
                            @endphp

                            <span class="item-name">
                                {{ $tProductTitle }}
                            </span>

                            @php
                                $tBatchNo = $item['batch_no'] ?? null;
                                $tSerialsRaw = $item['serials'] ?? null;
                                $tSerialsList = [];
                                if (!empty($tSerialsRaw)) {
                                    if (is_array($tSerialsRaw)) {
                                        $tSerialsList = $tSerialsRaw;
                                    } elseif (is_string($tSerialsRaw)) {
                                        $decodedT = json_decode($tSerialsRaw, true);
                                        if (is_array($decodedT)) {
                                            $tSerialsList = $decodedT;
                                        } else {
                                            $tSerialsList = array_filter(array_map('trim', explode(',', $tSerialsRaw)));
                                        }
                                    }
                                }
                            @endphp

                            @if(!empty($tBatchNo))
                                <span class="item-variant" style="font-weight: 600; color: #000; font-size: 9px; display: block; margin-top: 1px;">
                                    Batch: {{ $tBatchNo }}
                                </span>
                            @endif
                            @if(!empty($tSerialsList))
                                <span class="item-variant" style="font-weight: 600; color: #000; font-size: 9px; display: block; word-break: break-all; margin-top: 1px;">
                                    S/N: {{ implode(', ', $tSerialsList) }}
                                </span>
                            @endif

                            {{-- 
                                IMPORTANT:
                                Raw color / variant data intentionally
                                removed from thermal receipt as well.
                            --}}

                        </td>


                        <td style="width: 10%;"
                            class="text-center">

                            {{ $qtyDisplay }}

                        </td>


                        <td style="width: 13%;"
                            class="text-end">

                            {{ number_format($item['price'], 0) }}

                        </td>


                        <td style="width: 18%;"
                            class="text-end">

                            {{ number_format($item['total'], 0) }}

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>


        <!-- Returned Items -->

        @if(
            $exchangeReturn
            &&
            $exchangeReturn->items->count() > 0
        )

            <div class="divider"
                style="border-top: 1px dashed #000; margin: 4px 0;">
            </div>


            <div style="font-weight: bold; font-size: 11px; margin-bottom: 2px; color: #000;">
                Returned Items:
            </div>


            <table class="items-table"
                style="margin-bottom: 4px;">

                <tbody>

                    @foreach ($exchangeReturn->items as $retItem)

                        <tr>

                            <td style="width: 6%;">
                                {{ $loop->iteration }}
                            </td>


                            <td style="width: 53%;">

                                @if(
                                    $retItem->is_manual
                                    ||
                                    empty($retItem->product_id)
                                )

                                    <span class="item-name">

                                        {{ $retItem->product_name }}
                                        (Manual)

                                    </span>

                                @else

                                    <span class="item-name">

                                        {{
                                            $retItem->product->item_name
                                            ?? 'Unknown'
                                        }}

                                    </span>

                                @endif

                            </td>


                            <td style="width: 10%;"
                                class="text-center">

                                {{ (float)$retItem->qty }}

                            </td>


                            <td style="width: 13%;"
                                class="text-end">

                                {{ number_format($retItem->price, 0) }}

                            </td>


                            <td style="width: 18%;"
                                class="text-end">

                                -
                                {{
                                    number_format(
                                        $retItem->line_total,
                                        0
                                    )
                                }}

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @endif


        <!-- Totals -->

        <div class="totals-section">

            <div class="tot-row">

                <span>
                    Sub Total:
                </span>

                <span>

                    {{
                        number_format(
                            $sale->total_bill_amount,
                            0
                        )
                    }}

                </span>

            </div>


            @if ($sale->total_extradiscount > 0)
                <div class="tot-row">
                    <span>Discount:</span>
                    <span>- {{ number_format($sale->total_extradiscount, 0) }}</span>
                </div>
            @endif

            @if ($sale->freight_charges > 0)
                <div class="tot-row">
                    <span>Freight {{ $sale->freight_type === 'add' ? '(Added)' : '(Deducted)' }}:</span>
                    <span>{{ $sale->freight_type === 'add' ? '+' : '-' }} {{ number_format($sale->freight_charges, 0) }}</span>
                </div>
            @endif


            @if ($exchangeReturnedAmount > 0)

                <div class="tot-row">

                    <span>
                        Return Value:
                    </span>

                    <span>

                        -
                        {{
                            number_format(
                                $exchangeReturnedAmount,
                                0
                            )
                        }}

                    </span>

                </div>

            @endif


            @php

                $finalPayable =
                    $sale->total_net
                    -
                    $exchangeReturnedAmount;

            @endphp


            <div class="tot-row grand-total">

                @if($finalPayable < 0)

                    <span>
                        REFUND TO CUSTOMER:
                    </span>

                    <span>

                        {{
                            number_format(
                                abs($finalPayable),
                                0
                            )
                        }}

                    </span>

                @else

                    <span>
                        TOTAL PAYABLE:
                    </span>

                    <span>

                        {{
                            number_format(
                                $finalPayable,
                                0
                            )
                        }}

                    </span>

                @endif

            </div>

        </div>





        <!-- Footer -->

        <div class="footer">

            <p>
                Thank you for shopping with us!
            </p>

            <div class="soft-credit">

                Powered by Prowave Technologies
                <br>
                📞 +92 317 3836 223

            </div>

        </div>

    </div>


    <!-- ========================================== -->
    <!-- JAVASCRIPT -->
    <!-- ========================================== -->

    <script>

        function printA4() {
            document.title = "{{ addslashes($pageTitle) }}";
            let style = document.createElement('style');
            style.id = 'a4-print-style';
            style.innerHTML = `
                @media print {
                    @page { size: auto; margin: 10mm; }
                    .screen-only { display: block !important; }
                    .receipt-container { display: none !important; }
                    body { width: auto !important; margin: 0 !important; padding: 0 !important; color: #000 !important; }
                    * { color: #000 !important; -webkit-text-fill-color: #000 !important; }
                    .text-primary, .text-danger, .text-success, .text-warning, .text-info, .text-secondary, .text-muted, .text-dark, .info-label, .invoice-title, .company-name, .terms-box {
                        color: #000 !important;
                        -webkit-text-fill-color: #000 !important;
                    }
                }
            `;
            document.head.appendChild(style);
            
            window.print();
            
            setTimeout(() => {
                let s = document.getElementById('a4-print-style');
                if (s) {
                    s.remove();
                }
            }, 500);
        }

        window.addEventListener('beforeprint', function() {
            document.title = "{{ addslashes($pageTitle) }}";
        });

        function handleGoBack() {

            const urlParams =
                new URLSearchParams(
                    window.location.search
                );


            if (
                urlParams.get('from') === 'pos'
                ||
                (
                    document.referrer
                    &&
                    document.referrer.indexOf('/pos') !== -1
                )
            ) {

                window.location.href =
                    "{{ route('pos.index') }}";

                return;

            }


            if (
                window.opener
                &&
                !window.opener.closed
            ) {

                window.close();

                setTimeout(function() {

                    window.location.href =
                        "{{ route('sale.index') }}";

                }, 150);

                return;

            }


            if (
                window.history.length > 1
                &&
                document.referrer
                &&
                document.referrer.indexOf(
                    window.location.host
                ) !== -1
                &&
                !document.referrer.includes('/sales/store')
            ) {

                window.history.back();

                return;

            }


            window.location.href =
                "{{ route('sale.index') }}";

        }

    </script>

</body>

</html>