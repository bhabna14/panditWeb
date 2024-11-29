@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .aligned-list {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Order Assign</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <a href="{{ url('admin/add-order-assign') }}" class="breadcrumb-item tx-15 btn btn-warning">Add Order</a>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rider</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->


    @if (session('success'))
        <div id = 'Message' class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('danger'))
        <div id = 'Message' class="alert alert-danger">
            {{ session('danger') }}
        </div>
    @endif


    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive  export-table">
                        <table id="file-datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Assign Date</th>
                                    <th>Locality Name</th>
                                    <th>Apartment Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rider_details as $riderName => $riders)
                                <tr>
                                    <td colspan="4" class="table-secondary">
                                        <strong style="font-size: 20px">{{ $riderName }}</strong>
                                    </td>
                                
                                    <td class="table-secondary text-right">
                                        <button class="btn btn-sm btn-info deactive-button" style="font-size: 25px" data-id="{{ $riders->first()->rider_id }}">
                                            <i class="icon ion-md-unlock"></i>
                                        </button>
                                    </td>
                                </tr>
                                
                                    @foreach ($riders as $key => $rider)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $rider->assign_date ?? 'N/A' }}</td>
                                            <td>{{ $rider->locality->locality_name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $apartmentList = explode(',', $rider->apartment_id);
                                                @endphp
                                                <ul>
                                                    @foreach ($apartmentList as $apartmentId)
                                                        @php
                                                            $apartmentName =
                                                                App\Models\Apartment::find($apartmentId)
                                                                    ->apartment_name ?? 'N/A';
                                                        @endphp
                                                        <li>{{ $apartmentName }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <!-- Inside your table row -->
                                            <td>
                                                <a href="{{ url('admin/edit-order-assign/' . $rider->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('admin.deleteOrderAssign', $rider->id) }}"
                                                    class="d-inline" id="delete-form-{{ $rider->id }}">
                                                    @csrf
                                                    @method('POST') <!-- Ensure this is POST request -->
                                                    <button type="button" class="btn btn-sm btn-danger delete-button"
                                                        data-id="{{ $rider->id }}">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                            ton>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('delete-form-' + id); // Get the associated form

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit the form (DELETE request)
                    }
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deactiveButtons = document.querySelectorAll('.deactive-button');

        deactiveButtons.forEach(button => {
            button.addEventListener('click', function() {
                const riderId = this.getAttribute('data-id');  // Get the rider_id
                const url = `{{ url('admin/deactive-order-assign') }}/${riderId}`; // Create the URL for deactivation

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to deactivate this order assignment?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, deactivate it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url; // Redirect to the deactivation route
                    }
                });
            });
        });
    });
</script>


@endsection
