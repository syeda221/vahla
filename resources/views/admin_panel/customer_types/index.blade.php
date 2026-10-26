@extends('admin_panel.layout.app')
@section('content')

@if (session('success'))
    <script>
    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#4f46e5'
        });
    });
    </script>
@endif

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .customertype-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Page Header */
    .customertype-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .customertype-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .customertype-sub {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* Stat Cards */
    .customertype-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .customertype-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .customertype-stat-val {
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        line-height: 1.2;
    }
    .customertype-stat-lbl {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Primary Gradient Button */
    .btn-customertype-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        padding: 10px 20px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.22);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-customertype-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }

    /* Card & Table */
    .customertype-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        overflow: hidden;
        max-width: 100%;
    }
    .customertype-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .customertype-table-wrap {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    #default-datatable {
        width: 100% !important;
        margin-bottom: 0 !important;
        min-width: 600px;
    }
    #default-datatable thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
        white-space: nowrap;
    }
    #default-datatable tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    #default-datatable tbody tr:hover {
        background-color: #f8fafc;
    }

    /* ID Badge */
    .customertype-id-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: monospace;
    }

    /* Mobile Cards View */
    .mobile-customertype-cards {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        box-sizing: border-box;
    }
    .customertype-mcard {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        width: 100%;
        box-sizing: border-box;
    }
    .customertype-mcard-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .customertype-mcard-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
    }
    .customertype-mcard-desc {
        font-size: 0.82rem;
        color: #64748b;
    }
    .customertype-mcard-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
        justify-content: flex-end;
    }

    /* Desktop View Constraints */
    @media (min-width: 992px) {
        #mobileCardsWrapper {
            display: none !important;
        }
        #desktopTableWrapper {
            display: block !important;
        }
        .mobile-view-toggle {
            display: none !important;
        }
    }

    /* Tablet & Mobile Responsive (<= 991px) */
    @media (max-width: 991.98px) {
        html, body {
            max-width: 100% !important;
            overflow-x: hidden !important;
        }
        .main-content,
        .main-content-inner,
        .container-fluid {
            max-width: 100% !important;
            overflow-x: hidden !important;
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
        .customertype-page {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .customertype-header {
            padding: 16px !important;
            border-radius: 12px !important;
            margin-bottom: 14px !important;
        }
        .customertype-title {
            font-size: 1.18rem !important;
        }
        .btn-customertype-primary {
            width: 100%;
            justify-content: center;
            height: 42px;
            margin-top: 10px;
        }
        .customertype-card {
            border-radius: 12px !important;
        }
        .customertype-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
            padding: 12px 14px;
        }
        .mobile-table-hidden {
            display: none !important;
        }
        /* DataTables responsive adjustments */
        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter {
            text-align: left !important;
            margin-bottom: 8px;
            padding: 0 14px;
        }
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: 4px;
        }
        div.dataTables_wrapper div.dataTables_info,
        div.dataTables_wrapper div.dataTables_paginate {
            text-align: left !important;
            padding: 8px 14px;
        }
    }
</style>

