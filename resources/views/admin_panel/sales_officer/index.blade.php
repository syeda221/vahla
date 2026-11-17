@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>Sales Officers</h3>
                            @can('sales.officers.create')
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal"
                                    data-bs-toggle="modal" data-bs-target="#createModal" id="reset">
                                    Create
                                </button>
                            @endcan
                        </div>

                        <div class="border mt-1 shadow rounded" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive mt-5 mb-5">
                                    <table id="default-datatable" class="table">
                                        <thead class="text-center">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Name Urdu</th>
                                                <th>Mobile</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($officers as $officer)
                                                <tr id="row-{{ $officer->id }}">
                                                    <td>{{ $officer->id }}</td>
                                                    <td>{{ $officer->name }}</td>
                                                    <td>{{ $officer->name_urdu }}</td>
                                                    <td>{{ $officer->mobile }}</td>
                                                    <td>
                                                        @include('admin_panel.partials.action_buttons', [
                                                            'editRoute' => 'javascript:void(0)',
                                                            'deleteRoute' => '/sales-officers/' . $officer->id,
                                                            'editIsLink' => false,
                                                            'permissions' => [
                                                                'edit' => 'sales.officers.edit',
                                                                'delete' => 'sales.officers.delete',
                                                            ],
                                                            'dataId' => $officer->id,
                                                        ])
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
            <form class="create-form" action="{{ route('sales-officer.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Sales Officer</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نام <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="name_urdu" class="form-control text-end" dir="rtl" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @can('sales.officers.create')
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
            <form class="edit-form" action="{{ route('sales-officer.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Sales Officer</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نام <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="name_urdu" id="edit_name_urdu" class="form-control text-end"
                                dir="rtl" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" id="edit_mobile" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @can('sales.officers.edit')
                            <input type="submit" class="btn btn-primary" value="Update">
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if ($('#default-datatable').length) {
                $('#default-datatable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'desc']
                    ]
                });
            }

            // Explicit click handler on #reset so modal opens reliably
            $(document).on('click', '#reset', function() {
                $('.create-form')[0].reset();
                $('#createModal').modal('show');
            });

            // CREATE
            $(document).on('submit', '.create-form', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        $('#createModal').modal('hide');
                        Swal.fire('Success', 'Sales Officer Created', 'success').then(() =>
                            location.reload());
                    },
                    error: function(xhr) {
                        let msg = 'Failed to create sales officer.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // LOAD EDIT DATA
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('sales-officers/edit') }}/" + id, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_name_urdu').val(data.name_urdu);
                    $('#edit_mobile').val(data.mobile);
                    $('#editModal').modal('show');
                });
            });

            // UPDATE
            $(document).on('submit', '.edit-form', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        $('#editModal').modal('hide');
                        Swal.fire('Updated', 'Sales Officer Updated', 'success').then(() =>
                            location.reload());
                    },
                    error: function(xhr) {
                        let msg = 'Failed to update sales officer.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // DELETE
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let url = `/sales-officers/${id}`; // dynamically build route
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You can't undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#row-' + id).remove();
                                Swal.fire('Deleted!', 'Sales Officer has been deleted.',
                                    'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Delete failed. Please try again.',
                                    'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
