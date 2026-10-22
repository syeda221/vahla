@foreach ($Purchase as $purchase)
    {{-- Table Row --}}
    <tr class="border-bottom-0">
        <td class="cell-check ps-3 text-start" style="width: 40px; vertical-align: middle;">
            <div class="d-flex align-items-center gap-2">
                <input type="checkbox" class="select-purchase-row" value="{{ $purchase->id }}" style="cursor: pointer; width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin: 0 !important;">
                <span class="d-lg-none fw-bold text-primary font-monospace" style="font-size: 13.5px;">{{ $purchase->invoice_no }}</span>
                <span class="d-lg-none badge bg-light text-muted border font-monospace">#{{ $purchase->id }}</span>
            </div>
        </td>
        <td class="cell-bill fw-bold text-muted d-none d-lg-table-cell">#{{ $purchase->id }}</td>
        <td class="cell-date text-nowrap">
            <span class="d-lg-none text-muted me-1"><i class="far fa-calendar-alt text-primary me-1"></i></span>
            <span class="font-monospace text-secondary">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</span>
        </td>
        <td class="cell-invoice font-monospace text-dark d-none d-lg-table-cell">{{ $purchase->invoice_no }}</td>
        <td class="cell-vendor-inv font-monospace text-primary fw-medium">
            <span class="d-lg-none text-muted small me-1">Ref:</span>
            <span>{{ $purchase->purchase_order_no ?? '-' }}</span>
        </td>
        <td class="font-monospace text-dark small d-none">{{ $purchase->note ?? '-' }}</td>
        <td class="cell-status text-end">
            @if ($purchase->status_purchase == 'draft')
                <span class="badge badge-warning text-dark border border-warning">Draft</span>
            @elseif ($purchase->status_purchase == 'Returned')
                <span class="badge bg-danger text-white border border-danger">Returned</span>
            @else
                <span class="badge bg-success text-white border border-success d-inline-flex align-items-center gap-1 px-2 py-1" style="border-radius: 4px;"><i class="fas fa-check"></i> Approved</span>
            @endif
        </td>
        <td class="cell-vendor">
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                    style="width: 32px; height: 32px; font-size: 14px;">
                    {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 1)) }}
                </div>
                <span class="fw-medium text-dark text-truncate" style="max-width: 140px;">{{ $purchase->vendor->name ?? 'N/A' }}</span>
            </div>
        </td>
        <td class="cell-location text-muted small">
            <span class="d-lg-none text-muted me-1"><i class="fas fa-warehouse me-1 text-secondary"></i></span>
            <span>{{ $purchase->warehouse->warehouse_name ?? 'N/A' }}</span>
        </td>

        <td class="cell-inlinedisc text-end text-dark">
            @php
                $inline_val = $purchase->items->sum('item_discount');
                $gross_subtotal = $purchase->subtotal + $inline_val;
                $inline_pct = $gross_subtotal > 0 ? ($inline_val / $gross_subtotal) * 100 : 0;
            @endphp
            <div class="financial-chip chip-inline">
                <span class="chip-label">Inline Disc</span>
                <span class="chip-value">Rs. {{ number_format($inline_val, 2) }}</span>
                @if ($inline_val > 0)
                    <div class="text-muted small mt-1 chip-sub" style="font-size: 10px;">({{ number_format($inline_pct, 1) }}%)</div>
                @endif
            </div>
        </td>

        <td class="cell-extradisc text-end text-dark">
            @php
                $add_val = (float) $purchase->additional_discount;
                $original_net = $purchase->net_amount + $add_val;
                $add_pct = $original_net > 0 ? ($add_val / $original_net) * 100 : 0;
            @endphp
            <div class="financial-chip chip-extra">
                <span class="chip-label">Add. Disc</span>
                @if ($add_val > 0)
                    <span class="chip-value">
                        <i class="fas fa-tag d-none d-lg-inline" style="font-size: 10px;"></i> Rs. {{ number_format($add_val, 2) }}
                    </span>
                    <div class="text-muted small mt-1 chip-sub" style="font-size: 10px;">({{ number_format($add_pct, 1) }}%)</div>
                @else
                    <span class="chip-value text-muted">Rs. 0.00</span>
                @endif
            </div>
        </td>

        <td class="cell-nettotal text-end fw-bold text-dark">
            <div class="financial-chip chip-net">
                <span class="chip-label">Net Bill</span>
                @if ($purchase->total_returned > 0)
                    <div>
                        <small class="text-muted text-decoration-line-through chip-sub">Rs. {{ number_format($purchase->net_amount, 2) }}</small>
                    </div>
                    <div class="chip-value text-success">
                        Rs. {{ number_format($purchase->updated_net_amount, 2) }}
                    </div>
                    <small class="text-danger chip-sub">(-{{ number_format($purchase->total_returned, 2) }})</small>
                @else
                    <span class="chip-value">Rs. {{ number_format($purchase->net_amount, 2) }}</span>
                @endif
            </div>
        </td>
        <td class="cell-paid text-end text-success d-none d-lg-table-cell">
            <div class="financial-chip chip-paid">
                <span class="chip-label">Paid</span>
                <span class="chip-value">{{ number_format($purchase->paid_amount, 2) }}</span>
            </div>
        </td>
        <td class="cell-due text-end">
            @php
                $displayDue = $purchase->total_returned > 0 ? $purchase->updated_due_amount : $purchase->due_amount;
            @endphp
            <div class="financial-chip chip-due">
                <span class="chip-label">Due Amount</span>
                @if ($displayDue > 0)
                    <span class="chip-value text-danger">{{ number_format($displayDue, 2) }}</span>
                @else
                    <span class="chip-value text-success">Paid</span>
                @endif
                <div class="d-lg-none chip-sub text-muted mt-1" style="font-size: 10px;">
                    Paid: Rs. {{ number_format($purchase->paid_amount, 2) }}
                </div>
                @if ($purchase->has_partial_return)
                    <small class="badge bg-danger text-white mt-1 d-block"><i class="fas fa-undo-alt me-1"></i> Partial Return</small>
                @elseif($purchase->is_fully_returned)
                    <small class="badge bg-danger mt-1 d-block">Fully Returned</small>
                @endif
            </div>
        </td>

        <td class="cell-action pe-3 text-center">
            <div class="dropdown">
                <button class="btn btn-premium-action dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="color: #0d6efd !important; border-color: #cce5ff !important; background-color: #ffffff !important; border-radius: 20px !important;"><i class="fas fa-eye" style="color: #0d6efd; background-color: #e6f2ff; padding: 4px; border-radius: 50%; margin-right: 6px; font-size: 10px;"></i> ACTIONS</button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @can('purchases.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.edit', $purchase->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit (Simple)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase-pos.index') }}?edit_id={{ $purchase->id }}">
                                <i class="fas fa-cash-register text-success fa-fw"></i> Edit (POS Purchase)
                            </a>
                        </li>
                    @endcan

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-purchase-btn" href="{{ route('purchase.confirm', $purchase->id) }}">
                                    <i class="fas fa-check-circle fa-fw"></i> Confirm Purchase
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase != 'draft')
                        @can('purchases.view')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.invoice', $purchase->id) }}" target="_blank">
                                    <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.receipt', $purchase->id) }}" target="_blank">
                                    <i class="fas fa-receipt text-secondary fa-fw"></i> View Receipt
                                </a>
                            </li>
                        @endcan
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.return.show', $purchase->id) }}">
                                    <i class="fas fa-undo text-warning fa-fw"></i> Return
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.delete')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 delete-btn text-danger">
                                        <i class="fas fa-trash-alt fa-fw"></i> Delete
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
