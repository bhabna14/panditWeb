@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">EDIT APARTMENT ASSIGN</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-order-assign') }}"
                        class="btn btn-warning text-dark">Manage Order Assign</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Order</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">
            {{ $errors->first('danger') }}
        </div>
    @endif

 <!-- resources/views/admin/edit-order-assign.blade.php -->

<form action="{{ route('admin.updateOrderAssign', $rider->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- We are using PUT method for update -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rider_name">Rider Name</label>
                                <select class="form-control rider_name" name="rider_name" required>
                                    <option value="">Select Rider</option>
                                    @foreach ($rider_names as $rider_item)
                                        <option value="{{ $rider_item->rider_id }}" 
                                            @if($rider_item->rider_id == $rider->rider_id) selected @endif>
                                            {{ $rider_item->rider_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assign_date">Order Assign Date</label>
                                <input type="date" class="form-control" name="assign_date" value="{{ $rider->assign_date }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Locality and Apartment Selection -->
                    <div id="locality-apartment-container">
                        <div class="row locality-apartment-group">
                            <!-- Locality Dropdown -->
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="locality_name">Locality Name</label>
                                    <select class="form-control locality_name" name="locality_name[]" required>
                                        <option value="">Select Locality</option>
                                        @foreach ($localities as $locality)
                                            <option value="{{ $locality->id }}" 
                                                @if(in_array($locality->id, explode(',', $rider->locality_id))) selected @endif>
                                                {{ $locality->locality_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Apartment Dropdown -->
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="apartment_name">Apartment Name</label>
                                    <select class="form-control select2 apartment_name" name="apartment_name[]" multiple="multiple" required>
                                        <option value="">Select Apartment</option>
                                        @foreach ($apartments as $apartment)
                                            <option value="{{ $apartment->id }}" 
                                                @if(in_array($apartment->id, explode(',', $rider->apartment_id))) selected @endif>
                                                {{ $apartment->apartment_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Update">
                            </div>
                        </div>
                    </div>

                </div>
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
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <!--Internal  Form-elements js-->
    <script src="{{ asset('assets/js/advanced-form-elements.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            let index = 1; // Tracks dynamic fields

            // Add new locality-apartment group
            $(document).on('click', '.add-locality-apartment', function() {
                const clone = $('.locality-apartment-group:first').clone();
                clone.find('.locality_name, .apartment_name').val(''); // Clear the values
                clone.find('.apartment_name').attr('name',
                `apartment_name[${index}][]`); // Update name attribute
                clone.find('.remove-locality-apartment').show(); // Show the remove button
                clone.find('.select2-container').remove(); // Remove existing Select2 container
                clone.find('.apartment_name').removeAttr('data-select2-id').removeClass(
                    'select2-hidden-accessible'); // Reset Select2
                $('#locality-apartment-container').append(clone); // Append the cloned group
                clone.find('.select2').select2(); // Reinitialize Select2 for the new group
                index++;
            });

            // Remove a locality-apartment group
            $(document).on('click', '.remove-locality-apartment', function() {
                $(this).closest('.locality-apartment-group').remove();
            });

            // Dynamically fetch apartments based on locality
            $(document).on('change', '.locality_name', function() {
                const localityId = $(this).val();
                const apartmentDropdown = $(this).closest('.locality-apartment-group').find(
                    '.apartment_name');

                apartmentDropdown.html('<option value="">Loading...</option>'); // Show loading text

                if (localityId) {
                    $.ajax({
                        url: "{{ route('admin.getApartments') }}",
                        type: "GET",
                        data: {
                            locality_id: localityId
                        },
                        success: function(response) {
                            apartmentDropdown.empty(); // Clear the dropdown
                            if (response.apartments && response.apartments.length > 0) {
                                response.apartments.forEach(apartment => {
                                    apartmentDropdown.append(
                                        `<option value="${apartment.id}">${apartment.apartment_name}</option>`
                                    );
                                });
                            } else {
                                apartmentDropdown.append(
                                    '<option value="">No Apartments Found</option>');
                            }
                        },
                        error: function() {
                            apartmentDropdown.html(
                                '<option value="">Error Loading Apartments</option>');
                        }
                    });
                } else {
                    apartmentDropdown.html('<option value="">Select Apartment</option>'); // Reset dropdown
                }
            });

            // Initialize Select2 on page load
            $('.select2').select2();
        });
    </script>
@endsection
