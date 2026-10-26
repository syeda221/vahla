@extends('admin_panel.layout.app')
@section('content')

    <style>
        /* Desktop Default (min-width: 992px) */
        @media (min-width: 992px) {
            #mobileWarehouseCardsWrapper,
            .mobile-view-toggle {
                display: none !important;
            }
            #desktopTableWrapper {
                display: block !important;
            }
        }

        /* Mobile Card Styling */
        .warehouse-mcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            width: 100%;
            box-sizing: border-box;
            overflow: hidden;
            word-break: break-word;
        }
        .warehouse-mcard-header {
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .warehouse-search-box {
            position: relative;
            margin-bottom: 12px;
        }
        .warehouse-search-box input {
            border-radius: 8px;
            padding-left: 36px;
            font-size: 0.88rem;
            height: 38px;
            border: 1px solid #cbd5e1;
            width: 100%;
            box-sizing: border-box;
        }
        .warehouse-search-box i {
            position: absolute;
            left: 12px;
            top: 11px;
            color: #94a3b8;
            pointer-events: none;
        }

        /* Tablet & Mobile (max-width: 991.98px) */
        @media (max-width: 991.98px) {
            html, body {
                max-width: 100vw !important;
                width: 100% !important;
                overflow-x: hidden !important;
                position: relative;
            }
            .main-content,
            .main-content-inner,
            .container-fluid {
                max-width: 100vw !important;
                width: 100% !important;
                overflow-x: hidden !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
                box-sizing: border-box !important;
            }
            .page-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .page-header .page-btn {
                justify-content: stretch !important;
                width: 100% !important;
            }
            .page-header .page-btn button {
                width: 100% !important;
                justify-content: center !important;
            }
            .card {
                border-radius: 14px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }
            .card-body {
                padding: 12px !important;
            }
            #desktopTableWrapper.mobile-table-hidden {
                display: none !important;
            }
            #desktopTableWrapper {
                width: 100% !important;
                max-width: 100% !important;
                overflow: hidden !important;
            }
            #desktopTableWrapper .table-responsive {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .table.datanew {
                min-width: 550px !important;
                white-space: nowrap !important;
            }
            .modal-dialog {
                margin: 10px !important;
                max-width: calc(100vw - 20px) !important;
            }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-3 py-md-4 px-2 px-md-4">

                <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div class="page-title">
                        <h4 class="fw-bold mb-0" style="color: #0b5a2b; font-size: 22px;">Warehouse List</h4>
                        <h6 class="text-muted mb-0 small">Manage Warehouses</h6>
                    </div>
                    <div class="page-btn d-flex justify-content-end">
                        @can('warehouse.create')
                            <button class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center" data-toggle="modal" data-target="#warehouseModal"
                                data-bs-toggle="modal" data-bs-target="#warehouseModal"
                                onclick="clearWarehouse()" style="height: 38px; border-radius: 6px;">
                                <i class="fas fa-plus mr-1" style="margin-right: 5px;"></i> Add Warehouse
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-body">

                        {{-- Mobile View Switcher (Cards vs Table) --}}
                        <div class="d-flex justify-content-between align-items-center mobile-view-toggle pb-3 mb-2 border-bottom pt-1">
                            <span class="small text-muted font-weight-bold">
                                <i class="fas fa-th-list mr-1" style="margin-right: 4px;"></i> View Mode
                            </span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnShowCards">
                                    <i class="fas fa-th-large mr-1" style="margin-right: 4px;"></i> Cards
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnShowTable">
                                    <i class="fas fa-table mr-1" style="margin-right: 4px;"></i> Table
                                </button>
                            </div>
                        </div>

                        {{-- Mobile Cards View --}}
                        <div id="mobileWarehouseCardsWrapper">
                            <div class="warehouse-search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="mobileWarehouseSearch" class="form-control" placeholder="Search warehouses...">
                            </div>

                            <div class="mobile-warehouse-list">
                                @forelse ($warehouses as $key => $w)
                                    <div class="warehouse-mcard" data-search="{{ strtolower(($key + 1) . ' ' . $w->warehouse_name . ' ' . ($w->location ?? '') . ' ' . ($w->remarks ?? '') . ' ' . ($w->user->name ?? '')) }}">
                                        <div class="warehouse-mcard-header d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 6px; min-width: 0; max-width: 80%;">
                                                <span class="badge bg-dark text-white font-weight-bold">#{{ $key + 1 }}</span>
                                                <span class="fw-bold text-dark" style="font-size: 15px;">{{ $w->warehouse_name }}</span>
                                            </div>
                                            <div>
                                                @if(!empty($w->user->name))
                                                    <span class="badge bg-light text-secondary border" style="font-size: 10px;">{{ $w->user->name }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="warehouse-mcard-body pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="text-muted small">Location:</span>
                                                <span class="text-dark small fw-semibold">
                                                    @if(!empty($w->location))
                                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $w->location }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </span>
                                            </div>

                                            @if(!empty($w->remarks))
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="text-muted small me-2 flex-shrink-0">Remarks:</span>
                                                    <span class="text-dark small text-end">{{ $w->remarks }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="warehouse-mcard-footer d-flex justify-content-end align-items-center mt-2 pt-2 border-top" style="gap: 6px;">
                                            @can('warehouse.edit')
                                                <button class="btn btn-primary btn-sm edit-warehouse-btn"
                                                    data-id="{{ $w->id }}" data-name="{{ $w->warehouse_name }}"
                                                    data-location="{{ $w->location }}" data-remarks="{{ $w->remarks }}"
                                                    data-toggle="modal" data-target="#warehouseModal"
                                                    data-bs-toggle="modal" data-bs-target="#warehouseModal">
                                                    Edit
                                                </button>
                                            @endcan
                                            @can('warehouse.delete')
                                                <button class="btn btn-danger btn-sm delete-btn"
                                                    data-url="{{ url('warehouse/delete/' . $w->id) }}"
                                                    data-msg="Are you sure you want to delete this warehouse?" data-method="get"
                                                    onclick="logoutAndDeleteFunction(this)">
                                                    Delete
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-warehouse fs-2 d-block mb-1 text-muted"></i>
                                        <p class="mb-0">No warehouses found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Desktop Table View --}}
                        <div id="desktopTableWrapper" class="mobile-table-hidden">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Created By</th>
                                            <th>Name</th>
                                            <th>Location</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($warehouses as $key => $w)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $w->user->name ?? 'N/A' }}</td>
                                                <td>{{ $w->warehouse_name }}</td>
                                                <td>{{ $w->location }}</td>
                                                <td>{{ $w->remarks }}</td>
                                                <td>
                                                    @can('warehouse.edit')
                                                        <button class="btn btn-primary btn-sm edit-warehouse-btn"
                                                            data-id="{{ $w->id }}" data-name="{{ $w->warehouse_name }}"
                                                            data-location="{{ $w->location }}" data-remarks="{{ $w->remarks }}"
                                                            data-toggle="modal" data-target="#warehouseModal"
                                                            data-bs-toggle="modal" data-bs-target="#warehouseModal">
                                                            Edit
                                                        </button>
                                                    @endcan
                                                    @can('warehouse.delete')
                                                        <button class="btn btn-danger btn-sm delete-btn"
                                                            data-url="{{ url('warehouse/delete/' . $w->id) }}"
                                                            data-msg="Are you sure you want to delete this warehouse?" data-method="get"
                                                            onclick="logoutAndDeleteFunction(this)">
                                                            Delete
                                                        </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Create/Edit Warehouse Modal -->
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="warehouseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ url('warehouse/store') }}" method="POST" style="width: 100%;">
                @csrf
                <input type="hidden" name="id" id="warehouse_id">
                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold" id="warehouseModalLabel">Add/Edit Warehouse</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-3 p-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warehouse Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="warehouse_name" id="warehouse_name"
                                placeholder="Warehouse Name" required>
                        </div>
                        <div class="mb-2 d-none">
                            <input class="form-control" name="creater_id" id="creater_id"
                                value="{{ Auth()->user()->id }}" placeholder="Creator">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Location / Address</label>
                            <input class="form-control" name="location" id="location"
                                placeholder="e.g. Lahore Branch, Main Hub">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-3 py-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @canany(['warehouse.create', 'warehouse.edit'])
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        @endcanany
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
    <script>
        function clearWarehouse() {
            $('#warehouse_id').val('');
            $('#warehouse_name').val('');
            $('#location').val('');
            $('#remarks').val('');
            $('#warehouseModalLabel').text('Add Warehouse');
            $('#warehouseModal').modal('show');
        }

        // Handle Edit button click (works for both Table and Mobile Cards)
        $(document).on('click', '.edit-warehouse-btn', function() {
            $('#warehouse_id').val($(this).data('id'));
            $('#warehouse_name').val($(this).data('name'));
            $('#location').val($(this).data('location'));
            $('#remarks').val($(this).data('remarks'));
            $('#warehouseModalLabel').text('Edit Warehouse');
            $('#warehouseModal').modal('show');
        });

        // Mobile View Switcher (Cards vs Table)
        $('#btnShowCards').on('click', function() {
            $(this).addClass('active');
            $('#btnShowTable').removeClass('active');
            $('#mobileWarehouseCardsWrapper').show();
            $('#desktopTableWrapper').addClass('mobile-table-hidden').hide();
        });

        $('#btnShowTable').on('click', function() {
            $(this).addClass('active');
            $('#btnShowCards').removeClass('active');
            $('#mobileWarehouseCardsWrapper').hide();
            $('#desktopTableWrapper').removeClass('mobile-table-hidden').show();
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().columns.adjust();
            }
        });

        // Mobile Live Search Filter
        $('#mobileWarehouseSearch').on('keyup input', function() {
            var val = $(this).val().toLowerCase().trim();
            $('.mobile-warehouse-list .warehouse-mcard').each(function() {
                var searchTxt = $(this).attr('data-search') || '';
                $(this).toggle(searchTxt.indexOf(val) > -1);
            });
        });

        // Initialize DataTable
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }
            $('.datanew').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50, 100],
                "language": {
                    "search": "Filter warehouses:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        });
    </script>
@endsection
