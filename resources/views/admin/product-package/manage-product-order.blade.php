@extends('admin.layouts.app')

@section('styles')
<input type="hidden" name="_token" value="{{ csrf_token() }}">

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
                      <span class="main-content-title mg-b-0 mg-b-lg-1">Manage Product Order</span>
                    </div>
                    <div class="justify-content-center mt-2">
                        <ol class="breadcrumb d-flex justify-content-between align-items-center">
                            {{-- <a href="{{url('admin/add-pandit')}}" class="breadcrumb-item tx-15 btn btn-warning">Add Pandit</a> --}}
                            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Product Order</li>
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
                                        <a class="nav-link mb-2 mt-2 {{ Request::is('admin/product-orders') ? 'active' : '' }}" href="{{ route('admin.product.index') }}"
                                            onclick="changeColor(this)">Subscription Orders</a>
                                        <a class="nav-link mb-2 mt-2" href="{{ route('product-request') }}"
                                            onclick="changeColor(this)">Request Orders</a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                 
                    <div class="col-md-4">
                        <a href="{{ route('admin.orders.index', ['filter' => 'active']) }}">
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
                        <a href="{{ route('admin.orders.index', ['filter' => 'renew']) }}">
                            <div class="card bg-info text-dark mb-3">
                                <div class="card-header">
                                    <i class="fas fa-box"></i>Subscription Placed today
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $ordersRequestedToday }}</h5>
                                    <p class="card-text">Subscription Placed today</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                

                    <!-- Row -->
                    <div class="row row-sm">
                        <div class="col-lg-12">
                            <div class="card custom-card overflow-hidden">
                                <div class="card-body">
                                  
                                    
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
                                                       
                                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary">View Details</a>
                                                       <!-- Updated Modal Form -->
                                                       @if ($order->subscription->status === 'active')
                                                            <!-- Show Pause Button -->
                                                            <a href="#" class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#pauseModal{{ $order->order_id }}">Pause</a>
                                                        @elseif ($order->subscription->status === 'paused')
                                                            <!-- Show Resume Button -->
                                                            {{-- <a href="{{ route('resume.subscription', $order->order_id) }}" class="btn btn-success mb-3">Resume</a> --}}
                                                        @endif

                                                        <div class="modal fade" id="pauseModal{{ $order->order_id }}" tabindex="-1" aria-labelledby="pauseModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="pauseModalLabel">Pause Subscription</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form id="pauseForm{{ $order->order_id }}" action="{{ route('pause.subscription', $order->order_id) }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                                                                            <div class="mb-3">
                                                                                <label for="pause_start_date_{{ $order->order_id }}" class="form-label">Pause Start Date</label>
                                                                                <input type="date" id="pause_start_date_{{ $order->order_id }}" name="pause_start_date" 
                                                                                    class="form-control" required 
                                                                                    min="{{ $order->subscription->start_date }}" 
                                                                                    max="{{ $order->subscription->end_date }}">
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label for="pause_end_date_{{ $order->order_id }}" class="form-label">Pause End Date</label>
                                                                                <input type="date" id="pause_end_date_{{ $order->order_id }}" name="pause_end_date" 
                                                                                    class="form-control" required 
                                                                                    min="{{ $order->subscription->start_date }}" 
                                                                                    max="{{ $order->subscription->end_date }}">
                                                                            </div>

                                                                            <p class="text-muted">
                                                                                Dates must be between <strong>{{ $order->subscription->start_date }}</strong> and <strong>{{ $order->subscription->end_date }}</strong>.
                                                                            </p>

                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Updated JavaScript -->

@endsection
