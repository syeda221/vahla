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
        .sale-table-wrap { height: auto !important; max-height: none !important; overflow: visible !important; border: none !important; }
        .report-table th, .report-table td { border: 1px solid #cbd5e1 !important; }
    }
</style>

<div class="sale-report-container">

    {{-- DESKTOP FILTER HEADER CARD (d-none d-md-block Standard Pattern) --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="partiesFormDesk">
                
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    
                    {{-- Left Title --}}
                    <div class="d-flex align-items-center me-2">
                        <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                            <i class="fa-solid fa-users-viewfinder text-primary me-2"></i>Parties Balance Report
                        </span>
                    </div>

                    {{-- Mid Filters --}}
                    <div class="d-flex align-items-center me-auto flex-wrap" style="gap: 12px !important;">
                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Type:</label>
                            <select id="report_type_desk" class="form-select form-select-sm fw-bold reportTypeInput" style="height: 32px; width: 130px; font-size: .78rem; border-radius: 6px;">
                                <option value="BOTH">BOTH</option>
                                <option value="RECEIVABLE">RECEIVABLE</option>
                                <option value="PAYABLE">PAYABLE</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Party:</label>
                            <input type="text" id="party_name_desk" class="form-control form-control-sm fw-bold partyNameInput" placeholder="Name..." style="height: 32px; width: 140px; font-size: .78rem; border-radius: 6px;">
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Mobile:</label>
                            <input type="text" id="mobile_desk" class="form-control form-control-sm fw-bold mobileInput" placeholder="Mobile..." style="height: 32px; width: 120px; font-size: .78rem; border-radius: 6px;">
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Zone:</label>
                            <select id="zone_desk" class="form-select form-select-sm fw-bold zoneInput" style="height: 32px; width: 120px; font-size: .78rem; border-radius: 6px;">
                                <option value="">All Zones</option>
                                @foreach($zones as $z)
                                    <option value="{{ $z->id }}">{{ $z->zone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="sale-filter-label mb-0 ms-1 me-1">Address:</label>
                            <input type="text" id="address_desk" class="form-control form-control-sm fw-bold addressInput" placeholder="Area..." style="height: 32px; width: 130px; font-size: .78rem; border-radius: 6px;">
                        </div>

                        <div class="form-check form-check-inline ms-1 mb-0 d-flex align-items-center gap-1">
                            <input class="form-check-input showZeroInput" type="checkbox" id="show_zero_desk">
                            <label class="form-check-label fw-bold text-secondary" for="show_zero_desk" style="font-size: .75rem;">Show Zero</label>
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
                        <button type="button" class="btn btn-outline-info btn-sm px-3 fw-bold d-inline-flex align-items-center me-1 btnExcelTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem;">
                            <i class="fa fa-file-excel me-1"></i> Excel
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
            <form id="partiesFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fa-solid fa-users-viewfinder text-primary me-2"></i>Parties Balance
                        </span>
                    </div>

                    {{-- Report Type & Show Zero --}}
                    <div class="col-7 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Report Type</label>
                        <select id="report_type_mob" class="form-select form-select-sm reportTypeInput" style="font-size: 11px;">
                            <option value="BOTH">BOTH</option>
                            <option value="RECEIVABLE">RECEIVABLE</option>
                            <option value="PAYABLE">PAYABLE</option>
                        </select>
                    </div>
                    <div class="col-5 mb-1 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input class="form-check-input showZeroInput" type="checkbox" id="show_zero_mob">
                            <label class="form-check-label fw-bold text-secondary" for="show_zero_mob" style="font-size: 11px;">Show Zero</label>
                        </div>
                    </div>

                    {{-- Party Name & Mobile --}}
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Party Name</label>
                        <input type="text" id="party_name_mob" class="form-control form-control-sm partyNameInput" placeholder="Name..." style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Mobile</label>
                        <input type="text" id="mobile_mob" class="form-control form-control-sm mobileInput" placeholder="Mobile..." style="font-size: 11px;">
                    </div>

                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Zone</label>
                        <select id="zone_mob" class="form-select form-select-sm zoneInput" style="font-size: 11px;">
                            <option value="">All Zones</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->zone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Address</label>
                        <input type="text" id="address_mob" class="form-control form-control-sm addressInput" placeholder="Area..." style="font-size: 11px;">
                    </div>

                    {{-- Full Width Search Button --}}
                    <div class="col-12 my-2">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="background-color: #3b82f6; border-color: #3b82f6; font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Search Balances
                        </button>
                    </div>

                    {{-- Reset, Excel & Print Actions --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-center gap-2 pt-1" style="gap: 8px !important;">
                            <button type="button" class="btn btn-light border btn-sm flex-fill fw-bold text-secondary btnResetTrigger" style="font-size: 11px; margin-right: 6px !important;">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm flex-fill fw-bold me-1 btnExcelTrigger" style="font-size: 11px;">
                                <i class="fa fa-file-excel me-1"></i> Excel
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
                    <div class="stat-label text-info">Total Receivables</div>
                    <div class="stat-val text-primary" id="pillReceivable">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Total Payables</div>
                    <div class="stat-val text-danger" id="pillPayable">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-secondary">Net Position</div>
                    <div class="stat-val text-dark" id="pillNet">Rs 0.00</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (col-4 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-4 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-down text-primary me-1"></i>Receivable</span>
                <div class="mob-metric-val text-primary mt-1" id="mobPillReceivable" style="font-size: 12px;">Rs 0.00</div>
            </div>
        </div>
        <div class="col-4 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-up text-danger me-1"></i>Payable</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillPayable" style="font-size: 12px;">Rs 0.00</div>
            </div>
        </div>
        <div class="col-4 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-scale-balanced text-dark me-1"></i>Net</span>
                <div class="mob-metric-val text-dark mt-1" id="mobPillNet" style="font-size: 12px;">Rs 0.00</div>
            </div>
        </div>
    </div>

    {{-- Loader --}}
    <div id="loader" style="display:none; text-align:center; padding: 20px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="small text-muted mt-2">Fetching Parties Balance Data…</div>
    </div>

    <div id="reportBox">
        
        {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
        <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white d-none d-md-block">
            <div class="card-body p-0">
                <div class="sale-table-wrap">
                    <table class="table table-bordered table-hover align-middle mb-0 report-table" id="partiesTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th style="width: 100px;">CODE</th>
                                <th>PARTY / TITLE</th>
                                <th class="text-center" style="width: 100px;">TYPE</th>
                                <th style="width: 130px;">MOBILE</th>
                                <th style="width: 120px;">ZONE</th>
                                <th class="text-end col-receivable" style="width: 150px;">RECEIVABLE</th>
                                <th class="text-end col-payable" style="width: 150px;">PAYABLE</th>
                                <th style="width: 100px;">NOTES</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRows"></tbody>
                        <tfoot id="tfootTotals" class="bg-light fw-bold" style="font-size: 12px;"></tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- MOBILE CARDS CONTAINER (d-md-none) --}}
        <div class="d-md-none" id="mobPartiesContainer">
        </div>

    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        
        // Sync Inputs between Desktop & Mobile
        $('.reportTypeInput').on('change', function() { $('.reportTypeInput').val($(this).val()); });
        $('.partyNameInput').on('keyup change', function() { $('.partyNameInput').val($(this).val()); });
        $('.mobileInput').on('keyup change', function() { $('.mobileInput').val($(this).val()); });
        $('.zoneInput').on('change', function() { $('.zoneInput').val($(this).val()); });
        $('.addressInput').on('keyup change', function() { $('.addressInput').val($(this).val()); });
        $('.showZeroInput').on('change', function() { $('.showZeroInput').prop('checked', $(this).is(':checked')); });

        function fmt(amount) {
            let val = parseFloat(amount || 0);
            return 'Rs ' + val.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function loadReport() {
            let reportType = $(".reportTypeInput").val() || 'BOTH';
            let showZero = $(".showZeroInput").is(":checked");
            let partyName = $(".partyNameInput").val();
            let mobile = $(".mobileInput").val();
            let zone = $(".zoneInput").val();
            let address = $(".addressInput").val();
            
            $("#loader").show();
            $("#reportBox").hide();

            $.get("{{ route('report.parties_balance.fetch') }}", {
                report_type: reportType,
                show_zero: showZero,
                party_name: partyName,
                mobile: mobile,
                zone: zone,
                address: address
            }, function(res) {
                $("#loader").hide();
                $("#reportBox").show();

                // Column visibility logic for Desktop
                if (reportType === 'RECEIVABLE') {
                    $(".col-payable").hide();
                    $(".col-receivable").show();
                } else if (reportType === 'PAYABLE') {
                    $(".col-receivable").hide();
                    $(".col-payable").show();
                } else {
                    $(".col-receivable").show();
                    $(".col-payable").show();
                }

                let rowsHtml = "";
                let mobHtml = "";

                if (res.rows && res.rows.length > 0) {
                    res.rows.forEach(function(row) {
                        let isVendor = row.code && row.code.startsWith('V');
                        let typeBadge = isVendor 
                            ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:10px;">Vendor</span>`
                            : `<span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:10px;">Customer</span>`;

                        rowsHtml += `
                            <tr>
                                <td class="text-center text-muted fw-bold">${row.sr}</td>
                                <td class="fw-bold text-secondary" style="font-size: 11px;">${row.code}</td>
                                <td class="fw-semibold text-dark">${row.title}</td>
                                <td class="text-center">${typeBadge}</td>
                                <td class="text-muted" style="font-size: 11.5px;">${row.mobile || '-'}</td>
                                <td class="text-muted" style="font-size: 11.5px;">${row.zone || '-'}</td>
                                ${reportType !== 'PAYABLE' ? `<td class="text-end text-primary fw-semibold">${row.receivable != 0 ? fmt(row.receivable) : '-'}</td>` : ''}
                                ${reportType !== 'RECEIVABLE' ? `<td class="text-end text-danger fw-semibold">${row.payable != 0 ? fmt(row.payable) : '-'}</td>` : ''}
                                <td class="text-muted small">${row.notes || '-'}</td>
                            </tr>
                        `;

                        mobHtml += `
                            <div class="mob-card p-2.5 p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-light text-dark border" style="font-size: 10px;">${row.code}</span>
                                        <strong class="text-dark" style="font-size: 12.5px;">${row.title}</strong>
                                    </div>
                                    ${typeBadge}
                                </div>
                                <div class="border-top pt-2 mt-1">
                                    <div class="row g-1 align-items-center" style="font-size: 11px;">
                                        <div class="col-6">
                                            <span class="text-muted d-block" style="font-size: 10px;"><i class="fas fa-phone me-1"></i>${row.mobile || 'No Mobile'}</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            ${row.receivable > 0 ? `<strong class="text-primary" style="font-size: 12px;">Rec: ${fmt(row.receivable)}</strong>` : ''}
                                            ${row.payable > 0 ? `<strong class="text-danger" style="font-size: 12px;">Pay: ${fmt(row.payable)}</strong>` : ''}
                                            ${row.receivable == 0 && row.payable == 0 ? `<span class="text-muted fw-bold">Rs 0.00</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    $("#tbodyRows").html(rowsHtml);
                    $("#mobPartiesContainer").html(mobHtml);

                    // Totals
                    if (res.totals) {
                        let t = res.totals;
                        let totalsHtml = `
                            <tr>
                                <td colspan="6" class="text-end text-dark">TOTAL:</td>
                                ${reportType !== 'PAYABLE' ? `<td class="text-end text-primary">${fmt(t.receivable)}</td>` : ''}
                                ${reportType !== 'RECEIVABLE' ? `<td class="text-end text-danger">${fmt(t.payable)}</td>` : ''}
                                <td></td>
                            </tr>
                        `;
                        $("#tfootTotals").html(totalsHtml);

                        // Update Stat Pills
                        let net = t.receivable - t.payable;
                        $('#pillReceivable, #mobPillReceivable').text(fmt(t.receivable));
                        $('#pillPayable, #mobPillPayable').text(fmt(t.payable));
                        
                        $('#pillNet, #mobPillNet').text(fmt(net));
                        if(net > 0) {
                            $('#pillNet, #mobPillNet').removeClass('text-danger text-dark').addClass('text-primary');
                        } else if(net < 0) {
                            $('#pillNet, #mobPillNet').removeClass('text-primary text-dark').addClass('text-danger');
                        } else {
                            $('#pillNet, #mobPillNet').removeClass('text-primary text-danger').addClass('text-dark');
                        }
                    }
                } else {
                    let colCount = reportType === 'BOTH' ? 8 : 7;
                    $("#tbodyRows").html(`<tr><td colspan="${colCount}" class="text-center text-muted py-4">No data matching filters.</td></tr>`);
                    $("#mobPartiesContainer").html('<div class="card border-0 shadow-sm rounded-3 text-center py-4 bg-white"><div class="card-body py-4 text-muted"><i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i><p class="small fw-bold mb-0">No Parties Balance Found</p></div></div>');
                    $("#tfootTotals").empty();

                    $('#pillReceivable, #mobPillReceivable').text('Rs 0.00');
                    $('#pillPayable, #mobPillPayable').text('Rs 0.00');
                    $('#pillNet, #mobPillNet').text('Rs 0.00');
                }
            });
        }

        // Export to Excel function
        function exportToExcel() {
            let table = document.getElementById("partiesTable");
            let rows = Array.from(table.rows);
            let csvContent = "data:text/csv;charset=utf-8,";

            rows.forEach(function(row) {
                let rowData = [];
                Array.from(row.cells).forEach(function(cell) {
                    if (window.getComputedStyle(cell).display !== 'none') {
                        let text = cell.innerText.replace(/,/g, '').replace(/\n/g, ' ');
                        rowData.push('"' + text + '"');
                    }
                });
                csvContent += rowData.join(",") + "\r\n";
            });

            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            let filename = "parties_balance_report_" + new Date().toISOString().slice(0,10) + ".csv";
            
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        $(".btnSearchTrigger").click(function() {
            loadReport();
        });

        $(".btnResetTrigger").click(function() {
            $(".reportTypeInput").val('BOTH');
            $(".partyNameInput").val('');
            $(".mobileInput").val('');
            $(".zoneInput").val('');
            $(".addressInput").val('');
            $(".showZeroInput").prop('checked', false);
            loadReport();
        });

        $(".btnPrintReport").click(function() {
            window.print();
        });

        $(".btnExcelTrigger").click(function() {
            exportToExcel();
        });

        // Load report on initial page load
        loadReport();
    });
</script>
@endsection
