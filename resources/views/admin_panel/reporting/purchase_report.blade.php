@extends('admin_panel.layout.app')

@section('title', 'Purchase Report')

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
        padding: 8px 6px;
        text-align: center;
        height: 100%;
    }
    .mob-metric-label {
        font-size: 10px;
        color: #64748b;
        font-weight: 600;
    }
    .mob-metric-val {
        font-size: 12.5px;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
        .sale-table-wrap { height: auto !important; max-height: none !important; overflow: visible !important; border: none !important; }
        .report-table th, .report-table td { border: 1px solid #cbd5e1 !important; }
    }
</style>

<div class="sale-report-container">

    {{-- DESKTOP FILTER HEADER CARD (d-none d-md-block Standard Pattern) --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="purchaseFormDesk">
                
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    
                    {{-- Left Title --}}
                    <div class="d-flex align-items-center me-2">
                        <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                            <i class="fa-solid fa-cart-shopping text-primary me-2"></i>Purchase Report
                        </span>
                    </div>

                    {{-- Mid Filters --}}
                    <div class="d-flex align-items-center me-auto flex-wrap" style="gap: 12px !important;">
                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Start:</label>
                            <input type="date" id="start_date_desk" class="form-control form-control-sm fw-bold startDateInput" value="{{ date('Y-m-01') }}" style="height: 32px; width: 130px; font-size: .78rem; border-radius: 6px;">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">End:</label>
                            <input type="date" id="end_date_desk" class="form-control form-control-sm fw-bold endDateInput" value="{{ date('Y-m-d') }}" style="height: 32px; width: 130px; font-size: .78rem; border-radius: 6px;">
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Vendor:</label>
                            <select id="vendor_id_desk" class="form-select form-select-sm fw-bold vendorInput" style="height: 32px; width: 135px; font-size: .78rem; border-radius: 6px;">
                                <option value="all">All Vendors</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Product:</label>
                            <select id="product_id_desk" class="form-select form-select-sm fw-bold productInput" style="height: 32px; width: 135px; font-size: .78rem; border-radius: 6px;">
                                <option value="all">All Products</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->item_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Right Buttons --}}
                    <div class="d-flex align-items-center ms-auto" style="gap: 8px !important;">
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold d-inline-flex align-items-center btnSearchTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem; margin-right: 6px !important;">
                            <i class="fas fa-filter me-1"></i> Search
                        </button>
                        <button type="button" class="btn btn-light border btn-sm px-3 fw-bold text-secondary d-inline-flex align-items-center btnResetTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem; margin-right: 6px !important;">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm px-3 fw-bold d-inline-flex align-items-center me-1 btnCsvTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem;">
                            <i class="fa fa-file-csv me-1"></i> CSV
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold d-inline-flex align-items-center btnPrintReport" style="height: 32px; border-radius: 6px; font-size: .78rem;">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- MOBILE FILTER HEADER CARD (d-md-none With Top Margin) --}}
    <div class="card border-0 shadow-sm mb-3 no-print d-md-none mt-2" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form id="purchaseFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fa-solid fa-cart-shopping text-primary me-2"></i>Purchase Report
                        </span>
                    </div>

                    {{-- Start & End Date --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Start Date</label>
                        <input type="date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="{{ date('Y-m-01') }}" style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">End Date</label>
                        <input type="date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>

                    {{-- Vendor & Product --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Vendor</label>
                        <select id="vendor_id_mob" class="form-select form-select-sm vendorInput" style="font-size: 11px;">
                            <option value="all">All Vendors</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Product</label>
                        <select id="product_id_mob" class="form-select form-select-sm productInput" style="font-size: 11px;">
                            <option value="all">All Products</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->item_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Full Width Search Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Search Purchases
                        </button>
                    </div>

                    {{-- Reset, CSV & Print Actions --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-center gap-2 pt-1" style="gap: 8px !important;">
                            <button type="button" class="btn btn-light border btn-sm flex-fill fw-bold text-secondary btnResetTrigger" style="font-size: 11px; margin-right: 6px !important;">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm flex-fill fw-bold me-1 btnCsvTrigger" style="font-size: 11px;">
                                <i class="fa fa-file-csv me-1"></i> CSV
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill fw-bold btnPrintReport" style="font-size: 11px;">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DESKTOP SUMMARY METRIC PILL BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-2 d-none d-md-block" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body p-2">
            <div class="summary-pill-bar">
                
                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info">Total Purchases Net</div>
                    <div class="stat-val text-primary" id="pillNet">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">Total Paid</div>
                    <div class="stat-val text-success" id="pillPaid">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Total Due / Payable</div>
                    <div class="stat-val text-danger" id="pillDue">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-secondary">Total Purchased Qty</div>
                    <div class="stat-val text-dark" id="pillQty">0.00</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #f0f9ff; border-color: #bae6fd;">
                <span class="mob-metric-label text-info"><i class="fas fa-cart-shopping text-primary me-1"></i>Total Net</span>
                <div class="mob-metric-val text-primary mt-1" id="mobPillNet">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #f0fdf4; border-color: #86efac;">
                <span class="mob-metric-label text-success"><i class="fas fa-money-bill-wave text-success me-1"></i>Total Paid</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillPaid">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #fef2f2; border-color: #fca5a5;">
                <span class="mob-metric-label text-danger"><i class="fas fa-hand-holding-dollar text-danger me-1"></i>Total Due</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillDue">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card" style="background: #f8fafc; border-color: #cbd5e1;">
                <span class="mob-metric-label text-secondary"><i class="fas fa-boxes-stacked text-secondary me-1"></i>Total Qty</span>
                <div class="mob-metric-val text-dark mt-1" id="mobPillQty">0.00</div>
            </div>
        </div>
    </div>

    {{-- Loader --}}
    <div id="loader" style="display:none; text-align:center; padding: 20px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="small text-muted mt-2">Fetching Purchase Data…</div>
    </div>

    <div id="reportBox">
        
        {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
        <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none d-md-block">
            <div class="card-body p-0">
                <div class="sale-table-wrap">
                    <table class="table table-bordered table-hover align-middle mb-0 report-table" id="purchaseTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th style="width: 90px;">DATE</th>
                                <th style="width: 100px;">INVOICE</th>
                                <th>VENDOR</th>
                                <th style="width: 100px;">CODE</th>
                                <th>ITEM NAME</th>
                                <th class="text-center" style="width: 80px;">QTY</th>
                                <th class="text-center" style="width: 60px;">UNIT</th>
                                <th class="text-end" style="width: 90px;">PRICE</th>
                                <th class="text-end" style="width: 110px;">LINE TOTAL</th>
                                <th class="text-center" style="width: 100px;">RETURNS</th>
                                <th class="text-end" style="width: 110px;">NET AMOUNT</th>
                                <th class="text-end" style="width: 100px;">PAID</th>
                                <th class="text-end" style="width: 100px;">DUE</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRows"></tbody>
                        <tfoot id="tfootTotals" class="bg-light fw-bold" style="font-size: 12px;"></tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- MOBILE CARDS CONTAINER (d-md-none) --}}
        <div class="d-md-none" id="mobPurchaseContainer">
        </div>

    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        
        // Sync Inputs between Desktop & Mobile
        $('.startDateInput').on('change', function() { $('.startDateInput').val($(this).val()); });
        $('.endDateInput').on('change', function() { $('.endDateInput').val($(this).val()); });
        $('.vendorInput').on('change', function() { $('.vendorInput').val($(this).val()); });
        $('.productInput').on('change', function() { $('.productInput').val($(this).val()); });

        function fmt(amount) {
            let val = parseFloat(amount || 0);
            return 'Rs ' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        let rawRows = [];

        function loadReport() {
            let start = $(".startDateInput").val();
            let end = $(".endDateInput").val();
            let vendor = $(".vendorInput").val();
            let product = $(".productInput").val();
            
            $("#loader").show();
            $("#reportBox").hide();

            $.ajax({
                url: "{{ route('report.purchase.fetch') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    start_date: start,
                    end_date: end,
                    vendor_id: vendor,
                    product_id: product
                },
                success: function(res) {
                    $("#loader").hide();
                    $("#reportBox").show();

                    let rows = res.data || [];
                    rawRows = rows;

                    let rowsHtml = "";
                    let mobHtml = "";

                    let totalQty = 0;
                    let totalNet = 0;
                    let totalPaid = 0;
                    let totalDue = 0;

                    if (rows.length > 0) {
                        rows.forEach(function(row, idx) {
                            let qtyVal = parseFloat(row.qty || 0);
                            let netVal = parseFloat(row.net_amount || 0);
                            let paidVal = parseFloat(row.paid_amount || 0);
                            let dueVal = parseFloat(row.due_amount || 0);
                            let priceVal = parseFloat(row.price || 0);
                            let lineTotalVal = parseFloat(row.line_total || 0);

                            totalQty += qtyVal;
                            totalNet += netVal;
                            totalPaid += paidVal;
                            totalDue += dueVal;

                            let returnHtml = '-';
                            let mobReturnHtml = '';
                            if (row.returns && row.returns.length > 0) {
                                let retParts = row.returns.map(ret => `${ret.qty} (${fmt(ret.line_total)})`);
                                returnHtml = `<span class="text-danger fw-semibold">${retParts.join('<br>')}</span>`;
                                mobReturnHtml = `<div class="mt-1 text-danger small"><i class="fas fa-rotate-left me-1"></i>Returned: ${retParts.join(', ')}</div>`;
                            }

                            rowsHtml += `
                                <tr>
                                    <td class="text-center text-muted fw-bold">${idx + 1}</td>
                                    <td class="text-nowrap" style="font-size: 11px;">${row.purchase_date}</td>
                                    <td class="fw-bold text-dark" style="font-size: 11.5px;">${row.invoice_no}</td>
                                    <td class="fw-semibold text-secondary">${row.vendor_name}</td>
                                    <td class="text-muted" style="font-size: 11px;">${row.item_code}</td>
                                    <td class="fw-semibold text-dark">${row.item_name}</td>
                                    <td class="text-center fw-bold">${qtyVal.toFixed(2)}</td>
                                    <td class="text-center text-muted">${row.unit || 'pc'}</td>
                                    <td class="text-end">${fmt(priceVal)}</td>
                                    <td class="text-end fw-semibold text-dark">${fmt(lineTotalVal)}</td>
                                    <td class="text-center">${returnHtml}</td>
                                    <td class="text-end fw-bold text-primary">${fmt(netVal)}</td>
                                    <td class="text-end text-success fw-semibold">${fmt(paidVal)}</td>
                                    <td class="text-end text-danger fw-semibold">${fmt(dueVal)}</td>
                                </tr>
                            `;

                            mobHtml += `
                                <div class="mob-card p-2.5 p-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-light text-dark border" style="font-size: 10px;">${row.invoice_no}</span>
                                            <strong class="text-dark" style="font-size: 12.5px;">${row.vendor_name}</strong>
                                        </div>
                                        <span class="text-muted" style="font-size: 10.5px;">${row.purchase_date}</span>
                                    </div>
                                    <div class="border-top pt-2 mt-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 11.5px;">
                                            <span class="fw-bold text-dark">${row.item_name}</span>
                                            <span class="badge bg-secondary-subtle text-secondary border" style="font-size:10px;">${row.item_code}</span>
                                        </div>
                                        <div class="row g-1 text-center my-1" style="font-size: 11px;">
                                            <div class="col-4 border-end">
                                                <span class="text-muted d-block" style="font-size: 10px;">Qty</span>
                                                <strong class="text-dark">${qtyVal.toFixed(2)} ${row.unit || 'pc'}</strong>
                                            </div>
                                            <div class="col-4 border-end">
                                                <span class="text-muted d-block" style="font-size: 10px;">Price</span>
                                                <strong class="text-dark">${fmt(priceVal)}</strong>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted d-block" style="font-size: 10px;">Net Amount</span>
                                                <strong class="text-primary">${fmt(netVal)}</strong>
                                            </div>
                                        </div>
                                        ${mobReturnHtml}
                                        <div class="d-flex justify-content-between align-items-center border-top pt-1 mt-1" style="font-size: 11px;">
                                            <span class="text-success fw-bold">Paid: ${fmt(paidVal)}</span>
                                            <span class="text-danger fw-bold">Due: ${fmt(dueVal)}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        $("#tbodyRows").html(rowsHtml);
                        $("#mobPurchaseContainer").html(mobHtml);

                        // Totals
                        let totalsHtml = `
                            <tr>
                                <td colspan="6" class="text-end text-dark">TOTALS:</td>
                                <td class="text-center text-dark">${totalQty.toFixed(2)}</td>
                                <td colspan="4"></td>
                                <td class="text-end text-primary">${fmt(totalNet)}</td>
                                <td class="text-end text-success">${fmt(totalPaid)}</td>
                                <td class="text-end text-danger">${fmt(totalDue)}</td>
                            </tr>
                        `;
                        $("#tfootTotals").html(totalsHtml);

                        // Stat Pills
                        $('#pillNet, #mobPillNet').text(fmt(totalNet));
                        $('#pillPaid, #mobPillPaid').text(fmt(totalPaid));
                        $('#pillDue, #mobPillDue').text(fmt(totalDue));
                        $('#pillQty, #mobPillQty').text(totalQty.toFixed(2));
                    } else {
                        $("#tbodyRows").html(`<tr><td colspan="14" class="text-center text-muted py-4">No purchase records found in selected filters.</td></tr>`);
                        $("#mobPurchaseContainer").html('<div class="card border-0 shadow-sm rounded-3 text-center py-4 bg-white"><div class="card-body py-4 text-muted"><i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i><p class="small fw-bold mb-0">No Purchase Data Found</p></div></div>');
                        $("#tfootTotals").empty();

                        $('#pillNet, #mobPillNet').text('Rs 0.00');
                        $('#pillPaid, #mobPillPaid').text('Rs 0.00');
                        $('#pillDue, #mobPillDue').text('Rs 0.00');
                        $('#pillQty, #mobPillQty').text('0.00');
                    }
                },
                error: function() {
                    $("#loader").hide();
                    alert("Error fetching purchase report data.");
                }
            });
        }

        // Export to CSV Function
        function exportToCSV() {
            if (!rawRows || rawRows.length === 0) {
                alert("No data available to export.");
                return;
            }

            let csv = "Purchase Date,Invoice No,Vendor Name,Item Code,Item Name,Qty,Unit,Price,Line Total,Returns,Net Amount,Paid Amount,Due Amount\n";
            rawRows.forEach(function(r) {
                let returnStr = r.returns && r.returns.length ? r.returns.map(ret => `${ret.qty} (${ret.line_total})`).join(';') : '';
                csv += `"${r.purchase_date}","${r.invoice_no}","${r.vendor_name}","${r.item_code}","${r.item_name}",${r.qty},"${r.unit || 'pc'}",${r.price},${r.line_total},"${returnStr}",${r.net_amount},${r.paid_amount},${r.due_amount}\n`;
            });

            let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'purchase_report_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        $(".btnSearchTrigger").click(function() {
            loadReport();
        });

        $(".btnResetTrigger").click(function() {
            let startOfMonth = '{{ date("Y-m-01") }}';
            let today = '{{ date("Y-m-d") }}';
            $(".startDateInput").val(startOfMonth);
            $(".endDateInput").val(today);
            $(".vendorInput").val('all');
            $(".productInput").val('all');
            loadReport();
        });

        $(".btnPrintReport").click(function() {
            document.title = 'Purchase Report';
            window.print();
        });

        window.addEventListener('beforeprint', function() {
            document.title = 'Purchase Report';
        });

        $(".btnCsvTrigger").click(function() {
            exportToCSV();
        });

        // Load report on page ready
        loadReport();
    });
</script>
@endsection