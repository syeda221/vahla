@foreach ($sales as $sale)
    @php
        $pNames = 'N/A';
        if ($sale->items && $sale->items->count() > 0) {
            $pNames = $sale->items
                ->map(function($item) {
                    $baseName = $item->product_name ?: (optional($item->product)->item_name ?? '?');
                    $vData = [];
                    if (!empty($item->color)) {
                        $decoded = base64_decode($item->color, true);
                        $vData = ($decoded !== false) ? json_decode($decoded, true) : json_decode($item->color, true);
                        if (!is_array($vData)) $vData = [];
                    }
                    $vNamePart = $vData['name'] ?? '';
                    $vSizePart = ($vData['size'] ?? '') !== '-' ? ($vData['size'] ?? '') : '';
                    
                    if (!empty($vNamePart)) {
                        $fullName = trim($vNamePart . ' ' . $vSizePart);
                    } else {
                        $fullName = trim($baseName . ' ' . $vSizePart);
                    }
                    
                    return preg_replace('/\s+/', ' ', $fullName);
                })
                ->implode(', ');
        } elseif ($sale->product) {
            $pNames = $sale->product;
        }

        $statusBadge = '<span class="badge badge-warning text-dark border border-warning">Draft</span>';
        $isExchange = \Illuminate\Support\Str::startsWith($sale->reference, 'Exchange for');
        
        if ($sale->sale_status === 'posted') {
            if ($sale->is_booking) {
                $statusBadge = '<span class="badge badge-success border border-success"><i class="fas fa-check-circle me-1"></i>Confirmed Booking</span>';
            } elseif ($isExchange) {
                $statusBadge = '<span class="badge badge-info text-white border border-info"><i class="fas fa-exchange-alt me-1"></i>Exchange</span>';
            } else {
                $statusBadge = '<span class="badge badge-success border border-success">Posted</span>';
            }
        } elseif ($sale->sale_status === 'booked') {
            $statusBadge = '<span class="badge badge-warning text-dark border border-warning"><i class="fas fa-bookmark me-1"></i>Booked</span>';
        } elseif ($sale->sale_status === 'quotation') {
            $statusBadge = '<span class="badge badge-primary text-white border border-primary"><i class="fas fa-file-alt me-1"></i>Quotation</span>';
        } elseif ($sale->sale_status === 'returned') {
            $statusBadge = '<span class="badge badge-danger border border-danger">Returned</span>';
        } elseif ($sale->sale_status == 1) {
            $statusBadge = '<span class="badge badge-danger border border-danger">Return</span>';
        } elseif ($sale->sale_status === null) {
            $statusBadge = '<span class="badge badge-success border border-success">Sale</span>';
        }

        if ($sale->returns && $sale->returns->count() > 0) {
            $statusBadge .= '<br><small class="badge badge-danger border border-danger mt-1"><i class="fas fa-undo-alt me-1"></i> Partial Return</small>';
        }

        $inline_val = $sale->items ? $sale->items->sum('discount_amount') : 0;
        $bill_amount = $sale->total_bill_amount > 0 ? $sale->total_bill_amount : (float) $sale->per_total;
        $gross_subtotal = $bill_amount + $inline_val;
        $inline_pct = $gross_subtotal > 0 ? ($inline_val / $gross_subtotal) * 100 : 0;

        $collected = $sale->cash - $sale->change;
        $refunded = 0;
        if (isset($isExchange) && $isExchange && $collected <= 0) {
            $refundPayment = \App\Models\CustomerPayment::where('note', 'Refund Paid for POS Exchange #'.$sale->invoice_no)->first();
            if ($refundPayment) {
                $refunded = $refundPayment->amount;
            }
        }
    @endphp

    {{-- Table Row --}}
    <tr class="border-bottom-0">
        <td class="cell-invoice ps-3 fw-bold font-monospace">
            @if ($sale->invoice_no)
                <span class="text-primary fw-bold" style="font-size: 14.5px;">{{ $sale->invoice_no }}</span>
                <span class="badge bg-light text-muted border font-monospace ms-1" style="font-size: 10px; font-weight: 600;">#{{ $sale->id }}</span>
            @else
                <span class="text-muted">#{{ $sale->id }}</span>
            @endif
        </td>
        <td class="cell-customer">
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                    style="width: 32px; height: 32px; font-size: 13px; background-color: #e0f2fe; color: #0369a1;">
                    {{ strtoupper(substr(optional($sale->customer_relation)->customer_name ?? 'C', 0, 1)) }}
                </div>
                <div class="text-truncate">
                    <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 13px;">{{ optional($sale->customer_relation)->customer_name ?? 'Walk-in Customer' }}</span>
                </div>
            </div>
        </td>
        <td class="cell-mbill font-monospace text-dark">
            <span class="d-lg-none badge bg-light text-secondary border px-2 py-1" style="font-size: 10px; font-weight: 600;">
                <span class="text-muted">M.Bill:</span> {{ $sale->reference ?? '-' }}
            </span>
            <span class="d-none d-lg-inline">{{ $sale->reference ?? '-' }}</span>
        </td>
        <td class="cell-products text-muted small" title="{{ $pNames }}">
            <div class="d-flex align-items-center">
                <span class="d-lg-none badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill me-2 px-2 py-1 flex-shrink-0" style="font-size: 10px; font-weight: 700;">
                    <i class="fas fa-box me-1"></i>Items
                </span>
                <span class="text-dark fw-medium text-truncate" style="font-size: 12px;">{{ \Illuminate\Support\Str::limit($pNames, 45) }}</span>
            </div>
        </td>
        <td class="cell-qty text-center font-monospace">
            <span class="d-lg-none text-muted fw-bold me-1" style="font-size: 11px; text-transform: uppercase;">Items / Qty:</span>
            <span class="fw-bold text-dark">{{ $sale->total_items > 0 ? $sale->total_items : $sale->qty }}</span>
        </td>
        <td class="cell-gross text-end fw-bold text-dark font-monospace">
            <div class="financial-chip chip-gross">
                <span class="chip-label">Gross Total</span>
                <span class="chip-value">Rs. {{ number_format($gross_subtotal, 2) }}</span>
            </div>
        </td>
        <td class="cell-inlinedisc text-end text-dark font-monospace">
            <div class="financial-chip chip-inline">
                <span class="chip-label">Inline Disc</span>
                <span class="chip-value">Rs. {{ number_format($inline_val, 2) }}</span>
                @if ($inline_val > 0)
                    <span class="chip-sub">({{ number_format($inline_pct, 1) }}%)</span>
                @endif
            </div>
        </td>
        <td class="cell-extradisc text-end text-dark font-monospace">
            <div class="financial-chip chip-extra">
                <span class="chip-label">Add. Disc</span>
                @if ($sale->total_extradiscount > 0)
                    @php
                        $add_val = $sale->total_extradiscount;
                        $add_pct = $bill_amount > 0 ? ($add_val / $bill_amount) * 100 : 0;
                    @endphp
                    <span class="chip-value">Rs. {{ number_format($add_val, 2) }}</span>
                    <span class="chip-sub">({{ number_format($add_pct, 1) }}%)</span>
                @else
                    <span class="chip-value">Rs. 0.00</span>
                @endif
            </div>
        </td>
        <td class="cell-nettotal text-end text-success fw-bold font-monospace">
            <div class="financial-chip chip-net">
                <span class="chip-label">Net Bill</span>
                @if (isset($isExchange) && $isExchange)
                    @if ($collected > 0)
                        <span class="chip-value">Rs. {{ number_format($collected, 2) }}</span>
                    @elseif ($refunded > 0)
                        <span class="chip-value text-danger">-Rs. {{ number_format($refunded, 2) }}</span>
                    @else
                        <span class="chip-value">Rs. 0.00</span>
                    @endif
                    <span class="badge badge-info text-white border border-info px-1 py-0 mt-0.5" style="font-size: 9px;"><i class="fas fa-exchange-alt me-1"></i>Exchange</span>
                @else
                    <span class="chip-value">Rs. {{ number_format($sale->total_net, 2) }}</span>
                @endif
            </div>
        </td>
        <td class="cell-date text-nowrap small text-muted">
            <span class="d-lg-none text-muted me-1"><i class="far fa-calendar-alt text-primary me-1"></i></span>
            <span class="font-monospace fw-medium text-secondary">{{ $sale->created_at->format('d/m/Y') }}</span>
        </td>
        <td class="cell-status">{!! $statusBadge !!}</td>
        <td class="cell-action pe-3 text-center">
            <div class="dropdown w-100">
                <button class="btn btn-premium-action dropdown-toggle w-100" type="button" data-toggle="dropdown" data-display="static" aria-expanded="false">
                    <i class="fas fa-sliders-h me-1 text-primary"></i> <span>Actions</span> <i class="fas fa-chevron-down ms-1 text-muted" style="font-size: 9px;"></i>
                </button>
                </button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @can('sales.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $sale->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit (Simple)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('pos.index') }}?edit_id={{ $sale->id }}">
                                <i class="fas fa-cash-register text-success fa-fw"></i> Edit (POS Sale)
                            </a>
                        </li>
                    @endcan

                    @if (in_array($sale->sale_status, ['draft', 'booked', 'quotation']))
                        @can('sales.create')
                            <li>
                                <form action="{{ route('sales.confirm', $sale->id) }}" method="POST" class="confirm-booking-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-success d-flex align-items-center gap-2 py-2">
                                        <i class="fas fa-check-circle fa-fw"></i> Confirm to Post
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif

                    <li><hr class="dropdown-divider"></li>

                    @can('sales.view')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $sale->id) }}" target="_blank">
                                <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', ['id' => $sale->id, 'type' => 'estimate']) }}" target="_blank">
                                <i class="fas fa-calculator text-secondary fa-fw"></i> View Estimate
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc', $sale->id) }}" target="_blank">
                                <i class="fas fa-shipping-fast text-warning fa-fw"></i> Delivery Challan (DC)
                            </a>
                        </li>
                    @endcan

                    @if ($sale->sale_status !== 'returned')
                        @can('sales.create')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="{{ route('sale.return.show', $sale->id) }}">
                                    <i class="fas fa-undo fa-fw"></i> Return Sale
                                </a>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
