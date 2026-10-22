@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Professional ERP Styling */
        :root {
            --erp-primary: #4a69bd;
            /* Professional Blue */
            --erp-bg: #f5f6fa;
            --erp-border: #dcdde1;
            --erp-text: #2f3640;
            --erp-muted: #7f8fa6;
        }

        body {
            background-color: var(--erp-bg);
            color: var(--erp-text);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .erp-card {
            background: white;
            border: 1px solid var(--erp-border);
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .erp-header {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--erp-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
        }

        .erp-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--erp-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--erp-muted);
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .form-control,
        .form-select {
            border-radius: 4px;
            border: 1px solid var(--erp-border);
            padding: 0.4rem 0.75rem;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--erp-primary);
            box-shadow: 0 0 0 2px rgba(74, 105, 189, 0.2);
        }

        .form-control[readonly] {
            background-color: #f9fafb;
            /* Minimalist light gray */
            color: var(--erp-muted);
            font-weight: 500;
            cursor: not-allowed;
            pointer-events: none;
            border-color: var(--erp-border);
            /* Standard border, no pattern */
            opacity: 1;
            /* Full opacity for clarity */
        }

        .form-control[readonly]:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        /* Table Styles */
        .erp-table-container {
            border: 1px solid var(--erp-border);
            border-radius: 6px;
            overflow: hidden;
        }

        .table-erp {
            width: 100%;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f1f2f6;
            color: var(--erp-text);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 0.75rem;
            border-bottom: 2px solid var(--erp-border);
            white-space: nowrap;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 0.5rem;
            border-bottom: 1px solid #f1f2f6;
        }

        .table-erp input.form-control {
            border: 1px solid transparent;
            background: transparent;
            padding: 0.25rem 0.5rem;
            height: auto;
        }

        .table-erp input.form-control:focus,
        .table-erp input.form-control:hover {
            border-color: var(--erp-border);
            background: white;
        }

        /* Read-only inputs in table */
        .table-erp input.form-control[readonly] {
            cursor: not-allowed;
            pointer-events: none;
            background-color: transparent;
            /* Seamless integration */
            color: #adb5bd;
            /* Muted text */
            font-weight: 400;
            border-color: transparent;
        }

        .table-erp input.form-control[readonly]:hover,
        .table-erp input.form-control[readonly]:focus {
            background-color: #f8f9fa;
            border-color: transparent;
            box-shadow: none;
            cursor: not-allowed;
        }

        .summary-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 1.5rem;
            border: 1px solid var(--erp-border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .summary-row.total {
            border-top: 1px solid var(--erp-border);
            padding-top: 0.5rem;
            margin-top: 0.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--erp-primary);
        }

        .btn-erp-primary {
            background-color: var(--erp-primary);
            color: white;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .btn-erp-primary:hover {
            background-color: #3c5aa6;
            color: white;
            transform: translateY(-1px);
        }

        /* Select2 Tweaks */
        .select2-container .select2-selection--single {
            height: 36px;
            border-color: var(--erp-border);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-selection--multiple {
            border-color: var(--erp-border);
        }

        /* Editable Field Highlighting */
        /* Editable Field Highlighting - Minimal */
        .quantity-box:not([readonly]),
        #extraDiscount {
            background-color: #fff;
            font-weight: 600;
            color: var(--erp-text);
            transition: all 0.2s;
        }

        /* Only show focus ring when active */
        .quantity-box:not([readonly]):focus,
        #extraDiscount:focus {
            border-color: var(--erp-primary) !important;
            box-shadow: 0 0 0 3px rgba(74, 105, 189, 0.1) !important;
        }

        .quantity-box:not([readonly]):hover {
            border-color: #b0bdd1;
        }

        /* Visual indicator for editable vs read-only */
        .form-label .fa-lock {
            color: #dee2e6;
            /* Very subtle lock icon */
        }

        /* Page Overflow Protection */
        html, body {
            max-width: 100vw;
            overflow-x: hidden;
        }
        .container-fluid {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Modern Return Card Internal Components */
        .spec-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 7px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
        }
        .spec-card .spec-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .spec-card .spec-data {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .avail-card {
            background: #f0f7ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 10px;
            padding: 9px 12px;
        }
        .avail-card .avail-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .avail-card .avail-label {
            font-size: 11px;
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .avail-card .avail-badge {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 2px 8px;
            font-weight: 800;
            font-size: 12px;
            font-family: monospace;
        }
        .avail-card .avail-sub {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }

        .action-input-card {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            height: 100%;
        }
        .action-input-card.highlight-input {
            border-color: #3b82f6;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.1);
        }
        .action-input-card .action-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .action-input-card .action-box-input {
            height: 40px !important;
            border: 1.5px solid #3b82f6 !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            font-size: 16px !important;
            font-weight: 800 !important;
            color: #1d4ed8 !important;
            text-align: center !important;
        }
        .action-input-card .action-pcs-input {
            height: 40px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            background: #f8fafc !important;
            font-size: 16px !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            text-align: center !important;
        }
        .action-input-card .action-hint {
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }

        .refund-banner-card {
            background: #ecfdf5;
            border: 1.5px solid #6ee7b7;
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .refund-banner-card .refund-label {
            font-size: 12px;
            font-weight: 800;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .refund-banner-card .refund-val-wrap {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }
        .refund-banner-card .refund-currency {
            font-size: 14px;
            font-weight: 800;
            color: #059669;
            font-family: monospace;
        }
        .refund-banner-card .refund-input {
            border: none !important;
            background: transparent !important;
            font-size: 18px !important;
            font-weight: 800 !important;
            color: #047857 !important;
            font-family: monospace !important;
            text-align: right !important;
            width: 130px !important;
            padding: 0 !important;
        }

        /* Mobile Responsive Enhancements (<= 991.98px) */
        @media (max-width: 991.98px) {
            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
            .erp-card .card-body {
                padding: 12px !important;
            }

            .erp-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px !important;
                padding: 12px 14px !important;
            }
            .erp-header .header-actions {
                width: 100% !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
            }

            /* Table Container & Hide Table Header */
            .erp-table-container {
                border: none !important;
                background: transparent !important;
            }
            .table-erp {
                display: block !important;
                width: 100% !important;
                border: none !important;
            }
            .table-erp thead {
                display: none !important;
            }
            .table-erp tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 14px !important;
                width: 100% !important;
            }

            /* Transform TR into Responsive Modern Card */
            #returnItems tr {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                grid-template-areas:
                    "product product"
                    "code size"
                    "ppb price"
                    "purchased purchased"
                    "returnbox totalpcs"
                    "rowtotal rowtotal" !important;
                align-items: stretch !important;
                row-gap: 8px !important;
                column-gap: 8px !important;
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 14px !important;
                padding: 14px !important;
                box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05) !important;
                position: relative !important;
            }
            #returnItems tr:hover {
                border-color: #cbd5e1 !important;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
            }
            #returnItems tr td {
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
                min-width: 0 !important;
                width: auto !important;
            }

            /* Card Cells */
            .col-item-product {
                grid-area: product;
                padding-right: 36px !important;
            }
            .col-item-action {
                position: absolute !important;
                top: 12px !important;
                right: 12px !important;
                text-align: right !important;
            }
            .col-item-code {
                grid-area: code;
            }
            .col-item-size {
                grid-area: size;
            }
            .col-item-ppb {
                grid-area: ppb;
            }
            .col-item-price {
                grid-area: price;
            }
            .col-item-purchased {
                grid-area: purchased;
            }
            .col-item-returnbox {
                grid-area: returnbox;
            }
            .col-item-totalpcs {
                grid-area: totalpcs;
            }
            .col-item-rowtotal {
                grid-area: rowtotal;
            }
        }

        @media (min-width: 992px) {
            .col-item-product {
                padding-right: 0 !important;
            }
            .col-item-action {
                position: static !important;
                text-align: center !important;
            }
            .spec-card {
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
                display: block !important;
            }
            .spec-card .spec-title {
                display: none !important;
            }
            .spec-card .spec-data {
                font-size: inherit !important;
                font-weight: inherit !important;
                font-family: inherit !important;
                color: inherit !important;
            }
            .avail-card {
                display: none !important;
            }
            .action-input-card {
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
                display: block !important;
                height: auto !important;
                box-shadow: none !important;
            }
            .action-input-card .action-label, .action-input-card .action-hint {
                display: none !important;
            }
            .action-input-card .action-box-input, .action-input-card .action-pcs-input {
                height: auto !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                border: 1px solid transparent !important;
                background: transparent !important;
                color: inherit !important;
                border-radius: 4px !important;
            }
            .action-input-card .action-box-input:focus, .action-input-card .action-box-input:hover {
                border-color: var(--erp-border) !important;
                background: white !important;
            }
            .refund-banner-card {
                background: transparent !important;
                border: none !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 4px !important;
            }
            .refund-banner-card .refund-label {
                display: none !important;
            }
            .refund-banner-card .refund-val-wrap {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                gap: 4px !important;
                width: 100% !important;
            }
            .refund-banner-card .refund-currency {
                display: inline !important;
                font-size: 0.85rem !important;
                color: #059669 !important;
            }
            .refund-banner-card .refund-input {
                font-size: inherit !important;
                font-weight: bold !important;
                color: var(--erp-text) !important;
                width: 100% !important;
            }
        }
    </style>


    <!-- Structure Wrapper -->
    <div class="container-fluid py-4">
        <div class="erp-card">
            <div class="erp-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-undo-alt me-2"></i> Sale Return</h5>
                <div class="header-actions d-flex align-items-center gap-2 gap-md-3">
                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-file-invoice me-1 text-primary"></i> Invoice #{{ $sale->invoice_no }}</span>
                    <a href="{{ route('sale.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <form action="{{ route('sale.return.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                    <!-- Alert Section -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Top Section: Customer & Reference -->
                    <div class="row g-2 g-md-3 mb-3 mb-md-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label"><i class="fas fa-user text-muted me-1" style="font-size: 0.75rem;"></i>Customer</label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ optional($sale->customer)->name ?? (optional($sale->customer_relation)->customer_name ?? 'Walk-in Customer') }}" readonly
                                style="background-color: #f8fafc; border-color: #cbd5e1; font-weight: 600;">
                            <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label"><i class="fas fa-file-invoice text-muted me-1" style="font-size: 0.75rem;"></i>Reference / Inv #</label>
                            <input type="text" name="reference" class="form-control form-control-sm"
                                value="{{ $sale->invoice_no ?? '' }}" readonly style="background-color: #f8fafc; border-color: #cbd5e1;">
                        </div>
                        <div class="col-6 col-md-5 text-end align-self-end">
                            <div class="p-2 bg-light rounded border w-100 text-start text-md-end">
                                <small class="text-muted d-block" style="font-size: 0.68rem; font-weight: 600; text-transform: uppercase;">Original Sale Date</small>
                                <strong class="text-dark" style="font-size: 0.85rem;"><i class="far fa-calendar-alt text-primary me-1"></i>
                                    {{ $sale->created_at->format('d/m/Y h:i A') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Return Info -->
                    <div class="row g-2 g-md-3 mb-3 mb-md-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label"><i class="fas fa-warehouse text-muted me-1" style="font-size: 0.75rem;"></i>Return to Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required style="height: 42px;">
                                @if(isset($warehouses) && count($warehouses) > 0)
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ (old('warehouse_id', $defaultWarehouseId ?? 1) == $wh->id) ? 'selected' : '' }}>
                                            {{ $wh->warehouse_name }} {{ $wh->location ? '('.$wh->location.')' : '' }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="{{ $defaultWarehouseId ?? 1 }}" selected>Default Warehouse</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label">Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}"
                                required style="height: 42px;">
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="form-label">Return Reason / Notes</label>
                            <input type="text" name="return_reason" class="form-control"
                                placeholder="e.g., Damaged goods, Wrong item sent" style="height: 42px;">
                        </div>
                    </div>

                    <!-- Items Table Header Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-boxes text-primary me-1"></i> Items for Return</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="btnReturnAll">
                            <i class="fas fa-check-double me-1"></i> Return All Items
                        </button>
                    </div>
                    <div class="table-responsive erp-table-container mb-4">
                        <table class="table table-erp table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 20%;"><i class="fas fa-lock me-1"
                                            style="font-size: 0.65rem; opacity: 0.6;"></i>Product</th>
                                    <th style="width: 8%;">Size</th>
                                    {{-- <th style="width: 8%;">Color</th> --}}
                                    <th style="width: 8%;"><i class="fas fa-lock me-1"
                                            style="font-size: 0.65rem; opacity: 0.6;"></i>Item Code</th>
                                    <th style="width: 12%;"><i class="fas fa-lock me-1"
                                            style="font-size: 0.65rem; opacity: 0.6;"></i>PC per box</th>
                                    <th style="width: 10%;"><i class="fas fa-lock me-1"
                                            style="font-size: 0.65rem; opacity: 0.6;"></i>Purchased Price</th>
                                    <th style="width: 10%;"><i class="fas fa-lock me-1"
                                            style="font-size: 0.65rem; opacity: 0.6;"></i>Purchased Qty PC</th>
                                    <th style="width: 10%;">Return Qty (Box.Piece)</th>
                                    <th style="width: 12%;">Total Return Pieces</th>
                                    <th style="width: 12%;">Total Amount</th>
                                    <th style="width: 5%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="returnItems">
                                @foreach ($sale->items as $index => $item)
                                    <tr>
                                        <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}">
                                        <input type="hidden" name="color[]" value="{{ $item['color'] ?? '' }}">
                                        {{-- Hidden Discount to preserve refund math if needed --}}
                                        <input type="hidden" name="item_disc[]" class="item_disc"
                                            value="{{ $item['discount'] ?? 0 }}" data-original-discount="{{ $item['discount'] ?? 0 }}">
                                        <input type="hidden" name="unit[]" value="{{ $item['unit'] ?? 'pc' }}">
                                        <input type="hidden" name="size_mode[]" class="size-mode"
                                            value="{{ $item['size_mode'] ?? 'by_pieces' }}">
                                        <input type="hidden" name="pieces_per_m2[]" class="pieces-per-m2"
                                            value="{{ $item['pieces_per_m2'] ?? 0 }}">

                                        {{-- 1. Product Name & Brand --}}
                                        <td class="col-item-product">
                                            <div class="d-flex align-items-center">
                                                <span class="d-lg-none badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill me-2 px-2 py-1" style="font-size: 11px; font-weight: 700;">#{{ $index + 1 }}</span>
                                                <div class="overflow-hidden">
                                                    <span class="fw-bold text-dark d-block text-truncate product-name-display" style="font-size: 0.95rem;">{{ $item['item_name'] }}</span>
                                                    <input type="hidden" class="form-control fw-bold product-name-input" value="{{ $item['item_name'] }}" readonly>
                                                    @if(!empty($item['brand']))
                                                        <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;"><i class="fas fa-tag me-1 text-secondary" style="font-size: 0.65rem;"></i>{{ $item['brand'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 2. Size --}}
                                        <td class="col-item-size">
                                            <div class="spec-card">
                                                <span class="spec-title">Size</span>
                                                <span class="spec-data">{{ $item['size_val'] ?? '-' }}</span>
                                            </div>
                                        </td>

                                        {{-- 3. Item Code --}}
                                        <td class="col-item-code">
                                            <div class="spec-card">
                                                <span class="spec-title">Code</span>
                                                <span class="spec-data">{{ $item['item_code'] ?: '-' }}</span>
                                            </div>
                                        </td>

                                        {{-- 4. PC per box --}}
                                        <td class="col-item-ppb">
                                            <div class="spec-card">
                                                <span class="spec-title">Pcs/Box</span>
                                                <span class="spec-data d-lg-none">{{ $item['pieces_per_box'] ?? 1 }} pcs</span>
                                                <input type="number" class="form-control text-center pieces-per-box spec-value d-none d-lg-block"
                                                    value="{{ $item['pieces_per_box'] ?? 1 }}" readonly>
                                            </div>
                                        </td>

                                        {{-- 5. Purchased Price --}}
                                        <td class="col-item-price">
                                            <div class="spec-card">
                                                <span class="spec-title">Price</span>
                                                <span class="spec-data d-lg-none">Rs. {{ number_format((float) $item['price'], 2) }}</span>
                                                <div class="price-wrap text-end text-lg-end d-none d-lg-block">
                                                    <input type="number" name="price[]" step="0.01"
                                                        class="form-control text-end price spec-value p-0" value="{{ (float) $item['price'] }}"
                                                        readonly>
                                                    <small class="text-muted d-block text-end" style="font-size: 0.65rem;">
                                                        @if (($item['size_mode'] ?? '') == 'by_size')
                                                            Per M²
                                                        @elseif(($item['size_mode'] ?? '') == 'by_cartons')
                                                            Per Box
                                                        @else
                                                            Per Pc
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 6. Purchased Qty (Read Only) --}}
                                        <td class="col-item-purchased">
                                            @php
                                                $original = $item['original_qty'] ?? $item['qty'];
                                                $returned = $item['returned_qty'] ?? 0;
                                                $netRemaining = $item['max_returnable'] ?? ($original - $returned);
                                                $ppb = $item['pieces_per_box'] ?? 1;
                                                
                                                // Format remaining pieces to Box.Piece
                                                if ($ppb > 1) {
                                                    $remBoxes = floor($netRemaining / $ppb);
                                                    $remPcs = $netRemaining % $ppb;
                                                    $remDisplay = $remBoxes . ($remPcs > 0 ? '.'.$remPcs : '');
                                                    $availText = $remBoxes . ' Box' . ($remPcs > 0 ? ' + ' . $remPcs . ' pcs' : '') . ' (' . $netRemaining . ' pcs)';
                                                } else {
                                                    $remDisplay = $netRemaining;
                                                    $availText = $netRemaining . ' pcs';
                                                }
                                            @endphp
                                            {{-- Mobile Availability Card --}}
                                            <div class="avail-card d-lg-none">
                                                <div class="avail-head">
                                                    <span class="avail-label"><i class="fas fa-box-open me-1 text-primary"></i> Available To Return</span>
                                                    <span class="avail-badge">{{ $availText }}</span>
                                                </div>
                                                <div class="avail-sub">
                                                    <span><i class="fas fa-shopping-bag me-1 text-muted"></i> Sold: <strong>{{ $original }} pcs</strong></span>
                                                    <span><i class="fas fa-history me-1 text-muted"></i> Returned: <strong>{{ $returned }} pcs</strong></span>
                                                </div>
                                            </div>

                                            {{-- Desktop Availability Cell --}}
                                            <div class="purchased-qty-wrap d-none d-lg-block">
                                                <div class="text-center">
                                                    <span class="fw-bold text-dark fs-6">{{ $remDisplay }}</span>
                                                    <small class="text-muted d-block" style="font-size: 0.68rem;">Rem: {{ $netRemaining }} pcs</small>
                                                </div>
                                                @if ($returned > 0)
                                                    <div class="mt-1 pt-1 border-top text-center" style="font-size: 0.65rem;">
                                                        <span class="text-danger">Returned: {{ $returned }}</span>
                                                        <span class="text-muted ms-1">/ {{ $original }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 7. Return Qty (Box.Piece) --}}
                                        <td class="col-item-returnbox">
                                            <div class="action-input-card highlight-input">
                                                <span class="action-label text-primary d-lg-none"><i class="fas fa-edit me-1"></i> Return Qty</span>
                                                <input type="text" name="qty_box[]"
                                                    class="form-control text-center quantity-box action-box-input" value="0"
                                                    placeholder="0.0" {{ $netRemaining <= 0 ? 'readonly' : '' }}>
                                                <span class="action-hint d-lg-none">Box.Piece format</span>
                                            </div>
                                        </td>

                                        {{-- 8. Total Return Pieces (Read Only, Calculated) --}}
                                        <td class="col-item-totalpcs">
                                            <div class="action-input-card">
                                                <span class="action-label text-secondary d-lg-none"><i class="fas fa-calculator me-1"></i> Total Pieces</span>
                                                <input type="number" name="qty[]"
                                                    class="form-control text-center fw-bold text-primary quantity action-pcs-input"
                                                    value="0" readonly min="0" max="{{ $netRemaining }}"
                                                    data-max="{{ $netRemaining }}" data-original="{{ $original }}"
                                                    data-returned="{{ $returned }}">
                                                <span class="action-hint d-lg-none">Calculated Pieces</span>
                                            </div>
                                        </td>

                                        {{-- 9. Total Amount --}}
                                        <td class="col-item-rowtotal">
                                            <div class="refund-banner-card">
                                                <span class="refund-label"><i class="fas fa-undo me-1"></i> Refund Subtotal</span>
                                                <div class="refund-val-wrap">
                                                    <span class="refund-currency">Rs.</span>
                                                    <input type="text" name="total[]"
                                                        class="form-control text-end fw-bold row-total refund-input" value="0.00" readonly>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 10. Action Button --}}
                                        <td class="col-item-action text-end text-lg-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger border-0 remove-row rounded-circle"
                                                style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: #fef2f2; color: #ef4444;"
                                                title="Remove Item"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Summary -->
                    <div class="row g-3 mt-2">
                        <div class="col-12 col-md-7 mb-2 mb-md-0">
                            <div class="p-3 p-md-4 bg-light rounded-3 border h-100">
                                <label class="form-label text-muted small fw-bold">AMOUNT IN WORDS</label>
                                <input type="text" name="total_amount_Words"
                                    class="form-control border-0 bg-transparent fw-bold text-primary fs-6 fst-italic p-0"
                                    id="amountInWords" readonly placeholder="...">

                                <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-cubes text-primary fs-5"></i>
                                        <span class="text-muted small fw-semibold">Total Pieces Returned:</span>
                                    </div>
                                    <strong id="totalPieces" class="text-dark fs-5 font-monospace">0</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Partial Return Status Indicator -->
                        <div class="col-12 mb-2">
                            <div class="partial-return-indicator shadow-sm border-0 p-3 rounded-3"
                                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 text-white fw-bold">
                                        <i class="fas fa-chart-pie me-2"></i>Return Status
                                    </h6>
                                    <span id="returnTypeBadge" class="badge bg-light text-dark">
                                        <i class="fas fa-spinner fa-spin me-1"></i>Calculating...
                                    </span>
                                </div>
                                <div class="progress" style="height: 25px; background: rgba(255,255,255,0.2); border-radius: 6px;">
                                    <div id="returnProgressBar"
                                        class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                        style="width: 0%; background: #10ac84; border-radius: 6px;" aria-valuenow="0" aria-valuemin="0"
                                        aria-valuemax="100">
                                        <strong id="returnPercentage">0%</strong>
                                    </div>
                                </div>
                                <div class="mt-2 text-white small" id="returnStatusText">
                                    <i class="fas fa-info-circle me-1"></i>Select items to return
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-5">
                            <div class="summary-card shadow-sm border rounded-3 p-3 p-md-4 bg-white">
                                <h6 class="mb-3 text-uppercase fw-bold text-dark d-flex align-items-center gap-2"
                                    style="font-size: 0.85rem; letter-spacing: 0.8px;">
                                    <i class="fas fa-calculator text-primary"></i> Refund Summary
                                </h6>
                                <div class="summary-row d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted">Subtotal</span>
                                    <input type="text" name="total_subtotal" id="billAmount"
                                        class="form-control form-control-sm w-50 text-end border-0 bg-transparent p-0 font-monospace fw-bold text-dark"
                                        readonly value="0.00">
                                </div>
                                <div class="summary-row d-flex justify-content-between align-items-center py-1">
                                    <span class="text-muted">Less: Item Discount</span>
                                    <input type="text" name="total_discount" id="itemDiscount"
                                        class="form-control form-control-sm w-50 text-end border-0 bg-transparent p-0 text-danger font-monospace"
                                        readonly value="0.00">
                                </div>
                                <div class="summary-row d-flex justify-content-between align-items-center py-1">
                                    <span class="text-dark fw-medium">Less: Extra Deductions</span>
                                    <input type="number" name="extra_discount" id="extraDiscount"
                                        class="form-control form-control-sm w-50 text-end bg-white border font-monospace"
                                        value="{{ (float) ($sale->total_extradiscount ?? 0) }}">
                                </div>
                                <hr class="my-2">
                                <div class="summary-row total d-flex justify-content-between align-items-center pt-2">
                                    <span class="fw-bold text-dark">NET REFUND AMOUNT</span>
                                    <input type="text" name="net_amount" id="netAmount"
                                        class="form-control form-control-lg w-50 text-end border-0 bg-transparent p-0 fw-bold text-success font-monospace"
                                        readonly value="0.00">
                                </div>

                                <!-- Payment Voucher Section -->
                                <div class="mt-3 pt-3 border-top">
                                    <h6 class="mb-2 text-uppercase fw-bold text-muted"
                                        style="font-size: 0.78rem; letter-spacing: 0.8px;">
                                        <i class="fas fa-money-bill-wave text-success me-1"></i> Refund Received (Optional)
                                    </h6>

                                    <div class="alert alert-light border small text-muted py-2 px-3 mb-2" style="font-size: 11px;">
                                        <i class="fas fa-info-circle text-info me-1"></i> If you received cash/bank refund, enter details. Otherwise, amount will be credited to customer ledger.
                                    </div>

                                    <div class="payment-voucher-rows">
                                        <div class="payment-row mb-2">
                                            <div class="row g-2">
                                                <div class="col-7">
                                                    <select name="payment_account_id[]"
                                                        class="form-select form-select-sm payment-account" style="width: 100%;">
                                                        <option value="">Select Account</option>
                                                        @foreach ($accounts as $acc)
                                                            <option value="{{ $acc->id }}">{{ $acc->title }}
                                                                ({{ $acc->account_code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-5">
                                                    <input type="number" name="payment_amount[]" step="0.01"
                                                        class="form-control form-control-sm text-end payment-amount"
                                                        placeholder="Rs. Amount">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-grid gap-2">
                                    <button type="submit" class="btn btn-erp-primary btn-lg shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2" style="height: 48px; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #4f46e5);">
                                        <i class="fas fa-check-circle"></i> Process Sale Return
                                    </button>
                                </div>
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

            // Initialize Select2
            // Initialize Select2
            if ($.fn.select2) {
                $('.payment-account').select2();
            } else {
                console.warn('Select2 not loaded');
            }

            function num(n) {
                return isNaN(parseFloat(n)) ? 0 : parseFloat(n);
            }

            function numberToWords(num) {
                const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                    "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen",
                    "Eighteen", "Nineteen"
                ];
                const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
                if ((num = num.toString()).length > 9) return "Overflow";
                const n = ("000000000" + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{3})$/);
                if (!n) return;
                let str = "";
                str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + " " + a[n[1][1]]) + " Crore " : "";
                str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + " " + a[n[2][1]]) + " Lakh " : "";
                str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + " " + a[n[3][1]]) + " Thousand " : "";
                str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + " " + a[n[4][1]]) + " " : "";
                return str.trim() + " Rupees Only";
            }

            function recalcRow($row) {
                const qty = num($row.find('.quantity').val()); // Pieces (Total)
                const price = num($row.find('.price').val()); // Price (Unit Price)
                const sizeMode = $row.find('.size-mode').val();
                const ppm2 = num($row.find('.pieces-per-m2').val());
                const ppb = num($row.find('.pieces-per-box').val()) || 1;

                let total = 0;

                if (sizeMode === 'by_size') {
                    total = qty * ppm2 * price;
                } else if (['by_cartons', 'by_bandal'].includes(sizeMode) || sizeMode === 'by_carton') {
                    // Price is Per Box, qty is Total Pieces
                    total = ppb > 0 ? (qty / ppb) * price : qty * price;
                } else {
                    // Price is Per Piece
                    total = qty * price;
                }

                $row.find('.row-total').val(total.toFixed(2));
            }

            function recalcSummary() {
                let billAmount = 0;
                let totalQty = 0;
                let itemDiscountTotal = 0;

                $('#returnItems tr').each(function() {
                    const qty = num($(this).find('.quantity').val());
                    const rowTotal = num($(this).find('.row-total').val());

                    billAmount += rowTotal;
                    totalQty += qty;

                    const originalDiscount = num($(this).find('.item_disc').attr('data-original-discount'));
                    const originalQty = num($(this).find('.quantity').attr('data-original'));
                    
                    if (originalQty > 0) {
                        const calculatedDiscount = (originalDiscount / originalQty) * qty;
                        itemDiscountTotal += calculatedDiscount;
                        $(this).find('.item_disc').val(calculatedDiscount);
                    }
                });

                const extraDiscount = num($('#extraDiscount').val()); // Deduction

                const net = billAmount - itemDiscountTotal - extraDiscount;

                $('#billAmount').val(billAmount.toFixed(2));
                $('#itemDiscount').val(itemDiscountTotal.toFixed(2));
                $('#netAmount').val(net.toFixed(2));

                if (net > 0) {
                    $('#amountInWords').val(numberToWords(Math.round(net)));
                } else {
                    $('#amountInWords').val('Zero Rupees');
                }

                $('#totalPieces').text(totalQty);

                // Update visual indicators
                updatePartialReturnIndicator();
            }

            // Return All Button Logic
            $('#btnReturnAll').click(function() {
                $('#returnItems tr').each(function() {
                    const $row = $(this);
                    const maxQty = parseFloat($row.find('.quantity').attr('data-max')) || 0;
                    const ppb = parseFloat($row.find('.pieces-per-box').val()) || 1;

                    // Set Total Pieces
                    $row.find('.quantity').val(maxQty);

                    // Calculate Box.Piece representation
                    let boxDisplay = '';
                    if (ppb > 1) {
                        const boxes = Math.floor(maxQty / ppb);
                        const pieces = maxQty % ppb;
                        if (pieces > 0) {
                            boxDisplay = boxes + '.' + pieces;
                        } else {
                            boxDisplay = boxes;
                        }
                    } else {
                        boxDisplay = maxQty; // If no box size, just show total pieces
                    }

                    // Update Box Input
                    $row.find('.quantity-box').val(boxDisplay);

                    // Trigger recalc
                    recalcRow($row);
                });
                recalcSummary();
            });

            // Auto-fill Max on Click/Focus if 0
            $(document).on('click focus', '.quantity-box', function() {
                const val = $(this).val();
                // Check if 0, 0.0, or empty
                if (parseFloat(val) === 0 || val === '') {
                    const $row = $(this).closest('tr');
                    const maxQty = parseFloat($row.find('.quantity').attr('data-max')) || 0;
                    const ppb = parseFloat($row.find('.pieces-per-box').val()) || 1;

                    // Calculate Box.Piece representation
                    let boxDisplay = '';
                    if (ppb > 1) {
                        const boxes = Math.floor(maxQty / ppb);
                        const pieces = maxQty % ppb;
                        if (pieces > 0) {
                            boxDisplay = boxes + '.' + pieces;
                        } else {
                            boxDisplay = boxes;
                        }
                    } else {
                        boxDisplay = maxQty;
                    }

                    $(this).val(boxDisplay);
                    $(this).trigger('input'); // Update totals
                    $(this).select(); // Select text for easy overwrite
                }
            });

            // Box.Piece Input Logic
            $(document).on('input', '.quantity-box', function() {
                const $row = $(this).closest('tr');
                const val = $(this).val();
                const ppb = num($row.find('.pieces-per-box').val());

                // Parse Box.Piece format
                // Example: 1.2 => 1 Box, 2 Pieces (NOT 1.2 Boxes)
                // Logic: Integer part = Box. Decimal part = Pieces.

                let boxes = 0;
                let pieces = 0;

                if (val.includes('.')) {
                    const parts = val.split('.');
                    boxes = num(parts[0]);

                    // STRICT LOGIC: "10.1" -> 1 piece. "10.01" -> 1 piece. "10.10" -> 10 pieces.
                    // We treat the part after dot as an integer "Piece Count" directly.
                    const decimalPart = parts[1];
                    if (decimalPart) {
                        // User explicitly said "10.1 means 10 box and 1 piece".
                        // Standard float would treat 10.1 as 10.10 (10 pieces if ppb=100) or similar.
                        // Here we take the string value literally as integer pieces.
                        pieces = parseInt(decimalPart);
                    }
                } else {
                    boxes = num(val);
                }

                // Validation: If pieces >= ppb, just allow it but it might look weird.
                // Or auto-convert? User said "apply it everything... accucsry".
                // If I have 12 pieces/box and write 0.12, that is 12 pieces => 1 box.
                // Let's just sum it up.

                let totalPieces = 0;
                if (ppb > 0) {
                    totalPieces = (boxes * ppb) + pieces;
                } else {
                    totalPieces = boxes; // If no box size, inputs are pieces
                }

                // Update Read-Only Total Pieces
                $row.find('.quantity').val(totalPieces);

                // Trigger validations and recalc
                $row.find('.quantity').trigger('input');
            });


            // Events
            $(document).on('input', '.quantity, .price, #extraDiscount', function() {
                const $row = $(this).closest('tr');

                // Validate max returnable quantity
                if ($(this).hasClass('quantity')) {
                    const qtyPc = num($(this).val());
                    const maxReturnable = num($(this).attr('data-max'));

                    if (qtyPc > maxReturnable) {
                        $(this).val(maxReturnable);
                        $(this).addClass('border-danger');

                        // Show warning
                        if (!$(this).next('.text-danger').length) {
                            $(this).after('<small class="text-danger d-block">Max: ' + maxReturnable +
                                ' pieces (Purchased Qty)</small>');
                        }

                        setTimeout(() => {
                            $(this).removeClass('border-danger');
                            $(this).next('.text-danger').fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 2000);
                    }
                }

                if ($row.length) {
                    recalcRow($row);
                }
                recalcSummary();
            });

            // Initialize
            $('#returnItems tr').each(function() {
                recalcRow($(this));
            });
            recalcSummary();

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcSummary();
            });

            // Update Partial Return Visual Indicator
            function updatePartialReturnIndicator() {
                let totalOriginalPieces = 0;
                let totalReturningPieces = 0;

                $('#returnItems tr').each(function() {
                    const $qtyInput = $(this).find('.quantity');
                    const soldQty = num($qtyInput.attr('data-max')); // Max is original
                    const returningQty = num($qtyInput.val());

                    totalOriginalPieces += soldQty;
                    totalReturningPieces += returningQty;
                });

                const returnPercentage = totalOriginalPieces > 0 ? (totalReturningPieces / totalOriginalPieces *
                        100) :
                    0;

                // Update progress bar
                $('#returnProgressBar').css('width', returnPercentage + '%');
                $('#returnProgressBar').attr('aria-valuenow', returnPercentage);
                $('#returnPercentage').text(returnPercentage.toFixed(1) + '%');

                // Update badge and status text
                if (totalReturningPieces === 0) {
                    $('#returnTypeBadge').html('<i class="fas fa-info-circle me-1"></i>No Items Selected');
                    $('#returnTypeBadge').removeClass().addClass('badge bg-secondary');
                    $('#returnStatusText').html('<i class="fas fa-info-circle me-1"></i>Select items to return');
                    $('#returnProgressBar').css('background', '#6c757d');
                } else if (returnPercentage >= 100) {
                    $('#returnTypeBadge').html('<i class="fas fa-check-circle me-1"></i>Full Return');
                    $('#returnTypeBadge').removeClass().addClass('badge bg-success');
                    $('#returnStatusText').html('<i class="fas fa-check-circle me-1"></i>Returning all ' +
                        totalOriginalPieces + ' pieces (100% of purchase)');
                    $('#returnProgressBar').css('background', '#10ac84');
                } else {
                    $('#returnTypeBadge').html('<i class="fas fa-chart-pie me-1"></i>Partial Return');
                    $('#returnTypeBadge').removeClass().addClass('badge bg-warning text-dark');
                    $('#returnStatusText').html('<i class="fas fa-chart-pie me-1"></i>Returning ' +
                        totalReturningPieces + ' of ' + totalOriginalPieces + ' pieces (' + returnPercentage
                        .toFixed(1) + '%)');
                    $('#returnProgressBar').css('background', '#f79f1f');
                }
            }


        });
    </script>
@endsection
