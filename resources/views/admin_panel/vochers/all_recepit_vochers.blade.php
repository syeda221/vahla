@extends('admin_panel.layout.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

    <style>
        /* Mobile horizontal scroll prevention & responsiveness */
        @media (max-width: 991.98px) {
            html, body {
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .main-content,
            .main-content-inner,
            .container-fluid {
                max-width: 100% !important;
                overflow-x: hidden !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
            .page-header-box {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
            }
            .page-header-box .btn {
                width: 100% !important;
                justify-content: center !important;
            }
            .card {
                border-radius: 12px !important;
                max-width: 100% !important;
            }
            .card-body {
                padding: 12px !important;
                max-width: 100% !important;
            }
            .mobile-table-hidden {
                display: none !important;
            }
            .table-responsive {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .table-responsive table {
                min-width: 780px;
                white-space: nowrap;
            }
            /* DataTables responsive styling */
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left !important;
                margin-bottom: 8px;
            }
            div.dataTables_wrapper div.dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: 4px;
            }
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: left !important;
                padding: 8px 0;
            }
        }

        /* Desktop view constraints */
        @media (min-width: 992px) {
            #mobileCardsWrapper {
                display: none !important;
            }
            #desktopTableWrapper {
                display: block !important;
            }
            .mobile-view-toggle {
                display: none !important;
            }
        }

        /* Mobile Card Styling */
        .voucher-mcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            width: 100%;
            box-sizing: border-box;
        }
        .voucher-mcard-header {
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .voucher-search-box {
            position: relative;
            margin-bottom: 12px;
        }
        .voucher-search-box input {
            border-radius: 8px;
            padding-left: 35px;
            font-size: 0.88rem;
            height: 38px;
            border: 1px solid #cbd5e1;
            width: 100%;
        }
        .voucher-search-box i {
            position: absolute;
            left: 12px;
            top: 11px;
            color: #94a3b8;
        }
    </style>
    
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 page-header-box">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Receipts Vouchers</h4>
                        <p class="text-muted mb-0 small">View and manage all receipts vouchers</p>
                    </div>
                    @can('receipts.voucher.create')
                        <a class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center" href="{{ route('recepit_vochers') }}" style="height: 38px; border-radius: 6px;">
                            <i class="fas fa-plus mr-1" style="margin-right: 5px;"></i> Add Receipts Voucher
                        </a>
                    @endcan
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-body">

                        {{-- Mobile View Toggle (Cards vs Table) --}}
                        <div class="d-flex justify-content-between align-items-center mobile-view-toggle pb-3 mb-2 border-bottom">
                            <span class="small text-muted font-weight-bold">
                                <i class="fas fa-th-list mr-1"></i> View Mode
                            </span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnShowCards">
                                    <i class="fas fa-th-large mr-1"></i> Cards
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnShowTable">
                                    <i class="fas fa-table mr-1"></i> Table
                                </button>
                            </div>
                        </div>

                        {{-- Mobile Cards View --}}
                        <div id="mobileCardsWrapper">
                            <div class="voucher-search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="mobileSearchInput" class="form-control" placeholder="Search receipt vouchers...">
                            </div>

                            <div class="mobile-voucher-list">
                                @forelse ($receipts as $item)
                                    <div class="voucher-mcard" data-search="{{ strtolower($item->voucher_no . ' ' . $item->party_name . ' ' . ($item->type_label ?? '') . ' ' . ($item->remarks ?? '') . ' ' . ($item->payment_from ?? '')) }}">
                                        <div class="voucher-mcard-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary text-white font-weight-bold">#{{ $item->voucher_no }}</span>
                                                <span class="badge bg-info text-dark ml-1">{{ ucfirst($item->payment_from ?? 'Receipt') }}</span>
                                            </div>
                                            <div>
                                                @if ($item->status == 'posted')
                                                    <span class="badge bg-success">Posted</span>
                                                @elseif($item->status == 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($item->status) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="voucher-mcard-body mt-2">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <span class="text-muted small">Party / Account:</span>
                                                <div class="text-right text-end">
                                                    <span class="font-weight-bold text-dark">{{ $item->party_name }}</span>
                                                    @if($item->type_label)
                                                        <small class="d-block text-muted">{{ $item->type_label }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-muted small">Date:</span>
                                                <span class="text-dark small font-weight-bold">{{ $item->date ? $item->date->format('d/m/Y') : '-' }}</span>
                                            </div>
                                            @if($item->remarks)
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="text-muted small">Remarks:</span>
                                                    <span class="text-dark small text-right text-end" style="max-width: 65%;">{{ Str::limit($item->remarks, 60) }}</span>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <span class="text-muted small font-weight-bold">Amount:</span>
                                                <span class="text-success font-weight-bold" style="font-size: 1.1rem;">{{ number_format($item->total_amount, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="voucher-mcard-footer d-flex justify-content-end align-items-center mt-2 pt-2 border-top" style="gap: 8px;">
                                            <a href="{{ route('print', $item->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary" title="Print">
                                                <i class="bi bi-printer mr-1"></i> Print
                                            </a>
                                            @can('receipts.voucher.delete')
                                                <form action="{{ route('receipt_vouchers.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete">
                                                        <i class="bi bi-trash mr-1"></i> Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <p class="mb-0">No receipt vouchers found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Desktop Table Wrapper --}}
                        <div id="desktopTableWrapper" class="mobile-table-hidden">
                            <div class="table-responsive mt-2 mb-4">
                                <table id="example" class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Voucher No</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Party / Account</th>
                                            <th>Remarks</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-center">Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($receipts as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-bold text-primary">{{ $item->voucher_no }}</span>
                                                </td>
                                                <td>{{ $item->date ? $item->date->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-info text-dark">{{ ucfirst($item->payment_from ?? 'Receipt') }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $item->party_name }}</strong>
                                                    <small class="d-block text-muted">{{ $item->type_label }}</small>
                                                </td>
                                                <td>{{ Str::limit($item->remarks, 50) }}</td>
                                                <td class="text-end fw-bold">{{ number_format($item->total_amount, 2) }}</td>
                                                <td class="text-center">
                                                    @if ($item->status == 'posted')
                                                        <span class="badge bg-success">Posted</span>
                                                    @elseif($item->status == 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ ucfirst($item->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a href="{{ route('print', $item->id) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary" title="Print">
                                                            <i class="bi bi-printer"></i>
                                                        </a>
                                                        @can('receipts.voucher.delete')
                                                        <form action="{{ route('receipt_vouchers.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete">
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

        // Mobile View Switcher (Cards vs Table)
        $('#btnShowCards').on('click', function() {
            $(this).addClass('active');
            $('#btnShowTable').removeClass('active');
            $('#mobileCardsWrapper').show();
            $('#desktopTableWrapper').addClass('mobile-table-hidden').hide();
        });

        $('#btnShowTable').on('click', function() {
            $(this).addClass('active');
            $('#btnShowCards').removeClass('active');
            $('#mobileCardsWrapper').hide();
            $('#desktopTableWrapper').removeClass('mobile-table-hidden').show();
        });

        // Mobile Search Filter
        $('#mobileSearchInput').on('keyup', function() {
            let val = $(this).val().toLowerCase();
            $('.mobile-voucher-list .voucher-mcard').each(function() {
                let searchTxt = $(this).attr('data-search') || '';
                $(this).toggle(searchTxt.indexOf(val) > -1);
            });
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this Receipt Voucher?",
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