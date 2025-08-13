@extends('admin.layouts.apps')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">REFER OFFER</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ route('refer.manageReferOffer') }}"
                        class="btn btn-warning text-dark">Manage Refer Offer</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Offer</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('refer.saveReferOffer') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="offer_name" class="form-label">Offer Name</label>
                        <input type="text" class="form-control" id="offer_name" name="offer_name" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Refer & Benefit</label>
                        <div id="referBenefitFields">
                            <div class="row mb-2 refer-benefit-row">
                                <div class="col-md-4">
                                    <input type="number" class="form-control" name="no_of_refer[]"
                                        placeholder="No. of Refer" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="benefit[]" placeholder="Benefit"
                                        required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success add-row">+</button>
                                    <button type="button" class="btn btn-danger remove-row"
                                        style="display:none;">-</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Offer</button>
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
    </script>

    <script>
        $(document).ready(function() {
            $('#referBenefitFields').on('click', '.add-row', function() {
                var row = $(this).closest('.refer-benefit-row').clone();
                row.find('input').val('');
                row.find('.remove-row').show();
                $('#referBenefitFields').append(row);
            });

            $('#referBenefitFields').on('click', '.remove-row', function() {
                $(this).closest('.refer-benefit-row').remove();
            });
        });
    </script>
@endsection
