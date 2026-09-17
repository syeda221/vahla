@extends('admin_panel.layout.app')

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

        /* Print Styles */
        @media print {
            @page { size: A4 portrait; margin: 0; }
            body { background: #ffffff !important; font-size: 11px; color: #000; padding: 15mm 10mm !important; }
            .no-print, header, .sidebar, .navbar, .rt_nav_header, footer, form, .page-header, .mb-4.d-flex, .row.mb-4 { display: none !important; }
            
            .main-content, .main-content-inner, .container-fluid { padding: 0 !important; margin: 0 !important; }
            .card { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; }
            .card-body { padding: 0 !important; }
            
            .table-ledger { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; margin-bottom: 20px !important; }
            .table-ledger th, .table-ledger td { border: 1px solid #000 !important; padding: 6px !important; color: #000 !important; background: transparent !important; font-size: 11px !important; }
            .table-ledger th { font-weight: bold !important; text-align: center !important; text-transform: uppercase; border-bottom: 2px solid #000 !important; }
            
            /* Clean up texts and badges */
            .badge { border: none !important; color: #000 !important; padding: 0 !important; font-weight: normal !important; background: transparent !important; box-shadow: none !important; }
            .text-primary, .text-danger, .text-success, .text-dark, .text-muted, .text-secondary { color: #000 !important; }
            
            /* Print Header */
            .print-header { display: block !important; text-align: center; margin-bottom: 15px; }
            .print-header h2 { font-weight: 900; margin: 0; font-size: 22px; text-transform: uppercase; }
            .print-header h4 { font-weight: 800; margin: 8px 0; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; }
            .print-header p { margin: 2px 0; font-size: 11px; }
            
            /* Print Meta Info */
            .print-meta-info { display: flex !important; justify-content: space-between; margin-bottom: 10px; font-weight: bold; font-size: 12px; }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid mt-4">

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <div>
                        <h4 class="mb-1 text-primary"><i class="bi bi-link-45deg"></i> Combined Ledger (Customer + Vendor)</h4>
                        <p class="text-muted mb-0">Track net balance across both customer and vendor roles.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                        @if(isset($customer) && $customer->id)
                            <a href="{{ route('customers.ledger', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary"><i class="bi bi-person"></i>
                                Back to Customer Ledger</a>
                        @endif
                        <a href="{{ route('view_all') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                            Back to Accounts</a>
                    </div>
                </div>

                <div class="card shadow-sm ledger-card">
                    <div class="card-body">

                        <!-- Filters -->
                        <form method="GET" action="{{ route('customers.combined_ledger') }}"
                            class="row g-3 mb-4 p-3 bg-light rounded border">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Select Dual-Role Customer</label>
                                <select name="customer_id" class="form-control select2">
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($dualCustomers as $cust)
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
                                    <a href="{{ route('customers.combined_ledger') }}" class="btn btn-outline-secondary"><i
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
                                        <h3 class="fw-bold text-dark mb-0">{{ count($combinedLedger) }}</h3>
                                        <p class="small text-muted mb-0 mt-1">In selected period</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- PRINT ONLY HEADER --}}
                        <div class="print-header d-none d-print-block">
                            <h2>{{ isset($customer) && $customer->id ? $customer->customer_name : 'ALL CUSTOMERS' }}</h2>
                            <p>Hanif Garden Dry Port Road Near Soha Mall Faisalabad<br>Tel: +92 3216293333 | +92 300 7995500 | Web: vahlamanagement.com</p>
                            <h4>COMBINED LEDGER</h4>
                        </div>
                        
                        <div class="print-meta-info d-none d-print-flex">
                            <div>
                                Account No: - <br>
                                <span style="text-transform: uppercase;">{{ isset($customer) && $customer->id ? $customer->customer_name : 'COMBINED LEDGER' }}</span>
                            </div>
                            <div class="text-end" style="text-align: right;">
                                Date: {{ date('d-M') }}<br>
                                Currency: PKR<br>
                                <span>Total Due: PKR {{ isset($closing_balance) ? number_format(abs($closing_balance), 2) : '0.00' }}</span>
                            </div>
                        </div>

                        <!-- Ledger Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle table-ledger" id="zero_config">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 10%">Source</th>
                                        <th style="width: 35%">Description</th>
                                        <th style="width: 15%" class="text-end">Debit (+)</th>
                                        <th style="width: 15%" class="text-end">Credit (-)</th>
                                        <th style="width: 15%" class="text-end">Net Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Opening Balance Row -->
                                    <tr class="table-light">
                                        <td><strong>{{ \Carbon\Carbon::parse(request('from_date', '2000-01-01'))->format('d/m/Y') }}</strong></td>
                                        <td></td>
                                        <td><strong>Opening Balance</strong></td>
                                        <td class="text-end"></td>
                                        <td class="text-end"></td>
                                        <td class="text-end fw-bold {{ ($openingBalance ?? $opening_balance ?? 0) > 0 ? 'text-success' : (($openingBalance ?? $opening_balance ?? 0) < 0 ? 'text-danger' : '') }}">
                                            {{ number_format(abs($openingBalance ?? $opening_balance ?? 0), 2) }} {{ ($openingBalance ?? $opening_balance ?? 0) >= 0 ? 'Dr' : 'Cr' }}
                                        </td>
                                    </tr>
                                    
                                    @php
                                        $runningBalance = $openingBalance ?? $opening_balance ?? 0;
                                    @endphp

                                    @forelse ($combinedLedger as $row)
                                        @php
                                            $debit = $row->debit ?? 0;
                                            $credit = $row->credit ?? 0;
                                            $balance = $row->running_balance ?? 0;
                                            $suffix = $balance >= 0 ? 'Dr' : 'Cr';
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge {{ $row->source_type == 'Customer' ? 'bg-primary' : 'bg-info' }}">
                                                    {{ $row->source_type }}
                                                </span>
                                            </td>
                                            <td>{{ $row->description }}</td>
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
                                            <td colspan="6" class="text-center text-muted py-4">
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
        $(document).ready(function() {
            // Init Select2 if available
            if ($('.select2').length > 0) {
                $('.select2').select2();
            }
        });
    </script>
@endpush
