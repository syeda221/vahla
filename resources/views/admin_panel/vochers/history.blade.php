@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

<style>
:root {
    --vh-primary: #2563eb;
    --vh-primary-hover: #1d4ed8;
    --vh-primary-light: rgba(37,99,235,0.08);
    --vh-bg: #f8fafc;
    --vh-card-bg: #ffffff;
    --vh-border: #e2e8f0;
    --vh-text: #0f172a;
    --vh-text-muted: #64748b;
    --vh-success: #10b981;
    --vh-danger: #ef4444;
    --vh-warning: #f59e0b;
    --vh-card-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
    --vh-card-shadow-hover: 0 4px 12px rgba(0,0,0,0.08);
}

.main-content {
    padding-bottom: 40px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Summary Cards */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}

.summary-card {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: var(--vh-card-shadow);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-card:hover {
    box-shadow: var(--vh-card-shadow-hover);
    transform: translateY(-2px);
}

.summary-card .label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--vh-text-muted);
}

.summary-card .value {
    font-size: 22px;
    font-weight: 800;
    margin-top: 4px;
    line-height: 1.2;
}

.summary-card .icon {
    font-size: 28px;
    opacity: 0.25;
}

/* Filter Type Cards */
.filter-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-card-btn {
    background: var(--vh-card-bg);
    border: 1.5px solid var(--vh-border);
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 700;
    color: var(--vh-text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    letter-spacing: 0.2px;
}

.filter-card-btn:hover {
    border-color: var(--vh-primary);
    color: var(--vh-primary);
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(37,99,235,0.08);
}

.filter-card-btn.active {
    border-color: var(--vh-primary);
    background: var(--vh-primary);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37,99,235,0.25);
}

/* Filter Section */
.filter-section {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 24px;
    box-shadow: var(--vh-card-shadow);
}

.filter-section .form-label {
    color: var(--vh-text-muted);
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: 0.2px;
}

.filter-section .form-control,
.filter-section .form-select {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    color: var(--vh-text);
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.filter-section .form-control:focus,
.filter-section .form-select:focus {
    border-color: var(--vh-primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}

/* DataTable Card */
.data-card {
    background: var(--vh-card-bg);
    border: 1px solid var(--vh-border);
    border-radius: 16px;
    padding: 0;
    box-shadow: var(--vh-card-shadow);
    overflow: hidden;
}

.data-card .card-header-custom {
    padding: 16px 24px;
    border-bottom: 1px solid var(--vh-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}

.data-card .card-header-custom h5 {
    margin: 0;
    font-weight: 800;
    font-size: 15px;
    color: var(--vh-text);
    display: flex;
    align-items: center;
    gap: 8px;
}

.data-card .table-wrap {
    padding: 16px 24px 24px;
}

/* DataTable Overrides */
#voucherHistoryTable {
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0;
    width: 100% !important;
}

#voucherHistoryTable thead th {
    background: #f8fafc;
    color: var(--vh-text);
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 12px 10px;
    border-bottom: 2px solid var(--vh-border);
    white-space: nowrap;
}

#voucherHistoryTable tbody td {
    padding: 11px 10px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: var(--vh-text);
}

#voucherHistoryTable tbody tr:hover {
    background: #f8fafc;
}

#voucherHistoryTable .badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
}

/* Action Buttons */
.action-group {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.action-group .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 13px;
    transition: all 0.15s ease;
}

