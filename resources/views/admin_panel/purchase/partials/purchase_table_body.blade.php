@foreach ($Purchase as $purchase)
    {{-- Table Row --}}
    <tr class="border-bottom-0">
        <td class="ps-3 text-center" style="width: 40px; vertical-align: middle;">
            <input type="checkbox" class="select-purchase-row" value="{{ $purchase->id }}" style="cursor: pointer; width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin: 0 auto !important;">
        </td>
        <td class="fw-bold text-muted text-nowrap">#{{ $purchase->id }}</td>
        <td class="text-nowrap">
            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}
        </td>
        <td class="font-monospace text-dark text-nowrap">
            <span class="fw-bold">{{ $purchase->invoice_no }}</span>
            @if($purchase->goodsReceivingNotes && $purchase->goodsReceivingNotes->count() > 0)
                <div class="mt-1 d-flex flex-wrap gap-1">
                    @foreach($purchase->goodsReceivingNotes as $grn)
                        @canany(['grn.print', 'direct_grn.print'])
                        <a href="{{ route('purchases.grn.print', $grn->id) }}" target="_blank" class="badge bg-light text-primary border border-primary text-decoration-none px-1 py-0" style="font-size: 10px;" title="View GRN">
                            <i class="fas fa-boxes"></i> {{ $grn->grn_number }}
                        </a>
                        @else
                        <span class="badge bg-light text-secondary border px-1 py-0" style="font-size: 10px;">
                            <i class="fas fa-boxes"></i> {{ $grn->grn_number }}
                        </span>
                        @endcanany
                    @endforeach
                </div>
            @endif
            @if($purchase->parent_po_id)
                <div class="mt-1">
                    <span class="badge bg-light text-secondary border px-1 py-0" style="font-size: 10px;">
                        <i class="fas fa-file-contract"></i> PO: {{ optional($purchase->parentOrder)->invoice_no ?: ('PO-' . str_pad($purchase->parent_po_id, 4, '0', STR_PAD_LEFT)) }}
                    </span>
                </div>
            @endif
        </td>
        <td class="font-monospace text-dark small text-nowrap">{{ $purchase->note ?? '-' }}</td>
        <td class="text-center text-nowrap">
            @if ($purchase->status_purchase == 'draft')
                <span class="badge bg-warning text-dark border border-warning fw-bold px-2 py-1"><i class="fas fa-file-alt me-1"></i> Draft</span>
            @elseif ($purchase->status_purchase == 'Returned')
                <span class="badge bg-danger text-white border border-danger fw-bold px-2 py-1"><i class="fas fa-undo me-1"></i> Returned</span>
            @else
                <span class="badge bg-success text-white border border-success fw-bold px-2 py-1"><i class="fas fa-check-circle me-1"></i> Approved</span>
            @endif
        </td>
        <td class="text-nowrap">
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 28px; height: 28px; font-size: 12px; flex-shrink: 0;">
                    {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 1)) }}
                </div>
                <span class="fw-medium text-dark">{{ $purchase->vendor->name ?? 'N/A' }}</span>
            </div>
        </td>
        <td class="text-muted small text-nowrap">
            {{ $purchase->warehouse->warehouse_name ?? 'N/A' }}
        </td>

        <td class="text-end text-dark text-nowrap">
            @php
                $inline_val = $purchase->items->sum('item_discount');
                $gross_subtotal = $purchase->subtotal + $inline_val;
                $inline_pct = $gross_subtotal > 0 ? ($inline_val / $gross_subtotal) * 100 : 0;
            @endphp
            Rs. {{ number_format($inline_val, 2) }}
            @if ($inline_val > 0)
                <div class="text-muted small mt-1" style="font-size: 10px;">({{ number_format($inline_pct, 1) }}%)</div>
            @endif
        </td>

        <td class="text-end text-dark text-nowrap">
            @if ($purchase->additional_discount > 0)
                @php
                    $add_val = $purchase->additional_discount;
                    $original_net = $purchase->net_amount + $add_val;
                    $add_pct = $original_net > 0 ? ($add_val / $original_net) * 100 : 0;
                @endphp
                <span class="badge rounded-pill border px-2 py-1" style="background-color: #fff8e1; color: #b78103; border-color: #ffe082 !important; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-tag" style="font-size: 10px;"></i> Rs. {{ number_format($add_val, 2) }}
                </span>
                <div class="text-muted small mt-1" style="font-size: 10px;">({{ number_format($add_pct, 1) }}%)</div>
            @else
                <span class="text-muted">Rs. 0.00</span>
            @endif
        </td>

        <td class="text-end fw-bold text-dark text-nowrap">
            @if ($purchase->total_returned > 0)
                <div>
                    <small class="text-muted text-decoration-line-through">Rs. {{ number_format($purchase->net_amount, 2) }}</small>
                </div>
                <div class="text-success">
                    Rs. {{ number_format($purchase->updated_net_amount, 2) }}
                </div>
                <small class="text-danger">(-{{ number_format($purchase->total_returned, 2) }})</small>
            @else
                Rs. {{ number_format($purchase->net_amount, 2) }}
            @endif
        </td>
        @if (!isset($isOrdersPage) || !$isOrdersPage)
            <td class="text-end text-success text-nowrap">
                {{ number_format($purchase->paid_amount, 2) }}
                @if (($purchase->advance_adjusted ?? 0) > 0.001)
                    <br><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-1 py-0" style="font-size: 10px;" title="{{ $purchase->advance_settlement_note ?? 'Settled from Vendor Advance' }}">
                        <i class="fas fa-coins me-1 text-warning"></i>Adv: Rs. {{ number_format($purchase->advance_adjusted, 0) }}
                    </span>
                @endif
            </td>
            <td class="text-end text-nowrap">
                @php
                    $displayDue = $purchase->total_returned > 0 ? $purchase->updated_due_amount : $purchase->due_amount;
                @endphp
                @if ($displayDue > 0)
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">{{ number_format($displayDue, 2) }}</span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Paid</span>
                @endif

                @if ($purchase->has_partial_return)
                    <br><small class="badge bg-danger text-white mt-1"><i class="fas fa-undo-alt me-1"></i> Partial Return</small>
                @elseif($purchase->is_fully_returned)
                    <br><small class="badge bg-danger mt-1">Fully Returned</small>
                @endif
            </td>
        @endif

        <td class="pe-3 text-center col-action text-nowrap" style="width: 1%; white-space: nowrap;">
            <div class="d-inline-flex align-items-center justify-content-center gap-1">
                @if (!empty($purchase->attachment))
                    <a href="{{ asset('uploads/attachments/' . $purchase->attachment) }}" target="_blank"
                        class="btn btn-sm btn-outline-info shadow-sm" title="View Attachment / Bill" style="padding: 4px 8px; border-radius: 6px;">
                        <i class="fas fa-paperclip"></i>
                    </a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-premium-action dropdown-toggle" type="button" data-toggle="dropdown" data-boundary="window" data-bs-boundary="window" aria-expanded="false">
                        <i class="fas fa-ellipsis-v small me-1"></i> Actions
                    </button>
                @php
                    $isPO = ($purchase->purchase_type === 'purchase_order');
                    $canEditPurchase = $isPO ? auth()->user()->can('purchase_orders.edit') : auth()->user()->can('purchases.edit');
                    $canDeletePurchase = $isPO ? (auth()->user()->can('purchase_orders.delete') || auth()->user()->hasRole('Super Admin')) : (auth()->user()->can('purchases.delete') || auth()->user()->hasRole('Super Admin'));
                @endphp
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @if (!empty($purchase->attachment))
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-info fw-semibold" href="{{ asset('uploads/attachments/' . $purchase->attachment) }}" target="_blank">
                                <i class="fas fa-paperclip text-info fa-fw"></i> View Attachment
                            </a>
                        </li>
                    @endif
                    @if ($canEditPurchase)
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-primary btn-quick-attach" href="javascript:void(0)"
                               data-id="{{ $purchase->id }}"
                               data-invoice="{{ $purchase->invoice_no ?? $purchase->id }}"
                               data-has-attachment="{{ !empty($purchase->attachment) ? '1' : '0' }}"
                               data-existing-url="{{ !empty($purchase->attachment) ? asset('uploads/attachments/' . $purchase->attachment) : '' }}"
                               data-url="{{ route('purchase.upload_attachment', $purchase->id) }}">
                                <i class="fas fa-file-upload text-primary fa-fw"></i> {{ !empty($purchase->attachment) ? 'Change Attachment' : 'Upload Attachment' }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    @endif
                    @if ($canEditPurchase)
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
                    @endif

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success fw-bold confirm-purchase-btn" href="{{ route('purchase.confirm', $purchase->id) }}">
                                    <i class="fas fa-check-circle fa-fw text-success"></i> Convert to Purchase
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase == 'received')
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-purchase-btn" href="{{ route('purchase.confirm', $purchase->id) }}">
                                    <i class="fas fa-file-invoice-dollar fa-fw"></i> Generate Invoice
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase != 'draft')
                        @if ($isPO)
                            @can('purchase_orders.print')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.invoice', $purchase->id) }}" target="_blank">
                                        <i class="fas fa-file-invoice text-info fa-fw"></i> Print Purchase Order
                                    </a>
                                </li>
                            @endcan
                        @else
                            @can('purchases.print')
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
                                @if ($displayDue > 0.001 && $purchase->vendor_id && $purchase->purchase_type !== 'purchase_order' && $purchase->status_purchase !== 'draft')
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown-item text-warning-emphasis d-flex align-items-center gap-2 py-2 fw-semibold btn-settle-advance-modal"
                                            data-purchase-id="{{ $purchase->id }}"
                                            data-invoice-no="{{ $purchase->invoice_no }}">
                                            <i class="fas fa-coins text-warning fa-fw"></i> Settle from Vendor Advance
                                        </a>
                                    </li>
                                @endif
                            @endcan
                        @endif
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.return.show', $purchase->id) }}">
                                    <i class="fas fa-undo text-warning fa-fw"></i> Return
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase == 'draft' || $canDeletePurchase)
                        @if ($canDeletePurchase)
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
                        @endif
                    @endif
                </ul>
            </div>
            </div>
        </td>
    </tr>
@endforeach
