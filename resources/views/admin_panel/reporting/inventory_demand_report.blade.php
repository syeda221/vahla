@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --idr-navy: #0b1020;
        --idr-primary: #3452d9;
        --idr-primary-hover: #263fb3;
        --idr-bg: #f3f4f7;
        --idr-card: #ffffff;
        --idr-border: #e3e5ea;
        --idr-border-subtle: #edf0f5;
        --idr-text-main: #0f172a;
        --idr-text-body: #334155;
        --idr-text-muted: #64748b;
        --idr-danger: #e11d48;
        --idr-warning: #d97706;
        --idr-success: #059669;
    }

    .idr-page-wrap {
        background: var(--idr-bg);
        min-height: calc(100vh - 75px);
        padding: 24px 28px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--idr-text-body);
    }

    /* Top Breadcrumb */
    .idr-top-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .idr-breadcrumb-list {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .idr-breadcrumb-list a {
        color: var(--idr-text-muted);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.15s ease;
    }
    .idr-breadcrumb-list a:hover {
        color: var(--idr-primary);
    }
    .idr-crumb-badge {
        background: #ffffff;
        color: var(--idr-primary);
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #dbeafe;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Filter Card */
    .idr-filter-box {
        background: #ffffff;
        border: 1px solid var(--idr-border);
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .idr-field-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--idr-text-body);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .idr-form-control {
        height: 40px;
        border: 1px solid var(--idr-border);
        border-radius: 8px;
        font-size: 13.5px;
        color: var(--idr-text-main);
        padding: 0 12px;
        background-color: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .idr-form-control:focus {
        border-color: var(--idr-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 82, 217, 0.12);
    }

    /* KPI Stat Cards (Enterprise style with clean left accents) */
    .idr-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    .idr-stat-card {
        background: #ffffff;
        border: 1px solid var(--idr-border);
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        border-left: 4px solid var(--idr-primary);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .idr-stat-card.deficit { border-left-color: var(--idr-danger); }
    .idr-stat-card.demand  { border-left-color: var(--idr-warning); }
    .idr-stat-card.budget  { border-left-color: var(--idr-success); }

    .idr-stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--idr-text-muted);
        margin-bottom: 6px;
    }
    .idr-stat-number {
        font-size: 24px;
        font-weight: 700;
        color: var(--idr-text-main);
        font-variant-numeric: tabular-nums;
        line-height: 1.1;
    }

    /* Main Table Panel */
    .idr-table-panel {
        background: #ffffff;
        border: 1px solid var(--idr-border);
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .idr-table-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid var(--idr-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: #ffffff;
    }
    .idr-header-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--idr-text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .idr-header-subtitle {
        font-size: 12.5px;
        color: var(--idr-text-muted);
        margin-top: 3px;
    }

    /* Table & Columns */
    .idr-data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        font-size: 13px;
    }
    .idr-data-table th {
        background: #f8fafc;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 14px;
        border-top: 1px solid var(--idr-border);
        border-bottom: 1px solid var(--idr-border);
        border-right: 1px solid var(--idr-border-subtle);
        vertical-align: middle;
        white-space: nowrap;
    }
    .idr-data-table th:last-child {
        border-right: none;
    }
    .idr-data-table td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--idr-border-subtle);
        border-right: 1px solid var(--idr-border-subtle);
        vertical-align: middle;
        color: var(--idr-text-main);
        font-variant-numeric: tabular-nums;
        background-color: #ffffff;
    }
    .idr-data-table td:last-child {
        border-right: none;
    }
    .idr-data-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .idr-data-table tfoot th {
        background: #f8fafc;
        font-size: 13px;
        font-weight: 700;
        padding: 13px 14px;
        border-top: 2px solid var(--idr-border);
        border-bottom: 1px solid var(--idr-border);
        color: var(--idr-text-main);
    }

    /* Custom Badges */
    .badge-deficit-pill {
        background: #fff1f2;
        color: var(--idr-danger);
        border: 1px solid #fecdd3;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    .badge-zero-pill {
        background: #fffbeb;
        color: var(--idr-warning);
        border: 1px solid #fde68a;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    .badge-demand-pill {
        background: #eff6ff;
        color: var(--idr-primary);
        border: 1px solid #bfdbfe;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 4px;
        font-size: 12.5px;
    }
    .tag-neutral {
        background: #f1f5f9;
        color: #475569;
        font-size: 11.5px;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 500;
    }

    /* Buttons */
    .btn-action-primary {
        background: var(--idr-primary);
        color: #ffffff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        height: 40px;
        padding: 0 20px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.15s ease;
    }
    .btn-action-primary:hover {
        background: var(--idr-primary-hover);
        color: #ffffff;
    }
    .btn-action-reset {
        height: 40px;
        width: 40px;
        border: 1px solid var(--idr-border);
        border-radius: 8px;
        background: #ffffff;
        color: var(--idr-text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    .btn-action-reset:hover {
        background: #f8fafc;
        color: var(--idr-text-main);
        border-color: #cbd5e1;
    }
    .btn-header-tool {
        height: 36px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--idr-border);
        background: #ffffff;
        color: var(--idr-text-body);
        transition: all 0.15s ease;
    }
    .btn-header-tool:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--idr-text-main);
    }
    .btn-header-tool.btn-print {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .btn-header-tool.btn-print:hover {
        background: #1e293b;
        color: #ffffff;
    }

    /* Premium Segmented Filter Chips / Toggle Pills */
    .idr-toggles-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--idr-border-subtle);
    }
    .idr-toggle-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--idr-text-muted);
    }
    .idr-chip-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        margin: 0;
    }
    .idr-chip-toggle input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .idr-chip-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid var(--idr-border);
        background: #f8fafc;
        color: #475569;
        font-size: 12.5px;
        font-weight: 600;
        transition: all 0.15s ease-in-out;
    }
    .idr-chip-pill .chip-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #cbd5e1;
        transition: background 0.15s ease, transform 0.15s ease;
    }
    .idr-chip-toggle:hover .idr-chip-pill {
        background: #ffffff;
        border-color: #cbd5e1;
        color: var(--idr-text-main);
    }
    .idr-chip-toggle input:checked + .idr-chip-pill {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
        box-shadow: 0 1px 2px rgba(52, 82, 217, 0.08);
    }
    .idr-chip-toggle input:checked + .idr-chip-pill .chip-dot {
        background: #2563eb;
        transform: scale(1.15);
    }
    .idr-chip-pill.idr-chip-pill-primary {
        border-color: var(--idr-border);
        background: #f8fafc;
        color: #64748b;
    }
    .idr-chip-toggle input:checked + .idr-chip-pill-primary {
        background: #3452d9;
        border-color: #3452d9;
        color: #ffffff;
        box-shadow: 0 2px 4px rgba(52, 82, 217, 0.2);
    }
    .idr-chip-toggle input:checked + .idr-chip-pill-primary i {
        color: #ffffff;
    }

    /* Select2 custom override */
    .select2-container--default .select2-selection--single {
        height: 40px !important;
        border: 1px solid var(--idr-border) !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px !important;
        font-size: 13.5px !important;
        color: var(--idr-text-main) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 10px !important;
    }

    @media (max-width: 992px) {
        .idr-stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .idr-stats-row { grid-template-columns: 1fr; }
        .idr-page-wrap { padding: 16px; }
    }

    /* Print Styles */
    @media print {
        body * { visibility: hidden; }
        #printableReportArea, #printableReportArea * { visibility: visible; }
        #printableReportArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
            background: #ffffff;
        }
        .no-print { display: none !important; }
        .idr-table-panel { border: none !important; box-shadow: none !important; }
        .idr-data-table { font-size: 9.5pt !important; width: 100% !important; border-collapse: collapse !important; }
        .idr-data-table th, .idr-data-table td { border: 1px solid #cbd5e1 !important; padding: 5px 8px !important; }
        .idr-data-table th { background: #f1f5f9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<div class="idr-page-wrap">
    {{-- Breadcrumb --}}
    <div class="idr-top-nav no-print">
        <ul class="idr-breadcrumb-list">
            <li><a href="{{ route('dashboard') }}"><i class="fas fa-home me-1"></i> Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-muted" style="font-size: 10px;"></i></li>
            <li><a href="#">Reports</a></li>
            <li><i class="fas fa-chevron-right text-muted" style="font-size: 10px;"></i></li>
            <li><span class="idr-crumb-badge"><i class="fas fa-clipboard-check"></i> Inventory Demand Report</span></li>
        </ul>
    </div>

    {{-- Filter Panel (Warehouse Removed, Clean 3-Column Grid) --}}
    <div class="idr-filter-box no-print">
        <form id="idrFilterForm">
            <div class="row g-3 align-items-end">
                {{-- Company / Brand --}}
                <div class="col-md-4">
                    <label class="idr-field-label">
                        <i class="fas fa-building text-primary"></i> Company / Brand Name:
                    </label>
                    <select name="company_id" id="filterCompany" class="form-select select2-filter">
                        <option value="all">All Companies / Brands</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div class="col-md-4">
                    <label class="idr-field-label">
                        <i class="fas fa-layer-group text-primary"></i> Category:
                    </label>
                    <select name="category_id" id="filterCategory" class="form-select select2-filter">
                        <option value="all">All Categories</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Box & Action Buttons --}}
                <div class="col-md-4">
                    <label class="idr-field-label">
                        <i class="fas fa-search text-primary"></i> Search Item:
                    </label>
                    <div class="d-flex gap-2">
                        <input type="text" id="filterSearch" class="form-control idr-form-control flex-fill" placeholder="Search by name or code...">
                        <button type="submit" class="btn btn-action-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="button" id="btnReset" class="btn btn-action-reset" title="Reset Filters">
                            <i class="fas fa-redo-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Column & Demand Toggles (Premium Segmented Chips) --}}
            <div class="idr-toggles-bar">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="idr-toggle-label"><i class="fas fa-columns text-primary me-1"></i> Optional Columns:</span>
                    
                    <label class="idr-chip-toggle">
                        <input type="checkbox" id="chkShowCategory">
                        <span class="idr-chip-pill">
                            <span class="chip-dot"></span>
                            <i class="fas fa-layer-group" style="font-size: 11px;"></i> Category
                        </span>
                    </label>

                    <label class="idr-chip-toggle">
                        <input type="checkbox" id="chkShowCompany">
                        <span class="idr-chip-pill">
                            <span class="chip-dot"></span>
                            <i class="fas fa-building" style="font-size: 11px;"></i> Company / Brand
                        </span>
                    </label>
                </div>

                <div class="ms-auto">
                    <label class="idr-chip-toggle">
                        <input type="checkbox" id="chkDemandOnly" checked>
                        <span class="idr-chip-pill idr-chip-pill-primary">
                            <i class="fas fa-filter" style="font-size: 11px;"></i> Demand Items Only (Req Qty > 0)
                        </span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    {{-- Enterprise KPI Stat Cards --}}
    <div class="idr-stats-row no-print">
        <div class="idr-stat-card">
            <div class="idr-stat-label">Demand Items</div>
            <div class="idr-stat-number" id="kpiDemandItems">0</div>
        </div>
        <div class="idr-stat-card deficit">
            <div class="idr-stat-label">Negative Deficit Qty</div>
            <div class="idr-stat-number text-danger" id="kpiDeficitQty">0</div>
        </div>
        <div class="idr-stat-card demand">
            <div class="idr-stat-label">Total Required Qty</div>
            <div class="idr-stat-number" id="kpiDemandQty">0</div>
        </div>
        <div class="idr-stat-card budget">
            <div class="idr-stat-label">Total Estimated Cost</div>
            <div class="idr-stat-number text-success" id="kpiCostAmount">Rs. 0.00</div>
        </div>
    </div>

    {{-- Printable Report Table Panel --}}
    <div class="idr-table-panel" id="printableReportArea">
        <div class="idr-table-header-bar">
            <div>
                <h4 class="idr-header-title">
                    <i class="fas fa-boxes-stacked text-primary"></i> Inventory Demand List
                </h4>
                <div class="idr-header-subtitle">
                    Report Generated On: <strong id="reportGenDate" class="text-dark">{{ date('d-m-Y H:i') }}</strong>
                </div>
            </div>
            <div class="d-flex gap-2 no-print">
                <button type="button" class="btn btn-header-tool" id="btnExportCsv">
                    <i class="fas fa-file-csv text-success"></i> Export CSV
                </button>
                <button type="button" class="btn btn-header-tool btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="idr-data-table" id="demandTable">
                <thead>
                    <tr>
                        <th style="width: 55px;" class="text-center">SR#</th>
                        <th style="width: 85px;" class="text-center">CODE</th>
                        <th>ITEM</th>
                        <th class="col-category d-none">CATEGORY</th>
                        <th class="col-company d-none">COMPANY</th>
                        <th style="width: 95px;" class="text-end">STOCK</th>
                        <th style="width: 105px;" class="text-end">P.PRICE</th>
                        <th style="width: 95px;" class="text-end">MIN/QTY</th>
                        <th style="width: 100px;" class="text-end">REQ/QTY</th>
                        <th style="width: 130px;" class="text-end">COST AMOUNT</th>
                    </tr>
                </thead>
                <tbody id="demandTableBody">
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Loading Inventory Demand Report...
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end fw-bold">Grand Total:</th>
                        <th class="col-category d-none"></th>
                        <th class="col-company d-none"></th>
                        <th class="text-end fw-bold" id="totalStockCell">0</th>
                        <th class="text-end">-</th>
                        <th class="text-end fw-bold" id="totalMinQtyCell">0</th>
                        <th class="text-end fw-bold text-primary" id="totalReqQtyCell">0</th>
                        <th class="text-end fw-bold text-success" id="totalCostAmountCell">Rs. 0.00</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-filter').select2({ width: '100%' });
        }

        var reportData = [];

        function loadDemandReport() {
            var $tbody = $('#demandTableBody');
            $tbody.html(`
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Fetching inventory demand records...
                    </td>
                </tr>
            `);

            var params = {
                company_id:   $('#filterCompany').val(),
                category_id:  $('#filterCategory').val(),
                search:       $('#filterSearch').val(),
                demand_only:  $('#chkDemandOnly').is(':checked') ? 1 : 0,
                _token:       '{{ csrf_token() }}'
            };

            $.post("{{ route('report.inventory_demand.fetch') }}", params, function(res) {
                if (res && res.success) {
                    reportData = res.rows || [];
                    $('#reportGenDate').text(res.generated_on || '{{ date("d-m-Y H:i") }}');
                    renderTable(reportData);
                    updateKPIs(res.summary);
                } else {
                    $tbody.html(`<tr><td colspan="10" class="text-center py-4 text-danger">Failed to load report.</td></tr>`);
                }
            }).fail(function(xhr) {
                $tbody.html(`<tr><td colspan="10" class="text-center py-4 text-danger">Error loading report data.</td></tr>`);
            });
        }

        function renderTable(rows) {
            var $tbody = $('#demandTableBody');
            $tbody.empty();

            var showCategory = $('#chkShowCategory').is(':checked');
            var showCompany  = $('#chkShowCompany').is(':checked');

            // Toggle Columns visibility
            if (showCategory) {
                $('.col-category').removeClass('d-none');
            } else {
                $('.col-category').addClass('d-none');
            }

            if (showCompany) {
                $('.col-company').removeClass('d-none');
            } else {
                $('.col-company').addClass('d-none');
            }

            if (rows.length === 0) {
                $tbody.html(`<tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-check-circle text-success me-1"></i> No items found matching the demand criteria.</td></tr>`);
                $('#totalStockCell').text('0');
                $('#totalMinQtyCell').text('0');
                $('#totalReqQtyCell').text('0');
                $('#totalCostAmountCell').text('Rs. 0.00');
                return;
            }

            var grandStock = 0;
            var grandMin   = 0;
            var grandReq   = 0;
            var grandCost  = 0;

            rows.forEach(function(row, idx) {
                grandStock += parseFloat(row.stock) || 0;
                grandMin   += parseFloat(row.min_qty) || 0;
                grandReq   += parseFloat(row.req_qty) || 0;
                grandCost  += parseFloat(row.cost_amount) || 0;

                var stockDisplay = '';
                if (row.stock < 0) {
                    stockDisplay = `<span class="badge-deficit-pill">${row.stock}</span>`;
                } else if (row.stock === 0) {
                    stockDisplay = `<span class="badge-zero-pill">0</span>`;
                } else {
                    stockDisplay = `<span class="fw-bold">${row.stock}</span>`;
                }

                var reqDisplay = (row.req_qty > 0) 
                    ? `<span class="badge-demand-pill">${row.req_qty}</span>` 
                    : `<span>0</span>`;

                var tr = `
                    <tr>
                        <td class="text-center text-muted fw-bold">${idx + 1}</td>
                        <td class="text-center font-monospace fw-semibold">${row.code}</td>
                        <td class="fw-bold text-dark">${row.item_name}</td>
                        <td class="col-category ${showCategory ? '' : 'd-none'}"><span class="tag-neutral">${row.category}</span></td>
                        <td class="col-company ${showCompany ? '' : 'd-none'}"><span class="tag-neutral">${row.company}</span></td>
                        <td class="text-end">${stockDisplay}</td>
                        <td class="text-end font-monospace">${Number(row.p_price).toFixed(2)}</td>
                        <td class="text-end font-monospace">${row.min_qty}</td>
                        <td class="text-end">${reqDisplay}</td>
                        <td class="text-end fw-bold font-monospace">${Number(row.cost_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>
                `;
                $tbody.append(tr);
            });

            $('#totalStockCell').text(grandStock.toLocaleString());
            $('#totalMinQtyCell').text(grandMin.toLocaleString());
            $('#totalReqQtyCell').text(grandReq.toLocaleString());
            $('#totalCostAmountCell').text('Rs. ' + grandCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        function updateKPIs(summary) {
            if (!summary) return;
            $('#kpiDemandItems').text(summary.demand_items_count ? summary.demand_items_count.toLocaleString() : 0);
            $('#kpiDeficitQty').text(summary.total_deficit ? summary.total_deficit.toLocaleString() : 0);
            $('#kpiDemandQty').text(summary.total_demand_qty ? summary.total_demand_qty.toLocaleString() : 0);
            $('#kpiCostAmount').text('Rs. ' + (summary.total_cost_amount ? Number(summary.total_cost_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00'));
        }

        // Form Submit Filter
        $('#idrFilterForm').on('submit', function(e) {
            e.preventDefault();
            loadDemandReport();
        });

        // Reset Filter
        $('#btnReset').on('click', function() {
            $('#filterCompany').val('all').trigger('change');
            $('#filterCategory').val('all').trigger('change');
            $('#filterSearch').val('');
            $('#chkDemandOnly').prop('checked', true);
            $('#chkShowCategory').prop('checked', false);
            $('#chkShowCompany').prop('checked', false);
            loadDemandReport();
        });

        // Column toggles
        $('#chkShowCategory, #chkShowCompany').on('change', function() {
            renderTable(reportData);
        });

        $('#chkDemandOnly').on('change', function() {
            loadDemandReport();
        });

        // Export CSV Functionality
        $('#btnExportCsv').on('click', function() {
            if (!reportData || reportData.length === 0) {
                Swal.fire({ icon: 'info', title: 'No Data', text: 'No report data available to export.' });
                return;
            }

            var csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "SR#,CODE,ITEM,CATEGORY,COMPANY,STOCK,PURCHASE PRICE,MIN QTY,REQUIRED QTY,COST AMOUNT\n";

            reportData.forEach(function(row, idx) {
                var cleanName = '"' + (row.item_name || '').replace(/"/g, '""') + '"';
                var cleanCat  = '"' + (row.category || '').replace(/"/g, '""') + '"';
                var cleanComp = '"' + (row.company || '').replace(/"/g, '""') + '"';
                csvContent += `${idx + 1},${row.code},${cleanName},${cleanCat},${cleanComp},${row.stock},${row.p_price},${row.min_qty},${row.req_qty},${row.cost_amount}\n`;
            });

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `Inventory_Demand_Report_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Initial Load
        loadDemandReport();
    });
</script>
@endpush
