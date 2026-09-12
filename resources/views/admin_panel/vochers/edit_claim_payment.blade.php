@extends('admin_panel.layout.app')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

    <style>
        .claim-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            padding: 24px 32px;
            margin-bottom: 24px;
        }
        .claim-header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .claim-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
        }
        .claim-input {
            height: 42px;
            font-size: 0.95rem;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #ffffff;
            color: #0f172a;
            width: 100%;
            transition: all 0.2s ease;
        }
        .claim-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .claim-input[readonly] {
            background-color: #f8fafc;
            color: #64748b;
        }
        textarea.claim-input {
            min-height: 80px;
            resize: vertical;
        }
        .balance-badge {
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .balance-dr {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }
        .balance-cr {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
        }
        .section-bar {
            background: #fefce8;
            border-left: 4px solid #eab308;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #713f12;
            text-transform: uppercase;
            margin-bottom: 24px;
            border-radius: 0 8px 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-custom-outline {
            border: 1px solid #cbd5e1;
            color: #475569;
            background: white;
            font-weight: 600;
            border-radius: 8px;
            height: 38px;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        .btn-custom-outline:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .btn-custom-warning {
            background: #eab308;
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            height: 38px;
            padding: 0 20px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(234, 179, 8, 0.2);
        }
        .btn-custom-warning:hover {
            background: #ca8a04;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(234, 179, 8, 0.25);
        }
        
        /* Select2 custom styling */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 12px !important;
            font-size: 0.95rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4 px-md-4">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
                        <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" style="background-color: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('claim_payment.update', $voucher->id) }}" method="POST" id="claimPaymentForm">
                    @csrf
                    
                    <div class="claim-card">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
                            <div class="claim-header-title">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 d-inline-flex">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                                Edit Claim Payment <span class="text-muted fs-6 ms-2">({{ $voucher->evid }})</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('all_claim_vouchers') }}" class="btn-custom-outline text-decoration-none">
                                    <i class="bi bi-list-ul me-2"></i> All Claim Vouchers
                                </a>
                                <button type="submit" id="saveBtn" class="btn-custom-warning">
                                    <i class="bi bi-check2 me-2"></i> Update Payment
                                </button>
                            </div>
                        </div>

                        <div class="section-bar">
                            <i class="bi bi-person-lines-fill text-warning"></i>
                            Customer &amp; Claim Details
                        </div>

                        <div class="row g-4">
                            <div class="col-12 col-md-5 col-lg-5">
                                <label class="claim-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customerId" class="claim-input" required>
                                    @if($customer)
                                        <option value="{{ $customer->id }}" selected>{{ $customer->customer_name }} - {{ $customer->mobile }}</option>
                                    @else
                                        <option value="" disabled selected>Search by Name / Code / Mobile...</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <label class="claim-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="claim-input" value="{{ $voucher->entry_date }}" required>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <label class="claim-label">Claim Amount <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-stretch" style="height: 42px;">
                                    <span class="bg-light border text-muted d-flex align-items-center px-3" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1 !important; border-right: none !important; font-size: 0.95rem;">Rs</span>
                                    <input type="number" name="amount" id="claimAmount" step="0.01" min="0.01" value="{{ $amount }}" class="claim-input fw-bold m-0" placeholder="0.00" style="border-radius: 0 8px 8px 0; border-left: none !important; height: 100%; flex: 1;" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-3 mt-4 mt-lg-0">
                                <label class="claim-label">Current Balance</label>
                                <div id="balanceDisplay" class="balance-badge {{ ($customer->closing_balance ?? 0) >= 0 ? 'balance-dr' : 'balance-cr' }}">
                                    <span class="text-muted fw-normal"><i class="bi bi-wallet2 me-1"></i> Balance</span>
                                    <span>{{ number_format(abs($customer->closing_balance ?? 0), 2) }} {{ ($customer->closing_balance ?? 0) >= 0 ? 'Dr' : 'Cr' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-12 col-md-5">
                                <label class="claim-label">Reference No</label>
                                <input type="text" name="reference_no" class="claim-input" value="{{ json_decode($voucher->reference_no, true)[0] ?? $voucher->reference_no }}" placeholder="e.g. Chq-1029 / Ref-991">
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="claim-label">Description</label>
                                <input type="text" name="description" id="claimDescription" class="claim-input" placeholder="Explain the reason for this claim..." value="{{ $description }}">
                            </div>
                        </div>

                        <div class="alert mt-4 mb-0 border-0 d-flex align-items-center" style="background-color: #fffbeb; color: #92400e; border-radius: 8px;">
                            <i class="bi bi-info-circle text-warning fs-5 me-3"></i>
                            <div class="small">
                                Update karne par purani ledger aur journal entries reverse ho kar <strong>nayi entries</strong> automatically record ki jayengi.
                            </div>
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
                                <div><div class="fw-bold text-dark" style="font-size:0.85rem">${name}</div><div class="text-muted" style="font-size:0.75rem">${code}</div></div>
                                <div class="text-end"><div class="fw-bold ${bal >= 0 ? 'text-danger' : 'text-success'}" style="font-size:0.85rem">${balFormatted} ${balType}</div></div>
                              </div>`);
                },
                templateSelection: function(item) {
                    if (!item.id) return item.text;
                    if (item.party) {
                        return item.party.customer_name || item.text;
                    }
                    return item.text;
                }
            });
        }

        initCustomerSelect2();

        $('#customerId').on('select2:select', function(e) {
            const data = e.params.data;
            if (data.party) {
                const bal = parseFloat(data.party.closing_balance) || 0;
                const balFormatted = Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                const isDr = bal >= 0;
                const $balDisplay = $('#balanceDisplay');
                
                $balDisplay.removeClass('balance-dr balance-cr')
                           .addClass(isDr ? 'balance-dr' : 'balance-cr')
                           .text(`${balFormatted} ${isDr ? 'Dr' : 'Cr'}`);
            }
        });
        
        $('#claimPaymentForm').on('submit', function(e) {
            const btn = $('#saveBtn');
            btn.html('<i class="spinner-border spinner-border-sm me-1"></i> Updating...').prop('disabled', true);
        });
    });
</script>
@endsection
