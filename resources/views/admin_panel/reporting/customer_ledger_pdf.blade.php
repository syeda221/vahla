<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Customer Statement</title>
    <style>
        @page {
            margin: 25px 25px;
            size: A4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            color: #000000;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .header-container {
            text-align: center;
            margin-bottom: 14px;
        }
        .company-name {
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            padding: 0;
        }
        .report-title {
            font-size: 16px;
            font-weight: 800;
            margin: 4px 0 0 0;
            letter-spacing: 0.5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            vertical-align: top;
            padding: 0;
        }
        .info-left {
            width: 55%;
            text-align: left;
        }
        .info-right {
            width: 45%;
            text-align: right;
        }
        .info-line {
            margin-bottom: 2px;
            font-size: 10.5px;
        }
        .statement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .statement-table th {
            background-color: #ffffff;
            color: #000000;
            font-size: 9.5px;
            font-weight: 800;
            padding: 8px 4px;
            border-top: 3px solid #000000;
            border-bottom: 2px solid #000000;
            border-left: none;
            border-right: none;
            text-align: left;
        }
        .statement-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #e5e7eb;
            border-top: none;
            border-left: none;
            border-right: none;
            font-size: 9.5px;
            color: #000000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        
        .totals-row td {
            border-top: 2px solid #000000 !important;
            border-bottom: 2px solid #000000 !important;
            font-weight: 800;
            background-color: #ffffff;
            padding: 8px 4px;
            font-size: 10px;
        }
        .footer-note {
            margin-top: 20px;
            text-align: center;
            font-size: 8.5px;
            color: #777777;
        }
    </style>
</head>
<body>

    {{-- Company & Statement Header --}}
    @php
        $companyNameVal = \DB::table('settings')->where('key', 'company_name')->value('value') ?: 'WHITE DIAMOND (PACKAGES PRIVATE LIMITED)';
        $companyAddress = \DB::table('settings')->where('key', 'company_address')->value('value');
        $companyPhone = \DB::table('settings')->where('key', 'company_phone')->value('value');
        $companyEmail = \DB::table('settings')->where('key', 'company_email')->value('value');
        $companyWebsite = \DB::table('settings')->where('key', 'website_link')->value('value');
        $companyLogo = \DB::table('settings')->where('key', 'company_logo')->value('value');
    @endphp
    <div class="header-container">
        @if(!empty($companyLogo) && file_exists(public_path($companyLogo)))
            <div style="margin-bottom: 10px;">
                <img src="{{ public_path($companyLogo) }}" alt="Company Logo" style="max-height: 80px; max-width: 250px; object-fit: contain;">
            </div>
        @endif
        <div class="company-name" style="color: #000;">{{ $companyNameVal }}</div>
        <div style="font-size: 12px; margin-top: 5px; color: #222; font-weight: bold;">
            @if(!empty($companyAddress)) {!! nl2br(e($companyAddress)) !!} <br> @endif
            @if(!empty($companyPhone)) <strong>Tel:</strong> {{ $companyPhone }} @endif
            @if(!empty($companyEmail)) <strong>| Email:</strong> {{ $companyEmail }} @endif
            @if(!empty($companyWebsite)) <strong>| Web:</strong> {{ $companyWebsite }} @endif
        </div>
        <div class="report-title" style="color: #000;">CUSTOMER LEDGER</div>
    </div>

    {{-- Meta Information Box --}}
    <table class="info-table">
        <tr>
            <td class="info-left">
                <div class="info-line"><strong>Account No :</strong> {{ $customer->id ?? '-' }}</div>
                <div class="info-line" style="font-size: 13px;"><strong>{{ $customer->customer_name ?? 'All Customers' }}</strong></div>
                @if(!empty($customer->address))
                    <div class="info-line">{{ $customer->address }}</div>
                @endif
                @if(!empty($customer->mobile) || !empty($customer->email_address))
                    <div class="info-line">
                        @if(!empty($customer->mobile)) <strong>Tel:</strong> {{ $customer->mobile }} @endif
                        @if(!empty($customer->mobile) && !empty($customer->email_address)) | @endif
                        @if(!empty($customer->email_address)) <strong>Email:</strong> {{ $customer->email_address }} @endif
                    </div>
                @endif
            </td>
            <td class="info-right">
                <div class="info-line"><strong>Date :</strong> {{ $start == '2000-01-01' ? 'All' : \Carbon\Carbon::parse($start)->format('d/m/Y') . ' to ' . \Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
                <div class="info-line"><strong>Currency :</strong> PKR</div>
                <div class="info-line" style="font-size: 11px;">
                    <strong>Total Due :</strong> PKR {{ number_format($closing_balance, 2) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Main Ledger Statement Table --}}
    <table class="statement-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">Date</th>
                <th style="width: 13%;">Details</th>
                <th style="width: 15%;">Bank Name</th>
                <th style="width: 20%;">Ref No.</th>
                <th style="width: 8%; text-align: center;">V. No.</th>
                <th style="width: 7%; text-align: center;">Quantity</th>
                <th style="width: 9%; text-align: right;">Debit</th>
                <th style="width: 9%; text-align: right;">Credit</th>
                <th style="width: 9%; text-align: right;">Balance</th>
            </tr>
        </thead>
        <tbody>
            {{-- Opening Balance Row --}}
            <tr>
                <td class="text-center">-</td>
                <td>Opening Balance</td>
                <td class="text-center">-</td>
                <td>Opening Balance (B/F)</td>
                <td class="text-center">-</td>
                <td class="text-center">0</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end fw-bold">{{ number_format($opening_balance, 2) }}</td>
            </tr>

            {{-- Transactions Rows --}}
            @foreach($transactions as $t)
                @php
                    $debit = (float) ($t['debit'] ?? 0);
                    $credit = (float) ($t['credit'] ?? 0);
                    $qty = (float) ($t['quantity'] ?? 0);
                    $bal = (float) ($t['balance'] ?? 0);
                @endphp
                <tr>
                    <td class="text-center">{{ $t['date'] }}</td>
                    <td>{{ $t['details'] ?? '-' }}</td>
                    <td>{{ !empty($t['bank_name']) && $t['bank_name'] !== '-' ? $t['bank_name'] : '' }}</td>
                    <td>{{ $t['ref_no'] ?? '' }}</td>
                    <td class="text-center">{{ !empty($t['v_no']) && $t['v_no'] !== '-' ? $t['v_no'] : '' }}</td>
                    <td class="text-center">{{ $qty != 0 ? number_format($qty) : '0' }}</td>
                    <td class="text-end">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                    <td class="text-end">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                    <td class="text-end fw-bold">{{ number_format($bal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="5" class="text-end"></td>
                <td class="text-center fw-bold">{{ number_format($total_qty) }}</td>
                <td class="text-end fw-bold">{{ $total_debit > 0 ? number_format($total_debit, 2) : '' }}</td>
                <td class="text-end fw-bold">{{ $total_credit > 0 ? number_format($total_credit, 2) : '' }}</td>
                <td class="text-end fw-bold">{{ number_format($closing_balance, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        Generated on {{ date('d/m/Y h:i A') }} | This is a computer generated statement.
    </div>

</body>
</html>
