@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-1">Agents</h3>
                                <small class="text-muted">Manage third-party agents and their commission ledgers</small>
                            </div>
                            @can('agents.create')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <i class="fas fa-user-plus me-1"></i> Add Agent
                                </button>
                            @endcan
                        </div>

                        <!-- Summary Cards -->
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6 col-lg">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Total Agents</div>
                                        <div class="fs-4 fw-bold text-primary">{{ $stats['total_agents'] }}
                                            <span class="fs-6 text-muted fw-normal">({{ $stats['active_agents'] }} active)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Total Commission</div>
                                        <div class="fs-4 fw-bold text-dark">Rs {{ number_format($stats['total_commission'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Paid Commission</div>
                                        <div class="fs-4 fw-bold text-success">Rs {{ number_format($stats['paid_commission'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-3">
                                        <div class="text-muted small fw-bold text-uppercase">Outstanding Commission</div>
                                        <div class="fs-4 fw-bold text-danger">Rs {{ number_format($stats['outstanding_commission'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border mt-1 shadow rounded" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive mt-4 mb-4">
                                    <table id="default-datatable" class="table">
                                        <thead class="text-center">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Address</th>
                                                <th>Status</th>
                                                <th>Total Commission</th>
                                                <th>Paid</th>
                                                <th>Outstanding</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($agents as $agent)
                                                <tr id="row-{{ $agent->id }}">
                                                    <td>{{ $agent->id }}</td>
                                                    <td class="text-start fw-bold">{{ $agent->name }}</td>
                                                    <td>{{ $agent->contact_number }}</td>
                                                    <td>{{ $agent->address ?: '—' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $agent->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($agent->status) }}</span>
                                                    </td>
                                                    <td class="fw-bold">Rs {{ number_format($agent->total_commission, 2) }}</td>
                                                    <td class="text-success fw-bold">Rs {{ number_format($agent->paid_commission, 2) }}</td>
                                                    <td class="{{ $agent->outstanding > 0 ? 'text-danger fw-bold' : 'text-success' }}">Rs {{ number_format($agent->outstanding, 2) }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('agents.ledger', $agent->id) }}" class="btn btn-outline-primary" title="Ledger"><i class="fas fa-book"></i></a>
                                                            @can('agents.edit')
                                                                <button class="btn btn-outline-secondary edit-btn" data-id="{{ $agent->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                                                <button class="btn btn-outline-warning toggle-status-btn" data-id="{{ $agent->id }}" data-status="{{ $agent->status }}" title="Toggle Status"><i class="fas fa-power-off"></i></button>
                                                            @endcan
                                                            @can('agents.delete')
                                                                <button class="btn btn-outline-danger delete-btn" data-id="{{ $agent->id }}" title="Delete"><i class="fas fa-trash"></i></button>
                                                            @endcan
                                                        </div>
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
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="agent-form" action="{{ route('agents.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Agent Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address <small class="text-muted">(optional)</small></label>
                            <input type="text" name="address" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @can('agents.create')
                            <input type="submit" class="btn btn-primary" value="Save">
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="agent-form" action="{{ route('agents.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Agent Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_contact_number" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" id="edit_address" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @can('agents.edit')
                            <input type="submit" class="btn btn-primary" value="Update">
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#default-datatable').DataTable({
                pageLength: 15,
                order: [
                    [0, 'desc']
                ]
            });

            // CREATE / UPDATE
            $('.agent-form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#createModal').modal('hide');
                        $('#editModal').modal('hide');
                        Swal.fire('Success', response.message, 'success').then(() =>
                            location.reload());
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Something went wrong';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // LOAD EDIT DATA
            $('.edit-btn').click(function() {
                let id = $(this).data('id');
                $.get("{{ url('agents/edit') }}/" + id, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_contact_number').val(data.contact_number);
                    $('#edit_address').val(data.address || '');
                    $('#edit_status').val(data.status);
                    $('#editModal').modal('show');
                });
            });

            // TOGGLE STATUS
            $('.toggle-status-btn').click(function() {
                let id = $(this).data('id');
                $.post("{{ url('agents/toggle-status') }}/" + id, {
                    _token: '{{ csrf_token() }}'
                }, function() {
                    location.reload();
                });
            });

            // DELETE
            $('.delete-btn').click(function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You can't undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/agents/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#row-' + id).remove();
                                Swal.fire('Deleted!', 'Agent has been deleted.',
                                    'success');
                            },
                            error: function(xhr) {
                                let msg = xhr.responseJSON?.message ||
                                    'Delete failed. Please try again.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection