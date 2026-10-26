@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Mobile & Tablet Responsive (<= 991px) */
        @media (max-width: 991.98px) {
            html, body {
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .main-content,
            .main-content-inner,
            .container,
            .container-fluid {
                max-width: 100% !important;
                overflow-x: hidden !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
            .salereturn-header {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .btn-back-purchases {
                width: 100% !important;
                justify-content: center !important;
                height: 40px;
                display: inline-flex !important;
                align-items: center;
                margin-top: 8px;
            }
            .salereturn-card {
                border-radius: 12px !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .mobile-table-hidden {
                display: none !important;
            }
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left !important;
                margin-bottom: 8px;
                padding: 0 14px;
            }
            div.dataTables_wrapper div.dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: 4px;
            }
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: left !important;
                padding: 8px 14px;
            }
        }

        /* Desktop View Constraints (>= 992px) */
        @media (min-width: 992px) {
            #mobileReturnCards {
                display: none !important;
            }
            #desktopReturnTable {
                display: block !important;
            }
            .mobile-view-toggle {
                display: none !important;
            }
        }

        /* Mobile Card Styling (Zero Horizontal Scroll) */
        .mobile-salereturn-cards {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .salereturn-mcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            margin-bottom: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .salereturn-mcard-hdr {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }

        #return-table {
            width: 100% !important;
            min-width: 900px;
        }

        #return-table th {
            white-space: nowrap;
        }

        #return-table td {
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid px-2 px-md-3">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="d-flex justify-content-between align-items-center mb-3 salereturn-header">
                            <div>
                                <h3 class="mb-1 font-weight-bold text-dark"><i class="fas fa-undo-alt text-primary mr-2"></i>Sale Returns</h3>
                                <p class="text-muted mb-0 small">Manage customer sale returns, refunds, and adjustments.</p>
                            </div>
                            <div>
                                <a class="btn btn-primary btn-back-purchases" href="{{ route('sale.index') }}">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to Purchases
                                </a>
                            </div>
                        </div>

                        <div class="border mt-1 shadow-sm rounded-lg bg-white salereturn-card">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-dark"><i class="fas fa-list mr-1 text-muted"></i> All Sale Returns</span>
                                <span class="badge badge-light border text-muted">{{ count($returns) }} Returns</span>
                            </div>

                            {{-- Mobile View Switcher (Cards vs Table) --}}
                            <div class="d-flex justify-content-between align-items-center mobile-view-toggle px-3 pt-3">
                                <span class="small text-muted font-weight-bold">
                                    <i class="fas fa-th-list mr-1"></i> View Mode
                                </span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="btnReturnCards">
                                        <i class="fas fa-th-large mr-1"></i> Cards
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="btnReturnTable">
                                        <i class="fas fa-table mr-1"></i> Table
                                    </button>
                                </div>
                            </div>

                            {{-- Mobile Cards View (Zero Horizontal Scroll) --}}
                            <div id="mobileReturnCards">
                                <div class="mobile-salereturn-cards">
                                    @forelse ($returns as $return)
                                        @php
                                            $isPartialReturn =
                                                $return->sale &&
                                                $return->total_returned < $return->original_net_amount;
                                            $isFullReturn =
                                                $return->sale &&
                                                $return->total_returned >= $return->original_net_amount;
                                        @endphp
                                        <div class="salereturn-mcard">
                                            <div class="salereturn-mcard-hdr">
                                                <div>
                                                    <span class="badge badge-dark mr-1">#{{ $return->id }}</span>
                                                    <strong class="text-dark">{{ $return->return_invoice }}</strong>
                                                    @if ($return->sale)
                                                        <div class="small text-muted">Orig: {{ $return->sale->invoice_no }}</div>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    @if ($isFullReturn)
                                                        <span class="badge badge-danger text-white">Full Return</span>
                                                    @elseif($isPartialReturn)
                                                        <span class="badge badge-warning text-dark font-weight-bold">Partial Return</span>
                                                    @else
                                                        <span class="badge badge-secondary text-white">Standalone</span>
                                                    @endif
                                                    <div class="text-muted small mt-1"><i class="far fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') }}</div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center py-2 px-2 bg-light rounded mb-2 small">
                                                <div>
                                                    <i class="fas fa-user text-muted mr-1"></i>
                                                    <strong class="text-dark">{{ $return->customer->customer_name ?? 'N/A' }}</strong>
                                                </div>
                                                <div>
                                                    <i class="fas fa-warehouse text-muted mr-1"></i>
                                                    <span class="text-secondary">{{ $return->warehouse->warehouse_name ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="salereturn-mcard-stats">
                                                <div class="row no-gutters text-center border-top border-bottom py-2">
                                                    <div class="col-4 border-right pr-1">
                                                        <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase;">Return Amt</div>
                                                        <strong class="text-danger small">-{{ number_format($return->net_amount, 2) }}</strong>
                                                    </div>
                                                    <div class="col-4 border-right px-1">
                                                        <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase;">New Net</div>
                                                        <strong class="text-success small">{{ $return->sale ? number_format($return->new_net_amount, 2) : 'N/A' }}</strong>
                                                    </div>
                                                    <div class="col-4 pl-1">
                                                        <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase;">New Due</div>
                                                        <strong class="text-warning small">{{ $return->sale ? number_format($return->new_due_amount, 2) : 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                                <div class="row no-gutters text-muted small pt-2 px-1">
                                                    <div class="col-6">
                                                        <span>Orig Purchase: </span>
                                                        <strong class="text-dark">{{ $return->sale ? number_format($return->original_net_amount, 2) : 'N/A' }}</strong>
                                                    </div>
                                                    <div class="col-6 text-right">
                                                        <span>Total Returned: </span>
                                                        <strong class="text-danger">{{ $return->sale ? number_format($return->total_returned, 2) : 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3 pt-2 border-top">
                                                <a href="{{ route('sale.return.view', $return->id) }}" class="btn btn-primary btn-sm btn-block d-flex align-items-center justify-content-center" style="height: 38px;">
                                                    <i class="fas fa-eye mr-1"></i> View Details
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4 bg-light rounded border">
                                            <i class="fas fa-inbox fa-2x mb-2 text-secondary d-block"></i>
                                            No sale returns found.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Desktop Table View --}}
                            <div class="table-responsive p-3 mobile-table-hidden" id="desktopReturnTable">
                                <table id="return-table" class="table table-bordered text-center table-hover">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Invoice #</th>
                                            <th>Customer</th>
                                            <th>Warehouse</th>
                                            <th>Return Date</th>
                                            <th>Return Amount</th>
                                            <th>Original Purchase</th>
                                            <th>Total Returned</th>
                                            <th>New Net Amount</th>
                                            <th>New Due</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($returns as $return)
                                            @php
                                                $isPartialReturn =
                                                    $return->sale &&
                                                    $return->total_returned < $return->original_net_amount;
                                                $isFullReturn =
                                                    $return->sale &&
                                                    $return->total_returned >= $return->original_net_amount;
                                            @endphp
                                            <tr>
                                                <td>{{ $return->id }}</td>
                                                <td>
                                                    <strong>{{ $return->return_invoice }}</strong>
                                                    @if ($return->sale)
                                                        <br><small class="text-muted">Orig:
                                                            {{ $return->sale->invoice_no }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $return->customer->customer_name ?? 'N/A' }}</td>
                                                <td>{{ $return->warehouse->warehouse_name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') }}</td>

                                                {{-- Return Amount --}}
                                                <td class="text-danger font-weight-bold">
                                                    -{{ number_format($return->net_amount, 2) }}
                                                </td>

                                                {{-- Original Purchase Amount --}}
                                                <td>
                                                    @if ($return->sale)
                                                        {{ number_format($return->original_net_amount, 2) }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>

                                                {{-- Total Returned --}}
                                                <td class="text-danger font-weight-bold">
                                                    @if ($return->sale)
                                                        {{ number_format($return->total_returned, 2) }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>

                                                {{-- New Net Amount --}}
                                                <td class="text-success font-weight-bold">
                                                    @if ($return->sale)
                                                        {{ number_format($return->new_net_amount, 2) }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>

                                                {{-- New Due Amount --}}
                                                <td class="text-warning font-weight-bold">
                                                    @if ($return->sale)
                                                        {{ number_format($return->new_due_amount, 2) }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>

                                                {{-- Status Badge --}}
                                                <td>
                                                    @if ($isFullReturn)
                                                        <span class="badge badge-danger text-white">Full Return</span>
                                                    @elseif($isPartialReturn)
                                                        <span class="badge badge-warning text-dark font-weight-bold">Partial Return</span>
                                                    @else
                                                        <span class="badge badge-secondary text-white">Standalone</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <a href="{{ route('sale.return.view', $return->id) }}"
                                                        class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Scripts --}}
                        <script>
                            $(document).ready(function() {
                                if ($.fn.DataTable && $('#return-table').length) {
                                    $('#return-table').DataTable({
                                        "pageLength": 10,
                                        "lengthMenu": [5, 10, 25, 50, 100],
                                        "order": [
                                            [0, 'desc']
                                        ],
                                        "language": {
                                            "search": "Search Return:",
                                            "lengthMenu": "Show _MENU_ entries"
                                        }
                                    });
                                }

                                // Mobile View Switcher (Cards vs Table)
                                $('#btnReturnCards').on('click', function() {
                                    $(this).addClass('active');
                                    $('#btnReturnTable').removeClass('active');
                                    $('#mobileReturnCards').show();
                                    $('#desktopReturnTable').addClass('mobile-table-hidden').hide();
                                });

                                $('#btnReturnTable').on('click', function() {
                                    $(this).addClass('active');
                                    $('#btnReturnCards').removeClass('active');
                                    $('#mobileReturnCards').hide();
                                    $('#desktopReturnTable').removeClass('mobile-table-hidden').show();
                                });
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
