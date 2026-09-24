@extends('admin_panel.layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">ERP Settings</h3>
                    </div>
                    <div class="card-body">
                        <!-- Advanced Settings & Navigation -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted mb-3 font-weight-bold text-uppercase"
                                style="font-size: 0.8rem; letter-spacing: 1px;">Advanced Actions</h6>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('settings.return-policy') }}"
                                    class="btn btn-outline-primary mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-undo-alt mr-2"></i> Return Policy
                                </a>
                                <a href="{{ route('settings.return-approvers') }}"
                                    class="btn btn-outline-info mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-user-shield mr-2"></i> Return Approvers
                                </a>
                                <a href="#" class="btn btn-outline-dark mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-exchange-alt mr-2"></i> Switch Account
                                </a>
                            </div>
                        </div>

                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="company-tab" data-toggle="tab" href="#company"
                                    role="tab">
                                    <i class="fas fa-building"></i> Company
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sales-tab" data-toggle="tab" href="#sales" role="tab">
                                    <i class="fas fa-shopping-cart"></i> Sales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inventory-tab" data-toggle="tab" href="#inventory" role="tab">
                                    <i class="fas fa-boxes"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="accounting-tab" data-toggle="tab" href="#accounting" role="tab">
                                    <i class="fas fa-calculator"></i> Accounting
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="series-tab" data-toggle="tab" href="#series" role="tab">
                                    <i class="fas fa-barcode"></i> Document Series & Numbering
                                </a>
                            </li>
                        </ul>

                        @php
                            $canEditSettings = auth()->user()->hasAnyPermission(['settings.edit', 'settings.update']);
                        @endphp
                        <form id="settingsForm" class="mt-4" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content" id="settingsTabContent">
                                <!-- Company Tab -->
                                <div class="tab-pane fade show active" id="company" role="tabpanel">
                                    @if (isset($settings['company']))
                                        @foreach ($settings['company'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                @if ($setting['type'] === 'text')
                                                    <textarea name="settings[{{ $setting['key'] }}]" class="form-control" rows="3" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                @elseif ($setting['type'] === 'image')
                                                    @if($setting['value'])
                                                        <div class="mb-2">
                                                            <img src="{{ asset($setting['value']) }}" alt="Logo" style="max-height: 80px;" class="border rounded p-1">
                                                        </div>
                                                    @endif
                                                    <input type="file" name="settings_files[{{ $setting['key'] }}]" class="form-control-file" accept="image/*" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                    <input type="hidden" name="settings[{{ $setting['key'] }}]" value="{{ $setting['value'] }}">
                                                @else
                                                    <input type="text" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @endif
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Sales Tab -->
                                <div class="tab-pane fade" id="sales" role="tabpanel">
                                    @if (isset($settings['sales']))
                                        @foreach ($settings['sales'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                @if ($setting['type'] === 'text')
                                                    <textarea name="settings[{{ $setting['key'] }}]" class="form-control" rows="3" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                @elseif($setting['type'] === 'integer')
                                                    <input type="number" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @else
                                                    <input type="text" name="settings[{{ $setting['key'] }}]"
                                                        class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @endif
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Inventory Tab -->
                                <div class="tab-pane fade" id="inventory" role="tabpanel">
                                    @if (isset($settings['inventory']))
                                        @foreach ($settings['inventory'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                <input type="number" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Accounting Tab -->
                                <div class="tab-pane fade" id="accounting" role="tabpanel">
                                    @if (isset($settings['accounting']))
                                        @foreach ($settings['accounting'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                <input type="text" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Document Series Tab -->
                                <div class="tab-pane fade" id="series" role="tabpanel">
                                    <div class="alert alert-info py-2 mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Document & Invoice Series Configuration:</strong> Yahan se aap tamam modules (Sales Orders, Invoices, Purchase Orders, Quotations, Returns, Delivery Challans, Direct GRN waghaira) ka starting number aur format set kar sakte hain. Agar aap creation form par direct custom number likhenge to system agla number wahan se automatically resume karega.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 25%;">Document Type</th>
                                                    <th style="width: 15%; text-align: center;">Prefix</th>
                                                    <th style="width: 20%;">Next Number (Starting No)</th>
                                                    <th style="width: 20%;">Digit Padding</th>
                                                    <th style="width: 20%; text-align: center;">Live Preview</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($allSeries))
                                                    @foreach($allSeries as $idx => $s)
                                                        @php
                                                            $def = $standardDefs[$s->prefix] ?? null;
                                                            $label = $def ? $def['name'] : ($s->description ?: $s->prefix);
                                                            $pad = $s->padding ?? 4;
                                                            $next = $s->next_number ?? 1;
                                                            $preview = $s->prefix . '-' . str_pad($next, $pad, '0', STR_PAD_LEFT);
                                                        @endphp
                                                        <tr class="series-row" data-prefix="{{ $s->prefix }}">
                                                            <td>
                                                                <span class="font-weight-bold">{{ $label }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 13px;">{{ $s->prefix }}</span>
                                                                <input type="hidden" class="series-prefix-input" value="{{ $s->prefix }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" min="1" class="form-control series-next-input" value="{{ $next }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                            </td>
                                                            <td>
                                                                <select class="form-control series-pad-select" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                                    @for($p = 1; $p <= 6; $p++)
                                                                        <option value="{{ $p }}" {{ $pad == $p ? 'selected' : '' }}>{{ $p }} digits (e.g. {{ str_pad(1, $p, '0', STR_PAD_LEFT) }})</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-success px-3 py-2 series-preview-badge" style="font-size: 14px; letter-spacing: 0.5px;">{{ $preview }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($canEditSettings)
                                        <div class="mt-3">
                                            <button type="button" id="btnSaveSeries" class="btn btn-success px-4">
                                                <i class="fas fa-save"></i> Save Numbering Series
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4" id="mainSettingsSaveWrapper">
                                @if($canEditSettings)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary" disabled style="opacity: 0.6; cursor: not-allowed;">
                                        <i class="fas fa-lock"></i> Save Settings (Read Only)
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Live preview updater for series
                function updateSeriesPreview(row) {
                    let prefix = $(row).find('.series-prefix-input').val();
                    let nextNum = parseInt($(row).find('.series-next-input').val()) || 1;
                    let pad = parseInt($(row).find('.series-pad-select').val()) || 4;
                    let padded = String(nextNum).padStart(pad, '0');
                    $(row).find('.series-preview-badge').text(prefix + '-' + padded);
                }

                $(document).on('input change', '.series-next-input, .series-pad-select', function() {
                    let row = $(this).closest('.series-row');
                    updateSeriesPreview(row);
                });

                // Tab change toggle save button visibility
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    if ($(e.target).attr('href') === '#series') {
                        $('#mainSettingsSaveWrapper').hide();
                    } else {
                        $('#mainSettingsSaveWrapper').show();
                    }
                });

                // Save Series Batch
                $('#btnSaveSeries').on('click', function(e) {
                    e.preventDefault();
                    let seriesData = [];
                    $('.series-row').each(function() {
                        let prefix = $(this).find('.series-prefix-input').val();
                        let nextNum = $(this).find('.series-next-input').val();
                        let pad = $(this).find('.series-pad-select').val();
                        if (prefix && nextNum) {
                            seriesData.push({
                                prefix: prefix,
                                next_number: nextNum,
                                padding: pad
                            });
                        }
                    });

                    let $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                    $.ajax({
                        url: '{{ route('settings.invoice_series.update') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            series: seriesData
                        },
                        success: function(response) {
                            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Numbering Series');
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message || 'Document series updated successfully!',
                                timer: 2000
                            });
                        },
                        error: function(xhr) {
                            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Numbering Series');
                            let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save document series.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: msg
                            });
                        }
                    });
                });

                // General Settings Form Submit
                $('#settingsForm').on('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    
                    $.ajax({
                        url: '{{ route('settings.update') }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update settings',
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
