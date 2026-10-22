@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Modern Compact UI variables */
        @import url('/assets/fonts/noto-nastaliq/noto-nastaliq.css');

        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-light: #f8fafc;
            --input-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --text-dark: #1e293b;
        }

        /* Main Container */
        .main-content {
            overflow-y: auto;
        }

        .main-content-inner {
            padding: 14px;
        }

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-color);
            padding: 24px;
            min-height: calc(100vh - 140px);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
            gap: 12px;
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            color: var(--primary-color);
            background: #e0e7ff;
            padding: 8px;
            border-radius: 8px;
            font-size: 1.05rem;
        }

        /* Form Grid - Desktop (≥ 992px): 12-columns */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 14px 16px;
            overflow-y: auto;
            padding-right: 4px;
        }

        /* Modern Inputs */
        .input-group-modern {
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .modern-label {
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 20px;
            line-height: 1.2;
        }

        .modern-control {
            background: var(--input-bg);
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 0.92rem;
            color: var(--text-dark);
            transition: all 0.2s ease;
            width: 100%;
            height: 44px;
            box-sizing: border-box;
        }

        .modern-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
            outline: none;
            background: white;
        }

        .modern-control::placeholder {
            color: #cbd5e1;
        }

        select.modern-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 2.2rem;
            cursor: pointer;
        }

        textarea.modern-control {
            resize: none;
            min-height: 44px;
            height: auto;
        }

        /* Sections */
        .section-label {
            grid-column: 1 / -1;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary-color);
            margin-top: 10px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        /* Desktop Action Buttons */
        .desktop-action-bar {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-modern-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
            cursor: pointer;
            text-decoration: none;
        }

        .btn-modern-primary:hover {
            background: var(--primary-hover);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.4);
        }

        .btn-modern-secondary {
            background: #f1f5f9;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 9px 18px;
            font-weight: 600;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-modern-secondary:hover {
            background: #e2e8f0;
            color: var(--text-dark);
        }

        /* Scrollbar Styling */
        .form-grid::-webkit-scrollbar {
            width: 6px;
        }

        .form-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .form-grid::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        /* Mobile Sticky Action Bar (Hidden on desktop) */
        .mobile-action-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 10px 14px calc(10px + env(safe-area-inset-bottom, 0px)) 14px;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
            gap: 10px;
        }

        .mobile-action-bar .mob-btn-cancel {
            flex: 1;
            height: 46px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .mobile-action-bar .mob-btn-save {
            flex: 2;
            height: 46px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #fff;
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.35);
        }

        /* Desktop Column Grid Classes (≥ 992px) */
        @media (min-width: 992px) {
            .col-span-2 { grid-column: span 2; }
            .col-span-3 { grid-column: span 3; }
            .col-span-4 { grid-column: span 4; }
            .col-span-6 { grid-column: span 6; }
            .col-span-12 { grid-column: span 12; }
        }

        /* Tablet (768px to 991.98px): Clean 2-column grid */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 14px 16px !important;
            }
            .col-span-2, .col-span-3, .col-span-4, .col-span-6 {
                grid-column: span 1 !important;
            }
            .col-span-12, .col-tab-full {
                grid-column: span 2 !important;
            }
        }

        /* ═══════════════════════════════════════════════════════
           MOBILE RESPONSIVE (≤ 767.98px)
           Clean, spacious 1-column layout so labels never wrap
           and inputs are full width and perfectly aligned!
        ═══════════════════════════════════════════════════════ */
        @media (max-width: 767.98px) {
            html, body {
                overflow-x: hidden !important;
                max-width: 100vw !important;
            }

            .main-content {
                overflow-y: auto !important;
                height: auto !important;
                min-height: 100vh;
            }

            .main-content-inner {
                padding: 10px 8px !important;
            }

            .modern-card {
                height: auto !important;
                min-height: unset !important;
                padding: 16px 14px calc(85px + env(safe-area-inset-bottom, 0px)) 14px !important;
                border-radius: 12px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            }

            .page-header {
                margin-bottom: 14px;
                padding-bottom: 12px;
            }

            .page-title {
                font-size: 1.15rem;
            }

            .page-title i {
                padding: 6px;
                font-size: 0.95rem;
            }

            /* Hide desktop header buttons on mobile */
            .desktop-action-bar {
                display: none !important;
            }

            /* Show mobile sticky action bar */
            .mobile-action-bar {
                display: flex !important;
            }

            /* Single-column full-width grid on mobile */
            .form-grid {
                grid-template-columns: 1fr !important;
                gap: 14px 0 !important;
                overflow: visible !important;
                padding-right: 0 !important;
            }

            .input-group-modern {
                grid-column: span 1 !important;
                width: 100% !important;
            }

            .section-label {
                grid-column: span 1 !important;
                font-size: 0.78rem;
                margin-top: 8px;
                margin-bottom: 4px;
            }

            .modern-control {
                font-size: 15px !important; /* Prevents auto-zoom in Safari iOS */
                height: 46px !important;
                border-radius: 10px;
                padding: 10px 14px;
            }

            .modern-label {
                font-size: 0.72rem;
                margin-bottom: 6px;
            }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid p-0">

                <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="needs-validation modern-card" novalidate>
                    @csrf

                    <!-- Header -->
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fa fa-user-pen"></i> Edit Customer
                        </h1>
                        <div class="desktop-action-bar">
                            <a href="{{ route('customers.index') }}" class="btn-modern-secondary">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn-modern-primary">
                                <i class="fa fa-check me-1"></i> Update Customer
                            </button>
                        </div>
                    </div>

                    <!-- Form Content Grid -->
                    <div class="form-grid">

                        <!-- Section 1 -->
                        <div class="section-label mt-0">Basic Information</div>

                        <div class="input-group-modern col-span-2">
                            <label class="modern-label">
                                <span>Customer Code</span>
                            </label>
                            <input type="text" class="modern-control bg-light" name="customer_id"
                                value="{{ $customer->customer_id }}" readonly>
                        </div>

                        <div class="input-group-modern col-span-2">
                            <label class="modern-label">
                                <span>Customer Type <span class="text-danger">*</span></span>
                            </label>
                            <select class="modern-control" name="customer_type" required>
                                @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->name }}" {{ $customer->customer_type == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group-modern col-span-3 col-tab-full">
                            <label class="modern-label">
                                <span>Full Name <span class="text-danger">*</span></span>
                            </label>
                            <input type="text" class="modern-control" name="customer_name" required
                                value="{{ $customer->customer_name }}" placeholder="Customer Name">
                        </div>

                        <div class="input-group-modern col-span-2">
                            <label class="modern-label">
                                <span>Mobile</span>
                            </label>
                            <input type="text" class="modern-control" name="mobile" placeholder="0300-1234567"
                                value="{{ $customer->mobile }}">
                        </div>

                        <div class="input-group-modern col-span-3">
                            <label class="modern-label">
                                <span>Region (Zone)</span>
                            </label>
                            <select class="modern-control" name="zone">
                                <option value="">-- Select Zone --</option>
                                @foreach($zones as $z)
                                    <option value="{{ $z->id }}" {{ $customer->zone == $z->id ? 'selected' : '' }}>{{ $z->zone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Financials / Address Line 2 -->
                        <div class="input-group-modern col-span-3 col-tab-full">
                            <label class="modern-label">
                                <span>Address</span>
                            </label>
                            <input type="text" class="modern-control" name="address"
                                placeholder="Shop No, Street Area, City"
                                value="{{ $customer->address }}">
                        </div>

                        <div class="input-group-modern col-span-3">
                            <label class="modern-label text-danger">
                                <span>Opening Balance (Dr)</span>
                            </label>
                            <input type="number" step="0.01" class="modern-control" name="opening_balance"
                                value="{{ $customer->opening_balance ?? 0 }}" style="border-color: #fca5a5; background: #fff5f5; font-weight: 600;">
                        </div>

                        <div class="input-group-modern col-span-3">
                            <label class="modern-label text-success">
                                <span>Credit Limit</span>
                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 10px; padding: 2px 6px;">0 = Unlimited</span>
                            </label>
                            <input type="number" step="0.01" class="modern-control" name="balance_range"
                                value="{{ $customer->balance_range ?? 0 }}" style="border-color: #86efac; background: #f0fdf4; font-weight: 600;">
                        </div>

                        <div class="input-group-modern col-span-3 col-tab-full">
                            <label class="modern-label text-primary">
                                <span>Payment Reminder Day</span>
                            </label>
                            <select class="modern-control" name="reminder_day" style="border-color: #93c5fd; background: #eff6ff;">
                                <option value="">No Reminder</option>
                                <option value="Monday" {{ $customer->reminder_day == 'Monday' ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ $customer->reminder_day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ $customer->reminder_day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ $customer->reminder_day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="Friday" {{ $customer->reminder_day == 'Friday' ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ $customer->reminder_day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ $customer->reminder_day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>
                        </div>

                        {{-- ============ COMMENTED OUT FIELDS PRESERVED ============ --}}
                        {{-- Urdu Name --}}
                        {{-- <div class="input-group-modern col-span-2">
                            <label class="modern-label ms-auto"
                                style="font-family: 'Noto Nastaliq Urdu', serif;">کسٹمر کا نام</label>
                            <input type="text" class="modern-control text-end" dir="rtl" name="customer_name_ur"
                                value="{{ $customer->customer_name_ur }}"
                                style="font-family: 'Noto Nastaliq Urdu', serif;">
                        </div> --}}

                        {{-- CNIC / NTN --}}
                        {{-- <div class="input-group-modern col-span-3">
                            <label class="modern-label">NTN / CNIC</label>
                            <input type="text" class="modern-control" name="cnic" value="{{ $customer->cnic }}">
                        </div> --}}

                        {{-- Filer Type --}}
                        {{-- <div class="input-group-modern col-span-3">
                            <label class="modern-label">Filer Type</label>
                            <select class="modern-control" name="filer_type">
                                <option value="filer" {{ $customer->filer_type == 'filer' ? 'selected' : '' }}>Filer</option>
                                <option value="non filer" {{ $customer->filer_type == 'non filer' ? 'selected' : '' }}>Non Filer</option>
                                <option value="exempt" {{ $customer->filer_type == 'exempt' ? 'selected' : '' }}>Exempt</option>
                            </select>
                        </div> --}}

                        {{-- Contact Person --}}
                        {{-- <div class="input-group-modern col-span-3">
                            <label class="modern-label">Contact Person</label>
                            <input type="text" class="modern-control" name="contact_person" value="{{ $customer->contact_person }}">
                        </div> --}}

                        {{-- Email --}}
                        {{-- <div class="input-group-modern col-span-4">
                            <label class="modern-label">Email</label>
                            <input type="email" class="modern-control" name="email_address" value="{{ $customer->email_address }}">
                        </div> --}}

                        {{-- Secondary Contact --}}
                        {{-- <div class="section-label">Secondary Contact</div>
                        <div class="input-group-modern col-span-4">
                            <input type="text" class="modern-control" name="contact_person_2" value="{{ $customer->contact_person_2 }}">
                        </div>
                        <div class="input-group-modern col-span-4">
                            <input type="text" class="modern-control" name="mobile_2" value="{{ $customer->mobile_2 }}">
                        </div>
                        <div class="input-group-modern col-span-4">
                            <input type="email" class="modern-control" name="email_address_2" value="{{ $customer->email_address_2 }}">
                        </div> --}}

                    </div>
                    <!-- End Grid -->

                    <!-- Mobile Sticky Action Bar -->
                    <div class="mobile-action-bar">
                        <a href="{{ route('customers.index') }}" class="btn-modern-secondary mob-btn-cancel">
                            <i class="fa fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn-modern-primary mob-btn-save">
                            <i class="fa fa-check me-1"></i> Update Customer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let nameField = document.querySelector('input[name="customer_name"]');
            if (nameField && window.innerWidth > 768) {
                nameField.focus();
            }

            const form = document.querySelector('form.needs-validation');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        let invalid = form.querySelector(':invalid');
                        if (invalid) {
                            invalid.focus();
                            invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return;
                    }
                    Swal.fire({
                        title: 'Confirm Update',
                        text: "Are you sure you want to update this customer?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Update!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection
