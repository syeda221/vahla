@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid">

                <div class="page-header row">
                    <div class="page-title col-lg-6">
                        <h4>Warehouse List</h4>
                        <h6>Manage Warehouses</h6>
                    </div>
                    <div class="page-btn d-flex justify-content-end col-lg-6">
                        @can('warehouse.create')
                            <button class="btn btn-outline-primary mb-2" data-toggle="modal" data-target="#warehouseModal" data-bs-toggle="modal" data-bs-target="#warehouseModal"
                                onclick="clearWarehouse()">Add Warehouse</button>
                        @endcan
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        {{-- @if (session()->has('success'))
                            <div class="alert alert-success"><strong>Success!</strong> {{ session('success') }}</div>
                        @endif --}}

                        <table class="table datanew">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Current Stock</th>
                                    <th>Location</th>
                                    <th>Remarks</th>
                                    <th>Created By</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouses as $key => $w)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $w->warehouse_name }}</div>
                                            @if($w->id == 1)
                                                <small class="badge bg-light text-primary border">Primary Store</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($w->is_shop)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                                    <i class="fas fa-store me-1"></i> Shop / Retail Counter
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fw-bold">
                                                    <i class="fas fa-warehouse me-1"></i> Storage Godown
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('warehouse_stocks.index', ['warehouse_id' => $w->id]) }}" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-decoration-none fw-bold" title="Click to view full inventory">
                                                <i class="fas fa-boxes-stacked me-1"></i> {{ number_format($w->warehouse_stocks_sum_total_pieces ?? 0) }} Pcs ({{ $w->warehouse_stocks_count ?? 0 }} items)
                                            </a>
                                        </td>
                                        <td>{{ $w->location ?: '—' }}</td>
                                        <td>{{ $w->remarks ?: '—' }}</td>
                                        <td>{{ optional($w->user)->name ?: 'System' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('warehouse_stocks.index', ['warehouse_id' => $w->id]) }}" class="btn btn-outline-info btn-sm me-1" title="View Inventory">
                                                <i class="fas fa-eye me-1"></i> Stock
                                            </a>
                                            @can('warehouse.edit')
                                                <button type="button" class="btn btn-primary btn-sm edit-warehouse-btn"
                                                    data-id="{{ $w->id }}" data-name="{{ $w->warehouse_name }}"
                                                    data-location="{{ $w->location }}" data-remarks="{{ $w->remarks }}"
                                                    data-is-shop="{{ $w->is_shop ? '1' : '0' }}"
                                                    data-toggle="modal" data-target="#warehouseModal"
                                                    data-bs-toggle="modal" data-bs-target="#warehouseModal">
                                                    Edit
                                                </button>
                                            @endcan
                                            @can('warehouse.delete')
                                                @if($w->id != 1)
                                                    <button class="btn btn-danger btn-sm delete-btn"
                                                        data-url="{{ url('warehouse/delete/' . $w->id) }}"
                                                        data-msg="Are you sure you want to delete this warehouse?" data-method="get"
                                                        onclick="logoutAndDeleteFunction(this)">
                                                        Delete
                                                    </button>
                                                @endif
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

    <div class="modal fade" id="warehouseModal" tabindex="-1" role="dialog" aria-labelledby="warehouseModalTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ url('warehouse/store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="warehouse_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="warehouseModalTitle">Add/Edit Warehouse</h5>
                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Warehouse / Store Name <span class="text-danger">*</span></label>
                            <input class="form-control fw-semibold" name="warehouse_name" id="warehouse_name"
                                placeholder="e.g. Main Store, Godown A" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Location Type</label>
                            <select class="form-select fw-semibold" name="is_shop" id="is_shop">
                                <option value="0">🏢 Storage Godown / Warehouse (Bulk Stock)</option>
                                <option value="1">🏪 Shop / Retail Counter (Counter Sales)</option>
                            </select>
                            <small class="text-muted d-block mt-1">Shop type designates this location as your primary counter store for sales.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Address / Location</label>
                            <input class="form-control" name="location" id="location"
                                placeholder="Physical Address or City">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        @canany(['warehouse.create', 'warehouse.edit'])
                            <button type="submit" class="btn btn-primary" id="warehouseSubmitBtn">Save</button>
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
    <script>
        function showWarehouseModal() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var el = document.getElementById('warehouseModal');
                var modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                modal.show();
            } else if ($.fn.modal) {
                $('#warehouseModal').modal('show');
            }
        }

        function clearWarehouse() {
            $('#warehouseModalTitle').text('Add New Warehouse');
            $('#warehouseSubmitBtn').text('Save Warehouse');
            $('#warehouse_id').val('');
            $('#warehouse_name').val('');
            $('#is_shop').val('0');
            $('#location').val('');
            $('#remarks').val('');
            showWarehouseModal();
        }

        // Handle Edit button click
        $(document).on('click', '.edit-warehouse-btn', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var isShop = $(this).attr('data-is-shop');
            var location = $(this).attr('data-location') || '';
            var remarks = $(this).attr('data-remarks') || '';

            $('#warehouseModalTitle').text('Edit Warehouse: ' + name);
            $('#warehouseSubmitBtn').text('Update Warehouse');
            $('#warehouse_id').val(id);
            $('#warehouse_name').val(name);
            $('#is_shop').val(isShop == '1' ? '1' : '0');
            $('#location').val(location);
            $('#remarks').val(remarks);

            showWarehouseModal();
        });

        $(document).ready(function() {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable();
            }
        });
    </script>
@endsection
