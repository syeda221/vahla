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

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
<<<<<<< HEAD
            --primary-color: #000;
            --accent-color: #000;
            --border-color: #000;
            --text-color: #000;
        }

        body {
            background-color: #f8f9fa;
            color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
=======
            --primary: #0f172a;
            --primary-light: #1e293b;
            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --border: #cbd5e1;
            --border-light: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-main);
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
            font-size: 12px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

<<<<<<< HEAD
        /* Pure black override for all text elements */
        .text-primary, .text-danger, .text-success, .text-warning, .text-info, .text-secondary, .text-muted, .text-dark {
            color: #000 !important;
        }

        /* Screen Layout */
        .invoice-container {
=======
        /* Screen Action Bar */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .action-bar .btn {
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        /* A4 Page Container */
        .invoice-page {
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 28px 32px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            position: relative;
            color: #000;
        }

<<<<<<< HEAD
        .company-info {
            text-align: center;
            margin-bottom: 12px;
            color: #000;
=======
        /* Header Styling */
        .invoice-header {
            border-bottom: 2px solid var(--primary);
            padding-bottom: 16px;
            margin-bottom: 16px;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
<<<<<<< HEAD
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
=======
            color: var(--primary);
            letter-spacing: -0.02em;
            text-transform: uppercase;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .company-details {
            font-size: 11.5px;
            color: var(--text-muted);
            line-height: 1.45;
        }

        .company-details i {
            width: 14px;
            color: var(--primary-light);
        }

        .invoice-badge-title {
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            text-align: right;
            margin-bottom: 4px;
        }

        .invoice-meta-top {
            font-size: 12px;
            text-align: right;
            color: var(--text-muted);
        }

        .invoice-meta-top .meta-highlight {
            font-weight: 700;
            color: var(--primary);
            font-size: 13px;
        }

        /* Info Boxes */
        .info-card {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 14px;
            background-color: #ffffff;
            height: 100%;
        }

        .info-card-header {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary);
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 4px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .info-row {
            display: flex;
            font-size: 11.5px;
            margin-bottom: 3px;
            line-height: 1.35;
        }

        .info-row:last-child {
            margin-bottom: 0;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .info-label {
            font-weight: 600;
<<<<<<< HEAD
            color: #000;
            min-width: 65px;
            display: inline-block;
=======
            color: var(--text-muted);
            width: 75px;
            flex-shrink: 0;
        }

        .info-value {
            font-weight: 600;
            color: var(--text-main);
            flex-grow: 1;
            word-break: break-word;
        }

        /* Return Note Banner */
        .return-note-banner {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11.5px;
            color: #92400e;
            margin-bottom: 14px;
        }

        /* Main Table */
        .table-container {
            margin-top: 14px;
            margin-bottom: 16px;
            width: 100%;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

<<<<<<< HEAD
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
=======
        .invoice-table thead th {
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 8px;
            border: 1px solid var(--primary);
            vertical-align: middle;
        }

        .invoice-table tbody td {
            border: 1px solid var(--border);
            padding: 6px 8px;
            vertical-align: middle;
            color: var(--text-main);
        }

        .invoice-table tbody tr:nth-of-type(even) {
            background-color: #f8fafc;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .item-title {
            font-weight: 700;
            font-size: 12px;
            color: var(--text-main);
            line-height: 1.3;
        }

        .item-subtitle {
            font-size: 10.5px;
            color: var(--text-muted);
            margin-top: 2px;
            line-height: 1.2;
        }

<<<<<<< HEAD
        .footer-section {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 10px;
=======
        .item-badge {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid var(--border-light);
            color: #334155;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 5px;
            border-radius: 3px;
            margin-right: 4px;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        /* Exchange Return Table */
        .exchange-table thead th {
            background-color: #475569;
            color: #ffffff;
            border: 1px solid #475569;
        }

        /* Summary & Footer */
        .bottom-section {
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .terms-card {
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 8px 12px;
            background: #fafafa;
            height: 100%;
        }

        .terms-title {
            font-size: 11px;
<<<<<<< HEAD
            color: #000;
=======
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .terms-list {
            padding-left: 16px;
            margin-bottom: 0;
<<<<<<< HEAD
            color: #000;
=======
            font-size: 10.5px;
            color: #475569;
            line-height: 1.4;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .terms-list li {
            margin-bottom: 2px;
            color: #000;
        }

        .words-amount-box {
            margin-top: 8px;
            padding: 5px 10px;
            background-color: #f1f5f9;
            border-left: 3px solid var(--primary);
            border-radius: 0 4px 4px 0;
            font-size: 11px;
            color: #334155;
            line-height: 1.3;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .sig-block {
            text-align: center;
            width: 170px;
        }

        .sig-line {
            border-top: 1px solid #000000;
            margin-bottom: 5px;
        }

        .sig-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-main);
        }

        .printed-time {
            font-size: 9.5px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* Financial Totals Table */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
<<<<<<< HEAD
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
=======
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
        }

        .tot-label {
            color: var(--text-muted);
            font-weight: 600;
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        }

        .tot-val {
            text-align: right;
            font-weight: 600;
            color: var(--text-main);
        }

        .grand-total-row td {
            background-color: #f8fafc;
            border-top: 2px solid var(--primary);
            border-bottom: 2px solid var(--primary);
            font-weight: 800 !important;
            font-size: 13.5px !important;
            color: var(--primary) !important;
            padding: 7px 10px;
        }

        .closing-balance-row td {
            background-color: #0f172a;
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 13px !important;
            padding: 7px 10px;
        }

        .closing-balance-row .badge-balance {
            background-color: #ffffff;
            color: #0f172a;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 800;
            margin-left: 4px;
        }

        .avoid-page-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Print Specifics for A4 */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 11.5px !important;
            }

            .no-print,
            .action-bar {
                display: none !important;
            }

            .invoice-page {
                max-width: 100% !important;
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .invoice-table thead th {
                background-color: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-table tbody tr:nth-of-type(even) {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .grand-total-row td {
                background-color: #f1f5f9 !important;
                border-top: 2px solid #000 !important;
                border-bottom: 2px solid #000 !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .closing-balance-row td {
                background-color: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .closing-balance-row .badge-balance {
                background-color: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .words-amount-box,
            .return-note-banner,
            .terms-card {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>
@php
    $showTaxColumns = ((float)($sale->total_sales_tax ?? 0) > 0 || (float)($sale->total_further_tax ?? 0) > 0);
@endphp


<<<<<<< HEAD
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
=======
    <!-- Action Bar (Screen Only) -->
    <div class="action-bar no-print d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-dark fs-6">
                <i class="fa-solid fa-file-invoice me-1 text-primary"></i>
                Invoice Preview
            </span>
            <span class="badge bg-secondary">A4 Size</span>
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        </div>

        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-print"></i>
                Print Invoice (A4)
            </button>

            @if(Route::has('sales.receipt'))
                <a href="{{ route('sales.receipt', $sale->id) }}" target="_blank" class="btn btn-outline-dark shadow-sm">
                    <i class="fa-solid fa-receipt"></i>
                    Thermal Receipt
                </a>
            @endif

            <a href="javascript:void(0)" onclick="handleGoBack()" class="btn btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <!-- A4 Invoice Sheet -->
    <div class="invoice-page">

        <!-- Header -->
        <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-7">
                        @if(!($isEstimate ?? false))
                            <div class="company-name">
                                {{ \App\Models\Setting::get('company_name', 'Prowave Technologies') }}
                            </div>
                            <div class="company-details">
                                <div><i class="fa-solid fa-location-dot"></i> {{ \App\Models\Setting::get('company_address', 'Hyderabad') }}</div>
                                <div><i class="fa-solid fa-phone"></i> {{ \App\Models\Setting::get('company_phone', '0327-9226901') }}</div>
                                @if(\App\Models\Setting::get('company_email'))
                                    <div><i class="fa-solid fa-envelope"></i> {{ \App\Models\Setting::get('company_email') }}</div>
                                @endif
                            </div>
                        @else
                            <div class="company-name">
                                {{ \App\Models\Setting::get('company_name', 'Prowave Technologies') }}
                            </div>
                            <div class="company-details">
                                <div>{{ \App\Models\Setting::get('company_address', 'Hyderabad') }}</div>
                            </div>
                        @endif
<<<<<<< HEAD

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
                            $custNtn = $relCust ? ($relCust->ntn_number ?? (method_exists($relCust, 'getCustomValue') ? $relCust->getCustomValue('ntn_number') : null)) : null;
                            $custStrn = $relCust ? ($relCust->strn_number ?? (method_exists($relCust, 'getCustomValue') ? $relCust->getCustomValue('strn_number') : null)) : null;
                            $custCnic = $relCust ? ($relCust->cnic ?? (method_exists($relCust, 'getCustomValue') ? $relCust->getCustomValue('cnic') : null)) : null;
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

=======
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
                    </div>

                    <div class="col-5">
                        <div class="invoice-badge-title">
                            {{ ($isEstimate ?? false) ? 'Estimate' : 'Sales Invoice' }}
                        </div>
                        <div class="invoice-meta-top">
                            <div>Invoice #: <span class="meta-highlight">{{ $sale->invoice_no }}</span></div>
                            <div>Date: <span class="meta-highlight">{{ $sale->created_at ? $sale->created_at->format('d/m/Y') : date('d/m/Y') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer & Invoice Info Cards -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <span><i class="fa-solid fa-user me-1"></i> Customer Details</span>
                            @if($sale->customer_relation?->customer_id)
                                <span class="badge bg-light text-dark border">Code: {{ $sale->customer_relation->customer_id }}</span>
                            @endif
                        </div>

                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walk-in Customer') }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $sale->customer_relation->mobile ?? '—' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $sale->customer_relation->address ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <span><i class="fa-solid fa-file-lines me-1"></i> Invoice Details</span>
                            <span class="badge bg-light text-dark border">{{ $sale->created_at ? $sale->created_at->format('h:i A') : date('h:i A') }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Invoice #:</span>
                            <span class="info-value fw-bold text-primary">{{ $sale->invoice_no }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Issue Date:</span>
                            <span class="info-value">{{ $sale->created_at ? $sale->created_at->format('d M, Y') : date('d M, Y') }}</span>
                        </div>

                        @if(!empty($sale->customer_relation?->salesOfficer?->name) || auth()->check())
                            <div class="info-row">
                                <span class="info-label">Sales Rep:</span>
                                <span class="info-value">{{ $sale->customer_relation->salesOfficer->name ?? auth()->user()->name }}</span>
                            </div>
                        @endif

                        @if($sale->reference)
                            <div class="info-row">
                                <span class="info-label">Remarks:</span>
                                <span class="info-value text-muted">{{ $sale->reference }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Return Note Banner if present -->
            @if ($sale->return_note)
                <div class="return-note-banner">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    <strong>Return Note:</strong> {{ $sale->return_note }}
                </div>
            @endif

<<<<<<< HEAD
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

                    <th class="text-start" style="width: 44%">
                        Description of Product
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
=======
            <!-- Main Items Table -->
            <div class="table-container">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">Sr.</th>
                            <th class="text-start" style="width: 40%;">Item Description</th>
                            <th class="text-center" style="width: 14%;">Qty / Shipped</th>
                            <th class="text-center" style="width: 10%;">UOM</th>
                            <th class="text-end" style="width: 11%;">Rate</th>
                            <th class="text-end" style="width: 9%;">Disc</th>
                            <th class="text-end" style="width: 11%;">Net Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCartonsCount = 0;
                            $totalLooseCount = 0;
                            $totalPiecesCount = 0;
                        @endphp
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff

                        @foreach ($saleItems as $item)
                            @php
<<<<<<< HEAD
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
=======
                                $height = (float)($item['height'] ?? 0);
                                $width = (float)($item['width'] ?? 0);
                                $m2PerPiece = ($height > 0 && $width > 0) ? ($height * $width) / 10000 : 0;
                                
                                $piecesPerBox = (int)($item['pieces_per_box'] ?? 1);
                                if ($piecesPerBox <= 0) $piecesPerBox = 1;
                                
                                $totalPieces = (int)($item['total_pieces'] ?? 0);
                                $totalPiecesCount += $totalPieces;

                                $boxes = floor($totalPieces / $piecesPerBox);
                                $loosePieces = $totalPieces % $piecesPerBox;
                                $totalM2Line = $m2PerPiece * $totalPieces;

                                $sizeMode = $item['size_mode'] ?? 'by_size';
                                $variantUnit = strtolower($item['variant_unit'] ?? '');
                                $weightGrams = (float)($item['weight_per_piece'] ?? 0);

                                if ($sizeMode === 'by_cartons' || $variantUnit === 'carton' || $variantUnit === 'ctn') {
                                    $totalCartonsCount += $boxes;
                                    $totalLooseCount += $loosePieces;
                                }

                                // Format item titles & labels
                                $vName = $item['variant_name'] ?? '';
                                $vSize = (!empty($item['size_val']) && $item['size_val'] !== '-') ? $item['size_val'] : '';
                                $vColor = (!empty($item['color_val']) && $item['color_val'] !== '-') ? $item['color_val'] : '';
                                
                                $vExtra = [];
                                if ($vSize) $vExtra[] = $vSize;
                                if ($vColor) $vExtra[] = $vColor;
                                $vExtraStr = count($vExtra) > 0 ? ' (' . implode(', ', $vExtra) . ')' : '';
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff

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

<<<<<<< HEAD


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



                        @php
                            $ppb = (float)($item['pieces_per_box'] ?? 1);
                            if ($ppb <= 0) $ppb = 1;
                            $totalPieces = (int) ($item['total_pieces'] ?? ($ppb > 1 ? round(($item['qty_box'] ?? $item['qty'] ?? 0) * $ppb) : ($item['qty'] ?? 0)));
                            $sizeMode = $item['size_mode'] ?? 'std';

                            $cLabel = ($sizeMode === 'by_bandal') ? 'Bndl' : 'Ctn';
                            if (in_array($sizeMode, ['by_cartons', 'by_bandal']) || ($ppb > 1 && $sizeMode !== 'by_pieces')) {
                                $boxes = floor($totalPieces / $ppb);
                                $loose = $totalPieces % $ppb;
                                if ($boxes > 0 && $loose > 0) {
                                    $itemQtyDisplay = "{$boxes} {$cLabel} + {$loose} Pcs";
                                } elseif ($boxes > 0) {
                                    $itemQtyDisplay = "{$boxes} {$cLabel}";
                                } else {
                                    $itemQtyDisplay = "{$loose} Pcs";
                                }
                            } elseif ($sizeMode === 'by_pieces') {
                                $itemQtyDisplay = "{$totalPieces} Pcs";
                            } elseif (in_array($sizeMode, ['by_kg', 'by_gm', 'by_feet', 'by_meter'])) {
                                $uomLabel = match($sizeMode) {
                                    'by_kg' => 'Kg',
                                    'by_gm' => 'Gm',
                                    'by_feet' => 'Ft',
                                    'by_meter' => 'Mtr',
                                    default => '',
                                };
                                $qVal = (float)($item['qty_box'] ?? $item['qty'] ?? $totalPieces);
                                $itemQtyDisplay = ($qVal == (int)$qVal ? (int)$qVal : number_format($qVal, 2)) . ' ' . $uomLabel;
                            } else {
                                $itemQtyDisplay = $totalPieces > 0 ? "{$totalPieces} Pcs" : ($item['qty_box'] ?? $item['qty'] ?? 0);
                            }
                        @endphp
                        <td class="text-center fw-bold">
                            {{ $itemQtyDisplay }}
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

                    <td colspan="2" class="text-end fw-bold">
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

=======
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                
                                <td class="text-start">
                                    <div class="item-title">{{ $productTitle }}</div>
                                    <div class="item-subtitle">
                                        @if ($sizeMode == 'by_size' && $height > 0 && $width > 0)
                                            <span class="item-badge">Dims: {{ number_format($width, 0) }}x{{ number_format($height, 0) }}</span>
                                        @endif
                                        @if ($piecesPerBox > 1)
                                            <span class="item-badge">Pack: {{ $piecesPerBox }} pcs/box</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center">
                                    @if ($variantUnit === 'pcs' || $variantUnit === 'piece' || $variantUnit === 'pieces')
                                        <span class="fw-bold">{{ $totalPieces }} Pcs</span>
                                        @if ($weightGrams > 0)
                                            <small class="text-muted d-block" style="font-size: 10px;">({{ $weightGrams == (int)$weightGrams ? (int)$weightGrams : $weightGrams }}g)</small>
                                        @endif
                                    @elseif (in_array($sizeMode, ['by_kg', 'by_gm', 'by_feet', 'by_meter']))
                                        @php
                                            $uomLabel = match($sizeMode) {
                                                'by_kg' => 'Kg',
                                                'by_gm' => 'Gm',
                                                'by_feet' => 'Ft',
                                                'by_meter' => 'Meter',
                                                default => '',
                                            };
                                            $qtyVal = (float)($item['qty_box'] ?? $item['qty'] ?? $totalPieces);
                                            $displayQty = ($qtyVal == (int)$qtyVal) ? (int)$qtyVal : number_format($qtyVal, 3);
                                        @endphp
                                        <span class="fw-bold">{{ $displayQty }} {{ $uomLabel }}</span>
                                    @else
                                        <div class="fw-bold">
                                            @if ($sizeMode == 'by_pieces')
                                                {{ $totalPieces }} Pcs
                                            @else
                                                @if ($boxes > 0 && $loosePieces > 0)
                                                    {{ $boxes }} {{ $sizeMode == 'by_cartons' ? 'Ctn' : 'Box' }} + {{ $loosePieces }} Pc
                                                @elseif ($boxes > 0)
                                                    {{ $boxes }} {{ $sizeMode == 'by_cartons' ? 'Ctn' : 'Box' }}
                                                @else
                                                    {{ $loosePieces }} Pcs
                                                @endif
                                            @endif
                                        </div>
                                        <small class="text-muted" style="font-size: 10px;">({{ $totalPieces }} pcs)</small>
                                    @endif
                                </td>

                                <td class="text-center fw-semibold">
                                    @if (!empty($item['variant_unit']))
                                        {{ ucfirst($item['variant_unit']) }}
                                    @elseif ($sizeMode == 'by_pieces')
                                        Pieces
                                    @elseif ($sizeMode == 'by_cartons')
                                        Cartons
                                    @elseif ($sizeMode == 'by_size')
                                        {{ number_format($totalM2Line, 2) }} m²
                                    @else
                                        Pieces
                                    @endif
                                </td>

                                <td class="text-end">
                                    {{ number_format($item['price'], 2) }}
                                </td>

                                <td class="text-end">
                                    @php
                                        $discAmt = (float)($item['discount_amount'] ?? 0);
                                        $discPct = (float)($item['discount_percent'] ?? 0);
                                    @endphp
                                    @if ($discAmt > 0)
                                        <span class="text-danger fw-semibold">{{ number_format($discAmt, 2) }}</span>
                                        @if ($discPct > 0)
                                            <small class="text-muted d-block" style="font-size: 9.5px;">({{ number_format($discPct, 1) }}%)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td class="text-end fw-bold">
                                    {{ number_format($item['total'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Exchange Return Section if exists -->
            @php
                $exchangeReturn = \App\Models\SaleReturn::with('items.product')
                    ->where('remarks', 'LIKE', '%Invoice #'.$sale->invoice_no.'%')
                    ->first();

                $exchangeReturnedAmount = 0;
                if ($exchangeReturn) {
                    $exchangeReturnedAmount = $exchangeReturn->items->sum('line_total');
                }
            @endphp

            @if($exchangeReturn && $exchangeReturn->items->count() > 0)
                <div class="mb-3 avoid-page-break">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-danger">
                            <i class="fa-solid fa-arrow-rotate-left me-1"></i>
                            Returned Items (Exchange Deduction)
                        </span>
                        <span class="badge bg-danger">Return #{{ $exchangeReturn->return_invoice ?? $exchangeReturn->id }}</span>
                    </div>

                    <table class="invoice-table exchange-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">Sr.</th>
                                <th class="text-start" style="width: 40%;">Item Description</th>
                                <th class="text-center" style="width: 14%;">Qty</th>
                                <th class="text-center" style="width: 10%;">UOM</th>
                                <th class="text-end" style="width: 11%;">Rate</th>
                                <th class="text-end" style="width: 9%;">Disc</th>
                                <th class="text-end" style="width: 11%;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exchangeReturn->items as $retItem)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-start">
                                        <span class="item-title">{{ $retItem->product->item_name ?? ($retItem->product_name ?? 'Unknown') }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ (float)$retItem->qty }} Pcs</td>
                                    <td class="text-center">Pieces</td>
                                    <td class="text-end">{{ number_format($retItem->price, 2) }}</td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-danger fw-bold">- {{ number_format($retItem->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Footer / Financial Breakdown Section -->
            <div class="bottom-section avoid-page-break">
            <div class="row g-3">
                <!-- Terms & Signatures (Left) -->
                <div class="col-7 d-flex flex-column justify-content-between">
                    <div>
                        <div class="terms-card">
                            <div class="terms-title">
                                <i class="fa-solid fa-shield-halved me-1"></i> Terms & Conditions
                            </div>
                            <ul class="terms-list">
                                @php
                                    $invoiceTerms = \App\Models\Setting::get(
                                        'invoice_terms',
                                        "10% will be deducted on return of purchased goods within 7 days.\nLoose & Water Soaked products will not be RETURNED.\nPlease bring this invoice for any returns or exchanges."
                                    );
                                    $termLines = explode("\n", $invoiceTerms);
                                @endphp
                                @foreach($termLines as $line)
                                    @if(trim($line))
                                        <li>{{ trim($line) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        @if(!empty($sale->total_amount_Words))
                            <div class="words-amount-box">
                                <strong>Amount in Words:</strong> {{ $sale->total_amount_Words }}
                            </div>
                        @endif
                    </div>

                    <div class="signature-section">
                        <div class="sig-block">
                            <div class="sig-line"></div>
                            <div class="sig-title">Customer Signature</div>
                        </div>

                        <div class="sig-block">
                            <div class="sig-line"></div>
                            <div class="sig-title">Authorized Signature</div>
                            <div class="printed-time">Printed: {{ date('d/m/Y h:i A') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Financial Totals (Right) -->
                <div class="col-5">
                    @php
                        $grossTotal = collect($saleItems)->sum('total');
                        $totalDisc = collect($saleItems)->sum('discount_amount') + (float)($sale->total_extradiscount ?? 0);
                        $netBill = (float)$sale->total_net;
                        $paidAmount = (float)($sale->cash ?? 0);
                        $finalPayable = $netBill - $exchangeReturnedAmount;
                        $finalBal = $previousBalance + $finalPayable - $paidAmount;
                    @endphp

                    <table class="totals-table">
<<<<<<< HEAD

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


                        @if ($totalCartonsCount > 0 || $totalLooseCount > 0)

                            <tr>

                                <td class="text-muted fw-bold">
                                    Total Cartons / Bundals
                                </td>

                                <td class="text-end fw-bold" style="color: var(--primary-color);">

                                    @if ($totalCartonsCount > 0 && $totalLooseCount > 0)
                                        {{ $totalCartonsCount }} Ctn/Bndl + {{ $totalLooseCount }} Pcs
                                    @elseif ($totalCartonsCount > 0)
                                        {{ $totalCartonsCount }} Ctn/Bndl
=======
                        @if ($totalCartonsCount > 0)
                            <tr>
                                <td class="tot-label">Total Cartons</td>
                                <td class="tot-val text-primary">
                                    @if ($totalLooseCount > 0)
                                        {{ $totalCartonsCount }} Ctn + {{ $totalLooseCount }} Pcs
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
                                    @else
                                        {{ $totalLooseCount }} Pcs (Loose)
                                    @endif
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td class="tot-label">Gross Subtotal</td>
                            <td class="tot-val">{{ number_format($grossTotal + $totalDisc, 2) }}</td>
                        </tr>

<<<<<<< HEAD
                        @if ($totalDisc > 0 || $extraDisc > 0)

=======
                        @if ($totalDisc > 0)
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
                            <tr>
                                <td class="tot-label text-danger">Total Discount</td>
                                <td class="tot-val text-danger">- {{ number_format($totalDisc, 2) }}</td>
                            </tr>
<<<<<<< HEAD


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

=======
                        @endif

>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
                        @if ($exchangeReturnedAmount > 0)
                            <tr>
                                <td class="tot-label text-danger">Exchange Return</td>
                                <td class="tot-val text-danger">- {{ number_format($exchangeReturnedAmount, 2) }}</td>
                            </tr>
                        @endif

<<<<<<< HEAD

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




=======
                        <tr class="grand-total-row">
                            <td>{{ $finalPayable < 0 ? 'Refund To Customer' : 'Net Bill Amount' }}</td>
                            <td class="text-end">{{ number_format(abs($finalPayable), 2) }}</td>
                        </tr>

                        @if (round(abs($previousBalance), 2) > 0)
                            <tr>
                                <td class="tot-label">Previous Balance</td>
                                <td class="tot-val">
                                    {{ number_format(abs($previousBalance), 2) }}
                                    <small class="text-muted fw-bold">({{ $previousBalance >= 0 ? 'Dr' : 'Cr' }})</small>
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td class="tot-label text-success">Paid / Cash Received</td>
                            <td class="tot-val text-success">{{ number_format($paidAmount, 2) }}</td>
                        </tr>

                        @if ($sale->change > 0)
                            <tr>
                                <td class="tot-label text-danger">Change Returned</td>
                                <td class="tot-val text-danger">{{ number_format($sale->change, 2) }}</td>
                            </tr>
                        @endif

                        @if(!empty($sale->customer_id) || round(abs($finalBal), 2) > 0)
                            <tr class="closing-balance-row">
                                <td>Closing Balance</td>
                                <td class="text-end">
                                    {{ number_format(abs($finalBal), 2) }}
                                    <span class="badge-balance">{{ $finalBal >= 0 ? 'Dr' : 'Cr' }}</span>
                                </td>
                            </tr>
                        @endif
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
                    </table>
                </div>
            </div>
        </div>

    </div>

<<<<<<< HEAD

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
            @elseif(!empty($custCnic))
                <div class="meta-row">
                    <span class="meta-label">CNIC:</span>
                    <span class="meta-value">{{ $custCnic }}</span>
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


                            $cLabel = ($sizeMode === 'by_bandal') ? 'Bndl' : 'Ctn';
                            if (
                                $boxes > 0
                                &&
                                $loose > 0
                            ) {

                                $qtyDisplay =
                                    "{$boxes} {$cLabel} + {$loose} Pcs";

                            }

                            elseif ($boxes > 0) {

                                $qtyDisplay =
                                    "{$boxes} {$cLabel}";

                            }

                            else {

                                $qtyDisplay =
                                    "{$loose} Pcs";

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

=======
    <!-- Navigation Script -->
    <script>
>>>>>>> 90637cd5e8d4dcf256b13f10f431242d3f0accff
        function handleGoBack() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('from') === 'pos' || (document.referrer && document.referrer.indexOf('/pos') !== -1)) {
                window.location.href = "{{ route('pos.index') }}";
                return;
            }

            if (window.opener && !window.opener.closed) {
                window.close();
                setTimeout(function() {
                    window.location.href = "{{ route('sale.index') }}";
                }, 150);
                return;
            }

            if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1 && !document.referrer.includes('/sales/store')) {
                window.history.back();
                return;
            }

            window.location.href = "{{ route('sale.index') }}";
        }
    </script>
</body>

</html>