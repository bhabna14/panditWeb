@extends('admin.layouts.apps')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">FESTIVAL CALENDAR</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.manageFestivalCalendar') }}" class="btn btn-warning text-dark">
                        Manage Calendar
                    </a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Calendar</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.saveFestivalCalendar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="festival_name" class="form-label">Festival Name</label>
                        <input type="text" class="form-control" id="festival_name" name="festival_name" required>
                    </div>

                    <div class="col-md-6">
                        <label for="festival_date" class="form-label">Festival Date</label>
                        <input type="date" class="form-control" id="festival_date" name="festival_date" required>
                    </div>

                    <div class="col-md-6">
                        <label for="festival_image" class="form-label">Festival Image</label>
                        <input type="file" class="form-control" id="festival_image" name="festival_image"
                            accept="image/*">
                    </div>

                    <div class="col-md-6">
                        <label for="package_price" class="form-label">Package Price</label>
                        <input type="number" class="form-control" name="package_price" placeholder="Package Price">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Related Flowers</label>
                        <div id="flower-price-container">
                            <div class="row g-2 align-items-center flower-price-group mb-2">
                                <div class="col-md-8">
                                    <select class="form-control" name="related_flower[]">
                                        <option value="">Select Flower</option>
                                        @foreach ($flowerNames as $flower)
                                            <option value="{{ $flower->name }}">{{ $flower->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success add-flower-price w-100">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Packages</label>
                        <div id="package-container">
                            <div class="input-group mb-2 package-group">
                                <select name="product_id[]" class="form-select">
                                    <option value="">Select Package</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->product_id }}">{{ $package->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success add-package" title="Add"><i
                                        class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ✅ SweetAlert for success/error/validation --}}
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif

        @if (session('validation_errors'))
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessages,
                confirmButtonColor: '#d33',
                customClass: { popup: 'text-start' }
            });
        @endif
    </script>

    {{-- ✅ Dynamic Flower Fields --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flowerContainer = document.getElementById('flower-price-container');

            flowerContainer.addEventListener('click', function (e) {
                if (e.target.classList.contains('add-flower-price')) {
                    const group = e.target.closest('.flower-price-group');
                    const newGroup = group.cloneNode(true);
                    newGroup.querySelector('select').value = '';
                    flowerContainer.appendChild(newGroup);
                    updateFlowerButtons();
                }

                if (e.target.classList.contains('remove-flower-price')) {
                    e.target.closest('.flower-price-group').remove();
                    updateFlowerButtons();
                }
            });

            function updateFlowerButtons() {
                const groups = flowerContainer.querySelectorAll('.flower-price-group');
                groups.forEach((group, index) => {
                    const button = group.querySelector('button');
                    if (index === 0) {
                        button.className = 'btn btn-success add-flower-price w-100';
                        button.textContent = '+';
                    } else {
                        button.className = 'btn btn-danger remove-flower-price w-100';
                        button.textContent = '−';
                    }
                });
            }

            updateFlowerButtons(); // Run once on load
        });
    </script>

    {{-- ✅ Dynamic Package Fields --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const packageOptions = `@foreach ($packages as $package)
                <option value="{{ $package->product_id }}">{{ $package->name }}</option>
            @endforeach`;

            const packageContainer = document.getElementById('package-container');

            packageContainer.addEventListener('click', function (e) {
                if (e.target.closest('.add-package')) {
                    const group = document.createElement('div');
                    group.className = 'input-group mb-2 package-group';
                    group.innerHTML = `
                        <select name="product_id[]" class="form-select" required>
                            <option value="">Select Package</option>
                            ${packageOptions}
                        </select>
                        <button type="button" class="btn btn-danger remove-package"><i class="fa fa-minus"></i></button>
                    `;
                    packageContainer.appendChild(group);
                }

                if (e.target.closest('.remove-package')) {
                    e.target.closest('.package-group').remove();
                }
            });
        });
    </script>
@endsection
