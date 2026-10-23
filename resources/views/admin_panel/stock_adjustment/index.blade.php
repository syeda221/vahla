@extends('admin_panel.layout.app')
@section('content')

<style>
    /* ── ERP Stock Adjustment – Premium Design System ── */
    :root {
        --erp-primary:    #4f46e5;
        --erp-primary-lt: #eef2ff;
        --erp-success:    #059669;
        --erp-success-lt: #ecfdf5;
        --erp-warning:    #d97706;
        --erp-warning-lt: #fffbeb;
        --erp-danger:     #dc2626;
        --erp-danger-lt:  #fef2f2;
        --erp-info:       #0284c7;
        --erp-info-lt:    #e0f2fe;
        --erp-border:     #e2e8f0;
        --erp-bg:         #f8fafc;
        --erp-card-bg:    #ffffff;
        --erp-text:       #1e293b;
        --erp-muted:      #64748b;
        --erp-radius:     12px;
        --erp-shadow:     0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        --erp-shadow-md:  0 4px 24px rgba(0,0,0,.08);
    }

    .erp-page { background: var(--erp-bg); min-height: calc(100vh - 80px); padding: 20px 0; }
    .erp-page .container-fluid { max-width: 100%; }

    /* ── Stats Cards ── */
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
    .stat-card {
        background: var(--erp-card-bg);
        border-radius: var(--erp-radius);
        border: 1px solid var(--erp-border);
        box-shadow: var(--erp-shadow);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--erp-shadow-md); }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .stat-card .stat-label { font-size: .72rem; font-weight: 700; color: var(--erp-muted); text-transform: uppercase; letter-spacing: .5px; }
    .stat-card .stat-value { font-size: 1.35rem; font-weight: 700; color: var(--erp-text); line-height: 1.2; }
    .stat-card .stat-sub   { font-size: .72rem; color: var(--erp-muted); margin-top: 2px; }

    /* ── Main Card ── */
    .erp-card {
        background: var(--erp-card-bg);
        border-radius: var(--erp-radius);
        border: 1px solid var(--erp-border);
        box-shadow: var(--erp-shadow);
        overflow: hidden;
    }
    .erp-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--erp-border);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .erp-card-header .page-title { font-size: 1.05rem; font-weight: 700; color: var(--erp-text); margin: 0; }
    .erp-card-header .page-sub   { font-size: .76rem; color: var(--erp-muted); margin: 0; }

    /* Header action buttons */
    .btn-hdr {
        border-radius: 8px; padding: 7px 14px; font-size: .8rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent;
        transition: all .15s ease; cursor: pointer; text-decoration: none;
    }
    .btn-hdr-primary { background: var(--erp-primary); color: #fff; border-color: var(--erp-primary); }
    .btn-hdr-primary:hover { background: #4338ca; color: #fff; transform: translateY(-1px); }

    /* ── Filter Panel ── */
    .filter-panel { padding: 14px 20px; background: var(--erp-bg); border-bottom: 1px solid var(--erp-border); }
    .filter-panel .filter-heading { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--erp-muted); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }

    .erp-filter-row { display: flex; align-items: flex-end; flex-wrap: wrap; gap: 10px; width: 100%; }
    .erp-filter-field { display: flex; flex-direction: column; flex: 1 1 150px; min-width: 140px; max-width: 220px; box-sizing: border-box; }
    .erp-filter-search { flex: 1 1 200px; max-width: 280px; }
    .erp-filter-btns   { flex: 0 0 auto; max-width: none; min-width: 0; }

    .erp-flabel { display: block; font-size: .69rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--erp-muted); margin-bottom: 5px; white-space: nowrap; }
    .erp-finput { width: 100%; height: 35px; padding: 0 10px; border: 1px solid var(--erp-border); border-radius: 7px; font-size: .82rem; color: var(--erp-text); background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
    .erp-finput:focus { border-color: var(--erp-primary); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }

    .search-wrap { position: relative; }
    .search-wrap .search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--erp-muted); font-size: 12px; pointer-events: none; z-index: 1; }
    .search-wrap .erp-finput { padding-left: 30px; }

    .btn-erp-filter { background: var(--erp-primary); color: #fff; border: none; border-radius: 7px; height: 35px; padding: 0 14px; font-size: .8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: background .15s; }
    .btn-erp-filter:hover { background: #4338ca; }
    .btn-erp-clear { background: #fff; color: var(--erp-muted); border: 1px solid var(--erp-border); border-radius: 7px; height: 35px; padding: 0 12px; font-size: .8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; text-decoration: none; transition: background .15s; }
    .btn-erp-clear:hover { background: #f1f5f9; color: var(--erp-text); }

    /* ── Table ── */
    .erp-table-wrap { padding: 0; overflow-x: auto; }
    #adjustmentTable { width: 100% !important; border-collapse: collapse !important; font-size: .82rem; }
    #adjustmentTable thead th {
        background: #f8fafc !important; color: #475569 !important; font-weight: 700;
        text-transform: uppercase; font-size: .67rem; letter-spacing: .5px;
        padding: 10px 14px; border-bottom: 2px solid var(--erp-border) !important;
        white-space: nowrap; position: sticky; top: 0; z-index: 2;
    }
    #adjustmentTable tbody td { padding: 9px 14px; border-bottom: 1px solid #f1f5f9 !important; color: var(--erp-text); vertical-align: middle; white-space: nowrap; }
    #adjustmentTable tbody tr:hover { background: #f8fafc !important; }

    /* Type badges */
    .badge-add      { background: var(--erp-success-lt); color: var(--erp-success); border: 1px solid #a7f3d0; border-radius: 20px; padding: 2px 9px; font-size: .72rem; font-weight: 700; }
    .badge-subtract { background: var(--erp-danger-lt);  color: var(--erp-danger);  border: 1px solid #fecaca; border-radius: 20px; padding: 2px 9px; font-size: .72rem; font-weight: 700; }
    .badge-set      { background: var(--erp-info-lt);    color: var(--erp-info);    border: 1px solid #bae6fd; border-radius: 20px; padding: 2px 9px; font-size: .72rem; font-weight: 700; }

    /* Stock movement flow indicator */
    .stock-flow { display: inline-flex; align-items: center; gap: 6px; font-size: .8rem; font-weight: 600; }
    .stock-old  { color: var(--erp-muted); text-decoration: line-through; }
    .stock-arrow{ color: var(--erp-muted); font-size: .7rem; }
    .stock-new  { color: var(--erp-text); font-weight: 700; }

    /* POS-Style Variant Card Controls */
    .pos-variant-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: border-color .15s;
    }
    .pos-variant-card:hover { border-color: var(--erp-primary); }
    .pos-variant-title { font-weight: 700; font-size: .86rem; color: var(--erp-text); }
    .pos-variant-meta  { font-size: .74rem; color: var(--erp-muted); display: flex; gap: 8px; margin-top: 2px; }
    .pos-stock-badge   { background: var(--erp-primary-lt); color: var(--erp-primary); border-radius: 6px; padding: 2px 8px; font-weight: 700; font-size: .74rem; }

    /* Touch Stepper controls */
    .stepper-wrap { display: flex; align-items: center; gap: 4px; }
    .btn-stepper {
        width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--erp-border);
        background: #f8fafc; color: var(--erp-text); font-weight: 700; font-size: 14px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        user-select: none; transition: all .12s;
    }
    .btn-stepper:active { transform: scale(0.92); }
    .btn-stepper-plus  { background: var(--erp-success-lt); color: var(--erp-success); border-color: #a7f3d0; }
    .btn-stepper-minus { background: var(--erp-danger-lt);  color: var(--erp-danger);  border-color: #fecaca; }

    .stepper-input {
        width: 60px; height: 32px; text-align: center; font-weight: 700; font-size: .85rem;
        border: 1px solid var(--erp-border); border-radius: 6px; outline: none;
    }

    /* Batch cart table */
    .batch-cart-table { font-size: .8rem; }
    .batch-cart-table th { background: #f8fafc; font-size: .68rem; text-transform: uppercase; color: var(--erp-muted); padding: 8px 10px; }
    .batch-cart-table td { padding: 8px 10px; vertical-align: middle; }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; }
        .erp-card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
        .erp-filter-field, .erp-filter-search { flex: 1 1 100%; max-width: 100%; }
        #adjustmentTable { min-width: 750px !important; }
        .pos-variant-card { flex-direction: column; align-items: flex-start; }
        .stepper-wrap { width: 100%; justify-content: space-between; margin-top: 8px; }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-3 py-3">

    {{-- ── Stats Cards Row ── --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-sliders-h"></i></div>
            <div>
                <div class="stat-label">Total Adjustments</div>
                <div class="stat-value">{{ number_format($totalAdjustments) }}</div>
                <div class="stat-sub">recorded in system</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ecfdf5; color:#059669;"><i class="fas fa-plus-circle"></i></div>
            <div>
                <div class="stat-label">Stock Added (+)</div>
                <div class="stat-value">{{ number_format($totalAdded) }}</div>
                <div class="stat-sub">total pieces added</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2; color:#dc2626;"><i class="fas fa-minus-circle"></i></div>
            <div>
                <div class="stat-label">Stock Deducted (-)</div>
                <div class="stat-value">{{ number_format($totalSubtracted) }}</div>
                <div class="stat-sub">total pieces reduced</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <div class="stat-label">This Month</div>
                <div class="stat-value">{{ number_format($thisMonthCount) }}</div>
                <div class="stat-sub">adjustments done</div>
            </div>
        </div>
    </div>

    {{-- ── Main ERP Card ── --}}
    <div class="erp-card">

        {{-- Card Header --}}
        <div class="erp-card-header">
            <div>
                <p class="page-title"><i class="fas fa-boxes-stacked me-2" style="color:var(--erp-primary);"></i>Stock Adjustment System</p>
                <p class="page-sub">Quickly correct stock balances for products &amp; multiple variants in one single transaction</p>
            </div>
            <div>
                <a href="{{ route('stock_adjustments.create') }}" class="btn-hdr btn-hdr-primary">
                    <i class="fas fa-cash-register me-1"></i> Quick Multi-Variant Stock Adjustment
                </a>
            </div>
        </div>

        {{-- ── Filter Panel ── --}}
        <div class="filter-panel">
            <div class="filter-heading"><i class="fas fa-filter"></i> Filters &amp; Search</div>
            <form method="GET" action="{{ route('stock_adjustments.index') }}" id="filterForm">
                <div class="erp-filter-row">

                    {{-- Search --}}
                    <div class="erp-filter-field erp-filter-search">
                        <label class="erp-flabel">Search Product / Variant</label>
                        <div class="search-wrap">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="erp-finput" placeholder="Product name, code, variant…">
                        </div>
                    </div>

                    {{-- Warehouse (Only permitted warehouses) --}}
                    <div class="erp-filter-field">
                        <label class="erp-flabel">Warehouse</label>
                        <select name="warehouse_id" class="erp-finput">
                            <option value="">All Permitted Warehouses</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name ?? $wh->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div class="erp-filter-field" style="min-width:120px;">
                        <label class="erp-flabel">Type</label>
                        <select name="type" class="erp-finput">
                            <option value="">All Types</option>
                            <option value="add"      {{ request('type') === 'add'      ? 'selected' : '' }}>🟢 Add (+)</option>
                            <option value="subtract" {{ request('type') === 'subtract' ? 'selected' : '' }}>🔴 Deduct (-)</option>
                            <option value="set"      {{ request('type') === 'set'      ? 'selected' : '' }}>🔵 Set (=)</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="erp-filter-field" style="min-width:130px;">
                        <label class="erp-flabel">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="erp-finput">
                    </div>

                    {{-- Date To --}}
                    <div class="erp-filter-field" style="min-width:130px;">
                        <label class="erp-flabel">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="erp-finput">
                    </div>

                    {{-- Buttons --}}
                    <div class="erp-filter-field erp-filter-btns">
                        <label class="erp-flabel">&nbsp;</label>
                        <div style="display:flex; gap:6px;">
                            <button type="submit" class="btn-erp-filter">
                                <i class="fas fa-search"></i> Apply
                            </button>
                            <a href="{{ route('stock_adjustments.index') }}" class="btn-erp-clear">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- ── Audit Log Table ── --}}
        <div class="erp-table-wrap table-responsive">
            <table id="adjustmentTable" class="table table-hover align-middle nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th style="width:130px;">Date &amp; Time</th>
                        <th>Adjusted By</th>
                        <th>Warehouse</th>
                        <th>Product Details</th>
                        <th>Variant (If Any)</th>
                        <th style="width:100px;">Type</th>
                        <th style="width:90px;">Qty</th>
                        <th>Stock Change (Old &rarr; New)</th>
                        <th>Reason / Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $key => $adj)
                        <tr>
                            <td style="color:var(--erp-muted); font-size:.72rem;">{{ $adjustments->firstItem() + $key }}</td>
                            <td>
                                <div>{{ $adj->created_at ? $adj->created_at->format('d M Y') : '-' }}</div>
                                <small style="color:var(--erp-muted); font-size:.7rem;">{{ $adj->created_at ? $adj->created_at->format('h:i A') : '' }}</small>
                            </td>
                            <td class="fw-semibold">{{ $adj->user->name ?? 'System' }}</td>
                            <td>
                                <span style="background:#f1f5f9; border-radius:5px; padding:2px 7px; font-size:.75rem; font-weight:600; color:#475569;">
                                    <i class="fas fa-warehouse me-1" style="font-size:.65rem;"></i> {{ $adj->warehouse->name ?? $adj->warehouse->warehouse_name ?? 'Warehouse #'.$adj->warehouse_id }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:.84rem;">{{ $adj->product->item_name ?? 'Product #'.$adj->product_id }}</div>
                                <code style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:1px 5px; font-size:.7rem; color:#334155;">
                                    {{ $adj->product->item_code ?? '' }}
                                </code>
                            </td>
                            <td>
                                @if($adj->variant_name)
                                    <span style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; border-radius:6px; padding:3px 8px; font-size:.75rem; font-weight:600;">
                                        <i class="fas fa-tags me-1" style="font-size:.65rem;"></i> {{ $adj->variant_name }}
                                    </span>
                                @else
                                    <span style="color:#94a3b8; font-size:.75rem;">— Standard Product —</span>
                                @endif
                            </td>
                            <td>
                                @if($adj->type === 'add')
                                    <span class="badge-add"><i class="fas fa-plus me-1"></i> Add</span>
                                @elseif($adj->type === 'subtract')
                                    <span class="badge-subtract"><i class="fas fa-minus me-1"></i> Deduct</span>
                                @else
                                    <span class="badge-set"><i class="fas fa-equals me-1"></i> Set</span>
                                @endif
                            </td>
                            <td class="fw-bold" style="font-size:.85rem;">
                                @php
                                    $p = $adj->product;
                                    $ppb = $p ? (float)($p->pieces_per_box > 0 ? $p->pieces_per_box : 1) : 1;
                                    $vUnit = null;
                                    if ($p && $p->color) {
                                        $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                                        if (is_array($parsed)) {
                                            foreach ($parsed as $dv) {
                                                if (($adj->variant_name && ($dv['name'] ?? '') === $adj->variant_name) ||
                                                    ($adj->variant_key && (($dv['name'] ?? '').'|'.($dv['size'] ?? '-').'|'.($dv['color'] ?? '-')) === $adj->variant_key)) {
                                                    if (!empty($dv['conv_factor']) && (float)$dv['conv_factor'] > 0) $ppb = (float)$dv['conv_factor'];
                                                    $vUnit = $dv['unit'] ?? null;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    $isCarton = $p && in_array($p->size_mode, ['by_cartons', 'by_bandal']) && $ppb > 1;
                                    $ctnLbl = ($p && $p->size_mode === 'by_bandal') ? 'Bndl' : 'Ctn';
                                @endphp

                                @if($isCarton)
                                    @php
                                        $qtyCtns = floor($adj->qty / $ppb);
                                        $qtyLoose = round(fmod($adj->qty, $ppb));
                                        $qtyText = ($qtyCtns > 0 && $qtyLoose > 0) ? "{$qtyCtns} {$ctnLbl} + {$qtyLoose} Pcs" : (($qtyCtns > 0) ? "{$qtyCtns} {$ctnLbl}" : "{$qtyLoose} Pcs");
                                    @endphp
                                    <span style="color:#0f172a;">{{ $qtyText }}</span>
                                    <div class="text-muted font-monospace" style="font-size:.70rem; font-weight:normal;">({{ number_format($adj->qty, 0) }} Pcs)</div>
                                @else
                                    <span>{{ number_format($adj->qty, (fmod($adj->qty, 1) !== 0.0 ? 2 : 0)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($isCarton)
                                    @php
                                        $oldCtns = floor($adj->old_stock / $ppb);
                                        $oldLoose = round(fmod($adj->old_stock, $ppb));
                                        $oldText = ($oldCtns > 0 && $oldLoose > 0) ? "{$oldCtns} {$ctnLbl} + {$oldLoose} Pcs" : (($oldCtns > 0) ? "{$oldCtns} {$ctnLbl}" : "{$oldLoose} Pcs");

                                        $newCtns = floor($adj->new_stock / $ppb);
                                        $newLoose = round(fmod($adj->new_stock, $ppb));
                                        $newText = ($newCtns > 0 && $newLoose > 0) ? "{$newCtns} {$ctnLbl} + {$newLoose} Pcs" : (($newCtns > 0) ? "{$newCtns} {$ctnLbl}" : "{$newLoose} Pcs");
                                    @endphp
                                    <div class="stock-flow d-flex align-items-center gap-1">
                                        <span class="stock-old fw-bold">{{ $oldText }}</span>
                                        <i class="fas fa-long-arrow-alt-right stock-arrow text-muted mx-1"></i>
                                        <span class="stock-new fw-bold text-success">{{ $newText }}</span>
                                    </div>
                                    <div class="text-muted font-monospace mt-1" style="font-size:.68rem;">
                                        ({{ number_format($adj->old_stock, 0) }} &rarr; {{ number_format($adj->new_stock, 0) }} Pcs)
                                    </div>
                                @else
                                    <div class="stock-flow">
                                        <span class="stock-old">{{ number_format($adj->old_stock, (fmod($adj->old_stock, 1) !== 0.0 ? 2 : 0)) }}</span>
                                        <i class="fas fa-long-arrow-alt-right stock-arrow"></i>
                                        <span class="stock-new">{{ number_format($adj->new_stock, (fmod($adj->new_stock, 1) !== 0.0 ? 2 : 0)) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td style="max-width:240px; white-space:normal;">
                                <span style="font-size:.78rem; color:#334155;">{{ $adj->reason }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-2" style="color:#cbd5e1;"></i>
                                <p class="mb-0">No stock adjustments recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Pagination ── --}}
        <div class="erp-pagination d-flex align-items-center justify-content-between p-3 border-top">
            <span class="showing" style="font-size:.78rem; color:var(--erp-muted);">
                Showing <strong>{{ $adjustments->firstItem() ?? 0 }}–{{ $adjustments->lastItem() ?? 0 }}</strong>
                of <strong>{{ $adjustments->total() }}</strong> records
            </span>
            {{ $adjustments->appends(request()->query())->links() }}
        </div>

    </div>
        </div>{{-- /container-fluid --}}
    </div>{{-- /main-content-inner --}}
</div>{{-- /main-content --}}

{{-- ══════════════════════════════════════════════════════════════
     POS-STYLE MULTI-VARIANT BATCH ADJUSTMENT MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="batchAdjustmentModal" tabindex="-1" aria-labelledby="batchAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
            
            <div class="modal-header border-bottom bg-white px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="batchAdjustmentModalLabel">
                        <i class="fas fa-layer-group me-2" style="color:var(--erp-primary);"></i>Quick Multi-Variant Stock Adjustment
                    </h5>
                    <small class="text-muted">Select warehouse, search products, adjust multiple variants at once (POS Style), and submit in batch!</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="background:#f8fafc;">

                {{-- 1. Warehouse Selection --}}
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-1">
                            Target Warehouse <span class="text-danger">*</span>
                        </label>
                        <select id="batch_warehouse_id" class="form-select" style="border-radius:8px; height:42px; font-weight:600;">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name ?? $wh->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Product Search Picker --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small text-uppercase mb-1">
                            Search &amp; Pick Product <span class="text-danger">*</span>
                        </label>
                        <select id="batch_product_search" class="form-select" style="border-radius:8px; height:42px;">
                            <option value="">-- Type or Select Product (e.g. Trouser Profile) --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->item_name }} ({{ $p->item_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 3. POS Variant Picker Drawer / Container --}}
                <div id="posVariantDrawer" class="mb-4 d-none">
                    <div class="p-3 bg-white rounded-3 border" style="border-color:#cbd5e1 !important; box-shadow: 0 4px 12px rgba(0,0,0,.04);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-primary" style="font-size:.9rem;">
                                <i class="fas fa-box-open me-1"></i> <span id="posProductName">Product Variants</span>
                            </span>
                            <span id="posTotalStockBadge" class="badge bg-secondary">Total Stock: 0 Pcs</span>
                        </div>

                        {{-- Variant Cards Container --}}
                        <div id="posVariantsList" class="my-2" style="max-height:280px; overflow-y:auto;">
                            {{-- Dynamically populated POS Variant cards --}}
                        </div>

                        <div class="d-flex justify-content-end mt-2 pt-2 border-top">
                            <button type="button" id="addVariantsToCartBtn" class="btn btn-success btn-sm px-4 fw-bold">
                                <i class="fas fa-plus me-1"></i> Add Selected Adjustments to Batch List
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 4. Staged Batch Adjustment Cart Table --}}
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white py-2 px-3 d-flex align-items-center justify-content-between border-bottom">
                        <span class="fw-bold text-dark small text-uppercase">
                            <i class="fas fa-shopping-cart me-1 text-primary"></i> Staged Adjustments Batch (<span id="batchItemCount">0</span> items)
                        </span>
                        <button type="button" id="clearBatchBtn" class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                            <i class="fas fa-trash-alt me-1"></i> Clear All
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 batch-cart-table text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Details</th>
                                    <th>Variant</th>
                                    <th>Current Stock</th>
                                    <th>Action &amp; Qty</th>
                                    <th>Expected New Stock</th>
                                    <th>Item Remarks</th>
                                    <th class="text-end">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="batchCartBody">
                                <tr id="emptyCartRow">
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-cart-plus fa-2x mb-2" style="color:#cbd5e1;"></i>
                                        <p class="mb-0">No items added to batch adjustment yet. Search a product above and set variant adjustments!</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. Batch Remarks --}}
                <div class="mt-3">
                    <label class="form-label fw-bold text-secondary small text-uppercase mb-1">Batch Adjustment Reason / Remarks <span class="text-danger">*</span></label>
                    <textarea id="batch_global_reason" rows="2" class="form-control" style="border-radius:8px;" placeholder="Specify reason for this batch adjustment (e.g. 'Variant correction for Trouser Profile sale', 'Physical inventory recount')" required></textarea>
                </div>

            </div>

            <div class="modal-footer bg-white py-3 px-4">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                <button type="button" id="submitBatchBtn" class="btn btn-primary px-4 fw-bold" disabled>
                    <i class="fas fa-save me-1"></i> Save Batch Adjustment (<span id="btnBatchCount">0</span>)
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function () {

    // Global Staged Batch Cart Array
    let batchCart = [];

    // Open Batch Modal
    $('#openBatchModalBtn').on('click', function () {
        batchCart = [];
        renderBatchCart();
        $('#batch_product_search').val('');
        $('#posVariantDrawer').addClass('d-none');
        $('#batch_global_reason').val('');
        $('#batchAdjustmentModal').modal('show');
    });

    // When Product is selected in Search -> Fetch Product & Variants for POS Drawer
    $('#batch_product_search, #batch_warehouse_id').on('change', function () {
        let productId   = $('#batch_product_search').val();
        let warehouseId = $('#batch_warehouse_id').val();

        if (!productId) {
            $('#posVariantDrawer').addClass('d-none');
            return;
        }

        $.ajax({
            url: "/stock-adjustments/product-variants/" + productId,
            type: "GET",
            data: { warehouse_id: warehouseId },
            success: function (res) {
                if (!res.success) return;

                $('#posProductName').text(res.product_name + ' (' + res.item_code + ')');
                $('#posTotalStockBadge').text('Total Stock: ' + res.total_stock + ' Pcs');

                let container = $('#posVariantsList');
                container.empty();

                if (res.has_variants && res.variants.length > 0) {
                    $.each(res.variants, function (i, v) {
                        let cardHtml = `
                        <div class="pos-variant-card" data-vkey="${v.variant_key}" data-vname="${v.name}" data-stock="${v.current_stock}" data-pid="${res.product_id}" data-pname="${res.product_name}">
                            <div>
                                <div class="pos-variant-title">${v.name}</div>
                                <div class="pos-variant-meta">
                                    <span>Size: <strong>${v.size}</strong></span>
                                    <span>Color: <strong>${v.color}</strong></span>
                                    <span class="pos-stock-badge">Current Stock: ${v.current_stock} Pcs</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm pos-action-type" style="width:110px; height:32px; font-weight:600; font-size:.78rem;">
                                    <option value="add">🟢 Add (+)</option>
                                    <option value="subtract">🔴 Deduct (-)</option>
                                    <option value="set">🔵 Set (=)</option>
                                </select>
                                <div class="stepper-wrap">
                                    <button type="button" class="btn-stepper btn-stepper-minus pos-step-minus" title="Decrease Qty">-</button>
                                    <input type="number" step="1" min="0" value="0" class="stepper-input pos-qty-input">
                                    <button type="button" class="btn-stepper btn-stepper-plus pos-step-plus" title="Increase Qty">+</button>
                                </div>
                            </div>
                        </div>`;
                        container.append(cardHtml);
                    });
                } else {
                    // Standard single product
                    let cardHtml = `
                    <div class="pos-variant-card" data-vkey="" data-vname="" data-stock="${res.total_stock}" data-pid="${res.product_id}" data-pname="${res.product_name}">
                        <div>
                            <div class="pos-variant-title">${res.product_name} (${res.item_code})</div>
                            <div class="pos-variant-meta">
                                <span class="pos-stock-badge">Current Warehouse Stock: ${res.total_stock} Pcs</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select form-select-sm pos-action-type" style="width:110px; height:32px; font-weight:600; font-size:.78rem;">
                                <option value="add">🟢 Add (+)</option>
                                <option value="subtract">🔴 Deduct (-)</option>
                                <option value="set">🔵 Set (=)</option>
                            </select>
                            <div class="stepper-wrap">
                                <button type="button" class="btn-stepper btn-stepper-minus pos-step-minus">-</button>
                                <input type="number" step="1" min="0" value="0" class="stepper-input pos-qty-input">
                                <button type="button" class="btn-stepper btn-stepper-plus pos-step-plus">+</button>
                            </div>
                        </div>
                    </div>`;
                    container.append(cardHtml);
                }

                $('#posVariantDrawer').removeClass('d-none');
            }
        });
    });

    // Stepper + / - Click Handlers
    $(document).on('click', '.pos-step-plus', function () {
        let input = $(this).siblings('.pos-qty-input');
        let val = parseFloat(input.val()) || 0;
        input.val(val + 1);
    });

    $(document).on('click', '.pos-step-minus', function () {
        let input = $(this).siblings('.pos-qty-input');
        let val = parseFloat(input.val()) || 0;
        if (val > 0) input.val(val - 1);
    });

    // Add Selected POS Variant Adjustments to Batch Cart
    $('#addVariantsToCartBtn').on('click', function () {
        let addedCount = 0;

        $('.pos-variant-card').each(function () {
            let card     = $(this);
            let qty      = parseFloat(card.find('.pos-qty-input').val()) || 0;
            let type     = card.find('.pos-action-type').val();
            let productId= card.data('pid');
            let productName = card.data('pname');
            let vKey     = card.data('vkey') || '';
            let vName    = card.data('vname') || '';
            let curStock = parseFloat(card.data('stock')) || 0;

            if (qty > 0) {
                // Calculate expected stock
                let expStock = curStock;
                if (type === 'add') expStock = curStock + qty;
                else if (type === 'subtract') expStock = Math.max(0, curStock - qty);
                else expStock = Math.max(0, qty);

                // Add or update in batchCart
                let itemIndex = batchCart.findIndex(item => item.product_id == productId && item.variant_key == vKey);
                let cartItem = {
                    product_id:   productId,
                    product_name: productName,
                    variant_key:  vKey,
                    variant_name: vName,
                    type:         type,
                    qty:          qty,
                    cur_stock:    curStock,
                    exp_stock:    expStock,
                    reason:       ''
                };

                if (itemIndex > -1) {
                    batchCart[itemIndex] = cartItem;
                } else {
                    batchCart.push(cartItem);
                }
                addedCount++;
            }
        });

        if (addedCount > 0) {
            renderBatchCart();
            $('#posVariantDrawer').addClass('d-none');
            $('#batch_product_search').val('');
            Swal.fire({ toast:true, position:'top-end', icon:'success', title: addedCount + ' variant adjustment(s) added to batch!', showConfirmButton:false, timer:1500 });
        } else {
            Swal.fire('No Quantity Set', 'Please set an adjustment quantity (> 0) on at least one variant.', 'info');
        }
    });

    // Render Batch Cart Table
    function renderBatchCart() {
        let tbody = $('#batchCartBody');
        tbody.empty();

        if (batchCart.length === 0) {
            tbody.append(`
            <tr id="emptyCartRow">
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-cart-plus fa-2x mb-2" style="color:#cbd5e1;"></i>
                    <p class="mb-0">No items added to batch adjustment yet. Search a product above and set variant adjustments!</p>
                </td>
            </tr>`);
            $('#submitBatchBtn').prop('disabled', true);
            $('#batchItemCount, #btnBatchCount').text('0');
            return;
        }

        $.each(batchCart, function (index, item) {
            let typeBadge = '';
            if (item.type === 'add') typeBadge = '<span class="badge-add">+ Add</span>';
            else if (item.type === 'subtract') typeBadge = '<span class="badge-subtract">- Deduct</span>';
            else typeBadge = '<span class="badge-set">= Set</span>';

            let rowHtml = `
            <tr>
                <td>${index + 1}</td>
                <td class="fw-semibold">${item.product_name}</td>
                <td>${item.variant_name ? `<span class="badge bg-light text-dark border">${item.variant_name}</span>` : '<span class="text-muted">—</span>'}</td>
                <td>${item.cur_stock} Pcs</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        ${typeBadge}
                        <strong class="fs-6">${item.qty}</strong> Pcs
                    </div>
                </td>
                <td class="fw-bold text-success">${item.exp_stock} Pcs</td>
                <td>
                    <input type="text" class="form-control form-control-sm item-reason-input" data-index="${index}" value="${item.reason}" placeholder="Optional custom note...">
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-cart-item" data-index="${index}" style="padding:2px 8px;">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;
            tbody.append(rowHtml);
        });

        $('#batchItemCount, #btnBatchCount').text(batchCart.length);
        $('#submitBatchBtn').prop('disabled', false);
    }

    // Custom Reason change in Cart
    $(document).on('input', '.item-reason-input', function () {
        let idx = $(this).data('index');
        if (batchCart[idx]) {
            batchCart[idx].reason = $(this).val();
        }
    });

    // Remove single Item from Cart
    $(document).on('click', '.remove-cart-item', function () {
        let idx = $(this).data('index');
        batchCart.splice(idx, 1);
        renderBatchCart();
    });

    // Clear Batch Cart
    $('#clearBatchBtn').on('click', function () {
        batchCart = [];
        renderBatchCart();
    });

    // Submit Batch Adjustment AJAX
    $('#submitBatchBtn').on('click', function () {
        let warehouseId  = $('#batch_warehouse_id').val();
        let globalReason = $('#batch_global_reason').val();

        if (batchCart.length === 0) {
            Swal.fire('Empty Batch', 'Please add at least one item to the batch.', 'warning');
            return;
        }

        if (!globalReason) {
            Swal.fire('Reason Required', 'Please enter a reason/remarks for this batch adjustment.', 'warning');
            $('#batch_global_reason').focus();
            return;
        }

        let btn = $(this);
        let origHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Saving Batch...').prop('disabled', true);

        $.ajax({
            url: "{{ route('stock_adjustments.store_batch') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                warehouse_id: warehouseId,
                global_reason: globalReason,
                items: batchCart
            },
            success: function (res) {
                if (res.success) {
                    $('#batchAdjustmentModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Batch Saved!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message || 'Failed to save batch adjustment.', 'error');
                    btn.html(origHtml).prop('disabled', false);
                }
            },
            error: function (xhr) {
                btn.html(origHtml).prop('disabled', false);
                let msg = xhr.responseJSON?.message || 'Server error occurred.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

});
</script>
@endsection
