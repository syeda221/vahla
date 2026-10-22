@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Premium ERP Card & Container Styling */
        .premium-card {
            border: 2px solid #cbd5e1 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
            background-color: #ffffff;
            margin-top: 10px;
        }
        
        /* Clean & Bold Filter Panel */
        .filter-panel {
            background-color: #f8fafc !important;
            border: 2px dashed #94a3b8 !important;
            border-radius: 12px !important;
            padding: 18px !important;
        }
        
        .filter-panel label,
        .filter-panel .form-label {
            display: block !important;
            width: 100% !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 5px !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        .filter-panel .form-control,
        .filter-panel .form-select,
        .filter-panel select,
        .filter-panel input {
            display: block !important;
            width: 100% !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            color: #1e293b !important;
            transition: all 0.2s ease-in-out;
            height: 38px !important;
            padding: 6px 12px !important;
            box-sizing: border-box !important;
            background-color: #ffffff !important;
        }
        
        .filter-panel select.form-select {
            padding-right: 28px !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 12px 10px !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }
        
        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus,
        .filter-panel select:focus,
        .filter-panel input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none !important;
        }
        
        /* Elegant & Bold Buttons */
        .btn-premium-primary {
            background-color: #2563eb !important;
            border: 1.5px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            height: 38px !important;
            padding: 0 16px !important;
            transition: all 0.2s;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
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
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .btn-premium-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
        }

        /* Compact Button Styling for Action List */
        .btn-xs {
            padding: 4px 8px !important;
            font-size: 11px !important;
            border-radius: 4px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin: 2px !important;
        }
        .btn-xs i {
            font-size: 10px !important;
        }
        .btn-xs:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
        }

        /* Premium Action Dropdown Button */
        .btn-premium-action { background-color: #ffffff !important; border: 1px solid #cce5ff !important; color: #0d6efd !important; font-weight: 700 !important; border-radius: 20px !important; height: 32px !important; padding: 0 12px !important; font-size: 11px !important; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex !important; align-items: center !important; justify-content: center !important; transition: all 0.2s ease-in-out !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; } .btn-premium-action i.fa-eye { background: #e6f2ff; border-radius: 50%; padding: 4px; margin-right: 6px; }
        .btn-premium-action:hover, 
        .btn-premium-action:focus, 
        .btn-premium-action[aria-expanded="true"] {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08) !important;
        }
        .btn-premium-action::after {
            margin-left: 6px !important;
            vertical-align: middle !important;
        }

        /* Premium Dropdown Menu Customizations */
        .dropdown-menu {
            border: 2px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            padding: 6px 0 !important;
            z-index: 1050 !important;
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
            border-top: 2px solid #e2e8f0 !important;
            margin: 6px 0 !important;
        }

        /* Premium Bold Table Styling */
        .premium-table {
            border: 2px solid #475569 !important; /* Bold outer border */
            border-radius: 8px !important;
            overflow: visible !important;
        }
        
        .premium-table thead th { background-color: #0c336b !important; color: #ffffff !important; font-weight: 700 !important; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; border-bottom: none !important; border-right: 1px solid rgba(255,255,255,0.1) !important; padding: 12px 10px !important; white-space: nowrap; }
        
        .premium-table thead th:last-child {
            border-right: none !important;
        }
        
        .premium-table tbody td {
            border: 2px solid #e2e8f0 !important; /* Bold borders between all cells */
            padding: 12px 10px !important;
            font-size: 13px !important;
            color: #334155 !important;
            background-color: #ffffff;
        }
        
        .premium-table tbody tr:hover td {
            background-color: #f8fafc !important; /* Elegant row hover background */
        }
        
        /* Table Responsive border fix */
        .table-responsive {
            border-radius: 8px !important;
            overflow: visible !important;
        }

        /* Custom styled controls for inline Bulk Discount Card */
        .btn-circle-custom {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            background-color: transparent !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s;
        }
        .btn-circle-custom:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* Financial Chips */
        .financial-chip {
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 8px;
            padding: 6px 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .financial-chip .chip-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            line-height: 1;
            margin-bottom: 3px;
        }
        .financial-chip .chip-value {
            font-size: 13px;
            font-weight: 700;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            line-height: 1.2;
        }
        .financial-chip .chip-sub {
            font-size: 10px;
            font-weight: 500;
            margin-left: 2px;
        }

        .chip-inline {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }
        .chip-inline .chip-label {
            color: #64748b !important;
        }
        .chip-inline .chip-value {
            color: #334155 !important;
        }

        .chip-extra {
            background: #fffbeb !important;
            border: 1px solid #fde68a !important;
        }
        .chip-extra .chip-label {
            color: #b45309 !important;
        }
        .chip-extra .chip-value {
            color: #b45309 !important;
        }

        .chip-net {
            background: #ecfdf5 !important;
            border: 1.5px solid #6ee7b7 !important;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.1) !important;
        }
        .chip-net .chip-label {
            color: #047857 !important;
            font-weight: 800 !important;
        }
        .chip-net .chip-value {
            color: #065f46 !important;
            font-size: 13.5px !important;
            font-weight: 800 !important;
        }

        .chip-paid {
            background: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
        }
        .chip-paid .chip-label {
            color: #166534 !important;
        }
        .chip-paid .chip-value {
            color: #16a34a !important;
        }

        .chip-due {
            background: #fef2f2 !important;
            border: 1px solid #fecaca !important;
        }
        .chip-due .chip-label {
            color: #991b1b !important;
        }

        @media (min-width: 992px) {
            .financial-chip {
                display: block;
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .financial-chip .chip-label {
                display: none !important;
            }
            .financial-chip .chip-value {
                font-size: 13px !important;
                color: inherit !important;
            }
            .purch-hdr-actions {
                display: flex;
                gap: 8px;
            }
            .purch-status-pills {
                display: flex;
                gap: 8px;
            }
        }

        /* Mobile Breakpoints (<= 991.98px) */
        @media (max-width: 991.98px) {
            body, html {
                overflow-x: hidden !important;
                max-width: 100vw !important;
            }
            .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
                overflow-x: hidden !important;
                max-width: 100vw !important;
            }
            .premium-card {
                margin-top: 6px !important;
                border-radius: 10px !important;
            }
            .premium-card .card-body {
                padding: 10px !important;
            }

            /* Header Actions */
            .purch-hdr-actions {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 8px !important;
                width: 100% !important;
                margin-top: 6px !important;
            }
            .purch-hdr-actions .btn {
                width: 100% !important;
                justify-content: center !important;
                height: 38px !important;
                font-size: 12.5px !important;
                border-radius: 8px !important;
                padding: 0 10px !important;
            }
            .purch-hdr-actions a[href*="add_purchase"],
            .purch-hdr-actions a[href*="Add"] {
                grid-column: 1 / -1 !important;
                order: -1 !important;
                height: 42px !important;
                font-size: 13.5px !important;
            }

            /* Status Pills Horizontal Scroll */
            .purch-status-pills {
                display: flex !important;
                gap: 6px !important;
                overflow-x: auto !important;
                padding-bottom: 6px !important;
                -webkit-overflow-scrolling: touch !important;
                margin-bottom: 12px !important;
            }
            .purch-status-pills .btn {
                flex: 0 0 auto !important;
                white-space: nowrap !important;
                padding: 6px 12px !important;
                font-size: 12px !important;
                border-radius: 8px !important;
            }

            /* Filter Panel Mobile */
            .filter-panel {
                padding: 14px 12px !important;
                border: 1.5px dashed #94a3b8 !important;
                border-radius: 12px !important;
                margin-bottom: 14px !important;
                background-color: #ffffff !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
            }
            .filter-panel .form-label,
            .filter-panel label {
                font-size: 11px !important;
                font-weight: 700 !important;
                margin-bottom: 4px !important;
                color: #334155 !important;
            }
            .filter-panel .form-control,
            .filter-panel .form-select,
            .filter-panel select,
            .filter-panel input {
                height: 40px !important;
                font-size: 13px !important;
                border-radius: 8px !important;
                border: 1.5px solid #cbd5e1 !important;
                background-color: #f8fafc !important;
            }
            .filter-panel .btn-premium-primary,
            .filter-panel .btn-premium-secondary {
                height: 40px !important;
                font-size: 13px !important;
                border-radius: 8px !important;
            }

            /* DataTables Mobile Controls */
            .dataTables_wrapper .dataTables_length {
                display: none !important;
            }
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                width: 100% !important;
                margin-bottom: 10px !important;
            }
            .dataTables_wrapper .dataTables_filter label {
                width: 100% !important;
                margin: 0 !important;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                height: 38px !important;
                border-radius: 8px !important;
                border: 1.5px solid #cbd5e1 !important;
                padding: 6px 12px !important;
                font-size: 13px !important;
                background-color: #ffffff !important;
                box-sizing: border-box !important;
            }
            .dataTables_wrapper .dataTables_info {
                text-align: center !important;
                font-size: 12px !important;
                margin-bottom: 6px !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                text-align: center !important;
                float: none !important;
                display: flex !important;
                justify-content: center !important;
            }
            .dataTables_wrapper .dataTables_paginate .pagination {
                justify-content: center !important;
                flex-wrap: wrap !important;
                gap: 4px !important;
            }

            /* Transform Table to Sleek Mobile Cards */
            .table-responsive {
                overflow: visible !important;
                border: none !important;
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #purchase-table {
                border: none !important;
                display: block !important;
                width: 100% !important;
            }
            #purchase-table thead {
                display: none !important;
            }
            #purchase-table tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                width: 100% !important;
            }
            #purchase-table tbody tr {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
                grid-template-areas:
                    "check status"
                    "vendor vendorinv"
                    "location date"
                    "inlinedisc extradisc"
                    "nettotal due"
                    "action action" !important;
                align-items: center !important;
                row-gap: 8px !important;
                column-gap: 8px !important;
                background: #ffffff !important;
                border: 1.5px solid #cbd5e1 !important;
                border-radius: 14px !important;
                padding: 12px 10px !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
                position: relative !important;
                box-sizing: border-box !important;
                width: 100% !important;
            }
            #purchase-table tbody tr:hover td {
                background-color: transparent !important;
            }
            #purchase-table tbody td {
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }

            /* Grid Cells on Mobile */
            .cell-check {
                grid-area: check !important;
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
                text-align: left !important;
                width: auto !important;
            }
            .cell-status {
                grid-area: status !important;
                text-align: right !important;
                justify-self: end !important;
            }
            .cell-vendor {
                grid-area: vendor !important;
                border-top: 1px dashed #f1f5f9 !important;
                padding-top: 6px !important;
            }
            .cell-vendor-inv {
                grid-area: vendorinv !important;
                text-align: right !important;
                border-top: 1px dashed #f1f5f9 !important;
                padding-top: 6px !important;
                font-size: 12px !important;
            }
            .cell-location {
                grid-area: location !important;
                font-size: 12px !important;
            }
            .cell-date {
                grid-area: date !important;
                text-align: right !important;
                font-size: 12px !important;
            }
            .cell-inlinedisc {
                grid-area: inlinedisc !important;
            }
            .cell-extradisc {
                grid-area: extradisc !important;
            }
            .cell-nettotal {
                grid-area: nettotal !important;
            }
            .cell-due {
                grid-area: due !important;
            }
            .cell-action {
                grid-area: action !important;
                border-top: 1px dashed #e2e8f0 !important;
                padding-top: 8px !important;
                margin-top: 2px !important;
                width: 100% !important;
            }
            .cell-action .dropdown {
                width: 100% !important;
            }
            .cell-action .btn-premium-action {
                width: 100% !important;
                height: 38px !important;
                justify-content: center !important;
                border-radius: 8px !important;
                font-size: 12px !important;
            }
            .cell-action .dropdown-menu {
                width: 100% !important;
            }

            /* Sticky Bulk Discount Bar on Mobile */
            #bulk-discount-bar {
                position: fixed !important;
                left: 8px !important;
                right: 8px !important;
                bottom: 10px !important;
                width: calc(100% - 16px) !important;
                margin: 0 !important;
                border-radius: 12px !important;
                z-index: 1060 !important;
                max-height: 80vh !important;
                overflow-y: auto !important;
            }
            #bulk-discount-bar .card-body {
                padding: 12px !important;
            }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 p-3" style="background: linear-gradient(to right, #f8fbff, #eef5ff); border-radius: 12px;">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 mr-3 me-3" style="width: 52px; height: 52px; min-width: 52px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 14px; color: white; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);">
                            <i class="fas fa-shopping-cart fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bolder mb-0" style="color: #0f172a; font-size: 1.35rem; letter-spacing: -0.5px;">Purchase Management</h4>
                            <p class="mb-0" style="color: #64748b; font-size: 0.85rem; font-weight: 500;">View and manage your purchase invoices</p>
                        </div>
                    </div>
                    <div class="purch-hdr-actions d-flex gap-2">
                        <a class="btn bg-white fw-bold d-inline-flex align-items-center gap-1 justify-content-center" href="{{ route('purchase.return.index') }}" style="border-radius: 8px; color: #f43f5e; border: 1px solid #f43f5e; padding: 8px 16px; font-size: 0.9rem;">
                            <i class="fas fa-undo"></i> Returns
                        </a>

                        @can('purchases.create')
                            <a class="btn fw-bold text-white d-inline-flex align-items-center gap-1 justify-content-center"
                                href="{{ route('purchase.quick_create') }}" style="border-radius: 8px; background-color: #10b981; border: none; padding: 8px 16px; font-size: 0.9rem;">
                                <i class="fas fa-bolt"></i> Quick Purchase
                            </a>
                            <a class="btn fw-bold text-white d-inline-flex align-items-center gap-1 justify-content-center"
                                href="{{ route('add_purchase') }}" style="border-radius: 8px; background-color: #2563eb; border: none; padding: 8px 16px; font-size: 0.9rem;">
                                <i class="fas fa-plus"></i> Add Purchase
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- Status Filters --}}
                <div class="mb-4 purch-status-pills d-flex gap-3">
                    @php
                        $allCount = \App\Models\Purchase::count();
                        $approvedCount = \App\Models\Purchase::where('status_purchase', 'approved')->count();
                        $draftCount = \App\Models\Purchase::where('status_purchase', 'draft')->count();
                        $returnedCount = \App\Models\Purchase::where('status_purchase', 'Returned')->count();
                    @endphp
                    <a href="{{ route('Purchase.home', ['status' => 'all']) }}"
                        class="btn btn-sm {{ request('status') == 'all' || !request('status') ? 'btn-primary text-white border-primary' : 'bg-white text-dark border' }} shadow-sm px-3 py-2 d-inline-flex align-items-center gap-2 fw-bold" style="border-radius: 8px; font-size: 13px;">
                        All <span class="badge {{ request('status') == 'all' || !request('status') ? 'bg-primary text-white' : 'bg-primary text-white' }} rounded-circle p-1" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 11px; filter: brightness(1.2);">{{ $allCount }}</span>
                    </a>
                    <a href="{{ route('Purchase.home', ['status' => 'approved']) }}"
                        class="btn btn-sm bg-white shadow-sm px-3 py-2 d-inline-flex align-items-center gap-2 fw-bold border" style="border-radius: 8px; font-size: 13px; color: #198754;">
                        Approved <span class="badge bg-success text-white rounded-circle p-1" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 11px;">{{ $approvedCount }}</span>
                    </a>
                    <a href="{{ route('Purchase.home', ['status' => 'draft']) }}"
                        class="btn btn-sm bg-white shadow-sm px-3 py-2 d-inline-flex align-items-center gap-2 fw-bold border" style="border-radius: 8px; font-size: 13px; color: #ffc107;">
                        Draft <span class="badge bg-warning text-dark rounded-circle p-1" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 11px;">{{ $draftCount }}</span>
                    </a>
                    <a href="{{ route('Purchase.home', ['status' => 'Returned']) }}"
                        class="btn btn-sm bg-white shadow-sm px-3 py-2 d-inline-flex align-items-center gap-2 fw-bold border" style="border-radius: 8px; font-size: 13px; color: #dc3545;">
                        Returned <span class="badge bg-danger text-white rounded-circle p-1" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 11px;">{{ $returnedCount }}</span>
                    </a>
                </div>
                <div class="card premium-card">
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- AJAX Filter Panel --}}
                        <div class="card filter-panel mb-4">
                            <div class="card-body p-0">
                                <form id="filterForm" class="row g-2 g-lg-3 align-items-end" autocomplete="off">
                                    <div class="col-12 col-lg-2">
                                        <label class="form-label mb-1"><i class="fas fa-filter text-primary me-1"></i> Quick Filter</label>
                                        <select id="quick_filter" class="form-select">
                                            <option value="custom">Custom Range</option>
                                            <option value="daily">Daily (Today)</option>
                                            <option value="weekly">Weekly (This Week)</option>
                                            <option value="monthly">Monthly (This Month)</option>
                                            <option value="yearly">Yearly (This Year)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-lg-2">
                                        <label class="form-label mb-1"><i class="far fa-calendar-alt text-primary me-1"></i> From Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-lg-2">
                                        <label class="form-label mb-1"><i class="far fa-calendar-alt text-primary me-1"></i> To Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-lg-1">
                                        <label class="form-label mb-1"><i class="fas fa-hashtag text-primary me-1"></i> Bill#</label>
                                        <input type="text" class="form-control" name="bill_no" id="filter_bill_no" placeholder="PUR-...">
                                    </div>
                                    <div class="col-6 col-lg-1">
                                        <label class="form-label mb-1"><i class="fas fa-receipt text-primary me-1"></i> M.Bill#</label>
                                        <input type="text" class="form-control" name="reference" id="filter_reference" placeholder="M.Bill...">
                                    </div>
                                    <div class="col-12 col-lg-2">
                                        <label class="form-label mb-1"><i class="fas fa-user-tie text-primary me-1"></i> Vendor</label>
                                        <select class="form-select" name="vendor_id" id="filter_vendor_id">
                                            <option value="">All Vendors</option>
                                            @foreach ($vendors as $v)
                                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-2">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-premium-secondary flex-fill" id="btnReset">
                                                <i class="fas fa-undo me-1"></i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-premium-primary flex-fill" id="btnSearch">
                                                <i class="fas fa-search me-1"></i>Search
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="purchase-table" class="table table-hover align-middle datanew premium-table" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 rounded-start text-secondary fw-semibold text-uppercase small" style="width: 40px; vertical-align: middle;">
                                            <input type="checkbox" id="selectAllPurchases" style="cursor: pointer; width: 16px; height: 16px; margin: 0 auto !important;">
                                        </th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small d-none d-lg-table-cell">Bill#</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Date</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small d-none d-lg-table-cell">Invoice No</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Vendor Inv#</th><th class="d-none">Note</th>
                                        
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Status</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Vendor</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Location</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Inline Disc</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Add. Disc</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Net Amount</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end d-none d-lg-table-cell">Paid</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Due</th>
                                        <th class="py-3 pe-3 rounded-end text-secondary fw-semibold text-uppercase small text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseTableBody">
                                    @include('admin_panel.purchase.partials.purchase_table_body')
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Discount Card (Sticky/Floating action bar below table) -->
                        <div id="bulk-discount-bar" class="d-none card mt-4 shadow-lg border-2" style="border: 2px solid #2563eb !important; border-radius: 14px; background-color: #ffffff; position: sticky; bottom: 20px; z-index: 1040; box-shadow: 0 10px 35px rgba(37, 99, 235, 0.18) !important;">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-column">
                                    <!-- Header Row -->
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2" style="color: #2563eb; font-size: 16px; font-weight: 700;">
                                            <i class="fas fa-tag me-1"></i>
                                            <span>Additional Discount for <span id="selected-purchases-count-text" class="badge bg-primary text-white rounded-pill px-2">0</span> Selected Purchases</span>
                                        </div>
                                        <button type="button" id="btn-minimize-bulk-bar" class="btn btn-sm btn-outline-secondary rounded-circle py-0 px-2" title="Close" style="width: 28px; height: 28px; line-height: 1;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Discount Type Toggle & Input -->
                                    <div class="row g-3 align-items-end mb-3">
                                        <div class="col-md-4 col-12">
                                            <label class="form-label mb-1 text-secondary font-weight-bold fw-bold small">DISCOUNT TYPE</label>
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-sm btn-primary fw-bold btn-discount-type active" data-type="percentage">
                                                    <i class="fas fa-percent me-1"></i> Percentage (%)
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold btn-discount-type" data-type="fixed">
                                                    <i class="fas fa-money-bill-wave me-1"></i> Fixed (Rs.)
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-12">
                                            <label class="form-label mb-1 text-secondary font-weight-bold fw-bold small" id="bulk-discount-label">DISCOUNT PERCENTAGE (%)</label>
                                            <div class="input-group">
                                                <input type="number" id="bulk-discount-input" min="0" max="100" step="0.1" class="form-control" placeholder="Enter discount percentage (e.g. 5 for 5%)" style="border: 2px solid #cbd5e1 !important; border-radius: 8px 0 0 8px !important; height: 40px; font-size: 14px; font-weight: 600;">
                                                <span class="input-group-text fw-bold text-primary bg-light" id="bulk-discount-addon" style="border: 2px solid #cbd5e1; border-left: none; border-radius: 0 8px 8px 0 !important; padding: 0 16px;">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Footer Actions -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 border-top gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" id="btn-save-bulk-discount" class="btn btn-premium-primary px-4 py-2 d-flex align-items-center" style="height: 38px; font-weight: 700; border-radius: 8px;">
                                                <i class="fas fa-check me-2 mr-2"></i> Apply Additional Discount
                                            </button>
                                            <button type="button" id="btn-cancel-bulk-discount" class="btn btn-premium-secondary px-3 py-2" style="height: 38px; font-weight: 600; border-radius: 8px;">
                                                Deselect All
                                            </button>
                                        </div>
                                        <div class="text-muted small fw-semibold">
                                            <span id="selected-ratio-text">0 of 0</span> purchases selected
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            // Function to initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('.datanew')) {
                    $('.datanew').DataTable().destroy();
                }
                $('.datanew').DataTable({
                    "pageLength": 10,
                    "order": [],
                    "columnDefs": [
                        { "orderable": false, "targets": 0 }
                    ],
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search purchases..."
                    },
                    "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                });
            }

            // Initial call
            initDataTable();

            // Submit form via AJAX
            // Quick Filter Logic
            $(document).on('change', '#quick_filter', function() {
                let val = $(this).val();
                let today = new Date();
                let start = new Date();
                let end = new Date();

                if (val === 'daily') {
                    // Start and end are both today
                } else if (val === 'weekly') {
                    // Start is first day of current week (let's use Monday)
                    let day = today.getDay(); // 0 is Sunday, 1 is Monday
                    let diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    start.setDate(diff);
                } else if (val === 'monthly') {
                    // Start is 1st of current month
                    start.setDate(1);
                } else if (val === 'yearly') {
                    // Start is Jan 1st of current year
                    start.setMonth(0, 1);
                } else if (val === 'custom') {
                    return; // Don't change dates for custom
                }

                let pickerFrom = document.getElementById('filter_from_date')._flatpickr;
                let pickerTo = document.getElementById('filter_to_date')._flatpickr;
                if(pickerFrom) pickerFrom.setDate(start);
                else $("#filter_from_date").val(start.toISOString().split('T')[0]);
                
                if(pickerTo) pickerTo.setDate(end);
                else $("#filter_to_date").val(end.toISOString().split('T')[0]);
                
                $('#filterForm').trigger('submit');
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSearch');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Searching...');

                $.ajax({
                    url: '{{ route("Purchase.home") }}',
                    method: 'GET',
                    data: $(this).serialize(),
                    success: function(response) {
                        $btn.prop('disabled', false).html(origHtml);
                        
                        // 1. Destroy DataTable first to release DOM bindings
                        if ($.fn.DataTable.isDataTable('.datanew')) {
                            $('.datanew').DataTable().destroy();
                        }
                        
                        // 2. Safely replace the table body HTML
                        $('#purchaseTableBody').html(response.html);
                        
                        // 3. Re-initialize the DataTable on the new HTML
                        $('.datanew').DataTable({
                            "pageLength": 10,
                            "order": [],
                            "columnDefs": [
                                { "orderable": false, "targets": 0 }
                            ],
                            "language": {
                                "search": "",
                                "searchPlaceholder": "Search purchases..."
                            },
                            "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                                "<'row'<'col-sm-12'tr>>" +
                                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                        });
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(origHtml);
                        Swal.fire('Error', 'Failed to retrieve filtered list.', 'error');
                    }
                });
            });

            // Reset form
            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                $('#filterForm').trigger('submit');
            });

            // Confirm Purchase Action
            $(document).on('click', '.confirm-purchase-btn', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');

                Swal.fire({
                    title: "Confirm Purchase?",
                    text: "This will finalize the purchase, update stocks, and post ledgers.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Confirm it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: "GET",
                            success: function(response) {
                                if (response.invoice_url) {
                                    window.open(response.invoice_url, '_blank');
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Confirmed!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                let msg = 'Something went wrong.';
                                if (xhr.responseJSON && xhr.responseJSON.message) msg =
                                    xhr.responseJSON.message;
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // Delete Confirmation
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Helper to get checkboxes across DataTables and DOM
            function getPurchaseCheckboxes() {
                var table = $('#purchase-table');
                if ($.fn.DataTable.isDataTable(table[0])) {
                    var dt = table.DataTable();
                    return dt.$('input.select-purchase-row');
                }
                return table.find('input.select-purchase-row');
            }

            function updateBulkDiscountBar() {
                let $boxes = getPurchaseCheckboxes();
                let $checked = $boxes.filter(':checked');
                let count = $checked.length;
                let total = $boxes.length;

                // Visual row highlighting
                $boxes.closest('tr').removeClass('table-active bg-light');
                $checked.closest('tr').addClass('table-active bg-light');

                if (count > 0) {
                    $('#selected-purchases-count-text').text(count);
                    $('#selected-ratio-text').text(count + ' of ' + total);
                    $('#bulk-discount-bar').removeClass('d-none');
                } else {
                    $('#bulk-discount-bar').addClass('d-none');
                }
            }

            $(document).on('change', '.select-purchase-row', function() {
                updateBulkDiscountBar();
                let $boxes = getPurchaseCheckboxes();
                let allChecked = $boxes.length > 0 && $boxes.length === $boxes.filter(':checked').length;
                $('#selectAllPurchases').prop('checked', allChecked);
            });

            $(document).on('click', '#selectAllPurchases', function(e) {
                e.stopPropagation();
            });

            $(document).on('change', '#selectAllPurchases', function() {
                let isChecked = $(this).is(':checked');
                let $boxes = getPurchaseCheckboxes();
                $boxes.prop('checked', isChecked);
                updateBulkDiscountBar();
            });

            // Cancel / Deselect button functionality
            $(document).on('click', '#btn-cancel-bulk-discount, #btn-minimize-bulk-bar', function() {
                let $boxes = getPurchaseCheckboxes();
                $boxes.prop('checked', false);
                $('#selectAllPurchases').prop('checked', false);
                updateBulkDiscountBar();
            });

            // Discount Type Toggle (Percentage % vs Fixed Rs.)
            let currentDiscountType = 'percentage';
            $(document).on('click', '.btn-discount-type', function() {
                $('.btn-discount-type').removeClass('active btn-primary').addClass('btn-outline-primary');
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                currentDiscountType = $(this).data('type');
                
                if (currentDiscountType === 'fixed') {
                    $('#bulk-discount-label').text('DISCOUNT AMOUNT (RS.)');
                    $('#bulk-discount-addon').text('Rs.');
                    $('#bulk-discount-input').attr({
                        'placeholder': 'Enter discount amount (e.g. 500 for Rs. 500)',
                        'max': ''
                    });
                } else {
                    $('#bulk-discount-label').text('DISCOUNT PERCENTAGE (%)');
                    $('#bulk-discount-addon').text('%');
                    $('#bulk-discount-input').attr({
                        'placeholder': 'Enter discount percentage (e.g. 5 for 5%)',
                        'max': '100'
                    });
                }
            });

            // Recheck on AJAX table redraw
            $(document).ajaxComplete(function(event, xhr, settings) {
                if (settings && settings.url && (settings.url.indexOf('Purchase') !== -1 || settings.url.indexOf('purchase') !== -1)) {
                    $('#selectAllPurchases').prop('checked', false);
                    updateBulkDiscountBar();
                }
            });

            $(document).on('draw.dt', function() {
                updateBulkDiscountBar();
                let $boxes = getPurchaseCheckboxes();
                let allChecked = $boxes.length > 0 && $boxes.length === $boxes.filter(':checked').length;
                $('#selectAllPurchases').prop('checked', allChecked);
            });

            // Save bulk additional discount
            $(document).on('click', '#btn-save-bulk-discount', function() {
                let $boxes = getPurchaseCheckboxes();
                let selectedIds = $boxes.filter(':checked').map(function() {
                    return $(this).val();
                }).get();
                let discountValue = parseFloat($('#bulk-discount-input').val());

                if (selectedIds.length === 0) {
                    Swal.fire('Error', 'Please select at least one purchase.', 'error');
                    return;
                }
                if (isNaN(discountValue) || discountValue < 0 || (currentDiscountType === 'percentage' && discountValue > 100)) {
                    let errMsg = currentDiscountType === 'percentage'
                        ? 'Please enter a valid discount percentage (0 to 100).'
                        : 'Please enter a valid discount amount (0 or more).';
                    Swal.fire('Error', errMsg, 'error');
                    return;
                }

                let btn = $(this);
                let origHtml = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: '{{ route("purchases.bulk-additional-discount") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        purchase_ids: selectedIds,
                        discount_value: discountValue,
                        discount_percentage: discountValue,
                        discount_type: currentDiscountType
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html(origHtml);
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Failed to save discount.', 'error');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(origHtml);
                        let msg = 'Failed to save discount.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });
    </script>
@endsection



