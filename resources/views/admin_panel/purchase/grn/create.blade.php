@extends('admin_panel.layout.app')

@section('content')
<style>
    .premium-card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 2px solid #cbd5e1; }
    .premium-table thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; border-bottom: 2px solid #cbd5e1; }
    .premium-table tbody td { vertical-align: middle; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    .product-title { font-weight: 700; color: #1e293b; margin-bottom: 2px; }
    .qty-display { font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 700; }
    .input-group-text { background: #f8fafc; color: #64748b; font-weight: 600; border-color: #cbd5e1; }
    .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
</style>

<div class="container-fluid px-4 py-3">
    <div class="card premium-card bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-truck-loading text-primary"></i> Receive Goods (GRN) - {{ $purchase->invoice_no ?: ('PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT)) }}
                </h5>
                <small class="text-muted">Vendor: <strong class="text-dark">{{ optional($purchase->vendor)->name ?: 'Vendor #' . $purchase->vendor_id }}</strong> | Warehouse: <strong class="text-dark">{{ optional($purchase->warehouse)->warehouse_name ?: 'Main Warehouse' }}</strong></small>
            </div>
            <div>
                <span class="badge bg-light text-dark border px-3 py-2 fw-bold">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i> Order Date: {{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : '--' }}
                </span>
            </div>
        </div>

        <form action="{{ route('purchases.grn.store', $purchase->id) }}" method="POST">
            @csrf
            <div class="card-body p-4">
                <!-- METADATA ROW -->
                <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Receiving Date</label>
                        <input type="date" name="grn_date" class="form-control fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Carrier / Truck / Bilty Info</label>
                        <input type="text" name="carrier_info" class="form-control" placeholder="e.g. Truck # LES-1234 / Driver Aslam">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold small text-muted text-uppercase">Remarks / Receiving Notes</label>
                        <input type="text" name="remarks" class="form-control" placeholder="Any quality check or warehouse note...">
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <h6 class="fw-bold text-dark text-uppercase small mb-2"><i class="fas fa-boxes text-primary me-1"></i> Order Items to Receive</h6>
                <div class="table-responsive">
                    <table class="table premium-table mb-0">
                        <thead>
                            <tr class="text-uppercase small fw-bold">
                                <th style="width: 35%" class="ps-3">Product Details</th>
                                <th class="text-center" style="width: 15%">Ordered Qty</th>
                                <th class="text-center" style="width: 15%">Already Received</th>
                                <th class="text-center text-danger" style="width: 15%">Remaining</th>
                                <th style="width: 20%" class="text-end pe-3">Receive Now</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                                @php
                                    $ordered = (float) $item->qty;
                                    $received = (float) ($item->received_qty ?? 0);
                                    $remaining = max(0, $ordered - $received);

                                    $prodName = optional($item->product)->item_name ?: 'Product #' . $item->product_id;
                                    $prodCode = optional($item->product)->item_code ?: '';

                                    // Safely decode variant if color is base64 JSON
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

                                    $variantName = '';
                                    $variantColor = '';
                                    $variantSize = '';
                                    $variantUnit = $item->unit ?? '';

                                    if (is_array($variant)) {
                                        $variantName = (!empty($variant['name']) && $variant['name'] !== '-') ? $variant['name'] : '';
                                        $variantColor = (!empty($variant['color']) && $variant['color'] !== '-') ? $variant['color'] : '';
                                        $variantSize = (!empty($variant['size']) && $variant['size'] !== '-') ? $variant['size'] : '';
                                        if (empty($variantUnit) && !empty($variant['unit'])) {
                                            $variantUnit = $variant['unit'];
                                        }
                                    } elseif (!empty($item->color) && !str_starts_with($item->color, 'ey')) {
                                        $variantColor = $item->color;
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="product-title fw-bold text-dark fs-6">{{ $prodName }}</div>
                                        <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                            @if($prodCode)
                                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">{{ $prodCode }}</span>
                                            @endif
                                            @if($variantName && strcasecmp(trim($variantName), trim($prodName)) !== 0)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">{{ $variantName }}</span>
                                            @endif
                                            @if($variantColor)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 11px;">
                                                    <i class="fas fa-palette me-1"></i>{{ $variantColor }}
                                                </span>
                                            @endif
                                            @if($variantSize)
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 11px;">
                                                    <i class="fas fa-ruler me-1"></i>{{ $variantSize }}
                                                </span>
                                            @endif
                                            @if($variantUnit)
                                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 11px;">{{ $variantUnit }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center qty-display text-muted">
                                        {{ number_format($ordered, 2) }} {{ $variantUnit }}
                                    </td>
                                    <td class="text-center qty-display text-success">
                                        {{ number_format($received, 2) }} {{ $variantUnit }}
                                    </td>
                                    <td class="text-center qty-display text-danger fw-bold">
                                        {{ number_format($remaining, 2) }} {{ $variantUnit }}
                                    </td>
                                    <td class="pe-3 text-end">
                                        @if($remaining > 0)
                                            <div class="input-group input-group-sm justify-content-end ms-auto" style="max-width: 170px;">
                                                <input type="number" 
                                                       name="grn_qty[{{ $item->id }}]" 
                                                       class="form-control text-center fw-bold grn-qty-input" 
                                                       data-remaining="{{ $remaining }}"
                                                       value="{{ $remaining }}" 
                                                       min="0" 
                                                       max="{{ $remaining }}" 
                                                       step="any">
                                                <button type="button" class="btn btn-outline-secondary btn-fill-all fw-bold" title="Receive All Remaining">All</button>
                                            </div>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                                <i class="fas fa-check"></i> Fully Received
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light border-top py-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('purchase_orders.index') }}" class="btn btn-outline-secondary px-4 fw-bold">Cancel</a>
                <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Save Goods Receiving Note & Add Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-fill-all').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = this.closest('.input-group').querySelector('.grn-qty-input');
                if (input) {
                    input.value = input.getAttribute('data-remaining');
                }
            });
        });
    });
</script>
@endsection
