@extends('admin_panel.layout.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

    <style>
        /* Mobile horizontal scroll prevention & responsiveness */
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
            .page-header-box {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
            }
            .page-header-box .btn {
                width: 100% !important;
                justify-content: center !important;
            }
            .card {
                border-radius: 12px !important;
                max-width: 100% !important;
            }
            .card-body {
                padding: 12px !important;
                max-width: 100% !important;
            }
            .mobile-table-hidden {
                display: none !important;
            }
            .table-responsive {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .table-responsive table {
                min-width: 650px;
                white-space: nowrap;
            }

            /* Modal Mobile Stacked (Zero Horizontal Scroll) */
            .modal-dialog {
                max-width: calc(100% - 16px) !important;
                margin: 10px auto !important;
            }
            .modal-content {
                max-width: 100% !important;
                overflow-x: hidden !important;
                border-radius: 14px !important;
            }
            .modal-body {
                padding: 14px !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }
            .modal-table-wrap {
                width: 100% !important;
                max-width: 100% !important;
                white-space: normal !important;
                overflow-x: hidden !important;
            }
            #voucherItemsTable {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                border: none !important;
                margin-bottom: 0 !important;
            }
            #voucherItemsTable thead {
                display: none !important;
            }
            #voucherItemsTable, 
            #voucherItemsTable tbody {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            #voucherItemsTable tr.voucher-row {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                background: #f8fafc;
                border: 1px solid #cbd5e1 !important;
                border-radius: 12px;
                padding: 12px;
                margin-bottom: 14px;
                box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
                box-sizing: border-box !important;
            }
            #voucherItemsTable td {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 6px 0 !important;
                border: none !important;
                box-sizing: border-box !important;
            }
            #voucherItemsTable td .mobile-input-label {
                display: block !important;
                font-weight: 600;
                font-size: 0.82rem;
                color: #334155;
                margin-bottom: 4px;
            }
            #voucherItemsTable td .form-control,
            #voucherItemsTable td .form-select {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                height: 38px;
                font-size: 0.88rem;
            }
            #voucherItemsTable td .remove-row {
                width: 100% !important;
                margin-top: 6px;
            }
            .voucher-total-box {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed #cbd5e1;
            }
            .voucher-total-box input {
                width: 130px !important;
            }
            .modal-footer {
                padding: 12px !important;
                display: flex !important;
                gap: 8px;
            }
            .modal-footer .btn {
                flex: 1 1 0;
            }

            /* DataTables responsive styling */
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left !important;
                margin-bottom: 8px;
            }
            div.dataTables_wrapper div.dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
                margin-top: 4px;
            }
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: left !important;
                padding: 8px 0;
            }
        }

        /* Desktop view constraints */
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
            .mobile-input-label {
                display: none !important;
            }
            .modal-table-wrap {
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            .voucher-total-box {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 8px;
                margin-top: 12px;
            }
        }

        /* Mobile Card Styling */
        .voucher-mcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            width: 100%;
            box-sizing: border-box;
        }
        .voucher-mcard-header {
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .voucher-search-box {
            position: relative;
            margin-bottom: 12px;
        }
        .voucher-search-box input {
            border-radius: 8px;
            padding-left: 35px;
            font-size: 0.88rem;
            height: 38px;
            border: 1px solid #cbd5e1;
            width: 100%;
        }
        .voucher-search-box i {
            position: absolute;
            left: 12px;
            top: 11px;
            color: #94a3b8;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-3 py-md-4">

                {{-- Page Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 page-header-box">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ ucwords($type ?? 'Voucher') }}</h4>
                        <p class="text-muted mb-0 small">Manage and view all {{ strtolower($type ?? 'voucher') }} entries</p>
                    </div>
                    @can('expense.voucher.create')
                        <button class="btn btn-primary shadow-sm fw-bold d-inline-flex align-items-center" data-toggle="modal" data-target="#voucherModal" data-bs-toggle="modal" data-bs-target="#voucherModal" style="height: 38px; border-radius: 6px;">
                            <i class="fas fa-plus mr-1" style="margin-right: 5px;"></i> Add {{ ucwords($type ?? 'Voucher') }}
                        </button>
                    @endcan
                </div>

                {{-- Voucher Card & Content --}}
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body">

                        {{-- Mobile View Toggle (Cards vs Table) --}}
                        <div class="d-flex justify-content-between align-items-center mobile-view-toggle pb-3 mb-2 border-bottom">
                            <span class="small text-muted font-weight-bold">
                                <i class="fas fa-th-list mr-1"></i> View Mode
                            </span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnShowCards">
                                    <i class="fas fa-th-large mr-1"></i> Cards
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnShowTable">
                                    <i class="fas fa-table mr-1"></i> Table
                                </button>
                            </div>
                        </div>

                        {{-- Mobile Cards View --}}
                        <div id="mobileCardsWrapper">
                            <div class="voucher-search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="mobileSearchInput" class="form-control" placeholder="Search entries...">
                            </div>

                            <div class="mobile-voucher-list">
                                @forelse ($vouchers as $voucher)
                                    <div class="voucher-mcard" data-search="{{ strtolower(($voucher->id ?? '') . ' ' . ($voucher->type ?? '') . ' ' . ($voucher->person ?? '') . ' ' . ($voucher->narration ?? '')) }}">
                                        <div class="voucher-mcard-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary text-white font-weight-bold">#{{ $voucher->id }}</span>
                                                <span class="badge bg-info text-dark ml-1">{{ ucfirst($voucher->type) }}</span>
                                            </div>
                                            <span class="text-muted small font-weight-bold">{{ $voucher->date }}</span>
                                        </div>

                                        <div class="voucher-mcard-body mt-2">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <span class="text-muted small">Party / Customer:</span>
                                                <span class="font-weight-bold text-dark text-right text-end">{{ $voucher->person }}</span>
                                            </div>
                                            @if($voucher->narration)
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="text-muted small">Narration:</span>
                                                    <span class="text-dark small text-right text-end" style="max-width: 65%;">{{ $voucher->narration }}</span>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <span class="text-muted small font-weight-bold">Amount:</span>
                                                <span class="text-success font-weight-bold" style="font-size: 1.1rem;">Rs. {{ number_format($voucher->amount, 2) }}</span>
                                            </div>
                                        </div>

                                        <div class="voucher-mcard-footer d-flex justify-content-end align-items-center mt-2 pt-2 border-top">
                                            @can('expense.voucher.edit')
                                                <button class="btn btn-warning btn-sm edit-btn fw-bold" data-id="{{ $voucher->id }}"
                                                    data-sales_officer="{{ $voucher->sales_officer }}" data-date="{{ $voucher->date }}"
                                                    data-type="{{ $voucher->type }}" data-person="{{ $voucher->person }}"
                                                    data-sub_head="{{ $voucher->sub_head }}"
                                                    data-narration="{{ $voucher->narration }}" data-amount="{{ $voucher->amount }}"
                                                    data-toggle="modal" data-target="#voucherModal"
                                                    data-bs-toggle="modal" data-bs-target="#voucherModal">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <p class="mb-0">No entries found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Desktop Table Wrapper --}}
                        <div id="desktopTableWrapper" class="mobile-table-hidden">
                            <div class="table-responsive mt-2 mb-4">
                                <table id="voucherTable" class="table table-bordered table-striped table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer Type</th>
                                            <th>Party / Customer</th>
                                            <th>Narration</th>
                                            <th class="text-end">Amount</th>
                                            <th>Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vouchers as $voucher)
                                            <tr>
                                                <td>{{ $voucher->id }}</td>
                                                <td>{{ ucfirst($voucher->type) }}</td>
                                                <td>{{ $voucher->person }}</td>
                                                <td>{{ $voucher->narration }}</td>
                                                <td class="text-end fw-bold text-dark">{{ number_format($voucher->amount, 2) }}</td>
                                                <td>{{ $voucher->date }}</td>
                                                <td class="text-center">
                                                    @can('expense.voucher.edit')
                                                        <button class="btn btn-warning btn-sm edit-btn fw-bold" data-id="{{ $voucher->id }}"
                                                            data-sales_officer="{{ $voucher->sales_officer }}" data-date="{{ $voucher->date }}"
                                                            data-type="{{ $voucher->type }}" data-person="{{ $voucher->person }}"
                                                            data-sub_head="{{ $voucher->sub_head }}"
                                                            data-narration="{{ $voucher->narration }}" data-amount="{{ $voucher->amount }}"
                                                            data-toggle="modal" data-target="#voucherModal"
                                                            data-bs-toggle="modal" data-bs-target="#voucherModal">
                                                            <i class="fas fa-edit me-1"></i> Edit
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

    {{-- Voucher Modal --}}
    <div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="voucherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('vouchers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="voucher_id">

                    <div class="modal-header bg-primary text-white px-3 px-md-4 py-3">
                        <h5 class="modal-title fw-bold text-white mb-0" id="voucherModalLabel">Add {{ ucwords($type ?? 'Voucher') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem; border: none; background: transparent; cursor: pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3 p-md-4">
                        <div class="modal-table-wrap">
                            <table class="table table-bordered align-middle" id="voucherItemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer Type</th>
                                        <th>Customer</th>
                                        <th>Narration</th>
                                        <th>Amount</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="voucher-row">
                                        <td>
                                            <label class="mobile-input-label">Date</label>
                                            <input type="date" name="date[]" class="form-control" required>
                                        </td>
                                        <td>
                                            <label class="mobile-input-label">Customer Type</label>
                                            <select name="type[]" class="form-control form-select type-select" required>
                                                @foreach(\App\Models\CustomerType::orderBy('name')->get() as $cType)
                                                    <option value="{{ $cType->name }}" {{ $cType->name === 'Main Customer' ? 'selected' : '' }}>{{ $cType->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <label class="mobile-input-label">Customer</label>
                                            <select name="person[]" class="form-control form-select person-select" required>
                                                <option value="">Select Customer</option>
                                            </select>
                                        </td>
                                        <td>
                                            <label class="mobile-input-label">Narration</label>
                                            <select name="narration[]" class="form-control form-select narration-select" required>
                                                @foreach ($narration as $item)
                                                    <option value="{{ $item->narration }}">{{ $item->narration }}</option>
                                                @endforeach
                                                <option value="custom">-- Custom --</option>
                                            </select>
                                            <input type="text" name="custom_narration[]"
                                                class="form-control custom-narration-input mt-2" style="display:none;"
                                                placeholder="Write custom narration">
                                        </td>
                                        <div class="col-md-4 d-none">
                                            <label class="form-label">Sub-Head</label>
                                            <select name="sub_head" class="form-select">
                                                @foreach ($narration as $item)
                                                    <option value="{{ $item->expense_head }}">{{ $item->expense_head }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <td>
                                            <label class="mobile-input-label">Amount</label>
                                            <input type="number" name="amount[]" class="form-control" step="0.01" required placeholder="0.00">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fas fa-trash-alt mr-1"></i> Remove
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            {{-- Add Row Button for convenience --}}
                            <div class="mt-2 text-start">
                                <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" id="btnAddRow" style="border-radius: 6px;">
                                    <i class="fas fa-plus mr-1"></i> Add Another Row
                                </button>
                            </div>

                            <div class="voucher-total-box">
                                <strong class="text-dark">Total Amount:</strong>
                                <input type="text" name="total" class="form-control fw-bold text-success border-success text-end" style="width: 140px;" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top px-3 px-md-4 py-3">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">Save Voucher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#voucherTable')) {
                $('#voucherTable').DataTable({
                    order: [[0, 'desc']]
                });
            }

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
            });

            // Mobile Search Filter
            $('#mobileSearchInput').on('keyup', function() {
                let val = $(this).val().toLowerCase();
                $('.mobile-voucher-list .voucher-mcard').each(function() {
                    let searchTxt = $(this).attr('data-search') || '';
                    $(this).toggle(searchTxt.indexOf(val) > -1);
                });
            });

            function loadCustomers(type, $personSelect) {
                $.ajax({
                    url: "{{ url('get-customers-by-type') }}",
                    type: 'GET',
                    data: {
                        type: type
                    },
                    success: function(data) {
                        $personSelect.empty();
                        $personSelect.append('<option value="">Select Customer</option>');
                        $.each(data.customers, function(i, customer) {
                            $personSelect.append('<option value="' + customer.id + '">' +
                                customer.customer_name + '</option>');
                        });
                    },
                    error: function() {
                        alert('Error fetching customers.');
                    }
                });
            }

            // Load customers for row on type change
            $('#voucherItemsTable').on('change', '.type-select', function() {
                var selectedType = $(this).val();
                var $personSelect = $(this).closest('tr').find('.person-select');
                if (selectedType) {
                    loadCustomers(selectedType, $personSelect);
                } else {
                    $personSelect.empty().append('<option value="">Select Customer</option>');
                }
            });

            // Function to check if a row is fully filled
            function isRowComplete($row) {
                var complete = true;
                $row.find('input:visible, select:visible').each(function() {
                    let val = $(this).val();
                    if (val === null || val === undefined || val.toString().trim() === '') {
                        complete = false;
                        return false;
                    }
                });
                return complete;
            }

            // Auto add new row when previous row completed
            $('#voucherItemsTable').on('input change', 'input, select', function() {
                var $tbody = $('#voucherItemsTable tbody');
                var $lastRow = $tbody.find('tr.voucher-row:last');

                if (isRowComplete($lastRow)) {
                    var $newRow = $lastRow.clone();
                    $newRow.find('input').val('');
                    $newRow.find('.custom-narration-input').hide().removeAttr('required').val('');
                    $newRow.find('.narration-select').attr('name', 'narration[]').val($newRow.find('.narration-select option:first').val());
                    $newRow.find('select.person-select').empty().append(
                        '<option value="">Select Customer</option>');
                    $newRow.find('select.type-select').val('Main Customer');
                    $tbody.append($newRow);
                    loadCustomers('Main Customer', $newRow.find('.person-select'));
                }
            });

            // Manual Add Row Button
            $('#btnAddRow').on('click', function() {
                var $tbody = $('#voucherItemsTable tbody');
                var $lastRow = $tbody.find('tr.voucher-row:last');
                var $newRow = $lastRow.clone();
                $newRow.find('input').val('');
                $newRow.find('.custom-narration-input').hide().removeAttr('required').val('');
                $newRow.find('.narration-select').attr('name', 'narration[]').val($newRow.find('.narration-select option:first').val());
                $newRow.find('select.person-select').empty().append(
                    '<option value="">Select Customer</option>');
                $newRow.find('select.type-select').val('Main Customer');
                $tbody.append($newRow);
                loadCustomers('Main Customer', $newRow.find('.person-select'));
            });

            // Remove row button click
            $('#voucherItemsTable').on('click', '.remove-row', function() {
                var $tbody = $('#voucherItemsTable tbody');
                var rowCount = $tbody.find('tr.voucher-row').length;

                if (rowCount > 1) {
                    $(this).closest('tr').remove();
                    updateTotal();
                } else {
                    alert('At least one voucher row is required.');
                }
            });

            // Initial load: load customers for first row default type
            $('#voucherItemsTable tbody tr').each(function() {
                var typeVal = $(this).find('.type-select').val();
                var $personSelect = $(this).find('.person-select');
                if (typeVal) {
                    loadCustomers(typeVal, $personSelect);
                }
            });

            function updateTotal() {
                let total = 0;
                $('input[name="amount[]"]').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    total += val;
                });
                $('input[name="total"]').val(total.toFixed(2));
            }

            // When amount changes
            $('#voucherItemsTable').on('input', 'input[name="amount[]"]', function() {
                updateTotal();
            });

            // Also update after adding new row
            $('#voucherItemsTable').on('input change', 'input, select', function() {
                updateTotal();
            });

            // Narration dropdown change event
            $('#voucherItemsTable').on('change', '.narration-select', function() {
                var $row = $(this).closest('tr');
                var selectedVal = $(this).val();
                var $customInput = $row.find('.custom-narration-input');

                if (selectedVal === 'custom') {
                    $customInput.show().attr('required', true).focus();
                    $(this).removeAttr('name');
                    $customInput.attr('name', 'narration[]');
                } else {
                    $customInput.hide().removeAttr('required').val('');
                    $(this).attr('name', 'narration[]');
                    $customInput.removeAttr('name');
                }
            });

            // Handle page load for existing rows narration:
            $('#voucherItemsTable tbody tr').each(function() {
                var $select = $(this).find('.narration-select');
                $select.trigger('change');
            });

            // Edit Button Click (handles both desktop table and mobile cards)
            $(document).on('click', '.edit-btn', function() {
                $('#voucherModalLabel').text('Edit Voucher');
                $('#voucher_id').val($(this).data('id'));

                // Keep only first row for single edit
                $('#voucherItemsTable tbody tr.voucher-row:not(:first)').remove();
                var $firstRow = $('#voucherItemsTable tbody tr.voucher-row:first');

                $firstRow.find('[name="date[]"]').val($(this).data('date'));
                $firstRow.find('[name="type[]"]').val($(this).data('type')).trigger('change');
                
                var personVal = $(this).data('person');
                setTimeout(function() {
                    $firstRow.find('[name="person[]"]').val(personVal);
                }, 350);

                $firstRow.find('[name="narration[]"]').val($(this).data('narration')).trigger('change');
                $firstRow.find('[name="amount[]"]').val($(this).data('amount'));
                updateTotal();
            });

            // Reset Form on Add New Click
            $(document).on('click', '[data-target="#voucherModal"], [data-bs-target="#voucherModal"]', function() {
                if (!$(this).hasClass('edit-btn')) {
                    $('#voucherModalLabel').text('Add {{ ucwords($type ?? "Voucher") }}');
                    $('#voucher_id').val('');
                    $('#voucherModal form')[0].reset();
                    $('#voucherItemsTable tbody tr.voucher-row:not(:first)').remove();
                    var $firstRow = $('#voucherItemsTable tbody tr.voucher-row:first');
                    $firstRow.find('.custom-narration-input').hide().removeAttr('required').val('');
                    $firstRow.find('.narration-select').attr('name', 'narration[]').val($firstRow.find('.narration-select option:first').val());
                    $firstRow.find('select.type-select').val('Main Customer').trigger('change');
                    updateTotal();
                }
            });
        });
    </script>
@endsection