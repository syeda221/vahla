@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-1">Agent Ledger — {{ $agent->name }}</h3>
                                <small class="text-muted">Contact: {{ $agent->contact_number ?: '—' }} | Address: {{ $agent->address ?: '—' }}
                                    <span class="badge bg-{{ $agent->status === 'active' ? 'success' : 'secondary' }} ms-1">{{ ucfirst($agent->status) }}</span>
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('agents.index') }}" class="btn btn-sm btn-light border"><i class="fas fa-arrow-left me-1"></i> Back</a>
                                @can('agents.create')
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payModal">
                                        <i class="fas fa-hand-holding-usd me-1"></i> Pay Commission
                                    </button>
                                @endcan
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Total Commission</div>
                                        <div class="fs-4 fw-bold text-dark">Rs {{ number_format($totalCommission, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Paid</div>
                                        <div class="fs-4 fw-bold text-success">Rs {{ number_format($totalPaid, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Outstanding</div>
                                        <div class="fs-4 fw-bold text-danger">Rs {{ number_format($outstanding, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border mt-1 shadow rounded" style="background-color: white;">
                            <div class="table-responsive mt-4 mb-4 px-3">
                                <table class="table table-bordered table-hover" id="ledgerTable">
                                    <thead class="text-center bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Sale/Invoice No.</th>
                                            <th>Commission (Rs)</th>
                                            <th>Payment (Rs)</th>
                                            <th>Balance (Rs)</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @forelse ($entries as $entry)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $entry->date ? $entry->date->format('d/m/Y') : '—' }}</td>
                                                <td>{{ $entry->reference ?: '—' }}</td>
                                                <td class="fw-bold">{{ $entry->sale_invoice_no ?: '—' }}</td>
                                                <td class="text-success fw-bold">{{ $entry->commission_amount > 0 ? number_format($entry->commission_amount, 2) : '—' }}</td>
                                                <td class="text-danger fw-bold">{{ $entry->payment_amount > 0 ? number_format($entry->payment_amount, 2) : '—' }}</td>
                                                <td class="fw-bold">{{ number_format($entry->running_balance, 2) }}</td>
                                                <td class="text-start">{{ $entry->remarks ?: '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">No ledger entries yet.</td>
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

    <!-- PAY COMMISSION MODAL -->
    <div class="modal fade" id="payModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('agents.pay_commission', $agent->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pay Commission to {{ $agent->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Outstanding Balance</label>
                            <input type="text" class="form-control input-readonly" value="Rs {{ number_format($outstanding, 2) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (Rs) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="{{ $outstanding > 0 ? $outstanding : 0 }}" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Account (Cash/Bank)</label>
                            <select name="payment_account_id" class="form-select">
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>{{ $acc->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Record Payment">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection