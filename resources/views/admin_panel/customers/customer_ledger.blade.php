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
        .ledger-card {
            border-top: 3px solid #0d6efd;
        }

        .table-ledger th {
            background-color: #212529;
            color: #fff;
        }

        .balance-positive {
            color: #198754;
            font-weight: 700;
        }

        .balance-neutral {
            color: #6c757d;
            font-weight: 700;
        }

        @media print {
            body { background: #fff !important; }
            .no-print, header, .sidebar, .navbar, .rt_nav_header, footer, form, .btn, .d-flex.justify-content-between.align-items-center.mb-4 > div:last-child {
                display: none !important;
            }
            .card { border: none !important; box-shadow: none !important; }
            .table-ledger th { background-color: #eee !important; color: #000 !important; }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid mt-4">

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1 text-primary"><i class="bi bi-people"></i> Customer Ledger (Statement)</h4>
                        <p class="text-muted mb-0">Track all customer transactions, invoices, and receipts.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" onclick="printCustomerLedger()" class="btn btn-outline-primary"><i class="bi bi-printer"></i>
                            Print</button>
                        <a href="{{ route('view_all') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                            Back to Accounts</a>
                    </div>
                </div>

                <div class="card shadow-sm ledger-card">
                    <div class="card-body">

                        <!-- Filters -->
                        <form method="GET" action="{{ route('customers.ledger') }}"
                            class="row g-3 mb-4 p-3 bg-light rounded border">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Select Customer</label>
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
                            <div class="col-md-3">
                                <label class="form-label fw-bold">From Date</label>
                                <input type="text" name="from_date" value="{{ request('from_date') }}"
                                    class="form-control datepicker-custom">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">To Date</label>
                                <input type="text" name="to_date" value="{{ request('to_date') }}" class="form-control datepicker-custom">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="d-flex w-100 gap-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i>
                                        Filter</button>
                                    <a href="{{ route('customers.ledger') }}" class="btn btn-outline-secondary"><i
                                            class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </form>

                        @if(request('customer_id'))
                        <!-- Summary Cards -->
                        <div class="row mb-4 g-3">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                                    <div class="card-body p-4">
                                        <h6 class="text-secondary text-uppercase small fw-bold mb-2">Opening Balance</h6>
                                        <h3 class="fw-bold text-dark mb-0">Rs. {{ number_format(abs($opening_balance ?? 0), 2) }} <small class="fs-6 text-muted">{{ ($opening_balance ?? 0) >= 0 ? 'Dr' : 'Cr' }}</small></h3>
                                        <p class="small text-muted mb-0 mt-1">As of {{ \Carbon\Carbon::parse(request('from_date', '2000-01-01'))->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @php
                                    $cb = $closing_balance ?? 0;
                                @endphp
                                <div class="card border-0 shadow-sm rounded-4 h-100 {{ $cb > 0 ? 'bg-danger text-white' : ($cb < 0 ? 'bg-success text-white' : 'bg-primary text-white') }}">
                                    <div class="card-body p-4">
                                        <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Closing Balance</h6>
                                        <h3 class="fw-bold mb-0">Rs. {{ number_format(abs($cb), 2) }} <small class="fs-6 text-white-50">{{ $cb >= 0 ? 'Dr' : 'Cr' }}</small></h3>
                                        <p class="small text-white-50 mb-0 mt-1">{{ $cb > 0 ? 'Receivable (Customer Owes)' : ($cb < 0 ? 'Advance (We Owe)' : 'Settled') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                                    <div class="card-body p-4">
                                        <h6 class="text-secondary text-uppercase small fw-bold mb-2">Total Transactions</h6>
                                        <h3 class="fw-bold text-dark mb-0">{{ $CustomerLedgers->count() }}</h3>
                                        <p class="small text-muted mb-0 mt-1">In selected period</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Ledger Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-ledger" id="ledger-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="12%">Date</th>
                                        <th width="18%">Customer</th>
                                        <th width="30%">Description / Particulars</th>
                                        <th width="10%" class="text-end">Debit (Dr)</th>
                                        <th width="10%" class="text-end">Credit (Cr)</th>
                                        <th width="15%" class="text-end">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($CustomerLedgers as $key => $ledger)
                                        @php
                                            // Ledger object now has explicit debit/credit from Controller/BalanceService
                                            $debit = $ledger->debit ?? 0;
                                            $credit = $ledger->credit ?? 0;
                                            $balance = $ledger->closing_balance;
                                            $suffix = $balance >= 0 ? 'Dr' : 'Cr';
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $ledger->created_at->format('d/m/Y') }}</td>
                                            <td class="fw-bold">{{ $ledger->customer->customer_name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $ledger->description }}
                                            </td>
                                            <td class="text-end text-success">
                                                {{ $debit > 0 ? number_format($debit, 2) : '-' }}
                                            </td>
                                            <td class="text-end text-danger">
                                                {{ $credit > 0 ? number_format($credit, 2) : '-' }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format(abs($balance), 2) }}
                                                <small class="text-muted">{{ $suffix }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
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
                $('.select2').select2();
            }
        });
    </script>
@endpush
