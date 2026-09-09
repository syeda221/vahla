@extends('admin_panel.layout.app')

@section('content')
    <!-- Loader Overlay -->
    <div id="pageLoader"
        class="{{ isset($sale) ? '' : 'd-none' }} position-fixed top-0 start-0 w-100 h-100 d-flex flex-column gap-3 justify-content-center align-items-center"
        style="background: rgba(255,255,255,0.9); z-index: 1055;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="fw-bold text-primary fs-5">Loading Sale Data...</div>
    </div>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ==================== NEW SALE — CLEAN MODERN ERP/POS UI ==================== */
        :root {
            --pos-blue: #2563EB;
            --pos-blue-hover: #1D4ED8;
            --pos-blue-soft: #EFF6FF;
            --pos-green: #16A34A;
            --pos-green-soft: #F0FDF4;
            --pos-red: #DC2626;
            --pos-red-soft: #FEF2F2;
            --pos-orange: #F59E0B;
            --pos-orange-soft: #FFFBEB;
            --pos-text: #1E293B;
            --pos-muted: #64748B;
            --pos-border: #C7D0DA;
            --pos-border-strong: #AEBAC7;
            --pos-bg: #F8FAFC;
            --pos-card: #FFFFFF;
            --pos-radius: 10px;
            --pos-radius-lg: 14px;
            --pos-shadow-sm: 0 1px 2px rgba(15,23,42,.04);
            --pos-shadow-md: 0 6px 20px -6px rgba(15,23,42,.10);
            --pos-input-h: 42px;
        }

        body {
            background-color: var(--pos-bg) !important;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            color: var(--pos-text) !important;
            -webkit-font-smoothing: antialiased;
        }

        .sale-page {
            max-width: 1560px;
            margin: 0 auto;
        }

        /* ---------- CARDS ---------- */
        .sale-card {
            background: var(--pos-card);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius-lg);
            box-shadow: var(--pos-shadow-sm);
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--pos-text);
            line-height: 1.3;
        }

        /* ---------- LABELS ---------- */
        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--pos-muted);
            margin-bottom: 6px;
            line-height: 1.2;
        }

        /* ---------- INPUTS ---------- */
        .sale-page .form-control,
        .sale-page .form-select {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--pos-text);
            background-color: #ffffff;
            box-shadow: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .sale-page .form-control::placeholder {
            color: #94A3B8;
            font-weight: 400;
        }
        .sale-page .form-control:focus,
        .sale-page .form-select:focus {
            border-color: var(--pos-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: none;
            background-color: #ffffff;
        }
        .sale-page .input-readonly,
        .sale-page input[readonly] {
            background-color: #F8FAFC !important;
            color: var(--pos-muted) !important;
            cursor: default;
        }

        /* Select2 (customer) */
        #customerInputWrapper .select2-container--default .select2-selection--single {
            height: var(--pos-input-h) !important;
            border: 1px solid var(--pos-border) !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            padding: 0 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 12px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: var(--pos-text) !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }
        #customerInputWrapper .select2-container--default.select2-container--focus .select2-selection--single,
        #customerInputWrapper .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--pos-blue) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12) !important;
        }

        /* ---------- BUTTONS ---------- */
        .sale-page .btn-primary {
            background: var(--pos-blue);
            border-color: var(--pos-blue);
            color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .sale-page .btn-primary:hover,
        .sale-page .btn-primary:focus {
            background: var(--pos-blue-hover);
            border-color: var(--pos-blue-hover);
            color: #ffffff;
        }
        .sale-page .btn-outline-primary {
            color: var(--pos-blue);
            border-color: #BFDBFE;
            background: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .sale-page .btn-outline-primary:hover {
            background: var(--pos-blue-soft);
            color: var(--pos-blue-hover);
            border-color: var(--pos-blue);
        }
        .sale-page .btn-outline-secondary {
            color: var(--pos-muted);
            border-color: var(--pos-border);
            background: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .sale-page .btn-outline-secondary:hover {
            background: #F1F5F9;
            color: var(--pos-text);
            border-color: var(--pos-border-strong);
        }

        .btn-save-print {
            padding: 10px 20px !important;
            box-shadow: 0 4px 12px -2px rgba(37, 99, 235, .35);
        }

        .btn-icon-back {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--pos-border);
            background: #ffffff;
            color: var(--pos-muted);
            font-size: 15px;
            flex-shrink: 0;
            transition: all .15s ease;
        }
        .btn-icon-back:hover {
            background: #F1F5F9;
            color: var(--pos-text);
            border-color: var(--pos-border-strong);
        }

        /* ---------- PAGE HEADER ---------- */
        .sale-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 20px;
        }
        .sale-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .sale-title-ic {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--pos-blue-soft);
            color: var(--pos-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .sale-title-main h5 {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.3px;
            color: var(--pos-text);
            margin-bottom: 2px;
        }
        .sale-subtitle {
            font-size: 13px;
            color: var(--pos-muted);
        }

        /* ---------- SALE TYPE SEGMENTED TOGGLE ---------- */
        .seg-toggle {
            display: flex;
            height: var(--pos-input-h);
            background: #F1F5F9;
            border: 1px solid var(--pos-border);
            border-radius: 8px;
            padding: 3px;
            width: 100%;
        }
        .seg-toggle .btn {
            flex: 1;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 10px;
        }
        .seg-toggle .btn.btn-outline-primary {
            background: transparent;
        }
        .seg-toggle .btn-outline-primary:hover {
            background: rgba(37, 99, 235, .08);
        }

        /* ---------- INVOICE GROUP ---------- */
        .invoice-group {
            flex-wrap: nowrap;
        }
        .invoice-group .btn-prefix {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-right: none;
            background: #F8FAFC;
            color: var(--pos-text);
            font-weight: 700;
            font-size: 13px;
            border-radius: 8px 0 0 8px;
            padding: 0 12px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .invoice-group .btn-prefix:hover {
            background: #F1F5F9;
        }
        .invoice-group #inputInvoiceNo {
            border-radius: 0;
            border-left: none;
            border-right: none;
            font-family: Consolas, 'JetBrains Mono', monospace;
            font-size: 13.5px;
            font-weight: 700 !important;
        }
        .invoice-group .btn-refresh {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-left: none;
            background: #ffffff;
            color: var(--pos-muted);
            border-radius: 0 8px 8px 0;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .15s ease;
        }
        .invoice-group .btn-refresh:hover {
            background: #F1F5F9;
            color: var(--pos-blue);
        }

        /* ---------- CUSTOMER BALANCE CARD ---------- */
        .cust-bal-card {
            background: linear-gradient(180deg, #EEF6FF 0%, #F7FBFF 70%, #FFFFFF 100%);
            border: 1px solid #CFE2FA;
            border-radius: 10px;
            padding: 8px 12px;
            box-sizing: border-box;
            height: 136px;
            min-height: 136px;
            max-height: 136px;
            overflow: hidden;
            box-shadow: 0 2px 10px -4px rgba(37, 99, 235, .12);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .cb-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 5px;
        }
        .cb-id {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .cb-avatar {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: var(--pos-blue);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px -2px rgba(37, 99, 235, .45);
        }
        .cb-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cb-code {
            font-size: 11px;
            color: var(--pos-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cb-extras {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 5px;
        }
        .cb-ext {
            background: #FFFFFF;
            border: 1px solid #E3EEFC;
            border-radius: 7px;
            padding: 4px 7px;
        }
        .cb-ext-label {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            font-weight: 600;
            color: #5B84C4;
            margin-bottom: 1px;
        }
        .cb-ext-val {
            font-size: 12px;
            font-weight: 600;
            color: var(--pos-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .cb-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }
        .cb-cell {
            background: #FFFFFF;
            border: 1px solid #E3EEFC;
            border-radius: 7px;
            padding: 5px 2px;
            text-align: center;
        }
        .cb-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .3px;
            font-weight: 600;
            color: #5B84C4;
            margin-bottom: 2px;
            white-space: nowrap;
        }
        .cb-value {
            font-size: 12px;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
        }
        .cust-bal-card .text-danger {
            color: var(--pos-red) !important;
        }
        .cust-bal-card .text-success {
            color: var(--pos-green) !important;
        }
        #cc_paid_now {
            color: var(--pos-green) !important;
        }

        /* ---------- ITEMS HEADER ---------- */
        .items-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--pos-text);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .items-count {
            background: var(--pos-blue-soft);
            color: var(--pos-blue);
            font-weight: 700;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 12px;
        }

        /* ---------- PRODUCT CARDS ---------- */
        .pos-product-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: background .15s ease;
        }
        .pos-product-card:last-child {
            margin-bottom: 0;
        }
        .pos-product-card:hover {
            background: #F8FAFC;
        }
        .pos-product-img {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #F1F5F9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pos-product-info {
            flex: 1;
            min-width: 0;
        }
        .pos-product-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pos-product-sub {
            font-size: 12px;
            color: var(--pos-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pos-product-price {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
        }
        .pos-product-add-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--pos-blue);
            color: #ffffff;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            cursor: pointer;
            transition: background .15s ease;
        }
        .pos-product-add-btn:hover {
            background: var(--pos-blue-hover);
        }
        .badge-stock-green {
            background-color: var(--pos-green-soft) !important;
            color: #15803D !important;
            font-weight: 700 !important;
            border: 1px solid #BBF7D0 !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            font-size: 11.5px !important;
        }

        /* ---------- PRODUCT TABLE ---------- */
        .table-responsive {
            border: 1px solid var(--pos-border);
            border-radius: 10px;
            background: #ffffff;
            overflow-x: auto;
        }
        .sales-table {
            min-width: 1060px;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin-bottom: 0;
        }
        .sales-table thead th {
            background: #F8FAFC;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 11px 8px;
            border-bottom: 1px solid var(--pos-border);
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .sales-table thead th.col-product {
            text-align: left;
            padding-left: 14px;
        }
        .sales-table tbody td {
            padding: 7px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }
        .sales-table tbody tr:last-child td {
            border-bottom: none;
        }
        .sales-table tbody tr:hover td {
            background: #FBFDFF;
        }
        .row-index-cell {
            font-size: 13px;
            font-weight: 700;
            color: #94A3B8;
            text-align: center;
        }

        /* Table inputs — clean flat cells that highlight on focus */
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            height: 38px !important;
            border: 1px solid transparent !important;
            border-radius: 6px !important;
            padding: 4px 9px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: var(--pos-text) !important;
            width: 100% !important;
            transition: border-color .12s ease, background .12s ease, box-shadow .12s ease;
        }
        .sales-table tbody .form-control:hover,
        .sales-table tbody .form-select:hover {
            border-color: var(--pos-border) !important;
            background: #ffffff !important;
        }
        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus,
        .sales-table tbody .form-control:focus-visible {
            border-color: var(--pos-blue) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10) !important;
            outline: none !important;
        }
        .sales-table tbody input[readonly],
        .sales-table tbody .input-readonly {
            background: #FAFBFC !important;
            color: var(--pos-muted) !important;
            cursor: default !important;
            font-weight: 600 !important;
        }
        .sales-table tbody input[readonly]:hover {
            border-color: transparent !important;
        }

        /* Stock badge style inside stock cell */
        .stock-badge {
            display: inline-block;
            background: #F1F5F9;
            color: #475569;
            border: 1px solid var(--pos-border);
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            padding: 4px 8px;
            line-height: 1.2;
        }
        .stock-badge.strong {
            background: var(--pos-green-soft);
            color: #15803D;
            border-color: #BBF7D0;
        }

        /* Product select2 inside table */
        .sales-table tbody .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid transparent !important;
            border-radius: 6px !important;
            background: transparent !important;
            padding: 0 !important;
        }
        .sales-table tbody .select2-container:hover .select2-selection--single {
            border-color: var(--pos-border) !important;
            background: #ffffff !important;
        }
        .sales-table tbody .select2-container--focus .select2-selection--single,
        .sales-table tbody .select2-container--open .select2-selection--single {
            border-color: var(--pos-blue) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10) !important;
            background: #ffffff !important;
        }
        .sales-table tbody .select2-container .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 9px !important;
            padding-right: 18px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: var(--pos-text) !important;
        }
        .sales-table tbody .select2-container .select2-selection__arrow {
            height: 36px !important;
            right: 6px !important;
        }

        /* Qty cell */
        .qty-cell-flex {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .qty-cell-flex .carton-qty {
            flex: 1;
            min-width: 0;
        }
        .qty-unit-toggle {
            height: 38px !important;
            min-width: 42px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px !important;
        }

        /* Price cell */
        .price-cell-flex {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .price-cell-flex .visible-price {
            flex: 1;
            min-width: 0;
        }
        .price-mode-row-toggle {
            height: 38px !important;
            min-width: 32px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        /* Discount cell */
        .discount-wrapper {
            display: flex;
            align-items: stretch;
            gap: 4px;
        }
        .discount-wrapper .discount-value {
            flex: 1;
            min-width: 0;
            text-align: right;
        }
        .discount-wrapper .discount-toggle {
            width: 32px;
            flex-shrink: 0;
            height: 38px !important;
            border: 1px solid var(--pos-border) !important;
            background: #F8FAFC !important;
            color: var(--pos-muted) !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            border-radius: 6px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            transition: all .15s ease;
        }
        .discount-wrapper .discount-toggle:hover {
            background: #EEF2F7 !important;
            color: var(--pos-blue) !important;
        }

        /* Amount cell */
        .sales-amount {
            font-weight: 800 !important;
            color: var(--pos-text) !important;
            font-size: 14px !important;
        }

        /* Row delete button */
        .sales-table .del-row {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #FECACA;
            background: #ffffff;
            color: var(--pos-red);
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
            transition: all .15s ease;
        }
        .sales-table .del-row:hover {
            background: var(--pos-red);
            border-color: var(--pos-red);
            color: #ffffff;
        }

        /* Grid total footer */
        .sales-table tfoot td {
            background: #F8FAFC;
            border-top: 1px solid var(--pos-border);
            padding: 13px 16px;
        }
        .grid-total-label {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--pos-muted);
            text-align: right;
        }
        .grid-total-val {
            font-size: 17px;
            font-weight: 800;
            color: var(--pos-text);
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- PAYMENT METHODS ---------- */
        .pay-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 1px solid #F1F5F9;
        }
        .rv-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
        }
        .rv-row .rv-account {
            flex: 1;
            min-width: 0;
        }
        .rv-row .rv-amount {
            width: 132px;
            flex-shrink: 0;
            text-align: right;
            font-weight: 600;
        }
        .btnRemRV {
            width: 36px;
            height: var(--pos-input-h);
            border-radius: 8px;
            border: 1px solid var(--pos-border);
            background: #ffffff;
            color: var(--pos-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .15s ease;
        }
        .btnRemRV:hover {
            background: var(--pos-red-soft);
            color: var(--pos-red);
            border-color: #FECACA;
        }
        .change-row {
            border-top: 1px dashed var(--pos-border);
            margin-top: 12px;
            padding-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .change-row .change-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--pos-muted);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .change-row .form-select {
            width: 150px;
            height: 36px !important;
        }

        /* ---------- ORDER SUMMARY ---------- */
        .s-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            font-size: 14px;
        }
        .s-row + .s-row {
            border-top: 1px solid #F1F5F9;
        }
        .s-label {
            color: var(--pos-muted);
        }
        .s-val {
            font-weight: 600;
            color: var(--pos-text);
            font-variant-numeric: tabular-nums;
        }
        .s-row.net {
            padding: 15px 0;
            border-top: 2px solid var(--pos-border);
            align-items: flex-end;
        }
        .net-label {
            font-size: 15px;
            font-weight: 800;
            color: var(--pos-text);
        }
        .net-val {
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -.4px;
            color: var(--pos-blue);
            font-variant-numeric: tabular-nums;
        }
        .paid-val {
            font-weight: 700;
            font-size: 15px;
            color: var(--pos-green);
            font-variant-numeric: tabular-nums;
        }
        .change-val {
            font-weight: 700;
            font-size: 14.5px;
            font-variant-numeric: tabular-nums;
        }
        .change-val.text-success {
            color: var(--pos-green) !important;
        }
        .change-val.text-danger {
            color: var(--pos-red) !important;
        }
        .discount-input {
            width: 150px;
            flex-shrink: 0;
        }
        .discount-input input {
            font-weight: 600;
            text-align: right;
        }
        .discount-input .input-group-text {
            background: #F8FAFC;
            border: 1px solid var(--pos-border);
            border-radius: 0 8px 8px 0;
            color: var(--pos-muted);
            font-weight: 600;
            font-size: 12px;
        }

        /* ---------- STICKY BOTTOM ACTION BAR ---------- */
        .sale-bottom-bar {
            position: sticky;
            bottom: 0;
            z-index: 40;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px 18px;
            background: #ffffff;
            border: 1px solid var(--pos-border);
            border-radius: 12px;
            box-shadow: 0 -8px 24px -12px rgba(15, 23, 42, .18);
            padding: 12px 18px;
            margin-top: 18px;
        }
        .bb-left {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 13.5px;
            color: var(--pos-muted);
            flex-wrap: wrap;
        }
        .bb-left b {
            color: var(--pos-text);
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }
        .bb-left .text-success {
            color: var(--pos-green) !important;
        }
        .btn-ghost {
            border: 1px solid var(--pos-border);
            background: #ffffff;
            color: var(--pos-muted);
            border-radius: 8px;
            font-weight: 600;
            font-size: 12.5px;
            padding: 6px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .15s ease;
        }
        .btn-ghost:hover {
            background: #F8FAFC;
            color: var(--pos-text);
            border-color: var(--pos-border-strong);
        }
        .bb-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ---------- OFF-CANVAS (Quick Products) ---------- */
        .offcanvas-header {
            border-bottom: 1px solid var(--pos-border);
        }

        /* ---------- VALIDATION STATES ---------- */
        .invalid-input,
        .invalid-select {
            border-color: var(--pos-red) !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
        }
        .invalid-cell {
            background: #FFF7F7 !important;
            box-shadow: inset 0 0 0 1px rgba(220, 38, 38, .25) !important;
        }
        .invalid-input + .select2-container .select2-selection--single,
        .invalid-select + .select2-container .select2-selection--single {
            border-color: var(--pos-red) !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
        }

        /* ---------- ALERT BOX ---------- */
        #alertBox {
            border-radius: 10px;
            font-size: 13.5px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 1199.98px) {
            .cust-bal-card {
                height: auto;
                min-height: 0;
                max-height: none;
                overflow: visible;
            }
            .cb-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 575.98px) {
            .sale-subtitle {
                display: none;
            }
            .bb-left {
                gap: 12px;
                font-size: 12.5px;
            }
            .bb-actions .btn-outline-secondary,
            .bb-actions .btn-outline-primary {
                display: none;
            }
            .bb-actions .btn-primary {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid px-3 px-lg-4 pt-3 pb-4 sale-page">

        <div id="alertBox" class="alert d-none" role="alert"></div>

        <form id="saleForm" autocomplete="off">
            @csrf
            <input type="hidden" id="booking_id" name="booking_id" value="">
            <input type="hidden" id="action" name="action" value="sale">
            <input type="hidden" name="cash" value="0">
            <input type="hidden" id="totalBalance" value="0">

            {{-- ============================ PAGE HEADER ============================ --}}
            <div class="sale-header">
                <div class="sale-header-left">
                    <a href="{{ route('sale.index') }}" class="btn-icon-back" title="Back to Sales List">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="sale-title-ic">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="sale-title-main">
                        <h5 class="header-text mb-0">New Sale</h5>
                        <div class="sale-subtitle">Create a new invoice &amp; manage checkout</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnHeaderSaveDraft" class="btn btn-outline-primary px-3">
                        <i class="fas fa-save me-1"></i> Save Draft
                    </button>
                    <button type="button" id="btnHeaderSaveSale" class="btn btn-primary btn-save-print px-3">
                        <i class="fas fa-print me-1"></i> Save &amp; Print Invoice
                    </button>
                </div>
            </div>

            {{-- ============================ SALE INFORMATION CARD ============================ --}}
            <div class="sale-card mb-3 p-4">
                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="row g-3">
                            {{-- Invoice No --}}
                            <div class="col-6 col-md-3">
                                <label class="field-label" for="inputInvoiceNo">Invoice No.</label>
                                <div class="input-group invoice-group">
                                    <button class="btn btn-prefix dropdown-toggle d-flex align-items-center gap-1"
                                            type="button"
                                            id="btnInvoicePrefix"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <span id="activePrefixLabel">{{ $activePrefix ?? 'INV' }}</span>
                                    </button>
                                    <ul class="dropdown-menu shadow-lg p-1 border-0" id="dropdownInvoiceSeriesList" aria-labelledby="btnInvoicePrefix" style="min-width: 160px; font-size: 13px; z-index: 1050;">
                                        @if(isset($allSeries) && count($allSeries) > 0)
                                            @foreach($allSeries as $s)
                                                <li>
                                                    <a class="dropdown-item fw-bold {{ ($activePrefix ?? 'INV') == $s->prefix ? 'text-success active bg-light' : '' }}"
                                                       href="#"
                                                       data-prefix="{{ $s->prefix }}"
                                                       data-next="{{ $s->next_number }}"
                                                       data-padding="{{ $s->padding }}">
                                                        @if(($activePrefix ?? 'INV') == $s->prefix) <i class="fas fa-check text-success me-1"></i> @endif
                                                        {{ $s->prefix }} <span class="text-muted small font-monospace">({{ $s->padding }}d)</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            <li><a class="dropdown-item fw-bold text-success active bg-light" href="#" data-prefix="INV"><i class="fas fa-check text-success me-1"></i> INV (4d)</a></li>
                                        @endif
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item fw-bold text-success d-flex align-items-center gap-1" href="#" id="btnOpenAddSeriesModal">
                                                <i class="fas fa-plus-circle me-1"></i> Add Series
                                            </a>
                                        </li>
                                    </ul>

                                    <input type="text" class="form-control text-center fw-bold input-readonly" name="Invoice_no" id="inputInvoiceNo" value="{{ $nextInvoiceNumber }}" readonly>

                                    <button class="btn btn-refresh"
                                            type="button"
                                            id="btnRefreshInvoiceNo"
                                            title="Regenerate Invoice Number">
                                        <i class="fas fa-sync-alt" id="iconRefreshInvoice"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Credit Days --}}
                            <div class="col-6 col-md-2">
                                <label class="field-label" for="creditDaysInput">Credit Days</label>
                                <input type="number" class="form-control text-center" id="creditDaysInput" name="credit_days" placeholder="Days" min="0" value="{{ $sale->credit_days ?? '0' }}">
                            </div>

                            {{-- Sale Type --}}
                            <div class="col-12 col-md-4">
                                <label class="field-label">Sale Type</label>
                                <div class="seg-toggle" role="group" aria-label="Sale Type">
                                    <button type="button" class="btn btn-primary active text-white" id="btnTypeCustomer">
                                        <i class="fas fa-users me-1"></i> Customer
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="btnTypeWalkin">
                                        <i class="fas fa-walking me-1"></i> Walk-in
                                    </button>
                                </div>
                                <select class="d-none" id="partyTypeSelect" name="partyType">
                                    @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                        <option value="{{ $type->name }}" {{ $type->name === 'Main Customer' ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date --}}
                            <div class="col-6 col-md-3">
                                <label class="field-label" for="displayDateInput">Date</label>
                                <input type="text" name="sale_date" class="form-control datepicker-custom" id="displayDateInput" value="{{ date('d/m/Y') }}">
                            </div>

                            {{-- Reference / Remarks --}}
                            <div class="col-6 col-md-3">
                                <label class="field-label" for="remarks">Reference / Remarks</label>
                                <input type="text" class="form-control" name="reference" id="remarks" placeholder="Optional">
                            </div>

                            {{-- Customer --}}
                            <div class="col-12 col-md-6">
                                <label class="field-label" for="customerSelect">Customer</label>
                                <div class="d-flex gap-2">
                                    <div id="customerInputWrapper" class="flex-grow-1" style="min-width: 0;">
                                        <input type="text" class="form-control d-none" name="walkin_name" id="walkinNameInput" value="Walk-in Customer" placeholder="Enter Walk-in Name...">
                                        <select class="form-select" id="customerSelect" name="customer" style="width:100%">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <button type="button" id="btnOpenAddCustomerModal"
                                            class="btn btn-outline-primary flex-shrink-0 align-self-stretch"
                                            style="width: var(--pos-input-h); padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                            data-toggle="modal" data-target="#addCustomerModal"
                                            data-bs-toggle="modal" data-bs-target="#addCustomerModal"
                                            title="Quick Add Customer (Alt+C or F2)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Balance Card (right) --}}
                    <div class="col-xl-4">
                        <div class="cust-bal-card">
                            <div class="cb-head">
                                <div class="cb-id">
                                    <div class="cb-avatar"><i class="fas fa-user"></i></div>
                                    <div style="min-width:0;">
                                        <div class="cb-name" id="cc_customer_name">Select Customer</div>
                                        <div class="cb-code">Code: <span id="ci_code">—</span></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-link btn-sm text-muted text-decoration-none p-0" id="clearCustomerData" style="font-size:12px;">Clear</button>
                            </div>

                            <div id="customerInfoCard" class="d-none cb-extras">
                                <div class="cb-ext">
                                    <div class="cb-ext-label">Full Name</div>
                                    <div class="cb-ext-val" id="ci_name">—</div>
                                </div>
                                <div class="cb-ext">
                                    <div class="cb-ext-label">Mobile</div>
                                    <div class="cb-ext-val" id="ci_mobile">—</div>
                                </div>
                                <div class="cb-ext">
                                    <div class="cb-ext-label">Address</div>
                                    <div class="cb-ext-val" id="ci_address">—</div>
                                </div>
                            </div>

                            <div class="cb-grid">
                                <div class="cb-cell">
                                    <div class="cb-label">Prev. Due</div>
                                    <div class="cb-value">
                                        <span id="cc_prev_bal_val">Rs 0</span> <span id="cc_prev_bal_suffix">Dr</span>
                                    </div>
                                </div>
                                <div class="cb-cell">
                                    <div class="cb-label">Current Due</div>
                                    <div class="cb-value" id="cc_current_bill">Rs 0</div>
                                </div>
                                <div class="cb-cell">
                                    <div class="cb-label">Paid</div>
                                    <div class="cb-value" id="cc_paid_now">Rs 0</div>
                                </div>
                                <div class="cb-cell">
                                    <div class="cb-label">Closing</div>
                                    <div class="cb-value">
                                        <span id="cc_closing_bal_val">Rs 0</span> <span id="cc_closing_bal_suffix">Dr</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hidden fields for backend --}}
            <input type="hidden" name="is_walkin" id="is_walkin" value="0">
            <input type="hidden" id="address" name="address">
            <input type="hidden" id="tel" name="tel">
            <input type="hidden" id="previousBalance" value="0">
            <input type="hidden" id="rangeBalance" value="0">

            {{-- ============================ ITEMS SECTION ============================ --}}
            <div class="sale-card mb-3 p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="items-title">
                        Items
                        <span class="items-count" id="itemsRowCount">0</span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary px-3" data-bs-toggle="offcanvas" data-bs-target="#quickProductsOffcanvas">
                            <i class="fas fa-th me-1"></i> Quick Products
                        </button>
                        <button type="button" class="btn btn-primary px-3" id="btnAdd">
                            <i class="fas fa-plus me-1"></i> Add Product
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table sales-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:44px;">#</th>
                                <th class="col-product" style="min-width:230px;">Product</th>
                                <th style="width:82px;">Stock</th>
                                <th style="width:112px;">Qty</th>
                                <th style="width:82px;">Size</th>
                                <th style="width:82px;">Pcs</th>
                                <th style="width:118px;">Price</th>
                                <th style="width:118px;">Discount</th>
                                <th style="width:132px;">Amount</th>
                                <th style="width:58px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            <tr>
                                <td class="row-index-cell row-index">1</td>

                                <!-- PRODUCT -->
                                <td class="col-product">
                                    <select class="form-select product" style="width:100%">
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" class="product-id-hidden" name="product_id[]">
                                    <input type="hidden" class="variant-data-hidden" name="color[]">
                                    <input type="hidden" class="item-code-display">
                                    <input type="hidden" class="size-h">
                                    <input type="hidden" class="size-w">
                                    <input type="hidden" class="size-mode-text">
                                </td>

                                <!-- STOCK -->
                                <td class="col-stock text-center">
                                    <input type="text" class="form-control stock text-center input-readonly" readonly tabindex="-1">
                                    <input type="hidden" class="warehouse" name="warehouse_id[]" value="{{ auth()->user()->warehouse_id ?? 1 }}">
                                    <input type="hidden" class="variant-stock-value">
                                </td>

                                <!-- QTY -->
                                <td class="col-qty-wrapper">
                                    <div class="qty-cell-flex">
                                        <input type="number" step="any" class="form-control carton-qty text-start fw-bold" name="carton_qty[]" placeholder="0" min="0" value="">
                                        <button type="button" class="btn btn-sm qty-unit-toggle px-1 py-0 d-none"
                                                data-unit-mode="main" title="Toggle Unit"
                                                style="background: #fff; color: #2563EB; border: 1px solid #BFDBFE;">
                                            Kg
                                        </button>
                                    </div>
                                    <input type="hidden" class="hidden-sub-unit-mode" name="sub_unit_mode[]" value="main">
                                </td>

                                <!-- SIZE -->
                                <td class="col-size">
                                    <input type="text" class="form-control size-display text-center" name="size_display[]" placeholder="-">
                                    <input type="hidden" class="pack-qty" name="pack_qty[]" value="1">
                                </td>

                                <!-- PCS -->
                                <td class="col-pieces">
                                    <input type="text" class="form-control total-pieces text-end input-readonly fw-semibold" name="total_pieces[]" readonly placeholder="0" tabindex="-1">
                                    <input type="hidden" class="sales-qty" name="qty[]" value="0">
                                </td>

                                <!-- PRICE -->
                                <td class="col-price-p">
                                    <div class="price-cell-flex">
                                        <input type="text" class="form-control visible-price text-end fw-semibold" name="visible_price[]" placeholder="0">
                                        <button type="button" class="btn btn-sm price-mode-row-toggle px-1 py-0"
                                                data-mode="retail" title="Retail Mode"
                                                style="background: #fff; border: 1px solid #BFDBFE; color: #2563EB;">
                                            R
                                        </button>
                                    </div>
                                    <input type="hidden" class="price-per-piece" name="price_per_piece[]">
                                    <input type="hidden" class="retail-price">
                                    <input type="hidden" class="wholesale-price">
                                    <input type="hidden" class="weight-per-piece">
                                </td>

                                <!-- DISCOUNT -->
                                <td class="col-disc">
                                    <div class="discount-wrapper">
                                        <input type="number" class="form-control discount-value text-end" name="item_disc[]" placeholder="0">
                                        <input type="hidden" class="discount-type-hidden" name="discount_type[]" value="percent">
                                        <button type="button" class="btn btn-outline-secondary discount-toggle" data-type="percent" tabindex="-1">%</button>
                                    </div>
                                    <input type="hidden" class="discount-amount" value="0">
                                </td>

                                <!-- AMOUNT -->
                                <td class="col-amount">
                                    <input type="text" class="form-control sales-amount text-end input-readonly" name="total[]" value="0" readonly tabindex="-1">
                                    <input type="hidden" class="gross-amount" name="gross_amount[]">
                                </td>

                                <!-- ACTION -->
                                <td class="col-action text-center">
                                    <button type="button" class="del-row" tabindex="-1" title="Delete Row">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="grid-total-label">Grid Total:</td>
                                <td class="grid-total-val">Rs <span id="totalAmount">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ============================ PAYMENT METHODS & ORDER SUMMARY ============================ --}}
            <div class="row g-3 align-items-stretch">
                {{-- LEFT: Payment Methods --}}
                <div class="col-lg-6">
                    <div class="sale-card h-100 p-4 d-flex flex-column">
                        <div class="pay-head">
                            <span class="card-title">Payment Methods</span>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" id="btnAddRV">
                                <i class="fas fa-plus me-1"></i> Add Payment
                            </button>
                        </div>

                        <div id="rvWrapper">
                            <div class="rv-row">
                                <select class="form-select rv-account" name="receipt_account_id[]">
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') || str_contains(strtolower($acc->title), 'easypaisa') ? 'selected' : '' }}>{{ $acc->title }}</option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.01" class="form-control rv-amount" name="receipt_amount[]" placeholder="0.00">
                            </div>
                        </div>

                        <div class="mt-auto pt-3">
                            <div class="change-row" id="changeAccountRow" style="display:none;">
                                <span class="change-label">
                                    <i class="fas fa-exchange-alt me-1"></i> Change Account
                                </span>
                                <select class="form-select" name="change_account_id" id="changeAccountId">
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>{{ $acc->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Order Summary --}}
                <div class="col-lg-6">
                    <div class="sale-card h-100 p-4 d-flex flex-column">
                        <div class="pay-head">
                            <span class="card-title">Order Summary</span>
                        </div>

                        <div class="flex-grow-1">
                            <div class="s-row">
                                <span class="s-label">Subtotal</span>
                                <span class="s-val" id="tGross">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Line Discount</span>
                                <span class="s-val" id="tLineDisc">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Discount (Rs)</span>
                                <div class="input-group input-group-sm discount-input">
                                    <input type="number" class="form-control text-end" id="walkinDiscountRs" value="0" placeholder="0">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                            <div class="s-row net">
                                <span class="net-label">Net Total</span>
                                <span class="net-val" id="tSub">0.00</span>
                                <span id="walkinNetTotal" class="d-none">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Total Paid</span>
                                <span class="paid-val" id="receiptsTotal">0.00</span>
                                <span id="receiptsTotalBadge" style="display:none;">0.00</span>
                                <span id="bottomPaymentsTotal" class="d-none">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Remaining</span>
                                <span class="s-val" id="tPayable">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Change</span>
                                <span class="change-val" id="walkinChange">-0.00</span>
                                <span id="bottomChangeVal" class="d-none">-0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================ STICKY BOTTOM ACTION BAR ============================ --}}
            <div class="sale-bottom-bar">
                <div class="bb-left">
                    <span>Items: <b id="footerItemCount">0</b></span>
                    <span>Total: <b>Rs <span id="footerTotal">0.00</span></b></span>
                    <span>Paid: <b class="text-success">Rs <span id="footerPaid">0.00</span></b></span>
                </div>

                <div class="bb-secondary">
                    <button type="button" class="btn-ghost" id="btnPrint"><i class="fas fa-print"></i> A4 Print</button>
                    <button type="button" class="btn-ghost" id="btnEstimate"><i class="fas fa-file-invoice"></i> Estimate</button>
                    <button type="button" class="btn-ghost" id="btnPrint2"><i class="fas fa-receipt"></i> Thermal</button>
                    <button type="button" class="btn-ghost" id="btnDcThermal"><i class="fas fa-truck"></i> DC</button>
                    <button type="button" class="d-none" id="btnPosted">Sale</button>
                </div>

                <div class="bb-actions">
                    <a href="{{ route('sale.index') }}" class="btn btn-outline-secondary px-3">Cancel</a>
                    <button type="button" class="btn btn-outline-primary px-3" id="btnSave">
                        <i class="fas fa-save me-1"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-primary btn-save-print px-3" id="btnSaveAndComplete">
                        <i class="fas fa-print me-1"></i> Save &amp; Print Invoice
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Products Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="quickProductsOffcanvas" style="width: 360px;">
        <div class="offcanvas-header bg-light py-2 border-bottom">
            <h6 class="offcanvas-title fw-bold text-dark mb-0"><i class="fas fa-th text-primary me-2"></i>Quick Products Panel</h6>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-2">
            <div class="input-group input-group-sm mb-2">
                <input type="text" class="form-control" id="sidebarProductSearch" placeholder="Search product by name, barcode or SKU...">
                <button class="btn btn-primary px-2" type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="overflow-auto pe-1" id="sidebarProductContainer" style="max-height: calc(100vh - 120px);">
                @if(isset($recentProducts) && count($recentProducts) > 0)
                    @foreach($recentProducts as $prod)
                        <div class="pos-product-card">
                            <div class="pos-product-img">
                                <i class="fas fa-box text-secondary fs-5"></i>
                            </div>
                            <div class="pos-product-info">
                                <div class="pos-product-name" title="{{ $prod->item_name }}">{{ $prod->item_name }}</div>
                                <div class="pos-product-sub">
                                    <span class="badge-stock-green">{{ $prod->total_pieces ?? 0 }} Pcs</span> Stock
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="pos-product-price">{{ number_format($prod->retail_price ?? 0, 2) }}</div>
                                <button type="button" class="pos-product-add-btn add-product-direct-btn" data-id="{{ $prod->id }}" title="Add to Grid"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background: #2563EB !important; padding: 14px 18px;">
                    <h5 class="modal-title font-weight-bold fw-bold text-white mb-0" id="addCustomerModalLabel" style="font-size: 1rem;">
                        <i class="fas fa-user-plus me-2 mr-2"></i>Quick Customer
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.9; cursor: pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="ajaxAddCustomerForm" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Customer Type <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="customer_type" id="modalCustomerType" required>
                                    @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                        <option value="{{ $type->name }}" {{ $type->name === 'Main Customer' ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" id="modalCustomerName" required placeholder="Customer Name">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Mobile</label>
                                <input type="text" class="form-control" name="mobile" placeholder="0300-1234567">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold fw-bold">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Address">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm fw-bold" id="btnSaveAjaxCustomer">
                        <i class="fas fa-save me-1 mr-1"></i> Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Add Product Modal --}}
    @include('admin_panel.partials.quick_add_product_modal')
