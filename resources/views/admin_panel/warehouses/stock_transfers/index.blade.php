@extends('admin_panel.layout.app')

@section('content')
<div class="erp-container py-4">
    <!-- Top Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('warehouse_stocks.index') }}" class="text-decoration-none erp-caption fw-semibold">
                    <i class="fas fa-warehouse me-1"></i> Warehouse Stocks
                </a>
                <span class="text-muted small">/</span>
                <span class="erp-caption fw-semibold text-primary">Stock Transfers</span>
            </div>
            <h1 class="erp-page-title mb-1">Stock Transfers History</h1>
            <p class="erp-body-text mb-0">Audit log of all stock movements between godowns and branch shops</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('stock.transfer.create')
                <!-- Sole Primary Button in this area -->
                <a href="{{ route('stock_transfers.create') }}" class="erp-btn-primary">
                    <i class="fas fa-plus"></i> New Stock Transfer
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--success) !important;">
            <i class="fas fa-check-circle me-3 text-success fs-5"></i>
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show erp-card p-3 d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid var(--danger) !important;">
            <i class="fas fa-exclamation-triangle me-3 text-danger fs-5"></i>
            <div>
                <strong>Notice:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top KPI Cards -->
    @php
        $qtySumFmt = rtrim(rtrim(number_format((float)($totalQuantitySum ?? 0), 2, '.', ','), '0'), '.');
        if ($qtySumFmt === '' || $qtySumFmt === '-0') $qtySumFmt = '0';
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="erp-kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="erp-kpi-label mb-1">Total Transfers</div>
                        <div class="erp-kpi-value font-tabular">{{ number_format($totalTransfersCount ?? 0) }}</div>
                        <div class="erp-kpi-subtext">Recorded dispatch logs</div>
                    </div>
                    <div class="erp-kpi-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="erp-kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="erp-kpi-label mb-1">Total Qty Moved</div>
                        <div class="erp-kpi-value font-tabular">{{ $qtySumFmt }}</div>
                        <div class="erp-kpi-subtext">Cumulative units transferred</div>
                    </div>
                    <div class="erp-kpi-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="erp-kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="erp-kpi-label mb-1">To Main Shop</div>
                        <div class="erp-kpi-value font-tabular">{{ number_format($toShopCount ?? 0) }}</div>
                        <div class="erp-kpi-subtext">Shop replenishment transfers</div>
                    </div>
                    <div class="erp-kpi-icon">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="erp-kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="erp-kpi-label mb-1">Inter-Godown</div>
                        <div class="erp-kpi-value font-tabular">{{ number_format($toGodownCount ?? 0) }}</div>
                        <div class="erp-kpi-subtext">Storage relocations</div>
                    </div>
                    <div class="erp-kpi-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="erp-card mb-4">
        <div class="p-3">
            <form method="GET" action="{{ route('stock_transfers.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <select name="from_warehouse_id" class="form-select erp-input" onchange="this.form.submit()">
                        <option value="">-- From: All Locations --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (string)($fromWh ?? '') === (string)$w->id ? 'selected' : '' }}>
                                {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="to_warehouse_id" class="form-select erp-input" onchange="this.form.submit()">
                        <option value="">-- To: All Destinations --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ (string)($toWh ?? '') === (string)$w->id ? 'selected' : '' }}>
                                {{ $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $w->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control erp-input" placeholder="Search product, code, remarks...">
                        <button class="erp-btn-primary flex-shrink-0" type="submit">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-2 d-flex justify-content-md-end">
                    @if(!empty($fromWh) || !empty($toWh) || !empty($search))
                        <a href="{{ route('stock_transfers.index') }}" class="erp-btn-ghost w-100 text-danger">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="erp-card overflow-hidden">
        <div class="table-responsive">
            <table class="table erp-table mb-0" id="transfersTable">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 15%;">DATE / TIME</th>
                        <th style="width: 20%;">FROM (SOURCE)</th>
                        <th style="width: 20%;">TO (DESTINATION)</th>
                        <th style="width: 25%;">PRODUCT / ITEM</th>
                        <th class="text-center" style="width: 15%;">QUANTITY</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        @php
                            $qtyVal = (float)$transfer->quantity;
                            $fmtQty = rtrim(rtrim(number_format($qtyVal, 2, '.', ','), '0'), '.');
                            if ($fmtQty === '' || $fmtQty === '-0') $fmtQty = '0';
                            $unit = $transfer->product->unit_name ?? ($transfer->product->unit->name ?? 'Kg');
                            $itemName = $transfer->product->item_name ?? 'N/A';
                            $firstLetter = strtoupper(substr(trim($itemName), 0, 1)) ?: 'P';
                        @endphp
                        <tr>
                            <td class="erp-caption font-tabular">
                                {{ $transfers->firstItem() ? ($transfers->firstItem() + $loop->index) : $loop->iteration }}
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $transfer->created_at ? $transfer->created_at->format('d M, Y') : '—' }}</div>
                                <div class="erp-caption">{{ $transfer->created_at ? $transfer->created_at->format('h:i A') : '' }}</div>
                            </td>
                            <td>
                                @if($transfer->fromWarehouse)
                                    <span class="erp-badge {{ $transfer->fromWarehouse->is_shop ? 'erp-badge-success' : 'erp-badge-info' }}">
                                        <i class="fas {{ $transfer->fromWarehouse->is_shop ? 'fa-store' : 'fa-warehouse' }}"></i>
                                        {{ $transfer->fromWarehouse->warehouse_name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($transfer->to_shop || ($transfer->toWarehouse && $transfer->toWarehouse->is_shop))
                                    <span class="erp-badge erp-badge-success">
                                        <i class="fas fa-store"></i>
                                        {{ $transfer->toWarehouse ? $transfer->toWarehouse->warehouse_name : 'Main Shop' }}
                                    </span>
                                @elseif($transfer->toWarehouse)
                                    <span class="erp-badge erp-badge-info">
                                        <i class="fas fa-warehouse"></i>
                                        {{ $transfer->toWarehouse->warehouse_name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($transfer->product && $transfer->product->image)
                                        <img src="{{ asset('uploads/products/'.$transfer->product->image) }}" class="rounded me-3 flex-shrink-0" style="width: 36px; height: 36px; object-fit: cover; border: 1px solid var(--border-color);" alt="">
                                    @else
                                        <div class="prod-letter-icon me-3">
                                            {{ $firstLetter }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium text-dark">{{ $itemName }}</div>
                                        <div class="d-flex align-items-center gap-1 mt-0.5 flex-wrap">
                                            @if($transfer->product && $transfer->product->item_code)
                                                <span class="erp-code-badge">{{ $transfer->product->item_code }}</span>
                                            @endif
                                            @if($transfer->remarks)
                                                <span class="erp-caption text-secondary">{{ $transfer->remarks }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="erp-qty-cell">
                                    <span>{{ $fmtQty }}</span>
                                    <span class="erp-qty-unit">{{ $unit }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <p class="erp-body-text mb-3">No stock transfers recorded yet.</p>
                                @can('stock.transfer.create')
                                    <a href="{{ route('stock_transfers.create') }}" class="erp-btn-primary erp-btn-sm">
                                        <i class="fas fa-plus"></i> Create New Transfer
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $transfers->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
