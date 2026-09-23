@extends('admin_panel.layout.app')

@section('content')
    <style>
        .premium-card {
            border: 2px solid #cbd5e1 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
            background-color: #ffffff;
            margin-top: 10px;
            overflow: visible !important;
        }
        .table-responsive {
            overflow: visible !important;
            min-height: 280px;
        }
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            z-index: 1065 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
            min-width: 220px !important;
            padding: 6px !important;
            margin-top: 4px !important;
        }
        .dropdown-item {
            border-radius: 6px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
            font-weight: 500;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9 !important;
        }
        .filter-panel {
            background-color: #f8fafc !important;
            border: 2px dashed #94a3b8 !important;
            border-radius: 10px !important;
            padding: 18px !important;
        }
        .filter-panel label {
            font-size: 12px;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .filter-panel .form-control, .filter-panel .form-select {
            border: 2px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            height: 38px !important;
        }
        .btn-premium-primary {
            background-color: #2563eb !important;
            border: 2px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            height: 38px !important;
            padding: 0 16px !important;
        }
        .btn-premium-action {
            background-color: #f8fafc !important;
            border: 2px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            height: 32px !important;
            padding: 0 12px !important;
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .btn-premium-action:hover {
            background-color: #f1f5f9 !important;
            border-color: #2563eb !important;
            color: #2563eb !important;
        }
        .stat-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>

    <div class="container-fluid px-4 py-3">
        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice text-primary"></i> Purchase Orders (PO)
                </h4>
                <p class="text-muted small mb-0">Track vendor orders, receive goods (GRN), and generate purchase bills</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('direct-grn.index') }}" class="btn btn-outline-secondary fw-bold rounded-2 px-3">
                    <i class="fas fa-boxes me-1"></i> View All GRNs
                </a>
                <a href="{{ route('add_purchase') }}?type=purchase_order" class="btn btn-premium-primary shadow-sm d-flex align-items-center gap-2 px-3">
                    <i class="fas fa-plus"></i> Create Purchase Order
                </a>
            </div>
        </div>

        <!-- STATS -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="stat-card">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Total Orders</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $orders->count() }}</h4>
                    </div>
                    <div class="avatar-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Pending Receiving</span>
                        <h4 class="fw-bold text-warning mb-0">{{ $orders->where('receiving_status', 'pending')->count() }}</h4>
                    </div>
                    <div class="avatar-circle bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Partial Received</span>
                        <h4 class="fw-bold text-info mb-0">{{ $orders->where('receiving_status', 'partial')->count() }}</h4>
                    </div>
                    <div class="avatar-circle bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-truck-loading fa-lg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Fully Received</span>
                        <h4 class="fw-bold text-success mb-0">{{ $orders->where('receiving_status', 'received')->count() }}</h4>
                    </div>
                    <div class="avatar-circle bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER PANEL -->
        <div class="filter-panel mb-3">
            <form action="{{ route('purchase_orders.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <label>Vendor</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">All Vendors</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-premium-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('purchase_orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>

        <!-- TABLE CARD -->
        <div class="premium-card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="ordersTable">
                    <thead class="table-light">
                        <tr class="text-uppercase text-muted small fw-bold">
                            <th class="ps-3">Order No</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Warehouse</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-center">Receiving Status</th>
                            <th class="text-center">Linked GRNs</th>
                            <th class="text-center">Invoicing</th>
                            <th class="pe-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $po)
                            @php
                                $uninvoicedGrn = $po->goodsReceivingNotes ? $po->goodsReceivingNotes->where('is_invoiced', 0)->first() : null;
                                $allGrns = $po->goodsReceivingNotes ?: collect();
                            @endphp
                            <tr>
                                <td class="ps-3 fw-bold font-monospace text-primary">
                                    {{ $po->invoice_no ?: ('PO-' . str_pad($po->id, 4, '0', STR_PAD_LEFT)) }}
                                </td>
                                <td>{{ $po->purchase_date ? $po->purchase_date->format('d/m/Y') : '--' }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ optional($po->vendor)->name ?: 'Vendor #' . $po->vendor_id }}</span>
                                </td>
                                <td class="text-muted small">{{ optional($po->warehouse)->warehouse_name ?: 'Main Warehouse' }}</td>
                                <td class="text-end fw-bold text-dark font-monospace">Rs. {{ number_format($po->net_amount, 2) }}</td>
                                <td class="text-center">
                                    @if($po->receiving_status === 'received')
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> Fully Received
                                        </span>
                                    @elseif($po->receiving_status === 'partial')
                                        <span class="badge bg-info-subtle text-info border border-info px-2 py-1 rounded-pill fw-bold">
                                            <i class="fas fa-truck-loading me-1"></i> Partial Received
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 rounded-pill fw-bold">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($allGrns->count() > 0)
                                        <a href="{{ route('purchases.grn.index', $po->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill fw-bold" style="font-size: 11px;">
                                            <i class="fas fa-boxes me-1"></i> {{ $allGrns->count() }} GRN(s)
                                        </a>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($po->purchase_status === 'posted' && $allGrns->count() > 0 && !$uninvoicedGrn)
                                        <span class="badge bg-success text-white px-2 py-1 rounded-pill">Invoiced</span>
                                    @elseif($uninvoicedGrn)
                                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Uninvoiced GRN</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted px-2 py-1 rounded-pill">Not Billed</span>
                                    @endif
                                </td>
                                <td class="pe-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-premium-action dropdown-toggle" type="button" data-bs-toggle="dropdown" data-toggle="dropdown" data-boundary="window" data-bs-boundary="window" data-bs-display="static" data-display="static" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            @if($po->receiving_status !== 'received')
                                                <li>
                                                    <a class="dropdown-item fw-bold text-primary py-2 d-flex align-items-center gap-2" href="{{ route('purchases.grn.create', $po->id) }}">
                                                        <i class="fas fa-truck-loading fa-fw text-primary"></i> Receive Goods (GRN)
                                                    </a>
                                                </li>
                                            @endif

                                            @if($uninvoicedGrn)
                                                <li>
                                                    <a class="dropdown-item fw-bold text-success py-2 d-flex align-items-center gap-2" 
                                                       href="{{ route('direct-grn.index', ['highlight_grn' => $uninvoicedGrn->id, 'purchase_id' => $po->id]) }}">
                                                        <i class="fas fa-file-invoice-dollar fa-fw text-success"></i> Generate Purchase Bill
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('purchase.edit', $po->id) }}">
                                                    <i class="fas fa-edit fa-fw text-warning"></i> Edit Purchase Order
                                                </a>
                                            </li> --}}

                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('purchases.grn.index', $po->id) }}">
                                                    <i class="fas fa-history fa-fw text-secondary"></i> View GRNs / Order Trail
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('purchase.invoice', $po->id) }}" target="_blank">
                                                    <i class="fas fa-print fa-fw text-info"></i> Print Purchase Order
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>No Purchase Orders Found</h5>
                                    <p class="small mb-3">Start by creating your first purchase order for your vendors.</p>
                                    <a href="{{ route('add_purchase') }}?type=purchase_order" class="btn btn-premium-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i> Create Purchase Order
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
