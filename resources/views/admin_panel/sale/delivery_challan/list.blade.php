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
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a href="{{ route('sales.dc_print', $dc->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold shadow-sm">
                                                    <i class="fas fa-print me-1"></i> Print
                                                </a>
                                                @if($dc->is_invoiced == 0 && auth()->user()->can('sales.create'))
                                                    <a href="{{ route('direct-dc.index', ['highlight_dc' => $dc->id, 'sale_id' => $sale->id]) }}" class="btn btn-sm btn-success fw-bold shadow-sm">
                                                        <i class="fas fa-file-invoice-dollar me-1"></i> Invoice
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold shadow-sm btn-delete-dc" data-id="{{ $dc->id }}" data-no="{{ $dc->dc_number }}" data-url="{{ route('sales.dc_destroy', $dc->id) }}" title="Delete Delivery Challan">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold shadow-sm btn-invoiced-alert" title="Already Invoiced">
                                                        <i class="fas fa-lock"></i> Invoiced
                                                    </button>
                                                @endif
                                            </div>
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

@section('js')
<script>
$(document).ready(function() {
    // Invoiced DC Alert
    $(document).on('click', '.btn-invoiced-alert', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Cannot Delete!",
            text: "An invoice has already been generated for this Delivery Challan, so it cannot be deleted.",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK"
        });
    });

    // Delete DC
    $(document).on('click', '.btn-delete-dc', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let dcNo = $(this).data('no') || 'Delivery Challan';

        Swal.fire({
            title: "Delete Delivery Challan?",
            text: "Are you sure you want to delete " + dcNo + "? Deducted stock will be restored to warehouse and any linked un-invoiced order / quotation will be deleted. This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, Delete & Restore Stock!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deleting Delivery Challan and restoring warehouse stock...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message || 'Delivery Challan deleted and stock restored successfully.',
                            icon: 'success',
                            timer: 1800,
                            showConfirmButton: true
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errMsg = 'Error deleting Delivery Challan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Cannot Delete!',
                            text: errMsg,
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
