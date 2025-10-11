@extends('admin.layouts.apps')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

    <style>
        :root{ --line:#e5e7eb; --soft:#f8fafc; --ink:#0f172a; --muted:#64748b; --pri:#0ea5e9; }
        .card.custom-card{ border-radius:14px; }
        .form-hint{ color:var(--muted); font-size:.9rem; }
        .chip{ background:#eef2ff; color:#3730a3; border:1px solid #e2e8f0; border-radius:999px; padding:.25rem .55rem; }
        .sec-title{ display:flex; align-items:center; justify-content:space-between; gap:.75rem; }
        .apartment-row{ gap:.5rem; }
        .apartment-row .index-badge{ min-width:36px; height:36px; border-radius:10px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-weight:600; color:#0f172a; }
        .divider{ border-top:1px dashed var(--line); margin:.75rem 0 1rem; }
        .is-invalid{ border-color:#dc3545 !important; }
        .invalid-feedback{ display:block; }
    </style>
@endsection

@section('content')
    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">
            {{ $errors->first('danger') }}
        </div>
    @endif

    <form action="{{ route('savelocality') }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="sec-title mb-2">
                            <div>
                                <h5 class="mb-1">Add Locality</h5>
                                <div class="form-hint">Create a locality and optionally list its apartments.</div>
                            </div>
                            <span class="chip" id="apartmentCountChip">Apartments: 1</span>
                        </div>

                        <div class="divider"></div>

                        {{-- Locality & Pincode --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="locality_name" class="form-label">Locality Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('locality_name') is-invalid @enderror"
                                    id="locality_name"
                                    name="locality_name"
                                    placeholder="e.g. BTM Layout"
                                    value="{{ old('locality_name') }}"
                                    required>
                                @error('locality_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-hint">Use a clear, human-readable name.</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input
                                    type="text"
                                    class="form-control @error('pincode') is-invalid @enderror"
                                    id="pincode"
                                    name="pincode"
                                    placeholder="6-digit pincode"
                                    inputmode="numeric"
                                    pattern="\d{6}"
                                    title="Pincode must be a 6-digit number"
                                    value="{{ old('pincode') }}">
                                @error('pincode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-hint">Indian pincode is 6 digits.</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Apartments --}}
                        <div class="mt-4">
                            <label class="form-label">Apartment Names</label>
                            <div class="form-hint mb-2">Add one or more apartments in this locality.</div>

                            <div id="apartment-wrapper">
                                {{-- Row template 1 --}}
                                <div class="d-flex align-items-center apartment-row mb-2">
                                    <span class="index-badge" data-apt-index>1</span>
                                    <input type="text"
                                           class="form-control"
                                           name="apartment_name[]"
                                           placeholder="Enter Apartment name"
                                           value="{{ old('apartment_name.0') }}"
                                           required>
                                    <button type="button" class="btn btn-success" id="add-apartment">
                                        <i class="bi bi-plus-lg"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        {{-- Submit --}}
                        <div class="row">
                            <div class="col-md-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Submit
                                </button>
                                <button type="reset" class="btn btn-light">
                                    Reset
                                </button>
                            </div>
                        </div>

                    </div> {{-- card-body --}}
                </div>
            </div>
        </div>
    </form>
@endsection

@section('modal')
@endsection

@section('scripts')
    <!-- Form-layouts js -->
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>

    <!-- jQuery (needed for Select2, if you add selects later) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        // Auto-hide flash messages
        setTimeout(function(){
            var el = document.getElementById('Message');
            if(el){ el.style.display = 'none'; }
        }, 3000);

        // Pincode: digits only, max 6
        const pincode = document.getElementById('pincode');
        if (pincode){
            pincode.addEventListener('input', function(){
                this.value = this.value.replace(/\D/g,'').slice(0,6);
            });
        }

        // Apartments add/remove + live index + chip count
        const wrapper = document.getElementById('apartment-wrapper');
        const addBtn  = document.getElementById('add-apartment');
        const chip    = document.getElementById('apartmentCountChip');

        function refreshAptIndices(){
            const rows = wrapper.querySelectorAll('.apartment-row');
            rows.forEach((row, idx) => {
                const badge = row.querySelector('[data-apt-index]');
                if (badge) badge.textContent = (idx + 1);
                // First row keeps the Add button, others get Remove
                const addButton = row.querySelector('#add-apartment');
                if (addButton && idx !== 0) addButton.remove();
            });
            // Count chip
            if (chip) chip.textContent = 'Apartments: ' + rows.length;
        }

        function createApartmentRow(){
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center apartment-row mb-2';
            row.innerHTML = `
                <span class="index-badge" data-apt-index>?</span>
                <input type="text" class="form-control" name="apartment_name[]" placeholder="Enter Apartment name" required>
                <button type="button" class="btn btn-danger remove-apartment">
                    <i class="bi bi-x-lg"></i> Remove
                </button>
            `;
            return row;
        }

        if (addBtn){
            addBtn.addEventListener('click', function(){
                wrapper.appendChild(createApartmentRow());
                refreshAptIndices();
            });
        }

        // Remove via delegation
        wrapper.addEventListener('click', function(e){
            if (e.target.closest('.remove-apartment')){
                const row = e.target.closest('.apartment-row');
                if (!row) return;
                // Keep at least one row
                if (wrapper.querySelectorAll('.apartment-row').length > 1){
                    row.remove();
                    refreshAptIndices();
                }
            }
        });

        // Initial indices
        refreshAptIndices();
    </script>
@endsection
