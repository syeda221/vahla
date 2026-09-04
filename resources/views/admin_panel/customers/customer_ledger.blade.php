@extends('admin_panel.layout.app')

@section('content')
    <style>
        .ledger-wrapper {
            padding: 18px 22px;
            background-color: #f8fafc;
            min-height: calc(100vh - 70px);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* Clean Card Design */
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
        }

        .filter-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            display: block;
        }

        .form-control-clean,
        .form-select-clean {
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.84rem;
            font-weight: 500;
            color: #1e293b;
            padding: 6px 12px;
            background-color: #ffffff;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control-clean:focus,
        .form-select-clean:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }

        /* Metric Cards */
        .metric-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .metric-card.border-top-primary { border-top: 3px solid #3b82f6; }
        .metric-card.border-top-danger  { border-top: 3px solid #ef4444; }
        .metric-card.border-top-success { border-top: 3px solid #10b981; }
        .metric-card.border-top-info    { border-top: 3px solid #0ea5e9; }

        .metric-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .metric-val {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .metric-sub {
            font-size: 0.74rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 6px;
        }

        /* Tables */
        .table-custom {
            font-size: 0.82rem;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom thead th {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 10px 14px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            color: #1e293b;
            font-weight: 500;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .table-custom tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Select2 Modern Dropdown Row-to-Row Styling */
        .select2-container--default .select2-dropdown {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
            background: #ffffff !important;
        }

        .select2-container--default .select2-results > .select2-results__options {
            max-height: 280px !important;
        }

        .select2-container--default .select2-results__option {
            padding: 9px 14px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            transition: all 0.15s ease !important;
            font-size: 0.83rem !important;
            color: #1e293b !important;
        }

        .select2-container--default .select2-results__option:last-child {
            border-bottom: none !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            font-weight: 700 !important;
        }

        .select2-container--default .select2-search--dropdown {
            padding: 8px 10px !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
            font-size: 0.83rem !important;
            outline: none !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #3b82f6 !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            display: flex !important;
            align-items: center !important;
            background: #ffffff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            font-weight: 600 !important;
            font-size: 0.84rem !important;
            color: #1e293b !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }

        /* Clean Modern Badges */
        .badge-main-clean {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-sub-clean {
            background-color: #f0fdfa;
            color: #0f766e;
            border: 1px solid #99f6e4;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .meta-tag {
            color: #475569;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .meta-tag strong {
            color: #0f172a;
            font-weight: 700;
        }

        .meta-divider {
            color: #cbd5e1;
            font-weight: 300;
            margin: 0 6px;
        }

        @media print {
            .no-print { display: none !important; }
            .ledger-wrapper { background: #ffffff !important; padding: 0 !important; }
            .card-custom, .filter-card { border: none !important; box-shadow: none !important; }
            .table-custom thead th { background-color: #000000 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; }
        }
    </style>

    <div class="ledger-wrapper">
        <div class="container-fluid p-0">

            <!-- Top Header & Breadcrumb -->
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-white border rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px; background:#fff;" title="Back">
                        <i class="fa fa-arrow-left text-secondary" style="font-size: 12px;"></i>
                    </a>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.15rem; letter-spacing: -0.2px; gap: 8px;">
                            <i class="fa fa-book text-primary"></i> Customer Ledger
                        </h5>
                        <span class="text-muted fw-normal" style="font-size: 0.75rem;">Account statements, invoices and consolidated sub-customer reports</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if(request('customer_id'))
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary fw-bold px-3 d-flex align-items-center shadow-sm" style="height: 34px; font-size: 0.8rem; gap: 6px;">
                            <i class="fa fa-print"></i> Print Statement
                        </button>
                    @endif
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-light border fw-bold px-3 text-secondary shadow-sm d-flex align-items-center" style="height: 34px; font-size: 0.8rem; gap: 6px;">
                        <i class="fa fa-users"></i> Customers List
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="filter-card no-print">
                <form method="GET" action="{{ route('customers.ledger') }}" class="row g-3 align-items-end">
                    
                    <!-- Customer Selector -->
                    <div class="col-md-4">
                        <label class="filter-label">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm select2" id="filter_customer_id" style="width: 100%;">
                            <option value="">-- Select Customer --</option>
                            @php
                                $parents = $customers->whereNull('parent_id');
                            @endphp
                            @foreach ($parents as $parent)
                                @php
                                    $children = $customers->where('parent_id', $parent->id);
                                @endphp
                                <option value="{{ $parent->id }}"
                                    {{ request('customer_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->customer_name }} ({{ $parent->customer_id }}) {{ $children->count() > 0 ? '— [Main Customer]' : '' }}
                                </option>
                                @foreach ($children as $child)
                                    <option value="{{ $child->id }}"
                                        {{ request('customer_id') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;↳ Sub: {{ $child->customer_name }} ({{ $child->customer_id }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-2">
                        <label class="filter-label">From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', '2000-01-01') }}" class="form-control form-control-clean">
                    </div>
                    <div class="col-md-2">
                        <label class="filter-label">To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', date('Y-m-d')) }}" class="form-control form-control-clean">
                    </div>

                    <!-- Include Sub Customers Toggle -->
                    <div class="col-md-2">
                        <label class="filter-label">Sub-Accounts</label>
                        <div class="form-check form-switch d-flex align-items-center" style="height: 38px; margin-bottom: 0; gap: 8px;">
                            <input class="form-check-input mt-0" type="checkbox" name="include_sub" value="1" id="include_sub"
                                {{ request('include_sub', '1') == '1' ? 'checked' : '' }} style="cursor: pointer; width: 34px; height: 18px;">
                            <label class="form-check-label fw-semibold text-dark mb-0" for="include_sub" style="font-size: 0.8rem; cursor: pointer; white-space: nowrap;">
                                Include Sub-Customers
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-2">
                        <div class="d-flex" style="gap: 8px;">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="height: 38px; font-size: 0.82rem; border-radius: 6px; gap: 6px;">
                                <i class="fa fa-filter"></i> View Ledger
                            </button>
                            <a href="{{ route('customers.ledger') }}" class="btn btn-light border btn-sm d-flex align-items-center justify-content-center text-secondary shadow-sm" style="width: 38px; height: 38px; border-radius: 6px;" title="Reset Filters">
                                <i class="fa fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            @if(request('customer_id') && isset($selectedCustomer))
                <!-- Active Customer Profile Card -->
                <div class="card-custom p-3 bg-white mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
                        <div class="d-flex align-items-center" style="gap: 16px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; min-width: 48px; background: #eff6ff; color: #2563eb; font-size: 1.25rem;">
                                <i class="fa fa-user"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 14px; margin-bottom: 6px;">
                                    <h4 class="fw-bold text-dark mb-0" style="font-size: 1.25rem; letter-spacing: -0.2px;">{{ $selectedCustomer->customer_name }}</h4>
                                    @if($selectedCustomer->parent)
                                        <span class="badge-sub-clean" style="margin-left: 4px;">
                                            <i class="fa fa-sitemap" style="margin-right: 6px;"></i> Sub-Customer of: <strong style="margin-left: 4px;">{{ $selectedCustomer->parent->customer_name }}</strong>
                                        </span>
                                    @elseif($isConsolidated)
                                        <span class="badge-main-clean" style="margin-left: 4px;">
                                            <i class="fa fa-sitemap" style="margin-right: 6px;"></i> Consolidated Statement <span class="fw-normal text-muted" style="margin-left: 4px;">(Sub-Customers Included)</span>
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border fw-bold px-2.5 py-1" style="font-size: 0.72rem; border-radius: 5px; margin-left: 4px;">Customer Account</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                                    <span class="meta-tag">
                                        <i class="fa fa-tag text-muted" style="margin-right: 4px;"></i> Code: <strong>{{ $selectedCustomer->customer_id }}</strong>
                                    </span>
                                    @if($selectedCustomer->mobile)
                                        <span class="meta-divider">•</span>
                                        <span class="meta-tag">
                                            <i class="fa fa-phone text-muted" style="margin-right: 4px;"></i> Mobile: <strong>{{ $selectedCustomer->mobile }}</strong>
                                        </span>
                                    @endif
                                    @if($selectedCustomer->address)
                                        <span class="meta-divider">•</span>
                                        <span class="meta-tag">
                                            <i class="fa fa-map-marker text-muted" style="margin-right: 4px;"></i> Address: <strong>{{ $selectedCustomer->address }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark border px-3 py-2 fw-semibold shadow-xs" style="font-size: 0.78rem; border-radius: 6px;">
                                <i class="fa fa-calendar text-primary" style="margin-right: 6px;"></i> {{ \Carbon\Carbon::parse(request('from_date', '2000-01-01'))->format('d M, Y') }} — {{ \Carbon\Carbon::parse(request('to_date', date('Y-m-d')))->format('d M, Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 3 Metric Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="metric-card border-top-primary">
                            <div>
                                <div class="metric-label">Opening Balance</div>
                                <div class="metric-val text-dark">
                                    Rs. {{ number_format(abs($opening_balance ?? 0), 2) }}
                                    <small class="fs-6 text-muted fw-bold">{{ ($opening_balance ?? 0) >= 0 ? 'Dr' : 'Cr' }}</small>
                                </div>
                            </div>
                            <div class="metric-sub">
                                <i class="far fa-clock me-1 text-primary"></i> As of {{ \Carbon\Carbon::parse(request('from_date', '2000-01-01'))->format('d M, Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @php
                            $cb = $closing_balance ?? 0;
                            $borderClass = $cb > 0 ? 'border-top-danger' : ($cb < 0 ? 'border-top-success' : 'border-top-primary');
                            $textClass = $cb > 0 ? 'text-danger' : ($cb < 0 ? 'text-success' : 'text-primary');
                        @endphp
                        <div class="metric-card {{ $borderClass }}">
                            <div>
                                <div class="metric-label">{{ $isConsolidated ? 'Consolidated Total Balance' : 'Net Closing Balance' }}</div>
                                <div class="metric-val {{ $textClass }}">
                                    Rs. {{ number_format(abs($cb), 2) }}
                                    <small class="fs-6 fw-bold">{{ $cb >= 0 ? 'Dr' : 'Cr' }}</small>
                                </div>
                            </div>
                            <div class="metric-sub {{ $textClass }}">
                                {{ $cb > 0 ? '● Receivable (Customer Owes)' : ($cb < 0 ? '● Advance (We Owe)' : '● Settled / Zero Balance') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card border-top-info">
                            <div>
                                <div class="metric-label">Total Transactions</div>
                                <div class="metric-val text-dark">
                                    {{ $CustomerLedgers->count() }} <small class="fs-6 text-muted fw-bold">Entries</small>
                                </div>
                            </div>
                            <div class="metric-sub">
                                <i class="fas fa-exchange-alt me-1 text-info"></i> {{ $isConsolidated ? 'Across Main & All Sub-Customers' : 'In Selected Date Period' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Customers Breakdown Summary (Shown if Main Customer has sub-customers) -->
                @if(!empty($subCustomerBreakdown) && count($subCustomerBreakdown) > 1)
                    <div class="card-custom mb-3 overflow-hidden">
                        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-sitemap text-primary fs-6"></i>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.92rem; letter-spacing: -0.1px;">Sub-Customers Balance Breakdown</h6>
                            </div>
                            <span class="badge bg-primary px-2.5 py-1 fw-semibold" style="font-size: 0.73rem;">{{ count($subCustomerBreakdown) }} Sub-Accounts</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Customer / Sub-Customer</th>
                                        <th class="text-end">Opening Balance</th>
                                        <th class="text-end">Debit (Sales)</th>
                                        <th class="text-end">Credit (Receipts)</th>
                                        <th class="text-end">Period Closing</th>
                                        <th class="text-end">Current Balance</th>
                                        <th class="text-center no-print" style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totOb = 0;
                                        $totDeb = 0;
                                        $totCred = 0;
                                        $totCb = 0;
                                        $totCur = 0;
                                    @endphp
                                    @foreach($subCustomerBreakdown as $b)
                                        @php
                                            $totOb += $b['opening_balance'];
                                            $totDeb += $b['total_debit'];
                                            $totCred += $b['total_credit'];
                                            $totCb += $b['closing_balance'];
                                            $totCur += $b['current_total_balance'];
                                        @endphp
                                        <tr style="{{ $b['is_main'] ? 'background-color: #f8fafc;' : '' }}">
                                            <td>
                                                @if($b['is_main'])
                                                    <span class="badge-main-clean"><i class="fas fa-user"></i> {{ $b['name'] }} (Main)</span>
                                                @else
                                                    <span class="ms-3 text-dark fw-semibold"><i class="fas fa-code-branch text-muted me-1"></i> {{ $b['name'] }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end font-monospace">Rs. {{ number_format(abs($b['opening_balance']), 2) }} <small class="text-muted">{{ $b['opening_balance'] >= 0 ? 'Dr' : 'Cr' }}</small></td>
                                            <td class="text-end font-monospace text-success fw-bold">Rs. {{ number_format($b['total_debit'], 2) }}</td>
                                            <td class="text-end font-monospace text-danger fw-bold">Rs. {{ number_format($b['total_credit'], 2) }}</td>
                                            <td class="text-end font-monospace fw-bold text-dark">Rs. {{ number_format(abs($b['closing_balance']), 2) }} <small class="text-muted">{{ $b['closing_balance'] >= 0 ? 'Dr' : 'Cr' }}</small></td>
                                            <td class="text-end font-monospace fw-bold text-primary">Rs. {{ number_format(abs($b['current_total_balance']), 2) }} <small class="text-muted">{{ $b['current_total_balance'] >= 0 ? 'Dr' : 'Cr' }}</small></td>
                                            <td class="text-center no-print">
                                                <a href="{{ route('customers.ledger', ['customer_id' => $b['id'], 'from_date' => request('from_date', '2000-01-01'), 'to_date' => request('to_date', date('Y-m-d')), 'include_sub' => 0]) }}" 
                                                   class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2 fw-semibold shadow-xs" style="font-size: 0.74rem;">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background-color: #f1f5f9; color: #1e293b;">
                                    <tr class="fw-bold" style="border-top: 2px solid #cbd5e1;">
                                        <td class="text-dark fw-bold">TOTALS</td>
                                        <td class="text-end font-monospace fw-bold">Rs. {{ number_format(abs($totOb), 2) }}</td>
                                        <td class="text-end font-monospace fw-bold text-success">Rs. {{ number_format($totDeb, 2) }}</td>
                                        <td class="text-end font-monospace fw-bold text-danger">Rs. {{ number_format($totCred, 2) }}</td>
                                        <td class="text-end font-monospace fw-bold text-dark">Rs. {{ number_format(abs($totCb), 2) }}</td>
                                        <td class="text-end font-monospace fw-bold text-primary">Rs. {{ number_format(abs($totCur), 2) }}</td>
                                        <td class="no-print"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Main Transactions Ledger Table -->
                <div class="card-custom overflow-hidden">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.92rem; letter-spacing: -0.1px;">
                            <i class="fas fa-list text-primary"></i> Statement Entries
                        </h6>
                        <span class="badge bg-secondary px-2.5 py-1 fw-semibold" style="font-size: 0.73rem;">{{ $CustomerLedgers->count() }} Transactions</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0" id="ledger-table">
                            <thead>
                                <tr>
                                    <th width="4%" class="text-center">#</th>
                                    <th width="11%">Date</th>
                                    @if($isConsolidated)
                                        <th width="18%">Customer / Sub-Customer</th>
                                    @endif
                                    <th width="{{ $isConsolidated ? '31%' : '45%' }}">Description / Particulars</th>
                                    <th width="12%" class="text-end">Debit (Dr)</th>
                                    <th width="12%" class="text-end">Credit (Cr)</th>
                                    <th width="12%" class="text-end">Running Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($CustomerLedgers as $key => $ledger)
                                    @php
                                        $debit = $ledger->debit ?? 0;
                                        $credit = $ledger->credit ?? 0;
                                        $balance = $ledger->closing_balance;
                                        $suffix = $balance >= 0 ? 'Dr' : 'Cr';
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-semibold text-muted" style="font-size: 0.75rem;">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold text-dark">{{ $ledger->created_at->format('d-M-Y') }}</td>
                                        @if($isConsolidated)
                                            <td>
                                                @if($ledger->is_sub)
                                                    <span class="badge-sub-clean">
                                                        <i class="fas fa-code-branch"></i> {{ $ledger->party_name }}
                                                    </span>
                                                @else
                                                    <span class="badge-main-clean">
                                                        <i class="fas fa-user"></i> Main Customer
                                                    </span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <span class="text-dark fw-medium">{{ $ledger->description }}</span>
                                        </td>
                                        <td class="text-end font-monospace text-success fw-bold" style="font-size: 0.86rem;">
                                            {{ $debit > 0 ? 'Rs. ' . number_format($debit, 2) : '—' }}
                                        </td>
                                        <td class="text-end font-monospace text-danger fw-bold" style="font-size: 0.86rem;">
                                            {{ $credit > 0 ? 'Rs. ' . number_format($credit, 2) : '—' }}
                                        </td>
                                        <td class="text-end font-monospace fw-bold {{ $balance > 0 ? 'text-danger' : ($balance < 0 ? 'text-success' : 'text-dark') }}" style="font-size: 0.88rem;">
                                            Rs. {{ number_format(abs($balance), 2) }}
                                            <small class="text-muted fw-normal" style="font-size:0.75rem;">{{ $suffix }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isConsolidated ? 7 : 6 }}" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                            <span class="fw-bold fs-6">No transactions found for this customer in the selected date range.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                <!-- No Customer Selected State -->
                <div class="card-custom p-5 text-center">
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 76px; height: 76px; border-width: 2px !important;">
                        <i class="fas fa-search text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Select a Customer to View Ledger</h5>
                    <p class="text-muted fw-semibold small mx-auto mb-0" style="max-width: 500px;">
                        Choose a Main Customer or Sub-Customer from the dropdown above and click <strong>"View Ledger"</strong> to generate the account statement.
                    </p>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($('.select2').length > 0) {
                $('.select2').select2({
                    width: '100%'
                });
            }
        });
    </script>
@endpush
