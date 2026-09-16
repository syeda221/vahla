@extends('admin_panel.layout.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .vdetail-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }

    /* Page Header */
    .vdetail-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .vdetail-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .vdetail-sub {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* Back Button */
    .btn-vdetail-back {
        background: #ffffff;
        color: #475569 !important;
        border: 1.5px solid #cbd5e1;
        padding: 9px 18px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-vdetail-back:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Filter Card & Actions Grid */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .filter-actions-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 8px;
        width: 100%;
    }
    .btn-filter-main {
        border-radius: 10px !important;
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;
        border: none !important;
        font-weight: 700 !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        color: #ffffff !important;
        font-size: 0.86rem !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.22) !important;
    }
    .btn-filter-reset {
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        font-weight: 600 !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #475569 !important;
        text-decoration: none !important;
        font-size: 0.84rem !important;
        background: #ffffff !important;
    }
    .btn-filter-reset:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
    }
    .btn-filter-print {
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        font-weight: 600 !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #ffffff !important;
        color: #475569 !important;
        font-size: 0.84rem !important;
    }
    .btn-filter-print:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
    }

    /* Stat KPI Cards */
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .kpi-lbl {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 6px;
    }
    .kpi-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .kpi-closing-payable {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
        border: none;
    }
    .kpi-closing-advance {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        border: none;
    }

    /* Table & Container */
    .vdetail-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .vdetail-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vdetail-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .vdetail-table {
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .vdetail-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }
    .vdetail-table tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .vdetail-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Mobile Cards View */
    .mobile-vdetail-cards {
        display: none;
        padding: 14px;
        flex-direction: column;
        gap: 12px;
    }
    .vdetail-mcard {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
    }
    .vdetail-mcard-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    /* Print Styles */
    @media print {
        .btn-vdetail-back, .filter-card, .btn {
            display: none !important;
        }
        .vdetail-page {
            padding: 0;
            background: #fff;
        }
        .vdetail-card {
            border: none !important;
            box-shadow: none !important;
        }
        .vdetail-table-wrap {
            display: block !important;
        }
        .mobile-vdetail-cards {
            display: none !important;
        }
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .vdetail-header {
            padding: 16px;
        }
        .btn-vdetail-back {
            width: 100%;
            justify-content: center;
            height: 40px;
        }
        .vdetail-table-wrap {
            display: none !important;
        }
        .mobile-vdetail-cards {
            display: flex;
        }
    }
</style>

