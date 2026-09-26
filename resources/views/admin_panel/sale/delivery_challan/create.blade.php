@extends('admin_panel.layout.app')

@section('content')
<style>
    .premium-card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none; }
    .premium-table thead th { background: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
    .premium-table tbody td { vertical-align: middle; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    .product-title { font-weight: 600; color: #1e293b; margin-bottom: 4px; }
    .stock-badge { font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600; }
    .qty-display { font-family: monospace; font-size: 14px; font-weight: 600; }
    .input-group-text { background: #f8fafc; color: #64748b; font-weight: 600; border-color: #cbd5e1; }
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card premium-card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $orderDocNo = $sale->invoice_no ?: (($sale->sale_type === 'sales_order' && $sale->sale_status !== 'posted') 
                                ? ('SO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) 
                                : ($sale->sale_type === 'quotation' ? ('QUO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) : ('#' . $sale->id)));
                        @endphp
                        <small class="text-muted">Order: <span class="text-dark fw-bold">{{ $orderDocNo }}</span> | Customer: <span class="text-dark fw-bold">{{ optional($sale->customer_relation)->customer_name ?? 'Walk-in' }}</span></small>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark border"><i class="fas fa-calendar-alt me-1"></i> {{ $sale->created_at->format('d M, Y') }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('sales.store_dc', $sale->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40%">Product Details</th>
                                        <th class="text-center">Ordered</th>
                                        <th class="text-center">Delivered</th>
                                        <th class="text-center text-danger">Remaining</th>
                                        <th style="width: 180px;">Deliver Now</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $item)
                                        @php
                                            $variant = [];
                                            if (!empty($item->color)) {
                                                $b64Decoded = base64_decode($item->color, true);
                                                if ($b64Decoded !== false) {
                                                    $json = json_decode($b64Decoded, true);
                                                    if (is_array($json)) $variant = $json;
                                                }
                                                if (empty($variant)) {
                                                    $json = json_decode($item->color, true);
                                                    if (is_array($json)) $variant = $json;
                                                }
                                            }
                                            
                                            $vName = $variant['name'] ?? '';
                                            $vSize = (!empty($variant['size']) && $variant['size'] !== '-') ? $variant['size'] : '';
                                            $vColor = (!empty($variant['color']) && $variant['color'] !== '-') ? $variant['color'] : '';
                                            
                                            $vExtra = [];
                                            if ($vSize) $vExtra[] = $vSize;
                                            if ($vColor) $vExtra[] = $vColor;
                                            $vExtraStr = count($vExtra) > 0 ? ' (' . implode(', ', $vExtra) . ')' : '';

                                            $baseName = $item->product_name ?? optional($item->product)->item_name ?? 'Unknown Item';
                                            $productTitle = $baseName;
                                            if ($vName && strtolower(trim($vName)) !== strtolower(trim($baseName))) {
                                                $productTitle .= ' - ' . $vName;
                                            }
                                            $productTitle .= $vExtraStr;

                                            $sizeMode = $item->size_mode ?? optional($item->product)->size_mode ?? 'by_size';
                                            
                                            $vUnit = strtolower($variant['unit'] ?? optional(optional($item->product)->unit)->name ?? '');
                                            
                                            // INFER UNIT FROM PRICE RATIO
                                            if (empty($variant['unit']) && !in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces']) && in_array($sizeMode, ['by_kg', 'by_gm'])) {
                                                $grossTotal = (float)$item->total + (float)$item->discount_amount;
                                                $basePricePerKg = ($item->total_pieces > 0) ? ($grossTotal / $item->total_pieces) : 0;
                                                $storedPrice = (float) $item->price;
                                                $wtConvForInference = (float)($variant['conv_factor'] ?? $item->pieces_per_box ?? 1);
                                                if ($wtConvForInference <= 0) $wtConvForInference = 1;
                                                
                                                if ($storedPrice > 0 && $basePricePerKg > 0) {
                                                    $ratio = round($storedPrice / $basePricePerKg, 4);
                                                    if (abs($ratio - $wtConvForInference) < 0.001) {
                                                        $vUnit = 'pcs';
                                                    } elseif (abs($ratio - 0.001) < 0.0001) {
                                                        $vUnit = 'gm';
                                                    } elseif (abs($ratio - 1) < 0.001) {
                                                        $vUnit = 'kg';
                                                    }
                                                }
                                            }

                                            $dispQtyFactor = 1;
                                            
                                            $dispUnit = 'Pcs';
                                            if (in_array($sizeMode, ['by_kg', 'by_gm'])) {
                                                if (in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                                                    $dispUnit = 'Pcs';
                                                    $wtConv = (float)($variant['conv_factor'] ?? $item->pieces_per_box ?? 1);
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
                                            
                                            // Format quantities without trailing zeroes
                                            $rawOrdered = (float) $item->total_pieces * $dispQtyFactor;
                                            $rawDelivered = (float) $item->delivered_qty * $dispQtyFactor;
                                            $rawRemaining = (float) $item->remaining_qty * $dispQtyFactor;
                                            
                                            $ordered = $rawOrdered == (int)$rawOrdered ? (int)$rawOrdered : number_format($rawOrdered, 3, '.', '');
                                            $delivered = $rawDelivered == (int)$rawDelivered ? (int)$rawDelivered : number_format($rawDelivered, 3, '.', '');
                                            $remaining = $rawRemaining == (int)$rawRemaining ? (int)$rawRemaining : number_format($rawRemaining, 3, '.', '');
                                            
                                            // Available stock
                                            $availableStock = 0;
                                            if ($item->product_id) {
                                                $availableStock = \App\Models\WarehouseStock::where('product_id', $item->product_id)
                                                    ->sum('total_pieces'); // Simplified sum across warehouses
                                            }
                                            $rawStock = (float)$availableStock * $dispQtyFactor;
                                            $availStr = $rawStock == (int)$rawStock ? (int)$rawStock : number_format($rawStock, 3, '.', '');
                                            $stockColor = $rawStock >= $rawRemaining ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle';
                                        @endphp
                                    <tr>
                                        <td>
                                            <div class="product-title">{{ $productTitle }}</div>
                                            <div class="stock-badge {{ $stockColor }} d-inline-block mt-1">
                                                <i class="fas fa-cubes me-1"></i> Stock Available: {{ $availStr }} {{ $dispUnit }}
                                            </div>
                                        </td>
                                        <td class="text-center text-muted qty-display">{{ $ordered }}</td>
                                        <td class="text-center text-primary qty-display">{{ $delivered }}</td>
                                        <td class="text-center text-danger qty-display">{{ $remaining }}</td>
                                        <td>
                                            @if($rawRemaining > 0)
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="dc_qty[{{ $item->id }}]" 
                                                        class="form-control text-end fw-bold" 
                                                        max="{{ $rawRemaining }}" min="0" step="any"
                                                        placeholder="0">
                                                    <span class="input-group-text">{{ $dispUnit }}</span>
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <span class="badge bg-success w-100 py-2"><i class="fas fa-check"></i> Delivered</span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-light border-top">
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <label class="fw-bold text-muted mb-2" style="font-size: 12px; text-transform: uppercase;">Remarks / Note</label>
                                    <input type="text" name="remarks" class="form-control" placeholder="Enter any additional notes for this delivery challan (optional)...">
                                </div>
                                <div class="col-md-4 text-end mt-3 mt-md-0">
                                    <a href="{{ route('sale.index') }}" class="btn btn-light border fw-bold me-2 px-4">Cancel</a>
                                    <button type="submit" 
                                            class="btn btn-primary fw-bold px-4 shadow-sm"
                                            data-confirm="true"
                                            data-confirm-title="Confirm Delivery Challan?"
                                            data-confirm-text="Are you sure you want to dispatch and generate this Delivery Challan?"
                                            data-confirm-btn="<i class='fas fa-truck me-1'></i> Yes, Generate DC">
                                        <i class="fas fa-save me-1"></i> Confirm DC
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
