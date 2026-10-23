@extends('admin_panel.layout.app')
@section('content')

<style>
    /* ── ERP Stock Adjustment – Modern Design System ── */
    :root {
        --adj-primary:       #4f46e5;
        --adj-primary-hover: #4338ca;
        --adj-primary-light: #eef2ff;
        --adj-success:       #059669;
        --adj-success-light: #ecfdf5;
        --adj-danger:        #dc2626;
        --adj-danger-light:  #fef2f2;
        --adj-warning:       #d97706;
        --adj-warning-light: #fffbeb;
        --adj-info:          #0284c7;
        --adj-info-light:    #e0f2fe;
        --adj-card-bg:       #ffffff;
        --adj-border:        #e2e8f0;
        --adj-border-subtle: #f1f5f9;
        --adj-text-main:     #0f172a;
        --adj-text-muted:    #64748b;
        --adj-radius-lg:     14px;
        --adj-radius-md:     10px;
        --adj-radius-sm:     6px;
        --adj-shadow-sm:     0 1px 3px rgba(15,23,42,0.05);
        --adj-shadow-md:     0 4px 16px rgba(15,23,42,0.06);
    }

    .stock-adj-page {
        background-color: #f8fafc;
        min-height: calc(100vh - 80px);
        padding: 16px 0 32px 0;
    }

    /* ── Top Header Banner ── */
    .adj-header-card {
        background: #ffffff;
        border: 1px solid var(--adj-border);
        border-radius: var(--adj-radius-lg);
        box-shadow: var(--adj-shadow-sm);
        padding: 18px 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .adj-header-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .adj-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--adj-primary-light) 0%, #e0e7ff 100%);
        color: var(--adj-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(79,70,229,0.12);
    }
    .adj-header-title h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--adj-text-main);
        letter-spacing: -0.2px;
    }
    .adj-header-title p {
        margin: 3px 0 0 0;
        font-size: 0.82rem;
        color: var(--adj-text-muted);
    }

    .btn-create-adj {
        background: linear-gradient(135deg, var(--adj-primary) 0%, #6366f1 100%);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.86rem;
        padding: 9px 20px;
        border-radius: var(--adj-radius-md);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        box-shadow: 0 4px 12px rgba(79,70,229,0.25);
        border: none;
        transition: all 0.2s ease;
    }
    .btn-create-adj:hover {
        background: linear-gradient(135deg, var(--adj-primary-hover) 0%, var(--adj-primary) 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(79,70,229,0.32);
        color: #ffffff;
    }

    /* ── Metric Summary Cards ── */
    .adj-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }
    .adj-stat-box {
        background: var(--adj-card-bg);
        border-radius: var(--adj-radius-lg);
        border: 1px solid var(--adj-border);
        box-shadow: var(--adj-shadow-sm);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .adj-stat-box::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
    }
    .adj-stat-box.theme-primary::before { background: var(--adj-primary); }
    .adj-stat-box.theme-success::before { background: var(--adj-success); }
    .adj-stat-box.theme-danger::before  { background: var(--adj-danger); }
    .adj-stat-box.theme-warning::before { background: var(--adj-warning); }

    .adj-stat-box:hover {
        transform: translateY(-2px);
        box-shadow: var(--adj-shadow-md);
    }
    .adj-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .adj-stat-box.theme-primary .adj-stat-icon { background: var(--adj-primary-light); color: var(--adj-primary); }
    .adj-stat-box.theme-success .adj-stat-icon { background: var(--adj-success-light); color: var(--adj-success); }
    .adj-stat-box.theme-danger  .adj-stat-icon { background: var(--adj-danger-light);  color: var(--adj-danger); }
    .adj-stat-box.theme-warning .adj-stat-icon { background: var(--adj-warning-light); color: var(--adj-warning); }

    .adj-stat-meta .stat-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--adj-text-muted);
        margin-bottom: 2px;
    }
    .adj-stat-meta .stat-number {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--adj-text-main);
        line-height: 1.2;
        font-family: inherit;
    }
    .adj-stat-meta .stat-caption {
        font-size: 0.72rem;
        color: var(--adj-text-muted);
        margin-top: 2px;
    }

    /* ── Filter Card ── */
    .adj-filter-card {
        background: #ffffff;
        border: 1px solid var(--adj-border);
        border-radius: var(--adj-radius-lg);
        box-shadow: var(--adj-shadow-sm);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .adj-filter-header {
        padding: 12px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--adj-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .adj-filter-header-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--adj-text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .adj-filter-body {
        padding: 16px 20px;
    }
    .adj-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr 1.2fr auto;
        gap: 12px;
        align-items: flex-end;
    }
    .adj-filter-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .adj-filter-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--adj-text-muted);
        margin: 0;
    }
    .adj-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .adj-input-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: 13px;
        pointer-events: none;
    }
    .adj-form-control {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        font-size: 0.84rem;
        color: var(--adj-text-main);
        background-color: #ffffff;
        border: 1px solid var(--adj-border);
        border-radius: var(--adj-radius-md);
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .adj-form-control:focus {
        border-color: var(--adj-primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
    }
    .adj-input-wrapper .adj-form-control {
        padding-left: 34px;
    }

    .adj-btn-apply {
        background: var(--adj-primary);
        color: #ffffff;
        border: 1px solid var(--adj-primary);
        height: 38px;
        padding: 0 16px;
        border-radius: var(--adj-radius-md);
        font-weight: 600;
        font-size: 0.84rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .adj-btn-apply:hover {
        background: var(--adj-primary-hover);
        color: #ffffff;
        border-color: var(--adj-primary-hover);
    }
    .adj-btn-clear {
        background: #ffffff;
        color: var(--adj-text-muted);
        border: 1px solid var(--adj-border);
        height: 38px;
        padding: 0 14px;
        border-radius: var(--adj-radius-md);
        font-weight: 600;
        font-size: 0.84rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .adj-btn-clear:hover {
        background: #f1f5f9;
        color: var(--adj-text-main);
    }

    /* ── Main Data Table Card ── */
    .adj-table-card {
        background: var(--adj-card-bg);
        border: 1px solid var(--adj-border);
        border-radius: var(--adj-radius-lg);
        box-shadow: var(--adj-shadow-sm);
        overflow: hidden;
    }
    .adj-table-header {
        padding: 14px 22px;
        border-bottom: 1px solid var(--adj-border);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .adj-table-header h5 {
        margin: 0;
        font-size: 0.96rem;
        font-weight: 700;
        color: var(--adj-text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .adj-table-header .table-count {
        font-size: 0.75rem;
        color: var(--adj-text-muted);
        background: #f1f5f9;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Table styling */
    .adj-table-responsive {
        width: 100%;
        overflow-x: auto;
    }
    .adj-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.83rem;
        margin: 0;
    }
    .adj-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 16px;
        border-bottom: 2px solid var(--adj-border);
        white-space: nowrap;
        vertical-align: middle;
    }
    .adj-table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--adj-border-subtle);
        color: var(--adj-text-main);
        white-space: nowrap;
    }
    .adj-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .adj-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .adj-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Column Specific Styles */
    .adj-idx-cell {
        color: var(--adj-text-muted);
        font-weight: 600;
        font-size: 0.76rem;
        text-align: center;
        width: 45px;
    }
    .adj-date-time {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
    }
    .adj-date {
        font-weight: 600;
        color: var(--adj-text-main);
    }
    .adj-time {
        font-size: 0.72rem;
        color: var(--adj-text-muted);
        margin-top: 2px;
    }

    .adj-wh-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: var(--adj-radius-sm);
        padding: 3px 9px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .adj-prod-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .adj-prod-name {
        font-weight: 700;
        color: var(--adj-text-main);
        font-size: 0.85rem;
    }
    .adj-prod-code {
        font-family: monospace;
        font-size: 0.72rem;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1px 6px;
        width: fit-content;
    }

    .adj-variant-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--adj-info-light);
        color: var(--adj-info);
        border: 1px solid #bae6fd;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.74rem;
        font-weight: 700;
    }
    .adj-no-variant {
        color: #94a3b8;
        font-size: 0.76rem;
        font-style: italic;
    }

    /* Badges */
    .badge-type-add {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--adj-success-light);
        color: var(--adj-success);
        border: 1px solid #a7f3d0;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.74rem;
        font-weight: 700;
    }
    .badge-type-subtract {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--adj-danger-light);
        color: var(--adj-danger);
        border: 1px solid #fecaca;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.74rem;
        font-weight: 700;
    }
    .badge-type-set {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--adj-info-light);
        color: var(--adj-info);
        border: 1px solid #bae6fd;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.74rem;
        font-weight: 700;
    }

    /* Stock Flow Visual Pill */
    .adj-flow-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 0.78rem;
    }
    .flow-old {
        color: var(--adj-text-muted);
        text-decoration: line-through;
        font-weight: 600;
    }
    .flow-arrow {
        color: #94a3b8;
        font-size: 0.72rem;
    }
    .flow-new {
        color: var(--adj-text-main);
        font-weight: 800;
    }

    /* Pagination Bar */
    .adj-pagination-bar {
        padding: 14px 22px;
        background: #ffffff;
        border-top: 1px solid var(--adj-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .adj-showing-info {
        font-size: 0.8rem;
        color: var(--adj-text-muted);
    }
    .adj-showing-info strong {
        color: var(--adj-text-main);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .adj-filter-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 992px) {
        .adj-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .adj-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .adj-stats-grid {
            grid-template-columns: 1fr;
        }
        .adj-filter-grid {
            grid-template-columns: 1fr;
        }
        .adj-header-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="stock-adj-page">
            <div class="container-fluid px-3 px-md-4">

                {{-- ── 1. Top Header Banner ── --}}
                <div class="adj-header-card">
                    <div class="adj-header-title">
                        <div class="adj-header-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h4>Stock Adjustments Audit Log</h4>
                            <p>Complete historical log of all inventory corrections, damaged write-offs, and physical recount reconciliations.</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('stock_adjustments.create') }}" class="btn-create-adj">
                            <i class="fas fa-plus-circle"></i>
                            <span>New Stock Adjustment Terminal</span>
                        </a>
                    </div>
                </div>

                {{-- ── 2. Metric KPI Cards ── --}}
                <div class="adj-stats-grid">
                    
                    {{-- Total Adjustments --}}
                    <div class="adj-stat-box theme-primary">
                        <div class="adj-stat-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="adj-stat-meta">
                            <div class="stat-title">Total Adjustments</div>
                            <div class="stat-number">{{ number_format($totalAdjustments) }}</div>
                            <div class="stat-caption">Recorded in system</div>
                        </div>
                    </div>

                    {{-- Stock Added (+) --}}
                    <div class="adj-stat-box theme-success">
                        <div class="adj-stat-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="adj-stat-meta">
                            <div class="stat-title">Total Stock Added (+)</div>
                            <div class="stat-number text-success">+{{ number_format($totalAdded) }}</div>
                            <div class="stat-caption">Units / pieces added</div>
                        </div>
                    </div>

                    {{-- Stock Deducted (-) --}}
                    <div class="adj-stat-box theme-danger">
                        <div class="adj-stat-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="adj-stat-meta">
                            <div class="stat-title">Total Stock Deducted (-)</div>
                            <div class="stat-number text-danger">-{{ number_format($totalSubtracted) }}</div>
                            <div class="stat-caption">Units / pieces deducted</div>
                        </div>
                    </div>

                    {{-- This Month Activity --}}
                    <div class="adj-stat-box theme-warning">
                        <div class="adj-stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="adj-stat-meta">
                            <div class="stat-title">This Month</div>
                            <div class="stat-number">{{ number_format($thisMonthCount) }}</div>
                            <div class="stat-caption">Adjustments this month</div>
                        </div>
                    </div>

                </div>

                {{-- ── 3. Filters & Search Card ── --}}
                <div class="adj-filter-card">
                    <div class="adj-filter-header">
                        <div class="adj-filter-header-title">
                            <i class="fas fa-filter text-primary"></i>
                            <span>Filter &amp; Search Audit Logs</span>
                        </div>
                        @if(request()->anyFilled(['search', 'warehouse_id', 'type', 'date_from', 'date_to']))
                            <span class="badge bg-primary text-white" style="font-size: 0.72rem;">Filters Active</span>
                        @endif
                    </div>
                    <div class="adj-filter-body">
                        <form method="GET" action="{{ route('stock_adjustments.index') }}" id="filterForm">
                            <div class="adj-filter-grid">
                                
                                {{-- Search Query --}}
                                <div class="adj-filter-item">
                                    <label class="adj-filter-label">Search Product / Variant</label>
                                    <div class="adj-input-wrapper">
                                        <i class="fas fa-search adj-input-icon"></i>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            class="adj-form-control" placeholder="Search by item name, code, reason...">
                                    </div>
                                </div>

                                {{-- Warehouse Filter --}}
                                <div class="adj-filter-item">
                                    <label class="adj-filter-label">Warehouse</label>
                                    <select name="warehouse_id" class="adj-form-control">
                                        <option value="">All Permitted Warehouses</option>
                                        @foreach ($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                {{ $wh->name ?? $wh->warehouse_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Type Filter --}}
                                <div class="adj-filter-item">
                                    <label class="adj-filter-label">Adjustment Type</label>
                                    <select name="type" class="adj-form-control">
                                        <option value="">All Types</option>
                                        <option value="add"      {{ request('type') === 'add'      ? 'selected' : '' }}>🟢 Add (+)</option>
                                        <option value="subtract" {{ request('type') === 'subtract' ? 'selected' : '' }}>🔴 Deduct (-)</option>
                                        <option value="set"      {{ request('type') === 'set'      ? 'selected' : '' }}>🔵 Set (=)</option>
                                    </select>
                                </div>

                                {{-- Date From --}}
                                <div class="adj-filter-item">
                                    <label class="adj-filter-label">From Date</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="adj-form-control">
                                </div>

                                {{-- Date To --}}
                                <div class="adj-filter-item">
                                    <label class="adj-filter-label">To Date</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="adj-form-control">
                                </div>

                                {{-- Action Buttons --}}
                                <div class="adj-filter-item">
                                    <label class="adj-filter-label">&nbsp;</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="submit" class="adj-btn-apply">
                                            <i class="fas fa-check"></i>
                                            <span>Apply</span>
                                        </button>
                                        <a href="{{ route('stock_adjustments.index') }}" class="adj-btn-clear" title="Clear all filters">
                                            <i class="fas fa-undo"></i>
                                            <span>Reset</span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── 4. Main Audit Log Table ── --}}
                <div class="adj-table-card">
                    <div class="adj-table-header">
                        <h5>
                            <i class="fas fa-list-alt text-primary"></i>
                            <span>Adjustment Transactions</span>
                        </h5>
                        <span class="table-count">
                            {{ $adjustments->total() }} Records Found
                        </span>
                    </div>

                    <div class="adj-table-responsive">
                        <table class="adj-table">
                            <thead>
                                <tr>
                                    <th class="adj-idx-cell">#</th>
                                    <th>Date &amp; Time</th>
                                    <th>Adjusted By</th>
                                    <th>Warehouse</th>
                                    <th>Product Details</th>
                                    <th>Variant</th>
                                    <th class="text-center">Action Type</th>
                                    <th class="text-end">Adjusted Qty</th>
                                    <th class="text-center">Stock Change (Old &rarr; New)</th>
                                    <th>Reason / Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($adjustments as $key => $adj)
                                    <tr>
                                        {{-- Row Index --}}
                                        <td class="adj-idx-cell">{{ $adjustments->firstItem() + $key }}</td>

                                        {{-- Date & Time --}}
                                        <td>
                                            <div class="adj-date-time">
                                                <span class="adj-date">{{ $adj->created_at ? $adj->created_at->format('d M Y') : '-' }}</span>
                                                <span class="adj-time"><i class="far fa-clock me-1"></i>{{ $adj->created_at ? $adj->created_at->format('h:i A') : '' }}</span>
                                            </div>
                                        </td>

                                        {{-- Adjusted By User --}}
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user-circle text-muted" style="font-size: 1.1rem;"></i>
                                                <span class="fw-semibold">{{ $adj->user->name ?? 'System' }}</span>
                                            </div>
                                        </td>

                                        {{-- Warehouse --}}
                                        <td>
                                            <span class="adj-wh-badge">
                                                <i class="fas fa-warehouse text-primary" style="font-size: 0.7rem;"></i>
                                                {{ $adj->warehouse->name ?? $adj->warehouse->warehouse_name ?? 'Warehouse #'.$adj->warehouse_id }}
                                            </span>
                                        </td>

                                        {{-- Product --}}
                                        <td>
                                            <div class="adj-prod-info">
                                                <span class="adj-prod-name">{{ $adj->product->item_name ?? 'Product #'.$adj->product_id }}</span>
                                                @if(isset($adj->product->item_code) && $adj->product->item_code)
                                                    <span class="adj-prod-code">{{ $adj->product->item_code }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Variant --}}
                                        <td>
                                            @if($adj->variant_name)
                                                <span class="adj-variant-pill">
                                                    <i class="fas fa-tag"></i>
                                                    {{ $adj->variant_name }}
                                                </span>
                                            @else
                                                <span class="adj-no-variant">— Standard Item —</span>
                                            @endif
                                        </td>

                                        {{-- Type Badge --}}
                                        <td class="text-center">
                                            @if($adj->type === 'add')
                                                <span class="badge-type-add"><i class="fas fa-plus"></i> Add</span>
                                            @elseif($adj->type === 'subtract')
                                                <span class="badge-type-subtract"><i class="fas fa-minus"></i> Deduct</span>
                                            @else
                                                <span class="badge-type-set"><i class="fas fa-equals"></i> Set</span>
                                            @endif
                                        </td>

                                        {{-- Adjusted Qty --}}
                                        <td class="text-end">
                                            <strong class="font-monospace fs-6 {{ $adj->type === 'add' ? 'text-success' : ($adj->type === 'subtract' ? 'text-danger' : 'text-primary') }}">
                                                {{ $adj->type === 'add' ? '+' : ($adj->type === 'subtract' ? '-' : '') }}{{ number_format($adj->qty, 2) }}
                                            </strong>
                                        </td>

                                        {{-- Stock Flow --}}
                                        <td class="text-center">
                                            <div class="adj-flow-badge">
                                                <span class="flow-old">{{ number_format($adj->old_stock, 0) }}</span>
                                                <i class="fas fa-arrow-right flow-arrow"></i>
                                                <span class="flow-new">{{ number_format($adj->new_stock, 0) }}</span>
                                            </div>
                                        </td>

                                        {{-- Reason --}}
                                        <td style="max-width: 260px; white-space: normal;">
                                            @if($adj->reason)
                                                <span class="text-secondary small">{{ $adj->reason }}</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                                <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                                                    <i class="fas fa-clipboard-list fa-2x text-muted" style="opacity: 0.5;"></i>
                                                </div>
                                                <h6 class="fw-bold mb-1 text-dark">No Stock Adjustments Found</h6>
                                                <p class="small text-muted mb-3">No adjustments match your current filters or none have been recorded yet.</p>
                                                <a href="{{ route('stock_adjustments.create') }}" class="btn btn-primary btn-sm px-3" style="border-radius: 8px;">
                                                    <i class="fas fa-plus-circle me-1"></i> Make Stock Adjustment
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ── 5. Pagination ── --}}
                    @if($adjustments->hasPages() || $adjustments->total() > 0)
                        <div class="adj-pagination-bar">
                            <div class="adj-showing-info">
                                Showing <strong>{{ $adjustments->firstItem() ?? 0 }}</strong> to <strong>{{ $adjustments->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($adjustments->total()) }}</strong> entries
                            </div>
                            <div>
                                {{ $adjustments->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif

                </div>

            </div>{{-- /container-fluid --}}
        </div>{{-- /stock-adj-page --}}
    </div>{{-- /main-content-inner --}}
</div>{{-- /main-content --}}

@endsection
