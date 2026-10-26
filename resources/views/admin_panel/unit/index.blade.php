@extends('admin_panel.layout.app')
@section('content')

    <style>
        /* Desktop Default (min-width: 992px) */
        @media (min-width: 992px) {
            #mobileCardsWrapper {
                display: none !important;
            }
            .mobile-view-toggle {
                display: none !important;
            }
            #desktopTableWrapper {
                display: block !important;
            }
        }

        /* Mobile Card Styling */
        .unit-mcard {
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
        .unit-mcard-header {
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .unit-search-box {
            position: relative;
            margin-bottom: 12px;
        }
        .unit-search-box input {
            border-radius: 8px;
            padding-left: 36px;
            font-size: 0.88rem;
            height: 38px;
            border: 1px solid #cbd5e1;
            width: 100%;
            box-sizing: border-box;
        }
        .unit-search-box i {
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
            .container,
            .container-fluid {
                max-width: 100vw !important;
                width: 100% !important;
                overflow-x: hidden !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
                box-sizing: border-box !important;
            }
            .page-header-box {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .page-header-box .btn {
                width: 100% !important;
                justify-content: center !important;
            }
            .card-box-wrapper {
                border-radius: 14px !important;
                padding: 12px 10px !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
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
            #default-datatable {
                min-width: 480px !important;
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 page-header-box">
                            <div>
                                <h3 class="fw-bold mb-0" style="color: #0b5a2b; font-size: 22px;">Units</h3>
                                <p class="text-muted mb-0 small">Manage and view all product measurement units</p>
                            </div>
                            @can('units.create')
                                <button type="button" class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center" data-toggle="modal"
                                    data-target="#exampleModal" id="reset" style="height: 38px; border-radius: 6px;">
                                    <i class="fas fa-plus mr-1" style="margin-right: 5px;"></i> Create Unit
                                </button>
                            @endcan
                        </div>

                        <div class="border shadow rounded-4 card-box-wrapper" style="background-color: white;">
                            <div class="col-lg-12 m-auto px-0 px-md-3">

                                {{-- Mobile View Switcher (Cards vs Table) --}}
                                <div class="d-flex justify-content-between align-items-center mobile-view-toggle pb-3 mb-2 border-bottom pt-2">
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
                                <div id="mobileCardsWrapper">
                                    <div class="unit-search-box">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="mobileUnitSearch" class="form-control" placeholder="Search units...">
                                    </div>

                                    <div class="mobile-unit-list">
                                        @forelse ($unit as $company)
                                            <div class="unit-mcard" data-search="{{ strtolower($company->id . ' ' . $company->name) }}">
                                                <div class="unit-mcard-header d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                                        <span class="badge bg-dark text-white font-weight-bold">#<span class="id">{{ $company->id }}</span></span>
                                                        <span class="fw-bold text-dark name" style="font-size: 15px;">{{ $company->name }}</span>
                                                    </div>
                                                </div>

                                                <div class="unit-mcard-footer d-flex justify-content-end align-items-center mt-2 pt-2" style="gap: 6px;">
                                                    @include('admin_panel.partials.action_buttons', [
                                                        'editRoute' => route('store.Unit'),
                                                        'deleteRoute' => route('delete.Unit', $company->id),
                                                        'editIsLink' => false,
                                                        'permissions' => [
                                                            'edit' => 'units.edit',
                                                            'delete' => 'units.delete',
                                                        ],
                                                        'dataId' => $company->id,
                                                    ])
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted py-4">
                                                <i class="fas fa-balance-scale fs-2 d-block mb-1 text-muted"></i>
                                                <p class="mb-0">No units found.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Desktop Table View --}}
                                <div id="desktopTableWrapper" class="mobile-table-hidden">
                                    <div class="table-responsive mt-3 mb-4">
                                        <table id="default-datatable" class="table table-striped table-bordered align-middle">
                                            <thead class="table-dark text-center">
                                                <tr>
                                                    <th class="text-center" style="width: 80px;">Id</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center" style="width: 150px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                @foreach ($unit as $company)
                                                    <tr>
                                                        <td class="id fw-semibold">{{ $company->id }}</td>
                                                        <td class="name fw-semibold">{{ $company->name }}</td>
                                                        <td class="text-center">
                                                            @include('admin_panel.partials.action_buttons', [
                                                                'editRoute' => route('store.Unit'),
                                                                'deleteRoute' => route('delete.Unit', $company->id),
                                                                'editIsLink' => false,
                                                                'permissions' => [
                                                                    'edit' => 'units.edit',
                                                                    'delete' => 'units.delete',
                                                                ],
                                                                'dataId' => $company->id,
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
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Add Unit</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <form class="myform" action="{{ route('store.Unit') }}" method="POST">
                    @csrf
                    <div class="modal-body p-3 p-md-4">
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Unit Title / Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary-subtle" id="name" required placeholder="e.g. Kg, Pcs, Box" />
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-3 py-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @can('units.create')
                            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/mycode.js') }}"></script>

    <script>
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        // AJAX Form Submit
        $(document).on('submit', '.myform', function(e) {
            e.preventDefault();
            var formdata = new FormData(this);
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            $(this).find(':submit').attr('disabled', true);
            myAjax(url, formdata, method);
        });

        // Edit Handler (Works for both Table Row and Mobile Card)
        $(document).on('click', '.edit-btn', function() {
            var container = $(this).closest("tr, .unit-mcard");
            var id = container.find(".id").text().trim();
            var name = container.find(".name").text().trim();

            $('#id').val(id);
            $('#name').val(name);
            $('#exampleModalLabel').text('Edit Unit');
            $("#exampleModal").modal("show");
        });

        // Reset Handler for Create
        $('#reset').on('click', function() {
            $('#id').val('');
            $('#name').val('');
            $('#exampleModalLabel').text('Add Unit');
        });

        // Mobile View Switcher (Cards vs Table)
        $('#btnShowCards').on('click', function() {
            $(this).addClass('active');
            $('#btnShowTable').removeClass('active');
            $('#mobileCardsWrapper').show();
            $('#desktopTableWrapper').addClass('mobile-table-hidden').hide();
        });

        $('#btnShowTable').on('click', function() {
            $(this).addClass('active');
            $('#btnShowCards').removeClass('active');
            $('#mobileCardsWrapper').hide();
            $('#desktopTableWrapper').removeClass('mobile-table-hidden').show();
            if ($.fn.DataTable.isDataTable('#default-datatable')) {
                $('#default-datatable').DataTable().columns.adjust();
            }
        });

        // Mobile Search Filter
        $('#mobileUnitSearch').on('keyup input', function() {
            let val = $(this).val().toLowerCase().trim();
            $('.mobile-unit-list .unit-mcard').each(function() {
                let searchTxt = $(this).attr('data-search') || '';
                $(this).toggle(searchTxt.indexOf(val) > -1);
            });
        });

        // DataTable Initialization
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#default-datatable')) {
                $('#default-datatable').DataTable({
                    "pageLength": 10,
                    "lengthMenu": [5, 10, 25, 50, 100],
                    "order": [
                        [0, 'desc']
                    ],
                    "language": {
                        "search": "Filter units:",
                        "lengthMenu": "Show _MENU_ entries"
                    }
                });
            }
        });
    </script>
@endsection
