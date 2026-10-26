@extends('admin_panel.layout.app')
@section('content')
    
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}'
                });
            });
        </script>
    @endif

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
        .category-mcard {
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
        .category-mcard-header {
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .category-search-box {
            position: relative;
            margin-bottom: 12px;
        }
        .category-search-box input {
            border-radius: 8px;
            padding-left: 36px;
            font-size: 0.88rem;
            height: 38px;
            border: 1px solid #cbd5e1;
            width: 100%;
            box-sizing: border-box;
        }
        .category-search-box i {
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
                min-width: 600px !important;
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
                                <h3 class="fw-bold mb-0" style="color: #0b5a2b; font-size: 22px;">Expense Categories</h3>
                                <p class="text-muted mb-0 small">Manage and view all expense categories</p>
                            </div>
                            @can('expense.voucher.create')
                                <button type="button" class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center" data-toggle="modal" data-target="#exampleModal"
                                    id="reset" style="height: 38px; border-radius: 6px;">
                                    <i class="fas fa-plus mr-1" style="margin-right: 5px;"></i> Create Category
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
                                    <div class="category-search-box">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="mobileCategorySearch" class="form-control" placeholder="Search expense categories...">
                                    </div>

                                    <div class="mobile-category-list">
                                        @forelse ($categories as $cat)
                                            <div class="category-mcard" data-search="{{ strtolower($cat->id . ' ' . $cat->name . ' ' . ($cat->code ?? '') . ' ' . ($cat->description ?? '')) }}">
                                                <div class="category-mcard-header d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px; min-width: 0; max-width: 75%;">
                                                        <span class="badge bg-dark text-white font-weight-bold">#<span class="id">{{ $cat->id }}</span></span>
                                                        <span class="fw-bold text-dark name" style="font-size: 15px;">{{ $cat->name }}</span>
                                                    </div>
                                                    <div>
                                                        @if(!empty($cat->code))
                                                            <span class="badge bg-primary text-white code">{{ $cat->code }}</span>
                                                        @else
                                                            <span class="d-none code">-</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="category-mcard-body pt-2">
                                                    @if(!empty($cat->description))
                                                        <div class="d-flex align-items-start mb-1">
                                                            <span class="text-muted small me-2 flex-shrink-0" style="margin-right: 6px;">Description:</span>
                                                            <span class="text-dark small description">{{ $cat->description }}</span>
                                                        </div>
                                                    @else
                                                        <span class="d-none description">-</span>
                                                        <span class="text-muted small fst-italic">No description provided</span>
                                                    @endif
                                                </div>

                                                <div class="category-mcard-footer d-flex justify-content-end align-items-center mt-2 pt-2 border-top" style="gap: 6px;">
                                                    @include('admin_panel.partials.action_buttons', [
                                                        'editRoute' => route('expense_categories.store'),
                                                        'deleteRoute' => route('expense_categories.delete', $cat->id),
                                                        'editIsLink' => false,
                                                        'permissions' => [
                                                            'edit' => 'expense.voucher.create',
                                                            'delete' => 'expense.voucher.create',
                                                        ],
                                                        'deleteMsg' => 'Are you sure you want to delete this expense category?',
                                                    ])
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted py-4">
                                                <i class="fas fa-folder-open fs-2 d-block mb-1 text-muted"></i>
                                                <p class="mb-0">No expense categories found.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Desktop Table View --}}
                                <div id="desktopTableWrapper" class="mobile-table-hidden">
                                    <div class="table-responsive mt-3 mb-4">
                                        <table id="default-datatable" class="table table-striped table-bordered align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-center" style="width: 80px;">Id</th>
                                                    <th>Name</th>
                                                    <th>Code</th>
                                                    <th>Description</th>
                                                    <th class="text-center" style="width: 150px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($categories as $cat)
                                                    <tr>
                                                        <td class="id text-center fw-semibold">{{ $cat->id }}</td>
                                                        <td class="name fw-semibold">{{ $cat->name }}</td>
                                                        <td class="code">{{ $cat->code ?? '-' }}</td>
                                                        <td class="description text-muted">{{ $cat->description ?? '-' }}</td>
                                                        <td class="text-center">
                                                            @include('admin_panel.partials.action_buttons', [
                                                                'editRoute' => route('expense_categories.store'),
                                                                'deleteRoute' => route('expense_categories.delete', $cat->id),
                                                                'editIsLink' => false,
                                                                'permissions' => [
                                                                    'edit' => 'expense.voucher.create',
                                                                    'delete' => 'expense.voucher.create',
                                                                ],
                                                                'deleteMsg' => 'Are you sure you want to delete this expense category?',
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
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Add Expense Category</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <form class="myform" action="{{ route('expense_categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-3 p-md-4">
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control border-secondary-subtle" id="name" required />
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label fw-semibold">Code / Abbreviation</label>
                            <input type="text" name="code" class="form-control border-secondary-subtle" id="code" placeholder="e.g. RNT, SAL" />
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control border-secondary-subtle" id="description" rows="3" placeholder="Category details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-3 py-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        @can('expense.voucher.create')
                            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">
    <script src="{{ asset('assets/js/mycode.js') }}"></script>

    <script>
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        // Form AJAX Submit
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
            var container = $(this).closest("tr, .category-mcard");
            var id = container.find(".id").text().trim();
            var name = container.find(".name").text().trim();
            var code = container.find(".code").text().trim();
            var description = container.find(".description").text().trim();

            if (code === '-') code = '';
            if (description === '-') description = '';

            $('#id').val(id);
            $('#name').val(name);
            $('#code').val(code);
            $('#description').val(description);

            $('#exampleModalLabel').text('Edit Expense Category');
            $("#exampleModal").modal("show");
        });

        // Reset for Create
        $('#reset').on('click', function() {
            $('#id').val('');
            $('#name').val('');
            $('#code').val('');
            $('#description').val('');
            $('#exampleModalLabel').text('Add Expense Category');
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

        // Mobile Live Search Filter
        $('#mobileCategorySearch').on('keyup input', function() {
            let val = $(this).val().toLowerCase().trim();
            $('.mobile-category-list .category-mcard').each(function() {
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
                        "search": "Filter categories:",
                        "lengthMenu": "Show _MENU_ entries"
                    }
                });
            }
        });
    </script>
@endsection
