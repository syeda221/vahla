@extends('admin_panel.layout.app')

@section('content')
<style>
    .premium-card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none; }
    .premium-table thead th { background: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
    .premium-table tbody td { vertical-align: middle; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    .item-list-box { background: #f8fafc; border-radius: 8px; padding: 8px 12px; border: 1px solid #e2e8f0; max-height: 120px; overflow-y: auto; }
    .item-row { display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0; border-bottom: 1px dashed #cbd5e1; }
    .item-row:last-child { border-bottom: none; }
    .uninvoiced-highlight { background-color: #fffbeb !important; border-left: 4px solid #f59e0b !important; }
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
                        <a href="{{ route('sale.index') }}" class="btn btn-light border fw-bold btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Sales</a>
                        @if($sale->delivery_status !== 'delivered' && auth()->user()->can('sales.create'))
                            <a href="{{ route('sales.create_dc', $sale->id) }}" class="btn btn-primary fw-bold btn-sm ms-2"><i class="fas fa-plus me-1"></i> New DC</a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(request('highlight_uninvoiced') || request('action') === 'invoice')
                        <div class="alert alert-warning border-warning mx-3 mt-3 mb-0 d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle me-2 fa-lg text-warning"></i>
                            <div>Select an <strong>un-invoiced Delivery Challan</strong> below and click <strong>Generate Invoice</strong> to create an invoice for that specific delivery.</div>
                        </div>
                    @endif

                    @if($challans->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-box-open text-muted" style="font-size: 48px; opacity: 0.3;"></i>
                            <h6 class="mt-3 text-muted">No Delivery Challans found for this order.</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Challan ID</th>
                                        <th>Date</th>
                                        <th>Delivery Status</th>
                                        <th>Invoice Status</th>
                                        <th style="width: 35%">Items Delivered</th>
                                        <th>Remarks</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($challans as $dc)
                                    <tr class="{{ ($dc->is_invoiced == 0 && (request('highlight_uninvoiced') || request('action') === 'invoice')) ? 'uninvoiced-highlight' : '' }}">
                                        <td>
                                            <span class="text-primary fw-bold font-monospace">{{ $dc->dc_number }}</span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold">{{ \Carbon\Carbon::parse($dc->dc_date)->format('d M, Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i>{{ ucfirst($dc->status) }}</span>
                                        </td>
                                        <td>
                                            @if($dc->is_invoiced == 1)
                                                <span class="badge bg-success text-white px-2 py-1"><i class="fas fa-file-invoice-dollar me-1"></i>Invoiced</span>
                                            @else
                                                <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-clock me-1"></i>Un-invoiced</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="item-list-box">
                                                @foreach($dc->items as $item)
                                                    @php
                                                        $saleItem = $item->saleItem;
                                                        $variant = [];
                                                        if ($saleItem && !empty($saleItem->color)) {
                                                            $b64 = base64_decode($saleItem->color, true);
                                                            if ($b64 !== false) $variant = json_decode($b64, true) ?: [];
                                                            if (empty($variant)) $variant = json_decode($saleItem->color, true) ?: [];
                                                        }
                                                        $sizeMode = $saleItem->size_mode ?? optional($item->product)->size_mode ?? 'by_size';
                                                        $vUnit = strtolower($variant['unit'] ?? optional(optional($item->product)->unit)->name ?? '');
                                                        
                                                        $dispQtyFactor = 1;
                                                        $dispUnit = 'Pcs';
                                                        if (in_array($sizeMode, ['by_kg', 'by_gm'])) {
                                                            if (in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                                                                $dispUnit = 'Pcs';
                                                                $wtConv = (float)($variant['conv_factor'] ?? $saleItem->pieces_per_box ?? 1);
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
                                                        
                                                        $rawDelivered = (float) $item->delivered_qty * $dispQtyFactor;
                                                        if ($item->delivered_qty == 0 && $item->qty > 0) {
                                                            $rawDelivered = (float) $item->qty * $dispQtyFactor;
                                                        }
                                                        $deliveredStr = $rawDelivered == (int)$rawDelivered ? (int)$rawDelivered : number_format($rawDelivered, 3, '.', '');
                                                    @endphp
                                                    <div class="item-row">
                                                        <span class="text-dark fw-medium">{{ optional($item->product)->item_name ?? 'Item' }}</span>
                                                        <span class="text-primary fw-bold font-monospace">{{ $deliveredStr }} {{ $dispUnit }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $dc->remarks ?: '-' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('sales.dc_print', $dc->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold shadow-sm">
                                                <i class="fas fa-print me-1"></i> Print
                                            </a>
                                            @if($dc->is_invoiced == 0 && auth()->user()->can('sales.create'))
                                                <form action="{{ route('sales.dc_generate_invoice', $dc->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold shadow-sm" onclick="return confirm('Generate Invoice for DC {{ $dc->dc_number }}?')">
                                                        <i class="fas fa-file-invoice-dollar me-1"></i> Generate Invoice
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
