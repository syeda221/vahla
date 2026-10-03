@extends('admin_panel.layout.app')
@section('title', 'Import Customers - Preview')

@section('content')
<style>
    .info-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        height: 100%;
    }
    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .info-text-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .info-text-num {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid p-3">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="fas fa-file-import text-primary me-2"></i> Import Customers (Preview)
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                        Review customer data and validation status before confirming the import.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    @if(count($payload['customers']) > 0 && ($payload['preview_stats']['customers_create'] + $payload['preview_stats']['customers_update']) > 0)
                        <form action="{{ route('customers.import.confirm') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success px-4 fw-bold">
                                <i class="fas fa-check-circle me-1"></i> Confirm &amp; Import
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="info-card">
                        <div class="info-icon bg-success text-white">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="info-text-label">Customers to Create</div>
                            <div class="info-text-num text-success">{{ $payload['preview_stats']['customers_create'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="info-card">
                        <div class="info-icon bg-info text-white">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <div class="info-text-label">Customers to Update</div>
                            <div class="info-text-num text-info">{{ $payload['preview_stats']['customers_update'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="info-card">
                        <div class="info-icon bg-secondary text-white">
                            <i class="fas fa-forward"></i>
                        </div>
                        <div>
                            <div class="info-text-label">Rows Skipped</div>
                            <div class="info-text-num text-secondary">{{ $payload['preview_stats']['customers_skip'] }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="info-card">
                        <div class="info-icon {{ $payload['preview_stats']['errors_count'] > 0 ? 'bg-danger text-white' : 'bg-light text-muted' }}">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="info-text-label">Validation Errors</div>
                            <div class="info-text-num {{ $payload['preview_stats']['errors_count'] > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $payload['preview_stats']['errors_count'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Errors Alert -->
            @if(isset($payload['errors']) && count($payload['errors']) > 0)
                <div class="alert alert-danger shadow-sm mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-times-circle me-2 fs-5"></i>
                        <strong class="fs-6">Validation Errors Found ({{ count($payload['errors']) }}):</strong>
                    </div>
                    <ul class="mb-1 ps-3" style="max-height: 140px; overflow-y: auto;">
                        @foreach($payload['errors'] as $err)
                            <li><strong>Row {{ $err['row'] }}:</strong> {{ $err['msg'] }}</li>
                        @endforeach
                    </ul>
                    <small class="text-danger fw-bold d-block mt-2">
                        Note: Rows with missing required fields (e.g. Customer Name) are skipped automatically.
                    </small>
                </div>
            @endif

            <!-- Master Data Notice -->
            @if(!empty($payload['master_data']['customer_types']) || !empty($payload['master_data']['zones']))
                <div class="alert alert-warning shadow-sm mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-info-circle me-2 fs-5"></i>
                        <strong>Master Data Notice:</strong>
                    </div>
                    <p class="mb-0 small">
                        The following missing master records will be auto-created during import:
                        @if(!empty($payload['master_data']['customer_types']))
                            <br><strong>Customer Types:</strong> {{ implode(', ', $payload['master_data']['customer_types']) }}
                        @endif
                        @if(!empty($payload['master_data']['zones']))
                            <br><strong>Zones:</strong> {{ implode(', ', $payload['master_data']['zones']) }}
                        @endif
                    </p>
                </div>
            @endif

            <!-- Preview Table -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fas fa-list me-1 text-primary"></i> Data Preview ({{ count($payload['customers']) }} Rows)
                    </h5>
                    <span class="badge bg-light text-dark border px-2 py-1">
                        Mode: <strong>{{ strtoupper(str_replace('_', ' ', $payload['mode'])) }}</strong>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Action</th>
                                <th>Customer Code</th>
                                <th>Customer Type</th>
                                <th>Full Name</th>
                                <th>Mobile</th>
                                <th>Region (Zone)</th>
                                <th>Address</th>
                                <th class="text-end">Opening Balance (DR)</th>
                                <th class="text-end">Credit Limit</th>
                                <th>Payment Reminder Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payload['customers'] as $c)
                                <tr>
                                    <td>
                                        @if($c['action'] === 'create')
                                            <span class="badge bg-success text-white px-2 py-1"><i class="fas fa-plus me-1"></i>Create</span>
                                        @elseif($c['action'] === 'update')
                                            <span class="badge bg-info text-dark px-2 py-1"><i class="fas fa-edit me-1"></i>Update</span>
                                        @else
                                            <span class="badge bg-secondary text-white px-2 py-1"><i class="fas fa-forward me-1"></i>Skip</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($c['customer_id']))
                                            <strong class="text-primary">{{ $c['customer_id'] }}</strong>
                                        @else
                                            <span class="badge bg-light text-muted border">Auto-generate</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $c['customer_type'] ?: 'Main Customer' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $c['customer_name'] }}</strong>
                                    </td>
                                    <td>{{ $c['mobile'] ?: '-' }}</td>
                                    <td>{{ $c['zone'] ?: '-' }}</td>
                                    <td style="max-width: 250px;" class="text-truncate" title="{{ $c['address'] }}">
                                        {{ $c['address'] ?: '-' }}
                                    </td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($c['opening_balance'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        {{ $c['balance_range'] == 0 ? 'Unlimited' : number_format($c['balance_range'], 0) }}
                                    </td>
                                    <td>{{ $c['reminder_day'] ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                        No customer rows found in the uploaded file.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="d-flex justify-content-end gap-2 pb-4">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                @if(count($payload['customers']) > 0 && ($payload['preview_stats']['customers_create'] + $payload['preview_stats']['customers_update']) > 0)
                    <form action="{{ route('customers.import.confirm') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 fw-bold">
                            <i class="fas fa-check-circle me-1"></i> Confirm &amp; Import Customers
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
