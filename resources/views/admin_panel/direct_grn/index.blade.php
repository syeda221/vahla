@extends('admin_panel.layout.app')

@section('content')
<style>
    .selection-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        background-color: #1a1d21;
        color: #fff;
        padding: 10px 18px;
        border-radius: 6px;
        margin-bottom: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .selection-bar.error {
        background-color: #7f1d1d !important;
        color: #fee2e2 !important;
        border: 1px solid #ef4444 !important;
    }
    .selection-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .selection-bar .btn-consolidate {
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 6px 16px;
        font-weight: 600;
    }
    .selection-bar .btn-consolidate:disabled {
        background-color: #6b7280;
        cursor: not-allowed;
    }
    .highlight-uninvoiced-grn {
        background-color: #fffbeb !important;
        border-left: 4px solid #f59e0b !important;
    }
</style>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="page-title mb-1 fw-bold text-dark">
                <i class="fas fa-boxes text-primary me-2"></i> Goods Receiving Notes (GRNs) Management
            </h4>
            <p class="text-muted mb-0 small">Manage warehouse inward receiving slips and consolidate into purchase bills</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase_orders.index') }}" class="btn btn-outline-secondary fw-bold px-3">
                <i class="fas fa-file-invoice me-1"></i> Purchase Orders
            </a>
            <a href="{{ route('direct-grn.create') }}" class="btn btn-primary shadow-sm px-4 fw-bold">
                <i class="fas fa-plus me-1"></i> Create Direct GRN
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(request('highlight_grn') || request('purchase_id'))
        <div class="alert alert-warning border-warning d-flex align-items-center mb-3 py-2 px-3 shadow-sm rounded-2">
            <i class="fas fa-info-circle fa-lg me-2 text-warning"></i>
            <div><strong>Goods Receiving Note(s) Selected:</strong> Purchase Bill generate karne ke liye upar diye gaye <strong>"Consolidate into Purchase Bill"</strong> button par click karein.</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3">
            <form id="consolidateForm" action="{{ route('direct-grn.consolidate.preview') }}" method="POST">
                @csrf
                
                <div id="selectionBar" class="selection-bar">
                    <div class="selection-info">
                        <input type="checkbox" id="selectAllBar" checked>
                        <span id="selectionText"><strong>0 selected</strong> &mdash; Vendor: <span id="selectionVendorName" class="fw-bold text-info"></span></span>
                    </div>
                    <button type="button" id="btnConsolidate" class="btn-consolidate">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Consolidate into Purchase Bill
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-uppercase text-muted small fw-bold">
                                <th style="width: 40px;" class="text-center ps-3">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>GRN Number</th>
                                <th>Date</th>
                                <th>Vendor</th>
                                <th>PO Number</th>
                                <th>Warehouse</th>
                                <th>Items</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Billing Status</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grns as $grn)
                                @php
                                    $vName = optional($grn->vendor)->name ?: 'Vendor #' . $grn->vendor_id;
                                    $vId = $grn->vendor_id;
                                    $poNo = $grn->purchase ? ($grn->purchase->invoice_no ?: 'PO-' . $grn->purchase->id) : '';
                                    $isInvoiced = $grn->is_invoiced || !empty($grn->invoice_id);
                                    $isHighlighted = (request('highlight_grn') == $grn->id) || (request('purchase_id') && $grn->purchase_id == request('purchase_id') && !$isInvoiced);
                                @endphp
                                <tr class="{{ $isHighlighted ? 'highlight-uninvoiced-grn' : '' }}" id="grn-row-{{ $grn->id }}">
                                    <td class="text-center ps-3">
                                        @if(!$isInvoiced)
                                            <input type="checkbox" 
                                                   name="grn_ids[]" 
                                                   value="{{ $grn->id }}" 
                                                   class="grn-checkbox" 
                                                   data-vendor-id="{{ $vId }}"
                                                   data-vendor-name="{{ $vName }}"
                                                   data-purchase-id="{{ $grn->purchase_id ?? '' }}"
                                                   data-po-number="{{ $poNo }}"
                                                   {{ $isHighlighted ? 'checked' : '' }}>
                                        @else
                                            <i class="fas fa-lock text-muted" title="Already Invoiced"></i>
                                        @endif
                                    </td>
                                    <td class="fw-bold font-monospace text-primary">
                                        {{ $grn->grn_number }}
                                        @if($isHighlighted)
                                            <span class="badge bg-warning text-dark ms-1 shadow-sm"><i class="fas fa-check-circle me-1"></i> Selected for Bill</span>
                                        @endif
                                    </td>
                                    <td>{{ $grn->grn_date ? $grn->grn_date->format('d/m/Y') : '--' }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $vName }}</strong>
                                    </td>
                                    <td>
                                        @if($grn->purchase)
                                            <span class="font-monospace badge bg-light text-dark border">{{ $poNo }}</span>
                                        @else
                                            <span class="text-muted small">Direct GRN</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ optional($grn->warehouse)->warehouse_name ?: 'Main Warehouse' }}</td>
                                    <td>
                                        <span class="badge bg-secondary font-monospace">{{ $grn->items->count() }} items</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> {{ ucfirst($grn->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($isInvoiced)
                                            <span class="badge bg-success text-white px-2 py-1 rounded-pill">
                                                <i class="fas fa-file-invoice-dollar me-1"></i> Invoiced
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">
                                                <i class="fas fa-clock me-1"></i> Uninvoiced
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('purchases.grn.print', $grn->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1 fw-bold" title="Print Slip">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5>No Goods Receiving Notes Recorded</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    function updateSelection() {
        var selected = $('.grn-checkbox:checked');
        var count = selected.length;
        
        if (count === 0) {
            $('#selectionBar').hide().removeClass('error');
            return;
        }

        $('#selectionBar').css('display', 'flex');
        
        var vendors = new Set();
        var purchaseIds = new Set();
        var poNumbers = new Set();
        var hasPoGrn = false;
        var hasDirectGrn = false;
        var vendorName = '';

        selected.each(function() {
            var vId = $(this).data('vendor-id');
            var vName = $(this).data('vendor-name');
            var pId = $(this).data('purchase-id');
            var poNo = $(this).data('po-number');

            if (vId) {
                vendors.add(vId);
                vendorName = vName;
            }

            if (pId) {
                purchaseIds.add(pId);
                if (poNo) {
                    poNumbers.add(poNo);
                }
                hasPoGrn = true;
            } else {
                hasDirectGrn = true;
            }
        });

        if (vendors.size > 1) {
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Validation Error:</strong> Sirf ek hi vendor ki GRNs aik sath consolidate ho sakti hain.');
            $('#btnConsolidate').prop('disabled', true).hide();
        } else if (purchaseIds.size > 1) {
            var poList = Array.from(poNumbers).join(', ');
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Validation Error:</strong> Different Purchase Orders (' + (poList || 'Multiple POs') + ') ki GRNs ko aik sath consolidate nahi kar sakte! Sirf ek hi Purchase Order ki GRNs select karein.');
            $('#btnConsolidate').prop('disabled', true).hide();
        } else if (hasPoGrn && hasDirectGrn) {
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Validation Error:</strong> Direct GRN aur Purchase Order ki GRN ko aik sath consolidate nahi kar sakte! Alag alag consolidate karein.');
            $('#btnConsolidate').prop('disabled', true).hide();
        } else {
            $('#selectionBar').removeClass('error');
            var infoText = '<strong>' + count + ' selected</strong> &mdash; Vendor: ' + vendorName;
            if (poNumbers.size === 1) {
                infoText += ' <span class="badge bg-primary text-white ms-2" style="font-size: 11px;">' + Array.from(poNumbers)[0] + '</span>';
            }
            $('#selectionText').html(infoText);
            $('#btnConsolidate').prop('disabled', false).show();
        }
    }

    $(document).on('change', '#selectAll', function() {
        var isChecked = $(this).is(':checked');
        $('.grn-checkbox').prop('checked', isChecked);
        updateSelection();
    });
    
    $(document).on('change', '#selectAllBar', function() {
        if (!$(this).is(':checked')) {
            $('.grn-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            updateSelection();
        }
    });

    $(document).on('change', '.grn-checkbox', function() {
        updateSelection();
        
        if ($('.grn-checkbox:checked').length === $('.grn-checkbox').length && $('.grn-checkbox').length > 0) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
    });

    $('#consolidateForm').on('submit', function(e) {
        if ($('#selectionBar').hasClass('error') || $('#btnConsolidate').prop('disabled')) {
            e.preventDefault();
            return false;
        }
    });

    $(document).on('click', '#btnConsolidate', function() {
        if ($('.grn-checkbox:checked').length > 0 && !$('#btnConsolidate').prop('disabled') && !$('#selectionBar').hasClass('error')) {
            $('#consolidateForm').submit();
        }
    });
    
    // Call initially in case URL has highlight params
    updateSelection();

    if ($('.highlight-uninvoiced-grn').length) {
        $('html, body').animate({
            scrollTop: $('.highlight-uninvoiced-grn').first().offset().top - 150
        }, 500);
    }
});
</script>
@endsection
