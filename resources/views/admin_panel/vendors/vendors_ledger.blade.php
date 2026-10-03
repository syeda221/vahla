@extends('admin_panel.layout.app')

@section('title', 'Vendor - Ledger')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .vledger-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }

    /* Page Header */
    .vledger-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .vledger-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .vledger-sub {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* Action Buttons */
    .btn-vledger-back {
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
    .btn-vledger-back:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .vledger-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .vledger-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .vledger-stat-val {
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        line-height: 1.2;
    }
    .vledger-stat-lbl {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Card & Table */
    .vledger-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .vledger-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vledger-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .datanew {
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .datanew thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }
    .datanew tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .datanew tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Avatar Circle */
    .vendor-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 800;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Badges */
    .vledger-id-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: monospace;
    }

    /* Mobile Cards View */
    .mobile-vledger-cards {
        display: none;
        padding: 14px;
        flex-direction: column;
        gap: 12px;
    }
    .vledger-mcard {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
    }
    .vledger-mcard-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .vledger-mcard-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
    }
    .vledger-mcard-details {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 0.82rem;
        color: #475569;
        padding: 8px 0;
    }
    .btn-details-mob {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        height: 40px;
        background: #eef2ff;
        color: #4f46e5 !important;
        font-weight: 700;
        font-size: 0.84rem;
        border-radius: 10px;
        border: 1px solid #c7d2fe;
        text-decoration: none;
        margin-top: 10px;
        transition: all 0.15s ease;
    }
    .btn-details-mob:hover {
        background: #4f46e5;
        color: #ffffff !important;
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .vledger-header {
            padding: 16px;
        }
        .btn-vledger-back {
            width: 100%;
            justify-content: center;
            height: 40px;
        }
        .vledger-table-wrap {
            display: none !important;
        }
        .mobile-vledger-cards {
            display: flex;
        }
    }
</style>

<div class="vledger-page container-fluid px-3 px-md-4 pt-3">
    
    {{-- Header Row --}}
    <div class="vledger-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h3 class="vledger-title"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Vendor Ledger</h3>
            <div class="vledger-sub">View and manage vendor balances, opening balances, and ledger history</div>
        </div>
        <div>
            <a href="{{ url('vendor') }}" class="btn-vledger-back">
                <i class="fas fa-arrow-left"></i> Back to Vendors
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="vledger-stat-card">
                <div class="vledger-stat-icon"><i class="fas fa-book"></i></div>
                <div>
                    <div class="vledger-stat-val">{{ count($VendorLedgers) }}</div>
                    <div class="vledger-stat-lbl">Total Vendors in Ledger</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="vledger-card">
        <div class="vledger-card-header">
            <div class="fw-bold text-dark"><i class="fas fa-list me-1 text-muted"></i> All Vendor Balances</div>
            <div class="text-muted small">Showing {{ count($VendorLedgers) }} entries</div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mx-4 mt-3 mb-0">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Desktop Table View --}}
        <div class="vledger-table-wrap p-3">
            <table class="table table-hover align-middle datanew">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">ID</th>
                        <th class="text-start">Vendor Name</th>
                        <th class="text-start">Address</th>
                        <th class="text-end">Opening Bal</th>
                        <th class="text-end">Current Balance</th>
                        <th class="text-end pe-4" style="width: 130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($VendorLedgers->isEmpty())
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                if (document.getElementById("global-loader")) {
                                    document.getElementById("global-loader").style.display = "none";
                                }
                            });
                        </script>
                    @endif
                    @forelse($VendorLedgers as $ledger)
                        <tr>
                            <td class="text-center"><span class="vledger-id-badge">#{{ $ledger->vendor_id }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="vendor-avatar">
                                        {{ strtoupper(substr($ledger->vendor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $ledger->vendor->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ Str::limit($ledger->vendor->address ?? '', 30) ?: '—' }}</td>

                            <td class="text-end font-monospace">
                                Rs. {{ number_format((float) $ledger->opening_balance, 2) }}
                            </td>

                            <td class="text-end">
                                @php
                                    $balance = $ledger->formatted_closing_balance ?? $ledger->closing_balance;
                                    $isPayable = $balance >= 0;
                                @endphp
                                <div class="fw-bold {{ $isPayable ? 'text-danger' : 'text-success' }}">
                                    Rs. {{ number_format(abs((float)$balance), 2) }}
                                    <span class="badge {{ $isPayable ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} border ms-1 fw-bold" style="font-size: 0.72rem;">
                                        {{ $isPayable ? 'Cr (Payable)' : 'Dr (Advance)' }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-end pe-4">
                                <a href="{{ route('vendor.ledger', $ledger->vendor_id) }}" class="btn btn-sm btn-outline-primary px-3 fw-bold" title="View Detailed Ledger" style="border-radius: 8px;">
                                    <i class="fas fa-file-alt me-1"></i> Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No vendor ledger records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards View (< 768px) --}}
        <div class="mobile-vledger-cards">
            @forelse($VendorLedgers as $ledger)
                @php
                    $balance = $ledger->formatted_closing_balance ?? $ledger->closing_balance;
                    $isPayable = $balance >= 0;
                @endphp
                <div class="vledger-mcard">
                    <div class="vledger-mcard-hdr">
                        <div class="d-flex align-items-center gap-2">
                            <div class="vendor-avatar">
                                {{ strtoupper(substr($ledger->vendor->name ?? 'V', 0, 1)) }}
                            </div>
                            <div class="vledger-mcard-title">{{ $ledger->vendor->name ?? 'Unknown' }}</div>
                        </div>
                        <span class="vledger-id-badge">#{{ $ledger->vendor_id }}</span>
                    </div>

                    <div class="vledger-mcard-details">
                        <div><i class="fas fa-wallet me-2 text-muted"></i> <strong>Opening Balance:</strong> Rs. {{ number_format((float) $ledger->opening_balance, 2) }}</div>
                        <div>
                            <i class="fas fa-calculator me-2 text-primary"></i> <strong>Current Balance:</strong> 
                            <span class="fw-bold {{ $isPayable ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format(abs((float)$balance), 2) }} {{ $isPayable ? 'Cr' : 'Dr' }}
                            </span>
                        </div>
                        @if (!empty($ledger->vendor->address))
                            <div><i class="fas fa-map-marker-alt me-2 text-muted"></i> <strong>Address:</strong> {{ Str::limit($ledger->vendor->address, 45) }}</div>
                        @endif
                    </div>

                    <a href="{{ route('vendor.ledger', $ledger->vendor_id) }}" class="btn-details-mob">
                        <i class="fas fa-file-alt"></i> View Detailed Ledger
                    </a>
                </div>
            @empty
                <div class="text-center py-4 text-muted">No vendor ledger records found.</div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.datanew')) {
            $('.datanew').DataTable().destroy();
        }
        $('.datanew').DataTable({
            "pageLength": 10,
            "aaSorting": [],
            "language": {
                "search": "",
                "searchPlaceholder": "Search vendors ledger..."
            },
            "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });
    });
</script>
@endsection
