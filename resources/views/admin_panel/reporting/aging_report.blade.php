@extends('admin_panel.layout.app')
@section('content')
<style>
    .aging-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .aging-table th, .aging-table td { padding: 9px 12px; border: 1px solid #dee2e6; text-align: right; white-space: nowrap; }
    .aging-table th { background: #f8f9fa; font-weight: 600; text-align: center; text-transform: uppercase; font-size: 0.8rem; color: #555; }
    .aging-table td:first-child, .aging-table th:first-child { text-align: left; }
    .aging-table td:nth-child(2) { text-align: center; }

    .aging-table .col-current { background: #e8f5e9; color: #2e7d32; }
    .aging-table .col-15  { background: #fff8e1; color: #f57f17; }
    .aging-table .col-30  { background: #fff3e0; color: #e65100; }
    .aging-table .col-45  { background: #fce4ec; color: #c62828; }
    .aging-table .col-60  { background: #f3e5f5; color: #6a1b9a; }
    .aging-table .col-75  { background: #e3f2fd; color: #0d47a1; }
    .aging-table .col-90  { background: #ffebee; color: #b71c1c; font-weight: 700; }
    .aging-table .col-total { background: #e8eaf6; font-weight: 700; }
    .aging-table tfoot tr td { font-weight: bold; background: #f0f4f8; border-top: 2px solid #aaa; }
    .btn-print { background: #0d6efd; color: white; border: none; padding: 7px 16px; border-radius: 4px; cursor: pointer; }

    .print-header { display: none; }

    @media print {
        @page {
            size: landscape;
            margin: 8mm;
        }
        body, html {
            background: #ffffff !important;
            font-size: 11px !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            width: 100% !important;
        }
        .no-print, header, .sidebar, .navbar, .rt_nav_header, footer, nav, .btn-print, .btn, form, #filtersCard {
            display: none !important;
        }
        .main-content, .container-fluid, .card, .card-body {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 15px;
        }
        .table-responsive {
            overflow: visible !important;
            display: block !important;
            width: 100% !important;
        }
        .aging-table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10px !important;
            table-layout: auto !important;
        }
        .aging-table th, .aging-table td {
            border: 1px solid #64748b !important;
            padding: 5px 6px !important;
        }
        .aging-table th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .aging-table .col-total {
            background-color: #e2e8f0 !important;
            font-weight: 700 !important;
        }
        .aging-table tfoot tr td {
            background-color: #e2e8f0 !important;
            font-weight: 700 !important;
            border-top: 2px solid #000 !important;
        }
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card shadow-sm mb-3 no-print" id="filtersCard">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-semibold">Report Type:</label>
                        <select id="rpt_type" class="form-control">
                            <option value="payable">Account Payable (Vendor)</option>
                            <option value="receivable">Account Receivable (Customer)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted fw-semibold">Current Date:</label>
                        <input type="text" id="as_of_date" class="form-control datepicker-custom" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100 mt-1" id="btnGenerate">Generate</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report -->
        <div class="card shadow-sm">
            <div class="card-body p-4" id="printArea">
                {{-- PRINT HEADER --}}
                <div class="print-header">
                    <h3 style="font-weight: 900; margin: 0; font-size: 18px; text-transform: uppercase;">{{ \App\Models\Setting::get('company_name', 'THREE STARS MEDICAL') }}</h3>
                    <p style="margin: 0; font-size: 11px;">{{ \App\Models\Setting::get('company_address', '') }}</p>
                    <p style="margin: 0; font-size: 11px;">Tel: {{ \App\Models\Setting::get('company_phone', '') }}</p>
                    <h5 style="font-weight: 800; margin-top: 10px; margin-bottom: 4px; text-transform: uppercase;" id="printHeaderTitle">Account Payable Aging Report</h5>
                    <div style="font-size: 11px; font-weight: 600;">As of: <span id="printDisplayDate">{{ date('d/m/Y') }}</span></div>
                </div>

                <div class="d-flex justify-content-between align-items-start mb-3 no-print">
                    <div>
                        <h5 class="fw-bold mb-0" id="reportTitle">Account Payable Aging Report</h5>
                        <small class="text-muted">As of: <span id="displayDate">{{ date('d/m/Y') }}</span></small>
                    </div>
                    <button type="button" class="btn-print no-print" onclick="window.print()"><i class="fa fa-print me-1"></i> Print</button>
                </div>

                <div id="loader" style="display:none; text-align:center; padding:40px;">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">Calculating aging buckets...</p>
                </div>

                <div id="reportContent">
                    <div class="table-responsive">
                        <table class="aging-table">
                            <thead>
                                <tr>
                                    <th rowspan="2">Customer / Vendor Name</th>
                                    <th rowspan="2">Mobile</th>
                                    <th rowspan="2" class="col-total">Total Balance</th>
                                    <th class="col-current" id="hdrCurrent">Current</th>
                                    <th class="col-15">≤ 15 Days</th>
                                    <th class="col-30">≤ 30 Days</th>
                                    <th class="col-45">≤ 45 Days</th>
                                    <th class="col-60">≤ 60 Days</th>
                                    <th class="col-75">≤ 75 Days</th>
                                    <th class="col-90">90 & Above</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRows">
                                <tr><td colspan="10" class="text-center text-muted py-4">Click Generate to load report.</td></tr>
                            </tbody>
                            <tfoot id="tfootTotals"></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    function fmt(v) {
        let n = parseFloat(v || 0);
        if (n === 0) return '<span class="text-muted">—</span>';
        return n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    function fmtTotal(v) {
        let n = parseFloat(v || 0);
        return n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function loadReport() {
        let type    = $('#rpt_type').val();
        let asOf    = $('#as_of_date').val();

        // Update title
        let titleText = (type === 'payable') ? 'Account Payable Aging Report' : 'Account Receivable Aging Report';
        $('#reportTitle').text(titleText);
        $('#printHeaderTitle').text(titleText);

        $('#loader').show();
        $('#reportContent').hide();

        $.get("{{ route('report.aging.fetch') }}", {type: type, as_of_date: asOf}, function(res) {
            $('#loader').hide();
            $('#displayDate').text(res.as_of_date);
            $('#printDisplayDate').text(res.as_of_date);
            $('#hdrCurrent').text(res.as_of_date);

            let html = '';
            if (res.rows.length === 0) {
                html = `<tr><td colspan="10" class="text-center text-muted py-4">No outstanding balances found.</td></tr>`;
            } else {
                res.rows.forEach(r => {
                    html += `<tr>
                        <td>${r.name}</td>
                        <td style="text-align:center">${r.mobile || '—'}</td>
                        <td class="col-total">${fmtTotal(r.total)}</td>
                        <td class="col-current">${fmt(r.current)}</td>
                        <td class="col-15">${fmt(r['15d'])}</td>
                        <td class="col-30">${fmt(r['30d'])}</td>
                        <td class="col-45">${fmt(r['45d'])}</td>
                        <td class="col-60">${fmt(r['60d'])}</td>
                        <td class="col-75">${fmt(r['75d'])}</td>
                        <td class="col-90">${fmt(r['90plus'])}</td>
                    </tr>`;
                });
            }
            $('#tbodyRows').html(html);

            // Totals footer
            let t = res.totals;
            $('#tfootTotals').html(`
                <tr>
                    <td colspan="2">Totals:</td>
                    <td class="col-total">${fmtTotal(t.total)}</td>
                    <td class="col-current">${fmtTotal(t.current)}</td>
                    <td class="col-15">${fmtTotal(t['15d'])}</td>
                    <td class="col-30">${fmtTotal(t['30d'])}</td>
                    <td class="col-45">${fmtTotal(t['45d'])}</td>
                    <td class="col-60">${fmtTotal(t['60d'])}</td>
                    <td class="col-75">${fmtTotal(t['75d'])}</td>
                    <td class="col-90">${fmtTotal(t['90plus'])}</td>
                </tr>
            `);

            $('#reportContent').show();
        });
    }

    $('#btnGenerate').click(loadReport);
    loadReport(); // auto-load
});
</script>
@endsection
