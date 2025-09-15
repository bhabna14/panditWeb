@extends('admin.layouts.apps')

@section('styles')
    <!-- SweetAlert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .section-card {
            border: 1px solid #eef1f4;
            border-radius: 14px;
        }

        .section-card .card-header {
            background: #fbfcfe;
            border-bottom: 1px solid #eef1f4;
        }

        .section-title {
            font-weight: 600;
            margin: 0;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .help-text {
            font-size: .825rem;
            color: #6c757d;
        }

        .gap-12 {
            gap: 12px;
        }

        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 12px;
            border-top: 1px solid #eef1f4;
            z-index: 2;
        }

        .row-badge {
            font-size: .8rem;
        }

        .pickup-row {
            background: #fafafa;
            border: 1px dashed #e7ecf4;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .remove-row-btn[disabled] {
            pointer-events: none;
            opacity: .5
        }

        .is-invalid+.invalid-feedback {
            display: block;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Add Flower Pickup Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.manageflowerpickupdetails') }}" class="btn btn-info text-white">
                        Manage Flower Pickup Details
                    </a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Flower Pickup Details</li>
            </ol>
        </div>
    </div>

    {{-- Optional inline flash (SweetAlert also used below) --}}
    @if (session('success'))
        <div class="alert alert-success" id="Message">{{ session('success') }}</div>
    @endif

    {{-- Top-level validation summary (optional) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the errors below.</strong>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.saveFlowerPickupAssignRider') }}" novalidate>
        @csrf

        {{-- PICKUP INFO --}}
        <div class="card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="section-title">Pickup Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label required">Vendor</label>
                        <select id="vendor_id" name="vendor_id"
                            class="form-control @error('vendor_id') is-invalid @enderror" required autofocus>
                            <option value="" selected>Choose</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->vendor_id }}" @selected(old('vendor_id') == $vendor->vendor_id)>
                                    {{ $vendor->vendor_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="pickup_date" class="form-label required">Pickup Date</label>
                        <input type="date" id="pickup_date" name="pickup_date"
                            class="form-control @error('pickup_date') is-invalid @enderror" value="{{ old('pickup_date') }}"
                            min="{{ now()->toDateString() }}" required>
                        <div class="help-text">Pickups can be scheduled from today onwards.</div>
                        @error('pickup_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- FLOWERS LIST --}}
        @php
            $oldFlowerIds = old('flower_id', ['']);
            $oldUnitIds = old('unit_id', ['']);
            $oldQtys = old('quantity', ['']);
            $rowCount = max(count($oldFlowerIds), count($oldUnitIds), count($oldQtys));
        @endphp

        <div class="card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-12">
                <h6 class="section-title m-0">Flower Items</h6>
                <div class="d-flex align-items-center gap-12">
                    <span class="badge bg-light text-dark row-badge">
                        Rows: <span id="rowCount">{{ $rowCount }}</span>
                    </span>
                    <button type="button" class="btn btn-outline-primary" id="addRowBtn">Add Row</button>
                    <button type="button" class="btn btn-outline-secondary" id="clearAllBtn">Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <div id="rowsContainer">
                    @for ($i = 0; $i < $rowCount; $i++)
                        <div class="pickup-row" data-row>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label required">Flower</label>
                                    <select name="flower_id[]"
                                        class="form-control @error('flower_id.' . $i) is-invalid @enderror" required>
                                        <option value="" selected>Choose flower</option>
                                        @foreach ($flowers as $flower)
                                            <option value="{{ $flower->product_id }}" @selected(($oldFlowerIds[$i] ?? '') == $flower->product_id)>
                                                {{ $flower->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('flower_id.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Unit</label>
                                    <select name="unit_id[]"
                                        class="form-control @error('unit_id.' . $i) is-invalid @enderror" required>
                                        <option value="" selected>Choose unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected(($oldUnitIds[$i] ?? '') == $unit->id)>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Quantity</label>
                                    <input type="number" name="quantity[]"
                                        class="form-control @error('quantity.' . $i) is-invalid @enderror"
                                        placeholder="e.g. 10" value="{{ $oldQtys[$i] ?? '' }}" inputmode="decimal"
                                        min="0.01" step="0.01" required>
                                    @error('quantity.' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button type="button" class="btn btn-outline-danger remove-row-btn">Remove</button>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
                <!-- Hidden template for new rows -->
                <template id="rowTemplate">
                    <div class="pickup-row" data-row>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label required">Flower</label>
                                <select name="flower_id[]" class="form-control" required>
                                    <option value="" selected>Choose flower</option>
                                    @foreach ($flowers as $flower)
                                        <option value="{{ $flower->product_id }}">{{ $flower->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Unit</label>
                                <select name="unit_id[]" class="form-control" required>
                                    <option value="" selected>Choose unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Quantity</label>
                                <input type="number" name="quantity[]" class="form-control" placeholder="e.g. 10"
                                    inputmode="decimal" min="0.01" step="0.01" required>
                                <div class="help-text">Use whole numbers or decimals based on unit.</div>
                            </div>
                            <div class="col-md-1 d-grid">
                                <button type="button" class="btn btn-outline-danger remove-row-btn">Remove</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- RIDER ASSIGNMENT --}}
        <div class="card section-card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="section-title m-0">Assign Rider</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="rider_id" class="form-label required">Rider</label>
                        <select id="rider_id" name="rider_id"
                            class="form-control @error('rider_id') is-invalid @enderror" required>
                            <option value="" selected>Choose</option>
                            @foreach ($riders as $rider)
                                <option value="{{ $rider->rider_id }}" @selected(old('rider_id') == $rider->rider_id)>
                                    {{ $rider->rider_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('rider_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="card section-card">
            <div class="sticky-actions d-flex justify-content-between align-items-center">
                <div class="text-muted">Review details before submitting.</div>
                <div class="d-flex gap-12">
                    <a href="{{ route('admin.manageflowerpickupdetails') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="reset" class="btn btn-light">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit Pickup</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Flash with SweetAlert --}}
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                confirmButtonColor: '#3085d6'
            });
        @endif
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation error',
                text: 'Please review the highlighted fields.'
            });
        @endif

        // Hide inline flash after 3s
        setTimeout(function() {
            const msg = document.getElementById('Message');
            if (msg) msg.style.display = 'none';
        }, 3000);
    </script>

    {{-- Dynamic rows (vanilla JS) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rowsContainer = document.getElementById('rowsContainer');
            const rowTemplate = document.getElementById('rowTemplate');
            const addRowBtn = document.getElementById('addRowBtn');
            const clearAllBtn = document.getElementById('clearAllBtn');
            const rowCountBadge = document.getElementById('rowCount');

            function updateRowCount() {
                const count = rowsContainer.querySelectorAll('[data-row]').length;
                rowCountBadge.textContent = count.toString();
                // Disable remove if only one row remains
                const removeBtns = rowsContainer.querySelectorAll('.remove-row-btn');
                removeBtns.forEach(btn => btn.disabled = (count <= 1));
            }

            function addRow(focus = true) {
                const fragment = rowTemplate.content.cloneNode(true);
                const node = fragment.querySelector('[data-row]');
                rowsContainer.appendChild(node);
                bindRow(node);
                updateRowCount();
                if (focus) {
                    const firstSelect = node.querySelector('select[name="flower_id[]"]');
                    if (firstSelect) firstSelect.focus();
                }
            }

            function bindRow(rowEl) {
                const removeBtn = rowEl.querySelector('.remove-row-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        const total = rowsContainer.querySelectorAll('[data-row]').length;
                        if (total > 1) {
                            rowEl.remove();
                            updateRowCount();
                        }
                    });
                }
            }

            // Bind existing server-rendered rows
            rowsContainer.querySelectorAll('[data-row]').forEach(bindRow);

            // Add row
            if (addRowBtn) addRowBtn.addEventListener('click', () => addRow(true));

            // Clear all fields in all rows (keeps 1 row)
            if (clearAllBtn) clearAllBtn.addEventListener('click', function() {
                const rows = Array.from(rowsContainer.querySelectorAll('[data-row]'));
                rows.forEach((row, idx) => {
                    // Clear inputs
                    row.querySelectorAll('input').forEach(i => i.value = '');
                    row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                    // Remove extra rows
                    if (idx > 0) row.remove();
                });
                updateRowCount();
            });

            updateRowCount();
        });
    </script>
@endsection
