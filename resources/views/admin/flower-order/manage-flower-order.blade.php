@extends('admin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />

@endsection

@section('content')

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div class="left-content">
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Flower Order</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Flower Order</li>
                        </ol>
                    </div>
                </div>
                <!-- /breadcrumb -->

                   

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-footer py-0">
                                <div class="profile-tab tab-menu-heading border-bottom-0">
                                    <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 full-width-tabs">
                                        <a class="nav-link mb-2 mt-2 {{ Request::is('admin/flower-orders') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"
                                            onclick="changeColor(this)">Subscription Orders</a>
                                        <a class="nav-link mb-2 mt-2" href="{{ route('flower-request') }}"
                                            onclick="changeColor(this)">Request Orders</a>
                                       
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                 
                    <div class="col-md-4">
                        <a href="{{route('active.subscriptions')}}">
                            <div class="card bg-success text-dark mb-3">
                                <div class="card-header">
                                    <i class="fas fa-check-circle"></i> Active Subscriptions
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-white">{{ $activeSubscriptions }}</h5>
                                    <p class="card-text text-white">Users with an active subscription</p>
                                </div>
                            </div>
                        </a>
                    </div>
                
                
                    <div class="col-md-4">
                        <a href="{{route('paused.subscriptions')}}">
                            <div class="card bg-warning text-dark mb-3">
                                <div class="card-header">
                                    <i class="fas fa-pause-circle"></i> Paused Subscriptions
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $pausedSubscriptions }}</h5>
                                    <p class="card-text">Users with a paused subscription</p>
                                </div>
                            </div>
                        </a>
                    </div>
                
                    <div class="col-md-4">
                        
                            <div class="card bg-info text-dark mb-3">
                                <div class="card-header">
                                    <i class="fas fa-box"></i>Subscription Placed today
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $ordersRequestedToday }}</h5>
                                    <p class="card-text">Subscription Placed today</p>
                                </div>
                            </div>
                       
                    </div>
                </div>
                
                

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
                                    <div class="table-responsive ">
                                        <table id="file-datatable" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Purchase Date</th>
                                                    <th>Product Details</th>
                                                    <th>Address Details</th>
                                                    <th>Total Price</th>
                                                    <th>Status</th>
                                                    <th>Assigned Rider</th>
                                                    <th>Reffered By</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ $order->order_id }} <br>
                                                        Name: {{ $order->user->name }} <br>
                                                        Number : {{ $order->user->mobile_number }} <br>
                                                        <a href="{{ route('showCustomerDetails', $order->user->userid) }}" class="btn btn-sm btn-warning">View Customer</a>
                                                    </td>
                                                    <td>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d-m-Y h:i A') : 'N/A' }}</td>

                                                    <td>{{ $order->flowerProduct->name }} <br>
                                                       ( {{ \Carbon\Carbon::parse($order->subscription->start_date)->format('F j, Y') }} - {{ $order->subscription->new_date ? \Carbon\Carbon::parse($order->subscription->new_date)->format('F j, Y') : \Carbon\Carbon::parse($order->subscription->end_date)->format('F j, Y') }} )
                                                    </td>
                                                    <td>
                                                        <strong>Address:</strong> {{ $order->address->apartment_flat_plot ?? "" }},{{ $order->address->apartment_name ?? "" }}, {{ $order->address->localityDetails->locality_name ?? "" }}<br>
                                                        <strong>Landmark:</strong> {{ $order->address->landmark ?? "" }}<br>
                                                        <strong>City:</strong> {{ $order->address->city ?? ""}}<br>
                                                        <strong>State:</strong> {{ $order->address->state ?? ""}}<br>
                                                        <strong>Pin Code:</strong> {{ $order->address->pincode ?? "" }}
                                                    </td>
                                                    <td>{{ number_format($order->total_price, 2) }}</td>
                                                    <td>
                                                        <span class="status-badge 
                                                        {{ $order->subscription->status === 'active' ? 'status-running bg-success' : '' }}
                                                        {{ $order->subscription->status === 'paused' ? 'status-paused bg-warning' : '' }}
                                                        {{ $order->subscription->status === 'expired' ? 'status-expired bg-danger' : '' }}
                                                        {{ $order->subscription->status === 'pending' ? 'status-expired bg-danger' : '' }}">
                                                            {{ ucfirst($order->subscription->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($order->rider_id)
                                                            <span>{{ $order->rider->rider_name }}</span>
                                                            <a href="#editRiderModal{{ $order->id }}" class="btn btn-sm btn-info" data-bs-toggle="modal">Edit Rider</a>
                                                        @else
                                                            <form action="{{ route('admin.orders.assignRider', $order->id) }}" method="POST">
                                                                @csrf
                                                                <select name="rider_id" class="form-control" required>
                                                                    <option value="" selected>Choose</option>
                                                                    @foreach($riders as $rider)
                                                                        <option value="{{ $rider->rider_id }}" {{ $order->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                                            {{ $rider->rider_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="submit" class="btn btn-sm btn-success mt-2">Assign Rider</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($order->referral_id)
                                                            @php
                                                                $referralRider = $riders->firstWhere('rider_id', $order->referral_id);
                                                            @endphp
                                                            @if($referralRider)
                                                                <span>{{ $referralRider->rider_name }}</span>
                                                            @else
                                                                <span>No Referral Rider Found</span>
                                                            @endif
                                                        @else
                                                            <form action="{{ route('admin.orders.refferRider', $order->id) }}" method="POST">
                                                                @csrf
                                                                <select name="referral_id" class="form-control" required>
                                                                    <option value="" selected>Choose</option>
                                                                    @foreach($riders as $rider)
                                                                        <option value="{{ $rider->rider_id }}" {{ $order->referral_id == $rider->rider_id ? 'selected' : '' }}>
                                                                            {{ $rider->rider_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <button type="submit" class="btn btn-sm btn-success mt-2">Save</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    
                                                    <td>
                                                        {{-- <button id="viewOrdersButton" class="btn btn-primary">Mark Orders as Viewed</button> --}}

                                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary">View Details</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            
                                        </table>
                                        <!-- Add the modal for editing the rider -->
                                        @foreach($orders as $order)
                                        <div class="modal fade" id="editRiderModal{{ $order->id }}" tabindex="-1" aria-labelledby="editRiderModalLabel{{ $order->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editRiderModalLabel{{ $order->id }}">Change Rider for Order #{{ $order->order_id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('admin.orders.updateRider', $order->id) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="rider_id{{ $order->id }}" class="form-label">Select Rider</label>
                                                                <select name="rider_id" id="rider_id{{ $order->id }}" class="form-control">
                                                                    @foreach($riders as $rider)
                                                                        <option value="{{ $rider->rider_id }}" {{ $order->rider_id == $rider->rider_id ? 'selected' : '' }}>
                                                                            {{ $rider->rider_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        

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
    {{-- <script>
        let playSound = {{ $unviewedOrdersCount > 0 ? 'true' : 'false' }};
        const audio = new Audio('{{ asset('sound/flowersound.mp3') }}');
    
        if (playSound) {
            audio.loop = true;
            audio.play();
        }
    
        // Mark orders as viewed and stop sound
        function markOrdersAsViewed() {
            fetch("{{ route('orders.markAsViewed') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }).then(response => {
                if (response.ok) {
                    audio.pause();
                    playSound = false;
                }
            });
        }
    
        // Call the function when the "view orders" button or action is clicked
        document.getElementById('viewOrdersButton').addEventListener('click', markOrdersAsViewed);
    </script> --}}
    

@endsection
