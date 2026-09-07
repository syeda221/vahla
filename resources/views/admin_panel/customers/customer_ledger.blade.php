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
                    <div>
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
                            <table class="table table-bordered table-hover align-middle" id="ledger-table" style="font-size: .80rem; border: 1px solid #000000;">
                                <thead style="background-color: #000000; color: #ffffff;">
                                    <tr>
                                        <th width="9%" class="text-center" style="background-color: #000000; color: #fff; border: 1px solid #222;">Date</th>
                                        <th width="12%" style="background-color: #000000; color: #fff; border: 1px solid #222;">Details</th>
                                        <th width="14%" style="background-color: #000000; color: #fff; border: 1px solid #222;">Bank Name</th>
                                        <th width="20%" style="background-color: #000000; color: #fff; border: 1px solid #222;">Ref No.</th>
                                        <th width="9%" class="text-center" style="background-color: #000000; color: #fff; border: 1px solid #222;">V No.</th>
                                        <th width="8%" class="text-center" style="background-color: #000000; color: #fff; border: 1px solid #222;">Quantity</th>
                                        <th width="9%" class="text-end" style="background-color: #000000; color: #fff; border: 1px solid #222;">Debit</th>
                                        <th width="9%" class="text-end" style="background-color: #000000; color: #fff; border: 1px solid #222;">Credit</th>
                                        <th width="10%" class="text-end" style="background-color: #000000; color: #fff; border: 1px solid #222;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(request('customer_id'))
                                        <tr class="bg-light fw-bold">
                                            <td class="text-center">-</td>
                                            <td>Opening Balance</td>
                                            <td class="text-center">-</td>
                                            <td>Opening Balance (B/F)</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">0</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end">-</td>
                                            <td class="text-end text-dark">{{ number_format($opening_balance ?? 0, 2) }}</td>
                                        </tr>
                                    @endif

                                    @php
                                        $totQty = 0;
                                        $totDeb = 0;
                                        $totCrd = 0;
                                    @endphp

                                    @forelse ($CustomerLedgers as $key => $ledger)
                                        @php
                                            $debit = $ledger->debit ?? 0;
                                            $credit = $ledger->credit ?? 0;
                                            $qty = $ledger->quantity ?? 0;
                                            $balance = $ledger->closing_balance;
                                            $totDeb += $debit;
                                            $totCrd += $credit;
                                            $totQty += $qty;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $ledger->created_at->format('d/m/Y') }}</td>
                                            <td><span class="fw-semibold text-dark">{{ $ledger->details ?? '-' }}</span></td>
                                            <td class="text-dark small">{{ $ledger->bank_name && $ledger->bank_name !== '-' ? $ledger->bank_name : '' }}</td>
                                            <td class="small text-break text-dark">{{ $ledger->ref_no ?? $ledger->description ?? '' }}</td>
                                            <td class="text-center font-monospace fw-semibold text-dark">{{ $ledger->v_no && $ledger->v_no !== '-' ? $ledger->v_no : '' }}</td>
                                            <td class="text-center fw-semibold text-dark">{{ $qty != 0 ? number_format($qty) : '0' }}</td>
                                            <td class="text-end text-dark">
                                                {{ $debit > 0 ? number_format($debit, 2) : '' }}
                                            </td>
                                            <td class="text-end text-dark">
                                                {{ $credit > 0 ? number_format($credit, 2) : '' }}
                                            </td>
                                            <td class="text-end fw-bold text-dark">
                                                {{ number_format($balance, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                                No transactions found in this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(request('customer_id') && $CustomerLedgers->count() > 0)
                                    <tfoot style="border-top: 2px solid #000000 !important; border-bottom: 2px solid #000000 !important; background-color: #ffffff;">
                                        <tr class="fw-bold bg-white">
                                            <td colspan="5" class="text-end fw-bold text-dark"></td>
                                            <td class="text-center fw-bold text-dark">{{ number_format($totQty) }}</td>
                                            <td class="text-end fw-bold text-dark">{{ $totDeb > 0 ? number_format($totDeb, 2) : '' }}</td>
                                            <td class="text-end fw-bold text-dark">{{ $totCrd > 0 ? number_format($totCrd, 2) : '' }}</td>
                                            <td class="text-end fw-bold text-dark">{{ number_format($closing_balance ?? 0, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
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
