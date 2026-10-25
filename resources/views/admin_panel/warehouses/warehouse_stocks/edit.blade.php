@extends('admin_panel.layout.app')

@section('content')
<div class="erp-container py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('warehouse_stocks.index') }}" class="text-decoration-none text-muted erp-caption">Stock Inventory</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary erp-caption" aria-current="page">Adjust Stock</li>
                </ol>
            </nav>
            <h1 class="erp-page-title mb-1"><i class="fas fa-edit me-2 text-primary"></i> Adjust Warehouse Stock</h1>
            <p class="erp-caption mb-0">Update recorded stock quantity for <strong>{{ $stock->product->item_name ?? 'Product' }}</strong> in <strong>{{ $stock->warehouse->warehouse_name ?? 'Warehouse' }}</strong></p>
        </div>
        <div>
            <a href="{{ route('warehouse_stocks.index') }}" class="erp-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger erp-card p-3 mb-4" style="border-left: 4px solid var(--danger) !important;">
            <div class="fw-semibold mb-1 text-danger"><i class="fas fa-exclamation-circle me-1"></i> Please correct the following errors:</div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card erp-card">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-semibold text-dark"><i class="fas fa-sliders-h me-2 text-primary"></i> Stock Adjustment Form</span>
                    @if($stock->warehouse && $stock->warehouse->is_shop)
                        <span class="erp-badge erp-badge-success">🏪 Main Shop</span>
                    @else
                        <span class="erp-badge erp-badge-primary">🏢 Storage Godown</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <!-- Product Info Header -->
                    <div class="d-flex align-items-center p-3 bg-light rounded-3 mb-4 border">
                        <div class="rounded-3 bg-white p-2 border me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                            @if($stock->product && $stock->product->image)
                                <img src="{{ asset('uploads/products/'.$stock->product->image) }}" class="rounded" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-box text-primary fs-5"></i>
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0 text-dark">{{ $stock->product->item_name ?? 'N/A' }}</h6>
                            <span class="erp-caption font-monospace text-muted">{{ $stock->product->item_code ?? '' }}</span>
                            <span class="erp-badge erp-badge-neutral ms-2">{{ $stock->product->unit->name ?? 'Pcs' }}</span>
                        </div>
                    </div>

                    <form action="{{ route('warehouse_stocks.update', $stock->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Location Readonly -->
                        <div class="mb-3">
                            <label class="erp-caption fw-semibold text-uppercase d-block mb-1">Location / Warehouse</label>
                            <input type="text" class="form-control erp-input bg-light text-muted" value="{{ $stock->warehouse->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $stock->warehouse->warehouse_name ?? '' }}" disabled>
                        </div>

                        <!-- Current Stock Display -->
                        <div class="mb-3">
                            <label class="erp-caption fw-semibold text-uppercase d-block mb-1">Current Recorded Quantity</label>
                            <div class="p-2.5 px-3 bg-light rounded-2 border font-tabular text-dark fs-6">
                                {{ rtrim(rtrim(number_format((float)$stock->total_pieces, 2, '.', ''), '0'), '.') }} 
                                <span class="erp-caption text-muted fw-normal ms-1">{{ $stock->product->unit->name ?? 'Pcs' }}</span>
                            </div>
                        </div>

                        <!-- New Quantity Input -->
                        <div class="mb-3">
                            <label class="erp-caption fw-semibold text-uppercase d-block mb-1">
                                New Adjusted Quantity <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="any" name="quantity" id="quantity" 
                                       class="form-control erp-input font-tabular fw-semibold text-primary" 
                                       value="{{ old('quantity', rtrim(rtrim(number_format((float)$stock->total_pieces, 2, '.', ''), '0'), '.')) }}" required>
                                <span class="input-group-text bg-light text-muted fw-semibold" style="border: 1px solid var(--border-color); border-left: 0; font-size: 13px;">{{ $stock->product->unit->name ?? 'Pcs' }}</span>
                            </div>
                            <div class="erp-caption mt-1">Enter physical stock count. Supports decimals (e.g. <code>0.2</code>, <code>1.5</code>, <code>25.75</code>).</div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label class="erp-caption fw-semibold text-uppercase d-block mb-1">Adjustment Reason / Note</label>
                            <textarea name="remarks" class="form-control" rows="2" style="border-radius: var(--radius-md); border: 1px solid var(--border-color);" 
                                      placeholder="e.g. Physical stock count audit, stock correction...">{{ old('remarks', $stock->remarks) }}</textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex align-items-center gap-3">
                            <!-- Single Primary Button -->
                            <button type="submit" class="erp-btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Update Stock
                            </button>
                            <a href="{{ route('warehouse_stocks.index') }}" class="erp-btn-secondary ms-auto">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
