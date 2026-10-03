@extends('admin_panel.layout.app')
@section('content')
    <style>
        .btn-sm i.fa-toggle-on {
            color: green;
            font-size: 20px;
        }

        .btn-sm i.fa-toggle-off {
            color: gray;
            font-size: 20px;
        }

        .customer-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .customer-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .btn-action-custom {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-weight: 500;
            border-radius: 8px;
            font-size: 0.88rem;
            transition: all 0.2s;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid px-4 py-3">

                <!-- Header & Toolbar -->
                <div class="customer-toolbar">
                    <div>
                        <h3 class="mb-0 fw-bold text-dark">Customer List</h3>
                        <small class="text-muted">Manage your customer accounts, balances, import &amp; export data.</small>
                    </div>

                    <div class="customer-actions">
                        <!-- Template Download -->
                        <a href="{{ route('customers.template') }}" class="btn btn-outline-secondary btn-action-custom" title="Download blank CSV template">
                            <i class="fas fa-file-csv text-primary"></i> Template
                        </a>

                        <!-- Export CSV -->
                        <a href="{{ route('customers.export') }}" class="btn btn-outline-success btn-action-custom" title="Export all customers to CSV">
                            <i class="fas fa-file-download"></i> Export CSV
                        </a>

                        <!-- Import CSV (Modal Trigger) -->
                        <button type="button" class="btn btn-outline-primary btn-action-custom" data-toggle="modal" data-target="#customerImportModal" id="openCustomerImportModalBtn" title="Import customers from CSV">
                            <i class="fas fa-file-upload"></i> Import CSV
                        </button>

                        @can('customers.create')
                            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-action-custom">
                                <i class="fas fa-user-plus"></i> + Add New Customer
                            </a>
                        @endcan

                        @can('customers.view')
                            <a href="{{ route('customers.ledger') }}" class="btn btn-info btn-action-custom text-white">
                                <i class="fas fa-book"></i> Ledger
                            </a>
                            <a href="{{ route('customer.payments') }}" class="btn btn-secondary btn-action-custom">
                                <i class="fas fa-money-bill-wave"></i> Payment
                            </a>
                        @endcan

                        <a href="{{ route('customers.inactive') }}" class="btn btn-outline-danger btn-action-custom">
                            <i class="fas fa-user-slash"></i> Inactive Customers
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="customersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Credit Limit</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th style="min-width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                    <tr>
                                        <td><strong>{{ $customer->customer_id }}</strong></td>
                                        <td>
                                            <strong>{{ $customer->customer_name }}</strong>
                                            @if($customer->customer_name_ur)
                                                <small class="text-muted d-block" dir="rtl">{{ $customer->customer_name_ur }}</small>
                                            @endif
                                            @if($customer->customer_type)
                                                <span class="badge bg-light text-dark border">{{ $customer->customer_type }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $customer->mobile ?: '-' }}</td>
                                        <td>{{ $customer->balance_range == 0 ? 'Unlimited' : number_format($customer->balance_range, 0) }}</td>
                                        <td>
                                            @if($customer->status === 'active')
                                                <span class="badge bg-success text-white">active</span>
                                            @else
                                                <span class="badge bg-secondary text-white">inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $source = $customer->source ?? 'Manual';
                                            @endphp
                                            @if($source === 'Website')
                                                <span class="badge bg-success">Website</span>
                                            @elseif($source === 'Both')
                                                <span class="badge bg-info text-dark">Both</span>
                                            @else
                                                <span class="badge bg-secondary">Manual</span>
                                            @endif
                                        </td>
                                        <td>
                                            @include('admin_panel.partials.action_buttons', [
                                                'editRoute' => route('customers.edit', $customer->id),
                                                'deleteRoute' => route('customers.destroy', $customer->id),
                                                'editIsLink' => true,
                                                'permissions' => [
                                                    'edit' => 'customers.edit',
                                                    'delete' => 'customers.delete',
                                                ],
                                                'dataId' => $customer->id,
                                            ])

                                            @can('customers.edit')
                                                <a href="{{ route('customers.toggleStatus', $customer->id) }}"
                                                    class="btn btn-sm {{ $customer->status === 'active' ? 'btn-dark' : 'btn-secondary' }}"
                                                    title="Toggle Status">
                                                    <i
                                                        class="fa-solid {{ $customer->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </a>
                                            @endcan
                                            @can('customers.view')
                                                <a href="{{ route('customer.payments') }}" class="btn btn-sm btn-info" title="Customer Payments">Payments</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No customers found. Click <strong>+ Add New Customer</strong> or <strong>Import CSV</strong> to add customers.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CUSTOMER IMPORT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="customerImportModal" tabindex="-1" role="dialog" aria-labelledby="customerImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; border-bottom: none;">
                    <div>
                        <h5 class="modal-title fw-bold" id="customerImportModalLabel">
                            <i class="fas fa-file-upload me-2"></i> Import Customers from CSV
                        </h5>
                        <small style="color: rgba(255,255,255,0.88);">
                            Create new customers or update existing records directly from spreadsheet.
                        </small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.8; text-shadow:none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4" style="background:#f8fafc;">
                    <form action="{{ route('customers.import.validate') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info d-flex gap-2 align-items-start mb-4" style="font-size:.85rem; border-left: 4px solid #3b82f6;">
                            <i class="fas fa-info-circle fs-5 mt-1 flex-shrink-0 text-primary"></i>
                            <div>
                                <strong>Quick Guide:</strong><br>
                                &bull; <strong>Create New Customers:</strong> Download the <a href="{{ route('customers.template') }}" class="alert-link fw-bold">Blank Template</a>, enter customer info, and import.<br>
                                &bull; <strong>Edit Existing Customers:</strong> Download current data via <a href="{{ route('customers.export') }}" class="alert-link fw-bold">Export CSV</a>, modify prices/limits/phones in Excel, and upload here.<br>
                                &bull; Existing customers are matched accurately by <strong>Customer ID</strong> or <strong>System ID</strong>.<br>
                                &bull; You can review all rows using <strong>Validate &amp; Preview</strong> or import right away using <strong>Direct Import</strong>.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold text-dark">Import Mode</label>
                                <select name="import_mode" class="form-control" required style="border-radius: 8px;">
                                    <option value="upsert" selected>Create &amp; Update (Recommended)</option>
                                    <option value="create_only">Create Only (Skip existing)</option>
                                    <option value="update_only">Update Only (Ignore new)</option>
                                </select>
                                <small class="text-muted">Controls whether existing records are updated or skipped.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold text-dark">Auto-create Missing Data</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="autoCreateCustCheck" name="auto_create" value="1" checked>
                                    <label class="form-check-label fw-bold ms-1" for="autoCreateCustCheck">
                                        Auto-create missing Types &amp; Zones
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">Automatically registers new Customer Types or Zones found in the CSV.</small>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fw-bold text-dark">Upload CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" class="form-control p-1" accept=".csv,.txt" required style="border-radius: 8px;">
                            <small class="text-muted">Supported formats: .csv, .txt (UTF-8 encoded). Max size: 10 MB.</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap" style="gap: 10px;">
                            <a href="{{ route('customers.template') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>

                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                <button type="submit" name="action_type" value="preview" class="btn btn-primary px-3 fw-bold" style="border-radius: 8px;">
                                    <i class="fas fa-eye me-1"></i> Validate &amp; Preview
                                </button>
                                <button type="submit" name="action_type" value="direct" class="btn btn-success px-3 fw-bold" style="border-radius: 8px;">
                                    <i class="fas fa-bolt me-1"></i> Direct Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
