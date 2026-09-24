@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Modern Sales Management Styles */
        .sale-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            height: 100%;
        }
        .sale-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .sale-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Clean & Bold Filter Panel */
        .filter-panel {
            background-color: #f8fafc !important;
            border: 2px dashed #cbd5e1 !important;
            border-radius: 12px !important;
            padding: 16px !important;
        }
        
        .filter-panel label {
            font-size: 11px;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-panel .form-control,
        .filter-panel .form-select {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            transition: all 0.2s ease-in-out;
            height: 38px !important;
            font-size: 13px !important;
        }
        
        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }

        /* Select2 In Filter Panel */
        .filter-panel .select2-container {
            width: 100% !important;
        }
        .filter-panel .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 5px 8px !important;
            background-color: #ffffff !important;
        }
        .filter-panel .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding-left: 0 !important;
        }
        .filter-panel .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 6px !important;
        }
        .filter-panel .select2-container--default.select2-container--focus .select2-selection--single,
        .filter-panel .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        .select2-dropdown {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
            font-size: 13px !important;
        }

        /* Premium Buttons */
        .btn-premium-primary {
            background-color: #2563eb !important;
            border: 1.5px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            height: 38px !important;
            padding: 0 16px !important;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-premium-primary:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
        }
        
        .btn-premium-secondary {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            height: 38px !important;
            padding: 0 16px !important;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-premium-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
        }

        /* Premium Table Styling */
        .premium-card {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            background-color: #ffffff;
        }

        .premium-table {
            border: 2px solid #475569 !important;
            border-radius: 8px !important;
            overflow: visible !important;
        }

        /* Prevent Dropdowns from Being Clipped on Main Sales Table */
        .premium-card .table-responsive {
            border-radius: 8px !important;
            overflow: visible !important;
            min-height: 380px;
        }

        /* Order Trail Modal overrides to eliminate any empty bottom space and scrollbars */
        #orderTrailModal .modal-content {
            border-radius: 12px !important;
            overflow: hidden !important;
        }
        #orderTrailModal .modal-body {
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }
        #orderTrailModal div,
        #orderTrailModal table,
        #orderTrailModal .card,
        #orderTrailModal .card-body,
        #orderTrailModal .table-responsive {
            min-height: auto !important;
        }

        /* Premium Dropdown Menu Customizations */
        .dropdown-menu {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            padding: 6px 0 !important;
            z-index: 1060 !important;
        }
        .dropdown-item {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            padding: 8px 16px !important;
            transition: all 0.15s ease-in-out !important;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .dropdown-divider {
            border-top: 1.5px solid #e2e8f0 !important;
            margin: 6px 0 !important;
        }
        
        .premium-table thead th {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border-bottom: 3px solid #475569 !important;
            border-right: 1.5px solid #cbd5e1 !important;
            padding: 12px 10px !important;
        }
        
        .premium-table tbody td {
            border: 1.5px solid #e2e8f0 !important;
            padding: 12px 10px !important;
            font-size: 13px !important;
            color: #334155 !important;
            background-color: #ffffff;
        }
        
        .premium-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Dropdown Action Button */
        .btn-premium-action {
            background-color: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            height: 32px !important;
            padding: 0 12px !important;
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease-in-out !important;
        }
        .btn-premium-action:hover, 
        .btn-premium-action:focus, 
        .btn-premium-action[aria-expanded="true"] {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
        }

        /* Responsive Breakpoints (< 768px) */
        @media (max-width: 768px) {
            .sales-hdr-actions {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                width: 100%;
            }
            .sales-hdr-actions .btn {
                width: 100%;
                justify-content: center;
                height: 38px;
                font-size: 0.8rem;
            }
            .sales-status-pills {
                display: flex !important;
                gap: 6px;
                overflow-x: auto;
                padding-bottom: 6px;
                -webkit-overflow-scrolling: touch;
            }
            .sales-status-pills .btn {
                flex: 0 0 auto;
                white-space: nowrap;
            }
            /* DataTables Mobile Search Controls */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                margin-bottom: 10px;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
        @media (min-width: 769px) {
            .sales-hdr-actions {
                display: flex;
                gap: 8px;
            }
            .sales-status-pills {
                display: flex;
                gap: 8px;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .dataTables_scrollBody {
                overflow: visible !important;
            }
        }

        /* Force all DataTables wrappers and columns to not clip dropdowns */
        .dataTables_wrapper,
        .dataTables_wrapper .row,
        .dataTables_wrapper .col-sm-12 {
            overflow: visible !important;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        @php
                            $pageTitle = 'Sales Management';
                            $pageDesc = 'View, search, filter and edit your sales invoices & bookings';
                            $icon = 'fa-shopping-cart';
                            if (isset($page_type)) {
                                if ($page_type === 'quotation') {
                                    $pageTitle = 'Quotations';
                                    $pageDesc = 'Manage customer quotations and estimates';
                                    $icon = 'fa-file-invoice';
                                } elseif ($page_type === 'sales_order') {
                                    $pageTitle = 'Sales Orders';
                                    $pageDesc = 'Manage pending sales orders and deliveries';
                                    $icon = 'fa-file-signature';
                                } elseif ($page_type === 'direct_sale') {
                                    $pageTitle = 'Direct Sales';
                                    $pageDesc = 'View all POS and direct retail sales';
                                    $icon = 'fa-receipt';
                                }
                            }
                        @endphp
                        <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fas {{ $icon }} text-primary"></i> {{ $pageTitle }}
                        </h4>
                        <p class="text-muted mb-0 small">{{ $pageDesc }}</p>
                    </div>
                    <div class="sales-hdr-actions">
                        <a class="btn btn-outline-danger px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                            href="{{ route('sale.return.index') }}" style="border-radius: 8px;">
                            <i class="fas fa-undo"></i> Returns
                        </a>
                        <a class="btn btn-outline-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                            href="{{ url('bookings') }}" style="border-radius: 8px;">
                            <i class="fas fa-bookmark"></i> Bookings
                        </a>
                        @can('sales.create')
                            @if(isset($page_type) && $page_type === 'quotation')
                                <a class="btn btn-info px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1 text-white"
                                    href="{{ route('sale.add') }}?type=quotation" style="border-radius: 8px;">
                                    <i class="fas fa-file-contract"></i> Add Quotation
                                </a>
                            @elseif(isset($page_type) && $page_type === 'sales_order')
                                <a class="btn btn-outline-info px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                                    href="{{ route('quotations.index') }}" style="border-radius: 8px;" title="Convert an existing quotation into a sales order">
                                    <i class="fas fa-file-contract"></i> Convert from Quotation
                                </a>
                                <a class="btn btn-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1 text-white"
                                    href="{{ route('sale.add') }}?type=sales_order" style="border-radius: 8px;" title="Create a direct sales order">
                                    <i class="fas fa-plus"></i> Add Sales Order
                                </a>
                            @else
                                <a class="btn btn-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                                    href="{{ route('sale.add') }}" style="border-radius: 8px;">
                                    <i class="fas fa-plus"></i> Add Sale
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>

                {{-- KPI Stat Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Invoices</div>
                                    <h4 class="fw-bold text-dark mb-0 mt-1" id="statTotalCount">{{ number_format($stats['total_count'] ?? 0) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-primary-subtle text-primary" style="background-color: #eff6ff; color: #2563eb;">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Net Revenue</div>
                                    <h4 class="fw-bold text-dark mb-0 mt-1" id="statTotalNet">Rs. {{ number_format($stats['total_net'] ?? 0, 2) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-info-subtle text-info" style="background-color: #f0f9ff; color: #0284c7;">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Received (Paid)</div>
                                    <h4 class="fw-bold text-success mb-0 mt-1" id="statTotalPaid">Rs. {{ number_format($stats['total_paid'] ?? 0, 2) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-success-subtle text-success" style="background-color: #ecfdf5; color: #059669;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Unpaid (Due)</div>
                                    <h4 class="fw-bold text-danger mb-0 mt-1" id="statTotalDue">Rs. {{ number_format($stats['total_due'] ?? 0, 2) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-danger-subtle text-danger" style="background-color: #fef2f2; color: #dc2626;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $currentStatusRoute = Route::currentRouteName() ?: 'sale.index';
                @endphp
                {{-- Status Filter Pills --}}
                <div class="mb-4 sales-status-pills">
                    <a href="{{ route($currentStatusRoute, ['status' => 'all']) }}"
                        class="btn btn-sm {{ request('status') == 'all' || !request('status') ? 'btn-secondary' : 'btn-outline-secondary' }} rounded-3 shadow-sm px-3 fw-bold">
                        All <span class="badge bg-white text-dark ms-1">{{ $stats['total_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route($currentStatusRoute, ['status' => 'posted']) }}"
                        class="btn btn-sm {{ request('status') == 'posted' ? 'btn-success' : 'btn-outline-success' }} rounded-3 shadow-sm px-3 fw-bold">
                        Posted <span class="badge bg-white text-success ms-1">{{ $stats['posted_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route($currentStatusRoute, ['status' => 'draft']) }}"
                        class="btn btn-sm {{ request('status') == 'draft' ? 'btn-warning text-dark' : 'btn-outline-warning' }} rounded-3 shadow-sm px-3 fw-bold">
                        Draft <span class="badge bg-white text-dark ms-1">{{ $stats['draft_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route($currentStatusRoute, ['status' => 'booked']) }}"
                        class="btn btn-sm {{ request('status') == 'booked' ? 'btn-info text-white' : 'btn-outline-info' }} rounded-3 shadow-sm px-3 fw-bold">
                        Booked <span class="badge bg-white text-info ms-1">{{ $stats['booked_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route($currentStatusRoute, ['status' => 'returned']) }}"
                        class="btn btn-sm {{ request('status') == 'returned' ? 'btn-danger' : 'btn-outline-danger' }} rounded-3 shadow-sm px-3 fw-bold">
                        Returned <span class="badge bg-white text-danger ms-1">{{ $stats['returned_count'] ?? 0 }}</span>
                    </a>
                </div>

                <div class="card premium-card">
                    <div class="card-body p-3 p-md-4">
                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ session('error') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Mobile Filter Panel Toggle Button --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 d-md-none mb-3 fw-bold d-flex align-items-center justify-content-center gap-2" id="toggleFilterPanel">
                            <i class="fas fa-filter"></i> Search & Filters Toggle
                        </button>

                        {{-- AJAX Filter Panel --}}
                        <div class="card filter-panel mb-4" id="filterPanelContainer">
                            <div class="card-body p-0">
                                <form id="filterForm" class="row g-2 g-md-3 align-items-end" autocomplete="off" onsubmit="return false;">
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">Quick Filter</label>
                                        <select id="quick_filter" class="form-select">
                                            <option value="custom">Custom Range</option>
                                            <option value="daily">Daily (Today)</option>
                                            <option value="weekly">Weekly (This Week)</option>
                                            <option value="monthly">Monthly (This Month)</option>
                                            <option value="yearly">Yearly (This Year)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">From Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">To Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">Invoice / Bill#</label>
                                        <input type="text" class="form-control" name="bill_no" id="filter_bill_no" value="{{ request('bill_no') ?? request('invoice_no') }}" placeholder="Inv / Bill#...">
                                    </div>
                                    <div class="col-6 col-md-1">
                                        <label class="form-label mb-1">M.Bill / Ref</label>
                                        <input type="text" class="form-control" name="reference" id="filter_reference" placeholder="M.Bill...">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label mb-1">Customer</label>
                                        <select class="form-select select2-customer" name="customer_id" id="filter_customer_id" style="width: 100%;">
                                            <option value="">All Customers</option>
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->customer_name }} {{ $c->mobile ? '('.$c->mobile.')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                        <button type="button" class="btn btn-premium-secondary px-3" id="btnReset">
                                            <i class="fas fa-undo me-1"></i>Reset
                                        </button>
                                        <button type="button" class="btn btn-premium-primary px-4" id="btnSearch">
                                            <i class="fas fa-search me-1"></i>Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Table Container --}}
                        <div class="table-responsive">
                            <table id="sales-table" class="table table-hover align-middle datanew premium-table" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 rounded-start text-secondary fw-semibold text-uppercase small">Invoice / Bill#</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Customer</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small" style="width: 120px; max-width: 140px;">M.Bill</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Products</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-center">Qty</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Gross</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Inline Disc</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Add. Disc</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Net Total</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Paid</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Due</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Date</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Status</th>
                                        <th class="py-3 pe-3 rounded-end text-secondary fw-semibold text-uppercase small text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="salesTableBody">
                                    @include('admin_panel.sale.partials.sales_table_body')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Order Trail 360 Modal --}}
    <div class="modal fade" id="orderTrailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 820px; width: 95%;">
            <div class="modal-content border-0 shadow" id="orderTrailModalContent" style="border-radius: 12px; overflow: hidden;">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 with search for customer dropdown
            if ($('.select2-customer').length > 0) {
                $('.select2-customer').select2({
                    placeholder: "All Customers",
                    allowClear: true,
                    width: '100%'
                });
            }

            // Function to initialize DataTable safely
            function initDataTable() {
                try {
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#sales-table')) {
                        $('#sales-table').DataTable().destroy();
                    }
                    if ($.fn.DataTable) {
                        $('#sales-table').DataTable({
                            "pageLength": 10,
                            "order": [],
                            "language": {
                                "search": "",
                                "searchPlaceholder": "Search sales..."
                            },
                            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6 mb-2 mb-md-0'l><'col-12 col-md-6'f>>" +
                                "<'row'<'col-12'tr>>" +
                                "<'row mt-3 align-items-center'<'col-12 col-md-5 mb-2 mb-md-0'i><'col-12 col-md-7'p>>",
                        });
                    }
                } catch(e) {
                    console.error("DataTable initialization error: ", e);
                }
            }

            // Initial call
            initDataTable();

            // Mobile Filter Panel Toggle
            $('#toggleFilterPanel').on('click', function() {
                $('#filterPanelContainer').slideToggle(200);
            });

            // Core AJAX Filter Function
            function applySalesFilter() {
                const $btn = $('#btnSearch');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Searching...');

                let formData = $('#filterForm').serialize();
                let urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status')) {
                    formData += '&status=' + encodeURIComponent(urlParams.get('status'));
                }

                $.ajax({
                    url: '{{ route("sale.index") }}',
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(origHtml);
                        
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#sales-table')) {
                            $('#sales-table').DataTable().destroy();
                        }
                        
                        $('#salesTableBody').html(response.html);
                        
                        // Update Stat Cards dynamically if present
                        if (response.stats) {
                            $('#statTotalCount').text(Number(response.stats.total_count || 0).toLocaleString());
                            $('#statTotalNet').text('Rs. ' + Number(response.stats.total_net || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statTotalPaid').text('Rs. ' + Number(response.stats.total_paid || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statTotalDue').text('Rs. ' + Number(response.stats.total_due || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }

                        initDataTable();
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(origHtml);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Failed to retrieve filtered list.', 'error');
                        } else {
                            alert('Failed to retrieve filtered list.');
                        }
                    }
                });
            }

            // Quick Filter Logic
            $(document).on('change', '#quick_filter', function() {
                let val = $(this).val();
                if (val === 'custom') return;

                let today = new Date();
                let start = new Date();
                let end = new Date();

                if (val === 'daily') {
                    start = new Date();
                    end = new Date();
                } else if (val === 'weekly') {
                    let day = today.getDay();
                    let diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    start = new Date(today.setDate(diff));
                    end = new Date();
                } else if (val === 'monthly') {
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date();
                } else if (val === 'yearly') {
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date();
                }

                let formatDate = function(d) {
                    let year = d.getFullYear();
                    let month = String(d.getMonth() + 1).padStart(2, '0');
                    let day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                let startStr = formatDate(start);
                let endStr = formatDate(end);

                let pickerFrom = document.getElementById('filter_from_date') ? document.getElementById('filter_from_date')._flatpickr : null;
                let pickerTo = document.getElementById('filter_to_date') ? document.getElementById('filter_to_date')._flatpickr : null;

                if (pickerFrom) pickerFrom.setDate(startStr, true);
                else $("#filter_from_date").val(startStr);

                if (pickerTo) pickerTo.setDate(endStr, true);
                else $("#filter_to_date").val(endStr);

                applySalesFilter();
            });

            // Trigger search on button click & enter key
            $('#btnSearch').on('click', function(e) {
                e.preventDefault();
                applySalesFilter();
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                applySalesFilter();
                return false;
            });

            $(document).on('keypress', '#filterForm input', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    applySalesFilter();
                    return false;
                }
            });

            // Reset form completely and fetch unfiltered list via AJAX
            $('#btnReset').on('click', function(e) {
                e.preventDefault();

                // 1. Explicitly clear all filter inputs
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('#filter_bill_no').val('');
                $('#filter_reference').val('');
                $('#quick_filter').val('custom');
                
                // 2. Clear Select2 Customer Dropdown properly
                if ($('.select2-customer').length > 0) {
                    $('.select2-customer').val('').trigger('change');
                }
                
                // 3. Clear Flatpickr instances
                let fromElem = document.getElementById('filter_from_date');
                let toElem = document.getElementById('filter_to_date');
                if (fromElem && fromElem._flatpickr) {
                    fromElem._flatpickr.clear();
                }
                if (toElem && toElem._flatpickr) {
                    toElem._flatpickr.clear();
                }

                // Clear any Flatpickr visible alt-inputs inside filter container
                $('#filterPanelContainer .datepicker-custom').val('');
                $('#filterPanelContainer input.input').val('');

                // 4. Clear DataTables client search if any
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#sales-table')) {
                    $('#sales-table').DataTable().search('');
                }

                // 5. Clean browser URL query parameters (revert back to clean /sale or preserve status tab)
                let urlParams = new URLSearchParams(window.location.search);
                let newUrl = window.location.pathname;
                if (urlParams.has('status')) {
                    newUrl += '?status=' + encodeURIComponent(urlParams.get('status'));
                }
                if (window.history.replaceState) {
                    window.history.replaceState({}, '', newUrl);
                }

                // 6. Trigger AJAX to fetch complete unfiltered data
                applySalesFilter();
            });

            // Confirm Booking / Sale Action
            $(document).on('click', '.confirm-booking-btn', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");
                let btnText = $(this).text().trim() || "Confirm";

                Swal.fire({
                    title: btnText + "?",
                    text: "Are you sure you want to proceed with this action? This will update stocks and post ledgers.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Confirm!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // 360 Order Trail Modal Handler
            $(document).on('click', '.btn-order-trail', function(e) {
                e.preventDefault();
                var saleId = $(this).data('sale-id');
                if (!saleId) return;

                $('#orderTrailModalContent').html(
                    '<div class="p-5 text-center">' +
                    '  <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>' +
                    '  <div class="mt-3 text-muted fw-semibold">Loading 360° Order Trail...</div>' +
                    '</div>'
                );

                if (typeof $.fn.modal !== 'undefined') {
                    $('#orderTrailModal').modal('show');
                } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var m = bootstrap.Modal.getInstance(document.getElementById('orderTrailModal')) || new bootstrap.Modal(document.getElementById('orderTrailModal'));
                    m.show();
                }

                $.ajax({
                    url: '/sales-order/' + saleId + '/trail',
                    type: 'GET',
                    success: function(html) {
                        $('#orderTrailModalContent').html(html);
                    },
                    error: function(xhr) {
                        console.error("Failed to load trail:", xhr);
                        $('#orderTrailModalContent').html(
                            '<div class="p-5 text-center text-danger">' +
                            '  <i class="fas fa-exclamation-triangle fa-3x mb-3 text-danger"></i>' +
                            '  <h5 class="fw-bold">Failed to load Order Trail</h5>' +
                            '  <p class="text-muted small">Could not retrieve order details.</p>' +
                            '  <button type="button" class="btn btn-secondary btn-sm mt-2" data-bs-dismiss="modal" data-dismiss="modal">Close</button>' +
                            '</div>'
                        );
                    }
                });
            });
            // Modal handler to open Generate Invoice Confirmation with Series & Date
            $(document).on('click', '.btn-open-invoice-series-modal', function(e) {
                e.preventDefault();
                var saleId = $(this).data('sale-id');
                var orderNo = $(this).data('order-no') || ('#' + saleId);
                var customer = $(this).data('customer') || 'Walk-in';
                var amount = $(this).data('amount') || '0.00';
                var date = $(this).data('date') || "{{ date('Y-m-d') }}";

                $('#orderModalSaleId').val(saleId);
                $('#orderModalOrderNo').text(orderNo);
                $('#orderModalCustomer').text(customer);
                $('#orderModalAmount').text('PKR ' + amount);
                $('#orderModalInvoiceDate').val(date);

                // Set form action dynamically
                $('#formGenerateOrderInvoice').attr('action', '/sales/' + saleId + '/generate-invoice');

                // Pre-fetch live next invoice numbers for INV, TAX, CO
                ['INV', 'TAX', 'CO'].forEach(function(pref) {
                    $.ajax({
                        url: "{{ route('invoice_series.generate_no') }}",
                        type: "GET",
                        data: { prefix: pref },
                        success: function(res) {
                            if (res.invoice_no) {
                                $('#nextNoBadge_' + pref).text(res.invoice_no);
                            }
                        }
                    });
                });

                if (typeof $.fn.modal !== 'undefined') {
                    $('#modalGenerateOrderInvoice').modal('show');
                } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var m = bootstrap.Modal.getInstance(document.getElementById('modalGenerateOrderInvoice')) || new bootstrap.Modal(document.getElementById('modalGenerateOrderInvoice'));
                    m.show();
                }
            });
        });
    </script>

    <!-- Modal: Choose Invoice Series & Date for Sales Order Invoice -->
    <div class="modal fade" id="modalGenerateOrderInvoice" tabindex="-1" aria-labelledby="modalGenerateOrderInvoiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-bottom px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalGenerateOrderInvoiceLabel">
                        <i class="fas fa-file-invoice-dollar text-success me-2"></i> Confirm &amp; Generate Invoice
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formGenerateOrderInvoice" method="POST" action="">
                    @csrf
                    <input type="hidden" name="sale_id" id="orderModalSaleId">
                    <div class="modal-body p-3">
                        <!-- Order Summary Card -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Sales Order:</span>
                                <strong class="text-primary font-monospace" id="orderModalOrderNo">SO-0000</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Customer:</span>
                                <strong class="text-dark" id="orderModalCustomer">Customer Name</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Order Total:</span>
                                <strong class="text-success fs-6" id="orderModalAmount">PKR 0.00</strong>
                            </div>
                        </div>

                        <!-- Invoice Series Radio Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary mb-2">
                                <i class="fas fa-layer-group text-primary me-1"></i> Choose Invoice Series
                            </label>
                            <div class="d-flex flex-column gap-2">
                                <label class="border rounded-3 p-2 px-3 d-flex align-items-center justify-content-between cursor-pointer series-radio-card bg-white" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" name="prefix" value="INV" class="form-check-input mt-0" checked>
                                        <span class="badge bg-primary text-white font-monospace px-2 py-1">INV</span>
                                        <span class="fw-bold small text-dark">Standard Invoice</span>
                                    </div>
                                    <span class="badge bg-light text-primary border font-monospace" id="nextNoBadge_INV">
                                        {{ \App\Models\InvoiceSeries::generateNextNo('INV') }}
                                    </span>
                                </label>

                                <label class="border rounded-3 p-2 px-3 d-flex align-items-center justify-content-between cursor-pointer series-radio-card bg-white" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" name="prefix" value="TAX" class="form-check-input mt-0">
                                        <span class="badge bg-primary text-white font-monospace px-2 py-1">TAX</span>
                                        <span class="fw-bold small text-dark">Tax Invoice</span>
                                    </div>
                                    <span class="badge bg-light text-primary border font-monospace" id="nextNoBadge_TAX">
                                        {{ \App\Models\InvoiceSeries::generateNextNo('TAX') }}
                                    </span>
                                </label>

                                <label class="border rounded-3 p-2 px-3 d-flex align-items-center justify-content-between cursor-pointer series-radio-card bg-white" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="radio" name="prefix" value="CO" class="form-check-input mt-0">
                                        <span class="badge bg-primary text-white font-monospace px-2 py-1">CO</span>
                                        <span class="fw-bold small text-dark">Company Invoice</span>
                                    </div>
                                    <span class="badge bg-light text-primary border font-monospace" id="nextNoBadge_CO">
                                        {{ \App\Models\InvoiceSeries::generateNextNo('CO') }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Invoice Date Picker (supports backdating!) -->
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-secondary mb-1">
                                <i class="far fa-calendar-alt text-primary me-1"></i> Invoice Date
                            </label>
                            <input type="date" name="sale_date" id="orderModalInvoiceDate" class="form-control form-control-sm fw-bold" value="{{ date('Y-m-d') }}" required>
                            <small class="text-muted" style="font-size: 11px;">
                                <i class="fas fa-info-circle me-1"></i> Agar purani date ki invoice banani ho to yahan se date tabdeel karein.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top p-2 px-3">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                            <i class="fas fa-check-circle me-1"></i> Confirm &amp; Generate Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
