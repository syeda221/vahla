@extends('admin_panel.layout.app')

@php
    $selectedCust = null;
    if (request('customer_id')) {
        $selectedCust = $customers->firstWhere('id', request('customer_id'));
    }
    $pageTitle = $selectedCust ? ($selectedCust->customer_name . ' - Ledger') : 'Customer - Ledger';
@endphp

@section('title', $pageTitle)

@section('content')
    <style>
        /* Outer page overflow prevention on mobile (Fix horizontal scroll) */
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
            .ledger-card {
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .ledger-card .card-body {
                padding: 12px !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .ledger-header-wrapper {
                flex-direction: column !important;
                align-items: stretch !important;
                margin-bottom: 14px !important;
            }
            .ledger-header-actions {
                width: 100% !important;
                display: flex !important;
                margin-top: 10px;
            }
            .ledger-header-actions .btn {
                flex: 1 1 0 !important;
                justify-content: center !important;
                height: 38px;
                font-size: 0.85rem;
            }
            .summary-val {
                font-size: 1.25rem !important;
            }
            .table-ledger th,
            .table-ledger td {
                padding: 8px 10px !important;
                font-size: 0.82rem !important;
                white-space: nowrap;
            }
            .table-ledger th {
                font-size: 0.78rem !important;
            }
        }

        /* Desktop view styling */
        .ledger-card {
            border-top: 3px solid #0d6efd;
        }

        .table-ledger th {
            background-color: #212529 !important;
            color: #fff !important;
        }

        .balance-positive {
            color: #198754;
            font-weight: 700;
        }

        .balance-neutral {
            color: #6c757d;
            font-weight: 700;
        }

        /* Filter Box styling */
        .ledger-filter-container {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
        }

        /* Select2 responsive constraint */
        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
        }
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 4px 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            padding-left: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }
        .select2-dropdown {
            max-width: 92vw !important;
        }

        /* Transaction Table Wrapper */
        .ledger-table-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden;
        }

        .ledger-table-wrapper .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 0;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .table-ledger {
            width: 100% !important;
            min-width: 680px;
            margin-bottom: 0 !important;
        }

        /* Desktop vs Mobile Toggle */
        @media (min-width: 992px) {
            #mobileLedgerCards {
                display: none !important;
            }
            #desktopLedgerTable {
                display: block !important;
            }
            .mobile-view-toggle {
                display: none !important;
            }
        }

        @media (max-width: 991.98px) {
            .mobile-table-hidden {
                display: none !important;
            }
        }

        /* Mobile Cards Styling (Zero horizontal overflow) */
        .transaction-mcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .transaction-mcard-hdr {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }

        /* Print Media Styling */
        @media print {
            body { background: #fff !important; }
            .no-print, header, .sidebar, .navbar, .rt_nav_header, footer, form, .btn, .ledger-header-actions, .mobile-view-toggle, #mobileLedgerCards, .ledger-filter-container {
                display: none !important;
            }
            #desktopLedgerTable {
                display: block !important;
            }
            .card { border: none !important; box-shadow: none !important; }
            .table-ledger { width: 100% !important; min-width: 100% !important; }
            .table-ledger th { background-color: #eee !important; color: #000 !important; }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid mt-2 mt-md-4">

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 ledger-header-wrapper">
                    <div>
                        <h4 class="mb-1 text-primary font-weight-bold"><i class="bi bi-people"></i> Customer Ledger (Statement)</h4>
                        <p class="text-muted mb-0 small">Track all customer transactions, invoices, and receipts.</p>
                    </div>
                    <div class="d-flex align-items-center ledger-header-actions">
                        <button type="button" onclick="printCustomerLedger()" class="btn btn-outline-primary mr-2"><i class="bi bi-printer"></i>
                            Print</button>
                        <a href="{{ route('view_all') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                            Back to Accounts</a>
                    </div>
                </div>

                <div class="card shadow-sm ledger-card">
                    <div class="card-body p-3 p-md-4">

                        <!-- Filters -->
                        <div class="ledger-filter-container bg-light rounded border p-3 mb-3 mb-md-4">
                            <form method="GET" action="{{ route('customers.ledger') }}">
                                <div class="form-row align-items-end">
                                    <div class="col-12 col-md-4 mb-2 mb-md-0">
                                        <label class="form-label font-weight-bold small mb-1 text-dark">Select Customer</label>
                                        <select name="customer_id" class="form-control select2">
                                            <option value="">-- All Customers --</option>
                                            @foreach ($customers as $cust)
                                                <option value="{{ $cust->id }}"
                                                    {{ request('customer_id') == $cust->id ? 'selected' : '' }}>
                                                    {{ $cust->customer_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                        <label class="form-label font-weight-bold small mb-1 text-dark">From Date</label>
                                        <input type="text" name="from_date" value="{{ request('from_date') }}"
                                            class="form-control datepicker-custom" placeholder="YYYY-MM-DD">
                                    </div>
                                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                                        <label class="form-label font-weight-bold small mb-1 text-dark">To Date</label>
                                        <input type="text" name="to_date" value="{{ request('to_date') }}" class="form-control datepicker-custom" placeholder="YYYY-MM-DD">
                                    </div>
                                    <div class="col-12 col-md-2 mt-2 mt-md-0">
                                        <div class="d-flex w-100">
                                            <button type="submit" class="btn btn-primary flex-grow-1 mr-2" style="height: 38px;"><i class="bi bi-filter"></i>
                                                Filter</button>
                                            <a href="{{ route('customers.ledger') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 38px;" title="Reset Filters"><i
                                                    class="bi bi-arrow-clockwise"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if(request('customer_id'))
                        <!-- Summary Cards -->
                        <div class="form-row mb-3 mb-md-4">
                            <div class="col-12 col-sm-6 col-md-4 mb-2 mb-md-0">
                                <div class="card border-0 shadow-sm h-100 bg-light" style="border-radius: 12px;">
                                    <div class="card-body p-3 p-md-4">
                                        <div class="text-secondary text-uppercase small font-weight-bold mb-2">Opening Balance</div>
                                        <div class="h4 font-weight-bold text-dark mb-0 summary-val">Rs. {{ number_format(abs($opening_balance ?? 0), 2) }} <small class="text-muted" style="font-size: 0.85rem;">{{ ($opening_balance ?? 0) >= 0 ? 'Dr' : 'Cr' }}</small></div>
                                        <div class="small text-muted mt-1">As of {{ \Carbon\Carbon::parse(request('from_date', '2000-01-01'))->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 mb-2 mb-md-0">
                                @php
                                    $cb = $closing_balance ?? 0;
                                @endphp
                                <div class="card border-0 shadow-sm h-100 {{ $cb > 0 ? 'bg-danger text-white' : ($cb < 0 ? 'bg-success text-white' : 'bg-primary text-white') }}" style="border-radius: 12px;">
                                    <div class="card-body p-3 p-md-4">
                                        <div class="text-white-50 text-uppercase small font-weight-bold mb-2">Closing Balance</div>
                                        <div class="h4 font-weight-bold mb-0 summary-val">Rs. {{ number_format(abs($cb), 2) }} <small class="text-white-50" style="font-size: 0.85rem;">{{ $cb >= 0 ? 'Dr' : 'Cr' }}</small></div>
                                        <div class="small text-white-50 mt-1">{{ $cb > 0 ? 'Receivable (Customer Owes)' : ($cb < 0 ? 'Advance (We Owe)' : 'Settled') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 mt-2 mt-md-0">
                                <div class="card border-0 shadow-sm h-100 bg-light" style="border-radius: 12px;">
                                    <div class="card-body p-3 p-md-4">
                                        <div class="text-secondary text-uppercase small font-weight-bold mb-2">Total Transactions</div>
                                        <div class="h4 font-weight-bold text-dark mb-0 summary-val">{{ $CustomerLedgers->count() }}</div>
                                        <div class="small text-muted mt-1">In selected period</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Mobile View Switcher (Cards vs Table) -->
                        <div class="d-flex justify-content-between align-items-center mb-3 mobile-view-toggle">
                            <span class="small text-muted font-weight-bold">
                                <i class="bi bi-journal-text mr-1"></i> Transactions ({{ $CustomerLedgers->count() }})
                            </span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnShowCards">
                                    <i class="bi bi-grid"></i> Cards
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnShowTable">
                                    <i class="bi bi-table"></i> Table
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Transactions Cards View (No Horizontal Scroll) -->
                        <div id="mobileLedgerCards">
                            @forelse ($CustomerLedgers as $key => $ledger)
                                @php
                                    $debit = $ledger->debit ?? 0;
                                    $credit = $ledger->credit ?? 0;
                                    $balance = $ledger->closing_balance;
                                    $suffix = $balance >= 0 ? 'Dr' : 'Cr';
                                @endphp
                                <div class="transaction-mcard">
                                    <div class="transaction-mcard-hdr">
                                        <div>
                                            <span class="badge badge-dark mr-1">#{{ $loop->iteration }}</span>
                                            <span class="text-muted small font-weight-bold"><i class="bi bi-calendar3 mr-1"></i>{{ $ledger->created_at->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-weight-bold {{ $balance >= 0 ? 'text-primary' : 'text-danger' }}">
                                                Rs. {{ number_format(abs($balance), 2) }}
                                            </span>
                                            <span class="badge {{ $suffix === 'Dr' ? 'badge-primary' : 'badge-success' }} ml-1">{{ $suffix }}</span>
                                            <div class="text-muted" style="font-size: 0.7rem;">Balance</div>
                                        </div>
                                    </div>

                                    @if($ledger->customer)
                                    <div class="small font-weight-bold text-dark mb-1">
                                        <i class="bi bi-person mr-1 text-secondary"></i>{{ $ledger->customer->customer_name }}
                                    </div>
                                    @endif

                                    <div class="small text-secondary mb-2 bg-light p-2 rounded border">
                                        <span class="font-weight-bold text-dark">Particulars:</span> {{ $ledger->description ?: 'N/A' }}
                                    </div>

                                    <div class="row no-gutters pt-1 border-top">
                                        <div class="col-6">
                                            <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Debit (Dr)</div>
                                            <div class="font-weight-bold {{ $debit > 0 ? 'text-success' : 'text-muted' }}">
                                                {{ $debit > 0 ? 'Rs. ' . number_format($debit, 2) : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-6 text-right">
                                            <div class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Credit (Cr)</div>
                                            <div class="font-weight-bold {{ $credit > 0 ? 'text-danger' : 'text-muted' }}">
                                                {{ $credit > 0 ? 'Rs. ' . number_format($credit, 2) : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4 bg-light rounded border">
                                    <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                                    No transactions found in this period.
                                </div>
                            @endforelse
                        </div>

                        <!-- Ledger Table (Desktop + Mobile Responsive) -->
                        <div class="ledger-table-wrapper mobile-table-hidden" id="desktopLedgerTable">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-ledger" id="ledger-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="12%">Date</th>
                                            <th width="18%">Customer</th>
                                            <th width="30%">Description / Particulars</th>
                                            <th width="10%" class="text-right">Debit (Dr)</th>
                                            <th width="10%" class="text-right">Credit (Cr)</th>
                                            <th width="15%" class="text-right">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($CustomerLedgers as $key => $ledger)
                                            @php
                                                $debit = $ledger->debit ?? 0;
                                                $credit = $ledger->credit ?? 0;
                                                $balance = $ledger->closing_balance;
                                                $suffix = $balance >= 0 ? 'Dr' : 'Cr';
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $ledger->created_at->format('d/m/Y') }}</td>
                                                <td class="font-weight-bold">{{ $ledger->customer->customer_name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $ledger->description }}
                                                </td>
                                                <td class="text-right text-success font-weight-bold">
                                                    {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                                </td>
                                                <td class="text-right text-danger font-weight-bold">
                                                    {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                                </td>
                                                <td class="text-right font-weight-bold">
                                                    {{ number_format(abs($balance), 2) }}
                                                    <small class="text-muted">{{ $suffix }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                                                    No transactions found in this period.
                                                </td>
                                            </tr>
                                        @endforelse
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

@push('scripts')
    <script>
        function printCustomerLedger() {
            let custName = "{{ $selectedCust ? addslashes($selectedCust->customer_name) : '' }}";
            if (!custName && $('select[name="customer_id"]').length) {
                let optText = $('select[name="customer_id"] option:selected').text();
                if (optText && !optText.includes('-- All')) {
                    custName = optText.trim();
                }
            }
            let cleanName = (custName || 'Customer').replace(/[\/\\:*?"<>|]/g, ' ').replace(/\s+/g, ' ').trim();
            document.title = (cleanName || 'Customer') + ' - Ledger';
            window.print();
        }

        window.addEventListener('beforeprint', function() {
            let custName = "{{ $selectedCust ? addslashes($selectedCust->customer_name) : '' }}";
            if (!custName && $('select[name="customer_id"]').length) {
                let optText = $('select[name="customer_id"] option:selected').text();
                if (optText && !optText.includes('-- All')) {
                    custName = optText.trim();
                }
            }
            let cleanName = (custName || 'Customer').replace(/[\/\\:*?"<>|]/g, ' ').replace(/\s+/g, ' ').trim();
            document.title = (cleanName || 'Customer') + ' - Ledger';
        });

        $(document).ready(function() {
            // Init Select2 if available
            if ($('.select2').length > 0) {
                $('.select2').select2({
                    width: '100%'
                });
            }

            // Mobile view toggle (Cards vs Table)
            $('#btnShowCards').on('click', function() {
                $(this).addClass('active');
                $('#btnShowTable').removeClass('active');
                $('#mobileLedgerCards').show();
                $('#desktopLedgerTable').addClass('mobile-table-hidden').hide();
            });

            $('#btnShowTable').on('click', function() {
                $(this).addClass('active');
                $('#btnShowCards').removeClass('active');
                $('#mobileLedgerCards').hide();
                $('#desktopLedgerTable').removeClass('mobile-table-hidden').show();
            });
        });
    </script>
@endpush
