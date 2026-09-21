@extends('admin_panel.layout.app')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Consolidate DCs to Invoice</h3>
    </div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('direct-dc.consolidate.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Select Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control select2" required>
                                <option value="">Select Customer...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="dc_container" style="display:none; margin-top:20px;">
                            <h5>Select DCs to Invoice</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>DC Number</th>
                                        <th>Date</th>
                                        <th>Total Items</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="dc_list">
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success mt-3">Generate Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2();
    $('#customer_id').change(function() {
        var custId = $(this).val();
        if(custId) {
            $.ajax({
                url: '/direct-dc/api/fetch-dcs/' + custId,
                type: 'GET',
                success: function(data) {
                    var html = '';
                    if(data.length > 0) {
                        $.each(data, function(i, dc) {
                            html += '<tr>';
                            html += '<td><input type="checkbox" name="dc_ids[]" value="'+dc.id+'" checked></td>';
                            html += '<td>'+dc.dc_number+'</td>';
                            html += '<td>'+dc.dc_date+'</td>';
                            html += '<td>'+dc.items_count+'</td>';
                            html += '<td>'+dc.total_amount+'</td>';
                            html += '</tr>';
                        });
                        $('#dc_list').html(html);
                        $('#dc_container').show();
                    } else {
                        $('#dc_list').html('<tr><td colspan="5">No un-invoiced DCs found for this customer.</td></tr>');
                        $('#dc_container').show();
                    }
                }
            });
        } else {
            $('#dc_container').hide();
        }
    });
});
</script>
@endsection
