@extends('admin_panel.layout.app')
@section('content')
<style>
    .selection-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        background-color: #1a1d21;
        color: #fff;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .selection-bar.error {
        background-color: #5a1919;
        color: #fca5a5;
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
</style>
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Direct Delivery Challans</h3>
        <nav aria-label="breadcrumb">
            <a href="{{ route('direct-dc.create') }}" class="btn btn-primary">Create Direct DC</a>
        </nav>
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
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>DC Number</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Items Count</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($challans as $dc)
                                    <tr>
                                        <td>
                                            @if(!$dc->is_invoiced)
                                                <input type="checkbox" name="dc_ids[]" class="dc-checkbox" value="{{ $dc->id }}" data-customer-id="{{ $dc->customer_id }}" data-customer-name="{{ $dc->customer->customer_name ?? 'N/A' }}">
                                            @else
                                                <input type="checkbox" disabled style="opacity: 0.3;">
                                            @endif
                                        </td>
                                        <td>{{ $dc->dc_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($dc->dc_date)->format('d-m-Y') }}</td>
                                        <td>{{ $dc->customer->customer_name ?? 'N/A' }}</td>
                                        <td>{{ $dc->items->count() }}</td>
                                        <td>
                                            @if($dc->is_invoiced)
                                                <span class="badge badge-success">invoiced</span>
                                            @else
                                                <span class="badge badge-warning">un-invoiced</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$dc->is_invoiced)
                                                <a href="{{ route('direct-dc.edit', $dc->id) }}" class="btn btn-sm btn-info">Edit</a>
                                            @endif
                                            <a href="{{ route('sales.dc_print', $dc->id) }}" class="btn btn-sm btn-primary" target="_blank">Print</a>
                                        </td>
                                    </tr>
                                    @endforeach
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

        selectedCheckboxes.each(function() {
            var cid = $(this).data('customer-id');
            var cname = $(this).data('customer-name');
            if(cid) {
                customers.add(cid);
                customerName = cname;
            }
        });

        if (customers.size > 1) {
            $('#selectionBar').addClass('error');
            $('#selectionText').html('<i class="mdi mdi-alert-circle"></i> Sirf ek hi customer ki DCs ek sath consolidate ho sakti hain.');
            $('#btnConsolidate').prop('disabled', true);
            $('#btnConsolidate').hide();
        } else {
            $('#selectionBar').removeClass('error');
            $('#selectionText').html('<strong>' + count + ' selected</strong> &mdash; customer: ' + customerName);
            $('#btnConsolidate').prop('disabled', false);
            $('#btnConsolidate').show();
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

    $(document).on('click', '#btnConsolidate', function() {
        if ($('.dc-checkbox:checked').length > 0 && !$('#btnConsolidate').prop('disabled')) {
            $('#consolidateForm').submit();
        }
    });
    
    // Call it initially in case browser cached checkbox states
    updateSelection();
});
</script>
@endsection
