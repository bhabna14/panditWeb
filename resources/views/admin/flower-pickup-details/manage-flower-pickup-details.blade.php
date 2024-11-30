@extends('admin.layouts.app')

@section('styles')
    <!-- Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">MANAGE Flower Pickup Details</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="{{ route('admin.addflowerpickupdetails') }}" class="btn btn-info text-white">Add Flower Pickup Details</a></li>
                
                <li class="breadcrumb-item active" aria-current="page">Flower Pickup Details</li>
            </ol>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <div class="table-responsive  export-table">
                        <table id="file-datatable" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vendor</th>
                                    <th>Rider</th>
                                    <th>Flower Details</th>
                                    <th>PickUp Date</th>
                                    <th>Total Price</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pickupDetails->flatten()->sortByDesc('created_at') as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->vendor?->vendor_name ?? 'N/A' }}</td>
                                        <td>{{ $detail->rider?->rider_name ?? 'N/A' }}</td>
                                        <td>
                                            <ul>
                                                @foreach ($detail->flowerPickupItems as $item)
                                                    <li>
                                                        <strong>Flower:</strong> {{ $item->flower?->name ?? 'N/A' }} <br>
                                                        <strong>Quantity:</strong> {{ $item->quantity ?? 'N/A' }} {{ $item->unit?->unit_name ?? 'N/A' }} <br>
                                                        <strong>Price:</strong> ₹{{ $item->price ?? 'N/A' }}
                                                    </li>
                                                    @if (!$loop->last)
                                                        <hr>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>{{ $detail->pickup_date }}</td>
                                        <td>
                                            @if ($detail->total_price)
                                                ₹{{ $detail->total_price }}
                                            @else
                                                <span class="text-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($detail->payment_status === 'Paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>{{ $detail->status ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('flower-pickup.edit', $detail->id) }}" class="btn btn-primary btn-sm">Edit</a>

                                            
                                            <!-- Check if the price is greater than 0 to enable the Payment button -->
                                            <button 
                                                class="btn btn-secondary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#paymentModal{{ $detail->id }}"
                                               
                                            >
                                                Payment
                                            </button>
                                        </td>
                                        
                                    </tr>
                                    <!-- Payment Modal -->
                                    <div class="modal fade" id="paymentModal{{ $detail->id }}" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="paymentModalLabel">Add Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('update.payment') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="pickup_id" value="{{ $detail->id }}">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="payment_method" class="form-label">Payment Method</label>
                                                            <select class="form-control" name="payment_method" id="payment_method" required>
                                                                <option value="Cash">Cash</option>
                                                                <option value="Online">Online</option>
                                                                <option value="Card">Card</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="payment_id" class="form-label">Payment ID</label>
                                                            <input type="text" class="form-control" id="payment_id" name="payment_id" placeholder="Enter Payment ID" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>
@endsection
