@extends('admin_panel.layout.app')

@section('content')
<style>
    .chq-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 15px 15px 60px 15px;
    }
    .chq-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .kpi-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 16px 20px;
        transition: all 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);
    }
    .nav-tabs-chq .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        border-radius: 0;
    }
    .nav-tabs-chq .nav-link.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: transparent;
    }
    .badge-inward {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .badge-outward {
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
    .badge-pending {
        background-color: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .badge-cleared {
        background-color: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    .badge-bounced {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
</style>

<div class="chq-container">
    <!-- Header -->
    <div class="chq-card p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: #eef2ff; width: 48px; height: 48px;">
                <i class="fa-solid fa-money-check-dollar text-primary fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Cheque Register & Alerts</h4>
                <p class="text-muted small mb-0">Post-Dated Cheques (PDC), Bank Low-Balance Alerts & Auto-Deductions</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-pill" id="btnRunChecks">
                <i class="fa-solid fa-arrows-rotate me-1"></i> Check Alerts & Due Dates Now
            </button>
            <a href="{{ route('vouchers.make_payment') }}" class="btn btn-primary btn-sm px-3 rounded-pill">
                <i class="fa-solid fa-plus me-1"></i> Issue Cheque (Payment)
            </a>
            <a href="{{ route('vouchers.receive_payment') }}" class="btn btn-success btn-sm px-3 rounded-pill text-white">
                <i class="fa-solid fa-plus me-1"></i> Receive Cheque
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Pending Inward (Customer)</div>
                        <h4 class="fw-bold mb-0 text-success mt-1">PKR {{ number_format($stats['pending_inward_amount'], 2) }}</h4>
                        <span class="badge badge-inward rounded-pill mt-1">{{ $stats['pending_inward_count'] }} Cheques to Deposit</span>
                    </div>
                    <i class="fa-solid fa-arrow-down-left text-success fs-2 opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Pending Outward (Vendor)</div>
                        <h4 class="fw-bold mb-0 text-primary mt-1">PKR {{ number_format($stats['pending_outward_amount'], 2) }}</h4>
                        <span class="badge badge-outward rounded-pill mt-1">{{ $stats['pending_outward_count'] }} Cheques Due</span>
                    </div>
                    <i class="fa-solid fa-arrow-up-right text-primary fs-2 opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Cleared Cheques</div>
                        <h4 class="fw-bold mb-0 text-info mt-1">PKR {{ number_format($stats['cleared_amount'], 2) }}</h4>
                        <span class="badge badge-cleared rounded-pill mt-1">{{ $stats['cleared_count'] }} Cleared</span>
                    </div>
                    <i class="fa-solid fa-circle-check text-info fs-2 opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="kpi-card border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Bounced Cheques</div>
                        <h4 class="fw-bold mb-0 text-danger mt-1">{{ $stats['bounced_count'] }}</h4>
                        <span class="badge badge-bounced rounded-pill mt-1">Review Required</span>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation text-danger fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Table Card -->
    <div class="chq-card">
        <!-- Tabs & Search Filter -->
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <ul class="nav nav-tabs nav-tabs-chq border-0 mb-0">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}" href="{{ route('cheques.index', ['tab' => 'pending']) }}">
                        <i class="fa-solid fa-clock me-1"></i> All Pending ({{ $stats['pending_inward_count'] + $stats['pending_outward_count'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'inward' ? 'active' : '' }}" href="{{ route('cheques.index', ['tab' => 'inward']) }}">
                        <i class="fa-solid fa-arrow-down me-1 text-success"></i> Inward / Customer ({{ $stats['pending_inward_count'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'outward' ? 'active' : '' }}" href="{{ route('cheques.index', ['tab' => 'outward']) }}">
                        <i class="fa-solid fa-arrow-up me-1 text-primary"></i> Outward / Vendor ({{ $stats['pending_outward_count'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'cleared' ? 'active' : '' }}" href="{{ route('cheques.index', ['tab' => 'cleared']) }}">
                        <i class="fa-solid fa-check-double me-1 text-info"></i> Cleared History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'bounced' ? 'active' : '' }}" href="{{ route('cheques.index', ['tab' => 'bounced']) }}">
                        <i class="fa-solid fa-ban me-1 text-danger"></i> Bounced ({{ $stats['bounced_count'] }})
                    </a>
                </li>
            </ul>

            <form action="{{ route('cheques.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="input-group input-group-sm" style="width: 260px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search Chq #, Party, Bank..." value="{{ request('search') }}">
                </div>
                @if(request('search'))
                    <a href="{{ route('cheques.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-xmark"></i></a>
                @endif
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3">Cheque #</th>
                        <th>Type</th>
                        <th>Party Name</th>
                        <th>Amount (PKR)</th>
                        <th>Due Date (Maturity)</th>
                        <th>Bank Account / Drawn Bank</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cheques as $chq)
                        @php
                            $today = \Carbon\Carbon::today();
                            $dueDate = \Carbon\Carbon::parse($chq->cheque_date);
                            $diffDays = $today->diffInDays($dueDate, false);
                        @endphp
                        <tr>
                            <td class="ps-3 fw-bold text-dark">
                                <i class="fa-solid fa-money-check text-muted me-1"></i> {{ $chq->cheque_no }}
                                <div class="text-muted small fw-normal">Issued: {{ $chq->issue_date->format('d-M-Y') }}</div>
                            </td>
                            <td>
                                @if($chq->type === 'inward')
                                    <span class="badge badge-inward rounded-pill px-2 py-1"><i class="fa-solid fa-arrow-down me-1"></i> Customer Receipt</span>
                                @else
                                    <span class="badge badge-outward rounded-pill px-2 py-1"><i class="fa-solid fa-arrow-up me-1"></i> Vendor Payment</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $chq->party_name }}</div>
                                <div class="text-muted small">{{ ucfirst($chq->party_type) }}</div>
                            </td>
                            <td class="fw-bold text-dark">
                                PKR {{ number_format($chq->amount, 2) }}
                            </td>
                            <td>
                                <div>{{ $chq->cheque_date->format('d-M-Y') }}</div>
                                @if($chq->status === 'pending')
                                    @if($diffDays < 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill small">Overdue by {{ abs($diffDays) }} days</span>
                                    @elseif($diffDays == 0)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill small fw-bold">Due Today!</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill small">In {{ $diffDays }} day(s)</span>
                                    @endif
                                @elseif($chq->status === 'cleared')
                                    <span class="text-muted small">Cleared: {{ $chq->cleared_date ? $chq->cleared_date->format('d-M-Y') : '-' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($chq->bankAccount)
                                    <div class="fw-semibold">{{ $chq->bankAccount->title }}</div>
                                    @if($chq->status === 'pending' && $chq->type === 'outward')
                                        <div class="small {{ ($chq->bankAccount->current_balance < $chq->amount) ? 'text-danger fw-bold' : 'text-muted' }}">
                                            Bal: PKR {{ number_format($chq->bankAccount->current_balance, 2) }}
                                            @if($chq->bankAccount->current_balance < $chq->amount)
                                                <i class="fa-solid fa-triangle-exclamation text-danger ms-1" title="Balance is lower than cheque amount!"></i>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-dark">{{ $chq->bank_name ?: '-' }}</span>
                                @endif

                                @if($chq->clearedAccount)
                                    <div class="text-success small"><i class="fa-solid fa-check me-1"></i> Cleared into: {{ $chq->clearedAccount->title }}</div>
                                @endif
                            </td>
                            <td>
                                @if($chq->status === 'pending')
                                    <span class="badge badge-pending rounded-pill px-2 py-1">Pending</span>
                                @elseif($chq->status === 'cleared')
                                    <span class="badge badge-cleared rounded-pill px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Cleared</span>
                                @elseif($chq->status === 'bounced')
                                    <span class="badge badge-bounced rounded-pill px-2 py-1"><i class="fa-solid fa-ban me-1"></i> Bounced</span>
                                    @if($chq->bounce_reason)
                                        <div class="small text-danger" style="max-width: 150px;">{{ $chq->bounce_reason }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                @if($chq->status === 'pending')
                                    @if($chq->type === 'inward')
                                        <button type="button" class="btn btn-sm btn-success text-white rounded-pill px-3 btn-clear-modal" 
                                            data-id="{{ $chq->id }}"
                                            data-chq-no="{{ $chq->cheque_no }}"
                                            data-party="{{ $chq->party_name }}"
                                            data-amount="{{ number_format($chq->amount, 2) }}">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Clear & Deposit
                                        </button>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small py-1 px-2" title="Will auto-deduct from bank on due date">
                                            <i class="fa-solid fa-robot me-1"></i> Auto-Deducts on Due Date
                                        </span>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 ms-1 btn-bounce-modal" 
                                        data-id="{{ $chq->id }}"
                                        data-chq-no="{{ $chq->cheque_no }}">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-money-check-dollar fs-1 text-muted opacity-50 mb-2"></i>
                                <div>No cheques found in this section.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cheques->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $cheques->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Clear & Deposit Inward Cheque -->
<div class="modal fade" id="modalClearCheque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-money-check-dollar me-2"></i> Deposit & Clear Cheque</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formClearCheque">
                @csrf
                <input type="hidden" id="clearChequeId">
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3 border" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Cheque #:</span>
                                <strong class="text-dark d-block" id="modalChqNo"></strong>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small">Amount:</span>
                                <strong class="text-success fs-5 d-block" id="modalAmount"></strong>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <span class="text-muted small">Customer:</span>
                            <strong class="text-dark" id="modalParty"></strong>
                        </div>
                    </div>

                    <!-- Destination Account Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Deposit Into Account (Bank or Cash) <span class="text-danger">*</span></label>
                        <select name="destination_account_id" id="clearDestAccountId" class="form-select select2" required style="width: 100%;">
                            <option value="">-- Select Bank or Cash Account --</option>
                            @foreach ($destinationAccounts as $dAcc)
                                <option value="{{ $dAcc->id }}">
                                    {{ $dAcc->title }} ({{ $dAcc->account_code }}) - Available: PKR {{ number_format($dAcc->current_balance ?? 0, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Select the Bank or Cash drawer where funds are being deposited.</div>
                    </div>

                    <!-- Clearance Date -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Clearance Date <span class="text-danger">*</span></label>
                        <input type="date" name="cleared_date" id="clearDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark">Remarks / Deposit Slip #</label>
                        <input type="text" name="remarks" id="clearRemarks" class="form-control" placeholder="e.g. Deposit slip # 99201">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 text-white" id="btnSubmitClear">
                        <i class="fa-solid fa-check me-1"></i> Confirm & Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Bounce Cheque -->
<div class="modal fade" id="modalBounceCheque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> Mark Cheque as Bounced</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formBounceCheque">
                @csrf
                <input type="hidden" id="bounceChequeId">
                <div class="modal-body p-4">
                    <p class="text-muted small">Are you sure you want to mark Cheque #<strong id="modalBounceChqNo"></strong> as bounced?</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Bounce Reason <span class="text-danger">*</span></label>
                        <textarea name="bounce_reason" id="bounceReason" class="form-control" rows="3" placeholder="e.g. Insufficient Funds / Signature Mismatch" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4" id="btnSubmitBounce">
                        <i class="fa-solid fa-ban me-1"></i> Confirm Bounce
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Open Clear Modal
    $(document).on('click', '.btn-clear-modal', function() {
        let chqId = $(this).data('id');
        let chqNo = $(this).data('chq-no');
        let party = $(this).data('party');
        let amount = $(this).data('amount');

        $('#clearChequeId').val(chqId);
        $('#modalChqNo').text(chqNo);
        $('#modalParty').text(party);
        $('#modalAmount').text('PKR ' + amount);
        $('#clearRemarks').val('');
        $('#btnSubmitClear').prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i> Confirm & Deposit');

        $('#modalClearCheque').modal('show');
    });

    // Handle Clear Submit
    $('#formClearCheque').on('submit', function(e) {
        e.preventDefault();
        let chqId = $('#clearChequeId').val();
        let destAccId = $('#clearDestAccountId').val();
        let clrDate = $('#clearDate').val();
        let remarks = $('#clearRemarks').val();

        if (!destAccId) {
            Swal.fire({ icon: 'warning', title: 'Account Required', text: 'Please select the Bank or Cash account to deposit into.' });
            return;
        }

        let submitBtn = $('#btnSubmitClear');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Depositing...');

        $.ajax({
            url: "{{ url('/cheques') }}/" + chqId + "/clear-inward",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                destination_account_id: destAccId,
                cleared_date: clrDate,
                remarks: remarks
            },
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i> Confirm & Deposit');
                $('#modalClearCheque').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                Swal.fire({
                    icon: 'success',
                    title: 'Cheque Cleared!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-check me-1"></i> Confirm & Deposit');
                let msg = err.responseJSON ? err.responseJSON.message : 'Failed to clear cheque.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    // Open Bounce Modal
    $(document).on('click', '.btn-bounce-modal', function() {
        let chqId = $(this).data('id');
        let chqNo = $(this).data('chq-no');

        $('#bounceChequeId').val(chqId);
        $('#modalBounceChqNo').text(chqNo);
        $('#bounceReason').val('');
        $('#btnSubmitBounce').prop('disabled', false).html('<i class="fa-solid fa-ban me-1"></i> Confirm Bounce');

        $('#modalBounceCheque').modal('show');
    });

    // Handle Bounce Submit
    $('#formBounceCheque').on('submit', function(e) {
        e.preventDefault();
        let chqId = $('#bounceChequeId').val();
        let reason = $('#bounceReason').val().trim();

        if (!reason) {
            Swal.fire({ icon: 'warning', title: 'Reason Required', text: 'Please enter reason for bounce.' });
            return;
        }

        let submitBtn = $('#btnSubmitBounce');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Processing...');

        $.ajax({
            url: "{{ url('/cheques') }}/" + chqId + "/bounce",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                bounce_reason: reason
            },
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-ban me-1"></i> Confirm Bounce');
                $('#modalBounceCheque').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                Swal.fire({
                    icon: 'success',
                    title: 'Cheque Bounced',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-ban me-1"></i> Confirm Bounce');
                let msg = err.responseJSON ? err.responseJSON.message : 'Failed to mark as bounced.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

    // Run Checks Button
    $('#btnRunChecks').on('click', function() {
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Checking...');

        $.ajax({
            url: '{{ route("cheques.run_checks") }}',
            type: 'GET',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-arrows-rotate me-1"></i> Check Alerts & Due Dates Now');
                
                let detailsHtml = '';
                if (res.details && res.details.length > 0) {
                    detailsHtml = '<div class="text-start mt-3 p-3 bg-light rounded-3 small border">';
                    detailsHtml += '<strong class="text-danger d-block mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Active Low Balance Details:</strong>';
                    detailsHtml += '<ul class="mb-0 ps-3">';
                    res.details.forEach(function(d) {
                        detailsHtml += '<li>' + d + '</li>';
                    });
                    detailsHtml += '</ul></div>';
                }

                let iconType = (res.active_alerts > 0) ? 'warning' : 'success';
                let titleText = (res.active_alerts > 0) ? 'Active Alerts Found' : 'All Accounts Verified';

                Swal.fire({
                    icon: iconType,
                    title: titleText,
                    html: '<div style="white-space: pre-line; font-weight: 500;">' + res.message + '</div>' + detailsHtml,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fa-solid fa-arrows-rotate me-1"></i> Check Alerts & Due Dates Now');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Could not run checks.' });
            }
        });
    });
});
</script>
@endpush
@endsection
