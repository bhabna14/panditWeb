@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Marketing Visit Place</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.getVisitPlace') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Visit
                    Place</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Location</li>
            </ol>
        </div>
    </div>

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive  export-table">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>SlNo</th>
                                    <th>Visit Place</th>
                                    <th>Visitor Name</th>
                                    <th>Date & Time</th>
                                    <th>Contact Person</th>
                                    <th>Contact Numbers</th>
                                    <th>No. of Apartments</th>
                                    <th>Delivery</th>
                                    <th>View Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visitPlaces as $index => $visit)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ ucfirst($visit->location_type) }}</td>
                                        <td>{{ $visit->visitor_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($visit->date_time)->format('d-m-Y h:i A') }}</td>
                                        <td>{{ $visit->contact_person_name }}</td>
                                        <td>
                                            @foreach (explode(',', $visit->contact_person_number) as $num)
                                                <span class="badge">{{ $num }}</span><br>
                                            @endforeach
                                        </td>
                                        <td>{{ $visit->no_of_apartment ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($visit->already_delivery) ?? 'N/A' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addressModal"
                                                data-apartment="{{ $visit->apartment_name }}"
                                                data-apartmentnumber="{{ $visit->apartment_number }}"
                                                data-locality="{{ $visit->locality_name }}"
                                                data-landmark="{{ $visit->landmark }}">
                                                View Address
                                            </button>
                                        </td>
                                       
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Address Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Apartment Name:</strong> <span id="modalApartmentName"></span></p>
                    <p><strong>Apartment Number:</strong> <span id="modalApartmentNumber"></span></p>
                    <p><strong>Locality:</strong> <span id="modalLocality"></span></p>
                    <p><strong>Landmark:</strong> <span id="modalLandmark"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- End Row -->
@endsection

@section('scripts')
    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/table-data.js') }}"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const modal = document.getElementById('addressModal');
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('modalApartmentName').textContent = button.getAttribute('data-apartment') ||
                'N/A';
            document.getElementById('modalApartmentNumber').textContent = button.getAttribute(
                'data-apartmentnumber') || 'N/A';
            document.getElementById('modalLocality').textContent = button.getAttribute('data-locality') || 'N/A';
            document.getElementById('modalLandmark').textContent = button.getAttribute('data-landmark') || 'N/A';
        });
    </script>
@endsection
