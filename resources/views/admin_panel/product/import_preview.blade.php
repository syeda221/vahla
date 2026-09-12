@extends('admin_panel.layout.app')
@section('title', 'Import Preview')

@section('content')
<div class="content-wrapper text-sm">
    <div class="content-header pb-2">
        <div class="container-fluid">
            <div class="row align-items-center mb-1">
                <div class="col-sm-6">
                    <h5 class="m-0 text-dark fw-bold">Import Products (Preview)</h5>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-sm"><i class="las la-arrow-left"></i> Cancel</a>
                    @if(count($payload['products']) > 0)
                    <button type="button" id="btnConfirmImport" class="btn btn-success btn-sm"><i class="las la-check"></i> Confirm & Import</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <div id="importProgressContainer" class="card shadow-sm border-0 mb-3" style="display: none;">
                <div class="card-body">
                    <h5 class="fw-bold text-success mb-3"><i class="las la-spinner la-spin"></i> Importing Products... Please don't close this page.</h5>
                    <div class="progress" style="height: 25px;">
                        <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <div class="mt-2 text-muted text-center" id="importProgressText">Processing 0 out of X</div>
                </div>
            </div>

            <div class="row" id="statsRow">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="las la-plus"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Products to Create</span>
                            <span class="info-box-number">{{ $payload['preview_stats']['products_create'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="las la-edit"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Products to Update</span>
                            <span class="info-box-number">{{ $payload['preview_stats']['products_update'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning"><i class="las la-tags"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Variants to Process</span>
                            <span class="info-box-number">{{ $payload['preview_stats']['variants_create'] + $payload['preview_stats']['variants_update'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-secondary"><i class="las la-database"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Master Data to Auto-create</span>
                            <span class="info-box-number">{{ $payload['preview_stats']['master_create'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($payload['errors']) && count($payload['errors']) > 0)
            <div class="alert alert-danger shadow-sm">
                <strong><i class="las la-times-circle"></i> Validation Errors ({{ count($payload['errors']) }}):</strong>
                <ul class="mb-0 mt-2" style="max-height: 150px; overflow-y: auto;">
                    @foreach($payload['errors'] as $error)
                        <li>Row {{ $error['row'] }}: {{ $error['msg'] }}</li>
                    @endforeach
                </ul>
                <div class="mt-2 text-sm fw-bold">Please fix these errors in your CSV file and upload again. (Rows with errors will be skipped if you proceed).</div>
            </div>
            @endif

            @if(count($payload['master_data']['categories']) > 0 || count($payload['master_data']['brands']) > 0)
            <div class="alert alert-warning shadow-sm">
                <strong><i class="las la-exclamation-triangle"></i> Notice:</strong> The following master data is missing and will be auto-created:<br>
                @if(count($payload['master_data']['categories']) > 0)
                    <strong>Categories:</strong> {{ implode(', ', $payload['master_data']['categories']) }}<br>
                @endif
                @if(count($payload['master_data']['brands']) > 0)
                    <strong>Brands:</strong> {{ implode(', ', $payload['master_data']['brands']) }}<br>
                @endif
            </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h3 class="card-title fw-bold">Data Preview</h3>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-bordered mb-0" style="font-size:0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>Ref / Status</th>
                                <th>Name / Master Data</th>
                                <th>Variants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $previewProducts = array_slice($payload['products'], 0, 50, true);
                                $totalProducts = count($payload['products']);
                            @endphp
                            @forelse($previewProducts as $ref => $pData)
                                @php
                                    $exists = App\Models\Product::where('item_code', $ref)->exists();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $ref }}</strong><br>
                                        @if($exists)
                                            <span class="badge bg-info">Update</span>
                                        @else
                                            <span class="badge bg-success">Create</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $pData['name'] }}</strong><br>
                                        <small class="text-muted">
                                            Cat: {{ $pData['category'] ?? '-' }} | Brand: {{ $pData['brand'] ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="p-0">
                                        <table class="table table-sm table-borderless mb-0">
                                            <thead class="bg-light text-muted" style="font-size:0.75rem;">
                                                <tr>
                                                    <th>Variant Name</th>
                                                    <th>Size/Color</th>
                                                    <th>Stock</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pData['variants'] as $v)
                                                <tr>
                                                    <td>{{ $v['name'] }}</td>
                                                    <td>{{ $v['size'] }} / {{ $v['color'] }}</td>
                                                    <td>{{ $v['stock'] }}</td>
                                                    <td>{{ $v['sale_price'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">No valid data found to import.</td>
                                </tr>
                            @endforelse
                            @if($totalProducts > 50)
                                <tr>
                                    <td colspan="3" class="text-center py-3 bg-light text-muted">
                                        <em>... and {{ $totalProducts - 50 }} more products not shown in preview ...</em>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#btnConfirmImport').click(function(e) {
        e.preventDefault();
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Starting Import...');
        $('#importProgressContainer').show();
        $('#statsRow, .alert, .card.shadow-sm').hide(); // Hide warnings/stats/table
        
        processImportChunk(0);
    });
    
    function processImportChunk(offset) {
        $.ajax({
            url: "{{ route('products.import.confirm') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                offset: offset,
                chunk_size: 250
            },
            success: function(res) {
                if (res.status === 'success') {
                    // Import is complete
                    $('#importProgressBar').css('width', '100%').text('100%');
                    $('#importProgressText').text('Import complete! Redirecting...');
                    $('#btnConfirmImport').html('<i class="las la-check"></i> Complete!');
                    window.location.href = "{{ route('product') }}";
                } else if (res.status === 'continue') {
                    // Update progress bar
                    let pct = Math.round((res.processed / res.total) * 100);
                    $('#importProgressBar').css('width', pct + '%').text(pct + '%');
                    $('#importProgressText').text('Processed ' + res.processed + ' out of ' + res.total + ' products. Please wait...');
                    $('#btnConfirmImport').html('<i class="las la-spinner la-spin"></i> ' + pct + '% (' + res.processed + '/' + res.total + ')');
                    
                    // Call next chunk
                    processImportChunk(res.next_offset);
                } else {
                    alert('Import failed: ' + (res.message || 'Unknown error'));
                    $('#btnConfirmImport').prop('disabled', false).html('<i class="las la-check"></i> Retry Import');
                    $('#importProgressContainer').hide();
                }
            },
            error: function(err) {
                console.error(err);
                alert('A network error occurred during import. Check console for details.');
                $('#btnConfirmImport').prop('disabled', false).html('<i class="las la-check"></i> Retry Import');
                $('#importProgressContainer').hide();
            }
        });
    }
});
</script>
@endsection
