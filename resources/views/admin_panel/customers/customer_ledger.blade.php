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
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                                    <button type="button" class="btn btn-danger btnDownloadPdfDirect" title="Download PDF"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <a href="{{ route('customers.ledger') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-arrow-clockwise"></i></a>
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
                            <table class="table table-hover align-middle" id="ledger-table" style="font-size: .80rem;">
                                <thead>
                                    <tr>
                                        <th width="9%" class="text-center" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Date</th>
                                        <th width="12%" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Details</th>
                                        <th width="14%" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Bank Name</th>
                                        <th width="20%" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Ref No.</th>
                                        <th width="9%" class="text-center" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">V. No.</th>
                                        <th width="8%" class="text-center" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Quantity</th>
                                        <th width="9%" class="text-end" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Debit</th>
                                        <th width="9%" class="text-end" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Credit</th>
                                        <th width="10%" class="text-end" style="background-color: #ffffff; color: #000000; font-weight: 800; border-top: 3px solid #000; border-bottom: 2px solid #000; border-left: none; border-right: none;">Balance</th>
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

            $('.btnDownloadPdfDirect').on('click', function() {
                let $btn = $(this);
                let origText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                let cid = $('select[name="customer_id"]').val() || 'all';
                let start = $('input[name="from_date"]').val() || '2000-01-01';
                let end = $('input[name="to_date"]').val() || '{{ date("Y-m-d") }}';
                let custName = $('select[name="customer_id"] option:selected').text() || 'Customer';
                let safeName = custName.replace(/[^A-Za-z0-9_\-]/g, '_').trim();

                $.ajax({
                    url: "{{ route('report.customer.ledger.pdf') }}",
                    type: "GET",
                    data: {
                        customer_id: cid,
                        start_date: start,
                        end_date: end,
                        ajax: '1',
                        _t: new Date().getTime()
                    },
                    dataType: "json",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        if (res && res.success && res.pdf_base64) {
                            let byteCharacters = atob(res.pdf_base64);
                            let byteNumbers = new Array(byteCharacters.length);
                            for (let i = 0; i < byteCharacters.length; i++) {
                                byteNumbers[i] = byteCharacters.charCodeAt(i);
                            }
                            let byteArray = new Uint8Array(byteNumbers);
                            let blob = new Blob([byteArray], { type: 'application/pdf' });
                            let blobUrl = window.URL.createObjectURL(blob);
                            let a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = blobUrl;
                            a.download = res.filename || ('Customer_Statement_' + safeName + '_' + new Date().getTime() + '.pdf');
                            document.body.appendChild(a);
                            a.click();
                            setTimeout(() => {
                                window.URL.revokeObjectURL(blobUrl);
                                document.body.removeChild(a);
                            }, 1000);
                        } else {
                            alert('Could not generate PDF.');
                        }
                        $btn.prop('disabled', false).html(origText);
                    },
                    error: function() {
                        alert('Failed to generate PDF. Please try again.');
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });
        });
    </script>
@endpush
