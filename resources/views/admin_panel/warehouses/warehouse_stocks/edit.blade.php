@extends('admin_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('warehouse_stocks.index') }}" class="text-decoration-none text-muted">Stock Inventory</a></li>
                    <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Adjust Stock</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i> Adjust Warehouse Stock</h4>
            <p class="text-muted small mb-0">Update recorded stock quantity for <strong>{{ $stock->product->item_name ?? 'Product' }}</strong> in <strong>{{ $stock->warehouse->warehouse_name ?? 'Warehouse' }}</strong></p>
        </div>
        <div>
            <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Back to Inventory
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Please correct the following errors:</div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark"><i class="fas fa-sliders-h me-2 text-primary"></i> Stock Adjustment Form</span>
                    @if($stock->warehouse && $stock->warehouse->is_shop)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">🏪 Main Shop</span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">🏢 Storage Godown</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <!-- Product Info Header -->
                    <div class="d-flex align-items-center p-3 bg-light rounded-3 mb-4 border">
                        <div class="rounded-3 bg-white p-2 border me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            @if($stock->product && $stock->product->image)
                                <img src="{{ asset('uploads/products/'.$stock->product->image) }}" class="rounded" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fas fa-box fa-2x text-primary"></i>
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $stock->product->item_name ?? 'N/A' }}</h6>
                            <span class="small text-muted font-monospace">{{ $stock->product->item_code ?? '' }}</span>
                            <span class="badge bg-secondary-subtle text-dark ms-2">{{ $stock->product->unit->name ?? 'Pcs' }}</span>
                        </div>
                    </div>

                    <form action="{{ route('warehouse_stocks.update', $stock->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Location Readonly -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase">Location / Warehouse</label>
                            <input type="text" class="form-control" value="{{ $stock->warehouse->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ' }}{{ $stock->warehouse->warehouse_name ?? '' }}" disabled>
                        </div>

                        <!-- Current Stock Display -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase">Current Recorded Quantity</label>
                            <div class="p-3 bg-light rounded-3 border font-monospace fw-bold fs-5 text-dark">
                                {{ rtrim(rtrim(number_format((float)$stock->total_pieces, 2, '.', ''), '0'), '.') }} 
                                <span class="fs-6 fw-normal text-muted">{{ $stock->product->unit->name ?? 'Pcs' }}</span>
                            </div>
                        </div>

                        <!-- New Quantity Input -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase">
                                New Adjusted Quantity <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="any" name="quantity" id="quantity" 
                                       class="form-control form-control-lg fw-bold text-primary font-monospace" 
                                       value="{{ old('quantity', rtrim(rtrim(number_format((float)$stock->total_pieces, 2, '.', ''), '0'), '.')) }}" required>
                                <span class="input-group-text fw-bold bg-white text-muted">{{ $stock->product->unit->name ?? 'Pcs' }}</span>
                            </div>
                            <div class="form-text small text-muted">Enter the new total physical stock count. Supports decimals (e.g. <code>0.2</code>, <code>1.5</code>, <code>25.75</code>).</div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">Adjustment Reason / Note</label>
                            <textarea name="remarks" class="form-control rounded-3" rows="2" 
                                      placeholder="e.g. Physical stock count audit, stock correction...">{{ old('remarks', $stock->remarks) }}</textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-save me-1"></i> Update Stock
                            </button>
                            <a href="{{ route('warehouse_stocks.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill ms-auto">
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