<div class="customertype-page container-fluid px-2 px-md-4 pt-2 pt-md-3">
    
    {{-- Header Row --}}
    <div class="customertype-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <div>
            <h3 class="customertype-title"><i class="fas fa-tags text-primary mr-2"></i>Customer Type Management</h3>
            <div class="customertype-sub">Manage customer groups, credits, and transaction categories</div>
        </div>
        @can('customer_types.create')
            <button type="button" class="btn-customertype-primary" data-toggle="modal" data-target="#customerTypeModal" id="reset">
                <i class="fas fa-plus mr-1"></i> Create Customer Type
            </button>
        @endcan
    </div>

    {{-- Stats Row --}}
    <div class="row mb-3 mb-md-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="customertype-stat-card">
                <div class="customertype-stat-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="customertype-stat-val">{{ count($types) }}</div>
                    <div class="customertype-stat-lbl">Total Customer Types</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="customertype-card">
        <div class="customertype-card-header">
            <div class="font-weight-bold text-dark"><i class="fas fa-list mr-1 text-muted"></i> All Customer Types</div>
            <div class="text-muted small">Showing {{ count($types) }} entries</div>
        </div>

        {{-- Mobile View Toggle (Cards vs Table) --}}
        <div class="d-flex justify-content-between align-items-center mobile-view-toggle px-3 pt-3">
            <span class="small text-muted font-weight-bold">
                <i class="fas fa-th-list mr-1"></i> View Mode
            </span>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary active" id="btnCustTypeCards">
                    <i class="fas fa-th-large mr-1"></i> Cards
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnCustTypeTable">
                    <i class="fas fa-table mr-1"></i> Table
                </button>
            </div>
        </div>

        {{-- Mobile Cards View (Zero Horizontal Scroll) --}}
        <div id="mobileCardsWrapper">
            <div class="mobile-customertype-cards">
                @forelse ($types as $type)
                    <div class="customertype-mcard">
                        <div class="customertype-mcard-hdr">
                            <span class="customertype-id-badge">#{{ $type->id }}</span>
                            @if($type->is_static)
                                <span class="badge badge-danger text-white"><i class="fas fa-lock mr-1"></i> Static System</span>
                            @else
                                <span class="badge badge-success text-white"><i class="fas fa-cog mr-1"></i> Dynamic</span>
                            @endif
                        </div>
                        <div class="customertype-mcard-title mb-1 name">{{ $type->name }}</div>
                        <div class="customertype-mcard-desc description mb-2 bg-light p-2 rounded">{{ $type->description ?: 'N/A' }}</div>
                        <div class="customertype-mcard-actions">
                            @if($type->is_static)
                                <span class="text-muted small"><i class="fas fa-shield-alt text-warning mr-1"></i> System Protected</span>
                            @else
                                @can('customer_types.edit')
                                    <button class="btn btn-primary btn-sm edit-btn mr-1" data-id="{{ $type->id }}"><i class="fas fa-edit mr-1"></i> Edit</button>
                                @endcan
                                @can('customer_types.delete')
                                    <button class="btn btn-danger btn-sm delete-btn" data-url="{{ route('customer-types.destroy', $type->id) }}" data-msg="Are you sure you want to delete this customer type?" data-method="get" onclick="logoutAndDeleteFunction(this)"><i class="fas fa-trash-alt mr-1"></i> Delete</button>
                                @endcan
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4 bg-light rounded border">
                        <i class="fas fa-inbox fa-2x mb-2 text-secondary d-block"></i>
                        No customer types found.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div id="desktopTableWrapper" class="mobile-table-hidden">
            <div class="customertype-table-wrap">
                <table id="default-datatable" class="table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 90px;">Id</th>
                            <th class="text-left">Type Name</th>
                            <th class="text-left">Description</th>
                            <th class="text-left" style="width: 150px;">Status</th>
                            <th class="text-right pr-4" style="width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($types as $type)
                            <tr>
                                <td class="text-center id"><span class="customertype-id-badge">#{{ $type->id }}</span></td>
                                <td class="text-left name font-weight-bold text-dark">{{ $type->name }}</td>
                                <td class="text-left description text-muted">{{ $type->description ?: 'N/A' }}</td>
                                <td class="text-left">
                                    @if($type->is_static)
                                        <span class="badge badge-danger text-white"><i class="fas fa-lock mr-1"></i> Static System</span>
                                    @else
                                        <span class="badge badge-success text-white"><i class="fas fa-cog mr-1"></i> Dynamic</span>
                                    @endif
                                </td>
                                <td class="text-right pr-4">
                                    @if($type->is_static)
                                        <span class="text-muted small"><i class="fas fa-shield-alt text-warning mr-1"></i> System Protected</span>
                                    @else
                                        @can('customer_types.edit')
                                            <button class="btn btn-primary btn-sm edit-btn" data-id="{{ $type->id }}">Edit</button>
                                        @endcan
                                        @can('customer_types.delete')
                                            <button class="btn btn-danger btn-sm delete-btn" data-url="{{ route('customer-types.destroy', $type->id) }}" data-msg="Are you sure you want to delete this customer type?" data-method="get" onclick="logoutAndDeleteFunction(this)">Delete</button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Add/Edit Customer Type Modal --}}
<div class="modal fade" id="customerTypeModal" tabindex="-1" aria-labelledby="customerTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white px-4 py-3 border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="customerTypeModalLabel"><i class="fas fa-tags text-primary mr-2"></i><span id="modalTitleText">Add Customer Type</span></h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="myform" action="{{ route('customer-types.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="edit_id" id="edit_id" />
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label font-weight-bold text-dark small">Customer Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control px-3 py-2" id="name" placeholder="Enter type name (e.g. Retailer)..." required style="border-radius: 10px; border: 1.5px solid #cbd5e1;" />
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label font-weight-bold text-dark small">Description</label>
                        <textarea name="description" class="form-control px-3 py-2" id="description" rows="3" placeholder="Enter description..." style="border-radius: 10px; border: 1.5px solid #cbd5e1;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                    @can('customer_types.create')
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold save-btn" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                            <i class="fas fa-check mr-1"></i> Save Customer Type
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/mycode.js') }}"></script>
<script>
    // Fix ARIA focus warning on modal close
    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
    });

    $(document).on('submit', '.myform', function(e) {
        e.preventDefault();
        var formdata = new FormData(this);
        var url = $(this).attr('action');
        var method = $(this).attr('method');
        $(this).find(':submit').attr('disabled', true);
        myAjax(url, formdata, method);
    });

    $(document).on('click', '.edit-btn', function() {
        var tr = $(this).closest("tr, .customertype-mcard");
        var id = $(this).data('id');
        var name = tr.find(".name").text().trim();
        var description = tr.find(".description").text().trim();
        if (description === 'N/A') description = '';

        $('#edit_id').val(id);
        $('#name').val(name);
        $('#description').val(description);
        $('#modalTitleText').text('Edit Customer Type');
        $("#customerTypeModal").modal("show");
    });

    $('#reset').on('click', function() {
        $('#edit_id').val('');
        $('#name').val('');
        $('#description').val('');
        $('#modalTitleText').text('Add Customer Type');
    });

    $(document).ready(function() {
        if ($('#default-datatable').length) {
            $('#default-datatable').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50, 100],
                "order": [[0, 'desc']],
                "language": {
                    "search": "Search Customer Type:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        }

        // Mobile View Switcher (Cards vs Table)
        $('#btnCustTypeCards').on('click', function() {
            $(this).addClass('active');
            $('#btnCustTypeTable').removeClass('active');
            $('#mobileCardsWrapper').show();
            $('#desktopTableWrapper').addClass('mobile-table-hidden').hide();
        });

        $('#btnCustTypeTable').on('click', function() {
            $(this).addClass('active');
            $('#btnCustTypeCards').removeClass('active');
            $('#mobileCardsWrapper').hide();
            $('#desktopTableWrapper').removeClass('mobile-table-hidden').show();
        });
    });
</script>
@endsection
