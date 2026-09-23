<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goods Receiving Note - {{ $grn->grn_number }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        }
        .pad-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        .meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            padding: 10px;
            border: 1px solid #cbd5e1;
        }
        .items-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        @media print {
            .no-print { display: none !important; }
            .invoice-page { box-shadow: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="action-bar no-print d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-4">
            <i class="fas fa-print me-1"></i> Print GRN Slip
        </button>
    </div>

    <div class="invoice-page">
        <div class="pad-header align-items-center">
            <div>
                <h3 class="fw-bold text-dark mb-0">GOODS RECEIVING NOTE</h3>
                <span class="badge bg-primary fs-6 mt-1 font-monospace">{{ $grn->grn_number }}</span>
            </div>
            <div class="text-end">
                <h5 class="fw-bold text-primary mb-1">VAHLA TRADERS</h5>
                <p class="text-muted small mb-0">Warehouse Inward Receiving Slip</p>
            </div>
        </div>

        <div class="meta-box">
            <div class="row g-2">
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold">Vendor:</span>
                    <strong class="text-dark d-block fs-6">{{ optional($grn->vendor)->name ?: 'Vendor #' . $grn->vendor_id }}</strong>
                    <small class="text-muted">{{ optional($grn->vendor)->address }}</small>
                </div>
                <div class="col-3">
                    <span class="text-muted small text-uppercase fw-bold">Receiving Date:</span>
                    <strong class="text-dark d-block">{{ $grn->grn_date ? $grn->grn_date->format('d M, Y') : '--' }}</strong>
                    <span class="text-muted small text-uppercase fw-bold mt-1 d-block">Warehouse:</span>
                    <strong class="text-dark d-block">{{ optional($grn->warehouse)->warehouse_name ?: 'Main Warehouse' }}</strong>
                </div>
                <div class="col-3">
                    <span class="text-muted small text-uppercase fw-bold">Purchase Order #:</span>
                    <strong class="text-primary d-block font-monospace">{{ optional($grn->purchase)->invoice_no ?: ('PO-' . $grn->purchase_id) }}</strong>
                    <span class="text-muted small text-uppercase fw-bold mt-1 d-block">Carrier Info:</span>
                    <strong class="text-dark d-block">{{ $grn->carrier_info ?: 'N/A' }}</strong>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">#</th>
                    <th>Product / Item Description</th>
                    <th>Variant / Color</th>
                    <th class="text-center">Received Qty</th>
                    <th class="text-end">Unit Rate (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grn->items as $idx => $item)
                    @php
                        $variant = null;
                        if (!empty($item->color)) {
                            $decColor = base64_decode($item->color, true);
                            if ($decColor !== false) {
                                $variant = json_decode($decColor, true);
                            }
                            if (!is_array($variant)) {
                                $variant = json_decode($item->color, true);
                            }
                        }
                        $varDetails = [];
                        if (is_array($variant)) {
                            if (!empty($variant['name']) && $variant['name'] !== '-') $varDetails[] = $variant['name'];
                            if (!empty($variant['color']) && $variant['color'] !== '-') $varDetails[] = $variant['color'];
                            if (!empty($variant['size']) && $variant['size'] !== '-') $varDetails[] = $variant['size'];
                        } elseif (!empty($item->color) && !str_starts_with($item->color, 'ey')) {
                            $varDetails[] = $item->color;
                        }
                        $varDisplay = count($varDetails) > 0 ? implode(' / ', $varDetails) : '-';
                    @endphp
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                        <td>
                            <strong class="text-dark">{{ optional($item->product)->item_name ?: 'Product #' . $item->product_id }}</strong>
                            @if(optional($item->product)->item_code)
                                <small class="text-muted font-monospace d-block">Code: {{ optional($item->product)->item_code }}</small>
                            @endif
                        </td>
                        <td>{{ $varDisplay }}</td>
                        <td class="text-center font-monospace fw-bold fs-6 text-primary">{{ number_format($item->received_qty, 2) }}</td>
                        <td class="text-end font-monospace text-dark">{{ number_format($item->purchase_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($grn->remarks)
            <div class="p-2 border rounded bg-light mb-4">
                <small class="text-muted text-uppercase fw-bold">Remarks:</small>
                <div class="text-dark small">{{ $grn->remarks }}</div>
            </div>
        @endif

        <div class="row mt-5 pt-4 text-center">
            <div class="col-4">
                <div class="border-top pt-2 fw-bold text-muted">Received By (Store Keeper)</div>
            </div>
            <div class="col-4">
                <div class="border-top pt-2 fw-bold text-muted">Inspected / Verified By</div>
            </div>
            <div class="col-4">
                <div class="border-top pt-2 fw-bold text-muted">Authorized Signature</div>
            </div>
        </div>
    </div>
</body>
</html>
