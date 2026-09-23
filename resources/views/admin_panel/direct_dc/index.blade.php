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
        transition: all 0.2s ease-in-out;
    }
    .selection-bar.error {
        background-color: #7f1d1d !important;
        color: #fee2e2 !important;
        border: 1px solid #ef4444 !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important;
    }
    .selection-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .selection-bar .btn-consolidate {
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 6px 16px;
        font-weight: 500;
    }
    .selection-bar .btn-consolidate:disabled {
        background-color: #6b7280;
        cursor: not-allowed;
    }
    .highlight-uninvoiced-dc {
        background-color: #fffbeb !important;
        border-left: 4px solid #f59e0b !important;
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.25);
    }
</style>
<div class="content-wrapper">
    <!-- Modern Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1 fw-bold text-dark">
                <i class="fas fa-truck-loading text-primary me-2"></i> Direct Delivery Challans
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Manage and consolidate direct delivery challans</p>
        </div>
        <a href="{{ route('direct-dc.create') }}" class="btn btn-primary shadow-sm px-4 fw-bold rounded-2">
            <i class="fas fa-plus me-1"></i> Create Direct DC
        </a>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(request('highlight_dc') || request('sale_id'))
                        <div class="alert alert-warning border-warning d-flex align-items-center mb-3 py-2 px-3 shadow-sm rounded-2">
                            <i class="fas fa-info-circle fa-lg me-2 text-warning"></i>
                            <div><strong>Delivery Challan Selected:</strong> Invoice generate karne ke liye upar diye gaye <strong>"Consolidate into invoice"</strong> button par click karein.</div>
                        </div>
                    @endif
                    
                    <form id="consolidateForm" action="{{ route('direct-dc.consolidate.preview') }}" method="POST">
                        @csrf
                        
                        <div id="selectionBar" class="selection-bar">
                            <div class="selection-info">
                                <input type="checkbox" id="selectAllBar" checked>
                                <span id="selectionText"><strong>0 selected</strong> &mdash; customer: <span id="selectionCustomerName"></span></span>
                            </div>
                            <button type="button" id="btnConsolidate" class="btn-consolidate">Consolidate into invoice</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th class="fw-bold">DC Number</th>
                                        <th class="fw-bold">Date</th>
                                        <th class="fw-bold">Customer</th>
                                        <th class="fw-bold">Items Count</th>
                                        <th class="fw-bold text-center">Status</th>
                                        <th class="fw-bold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($challans as $dc)
                                    @php
                                        $cName = $dc->customer->customer_name ?? ($dc->sale->walkin_name ?? ($dc->sale->customer_relation->customer_name ?? 'N/A'));
                                        $cId = $dc->customer_id ?? ($dc->sale->customer_id ?? '');
                                        $soNo = $dc->sale ? ($dc->sale->invoice_no ?: 'SO-' . $dc->sale->id) : '';
                                        $isInvoiced = $dc->is_invoiced || !empty($dc->invoice_id) || ($dc->sale && ($dc->sale->sale_type === 'direct_sale' || $dc->sale->sale_status === 'posted'));
                                        $isHighlighted = (request('highlight_dc') == $dc->id) || (request('sale_id') && $dc->sale_id == request('sale_id') && !$isInvoiced);
                                    @endphp
                                    <tr class="{{ $isHighlighted ? 'highlight-uninvoiced-dc' : '' }}" id="dc-row-{{ $dc->id }}">
                                        <td class="text-center">
                                            @if(!$isInvoiced)
                                                <input type="checkbox" name="dc_ids[]" class="dc-checkbox" value="{{ $dc->id }}" data-customer-id="{{ $cId }}" data-customer-name="{{ $cName }}" data-sale-id="{{ $dc->sale_id ?? '' }}" data-so-number="{{ $soNo }}" {{ $isHighlighted ? 'checked' : '' }}>
                                            @else
                                                <input type="checkbox" disabled style="opacity: 0.3;">
                                            @endif
                                        </td>
                                        <td class="fw-bold text-primary">
                                            {{ $dc->dc_number }}
                                            @if($dc->sale && $dc->sale->invoice_no)
                                                <small class="text-muted d-block font-monospace" style="font-size: 11px;">
                                                    <i class="fas fa-file-invoice text-secondary me-1"></i>{{ $dc->sale->invoice_no }}
                                                </small>
                                            @endif
                                            @if($isHighlighted)
                                                <span class="badge bg-warning text-dark ms-1 shadow-sm"><i class="fas fa-check-circle me-1"></i> Selected for Invoice</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($dc->dc_date)->format('d M, Y') }}</td>
                                        <td>{{ $cName }}</td>
                                        <td>{{ $dc->items->count() }}</td>
                                        <td class="text-center">
                                            @if($isInvoiced)
                                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-check-circle me-1"></i> invoiced</span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-clock me-1"></i> un-invoiced</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                @if(!$isInvoiced)
                                                    <a href="{{ route('direct-dc.edit', $dc->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm"><i class="fas fa-edit"></i> Edit</a>
                                                @endif
                                                <a href="{{ route('sales.dc_print', $dc->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" target="_blank"><i class="fas fa-print"></i> Print</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No Direct Delivery Challans found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
