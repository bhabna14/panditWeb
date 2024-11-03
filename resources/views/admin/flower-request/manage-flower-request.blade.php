@extends('admin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
<style>
    
</style>
@endsection

@section('content')

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Bookings</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Bookings</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   

                  

                    <!-- Row -->
                    <div class="row row-sm">
                        <div class="col-lg-12">
                            <div class="card custom-card overflow-hidden">
                                <div class="card-body">
                                    <!-- <div>
                                        <h6 class="main-content-label mb-1">File export Datatables</h6>
                                        <p class="text-muted card-sub-title">Exporting data from a table can often be a key part of a complex application. The Buttons extension for DataTables provides three plug-ins that provide overlapping functionality for data export:</p>
                                    </div> -->
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
                                    <div class="table-responsive  export-table">
                                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom">
                                            <thead>
                                                <tr>
                                                    <th>Request ID</th>
                                                    <th>Product ID</th>
                                                  
                                                    <th>Description</th>
                                                 
                                                    <th>Status</th>
                                                    <th>Price</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pendingRequests as $request)
                                                    <tr>
                                                        <td>{{ $request->id }}</td>
                                                        <td>{{ $request->product_id }}</td>
                                                      
                                                        <td>{{ $request->description }}</td>
                                                     
                                                        <td>{{ $request->status }}</td>
                                                        <td>
                                                            @if($request->order && $request->order->total_price)
                                                                {{-- Display the saved price if it exists --}}
                                                                <span>{{ $request->order->total_price }} </span>
                                                            @else
                                                                {{-- Show the input box and save button if no price is set --}}
                                                                <form action="{{ route('admin.saveOrder', $request->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <input type="number" name="price" class="form-control" placeholder="Enter Price" required>
                                                                    <button type="submit" class="btn btn-primary mt-2">Save</button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            <form action="{{ route('admin.markPayment', $request->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success mt-2" {{ $request->status === 'paid' ? 'disabled' : '' }}>Paid</button>
                                                            </form>
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
                    <!-- End Row -->

@endsection

@section('scripts')

    <!-- Internal Data tables -->
    <script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/pdfmake/vfs_fonts.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/js/table-data.js')}}"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            // Save Order Button Click
            $('.save-order').on('click', function () {
                let requestId = $(this).data('request-id');
                let price = $(this).closest('tr').find('.price-input').val();
        
                if (!price) {
                    alert('Please enter a price');
                    return;
                }
        
                $.ajax({
                    url: '/api/save-order',
                    type: 'POST',
                    data: {
                        request_id: requestId,
                        price: price,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        alert('Order saved successfully');
                        $('.payment[data-request-id="' + requestId + '"]').prop('disabled', false); // Enable Payment Button
                    }
                });
            });
        
            // Paid Button Click
            $('.payment').on('click', function () {
                let requestId = $(this).data('request-id');
        
                $.ajax({
                    url: '/api/mark-payment',
                    type: 'POST',
                    data: {
                        request_id: requestId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        alert('Payment marked as paid');
                    }
                });
            });
        });
        </script>
        

@endsection