@endsection

@section('js')
    @include('admin_panel.sale.scripts.shared_logic')

    <script>
        $(document).ready(function() {
            // --- Initial Setup ---
            $('#salesTableBody tr').each(function() {
                initProductSelect2($(this).find('.product'));
            });
            if ($('#salesTableBody tr').length === 0) {
                addNewRow();
            }
            updateGrandTotals();
            refreshPostedState();

            // --- Check if URL is for Booking Flow ---
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type') === 'booking') {
                $('.header-text').html('<i class="fas fa-bookmark text-primary me-2"></i>Add Booking');
                $('#action').val('booking');
                $('#btnPosted').addClass('d-none');
                $('#btnHeaderPosted').addClass('d-none');
            }

            // ============================================================
            // CUSTOMER SELECT2 AJAX SEARCH (Name or Code)
            // ============================================================
            function getPartyType() {
                return $('#partyTypeSelect').val() || 'Main Customer';
            }

            $('#customerSelect').select2({
                placeholder: 'Search by Name or Code...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('salecustomers.index') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            type: getPartyType(),
                            search: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(c) {
                                return {
                                    id: c.id,
                                    text: (c.customer_id || '') + ' — ' + c.customer_name,
                                    customer: c
                                };
                            })
                        };
                    },
                    cache: false
                },
                language: {
                    noResults: function() {
                        return $('<div>No customer found. <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-open-customer-modal" style="font-size:0.75rem;"><i class="fas fa-user-plus"></i> Quick Add Customer</a></div>');
                    }
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(item) {
                    if (item.loading) return item.text;
                    if (!item.customer) return item.text;
                    const c = item.customer;
                    return $(`<div>
                        <strong>${c.customer_name}</strong>
                        <small class="text-muted ms-2">${c.customer_id || ''}</small>
                        ${c.mobile ? '<br><small class="text-muted">' + c.mobile + '</small>' : ''}
                    </div>`);
                },
                templateSelection: function(item) {
                    if (!item.customer) return item.text;
                    return item.customer.customer_id + ' — ' + item.customer.customer_name;
                }
            });

            // Set initial visibility state of Customer Select / Walk-in input
            $('#partyTypeSelect').trigger('change');

            // Party type change → reset customer
            $(document).on('change', '#partyTypeSelect', function() {
                $('#customerSelect').val(null).trigger('change');
                clearCustomerInfo();
            });

            // Customer selected → load details
            $('#customerSelect').on('select2:select', function(e) {
                const id = e.params.data.id;
                if (!id) return;

                $.get("{{ url('sale/customers') }}/" + id + "?t=" + new Date().getTime(), function(d) {
                    // Fill hidden fields
                    $('#address').val(d.address || '');
                    $('#tel').val(d.mobile || '');
                    const prev = parseFloat(d.previous_balance || 0);
                    const range = parseFloat(d.balance_range || 0);
                    $('#previousBalance').val(prev.toFixed(2));
                    $('#rangeBalance').val(range.toFixed(2));

                    // Fill info card
                    $('#ci_code').text(d.customer_id || '—');
                    $('#ci_name').text(d.customer_name || '—');
                    $('#ci_mobile').text(d.mobile || '—');
                    $('#ci_address').text(d.address || '—');
                    $('#ci_prev_bal').text(prev.toFixed(2));
                    $('#ci_range_bal').text(range.toFixed(2));
                    $('#customerInfoCard').removeClass('d-none');

                    // Auto-fill Sales Officer if customer has one
                    if (d.sales_officer_id) {
                        $('#salesOfficerSelect').val(d.sales_officer_id);
                    }

                    if (typeof updateGrandTotals === 'function') updateGrandTotals();
                }).fail(function() {
                    showAlert('error', 'Failed to load customer details');
                });
            });

            // Customer cleared
            $('#customerSelect').on('select2:clear', function() {
                clearCustomerInfo();
                if (typeof updateGrandTotals === 'function') updateGrandTotals();
            });

            function clearCustomerInfo() {
                $('#address, #tel').val('');
                $('#previousBalance, #rangeBalance').val('0');
                $('#ci_code, #ci_name, #ci_mobile, #ci_address').text('—');
                $('#ci_prev_bal, #ci_range_bal').text('0.00');
                $('#customerInfoCard').addClass('d-none');
                $('#salesOfficerSelect').val('');
            }

            $('#clearCustomerData').on('click', function() {
                $('#customerSelect').val(null).trigger('change');
                clearCustomerInfo();
                if (typeof updateGrandTotals === 'function') updateGrandTotals();
            });

            $('#btnPrint').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/invoice', '_blank'));
            });
            $('#btnEstimate').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/invoice?type=estimate', '_blank'));
            });
            $('#btnPrint2').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/recepit', '_blank'));
            });
            $('#btnDcThermal').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/dc-thermal', '_blank'));
            });

            // ══════════════════════════════════════════════════════════════
            // QUICK CUSTOMER MODAL LOGIC & EVENT HANDLERS
            // ══════════════════════════════════════════════════════════════
            window.openCustomerModal = function(initialName = '') {
                $('#ajaxAddCustomerForm')[0].reset();
                let currentParty = $('#partyTypeSelect').val() || 'Main Customer';
                $('#modalCustomerType').val(currentParty);
                if (initialName && typeof initialName === 'string') {
                    $('#modalCustomerName').val(initialName.trim());
                }
                
                if (typeof $('#addCustomerModal').modal === 'function') {
                    $('#addCustomerModal').modal('show');
                } else if (window.bootstrap && window.bootstrap.Modal) {
                    let m = bootstrap.Modal.getOrCreateInstance(document.getElementById('addCustomerModal'));
                    m.show();
                }

                setTimeout(function() {
                    $('#modalCustomerName').focus();
                }, 400);
            };

            window.closeCustomerModal = function() {
                try {
                    $('#addCustomerModal').modal('hide');
                } catch(e) {}
                if (window.bootstrap && window.bootstrap.Modal) {
                    let m = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                    if (m) m.hide();
                }
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            };

            // Explicit click listener on any button with btn-open-customer-modal or #btnOpenAddCustomerModal
            $(document).on('click', '#btnOpenAddCustomerModal, .btn-open-customer-modal', function(e) {
                e.preventDefault();
                let term = '';
                // If clicked from select2 noResults or search box, grab search term
                if ($('.select2-search__field:visible').length) {
                    term = $('.select2-search__field:visible').val();
                    $('#customerSelect').select2('close');
                }
                openCustomerModal(term);
            });

            // Keyboard shortcut (F2 or Alt+C) to open Quick Customer modal
            $(document).on('keydown', function(e) {
                if ((e.key === 'F2' || (e.altKey && (e.key === 'c' || e.key === 'C'))) && !$('#addCustomerModal').is(':visible')) {
                    e.preventDefault();
                    openCustomerModal();
                }
            });

            // AJAX Customer Submit
            $('#btnSaveAjaxCustomer').on('click', function() {
                let form = $('#ajaxAddCustomerForm');
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }
                
                let btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                
                $.ajax({
                    url: '{{ route('customers.store') }}',
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1 mr-1"></i> Save Customer');
                        if (res.success) {
                            closeCustomerModal();
                            form[0].reset();
                            
                            // Make sure partyTypeSelect matches the customer type
                            if (res.customer.customer_type) {
                                $('#partyTypeSelect').val(res.customer.customer_type);
                            }
                            
                            // Auto select new customer in Select2
                            let displayText = (res.customer.customer_id ? res.customer.customer_id + ' — ' : '') + res.customer.customer_name;
                            let newOption = new Option(displayText, res.customer.id, true, true);
                            $('#customerSelect').append(newOption).trigger('change');
                            
                            // Trigger select2 API selection to load customer details like Prev Bal
                            $('#customerSelect').trigger({
                                type: 'select2:select',
                                params: {
                                    data: {
                                        id: res.customer.id,
                                        text: displayText
                                    }
                                }
                            });
                            
                            showAlert('success', 'Customer added successfully!');
                        } else {
                            showAlert('error', res.message || 'Failed to save customer.');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1 mr-1"></i> Save Customer');
                        let msg = 'Error adding customer. Check inputs.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('error', msg);
                    }
                });
            });

            // ══════════════════════════════════════════════════════════════
            // DYNAMIC INVOICE SERIES & PREFIX GENERATOR LOGIC (INSTANT 0ms)
            // ══════════════════════════════════════════════════════════════
            let currentInvoicePrefix = "{{ $activePrefix ?? 'INV' }}";

            function fetchNextInvoiceNo(prefix) {
                $('#iconRefreshInvoice').addClass('fa-spin');
                $.ajax({
                    url: "{{ route('invoice_series.generate_no') }}",
                    type: "GET",
                    data: { prefix: prefix },
                    success: function(res) {
                        $('#iconRefreshInvoice').removeClass('fa-spin');
                        if (res.invoice_no) {
                            $('#inputInvoiceNo').val(res.invoice_no);
                        }
                    },
                    error: function() {
                        $('#iconRefreshInvoice').removeClass('fa-spin');
                    }
                });
            }

            // Prefix Selection Handler
            $(document).on('click', '#dropdownInvoiceSeriesList a[data-prefix]', function(e) {
                e.preventDefault();
                let prefix = $(this).data('prefix');
                if (!prefix) return;

                currentInvoicePrefix = prefix;
                $('#activePrefixLabel').text(prefix);

                // Update active highlight in dropdown instantly
                $('#dropdownInvoiceSeriesList a[data-prefix]').removeClass('text-success active bg-light').find('i.fa-check').remove();
                $(this).addClass('text-success active bg-light').prepend('<i class="fas fa-check text-success me-1"></i>');

                fetchNextInvoiceNo(prefix);
            });

            // Refresh Invoice No Handler
            $(document).on('click', '#btnRefreshInvoiceNo', function() {
                fetchNextInvoiceNo(currentInvoicePrefix);
            });

            // Open Add Series Modal
            $(document).on('click', '#btnOpenAddSeriesModal', function(e) {
                e.preventDefault();
                $('#modalAddInvoiceSeries').modal('show');
            });

            // Submit Add Series Form via AJAX
            $('#formAddInvoiceSeries').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let btn = $('#btnSaveSeries');

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');

                $.ajax({
                    url: "{{ route('invoice_series.store') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save &amp; Select');
                        
                        if (res.success) {
                            $('#modalAddInvoiceSeries').modal('hide');
                            $('#formAddInvoiceSeries')[0].reset();

                            currentInvoicePrefix = res.prefix;
                            $('#activePrefixLabel').text(res.prefix);
                            $('#inputInvoiceNo').val(res.invoice_no);

                            // Update or insert item in dropdown list instantly
                            let existingItem = $(`#dropdownInvoiceSeriesList a[data-prefix="${res.prefix}"]`);
                            if (existingItem.length > 0) {
                                existingItem.data('next', res.series.next_number).data('padding', res.series.padding);
                            } else {
                                let newItemHtml = `<li>
                                    <a class="dropdown-item fw-bold text-success active bg-light" href="#" data-prefix="${res.prefix}" data-next="${res.series.next_number}" data-padding="${res.series.padding}">
                                        <i class="fas fa-check text-success me-1"></i> ${res.prefix} <span class="text-muted small font-monospace">(${res.series.padding}d)</span>
                                    </a>
                                </li>`;
                                $('#dropdownInvoiceSeriesList li:has(hr)').before(newItemHtml);
                            }

                            // Update active highlight state
                            $('#dropdownInvoiceSeriesList a[data-prefix]').removeClass('text-success active bg-light').find('i.fa-check').remove();
                            $(`#dropdownInvoiceSeriesList a[data-prefix="${res.prefix}"]`).addClass('text-success active bg-light').prepend('<i class="fas fa-check text-success me-1"></i>');

                            if (typeof showAlert === 'function') {
                                showAlert('success', res.message);
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
                            } else {
                                alert(res.message);
                            }
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save &amp; Select');
                        let msg = 'Error saving series. Please check form inputs.';
                        if (err.responseJSON && err.responseJSON.errors) {
                            msg = Object.values(err.responseJSON.errors).flat().join('\n');
                        } else if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        
                        if (typeof showAlert === 'function') {
                            showAlert('error', msg);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            });
        });
    </script>

    {{-- New Sale UI additions (footer totals sync, header save-draft, global product search) --}}
    <script>
        $(function() {
            // Footer totals sync (display-only mirror of existing values)
            if (typeof window.updateGrandTotals === 'function') {
                var __baseUGT = window.updateGrandTotals;
                window.updateGrandTotals = function() {
                    __baseUGT();
                    if ($('#footerItemCount').length) $('#footerItemCount').text($('#itemsRowCount').text());
                    if ($('#footerTotal').length) $('#footerTotal').text($('#tSub').text());
                    if ($('#footerPaid').length) $('#footerPaid').text($('#receiptsTotal').text());
                };
                window.updateGrandTotals();
            }

            // Header Save Draft -> existing booking/save flow
            $('#btnHeaderSaveDraft').on('click', function() {
                $('#btnSave').trigger('click');
            });
        });
    </script>

    <!-- Modal: Add / Manage Invoice Series -->
    <div class="modal fade" id="modalAddInvoiceSeries" tabindex="-1" aria-labelledby="modalAddInvoiceSeriesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalAddInvoiceSeriesLabel">
                        <i class="fas fa-barcode text-success me-1"></i> Add Invoice Series
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAddInvoiceSeries">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Prefix (e.g., SQ, POS, INV)</label>
                            <input type="text" name="prefix" id="seriesPrefixInput" class="form-control form-control-sm text-uppercase fw-bold" placeholder="e.g. SQ" required style="letter-spacing: 1px;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Starting Number (Counter)</label>
                            <input type="number" name="next_number" id="seriesNextNumInput" class="form-control form-control-sm fw-bold text-primary" placeholder="e.g. 50" min="1" value="50" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Padding Length (Zero Digits)</label>
                            <select name="padding" id="seriesPaddingSelect" class="form-select form-select-sm fw-bold">
                                <option value="4">4 Digits (e.g., 0050)</option>
                                <option value="6" selected>6 Digits (e.g., 000050)</option>
                                <option value="8">8 Digits (e.g., 00000050)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top p-2 px-3">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-3" id="btnSaveSeries">
                            <i class="fas fa-save me-1"></i> Save &amp; Select
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection