@extends('admin_panel.layout.app')
@section('content')
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --primary-lt: #eef2ff;
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --card-radius: 12px;
        }

        .main-content {
            overflow-y: auto;
        }

        /* ── Header Toolbar ── */
        .customer-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 18px;
        }

        .customer-toolbar-title h3 {
            font-size: 1.35rem;
            margin-bottom: 2px;
        }

        .customer-toolbar-title small {
            font-size: 0.83rem;
        }

        .customer-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .btn-action-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 0.86rem;
            transition: all 0.2s ease;
            white-space: nowrap;
            text-decoration: none;
            height: 38px;
        }

        .btn-action-custom:hover {
            transform: translateY(-1px);
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #ffffff !important;
            border: none;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
        }

        .btn-action-primary:hover {
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.4);
            color: #ffffff;
        }

        /* ── Search & Filter Bar ── */
        .customer-filter-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--card-radius);
            padding: 12px 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1 1 280px;
            max-width: 450px;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .search-input-wrapper input {
            width: 100%;
            height: 40px;
            padding: 8px 14px 8px 38px;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.88rem;
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input-wrapper input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .filter-status-pills {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            background: #f8fafc;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .filter-pill:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-pill.active {
            background: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
        }

        .filter-pill .pill-badge {
            background: rgba(0, 0, 0, 0.08);
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 0.72rem;
        }

        .filter-pill.active .pill-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .view-switch-btns {
            display: inline-flex;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .view-switch-btn {
            background: #ffffff;
            border: none;
            padding: 6px 12px;
            font-size: 0.82rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .view-switch-btn.active {
            background: var(--primary-lt);
            color: var(--primary-color);
            font-weight: 700;
        }

        /* ── Desktop Table View ── */
        .desktop-table-card {
            background: #ffffff;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .desktop-table-card table thead th {
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        .desktop-table-card table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
            font-size: 0.88rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .desktop-table-card table tbody tr:hover {
            background-color: #f8fafc;
        }

        .btn-sm i.fa-toggle-on {
            color: #10b981;
            font-size: 18px;
        }

        .btn-sm i.fa-toggle-off {
            color: #94a3b8;
            font-size: 18px;
        }

        /* ── Mobile Customer Cards (Default hidden on desktop) ── */
        .mobile-customer-cards {
            display: none;
        }

        .cust-card {
            background: #ffffff;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.2s, border-color 0.2s;
        }

        .cust-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        .cust-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .cust-card-title-group {
            flex: 1;
            min-width: 0;
        }

        .cust-card-name {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.3;
            word-break: break-word;
        }

        .cust-card-ur {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .cust-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
            margin-top: 5px;
        }

        .cust-code-pill {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 0.72rem;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.02em;
            font-family: monospace;
        }

        .cust-card-body {
            padding: 10px 0;
            font-size: 0.84rem;
        }

        .cust-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 6px;
        }

        .cust-info-row:last-child {
            margin-bottom: 0;
        }

        .cust-info-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cust-info-label {
            font-size: 0.74rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .cust-info-val {
            font-size: 0.86rem;
            color: var(--text-dark);
        }

        .cust-phone-link {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 6px;
            border-radius: 6px;
            background: var(--primary-lt);
        }

        .cust-card-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 6px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }

        .cust-act-btn {
            height: 34px;
            padding: 0 10px;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }

        /* ── Sticky Mobile Add Button ── */
        .mobile-fab-add {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1040;
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #ffffff;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .mobile-fab-add:hover, .mobile-fab-add:active {
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 8px 22px rgba(79, 70, 229, 0.55);
        }

        /* ═══════════════════════════════════════════════════════
           TABLET & MOBILE RESPONSIVE (≤ 768px)
        ═══════════════════════════════════════════════════════ */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
                padding-top: 10px !important;
            }

            .customer-toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 14px;
            }

            .customer-toolbar-title {
                text-align: left;
            }

            .customer-toolbar-title h3 {
                font-size: 1.18rem;
            }

            /* Actions toolbar on mobile */
            .customer-actions {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                width: 100%;
            }

            .customer-actions .btn-add-customer {
                grid-column: span 2;
                height: 44px;
                font-size: 0.92rem;
            }

            .customer-actions .btn-action-custom {
                width: 100%;
                justify-content: center;
                height: 38px;
                font-size: 0.8rem;
                padding: 6px 10px;
            }

            /* Filter Card */
            .customer-filter-card {
                padding: 10px 12px;
                gap: 10px;
            }

            .search-input-wrapper {
                max-width: 100%;
                flex: 1 1 100%;
            }

            .search-input-wrapper input {
                height: 42px;
                font-size: 15px; /* iOS auto-zoom prevention */
            }

            .filter-status-pills {
                width: 100%;
                justify-content: flex-start;
                gap: 5px;
            }

            .filter-pill {
                padding: 5px 10px;
                font-size: 0.74rem;
            }

            .view-switch-btns {
                display: none !important; /* Always cards on small mobile by default */
            }

            /* Toggle desktop table vs cards */
            .desktop-table-card {
                display: none;
            }

            .mobile-customer-cards {
                display: block;
            }

            .mobile-fab-add {
                display: flex;
            }
        }

        /* ═══════════════════════════════════════════════════════
           EXTRA SMALL MOBILE (≤ 480px)
        ═══════════════════════════════════════════════════════ */
        @media (max-width: 480px) {
            .customer-actions {
                grid-template-columns: 1fr 1fr;
            }

            .cust-card {
                padding: 12px;
            }

            .cust-card-actions {
                justify-content: stretch;
            }

            .cust-card-actions .cust-act-btn {
                flex: 1 1 auto;
                justify-content: center;
                height: 36px;
            }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid px-4 py-3">

                <!-- Header & Toolbar -->
                <div class="customer-toolbar">
                    <div class="customer-toolbar-title">
                        <h3 class="mb-0 fw-bold text-dark">Customer List</h3>
                        <small class="text-muted">Manage your customer accounts, balances, import &amp; export data.</small>
                    </div>

                    <div class="customer-actions">
                        @can('customers.create')
                            <a href="{{ route('customers.create') }}" class="btn-action-custom btn-action-primary btn-add-customer">
                                <i class="fas fa-user-plus"></i> + Add New Customer
                            </a>
                        @endcan

                        @can('customers.view')
                            <a href="{{ route('customers.ledger') }}" class="btn btn-info btn-action-custom text-white">
                                <i class="fas fa-book"></i> Ledger
                            </a>
                            <a href="{{ route('customer.payments') }}" class="btn btn-secondary btn-action-custom">
                                <i class="fas fa-money-bill-wave"></i> Payment
                            </a>
                        @endcan

                        <!-- Import CSV (Modal Trigger) -->
                        <button type="button" class="btn btn-outline-primary btn-action-custom" data-toggle="modal" data-target="#customerImportModal" id="openCustomerImportModalBtn" title="Import customers from CSV">
                            <i class="fas fa-file-upload"></i> Import CSV
                        </button>

                        <!-- Export CSV -->
                        <a href="{{ route('customers.export') }}" class="btn btn-outline-success btn-action-custom" title="Export all customers to CSV">
                            <i class="fas fa-file-download"></i> Export CSV
                        </a>

                        <!-- Template Download -->
                        <a href="{{ route('customers.template') }}" class="btn btn-outline-secondary btn-action-custom" title="Download blank CSV template">
                            <i class="fas fa-file-csv text-primary"></i> Template
                        </a>

                        <a href="{{ route('customers.inactive') }}" class="btn btn-outline-danger btn-action-custom">
                            <i class="fas fa-user-slash"></i> Inactive Customers
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @php
                    $totalCustomers = count($customers);
                    $activeCustomers = $customers->where('status', 'active')->count();
                    $inactiveCustomers = $totalCustomers - $activeCustomers;
                @endphp

                <!-- Live Search & Status Filter Bar -->
                <div class="customer-filter-card">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="customerSearchInput" placeholder="Search by name, code, phone, zone..." autocomplete="off">
                    </div>

                    <div class="filter-status-pills">
                        <span class="filter-pill active" data-filter="all">
                            All <span class="pill-badge" id="countAll">{{ $totalCustomers }}</span>
                        </span>
                        <span class="filter-pill" data-filter="active">
                            Active <span class="pill-badge" id="countActive">{{ $activeCustomers }}</span>
                        </span>
                        <span class="filter-pill" data-filter="inactive">
                            Inactive <span class="pill-badge" id="countInactive">{{ $inactiveCustomers }}</span>
                        </span>
                    </div>

                    <div class="view-switch-btns d-none d-md-inline-flex">
                        <button type="button" class="view-switch-btn active" id="btnSwitchTable" title="Table View">
                            <i class="fas fa-table"></i> Table
                        </button>
                        <button type="button" class="view-switch-btn" id="btnSwitchCards" title="Cards View">
                            <i class="fas fa-th-large"></i> Cards
                        </button>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="desktop-table-card" id="desktopTableContainer">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="customersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Credit Limit</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th style="min-width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                @forelse ($customers as $customer)
                                    <tr class="cust-row"
                                        data-search="{{ strtolower($customer->customer_id . ' ' . $customer->customer_name . ' ' . $customer->customer_name_ur . ' ' . $customer->mobile . ' ' . $customer->customer_type) }}"
                                        data-status="{{ $customer->status === 'active' ? 'active' : 'inactive' }}">
                                        <td><strong>{{ $customer->customer_id }}</strong></td>
                                        <td>
                                            <strong>{{ $customer->customer_name }}</strong>
                                            @if($customer->customer_name_ur)
                                                <small class="text-muted d-block" dir="rtl">{{ $customer->customer_name_ur }}</small>
                                            @endif
                                            @if($customer->customer_type)
                                                <span class="badge bg-light text-dark border">{{ $customer->customer_type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($customer->mobile)
                                                <a href="tel:{{ $customer->mobile }}" class="text-dark text-decoration-none">
                                                    {{ $customer->mobile }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $customer->balance_range == 0 ? 'Unlimited' : number_format($customer->balance_range, 0) }}</td>
                                        <td>
                                            @if($customer->status === 'active')
                                                <span class="badge bg-success text-white">active</span>
                                            @else
                                                <span class="badge bg-secondary text-white">inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $source = $customer->source ?? 'Manual';
                                            @endphp
                                            @if($source === 'Website')
                                                <span class="badge bg-success">Website</span>
                                            @elseif($source === 'Both')
                                                <span class="badge bg-info text-dark">Both</span>
                                            @else
                                                <span class="badge bg-secondary">Manual</span>
                                            @endif
                                        </td>
                                        <td>
                                            @include('admin_panel.partials.action_buttons', [
                                                'editRoute' => route('customers.edit', $customer->id),
                                                'deleteRoute' => route('customers.destroy', $customer->id),
                                                'editIsLink' => true,
                                                'permissions' => [
                                                    'edit' => 'customers.edit',
                                                    'delete' => 'customers.delete',
                                                ],
                                                'dataId' => $customer->id,
                                            ])

                                            @can('customers.edit')
                                                <a href="{{ route('customers.toggleStatus', $customer->id) }}"
                                                    class="btn btn-sm {{ $customer->status === 'active' ? 'btn-dark' : 'btn-secondary' }}"
                                                    title="Toggle Status">
                                                    <i
                                                        class="fa-solid {{ $customer->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </a>
                                            @endcan
                                            @can('customers.view')
                                                <a href="{{ route('customer.payments') }}" class="btn btn-sm btn-info" title="Customer Payments">Payments</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyTableRow">
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No customers found. Click <strong>+ Add New Customer</strong> or <strong>Import CSV</strong> to add customers.
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsTableRow" style="display: none;">
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-search me-1"></i> No matching customers found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Customer Cards View -->
                <div class="mobile-customer-cards" id="mobileCardsContainer">
                    @forelse ($customers as $customer)
                        <div class="cust-card cust-card-item"
                             data-search="{{ strtolower($customer->customer_id . ' ' . $customer->customer_name . ' ' . $customer->customer_name_ur . ' ' . $customer->mobile . ' ' . $customer->customer_type) }}"
                             data-status="{{ $customer->status === 'active' ? 'active' : 'inactive' }}">
                            
                            <!-- Header -->
                            <div class="cust-card-header">
                                <div class="cust-card-title-group">
                                    <div class="cust-card-name">{{ $customer->customer_name }}</div>
                                    @if($customer->customer_name_ur)
                                        <div class="cust-card-ur" dir="rtl">{{ $customer->customer_name_ur }}</div>
                                    @endif
                                    <div class="cust-card-meta">
                                        <span class="cust-code-pill">{{ $customer->customer_id }}</span>
                                        @if($customer->customer_type)
                                            <span class="badge bg-light text-dark border">{{ $customer->customer_type }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="cust-card-status">
                                    @if($customer->status === 'active')
                                        <span class="badge bg-success text-white">Active</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Body Info -->
                            <div class="cust-card-body">
                                <div class="cust-info-row">
                                    <div class="cust-info-item">
                                        <span class="cust-info-label"><i class="fas fa-phone-alt me-1 text-primary"></i> Mobile:</span>
                                        @if($customer->mobile)
                                            <a href="tel:{{ $customer->mobile }}" class="cust-phone-link">
                                                {{ $customer->mobile }} <i class="fas fa-phone ms-1 text-success" style="font-size: 10px;"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>

                                    <div class="cust-info-item">
                                        <span class="cust-info-label"><i class="fas fa-shield-alt me-1 text-info"></i> Limit:</span>
                                        <span class="cust-info-val fw-bold">
                                            {{ $customer->balance_range == 0 ? 'Unlimited' : number_format($customer->balance_range, 0) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="cust-info-row mt-1">
                                    <div class="cust-info-item">
                                        <span class="cust-info-label"><i class="fas fa-database me-1 text-muted"></i> Source:</span>
                                        <span>
                                            @php
                                                $src = $customer->source ?? 'Manual';
                                            @endphp
                                            @if($src === 'Website')
                                                <span class="badge bg-success">Website</span>
                                            @elseif($src === 'Both')
                                                <span class="badge bg-info text-dark">Both</span>
                                            @else
                                                <span class="badge bg-secondary">Manual</span>
                                            @endif
                                        </span>
                                    </div>

                                    @if($customer->reminder_day)
                                        <div class="cust-info-item">
                                            <span class="cust-info-label"><i class="fas fa-calendar-day me-1 text-warning"></i> Reminder:</span>
                                            <span class="badge bg-warning text-dark">{{ $customer->reminder_day }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="cust-card-actions">
                                @can('customers.edit')
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary cust-act-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endcan

                                @can('customers.view')
                                    <a href="{{ route('customer.payments') }}" class="btn btn-sm btn-info text-white cust-act-btn">
                                        <i class="fas fa-money-bill-wave"></i> Payment
                                    </a>
                                @endcan

                                @can('customers.edit')
                                    <a href="{{ route('customers.toggleStatus', $customer->id) }}"
                                        class="btn btn-sm {{ $customer->status === 'active' ? 'btn-outline-dark' : 'btn-outline-secondary' }} cust-act-btn"
                                        title="Toggle Status">
                                        <i class="fa-solid {{ $customer->status === 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' }}"></i>
                                        <span>{{ $customer->status === 'active' ? 'Active' : 'Inactive' }}</span>
                                    </a>
                                @endcan

                                @can('customers.delete')
                                    <button class="btn btn-sm btn-outline-danger cust-act-btn delete-btn"
                                            data-id="{{ $customer->id }}"
                                            data-url="{{ route('customers.destroy', $customer->id) }}"
                                            data-msg="Are you sure you want to delete customer {{ $customer->customer_name }}?"
                                            data-method="get"
                                            onclick="logoutAndDeleteFunction(this)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                @endcan
                            </div>

                        </div>
                    @empty
                        <div class="card p-4 text-center text-muted">
                            No customers found. Click <strong>+ Add New Customer</strong> or <strong>Import CSV</strong> to add customers.
                        </div>
                    @endforelse

                    <div id="noResultsMobileCard" class="card p-4 text-center text-muted" style="display: none;">
                        <i class="fas fa-search me-1"></i> No matching customers found.
                    </div>
                </div>

                <!-- Floating Mobile Add Button -->
                @can('customers.create')
                    <a href="{{ route('customers.create') }}" class="mobile-fab-add" title="Add Customer">
                        <i class="fas fa-plus"></i>
                    </a>
                @endcan

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CUSTOMER IMPORT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="customerImportModal" tabindex="-1" role="dialog" aria-labelledby="customerImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; border-bottom: none; padding: 16px 20px;">
                    <div>
                        <h5 class="modal-title fw-bold" id="customerImportModalLabel" style="font-size: 1.15rem;">
                            <i class="fas fa-file-upload me-2"></i> Import Customers from CSV
                        </h5>
                        <small style="color: rgba(255,255,255,0.88); font-size: 0.8rem;">
                            Create new customers or update existing records directly from spreadsheet.
                        </small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.9; text-shadow:none; outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-3 p-md-4" style="background:#f8fafc;">
                    <form action="{{ route('customers.import.validate') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info d-flex gap-2 align-items-start mb-3 mb-md-4" style="font-size:.85rem; border-left: 4px solid #3b82f6;">
                            <i class="fas fa-info-circle fs-5 mt-1 flex-shrink-0 text-primary"></i>
                            <div>
                                <strong>Quick Guide:</strong><br>
                                &bull; <strong>Create New:</strong> Download the <a href="{{ route('customers.template') }}" class="alert-link fw-bold">Blank Template</a>, fill it, and import.<br>
                                &bull; <strong>Edit Existing:</strong> Download data via <a href="{{ route('customers.export') }}" class="alert-link fw-bold">Export CSV</a>, update in Excel, and upload here.<br>
                                &bull; Matched accurately by <strong>Customer ID</strong> or <strong>System ID</strong>.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="fw-bold text-dark" style="font-size: 0.88rem;">Import Mode</label>
                                <select name="import_mode" class="form-control" required style="border-radius: 8px; height: 42px;">
                                    <option value="upsert" selected>Create &amp; Update (Recommended)</option>
                                    <option value="create_only">Create Only (Skip existing)</option>
                                    <option value="update_only">Update Only (Ignore new)</option>
                                </select>
                                <small class="text-muted d-block mt-1">Controls whether existing records are updated or skipped.</small>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label class="fw-bold text-dark" style="font-size: 0.88rem;">Auto-create Missing Data</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="autoCreateCustCheck" name="auto_create" value="1" checked>
                                    <label class="form-check-label fw-bold ms-1" for="autoCreateCustCheck">
                                        Auto-create missing Types &amp; Zones
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">Automatically registers new Customer Types or Zones found in the CSV.</small>
                            </div>
                        </div>

                        <div class="form-group mb-3 mb-md-4">
                            <label class="fw-bold text-dark" style="font-size: 0.88rem;">Upload CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" class="form-control p-1" accept=".csv,.txt" required style="border-radius: 8px; height: 44px;">
                            <small class="text-muted">Supported formats: .csv, .txt (UTF-8 encoded). Max size: 10 MB.</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top flex-wrap" style="gap: 10px;">
                            <a href="{{ route('customers.template') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 8px; height: 38px; display: inline-flex; align-items: center;">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>

                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px; height: 38px;">Cancel</button>
                                <button type="submit" name="action_type" value="preview" class="btn btn-primary px-3 fw-bold" style="border-radius: 8px; height: 38px;">
                                    <i class="fas fa-eye me-1"></i> Validate &amp; Preview
                                </button>
                                <button type="submit" name="action_type" value="direct" class="btn btn-success px-3 fw-bold" style="border-radius: 8px; height: 38px;">
                                    <i class="fas fa-bolt me-1"></i> Direct Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById('customerSearchInput');
            const statusPills = document.querySelectorAll('.filter-pill');
            const tableRows = document.querySelectorAll('.cust-row');
            const cards = document.querySelectorAll('.cust-card-item');
            const noResultsTableRow = document.getElementById('noResultsTableRow');
            const noResultsMobileCard = document.getElementById('noResultsMobileCard');

            let currentStatus = 'all';
            let currentSearch = '';

            function filterItems() {
                let visibleCount = 0;
                let activeCount = 0;
                let inactiveCount = 0;

                // Filter Table Rows
                tableRows.forEach(row => {
                    const rowSearch = row.getAttribute('data-search') || '';
                    const rowStatus = row.getAttribute('data-status') || '';

                    const matchesSearch = !currentSearch || rowSearch.includes(currentSearch);
                    const matchesStatus = currentStatus === 'all' || rowStatus === currentStatus;

                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Filter Mobile Cards
                let visibleCardsCount = 0;
                cards.forEach(card => {
                    const cardSearch = card.getAttribute('data-search') || '';
                    const cardStatus = card.getAttribute('data-status') || '';

                    const matchesSearch = !currentSearch || cardSearch.includes(currentSearch);
                    const matchesStatus = currentStatus === 'all' || cardStatus === currentStatus;

                    if (matchesSearch && matchesStatus) {
                        card.style.display = '';
                        visibleCardsCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Empty / No results display
                if (noResultsTableRow) {
                    noResultsTableRow.style.display = (visibleCount === 0 && tableRows.length > 0) ? '' : 'none';
                }
                if (noResultsMobileCard) {
                    noResultsMobileCard.style.display = (visibleCardsCount === 0 && cards.length > 0) ? '' : 'none';
                }
            }

            // Search input live event
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    currentSearch = this.value.trim().toLowerCase();
                    filterItems();
                });
            }

            // Status filter pill click
            statusPills.forEach(pill => {
                pill.addEventListener('click', function () {
                    statusPills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    currentStatus = this.getAttribute('data-filter') || 'all';
                    filterItems();
                });
            });

            // View switch buttons (desktop)
            const btnSwitchTable = document.getElementById('btnSwitchTable');
            const btnSwitchCards = document.getElementById('btnSwitchCards');
            const desktopTable = document.getElementById('desktopTableContainer');
            const mobileCards = document.getElementById('mobileCardsContainer');

            if (btnSwitchTable && btnSwitchCards && desktopTable && mobileCards) {
                btnSwitchTable.addEventListener('click', function () {
                    btnSwitchTable.classList.add('active');
                    btnSwitchCards.classList.remove('active');
                    desktopTable.style.display = 'block';
                    mobileCards.style.display = 'none';
                });

                btnSwitchCards.addEventListener('click', function () {
                    btnSwitchCards.classList.add('active');
                    btnSwitchTable.classList.remove('active');
                    desktopTable.style.display = 'none';
                    mobileCards.style.display = 'block';
                });
            }
        });
    </script>
@endsection
