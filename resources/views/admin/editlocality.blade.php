@extends('admin.layouts.apps')

@section('styles')
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

    {{-- Global errors --}}
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
            {{ session()->get('success') }}
        </div>
    @endif

    <form action="{{ route('updatelocality', $locality->id) }}" method="post" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-body">

                        <div class="sec-title mb-2">
                            <div>
                                <h5 class="mb-1">Edit Locality</h5>
                                <div class="form-hint">Update locality information and its apartment list.</div>
                            </div>
                            <span class="chip" id="apartmentCountChip">Apartments: {{ max(1, ($locality->apartment->count() ?? 0)) }}</span>
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
                                    value="{{ old('locality_name', $locality->locality_name) }}"
                                    placeholder="e.g. BTM Layout"
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
                                    value="{{ old('pincode', $locality->pincode) }}"
                                    placeholder="6-digit pincode"
                                    inputmode="numeric"
                                    pattern="\d{6}"
                                    title="Pincode must be a 6-digit number">
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
                            <div class="form-hint mb-2">Add, edit, or remove apartments for this locality.</div>

                            <div id="apartment-wrapper">
                                @php $existing = $locality->apartment ?? collect(); @endphp

                                {{-- Existing apartments --}}
                                @forelse($existing as $idx => $apartment)
                                    <div class="d-flex align-items-center apartment-row mb-2">
                                        <span class="index-badge" data-apt-index>{{ $idx + 1 }}</span>
                                        <input type="text"
                                               class="form-control"
                                               name="apartment_name[]"
                                               value="{{ old("apartment_name.$idx", $apartment->apartment_name) }}"
                                               placeholder="Enter Apartment name"
                                               required>
                                        <button type="button" class="btn btn-danger remove-apartment">Remove</button>
                                    </div>
                                @empty
                                    {{-- If none exist, show one empty row --}}
                                    <div class="d-flex align-items-center apartment-row mb-2">
                                        <span class="index-badge" data-apt-index>1</span>
                                        <input type="text"
                                               class="form-control"
                                               name="apartment_name[]"
                                               placeholder="Enter Apartment name"
                                               required>
                                        <button type="button" class="btn btn-success" id="add-apartment">
                                            Add
                                        </button>
                                    </div>
                                @endforelse

                                {{-- Always show one add row at the end --}}
                                <div class="d-flex align-items-center apartment-row mb-2" id="addRowContainer" @if($existing->count()===0) style="display:none" @endif>
                                    <span class="index-badge" data-apt-index>{{ $existing->count() + 1 }}</span>
                                    <input type="text"
                                           class="form-control"
                                           name="apartment_name[]"
                                           placeholder="Enter Apartment name">
                                    <button type="button" class="btn btn-success" id="add-apartment">Add</button>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        {{-- Submit --}}
                        <div class="row">
                            <div class="col-md-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
                            </div>
                        </div>

                    </div> {{-- card-body --}}
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
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

        const wrapper   = document.getElementById('apartment-wrapper');
        const addBtn    = document.getElementById('add-apartment');
        const addRow    = document.getElementById('addRowContainer');
        const countChip = document.getElementById('apartmentCountChip');

        function refreshAptIndices(){
            const rows = wrapper.querySelectorAll('.apartment-row');
            let visible = 0;
            rows.forEach((row) => {
                if (row.style.display !== 'none') visible++;
            });

            let idx = 1;
            rows.forEach((row) => {
                if (row.style.display === 'none') return;
                const badge = row.querySelector('[data-apt-index]');
                if (badge) badge.textContent = idx++;
            });

            if (countChip) countChip.textContent = 'Apartments: ' + visible;

            // Ensure there's always at least one input row
            if (visible === 0 && addRow){
                addRow.style.display = '';
            }
        }

        function createApartmentRow(){
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center apartment-row mb-2';
            row.innerHTML = `
                <span class="index-badge" data-apt-index>?</span>
                <input type="text" class="form-control" name="apartment_name[]" placeholder="Enter Apartment name" required>
                <button type="button" class="btn btn-danger remove-apartment">Remove</button>
            `;
            return row;
        }

        // Add button (present in addRow and initial empty state)
        wrapper.addEventListener('click', function(e){
            if (e.target && e.target.id === 'add-apartment'){
                // Insert before the "addRow" so it stays last
                const newRow = createApartmentRow();
                if (addRow){
                    wrapper.insertBefore(newRow, addRow);
                    addRow.querySelector('input').value = ''; // clear the addRow input
                } else {
                    wrapper.appendChild(newRow);
                }
                // If addRow was hidden initially (when no rows), show it now
                if (addRow && addRow.style.display === 'none') addRow.style.display = '';
                refreshAptIndices();
            }
            if (e.target && e.target.classList.contains('remove-apartment')){
                const row = e.target.closest('.apartment-row');
                // Don't remove the dedicated addRow
                if (row && row.id !== 'addRowContainer'){
                    row.remove();
                    refreshAptIndices();
                }
            }
        });

        // Initial indices
        refreshAptIndices();
    </script>
@endsection
