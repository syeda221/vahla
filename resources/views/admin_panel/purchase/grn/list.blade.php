@extends('admin_panel.layout.app')

@section('content')
<style>
    .premium-card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 2px solid #cbd5e1; }
    .premium-table thead th { background: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; border-bottom: 2px solid #cbd5e1; }
    .premium-table tbody td { vertical-align: middle; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    .item-list-box { background: #f8fafc; border-radius: 8px; padding: 8px 12px; border: 1px solid #e2e8f0; max-height: 120px; overflow-y: auto; }
    .item-row { display: flex; justify-content: space-between; font-size: 12px; padding: 4px 0; border-bottom: 1px dashed #cbd5e1; }
    .item-row:last-child { border-bottom: none; }
</style>

<div class="container-fluid px-4 py-3">
    <div class="card premium-card bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-boxes text-primary"></i> Goods Receiving Notes (GRNs) for {{ $purchase->invoice_no ?: ('PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT)) }}
                </h5>
                <small class="text-muted">Vendor: <strong class="text-dark">{{ optional($purchase->vendor)->name ?: 'Vendor #' . $purchase->vendor_id }}</strong> | Warehouse: <strong class="text-dark">{{ optional($purchase->warehouse)->warehouse_name ?: 'Main Warehouse' }}</strong></small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('purchase_orders.index') }}" class="btn btn-outline-secondary btn-sm fw-bold px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Orders
                </a>
                @if($purchase->receiving_status !== 'received')
                    <a href="{{ route('purchases.grn.create', $purchase->id) }}" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="fas fa-plus me-1"></i> New GRN (Receive Goods)
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($grns->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                    <h6>No Goods Receiving Notes found for this Purchase Order.</h6>
                    <p class="small mb-3">When goods arrive from the vendor, record receiving by creating a GRN.</p>
                    <a href="{{ route('purchases.grn.create', $purchase->id) }}" class="btn btn-primary btn-sm fw-bold">
                        <i class="fas fa-truck-loading me-1"></i> Receive Goods Now
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table premium-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">GRN Number</th>
                                <th>Date</th>
                                <th>Receiving Status</th>
                                <th>Billing Status</th>
                                <th style="width: 35%">Items Received</th>
                                <th>Carrier Info / Remarks</th>
                                <th class="pe-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grns as $grn)
                                <tr>
                                    <td class="ps-3 fw-bold font-monospace text-primary">
                                        {{ $grn->grn_number }}
                                    </td>
                                    <td>{{ $grn->grn_date ? $grn->grn_date->format('d/m/Y') : '--' }}</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> {{ ucfirst($grn->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($grn->is_invoiced)
                                            <span class="badge bg-success text-white px-2 py-1 rounded-pill">
                                                <i class="fas fa-file-invoice-dollar me-1"></i> Invoiced
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">
                                                <i class="fas fa-clock me-1"></i> Uninvoiced
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="item-list-box">
                                            @foreach($grn->items as $gItem)
                                                @php
                                                    $variant = null;
                                                    if (!empty($gItem->color)) {
                                                        $decColor = base64_decode($gItem->color, true);
                                                        if ($decColor !== false) {
                                                            $variant = json_decode($decColor, true);
                                                        }
                                                        if (!is_array($variant)) {
                                                            $variant = json_decode($gItem->color, true);
                                                        }
                                                    }
                                                    $vName = is_array($variant) && !empty($variant['name']) && $variant['name'] !== '-' ? ' (' . $variant['name'] . ')' : '';
                                                @endphp
                                                <div class="item-row">
                                                    <span>{{ optional($gItem->product)->item_name ?: 'Product #' . $gItem->product_id }}{{ $vName }}</span>
                                                    <strong class="text-primary font-monospace">{{ number_format($gItem->received_qty, 2) }} Qty</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $grn->carrier_info ?: ($grn->remarks ?: '--') }}
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if(!$grn->is_invoiced)
                                                <a href="{{ route('direct-grn.index', ['highlight_grn' => $grn->id, 'purchase_id' => $purchase->id]) }}" 
                                                   class="btn btn-sm btn-success fw-bold px-2 py-1">
                                                    <i class="fas fa-file-invoice-dollar me-1"></i> Bill
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold px-2 py-1 btn-delete-grn" data-id="{{ $grn->id }}" data-no="{{ $grn->grn_number }}" data-url="{{ route('purchases.grn.destroy', $grn->id) }}" title="Delete GRN">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold px-2 py-1 btn-invoiced-grn-alert" title="Already Invoiced">
                                                    <i class="fas fa-lock"></i> Invoiced
                                                </button>
                                            @endif
                                            <a href="{{ route('purchases.grn.print', $grn->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1 fw-bold" title="Print GRN Slip">
                                                <i class="fas fa-print"></i> Print
                                            </a>
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
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Invoiced GRN Alert
    $(document).on('click', '.btn-invoiced-grn-alert', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Cannot Delete!",
            text: "A purchase bill / invoice has already been generated for this Goods Receiving Note (GRN), so it cannot be deleted.",
            icon: "warning",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK"
        });
    });

    // Delete GRN
    $(document).on('click', '.btn-delete-grn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let grnNo = $(this).data('no') || 'GRN';

        Swal.fire({
            title: "Delete GRN?",
            text: "Are you sure you want to delete " + grnNo + "? Added warehouse stock will be reverted/deducted and any linked un-invoiced Purchase Order will be deleted. This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, Delete & Revert Stock!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deleting Goods Receiving Note and deducting warehouse stock...',
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
                            text: response.message || 'GRN deleted and stock reverted successfully.',
                            icon: 'success',
                            timer: 1800,
                            showConfirmButton: true
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errMsg = 'Error deleting GRN.';
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
