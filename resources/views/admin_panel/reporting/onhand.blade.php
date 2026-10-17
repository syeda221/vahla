@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Standardized Sale Report Pattern Styling */
    .sale-report-container {
        padding: 10px 14px;
        background: #f1f5f9;
        min-height: calc(100vh - 75px);
    }
    .sale-filter-label {
        margin-right: 4px !important;
        margin-bottom: 0 !important;
        white-space: nowrap;
        font-weight: 700;
        font-size: .78rem;
        color: #475569;
    }
    .summary-pill-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .stat-pill {
        flex: 1 1 0px;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .stat-pill .stat-label {
        font-size: .60rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 1px;
    }
    .stat-pill .stat-val {
        font-size: .88rem;
        font-weight: 800;
        line-height: 1.2;
    }

    /* Mobile Cards Styling */
    .mob-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .mob-metric-card {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 10px 6px;
        text-align: center;
        height: 100%;
    }
    .mob-metric-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
    }
    .mob-metric-val {
        font-size: 14px;
        font-weight: 800;
    }

    /* Table Styling with Sticky Header */
    .sale-table-wrap {
        height: calc(100vh - 250px);
        max-height: calc(100vh - 250px);
        min-height: 380px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
    }
    .report-table {
        font-size: .78rem;
        margin-bottom: 0;
    }
    .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-size: .75rem;
        font-weight: 700;
        padding: 9px 10px;
        border-bottom: 2px solid #334155;
        white-space: nowrap;
    }

    @media print {
        body { background: #ffffff !important; font-size: 11px; }
        .no-print, header, .sidebar, .navbar, footer { display: none !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; margin-bottom: 10px !important; }
    }
</style>

<div class="sale-report-container">

    @php
        $totalItems = count($rows);
        $totalProducts = $rows->pluck('product_id')->unique()->count();
        $totalOnhandQty = $rows->sum('onhand_qty');
        $inStockCount = $rows->where('onhand_qty', '>', 0)->count();
        $outOfStockCount = $rows->where('onhand_qty', '<=', 0)->count();
    @endphp

    {{-- DESKTOP FILTER HEADER CARD (d-none d-md-block Standard Pattern) --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                
                {{-- Left Title --}}
                <div class="d-flex align-items-center me-3">
                    <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                        <i class="fas fa-warehouse text-primary me-2"></i>Inventory On-Hand Report
                    </span>
                </div>

                {{-- Search Input --}}
                <div class="flex-grow-1" style="min-width: 250px; max-width: 400px; margin-right: 14px;">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; pointer-events: none;"></i>
                        <input type="text" class="form-control form-control-sm searchOnhandInput" placeholder="Search Code / Name / Brand / UOM…" style="height: 34px; font-size: .80rem; border-radius: 6px; padding-left: 34px; border: 1px solid #cbd5e1;">
                    </div>
                </div>

                {{-- Action Buttons with Spacing --}}
                <div class="d-flex align-items-center ms-auto" style="gap: 10px !important;">
                    <button type="button" class="btn btn-light border btn-sm px-3 fw-bold text-secondary d-inline-flex align-items-center btnResetTrigger" style="height: 34px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm px-3 fw-bold d-inline-flex align-items-center btnExportExcel" style="height: 34px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold d-inline-flex align-items-center btnPrintReport" style="height: 34px; border-radius: 6px; font-size: .78rem; margin-right: 8px !important;">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="{{ route('product') }}" class="btn btn-primary btn-sm px-3 fw-bold d-inline-flex align-items-center" style="height: 34px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MOBILE FILTER HEADER CARD (d-md-none With Top Margin) --}}
    <div class="card border-0 shadow-sm mb-3 no-print d-md-none mt-2" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 mb-1 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-6">
                        <i class="fas fa-warehouse text-primary me-2"></i>Inventory On-Hand
                    </span>
                    <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-sm" style="font-size: 11px;">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                {{-- Search Input --}}
                <div class="col-12 mb-2">
                    <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Search Inventory</label>
                    <input type="text" class="form-control form-control-sm searchOnhandInput" placeholder="Search Code / Name / Brand / UOM…" style="font-size: 11px; height: 34px; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>

                {{-- Action Buttons Row --}}
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-center gap-2 pt-1" style="gap: 10px !important;">
                        <button type="button" class="btn btn-light border btn-sm flex-fill fw-bold text-secondary btnResetTrigger" style="font-size: 11px; margin-right: 8px !important;">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm flex-fill fw-bold btnExportExcel" style="font-size: 11px; margin-right: 8px !important;">
                            <i class="fas fa-file-excel me-1"></i> Excel
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill fw-bold btnPrintReport" style="font-size: 11px;">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DESKTOP SUMMARY METRIC PILL BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-2 d-none d-md-block" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body p-2">
            <div class="summary-pill-bar">
                
                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-muted">Total Products / Items</div>
                    <div class="stat-val text-dark">
                        {{ number_format($totalItems) }}
                        @if($totalItems != $totalProducts)
                            <small class="text-muted fw-normal" style="font-size: .65rem;">({{ $totalProducts }} Products)</small>
                        @endif
                    </div>
                </div>

                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info">Total On-Hand Qty</div>
                    <div class="stat-val text-primary">{{ rtrim(rtrim(number_format($totalOnhandQty, 3, '.', ''), '0'), '.') }}</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">In Stock Items</div>
                    <div class="stat-val text-success">{{ number_format($inStockCount) }}</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Out Of Stock</div>
                    <div class="stat-val text-danger">{{ number_format($outOfStockCount) }}</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-boxes text-muted me-1"></i>Total Items</span>
                <div class="mob-metric-val text-dark mt-1">
                    {{ number_format($totalItems) }}
                    @if($totalItems != $totalProducts)
                        <small class="text-muted fw-normal" style="font-size: 10px;">({{ $totalProducts }} Prods)</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-cubes text-primary me-1"></i>On-Hand Qty</span>
                <div class="mob-metric-val text-primary mt-1">{{ rtrim(rtrim(number_format($totalOnhandQty, 3, '.', ''), '0'), '.') }}</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-check-circle text-success me-1"></i>In Stock</span>
                <div class="mob-metric-val text-success mt-1">{{ number_format($inStockCount) }}</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-times-circle text-danger me-1"></i>Out of Stock</span>
                <div class="mob-metric-val text-danger mt-1">{{ number_format($outOfStockCount) }}</div>
            </div>
        </div>
    </div>

    {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none d-md-block">
        <div class="card-body p-0">
            <div class="sale-table-wrap">
                <table class="table table-bordered table-hover align-middle mb-0 report-table" id="onhandTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 140px;">CODE</th>
                            <th>ITEM NAME</th>
                            <th style="width: 150px;">BRAND</th>
                            <th style="width: 100px;">UOM</th>
                            <th class="text-end" style="width: 130px;">ON-HAND QTY</th>
                            <th class="text-center" style="width: 110px;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $i => $r)
                            @php
                                $qtyVal = rtrim(rtrim(number_format($r->onhand_qty, 3, '.', ''), '0'), '.');
                                $qtyNum = (float)$r->onhand_qty;
                            @endphp
                            <tr data-search="{{ strtolower($r->item_code . ' ' . $r->parent_code . ' ' . $r->item_name . ' ' . $r->parent_name . ' ' . $r->brand_name . ' ' . $r->unit_name . ' ' . ($r->barcode ?? '')) }}">
                                <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                                <td>
                                    <span class="font-monospace text-primary fw-bold">{{ $r->item_code }}</span>
                                    @if($r->is_variant && $r->parent_code && $r->parent_code !== $r->item_code)
                                        <div class="text-muted font-monospace" style="font-size: 10px;">{{ $r->parent_code }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <span class="fw-semibold text-dark">{{ $r->item_name }}</span>
                                        @if($r->is_variant)
                                            <span class="badge bg-light text-primary border" style="font-size: 10px;"><i class="fas fa-code-branch me-1"></i>Variant</span>
                                        @endif
                                    </div>
                                    @if($r->is_variant && $r->parent_name && $r->parent_name !== $r->item_name)
                                        <div class="text-muted" style="font-size: 11px;"><i class="fas fa-box me-1"></i>{{ $r->parent_name }}</div>
                                    @endif
                                    @if($r->is_variant && (($r->variant_size && $r->variant_size !== '-') || ($r->variant_color && $r->variant_color !== '-')))
                                        <div class="mt-0.5">
                                            @if($r->variant_size && $r->variant_size !== '-')
                                                <span class="badge bg-light text-secondary border me-1" style="font-size: 9.5px;">Size: {{ $r->variant_size }}</span>
                                            @endif
                                            @if($r->variant_color && $r->variant_color !== '-')
                                                <span class="badge bg-light text-secondary border" style="font-size: 9.5px;">Color: {{ $r->variant_color }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $r->brand_name ?: '-' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $r->unit_name ?: '-' }}</span></td>
                                <td class="text-end">
                                    <span class="fw-bold {{ $qtyNum > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $qtyVal }}
                                    </span>
                                    @if($r->formatted_qty && $r->total_pieces != $qtyVal)
                                        <div class="text-muted" style="font-size: 10.5px;">({{ $r->total_pieces }} Pcs)</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($r->is_variant)
                                        <span class="badge bg-primary text-white">Variant</span>
                                    @elseif($r->is_part)
                                        <span class="badge bg-info text-white">Part</span>
                                    @elseif($r->is_assembled)
                                        <span class="badge bg-success text-white">Assembled</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Simple</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No Inventory Data Found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MOBILE CARDS CONTAINER (d-md-none) --}}
    <div class="d-md-none" id="onhandMobileContainer">
        @forelse($rows as $i => $r)
            @php
                $qtyVal = rtrim(rtrim(number_format($r->onhand_qty, 3, '.', ''), '0'), '.');
                $qtyNum = (float)$r->onhand_qty;
            @endphp
            <div class="mob-card p-2.5 p-2 mb-2 mob-onhand-card" data-search="{{ strtolower($r->item_code . ' ' . $r->parent_code . ' ' . $r->item_name . ' ' . $r->parent_name . ' ' . $r->brand_name . ' ' . $r->unit_name . ' ' . ($r->barcode ?? '')) }}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-light text-muted border" style="font-size: 10px;">#{{ $i + 1 }}</span>
                        <span class="badge bg-light text-primary border font-monospace fw-bold" style="font-size: 11px;">{{ $r->item_code }}</span>
                        @if($r->is_variant && $r->parent_code && $r->parent_code !== $r->item_code)
                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 9px;">{{ $r->parent_code }}</span>
                        @endif
                    </div>
                    <div>
                        @if($r->is_variant)
                            <span class="badge bg-primary text-white" style="font-size: 10px;">Variant</span>
                        @elseif($r->is_part)
                            <span class="badge bg-info text-white" style="font-size: 10px;">Part</span>
                        @elseif($r->is_assembled)
                            <span class="badge bg-success text-white" style="font-size: 10px;">Assembled</span>
                        @else
                            <span class="badge bg-secondary text-white" style="font-size: 10px;">Simple</span>
                        @endif
                    </div>
                </div>
                <div class="mb-1">
                    <strong class="text-dark d-block" style="font-size: 12.5px; line-height: 1.2;">{{ $r->item_name }}</strong>
                    @if($r->is_variant && $r->parent_name && $r->parent_name !== $r->item_name)
                        <small class="text-muted d-block" style="font-size: 10.5px;"><i class="fas fa-box me-1"></i>{{ $r->parent_name }}</small>
                    @endif
                    <small class="text-muted" style="font-size: 11px;">Brand: {{ $r->brand_name ?: '-' }} | UOM: {{ $r->unit_name ?: '-' }}</small>
                    @if($r->is_variant && (($r->variant_size && $r->variant_size !== '-') || ($r->variant_color && $r->variant_color !== '-')))
                        <div class="mt-1">
                            @if($r->variant_size && $r->variant_size !== '-')
                                <span class="badge bg-light text-secondary border me-1" style="font-size: 9px;">Size: {{ $r->variant_size }}</span>
                            @endif
                            @if($r->variant_color && $r->variant_color !== '-')
                                <span class="badge bg-light text-secondary border" style="font-size: 9px;">Color: {{ $r->variant_color }}</span>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="border-top pt-2 mt-1">
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 11px;">
                        <span class="text-muted fw-bold">On-Hand Quantity:</span>
                        <div class="text-end">
                            <strong class="fs-6 {{ $qtyNum > 0 ? 'text-success' : 'text-danger' }}">{{ $qtyVal }} {{ $r->unit_name }}</strong>
                            @if($r->formatted_qty && $r->total_pieces != $qtyVal)
                                <div class="text-muted" style="font-size: 10px;">({{ $r->total_pieces }} Pcs)</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-3 text-center py-4 bg-white">
                <div class="card-body py-4 text-muted">
                    <i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i>
                    <p class="small fw-bold mb-0">No Inventory Data Found</p>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        
        // Real-time Search Handler for Desktop & Mobile
        $('.searchOnhandInput').on('input', function() {
            let val = $(this).val().toLowerCase().trim();
            $('.searchOnhandInput').val($(this).val()); // sync input value

            // Desktop Table Filter
            $('#onhandTable tbody tr').each(function() {
                let searchStr = $(this).attr('data-search') || $(this).text().toLowerCase();
                $(this).toggle(searchStr.indexOf(val) > -1);
            });

            // Mobile Cards Filter
            $('.mob-onhand-card').each(function() {
                let searchStr = $(this).attr('data-search') || $(this).text().toLowerCase();
                $(this).toggle(searchStr.indexOf(val) > -1);
            });
        });

        // Reset Handler
        $('.btnResetTrigger').on('click', function() {
            $('.searchOnhandInput').val('').trigger('input');
        });

        // Print Handler
        $('.btnPrintReport').on('click', function() {
            window.print();
        });

        // Export Excel CSV Handler
        $('.btnExportExcel').on('click', function() {
            let csv = [];
            csv.push(['#', 'Code', 'Item Name', 'Brand', 'UOM', 'On-Hand Qty', 'Status'].join(','));
            
            $('#onhandTable tbody tr').each(function() {
                if ($(this).is(':visible')) {
                    let row = [];
                    $(this).find('td').each(function(idx) {
                        let text = $(this).text().trim().replace(/,/g, '').replace(/\n/g, ' ');
                        row.push('"' + text + '"');
                    });
                    if (row.length > 1) csv.push(row.join(','));
                }
            });

            let blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Inventory_Onhand_Report.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

    });
</script>
@endsection
