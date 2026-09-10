<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Voucher - {{ $partyTransfer->voucher_no }}</title>
    <style>
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px 0;
            color: #000;
        }

        .receipt-container {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
        }

        h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        .company-name {
            font-size: 18px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .company-info {
            font-size: 11px;
            text-align: center;
            color: #333;
            line-height: 1.4;
        }

        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin: 5px 0;
            border: 1px solid #000;
            padding: 2px;
            border-radius: 3px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .meta-info {
            font-size: 11px;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }

        .meta-info-block {
            font-size: 11px;
            margin-bottom: 3px;
        }

        .transfer-box {
            border: 1px solid #000;
            border-radius: 4px;
            padding: 8px;
            margin: 8px 0;
        }

        .transfer-box .row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
        }

        .transfer-box .arrow {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 4px 0;
        }

        .amount-words {
            font-size: 11px;
            font-style: italic;
            margin: 6px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin: 8px 0;
        }

        .items-table th {
            border-bottom: 1px dashed #000;
            border-top: 1px dashed #000;
            padding: 4px 0;
            text-align: left;
            font-weight: bold;
        }

        .items-table td {
            padding: 4px 0;
            vertical-align: top;
            border-bottom: 1px dotted #ccc;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .text-end { text-align: right !important; }
        .text-center { text-align: center !important; }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin: 8px 0;
        }
        .summary-table td {
            padding: 3px 0;
        }
        .summary-table tr.total-row td {
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 4px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 15px;
            line-height: 1.4;
        }

        .print-controls {
            width: 80mm;
            margin: 0 auto 15px auto;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary { background: #000; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
    </style>
</head>

<body>

    <div class="print-controls no-print">
        <a href="javascript:window.print()" class="btn btn-primary">🖨️ Print Transfer</a>
        <a href="{{ route('voucher.history') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="company-name">{{ \App\Models\Setting::get('company_name', 'prowave technogies') }}</div>
        <div class="company-info">
            <div>{{ \App\Models\Setting::get('company_address', 'Hyderabad') }}</div>
            <div>Ph: {{ \App\Models\Setting::get('company_phone', '0327-9226901') }}</div>
        </div>

        <div class="receipt-title">PARTY TRANSFER VOUCHER</div>
        <div class="divider"></div>

        <!-- Meta Info -->
        <div class="meta-info">
            <span><strong>Voucher No:</strong> {{ $partyTransfer->voucher_no }}</span>
            <span>{{ \Carbon\Carbon::parse($partyTransfer->date)->format('d/m/Y') }}</span>
        </div>

        <!-- Transfer Detail Box -->
        <div class="transfer-box">
            <div class="row">
                <span><strong>From:</strong> {{ $sourceName ?: '-' }}</span>
            </div>
            <div class="arrow">↓</div>
            <div class="row">
                <span><strong>To:</strong> {{ $destName ?: '-' }}</span>
            </div>
        </div>

        @if(!empty($partyTransfer->remarks))
        <div class="meta-info-block">
            <strong>Remarks:</strong> {{ $partyTransfer->remarks }}
        </div>
        @endif

        <div class="divider"></div>

        <!-- Accounts Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Account</th>
                    <th style="width: 25%; text-align: right;">Debit</th>
                    <th style="width: 25%; text-align: right;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['account_name'] ?? '-' }}</td>
                        <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '-' }}</td>
                        <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No details found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Amount -->
        <div class="amount-words">
            In Words: <strong id="amountInWords">{{ $partyTransfer->total_amount }}</strong>
        </div>

        <div class="divider"></div>

        <!-- Summary -->
        <table class="summary-table">
            <tr class="total-row">
                <td>Total Amount</td>
                <td class="text-end">{{ number_format((float)$partyTransfer->total_amount, 2) }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer">
            <div>Printed: {{ now()->format('d/m/Y H:i') }}</div>
            <div style="font-weight: bold; margin-top: 5px;">Thank You ✓</div>
        </div>
    </div>

    <script>
        function numberToWords(num) {
            const a = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten',
                'Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
            const b = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            if ((num = num.toString()).length > 9) return 'Overflow';
            let n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
            if (!n) return '';
            let str = '';
            str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + ' Crore ' : '';
            str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + ' Lakh ' : '';
            str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + ' Thousand ' : '';
            str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + ' Hundred ' : '';
            str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + ' ' : '';
            return str.trim() + ' Only';
        }
        document.addEventListener("DOMContentLoaded", function () {
            let el = document.getElementById("amountInWords");
            if (el) {
                let amount = parseFloat(el.innerText) || 0;
                el.innerText = numberToWords(Math.round(amount)) || el.innerText;
            }
        });

        window.addEventListener("load", function () {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>

</body>
</html>