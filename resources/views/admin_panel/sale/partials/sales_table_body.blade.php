@foreach ($sales as $sale)
    @php
        $pNames = 'N/A';
        if ($sale->items && $sale->items->count() > 0) {
            $pNames = $sale->items
                ->map(fn($item) => optional($item->product)->item_name ?? '?')
                ->implode(', ');
        } elseif ($sale->product) {
            $pNames = $sale->product;
        }

        $statusBadge = '<span class="badge badge-warning text-dark border border-warning">Draft</span>';
        $isExchange = \Illuminate\Support\Str::startsWith($sale->reference, 'Exchange for');
        
        if ($sale->sale_type === 'quotation') {
            $hasConverted = \App\Models\Sale::where('parent_quotation_id', $sale->id)->exists();
            if ($hasConverted) {
                $statusBadge = '<span class="badge badge-success text-white border border-success"><i class="fas fa-check-circle me-1"></i>Converted</span>';
            } else {
                $statusBadge = '<span class="badge badge-info text-white border border-info"><i class="fas fa-file-contract me-1"></i>Quotation</span>';
            }
        } elseif ($sale->sale_type === 'sales_order') {
            if ($sale->sale_status === 'posted') {
                $statusBadge = '<span class="badge badge-success border border-success"><i class="fas fa-file-invoice-dollar me-1"></i>Invoiced</span>';
            } else {
                if ($sale->delivery_status === 'pending') {
                    $statusBadge = '<span class="badge badge-warning text-dark border border-warning"><i class="fas fa-clock me-1"></i>SO Pending</span>';
                } elseif ($sale->delivery_status === 'partial') {
                    $hasInvoicedDc = $sale->deliveryChallans()->where('is_invoiced', 1)->exists();
                    $hasUninvoicedDc = $sale->deliveryChallans()->where('is_invoiced', 0)->exists();
                    if ($hasInvoicedDc && !$hasUninvoicedDc) {
                        $statusBadge = '<span class="badge text-white border" style="background-color: #0d6efd;"><i class="fas fa-file-invoice-dollar me-1"></i>SO Partial (Invoiced)</span>';
                    } elseif ($hasInvoicedDc && $hasUninvoicedDc) {
                        $statusBadge = '<span class="badge text-white border" style="background-color: #6f42c1;"><i class="fas fa-file-invoice-dollar me-1"></i>SO Partial (Partial Inv)</span>';
                    } else {
                        $statusBadge = '<span class="badge text-white border" style="background-color: #6610f2;"><i class="fas fa-truck-loading me-1"></i>SO Partial (Inv. Pending)</span>';
                    }
                } elseif ($sale->delivery_status === 'delivered') {
                    $statusBadge = '<span class="badge badge-info text-white border border-info"><i class="fas fa-check-circle me-1"></i>Delivered (Inv. Pending)</span>';
                }
            }
        } else {
            if ($sale->sale_status === 'posted') {
                if ($sale->is_booking) {
                    $statusBadge = '<span class="badge badge-success border border-success"><i class="fas fa-check-circle me-1"></i>Confirmed Booking</span>';
                } elseif ($isExchange) {
                    $statusBadge = '<span class="badge badge-info text-white border border-info"><i class="fas fa-exchange-alt me-1"></i>Exchange</span>';
                } else {
                    $statusBadge = '<span class="badge badge-success border border-success">Posted</span>';
                }
            } elseif ($sale->sale_status === 'booked') {
                if ($sale->is_booking) {
                    $statusBadge = '<span class="badge badge-warning text-dark border border-warning"><i class="fas fa-bookmark me-1"></i>Booked</span>';
                } else {
                    $statusBadge = '<span class="badge badge-info text-white border border-info"><i class="fas fa-file-invoice me-1"></i>Quotation</span>';
                }
            } elseif ($sale->sale_status === 'returned') {
                $statusBadge = '<span class="badge badge-danger border border-danger">Returned</span>';
            } elseif ($sale->sale_status == 1) {
                $statusBadge = '<span class="badge badge-danger border border-danger">Return</span>';
            } elseif ($sale->sale_status === null) {
                $statusBadge = '<span class="badge badge-success border border-success">Sale</span>';
            }
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
        <td class="ps-3 fw-bold font-monospace">
            @if ($sale->sale_type === 'quotation')
                <span class="text-info fw-bold">{{ $sale->invoice_no ?: ('QUO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}</span>
                <small class="text-muted d-block" style="font-size: 11px;">#{{ $sale->id }}</small>
            @elseif ($sale->sale_type === 'sales_order')
                <span class="{{ $sale->sale_status === 'posted' ? 'text-success' : 'text-warning' }} fw-bold">{{ $sale->invoice_no ?: ('SO-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)) }}</span>
                <small class="text-muted d-block" style="font-size: 11px;">#{{ $sale->id }}</small>
                <a href="javascript:void(0)" class="btn-order-trail text-decoration-none d-inline-flex align-items-center mt-1" data-sale-id="{{ $sale->id }}" title="View Invoices & Trail">
                    <span class="badge rounded-pill" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 600; padding: 2px 7px;">
                        <i class="fas fa-layer-group text-primary me-1"></i> Trail
                    </span>
                </a>
            @elseif ($sale->invoice_no)
                <span class="text-primary fw-bold">{{ $sale->invoice_no }}</span>
                <small class="text-muted d-block" style="font-size: 11px;">#{{ $sale->id }}</small>
                @if ($sale->parent_quotation_id)
                    <a href="javascript:void(0)" class="btn-order-trail text-decoration-none d-inline-flex align-items-center mt-1" data-sale-id="{{ $sale->parent_quotation_id }}" title="View Order Trail">
                        <span class="badge rounded-pill" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-size: 10px; font-weight: 500; padding: 2px 7px;">
                            <i class="fas fa-link text-info me-1"></i> SO #{{ $sale->parent_quotation_id }}
                        </span>
                    </a>
                @endif
            @else
                <span class="text-muted">#{{ $sale->id }}</span>
            @endif
        </td>
        <td>
            @php
                $displayCustomerName = $sale->walkin_name ?: (optional($sale->customer_relation)->customer_name ?? 'N/A');
            @endphp
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 32px; height: 32px; font-size: 14px; background-color: #e0f2fe; color: #0369a1;">
                    {{ strtoupper(substr($displayCustomerName, 0, 1)) }}
                </div>
                <div>
                    <span class="fw-medium text-dark">{{ $displayCustomerName }}</span>
                    @if($sale->walkin_name)
                        <span class="badge bg-light text-muted border ms-1" style="font-size: 10px;">Walk-in</span>
                    @endif
                </div>
            </div>
        </td>
        <td class="font-monospace text-dark" style="max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $sale->reference ?? '' }}">
            @php
                $rawRef = $sale->reference ?? '';
                $cleanRef = $rawRef;
                $isSpecial = false;
                $badgeIcon = 'fas fa-hashtag';
                $badgeClass = 'bg-light text-secondary border';

                if (preg_match('/^Consolidated\s+Invoice\s+for\s*(.*)$/i', $rawRef, $m)) {
                    $cleanRef = trim($m[1]);
                    $isSpecial = true;
                    $badgeIcon = 'fas fa-layer-group';
                    $badgeClass = 'bg-light text-primary border';
                } elseif (preg_match('/^Invoice\s+for\s+DC:\s*(.*)$/i', $rawRef, $m)) {
                    $cleanRef = trim($m[1]);
                    $isSpecial = true;
                    $badgeIcon = 'fas fa-truck';
                    $badgeClass = 'bg-light text-info border';
                } elseif (preg_match('/^Exchange\s+for\s*(.*)$/i', $rawRef, $m)) {
                    $cleanRef = 'Ex: ' . trim($m[1]);
                    $isSpecial = true;
                    $badgeIcon = 'fas fa-exchange-alt';
                    $badgeClass = 'bg-light text-warning border';
                }
            @endphp

            @if(empty($rawRef))
                <span class="text-muted">-</span>
            @elseif($isSpecial || str_contains($cleanRef, ','))
                @php
                    $parts = array_filter(array_map('trim', explode(',', $cleanRef)));
                    $firstPart = $parts[0] ?? $cleanRef;
                    $extraCount = count($parts) - 1;
                @endphp
                <span class="badge {{ $badgeClass }} font-monospace text-truncate d-inline-flex align-items-center"
                      style="font-size: 11px; max-width: 135px; vertical-align: middle; padding: 3px 6px;"
                      title="{{ $rawRef }}">
                    <i class="{{ $badgeIcon }} me-1" style="font-size: 10px;"></i>
                    <span class="text-truncate">{{ \Illuminate\Support\Str::limit($firstPart, 13, '..') }}</span>
                    @if($extraCount > 0)
                        <span class="badge bg-secondary-subtle text-dark ms-1 px-1 py-0" style="font-size: 9px; font-weight: 600;">+{{ $extraCount }}</span>
                    @endif
                </span>
            @else
                <span title="{{ $rawRef }}">{{ \Illuminate\Support\Str::limit($rawRef, 15, '..') }}</span>
            @endif
        </td>
        <td title="{{ $pNames }}" class="text-muted small">
            {{ \Illuminate\Support\Str::limit($pNames, 40) }}
        </td>
        <td class="text-center font-monospace">
            {{ $sale->total_items > 0 ? $sale->total_items : $sale->qty }}
        </td>
        <td class="text-end fw-bold text-dark font-monospace">
            Rs. {{ number_format($gross_subtotal, 2) }}
        </td>
        <td class="text-end text-dark font-monospace">
            Rs. {{ number_format($inline_val, 2) }}
            @if ($inline_val > 0)
                <div class="text-muted small mt-1" style="font-size: 10px;">({{ number_format($inline_pct, 1) }}%)</div>
            @endif
        </td>
        <td class="text-end text-dark font-monospace">
            @if ($sale->total_extradiscount > 0)
                @php
                    $add_val = $sale->total_extradiscount;
                    $add_pct = $bill_amount > 0 ? ($add_val / $bill_amount) * 100 : 0;
                @endphp
                <span class="badge rounded-pill border px-2 py-1" style="background-color: #fff8e1; color: #b78103; border-color: #ffe082 !important; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-tag" style="font-size: 10px;"></i> Rs. {{ number_format($add_val, 2) }}
                </span>
                <div class="text-muted small mt-1" style="font-size: 10px;">({{ number_format($add_pct, 1) }}%)</div>
            @else
                <span class="text-muted">Rs. 0.00</span>
            @endif
        </td>
        <td class="text-end text-dark fw-bold font-monospace">
            @if (isset($isExchange) && $isExchange)
                @if ($collected > 0)
                    Rs. {{ number_format($collected, 2) }}
                @elseif ($refunded > 0)
                    <span class="text-danger">-Rs. {{ number_format($refunded, 2) }}</span>
                @else
                    Rs. 0.00
                @endif
                <br><span class="badge badge-info text-white border border-info px-1 py-0 mt-1" style="font-size: 10px;"><i class="fas fa-exchange-alt me-1"></i>Exchange</span>
            @else
                Rs. {{ number_format($sale->total_net, 2) }}
            @endif
        </td>

        @php
            $saleNet = (float) $sale->total_net;
            $salePaid = max(0, (float) ($sale->cash ?? 0) + (float) ($sale->card ?? 0));
            $saleChange = (float) ($sale->change ?? 0);
            if ($saleChange > 0) {
                $salePaid = max(0, $salePaid - $saleChange);
            }
            $salePaid = min($saleNet, $salePaid);
            $saleDue = max(0, $saleNet - $salePaid);
        @endphp

        {{-- Paid Column --}}
        <td class="text-end font-monospace">
            @if ($sale->sale_type === 'quotation')
                <span class="text-muted">-</span>
            @elseif ($salePaid > 0.001)
                <span class="text-success fw-bold">Rs. {{ number_format($salePaid, 2) }}</span>
            @else
                <span class="text-muted">Rs. 0.00</span>
            @endif
        </td>

        {{-- Due Column --}}
        <td class="text-end font-monospace">
            @if ($sale->sale_type === 'quotation')
                <span class="text-muted">-</span>
            @elseif ($saleDue <= 0.001 && $saleNet > 0)
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                    <i class="fas fa-check-circle me-1"></i>Paid
                </span>
            @elseif ($salePaid > 0.001 && $saleDue > 0.001)
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 mb-1" style="font-size: 10px;">
                    <i class="fas fa-clock me-1"></i>Partial
                </span>
                <div class="text-danger fw-bold" style="font-size: 11px;">Rs. {{ number_format($saleDue, 2) }}</div>
            @elseif ($saleDue > 0.001)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 mb-1" style="font-size: 10px;">
                    <i class="fas fa-exclamation-circle me-1"></i>Unpaid
                </span>
                <div class="text-danger fw-bold" style="font-size: 11px;">Rs. {{ number_format($saleDue, 2) }}</div>
            @else
                <span class="text-muted">Rs. 0.00</span>
            @endif
        </td>

        <td class="text-nowrap small text-muted">
            {{ $sale->created_at->format('d/m/Y') }}
        </td>
        <td>{!! $statusBadge !!}</td>
        <td class="pe-3 text-center">
            <div class="dropdown">
                <button class="btn btn-premium-action dropdown-toggle" type="button" data-toggle="dropdown" data-boundary="window" data-bs-boundary="window" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @can('sales.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $sale->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit (Simple)
                            </a>
                        </li>
                        {{-- <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('pos.index') }}?edit_id={{ $sale->id }}">
                                <i class="fas fa-cash-register text-success fa-fw"></i> Edit (POS Sale)
                            </a>
                        </li> --}}
                    @endcan

                    @if ($sale->sale_status === 'draft' || $sale->sale_status === 'booked')
                        @can('sales.create')
                            @if ($sale->sale_type === 'quotation')
                                <li>
                                    <a class="dropdown-item text-success d-flex align-items-center gap-2 py-2 fw-bold" href="{{ route('sales.edit', $sale->id) }}?convert_to_sale=1">
                                        <i class="fas fa-check-circle fa-fw text-success"></i> Convert to Sale
                                    </a>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('sales.confirm', $sale->id) }}" method="POST" class="confirm-booking-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-success d-flex align-items-center gap-2 py-2 fw-bold confirm-booking-btn">
                                            <i class="fas fa-check-circle fa-fw text-success"></i> 
                                            @if($sale->sale_status === 'draft')
                                                 Confirm Sale
                                            @elseif($sale->sale_status === 'booked' && !$sale->is_booking)
                                                Convert to Sale
                                            @else
                                                Confirm Booking
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endif
                        @endcan
                    @endif

                    @if ($saleDue > 0.001 && $sale->customer_id && ($sale->sale_type === 'direct_sale' || empty($sale->sale_type) || ($sale->sale_type === 'sales_order' && $sale->sale_status === 'posted')))
                        <li>
                            <a class="dropdown-item text-success d-flex align-items-center gap-2 py-2 fw-bold" href="{{ route('vouchers.create') }}?tab=payment_in&customer_id={{ $sale->customer_id }}&invoice_id={{ $sale->id }}">
                                <i class="fas fa-hand-holding-usd text-success fa-fw"></i> Receive Payment
                            </a>
                        </li>
                    @endif

                    <li><hr class="dropdown-divider"></li>

                    @can('sales.view')
                        @if ($sale->sale_type === 'direct_sale' || empty($sale->sale_type) || ($sale->sale_type === 'sales_order' && $sale->sale_status === 'posted'))
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $sale->id) }}" target="_blank">
                                    <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.receipt', $sale->id) }}" target="_blank">
                                    <i class="fas fa-receipt text-success fa-fw"></i> Receipt
                                </a>
                            </li>
                            @php
                                $directDc = $sale->deliveryChallans ? $sale->deliveryChallans->first() : null;
                            @endphp
                            @if ($directDc)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc_print', $directDc->id) }}" target="_blank">
                                        <i class="fas fa-truck text-warning fa-fw"></i> Delivery Challan
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc', $sale->id) }}" target="_blank">
                                        <i class="fas fa-truck text-warning fa-fw"></i> Delivery Challan
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc_thermal', $sale->id) }}" target="_blank">
                                    <i class="fas fa-print text-secondary fa-fw"></i> Thermal DC
                                </a>
                            </li>
                        @endif

                        @if ($sale->sale_type === 'quotation')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', ['id' => $sale->id, 'type' => 'estimate']) }}" target="_blank">
                                    <i class="fas fa-file-contract text-info fa-fw"></i> Print Quotation
                                </a>
                            </li>
                            @can('sales.create')
                                @if (!isset($hasConverted) || !$hasConverted)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-bold text-primary" href="{{ route('sales.edit', $sale->id) }}?convert_to_so=1">
                                            <i class="fas fa-random text-primary fa-fw"></i> Convert to Sales Order
                                        </a>
                                    </li>
                                @endif
                            @endcan
                        @endif

                        @php
                            $uninvoicedDc = $sale->deliveryChallans ? $sale->deliveryChallans->where('is_invoiced', 0)->first() : null;
                        @endphp

                        @if ($sale->sale_type === 'sales_order' && $uninvoicedDc)
                            @can('sales.create')
                                <li>
                                    <a class="dropdown-item text-success d-flex align-items-center gap-2 py-2 fw-bold" href="{{ route('direct-dc.index', ['highlight_dc' => $uninvoicedDc->id, 'sale_id' => $sale->id]) }}">
                                        <i class="fas fa-file-invoice-dollar fa-fw text-success"></i> Generate Partial Invoice
                                    </a>
                                </li>
                            @endcan
                        @elseif ($sale->sale_type === 'sales_order' && $sale->delivery_status === 'delivered' && $sale->sale_status !== 'posted')
                            @can('sales.create')
                                <li>
                                    <button type="button" 
                                            class="dropdown-item text-success d-flex align-items-center gap-2 py-2 fw-bold btn-open-invoice-series-modal" 
                                            data-sale-id="{{ $sale->id }}" 
                                            data-order-no="{{ $orderDocNo }}" 
                                            data-customer="{{ optional($sale->customer_relation)->customer_name ?? ($sale->walkin_name ?? 'Walk-in') }}"
                                            data-amount="{{ number_format($sale->total_net, 2) }}"
                                            data-date="{{ date('Y-m-d') }}">
                                        <i class="fas fa-file-invoice-dollar fa-fw text-success"></i> Generate Invoice
                                    </button>
                                </li>
                            @endcan
                        @endif

                        @if ($sale->sale_type === 'sales_order')
                            @can('sales.create')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.create_dc', $sale->id) }}">
                                        <i class="fas fa-truck-loading text-warning fa-fw"></i> Create Delivery Challan
                                    </a>
                                </li>
                            @endcan
                            @can('sales.view')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc_list', $sale->id) }}">
                                        <i class="fas fa-list text-info fa-fw"></i> View Delivery Challans
                                    </a>
                                </li>
                            @endcan
                        @endif
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
