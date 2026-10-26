@extends('admin_panel.layout.app')
@section('content')

    <style>
        /* Zero horizontal scroll guarantee */
        html, body {
            max-width: 100vw;
            overflow-x: hidden;
        }
        .container-fluid, .card {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Desktop View (min-width: 992px) */
        @media (min-width: 992px) {
            #mobileTransferCardsWrapper,
            .mobile-view-toggle {
                display: none !important;
            }
            #desktopTableWrapper {
                display: block !important;
            }
        }

        /* Mobile View (max-width: 991.98px) */
        @media (max-width: 991.98px) {
            .card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
                padding: 14px 16px !important;
            }
            .card-header a.btn {
                width: 100% !important;
                justify-content: center !important;
                height: 38px !important;
                display: flex !important;
                align-items: center !important;
            }
            .card-body {
                padding: 12px !important;
            }

            /* View Toggle */
            .mobile-view-toggle {
                display: flex !important;
                background: #f1f5f9;
                padding: 3px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                margin-bottom: 12px;
            }
            .mobile-view-btn {
                flex: 1;
                border: none;
                background: transparent;
                padding: 6px 12px;
                font-size: 0.80rem;
                font-weight: 700;
                color: #64748b;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.15s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }
            .mobile-view-btn.active {
                background: #ffffff;
                color: #0d6efd;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }

            #desktopTableWrapper.mobile-table-hidden {
                display: none !important;
            }
            #mobileTransferCardsWrapper.mobile-cards-hidden {
                display: none !important;
            }

            /* DataTables controls responsive */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                margin-bottom: 10px !important;
                width: 100% !important;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: 4px !important;
            }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                text-align: center !important;
                margin-top: 10px !important;
                width: 100% !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 4px 8px !important;
                font-size: 0.80rem !important;
            }
        }

        /* Mobile Card Styling */
        .transfer-mcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: border-color 0.15s ease;
        }
        .transfer-mcard:hover {
            border-color: #cbd5e1;
        }
        .transfer-mcard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 10px;
        }
        .transfer-route-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }
        .transfer-search-box {
            position: relative;
            margin-bottom: 12px;
        }
        .transfer-search-box input {
            border-radius: 8px;
            padding-left: 36px;
            font-size: 0.88rem;
            height: 38px;
            border: 1px solid #cbd5e1;
            width: 100%;
            box-sizing: border-box;
        }
        .transfer-search-box i {
            position: absolute;
            left: 12px;
            top: 11px;
            color: #94a3b8;
            pointer-events: none;
        }
    </style>

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fas fa-exchange-alt text-primary me-2"></i>Stock Transfer List</h5>
            @can('stock.transfer.create')
                <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> New Transfer
                </a>
            @endcan
        </div>
        @if (session('error'))
            <div class="alert alert-danger m-3 mb-0">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
        @endif
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger m-3 mb-0">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-body">
            <!-- Mobile View Switcher -->
            <div class="mobile-view-toggle">
                <button type="button" class="mobile-view-btn active" id="btnTransferCards" onclick="switchTransferView('cards')">
                    <i class="fas fa-th-large"></i> Cards
                </button>
                <button type="button" class="mobile-view-btn" id="btnTransferTable" onclick="switchTransferView('table')">
                    <i class="fas fa-table"></i> Table
                </button>
            </div>

            <!-- Mobile Cards Container (Default on mobile) -->
            <div id="mobileTransferCardsWrapper">
                <!-- Search Box for Mobile Cards -->
                <div class="transfer-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="mobileTransferSearchInput" placeholder="Search product, warehouse, remarks..." onkeyup="filterTransferCards()">
                </div>

                <div id="transferCardsList">
                    @forelse ($transfers as $transfer)
                        <div class="transfer-mcard transfer-card-item" 
                             data-search="{{ strtolower(($transfer->fromWarehouse?->warehouse_name ?? '') . ' ' . ($transfer->to_shop ? 'shop' : ($transfer->toWarehouse?->warehouse_name ?? '')) . ' ' . ($transfer->product?->item_name ?? '') . ' ' . ($transfer->remarks ?? '')) }}">
                            
                            <!-- Header with # badge & destination tag -->
                            <div class="transfer-mcard-header">
                                <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-size: 0.78rem;">
                                    #{{ $loop->iteration }}
                                </span>
                                @if ($transfer->to_shop)
                                    <span class="badge bg-info text-white px-2.5 py-1">
                                        <i class="fas fa-store me-1"></i>To Shop
                                    </span>
                                @else
                                    <span class="badge bg-primary text-white px-2.5 py-1">
                                        <i class="fas fa-warehouse me-1"></i>Warehouse Transfer
                                    </span>
                                @endif
                            </div>

                            <!-- Route Visual Box -->
                            <div class="transfer-route-box">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div style="flex: 1;">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">From Source</small>
                                        <div class="fw-bold text-dark mt-0.5" style="font-size: 0.88rem;">
                                            <i class="fas fa-warehouse text-secondary me-1"></i>{{ $transfer->fromWarehouse?->warehouse_name ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="px-2 text-primary" style="font-size: 1rem;">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div style="flex: 1; text-align: right;">
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">To Destination</small>
                                        <div class="fw-bold text-dark mt-0.5" style="font-size: 0.88rem;">
                                            @if ($transfer->to_shop)
                                                <span class="text-info fw-bold"><i class="fas fa-store me-1"></i>Shop</span>
                                            @else
                                                <i class="fas fa-warehouse text-primary me-1"></i>{{ $transfer->toWarehouse?->warehouse_name ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product & Quantity -->
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div>
                                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.68rem;">Product</small>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">
                                        <i class="fas fa-box text-primary me-1"></i>{{ $transfer->product?->item_name ?? '-' }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.68rem;">Quantity</small>
                                    <span class="badge bg-primary text-white fs-6 px-2.5 py-1">
                                        {{ $transfer->quantity }}
                                    </span>
                                </div>
                            </div>

                            <!-- Remarks -->
                            @if($transfer->remarks)
                                <div class="small text-muted bg-light p-2 rounded border mt-2">
                                    <i class="fas fa-sticky-note me-1 text-secondary"></i><b>Remarks:</b> {{ $transfer->remarks }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-exchange-alt fa-2x mb-2 text-secondary opacity-50"></i>
                            <h6>No Stock Transfers recorded yet</h6>
                        </div>
                    @endforelse
                </div>

                <div id="noCardsMatchNotice" class="text-center py-4 text-muted" style="display: none;">
                    <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                    <h6>No matching transfers found</h6>
                </div>
            </div>

            <!-- Desktop Table Wrapper (Active on desktop, toggleable on mobile) -->
            <div id="desktopTableWrapper" class="mobile-table-hidden">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0" id="transferTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>From Warehouse</th>
                                <th>To Warehouse / Shop</th>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfers as $transfer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transfer->fromWarehouse?->warehouse_name ?? '-' }}</td>
                                    <td>
                                        @if ($transfer->to_shop)
                                            <span class="badge badge-info bg-info text-white">Shop</span>
                                        @else
                                            {{ $transfer->toWarehouse?->warehouse_name ?? '-' }}
                                        @endif
                                    </td>
                                    <td>{{ $transfer->product?->item_name ?? '-' }}</td>
                                    <td class="text-center fw-bold">{{ $transfer->quantity }}</td>
                                    <td>{{ $transfer->remarks ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#transferTable').DataTable({
                responsive: false,
                pageLength: 25,
                autoWidth: false
            });
        });

        function switchTransferView(mode) {
            if (mode === 'cards') {
                $('#btnTransferCards').addClass('active');
                $('#btnTransferTable').removeClass('active');
                $('#mobileTransferCardsWrapper').removeClass('mobile-cards-hidden');
                $('#desktopTableWrapper').addClass('mobile-table-hidden');
            } else {
                $('#btnTransferTable').addClass('active');
                $('#btnTransferCards').removeClass('active');
                $('#desktopTableWrapper').removeClass('mobile-table-hidden');
                $('#mobileTransferCardsWrapper').addClass('mobile-cards-hidden');
            }
        }

        function filterTransferCards() {
            const query = $('#mobileTransferSearchInput').val().toLowerCase().trim();
            let visibleCount = 0;

            $('.transfer-card-item').each(function() {
                const searchData = $(this).attr('data-search') || '';
                if (!query || searchData.indexOf(query) !== -1) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCount === 0 && query.length > 0) {
                $('#noCardsMatchNotice').show();
            } else {
                $('#noCardsMatchNotice').hide();
            }
        }
    </script>
@endsection
