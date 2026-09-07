@extends('admin_panel.layout.app')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

    <style>
        .claim-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            padding: 20px 24px;
            margin-bottom: 20px;
        }
        .claim-header-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .claim-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            display: block;
        }
        .claim-input {
            height: 36px;
            font-size: 0.88rem;
            padding: 4px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #ffffff;
            color: #0f172a;
            width: 100%;
            transition: all 0.15s ease;
        }
        .claim-input:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }
        .claim-input[readonly] {
            background-color: #f8fafc;
        }
        .balance-badge {
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 12px;
            border-radius: 6px;
            font-size: 0.86rem;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }
        .balance-dr {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }
        .balance-cr {
            background: #f0fdf4;
            border-color: #86efac;
            color: #16a34a;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4 px-md-4">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 px-3 small border-0 shadow-sm rounded-3 mb-3">
                        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" style="padding: 0.75rem;"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small border-0 shadow-sm rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" style="padding: 0.75rem;"></button>
                    </div>
                @endif

                <form action="{{ route('claim_payment.store') }}" method="POST" id="claimPaymentForm">
                    @csrf

                    <div class="claim-card">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                            <div class="claim-header-title">
                                <i class="bi bi-receipt-cutoff text-warning"></i> Claim Payment
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('all_expense_vochers') }}" class="btn btn-outline-secondary btn-sm fw-bold d-inline-flex align-items-center me-2" style="height: 34px; font-size: 0.82rem; padding: 4px 14px; border-radius: 6px;">
                                    <i class="bi bi-list-ul me-1"></i> All Expense Vouchers
                                </a>
                                <button type="submit" id="saveBtn" class="btn btn-warning btn-sm fw-bold shadow-sm d-inline-flex align-items-center" style="height: 34px; font-size: 0.82rem; padding: 4px 18px; border-radius: 6px;">
                                    <i class="bi bi-check2 me-1"></i> Save Claim Payment
                                </button>
                            </div>
                        </div>

                        <div class="section-bar" style="background:#f8fafc; border-left:3px solid #f59e0b; padding:6px 12px; font-size:0.8rem; font-weight:700; color:#334155; text-transform:uppercase; margin-bottom:16px;">
                            1. Customer &amp; Claim Details
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-5">
                                <label class="claim-label">Customer</label>
                                <select name="customer_id" id="customerId" class="claim-input" style="height:auto; min-height:36px;" required>
                                    <option value="" disabled selected>Search Customer by Name / Code / Mobile...</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <label class="claim-label">Date</label>
                                <input type="date" name="date" class="claim-input" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <label class="claim-label">Claim Amount</label>
                                <input type="number" name="amount" id="claimAmount" step="0.01" min="0.01" class="claim-input fw-bold" placeholder="0.00" required>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="col-12 p-0">
                                    <label class="claim-label">Current Balance</label>
                                    <div id="balanceDisplay" class="balance-badge balance-dr">0.00 Dr</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <label class="claim-label">Reference No (optional)</label>
                                <input type="text" name="reference_no" class="claim-input" placeholder="e.g. Chq-1029">
                            </div>
                            <div class="col-12">
                                <label class="claim-label">Description (optional)</label>
                                <textarea name="description" id="claimDescription" rows="2" class="claim-input" style="height:auto;" placeholder="Explain the claim reason..."></textarea>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center gap-2 small mt-3 mb-0 py-2 px-3" style="border-radius: 6px; background:#fffbeb; border:1px solid #fcd34d; color:#92400e;">
                            <i class="bi bi-info-circle"></i>
                            Save karne par <strong>Expense Voucher</strong> automatically create hoga aur customer ke <strong>ledger</strong> mein ye amount adjust ho jayega.
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        function initCustomerSelect2() {
            $('#customerId').select2({
                placeholder: 'Search Customer...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route("party.list") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            type: 'customer',
                            search: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    party: item
                                };
                            })
                        };
                    },
                    cache: false
                },
                templateResult: function(item) {
                    if (item.loading) return item.text;
                    if (!item.party) return item.text;
                    const p = item.party;
                    const code = p.customer_id || '';
                    const name = p.customer_name || item.text;
                    const bal = p.closing_balance !== undefined ? parseFloat(p.closing_balance) : 0;
                    const balFormatted = Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
                    const balType = bal >= 0 ? 'Dr' : 'Cr';
                    return $(`<div class="d-flex justify-content-between align-items-center py-1">
                        <div>
                            <div class="fw-bold text-dark">${name}</div>
                            <div class="d-flex gap-2 align-items-center">
                                ${code ? '<small class="text-primary fw-semibold">' + code + '</small>' : ''}
                                ${p.mobile ? '<small class="text-muted"><i class="bi bi-telephone me-1"></i>' + p.mobile + '</small>' : ''}
                            </div>
                        </div>
                        <div class="text-end ms-2">
                            <span class="badge ${bal >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} border px-2 py-1" style="font-size: 0.75rem;">
                                Bal: ${balFormatted} ${balType}
                            </span>
                        </div>
                    </div>`);
                },
                templateSelection: function(item) {
                    if (!item.party) return item.text;
                    const p = item.party;
                    return (p.customer_name || item.text) + ' (Bal: ' + Math.abs(p.closing_balance || 0) + ' ' + (p.closing_balance >= 0 ? 'Dr' : 'Cr') + ')';
                }
            });
        }
        initCustomerSelect2();

        function updateBalance(bal) {
            let $badge = $('#balanceDisplay');
            let formatted = Math.abs(bal).toFixed(2);
            if (bal >= 0) {
                $badge.removeClass('balance-cr').addClass('balance-dr');
                $badge.html(formatted + ' <small>Dr</small>');
            } else {
                $badge.removeClass('balance-dr').addClass('balance-cr');
                $badge.html(formatted + ' <small>Cr</small>');
            }
        }

        $('#customerId').on('select2:select', function(e) {
            let item = e.params.data.party || e.params.data;
            if (!item) return;
            let bal = parseFloat(item.closing_balance) || 0;
            updateBalance(bal);
            let partyName = item.customer_name || '';
            if (!$.trim($('#claimDescription').val()) && partyName) {
                $('#claimDescription').val('Customer Claim - ' + partyName);
            }
        });

        $('#customerId').on('select2:clear', function() {
            updateBalance(0);
        });

        $('#claimPaymentForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);

            let customerId = $('#customerId').val();
            let amount = parseFloat($('#claimAmount').val());

            if (!customerId) {
                Swal.fire('Customer Required', 'Please select a customer.', 'warning');
                return;
            }
            if (!amount || amount <= 0) {
                Swal.fire('Invalid Amount', 'Please enter a valid claim amount.', 'warning');
                return;
            }

            $('#saveBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            $.ajax({
                url: '{{ route("claim_payment.store") }}',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    $('#saveBtn').prop('disabled', false).html('<i class="bi bi-check2 me-1"></i> Save Claim Payment');
                    if (res.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: res.message,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'View Expense Voucher',
                            cancelButtonText: 'New Claim',
                            confirmButtonColor: '#f59e0b'
                        }).then((result) => {
                            if (result.isConfirmed && res.print_url) {
                                window.open(res.print_url, '_blank');
                            } else {
                                form[0].reset();
                                $('#customerId').val(null).trigger('change');
                                updateBalance(0);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    $('#saveBtn').prop('disabled', false).html('<i class="bi bi-check2 me-1"></i> Save Claim Payment');
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
@endsection