$(document).ready(function() {
    function updateSelection() {
        var selectedCheckboxes = $('.dc-checkbox:checked');
        var count = selectedCheckboxes.length;
        
        if (count === 0) {
            $('#selectionBar').hide();
            $('#selectAll').prop('checked', false);
            $('#selectAllBar').prop('checked', false);
            return;
        }

        $('#selectionBar').css('display', 'flex');
        $('#selectAllBar').prop('checked', true);

        var customers = new Set();
        var customerName = '';
        var saleIds = new Set();
        var soNumbers = new Set();
        var hasSoDc = false;
        var hasDirectDc = false;

        selectedCheckboxes.each(function() {
            var cid = $(this).data('customer-id');
            var cname = $(this).data('customer-name');
            var sid = $(this).data('sale-id');
            var soNo = $(this).data('so-number');

            if (cid) {
                customers.add(cid);
                customerName = cname;
            }
            if (sid) {
                saleIds.add(sid);
                if (soNo) {
                    soNumbers.add(soNo);
                }
                hasSoDc = true;
            } else {
                hasDirectDc = true;
            }
        });

        if (customers.size > 1) {
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Validation Error:</strong> Sirf ek hi customer ki DCs ek sath consolidate ho sakti hain.');
            $('#btnConsolidate').prop('disabled', true).hide();
        } else if (saleIds.size > 1) {
            var soList = Array.from(soNumbers).join(', ');
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Validation Error:</strong> Different Sales Orders (' + (soList || 'Multiple SOs') + ') ki DCs ko aik sath consolidate nahi kar sakte! Sirf ek hi Sales Order ki DCs select karein.');
            $('#btnConsolidate').prop('disabled', true).hide();
        } else if (hasSoDc && hasDirectDc) {
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="fas fa-exclamation-triangle me-1"></i> <strong>Validation Error:</strong> Direct Delivery Challan aur Sales Order ki DC ko aik sath consolidate nahi kar sakte! Alag alag consolidate karein.');
            $('#btnConsolidate').prop('disabled', true).hide();
        } else {
            $('#selectionBar').removeClass('error');
            var infoText = '<strong>' + count + ' selected</strong> &mdash; customer: ' + customerName;
            if (soNumbers.size === 1) {
                infoText += ' <span class="badge bg-primary text-white ms-2" style="font-size: 11px;">' + Array.from(soNumbers)[0] + '</span>';
            }
            $('#selectionText').html(infoText);
            $('#btnConsolidate').prop('disabled', false).show();
        }
    }

    $(document).on('change', '#selectAll', function() {
        var isChecked = $(this).is(':checked');
        $('.dc-checkbox').prop('checked', isChecked);
        updateSelection();
    });
    
    $(document).on('change', '#selectAllBar', function() {
        if (!$(this).is(':checked')) {
            $('.dc-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            updateSelection();
        }
    });

    $(document).on('change', '.dc-checkbox', function() {
        updateSelection();
        
        if ($('.dc-checkbox:checked').length === $('.dc-checkbox').length && $('.dc-checkbox').length > 0) {
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
        if ($('.dc-checkbox:checked').length > 0 && !$('#btnConsolidate').prop('disabled') && !$('#selectionBar').hasClass('error')) {
            $('#consolidateForm').submit();
        }
    });
    
    // Call it initially in case browser cached checkbox states or highlighted via URL
    updateSelection();

    if ($('.highlight-uninvoiced-dc').length) {
        $('html, body').animate({
            scrollTop: $('.highlight-uninvoiced-dc').first().offset().top - 150
        }, 500);
    }
});
</script>
@endsection
