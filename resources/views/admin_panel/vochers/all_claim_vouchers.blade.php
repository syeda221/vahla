@extends('admin_panel.layout.app')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

    <style>
        .custom-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.025em;
        }
        .page-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn-create {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }
        .btn-create:hover {
            background-color: #1d4ed8;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.25);
        }
        .table-custom {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #e2e8f0;
            border-top: none;
        }
        .table-custom tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            color: #334155;
            font-size: 0.875rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .action-btn-print {
            color: #0284c7;
            background: #e0f2fe;
        }
        .action-btn-print:hover {
            background: #0284c7;
            color: white;
        }
        .action-btn-edit {
            color: #d97706;
            background: #fef3c7;
        }
        .action-btn-edit:hover {
            background: #d97706;
            color: white;
        }
        .action-btn-delete {
            color: #dc2626;
            background: #fee2e2;
        }
        .action-btn-delete:hover {
            background: #dc2626;
            color: white;
        }
        .badge-amount {
            background: #f0fdf4;
            color: #16a34a;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid #bbf7d0;
        }
        
        /* DataTables Customization */
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
            outline: none;
            box-shadow: none;
        }
        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.1);
        }
        .dataTables_info, .dataTables_paginate {
            padding: 1rem 1.25rem !important;
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4 px-md-4">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h1 class="page-title mb-0">Customer Claim Vouchers</h1>
                        <p class="page-subtitle mb-0">Manage and track all customer claim payments efficiently.</p>
                    </div>
                    @can('receipts.voucher.view')
                        <a href="{{ route('claim_payment') }}" class="btn-create text-decoration-none d-inline-flex align-items-center">
                            <i class="bi bi-plus-lg me-2"></i> New Claim Payment
                        </a>
                    @endcan
                </div>
                
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
                        <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center" role="alert" style="background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="custom-card">
                    <div class="table-responsive">
                        <table id="example" class="table table-custom w-100">
                            <thead>
                                <tr>
                                    <th width="8%">ID</th>
                                    <th width="12%">Voucher No</th>
                                    <th width="12%">Date</th>
                                    <th width="20%">Customer</th>
                                    <th width="25%">Remarks</th>
                                    <th width="12%" class="text-end">Amount</th>
                                    <th width="11%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($claims as $item)
                                    @php
                                        $amounts = json_decode($item->amount, true);
                                        $amount = is_array($amounts)
                                            ? (float) ($amounts[0] ?? 0)
                                            : (float) $item->amount;
                                    @endphp
                                    <tr>
                                        <td><span class="text-muted fw-medium">#{{ $item->id }}</span></td>
                                        <td><span class="fw-semibold text-dark">{{ $item->evid }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::parse($item->entry_date)->format('d M, Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->party_name }}</div>
                                        </td>
                                        <td>
                                            <span class="text-muted text-truncate d-inline-block" style="max-width: 250px;" title="{{ $item->remarks }}">
                                                {{ $item->remarks }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge-amount">{{ number_format($amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('expenseprint', $item->id) }}" target="_blank"
                                                    class="action-btn action-btn-print" title="Print Voucher">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                @can('receipts.voucher.view')
                                                <a href="{{ route('claim_payment.edit', $item->id) }}" class="action-btn action-btn-edit" title="Edit Claim">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @endcan
                                                @can('expense.voucher.delete')
                                                <form action="{{ route('expense_vouchers.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="action-btn action-btn-delete delete-btn" title="Delete Claim">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable({
                order: [[0, 'desc']]
            });
        }

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this Claim Voucher?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
