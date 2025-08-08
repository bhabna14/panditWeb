@extends('admin.layouts.apps')

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Flower Promotion</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15">
                    <a href="{{ route('admin.manageFlowerPromotion') }}" class="btn btn-warning text-dark">
                        Manage Flower Promotion
                    </a>
                </li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            </ol>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.saveFlowerPromotion') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <!-- Header -->
                    <div class="col-md-6">
                        <label for="header" class="form-label">Header</label>
                        <input type="text" class="form-control @error('header') is-invalid @enderror" id="header"
                            name="header" value="{{ old('header') }}" required>
                        @error('header')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Body -->
                    <div class="col-md-12">
                        <label for="body" class="form-label">Body</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="4"
                            required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date"
                            name="start_date" value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                            name="end_date" value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Photo -->
                    <div class="col-md-6">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo"
                            name="photo" accept="image/*" required>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
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

        @if ($errors->any())
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessages,
                confirmButtonColor: '#d33',
                customClass: {
                    popup: 'text-start'
                }
            });
        @endif
    </script>
@endsection