<div class="vdetail-page container-fluid px-3 px-md-4 pt-3">
    
    {{-- Header Row --}}
    <div class="vdetail-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h3 class="vdetail-title"><i class="fas fa-receipt text-primary me-2"></i>Vendor Ledger Details</h3>
            <div class="vdetail-sub">Transaction history for <span class="fw-bold text-primary">{{ $vendor->name }}</span></div>
        </div>
        <div>
            <a href="{{ route('vendors-ledger') }}" class="btn-vdetail-back">
                <i class="fas fa-arrow-left"></i> Back to Ledger List
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary fw-bold mb-1">Start Date</label>
                <input type="date" name="start_date" class="form-control px-3 py-2" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" style="border-radius: 10px; border: 1.5px solid #cbd5e1;">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-secondary fw-bold mb-1">End Date</label>
                <input type="date" name="end_date" class="form-control px-3 py-2" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" style="border-radius: 10px; border: 1.5px solid #cbd5e1;">
            </div>
            <div class="col-12 col-md-4">
                <div class="filter-actions-grid">
                    <button type="submit" class="btn btn-primary btn-filter-main">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('vendor.ledger', $vendor->id) }}?reset=1" class="btn btn-outline-secondary btn-filter-reset">Reset</a>
                    <button type="button" onclick="window.print()" class="btn btn-light btn-filter-print" title="Print Ledger">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="kpi-card">
                <div class="kpi-lbl">Opening Balance</div>
                <div class="kpi-val text-dark">Rs. {{ number_format((float)$opening_balance, 2) }}</div>
                <div class="small text-muted mt-1">As of {{ \Carbon\Carbon::parse(request('start_date', now()->startOfMonth()))->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="kpi-card {{ $closing_balance > 0 ? 'kpi-closing-payable' : ($closing_balance < 0 ? 'kpi-closing-advance' : '') }}">
                <div class="kpi-lbl {{ $closing_balance != 0 ? 'text-white-50' : '' }}">Closing Balance</div>
                <div class="kpi-val {{ $closing_balance != 0 ? 'text-white' : 'text-dark' }}">
                    Rs. {{ number_format(abs((float)$closing_balance), 2) }}
                </div>
                <div class="small mt-1 {{ $closing_balance != 0 ? 'text-white-50' : 'text-muted' }}">
                    {{ $closing_balance > 0 ? 'To be Paid (Payable Cr)' : ($closing_balance < 0 ? 'Advance (Dr)' : 'Settled') }}
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="kpi-card">
                <div class="kpi-lbl">Total Transactions</div>
                <div class="kpi-val text-dark">{{ $transactions->count() }}</div>
                <div class="small text-muted mt-1">In selected period</div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="vdetail-card">
        <div class="vdetail-card-header">
            <div class="fw-bold text-dark"><i class="fas fa-list me-1 text-muted"></i> Statement of Account</div>
            <div class="text-muted small">Showing {{ $transactions->count() }} entries</div>
        </div>

        {{-- Desktop Table View --}}
        <div class="vdetail-table-wrap">
            <table class="table vdetail-table">
                <thead>
                    <tr>
                        <th class="text-start ps-4" style="width: 110px;">Date</th>
                        <th class="text-start">Description</th>
                        <th class="text-end" style="width: 130px;">Debit (Dr)</th>
                        <th class="text-end" style="width: 130px;">Credit (Cr)</th>
                        <th class="text-end" style="width: 140px;">Balance</th>
                        <th class="text-center pe-4" style="width: 130px;">Source</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($transactions->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <p class="mb-0">No transactions found in this period.</p>
                            </td>
                        </tr>
                    @else
                        @foreach ($transactions as $txn)
                            <tr>
                                <td class="ps-4 fw-semibold text-dark">
                                    {{ \Carbon\Carbon::parse($txn['date'])->format('d/m/Y') }}
                                </td>
                                <td class="text-secondary small">{{ $txn['description'] }}</td>
                                <td class="text-end font-monospace text-dark">
                                    {{ $txn['debit'] > 0 ? number_format((float)$txn['debit'], 2) : '—' }}
                                </td>
                                <td class="text-end font-monospace text-dark">
                                    {{ $txn['credit'] > 0 ? number_format((float)$txn['credit'], 2) : '—' }}
                                </td>
                                <td class="text-end fw-bold font-monospace {{ $txn['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    Rs. {{ number_format(abs((float)$txn['balance']), 2) }}
                                    <small class="fw-normal text-muted ms-1" style="font-size: 0.72rem;">{{ $txn['balance'] >= 0 ? 'Cr' : 'Dr' }}</small>
                                </td>
                                <td class="pe-4 text-center">
                                    @if ($txn['source_type'] === 'App\\Models\\Purchase')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">Purchase</span>
                                    @elseif($txn['source_type'] === 'App\\Models\\VoucherMaster')
                                        @if ($txn['debit'] > 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Payment</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Receipt</span>
                                        @endif
                                    @elseif($txn['source_type'] === 'App\\Models\\VendorPayment')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">Vendor Payment</span>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-pill px-3">{{ class_basename($txn['source_type']) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot class="bg-light border-top">
                    <tr>
                        <td colspan="4" class="text-end py-3 fw-bold text-dark text-uppercase small">Closing Balance:</td>
                        <td class="text-end py-3 fw-bold font-monospace {{ $closing_balance > 0 ? 'text-danger' : 'text-success' }}">
                            Rs. {{ number_format(abs((float)$closing_balance), 2) }}
                            <small class="fw-normal text-muted ms-1">{{ $closing_balance >= 0 ? 'Cr' : 'Dr' }}</small>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Mobile Cards View (< 768px) --}}
        <div class="mobile-vdetail-cards">
            @if ($transactions->isEmpty())
                <div class="text-center py-4 text-muted">No transactions found in this period.</div>
            @else
                @foreach ($transactions as $txn)
                    <div class="vdetail-mcard">
                        <div class="vdetail-mcard-hdr">
                            <span class="fw-bold text-dark"><i class="far fa-calendar-alt me-1 text-primary"></i> {{ \Carbon\Carbon::parse($txn['date'])->format('d/m/Y') }}</span>
                            @if ($txn['source_type'] === 'App\\Models\\Purchase')
                                <span class="badge bg-primary-subtle text-primary border rounded-pill px-2">Purchase</span>
                            @elseif($txn['source_type'] === 'App\\Models\\VoucherMaster')
                                @if ($txn['debit'] > 0)
                                    <span class="badge bg-danger-subtle text-danger border rounded-pill px-2">Payment</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border rounded-pill px-2">Receipt</span>
                                @endif
                            @elseif($txn['source_type'] === 'App\\Models\\VendorPayment')
                                <span class="badge bg-info-subtle text-info border rounded-pill px-2">Vendor Payment</span>
                            @else
                                <span class="badge bg-light text-secondary border rounded-pill px-2">{{ class_basename($txn['source_type']) }}</span>
                            @endif
                        </div>

                        <div class="text-secondary small mb-2">{{ $txn['description'] }}</div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                            <div>
                                @if ($txn['debit'] > 0)
                                    <div class="text-dark"><strong>Dr:</strong> Rs. {{ number_format((float)$txn['debit'], 2) }}</div>
                                @endif
                                @if ($txn['credit'] > 0)
                                    <div class="text-dark"><strong>Cr:</strong> Rs. {{ number_format((float)$txn['credit'], 2) }}</div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="text-muted" style="font-size: 0.72rem;">Running Balance</div>
                                <div class="fw-bold {{ $txn['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    Rs. {{ number_format(abs((float)$txn['balance']), 2) }} <small>{{ $txn['balance'] >= 0 ? 'Cr' : 'Dr' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sticky Filters Logic
        let hasQueryParams = window.location.search.length > 0;
        let isReset = window.location.search.includes('reset=1');
        
        // Vendor Ledger specific storage key based on URL path to isolate different vendors
        let storageKey = 'vendor_ledger_filters_' + window.location.pathname;

        if (isReset) {
            sessionStorage.removeItem(storageKey);
            window.location.href = window.location.pathname; // strip query params
            return;
        }

        if (hasQueryParams) {
            sessionStorage.setItem(storageKey, window.location.search);
        } else {
            let savedFilters = sessionStorage.getItem(storageKey);
            if (savedFilters) {
                window.location.search = savedFilters;
            }
        }
    });
</script>
@endpush

@endsection
