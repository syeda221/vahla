<div class="modal-header py-2 px-3 border-bottom" style="background-color: #f8fafc;">
    <div>
        @php
            $orderDocNo = $sale->invoice_no ?: ('SO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT));
        @endphp
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 13px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                <i class="fas fa-layer-group me-1"></i> {{ $orderDocNo }}
            </span>
            <span class="fw-bold text-dark" style="font-size: 14px;">Order Invoices & Trail</span>
        </div>
        <div class="text-muted mt-1" style="font-size: 11.5px;">
            Customer: <strong class="text-dark">{{ optional($sale->customer_relation)->customer_name ?? 'Walk-in' }}</strong>
            <span class="mx-2 text-muted">&bull;</span>
            Date: <span class="text-dark font-monospace">{{ $sale->created_at->format('d M, Y') }}</span>
        </div>
    </div>
    <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="background: none; border: 0; font-size: 24px; line-height: 1; color: #64748b; cursor: pointer;">&times;</button>
</div>

<div class="modal-body p-3 bg-white" style="min-height: 0 !important; max-height: none !important;">
    {{-- KPI Cards Row --}}
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="p-2 rounded-2 border" style="background: #f8fafc;">
                <div class="text-muted fw-bold text-uppercase" style="font-size: 9.5px; letter-spacing: 0.5px;">Total Order Value</div>
                <div class="fw-bold text-dark font-monospace mt-1" style="font-size: 15px;">Rs. {{ number_format($totalOrderAmount, 2) }}</div>
                <div class="text-muted mt-1" style="font-size: 10px;">{{ count($sale->items) }} items ordered</div>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 rounded-2 border" style="background: #f8fafc;">
                <div class="text-muted fw-bold text-uppercase" style="font-size: 9.5px; letter-spacing: 0.5px;">Total Invoiced</div>
                <div class="fw-bold text-success font-monospace mt-1" style="font-size: 15px;">Rs. {{ number_format($totalInvoicedAmount, 2) }}</div>
                <div class="progress mt-1" style="height: 4px; background: #e2e8f0;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $invoicedPct }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 10px;">
                    <span>{{ $invoicedPct }}% Billed</span>
                    <span>{{ $invoices->count() }} Inv</span>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 rounded-2 border" style="background: #f8fafc;">
                <div class="text-muted fw-bold text-uppercase" style="font-size: 9.5px; letter-spacing: 0.5px;">Remaining to Bill</div>
                <div class="fw-bold {{ $remainingInvoiceAmount > 0 ? 'text-danger' : 'text-muted' }} font-monospace mt-1" style="font-size: 15px;">
                    Rs. {{ number_format($remainingInvoiceAmount, 2) }}
                </div>
                <div class="mt-1" style="font-size: 10px;">
                    @if($remainingInvoiceAmount <= 0.01)
                        <span class="badge bg-success text-white px-2 py-0" style="font-size: 9px;"><i class="fas fa-check-circle me-1"></i>Fully Invoiced</span>
                    @else
                        <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 9px;"><i class="fas fa-clock me-1"></i>Pending</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 rounded-2 border" style="background: #f8fafc;">
                <div class="text-muted fw-bold text-uppercase" style="font-size: 9.5px; letter-spacing: 0.5px;">Delivery Progress</div>
                <div class="fw-bold text-primary font-monospace mt-1" style="font-size: 15px;">{{ $deliveryPct }}%</div>
                <div class="progress mt-1" style="height: 4px; background: #e2e8f0;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $deliveryPct }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 10px;">
                    <span>Status: <strong class="text-dark">{{ ucfirst($sale->delivery_status) }}</strong></span>
                    <span>{{ $sale->deliveryChallans->count() }} DCs</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoices Table Card --}}
    <div class="border rounded-2 mb-0" style="background: #ffffff; min-height: 0 !important;">
        <div class="py-2 px-3 border-bottom d-flex justify-content-between align-items-center" style="background: #f8fafc;">
            <span class="fw-bold text-dark" style="font-size: 12px;">
                <i class="fas fa-file-invoice-dollar text-success me-1"></i> Generated Sales Invoices ({{ $invoices->count() }})
            </span>
            @php
                $uninvoicedDc = $sale->deliveryChallans->where('is_invoiced', 0)->first();
            @endphp
            @if($uninvoicedDc)
                <a href="{{ route('direct-dc.index', ['highlight_dc' => $uninvoicedDc->id, 'sale_id' => $sale->id]) }}" class="btn btn-xs btn-outline-success py-0 px-2 rounded-pill fw-bold" style="font-size: 10px; height: 22px; line-height: 20px;">
                    <i class="fas fa-plus me-1"></i> Invoice Un-invoiced DC
                </a>
            @endif
        </div>
        <div class="p-0" style="min-height: 0 !important;">
            @if($invoices->isEmpty())
                <div class="text-center py-3 text-muted">
                    <p class="mb-0 small">No partial or final invoices generated yet for this Sales Order.</p>
                </div>
            @else
                <div style="overflow-x: auto; min-height: 0 !important;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 12px; margin-bottom: 0 !important; min-height: 0 !important;">
                        <thead class="table-light">
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <th class="ps-3 py-2 text-secondary" style="font-size: 11px;">Invoice #</th>
                                <th class="py-2 text-secondary" style="font-size: 11px;">Date</th>
                                <th class="py-2 text-secondary" style="font-size: 11px;">Delivery Challan(s)</th>
                                <th class="py-2 text-end text-secondary" style="font-size: 11px;">Invoice Amount</th>
                                <th class="py-2 text-center text-secondary" style="font-size: 11px;">Status</th>
                                <th class="py-2 text-end pe-3 text-secondary" style="font-size: 11px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                @php
                                    $linkedDcs = $sale->deliveryChallans->where('invoice_id', $inv->id);
                                    $dcNos = $linkedDcs->pluck('dc_number')->toArray();
                                    if(empty($dcNos) && !empty($inv->reference)) {
                                        $dcNos[] = $inv->reference;
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-3 py-2 fw-bold font-monospace text-primary">
                                        {{ $inv->invoice_no }}
                                    </td>
                                    <td class="py-2 text-muted">{{ $inv->created_at->format('d M, Y') }}</td>
                                    <td class="py-2">
                                        @if(!empty($dcNos))
                                            @foreach($dcNos as $dcn)
                                                <span class="badge bg-light text-dark border font-monospace me-1" style="font-size: 10.5px;">{{ $dcn }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-end fw-bold font-monospace text-dark">
                                        Rs. {{ number_format($inv->total_net, 2) }}
                                    </td>
                                    <td class="py-2 text-center">
                                        <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 10px;">
                                            <i class="fas fa-check-circle me-1"></i> Posted
                                        </span>
                                    </td>
                                    <td class="py-2 text-end pe-3">
                                        <a href="{{ route('sales.invoice', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 fw-semibold" style="font-size: 11px; height: 24px; line-height: 22px;" title="Print Invoice">
                                            <i class="fas fa-print me-1"></i> Print
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light border-top">
                            <tr>
                                <th colspan="3" class="ps-3 py-2 text-end" style="font-size: 12px;">Total Invoiced:</th>
                                <th class="py-2 text-end text-success font-monospace" style="font-size: 13px;">Rs. {{ number_format($totalInvoicedAmount, 2) }}</th>
                                <th colspan="2" class="py-2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer py-2 px-3 border-top" style="background-color: #f8fafc;">
    <button type="button" class="btn btn-secondary btn-sm px-3 py-1 fw-bold rounded-pill" style="font-size: 11.5px;" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
</div>
