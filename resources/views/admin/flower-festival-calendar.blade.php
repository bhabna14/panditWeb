@extends('admin.layouts.apps')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">FESTIVAL CALENDAR</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.manageFestivalCalendar') }}"
                        class="btn btn-warning text-dark">Manage Calendar</a></li>
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
                        <input type="file" class="form-control" id="festival_image" name="festival_image"  accept="image/*">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Related Flowers & Package Price</label>
                        <div id="flower-price-container">
                            <div class="row flower-price-group mb-2">
                                <div class="col-md-5">
                                    <select class="form-control" name="related_flower[]">
                                        <option value="">Select Flower</option>
                                        @foreach ($flowerNames as $flower)
                                            <option value="{{ $flower->name }}">{{ $flower->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" class="form-control" name="package_price[]"
                                        placeholder="Package Price">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success add-flower-price">+</button>
                                </div>
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

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('flower-price-container');

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-flower-price')) {
                    const group = document.createElement('div');
                    group.classList.add('row', 'flower-price-group', 'mb-2');
                    group.innerHTML = `
                    <div class="col-md-5">
                        <select class="form-control" name="related_flower[]">
                            <option value="">Select Flower</option>
                            @foreach ($flowerNames as $flower)
                                <option value="{{ $flower->name }}">{{ $flower->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control" name="package_price[]" placeholder="Package Price">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-flower-price">−</button>
                    </div>
                `;
                    container.appendChild(group);

                    // Update buttons: only first group gets "+"
                    updateButtons();
                }

                if (e.target.classList.contains('remove-flower-price')) {
                    e.target.closest('.flower-price-group').remove();
                    updateButtons();
                }
            });

            function updateButtons() {
                const groups = container.querySelectorAll('.flower-price-group');
                groups.forEach((group, index) => {
                    const btn = group.querySelector('button');
                    if (index === 0) {
                        btn.className = 'btn btn-success add-flower-price';
                        btn.textContent = '+';
                    } else {
                        btn.className = 'btn btn-danger remove-flower-price';
                        btn.textContent = '−';
                    }
                });
            }
        });
    </script>
@endsection
