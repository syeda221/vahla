@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Modern Sales Management Styles */
        .sale-stat-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            padding: 20px 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03), inset 0 2px 4px rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            overflow: hidden;
        }
        .sale-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .sale-stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Clean & Bold Filter Panel */
        .filter-panel {
            background: linear-gradient(to bottom right, #ffffff, #fafafa) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 16px !important;
            padding: 20px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02) !important;
        }
        
        .filter-panel label {
            font-size: 11px;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        
        .filter-panel .form-control,
        .filter-panel .form-select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            transition: all 0.2s ease-in-out;
            height: 42px !important;
            font-size: 13px !important;
            background-color: rgba(255,255,255,0.8) !important;
        }
        
        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
            background-color: #ffffff !important;
        }

        /* Select2 In Filter Panel */
        .filter-panel .select2-container {
            width: 100% !important;
        }
        .filter-panel .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 7px 8px !important;
            background-color: rgba(255,255,255,0.8) !important;
        }
        .filter-panel .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding-left: 0 !important;
        }
        .filter-panel .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 6px !important;
        }
        .filter-panel .select2-container--default.select2-container--focus .select2-selection--single,
        .filter-panel .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
            background-color: #ffffff !important;
        }
        .select2-dropdown {
            border: 1px solid #cbd5e1 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
            overflow: hidden;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 8px 10px !important;
            font-size: 13px !important;
        }

        /* Premium Buttons */
        .btn-premium-primary {
            background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 0 20px !important;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
        }
        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.4) !important;
            background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
        }
        
        .btn-premium-secondary {
            background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 0 20px !important;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) !important;
        }
        .btn-premium-secondary:hover {
            background: #f1f5f9 !important;
            color: #1e293b !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05) !important;
        }

        /* Top Action Buttons */
        .sales-hdr-actions .btn {
            border-radius: 10px !important;
            padding: 8px 16px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            border-width: 1.5px !important;
        }
        .sales-hdr-actions .btn-primary {
            background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
        }
        .sales-hdr-actions .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.4) !important;
            background: linear-gradient(135deg, #1d4ed8, #2563eb) !important;
        }
        .sales-hdr-actions .btn-outline-primary {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
            background: transparent !important;
        }
        .sales-hdr-actions .btn-outline-primary:hover {
            background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(59,130,246,0.05)) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1) !important;
        }
        .sales-hdr-actions .btn-outline-danger {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
            background: transparent !important;
        }
        .sales-hdr-actions .btn-outline-danger:hover {
            background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(239,68,68,0.05)) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.1) !important;
        }

        /* Filter Pills Modernization */
        .sales-status-pills .btn {
            border-radius: 12px !important;
            padding: 8px 18px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            border-width: 1px !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) !important;
        }
        .sales-status-pills .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.08) !important;
        }
        .sales-status-pills .badge {
            font-size: 11px !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Premium Table Styling */
        .premium-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            background: linear-gradient(to bottom, #ffffff, #fafafa);
        }

        .premium-table {
            border: none !important;
            border-radius: 12px !important;
            overflow: visible !important;
        }
        .premium-table thead th {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.8px;
            border: none !important;
            padding: 16px 24px 16px 12px !important;
        }
        .premium-table thead tr th:first-child {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }
        .premium-table thead tr th:last-child {
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

        /* Prevent Dropdowns from Being Clipped */
        .table-responsive {
            border-radius: 12px !important;
            overflow: visible !important;
            min-height: 380px;
            padding-top: 8px;
        }

        /* Premium Dropdown Menu Customizations */
        .dropdown-menu {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            padding: 8px 0 !important;
            z-index: 1060 !important;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .dropdown-item {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            padding: 8px 20px !important;
            transition: all 0.2s ease-in-out !important;
        }
        .dropdown-item:hover {
            background-color: #eff6ff !important;
            color: #2563eb !important;
        }
        .dropdown-divider {
            border-top: 1px solid #f1f5f9 !important;
            margin: 6px 0 !important;
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
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-chart-simple text-primary"></i> Sales Management
                        </h4>
                        <p class="text-muted mb-0 small">View, search, filter and edit your sales invoices & bookings</p>
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
                            <a class="btn btn-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                                href="{{ route('sale.add') }}" style="border-radius: 8px;">
                                <i class="fas fa-plus"></i> Add Sale
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- KPI Stat Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card position-relative d-flex align-items-center" style="border: 1px solid #bfdbfe !important; background: linear-gradient(145deg, #f4f8ff, #ffffff) !important;">
                            <div class="sale-stat-icon me-3" style="background-color: #dbeafe; color: #2563eb; width: 48px; height: 48px; border-radius: 50%;">
                                <i class="fas fa-file-invoice fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px; color: #1e3a8a !important;">Total Invoices</div>
                                <h4 class="fw-bold text-dark mb-0 mt-1" id="statTotalCount">{{ number_format($stats['total_count'] ?? 0) }}</h4>
                            </div>
                            <div class="position-absolute" style="right: 16px; top: 50%; transform: translateY(-50%);">
                                <i class="fas fa-file-alt" style="font-size: 24px; color: #2563eb; opacity: 0.8;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card position-relative d-flex align-items-center" style="border: 1px solid #bbf7d0 !important; background: linear-gradient(145deg, #f0fdf4, #ffffff) !important;">
                            <div class="sale-stat-icon me-3" style="background-color: #dcfce7; color: #16a34a; width: 48px; height: 48px; border-radius: 50%;">
                                <i class="fas fa-coins fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px; color: #14532d !important;">Total Net Revenue</div>
                                <h4 class="fw-bold text-success mb-0 mt-1" id="statTotalNet" style="color: #16a34a !important;">Rs. {{ number_format($stats['total_net'] ?? 0, 2) }}</h4>
                            </div>
                            <div class="position-absolute text-end" style="right: 16px; top: 50%; transform: translateY(-50%);">
                                <i class="fas fa-arrow-trend-up text-success mb-1" style="font-size: 14px; color: #16a34a !important;"></i>
                                <div class="text-muted" style="font-size: 11px; color: #14532d !important;">100%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card position-relative d-flex align-items-center" style="border: 1px solid #fed7aa !important; background: linear-gradient(145deg, #fffbeb, #ffffff) !important;">
                            <div class="sale-stat-icon me-3" style="background-color: #ffedd5; color: #ea580c; width: 48px; height: 48px; border-radius: 50%;">
                                <i class="fas fa-tags fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px; color: #7c2d12 !important;">Discounts Given</div>
                                <h4 class="fw-bold text-warning mb-0 mt-1" id="statTotalDiscount" style="color: #ea580c !important;">Rs. {{ number_format($stats['total_discount'] ?? 0, 2) }}</h4>
                            </div>
                            <div class="position-absolute text-end" style="right: 16px; top: 50%; transform: translateY(-50%);">
                                <div class="text-muted" style="font-size: 11px; color: #7c2d12 !important;">0%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card position-relative d-flex align-items-center" style="border: 1px solid #e9d5ff !important; background: linear-gradient(145deg, #faf5ff, #ffffff) !important;">
                            <div class="sale-stat-icon me-3" style="background-color: #f3e8ff; color: #7e22ce; width: 48px; height: 48px; border-radius: 50%;">
                                <i class="fas fa-check-circle fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px; color: #4c1d95 !important;">Posted / Booked</div>
                                <h4 class="fw-bold mb-0 mt-1" id="statStatusCounts" style="color: #7e22ce !important;">
                                    {{ $stats['posted_count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['booked_count'] ?? 0 }}</span>
                                </h4>
                            </div>
                            <div class="position-absolute text-end" style="right: 16px; top: 50%; transform: translateY(-50%); width: 40px;">
                                <div class="text-muted mb-1" style="font-size: 11px; text-align: right; color: #4c1d95 !important;">50%</div>
                                <div class="progress" style="height: 4px; border-radius: 2px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 50%; background-color: #16a34a !important;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Filter Pills --}}
                <div class="mb-4 sales-status-pills d-flex gap-2">
                    <a href="{{ route('sale.index', ['status' => 'all']) }}"
                        class="btn btn-sm rounded-3 px-3 fw-bold d-flex align-items-center" style="border: 1px solid #1d4ed8; background-color: {{ request('status') == 'all' || !request('status') ? '#1d4ed8' : '#eff6ff' }}; color: {{ request('status') == 'all' || !request('status') ? '#ffffff' : '#1d4ed8' }};">
                        All <span class="badge rounded-circle ms-2 px-2 py-1" style="background-color: {{ request('status') == 'all' || !request('status') ? '#ffffff' : '#dbeafe' }}; color: {{ request('status') == 'all' || !request('status') ? '#1d4ed8' : '#1d4ed8' }};">{{ $stats['total_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'posted']) }}"
                        class="btn btn-sm rounded-3 px-3 fw-bold d-flex align-items-center" style="border: 1px solid #bbf7d0; background-color: {{ request('status') == 'posted' ? '#16a34a' : '#f0fdf4' }}; color: {{ request('status') == 'posted' ? '#ffffff' : '#16a34a' }};">
                        Posted <span class="badge rounded-circle ms-2 px-2 py-1" style="background-color: {{ request('status') == 'posted' ? '#ffffff' : '#dcfce7' }}; color: {{ request('status') == 'posted' ? '#16a34a' : '#16a34a' }};">{{ $stats['posted_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'draft']) }}"
                        class="btn btn-sm rounded-3 px-3 fw-bold d-flex align-items-center" style="border: 1px solid #fed7aa; background-color: {{ request('status') == 'draft' ? '#ea580c' : '#fffbeb' }}; color: {{ request('status') == 'draft' ? '#ffffff' : '#ea580c' }};">
                        Draft <span class="badge rounded-circle ms-2 px-2 py-1" style="background-color: {{ request('status') == 'draft' ? '#ffffff' : '#ffedd5' }}; color: {{ request('status') == 'draft' ? '#ea580c' : '#ea580c' }};">{{ $stats['draft_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'booked']) }}"
                        class="btn btn-sm rounded-3 px-3 fw-bold d-flex align-items-center" style="border: 1px solid #bfdbfe; background-color: {{ request('status') == 'booked' ? '#2563eb' : '#eff6ff' }}; color: {{ request('status') == 'booked' ? '#ffffff' : '#2563eb' }};">
                        Booked <span class="badge rounded-circle ms-2 px-2 py-1" style="background-color: {{ request('status') == 'booked' ? '#ffffff' : '#dbeafe' }}; color: {{ request('status') == 'booked' ? '#2563eb' : '#2563eb' }};">{{ $stats['booked_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'quotation']) }}"
                        class="btn btn-sm rounded-3 px-3 fw-bold d-flex align-items-center" style="border: 1px solid #e9d5ff; background-color: {{ request('status') == 'quotation' ? '#7e22ce' : '#faf5ff' }}; color: {{ request('status') == 'quotation' ? '#ffffff' : '#7e22ce' }};">
                        Quotation <span class="badge rounded-circle ms-2 px-2 py-1" style="background-color: {{ request('status') == 'quotation' ? '#ffffff' : '#f3e8ff' }}; color: {{ request('status') == 'quotation' ? '#7e22ce' : '#7e22ce' }};">{{ $stats['quotation_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'returned']) }}"
                        class="btn btn-sm rounded-3 px-3 fw-bold d-flex align-items-center" style="border: 1px solid #fecaca; background-color: {{ request('status') == 'returned' ? '#dc2626' : '#fef2f2' }}; color: {{ request('status') == 'returned' ? '#ffffff' : '#dc2626' }};">
                        Returned <span class="badge rounded-circle ms-2 px-2 py-1" style="background-color: {{ request('status') == 'returned' ? '#ffffff' : '#fee2e2' }}; color: {{ request('status') == 'returned' ? '#dc2626' : '#dc2626' }};">{{ $stats['returned_count'] ?? 0 }}</span>
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
                                        <label class="form-label mb-1"><i class="fas fa-filter text-muted me-1"></i> Quick Filter</label>
                                        <select id="quick_filter" class="form-select">
                                            <option value="custom">Custom Range</option>
                                            <option value="daily">Daily (Today)</option>
                                            <option value="weekly">Weekly (This Week)</option>
                                            <option value="monthly">Monthly (This Month)</option>
                                            <option value="yearly">Yearly (This Year)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1"><i class="far fa-calendar-alt text-muted me-1"></i> From Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1"><i class="far fa-calendar-alt text-muted me-1"></i> To Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1"><i class="fas fa-file-invoice text-muted me-1"></i> Invoice / Bill#</label>
                                        <input type="text" class="form-control" name="bill_no" id="filter_bill_no" value="{{ request('bill_no') ?? request('invoice_no') }}" placeholder="Inv / Bill#...">
                                    </div>
                                    <div class="col-6 col-md-1">
                                        <label class="form-label mb-1"><i class="fas fa-link text-muted me-1"></i> M.Bill / Ref</label>
                                        <input type="text" class="form-control" name="reference" id="filter_reference" placeholder="M.Bill...">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label mb-1"><i class="far fa-user text-muted me-1"></i> Customer</label>
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
                                <thead>
                                    <tr>
                                        <th class="text-center">Invoice / Bill#</th>
                                        <th>Customer</th>
                                        <th>M.Bill</th>
                                        <th>Products</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Gross</th>
                                        <th class="text-end">Inline Disc</th>
                                        <th class="text-end">Add. Disc</th>
                                        <th class="text-end">Net Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
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
                            $('#statTotalDiscount').text('Rs. ' + Number(response.stats.total_discount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statStatusCounts').html((response.stats.posted_count || 0) + ' <span class="fs-6 fw-normal text-muted">/ ' + (response.stats.booked_count || 0) + '</span>');
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

            // Confirm Booking Action
            $(document).on('click', '.confirm-booking-btn', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: "Confirm to Post?",
                    text: "This will convert the sale to Posted status. Stock will be deducted and ledger will be updated.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Confirm it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
