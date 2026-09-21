@extends('admin_panel.layout.app')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Invoice Preview (DC Consolidation)</h3>
        <nav aria-label="breadcrumb">
            <a href="{{ route('direct-dc.index') }}" class="btn btn-secondary">Back to DCs</a>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-primary">New Invoice Items</h4>
                    <p class="card-description">From DCs: 
                        @php
                            $dcNumbers = $dcs->pluck('dc_number')->toArray();
                        @endphp
                        <strong>{{ implode(', ', $dcNumbers) }}</strong>
                    </p>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach($mergedItems as $mi)
                                @php 
                                    $amount = $mi['delivered_qty'] * $mi['price'];
                                    $grandTotal += $amount;
                                    
                                    $vName = '';
                                    if(!empty($mi['color'])) {
                                        $decoded = base64_decode($mi['color'], true);
                                        $vData = $decoded !== false ? json_decode($decoded, true) : null;
                                        if(!is_array($vData)) {
                                            $vData = is_string($mi['color']) ? json_decode($mi['color'], true) : $mi['color'];
                                        }
                                        if(is_array($vData) && !empty($vData['name'])) {
                                            $vName = ' <span class="text-muted">(' . $vData['name'] . ')</span>';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $mi['product']->item_name ?? 'N/A' }}</strong>{!! $vName !!}</td>
                                    <td class="text-right">{{ $mi['delivered_qty'] }}</td>
                                    <td class="text-right">{{ number_format($amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right"><strong>Total Invoice Amount:</strong></td>
                                    <td class="text-right text-success"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-info">Customer Summary</h4>
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Customer Name:</span>
                        <strong>{{ $customer->customer_name }}</strong>
                    </div>
                    
                    @php 
                        $prevBalance = $customer->previous_balance ?? 0;
                        $balText = $prevBalance >= 0 ? 'Dr (Receivable)' : 'Cr (Payable)';
                        $netBalance = $prevBalance + $grandTotal;
                        $netBalText = $netBalance >= 0 ? 'Dr (Receivable)' : 'Cr (Payable)';
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Previous Balance:</span>
                        <strong class="{{ $prevBalance >= 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format(abs($prevBalance), 2) }} <small>{{ $balText }}</small>
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">New Invoice Amount:</span>
                        <strong class="text-primary">+ {{ number_format($grandTotal, 2) }}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4" style="font-size: 1.1rem;">
                        <span class="text-dark"><strong>Net Balance:</strong></span>
                        <strong class="{{ $netBalance >= 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format(abs($netBalance), 2) }} <small>{{ $netBalText }}</small>
                        </strong>
                    </div>

                    <form action="{{ route('direct-dc.consolidate.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                        @foreach($dcIds as $did)
                            <input type="hidden" name="dc_ids[]" value="{{ $did }}">
                        @endforeach
                        
                        <button type="submit" class="btn btn-success btn-lg btn-block w-100">
                            <i class="mdi mdi-file-document-box"></i> Confirm & Generate Invoice
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