.action-group .btn:hover {
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 991px) {
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="main-content container-fluid px-3 px-lg-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-0" style="color:var(--vh-text);font-weight:800;font-size:22px;">All Vouchers</h4>
            <p class="text-muted mb-0 small">Unified history for Expense, Receipts (Payment In), and Payment Out vouchers</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle px-4 py-2" type="button" id="newVoucherDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:10px;font-weight:700;font-size:13px;background:var(--vh-primary);border:0;box-shadow:0 4px 12px rgba(37,99,235,0.25);">
                <i class="fas fa-plus me-1"></i> New Voucher
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="newVoucherDropdown" style="border-radius:12px; font-size:13px; font-weight:600; padding:8px;">
                @can('expense.voucher.create')
                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('expense_vochers') }}"><i class="fas fa-file-invoice text-danger me-2"></i> Add Expense Voucher</a></li>
                @endcan
                @can('receipts.voucher.create')
                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('recepit_vochers') }}"><i class="fas fa-receipt text-success me-2"></i> Add Receipt Voucher (Payment In)</a></li>
                @endcan
                @can('payment.voucher.create')
                    <li><a class="dropdown-item py-2 rounded-2" href="{{ route('Payment_vochers') }}"><i class="fas fa-hand-holding-dollar text-warning me-2"></i> Add Payment Voucher (Payment Out)</a></li>
                @endcan
            </ul>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-grid" id="summaryCards">
        <div class="summary-card">
            <div>
                <div class="label">Total Vouchers</div>
                <div class="value" id="sumTotal" style="color:var(--vh-primary);">0.00</div>
            </div>
            <div class="icon"><i class="fas fa-receipt" style="color:var(--vh-primary);"></i></div>
        </div>
        <div class="summary-card">
            <div>
                <div class="label">Expense</div>
                <div class="value" id="sumExpense" style="color:var(--vh-danger);">0.00</div>
            </div>
            <div class="icon"><i class="fas fa-arrow-up" style="color:var(--vh-danger);"></i></div>
        </div>
        <div class="summary-card">
            <div>
                <div class="label">Payment In</div>
                <div class="value" id="sumPaymentIn" style="color:var(--vh-success);">0.00</div>
            </div>
            <div class="icon"><i class="fas fa-arrow-right" style="color:var(--vh-success);"></i></div>
        </div>
        <div class="summary-card">
            <div>
                <div class="label">Payment Out</div>
                <div class="value" id="sumPaymentOut" style="color:var(--vh-warning);">0.00</div>
            </div>
            <div class="icon"><i class="fas fa-arrow-left" style="color:var(--vh-warning);"></i></div>
        </div>
    </div>

    {{-- Filter Type Buttons --}}
    <div class="filter-cards" id="typeFilterGroup">
        <button class="filter-card-btn active" data-type="all"><i class="fas fa-list-ul me-1"></i> All Vouchers</button>
        <button class="filter-card-btn" data-type="expense"><i class="fas fa-arrow-up text-danger me-1"></i> Expense</button>
        <button class="filter-card-btn" data-type="payment_in"><i class="fas fa-arrow-right text-success me-1"></i> Payment In (Receipts)</button>
        <button class="filter-card-btn" data-type="payment_out"><i class="fas fa-arrow-left text-warning me-1"></i> Payment Out (Payments)</button>
    </div>

    {{-- Advanced Filters --}}
    <div class="filter-section">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" id="filterFromDate">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" id="filterToDate">
            </div>
            <div class="col-md-2">
                <label class="form-label">Party Type</label>
                <select class="form-select" id="filterPartyType">
                    <option value="">All Parties</option>
                    <option value="customer">Customer</option>
                    <option value="vendor">Vendor</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Account</label>
                <select class="form-select" id="filterAccount">
                    <option value="">All Accounts</option>
                    @if(isset($accounts))
                        @foreach($accounts as $ac)
                            <option value="{{ $ac->id }}">{{ $ac->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Min Amt</label>
                <input type="number" step="0.01" class="form-control" id="filterMinAmount" placeholder="Min">
            </div>
            <div class="col-md-1">
                <label class="form-label">Max Amt</label>
                <input type="number" step="0.01" class="form-control" id="filterMaxAmount" placeholder="Max">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm w-100" id="applyFilters" style="border-radius:8px;font-weight:700;background:var(--vh-primary);border:0;height:35px;">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100" id="resetFilters" style="border-radius:8px;font-weight:700;height:35px;">
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- DataTable Card --}}
    <div class="data-card">
        <div class="card-header-custom">
            <h5><i class="fas fa-list text-primary"></i> Vouchers List</h5>
        </div>
        <div class="table-wrap">
            <div class="table-responsive">
                <table id="voucherHistoryTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Voucher No</th>
                            <th class="text-center">Type</th>
                            <th>Date</th>
                            <th>Party</th>
                            <th>Details</th>
                            <th class="text-end">Amount</th>
                            <th>Remarks</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@section('js')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function() {

    let table = $('#voucherHistoryTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: '{{ route("voucher.history.data") }}',
            data: function(d) {
                d.type = $('#typeFilterGroup .filter-card-btn.active').data('type') || 'all';
                d.from_date = $('#filterFromDate').val();
                d.to_date = $('#filterToDate').val();
                d.party_type = $('#filterPartyType').val();
                d.account_id = $('#filterAccount').val();
                d.min_amount = $('#filterMinAmount').val();
                d.max_amount = $('#filterMaxAmount').val();
            },
            dataSrc: function(json) {
                if (json.summary) {
                    $('#sumTotal').text(numberFormat(json.summary.total_amount));
                    $('#sumExpense').text(numberFormat(json.summary.total_expense));
                    $('#sumPaymentIn').text(numberFormat(json.summary.total_payment_in));
                    $('#sumPaymentOut').text(numberFormat(json.summary.total_payment_out));
                }
                return json.data;
            }
        },
        columns: [
            { 
                data: 'voucher_no',
                render: function(v, t, r) {
                    return '<span class="fw-bold text-primary">' + (v || '-') + '</span>';
                }
            },
            {
                data: 'type_label',
                className: 'text-center',
                render: function(v, t, r) {
                    let badge = 'bg-secondary';
                    if (r.source === 'expense') badge = 'bg-danger text-white';
                    else if (r.source === 'payment_in') badge = 'bg-success text-white';
                    else if (r.source === 'payment_out') badge = 'bg-warning text-dark';
                    return '<span class="badge ' + badge + '">' + (v || '-') + '</span>';
                }
            },
            { data: 'date' },
            {
                data: null,
                render: function(r) {
                    let html = '<strong>' + (r.party_name || '-') + '</strong>';
                    if (r.party_type_label && r.party_type_label !== '-') {
                        html += ' <small class="text-muted d-block">(' + r.party_type_label + ')</small>';
                    }
                    return html;
                }
            },
            {
                data: 'detail',
                render: function(v) { return v || '-'; }
            },
            { 
                data: 'amount', 
                render: function(v) { return '<span class="fw-bold">Rs ' + numberFormat(v) + '</span>'; }, 
                className: 'text-end' 
            },
            { 
                data: 'remarks', 
                render: function(v) { return v || '-'; } 
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(r) {
                    var btns = [];
                    if (r.print_url) {
                        btns.push({ 
                            icon: 'bi-printer', 
                            cls: 'btn-outline-primary', 
                            url: r.print_url, 
                            title: 'Print Voucher', 
                            target: '_blank' 
                        });
                    }
                    if (r.delete_url) {
                        btns.push({ 
                            icon: 'bi-trash', 
                            cls: 'btn-outline-danger', 
                            url: r.delete_url, 
                            title: 'Delete Voucher', 
                            isDelete: true, 
                            deleteMethod: r.delete_method || 'DELETE' 
                        });
                    }
                    if (btns.length === 0) return '-';
                    var html = '<div class="action-group">';
                    $.each(btns, function(i, b) {
                        if (b.isDelete) {
                            html += '<button type="button" class="btn btn-sm ' + b.cls + '" title="' + b.title + '" data-delete-url="' + b.url + '" data-delete-method="' + b.deleteMethod + '"><i class="bi ' + b.icon + '"></i></button>';
                        } else {
                            var tgt = b.target ? ' target="' + b.target + '"' : '';
                            html += '<a href="' + b.url + '" class="btn btn-sm ' + b.cls + '" title="' + b.title + '"' + tgt + '><i class="bi ' + b.icon + '"></i></a>';
                        }
                    });
                    html += '</div>';
                    return html;
                }
            }
        ],
        order: [[2, 'desc']],
        language: {
            searchPlaceholder: 'Search vouchers...',
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...'
        }
    });

    // Delete handler
    $('#voucherHistoryTable').on('click', '[data-delete-url]', function(e) {
        e.preventDefault();
        var url = $(this).data('delete-url');
        var method = $(this).data('delete-method') || 'DELETE';
        var row = table.row($(this).closest('tr'));
        var label = row.data() ? row.data().voucher_no + ' (' + row.data().type_label + ')' : 'this voucher';
        
        Swal.fire({
            title: 'Delete Voucher?',
            html: 'Are you sure you want to delete <strong>' + label + '</strong>?<br>This will reverse related accounting entries.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: url,
                type: 'POST',
                data: { _method: method, _token: '{{ csrf_token() }}' },
                success: function(resp) {
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: resp.message || 'Voucher deleted successfully.', timer: 2000, showConfirmButton: false });
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to delete voucher.' });
                }
            });
        });
    });

    // Type filter buttons
    $('#typeFilterGroup').on('click', '.filter-card-btn', function() {
        $('#typeFilterGroup .filter-card-btn').removeClass('active');
        $(this).addClass('active');
        table.ajax.reload();
    });

    $('#applyFilters').on('click', function() { table.ajax.reload(); });
    $('#resetFilters').on('click', function() {
        $('#filterFromDate, #filterToDate, #filterMinAmount, #filterMaxAmount').val('');
        $('#filterPartyType, #filterAccount').val('');
        table.ajax.reload();
    });

    $('#filterFromDate, #filterToDate, #filterPartyType, #filterAccount, #filterMinAmount, #filterMaxAmount').on('keypress', function(e) {
        if (e.which === 13) { $('#applyFilters').click(); }
    });

    function numberFormat(v) {
        v = parseFloat(v) || 0;
        return v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

});
</script>

@endsection
