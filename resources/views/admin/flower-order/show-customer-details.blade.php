@extends('admin.layouts.app')

    @section('styles')

	<!--- Internal Select2 css-->
	<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">

	<!--  smart photo master css -->
	<link href="{{asset('assets/plugins/SmartPhoto-master/smartphoto.css')}}" rel="stylesheet">
<style>
    .my-md-auto.mt-4.prof-details {
    width: 220px;
    margin-right: 15px;
}
.custom-size-icon{
    font-size: 26px;
    margin-bottom: 10px;
}
</style>
    @endsection

    @section('content')

				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<span class="main-content-title mg-b-0 mg-b-lg-1">PROFILE</span>
					</div>
					<div class="justify-content-center mt-2">
						<ol class="breadcrumb">
							<li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
							<li class="breadcrumb-item active" aria-current="page">Profile</li>
						</ol>
					</div>
				</div>
				<!-- /breadcrumb -->

                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-body d-md-flex">
                                <div>
                                    <span class="profile-image pos-relative">
                                        <img class="br-5" alt="" src="{{ asset($user->userphoto ? 'storage/' . $user->userphoto : 'front-assets/img/images.jfif') }}">
                                        <span class="bg-success text-white wd-1 ht-1 rounded-pill profile-online"></span>
                                    </span>
                                </div>
                                <div class="my-md-auto mt-4 prof-details">
                                    <h4 class="font-weight-semibold ms-md-4 ms-0 mb-1 pb-0">{{ $user->name }}</h4>
                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa fa-phone me-2"></i></span><span class="font-weight-semibold me-2">Phone:</span><span>{{ $user->mobile_number }}</span>
                                    </p>
                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa fa-envelope me-2"></i></span><span class="font-weight-semibold me-2">Email:</span><span>{{ $user->email }}</span>
                                    </p>
                                    <p class="text-muted ms-md-4 ms-0 mb-2">
                                        <span><i class="fa fa-venus-mars me-2"></i></span><span class="font-weight-semibold me-2">Gender:</span><span>{{ $user->gender }}</span>
                                    </p>
                                </div>
                                <div class="my-md-auto mt-4 prof-details">
                                    <div class="card bg-light mb-2 shadow-sm">
                                        <div class="card-body p-3 align-items-center">
                                            
                                            <div class="text-center">
                                                <i class="fa fa-shopping-cart text-primary me-2 custom-size-icon"></i>
                                                <h6 class="mb-1">Total Orders</h6>
                                                <p class="mb-0 font-weight-bold">{{ $totalOrders }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-md-auto mt-4 prof-details">
                                    <div class="card bg-light mb-2 shadow-sm">
                                        <div class="card-body p-3 align-items-center">
                                           
                                            <div class="text-center">
                                                <i class="fa fa-hourglass-half text-warning me-2 custom-size-icon"></i>
                                                <h6 class="mb-1">Ongoing Subscriptiom</h6>
                                                <p class="mb-0 font-weight-bold">{{ $ongoingOrders }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-md-auto mt-4 prof-details">
                                    <div class="card bg-light mb-2 shadow-sm">
                                        <div class="card-body p-3 align-items-center">
                                            <div class="text-center">
                                               
                                                <i class="fa fa-rupee-sign text-success me-2 custom-size-icon"></i>
                                                <h6 class="mb-1">Total Spend</h6>
                                                <p class="mb-0 font-weight-bold">₹{{ number_format($totalSpend, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="card-footer py-0">
                                <div class="profile-tab tab-menu-heading border-bottom-0">
                                    <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0">
                                        {{-- <a class="nav-link mb-2 mt-2 active" data-bs-toggle="tab" href="#about">About</a> --}}
                                        <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#order">Subscription Orders</a>
                                        <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#requestorder">Request Orders</a>

                                        <a class="nav-link mb-2 mt-2" data-bs-toggle="tab" href="#address">Addresses</a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row row-sm">
                    <div class="col-lg-12 col-md-12">
                        <div class="custom-card main-content-body-profile">
                            <div class="tab-content">
                                <div class="main-content-body tab-pane border-top-0 " id="about">
                                    <div class="card">
                                        <div class="card-body p-0 border-0 rounded-10">
                                            <div class="p-4">
                                                <h4 class="tx-15 text-uppercase mb-3">About</h4>
                                                <p class="m-b-5">{{ $user->about ?? 'No additional information available.' }}</p>
                                            </div>
                                            <div class="border-top"></div>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="main-content-body tab-pane border-top-0 active" id="order">
                                    <div class="border-0">
                                        <div class="main-content-body main-content-body-profile">
                                            <div class="main-profile-body p-0">
                                                <div class="row row-sm">
                                                    <div class="col-12">
                                                        <table id="file-datatable" class="table table-bordered ">
                                                            <thead>
                                                                <tr>
                                                                    <th>Order ID</th>
                                                                    <th>Start Date</th>
                                                                    <th>Product Details</th>
                                                                    <th>Address Details</th>
                                                                    <th>Total Price</th>
                                                                    <th>Status</th>
                
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($orders as $order)
                                                                <tr>
                                                                    <td>{{ $order->order_id }} 
                                                                       
                                                                    </td>
                                                                    <td>{{ $order->subscription->start_date ?? "NA" }} 
                                                                       
                                                                    </td>
                                                                    <td>{{ $order->flowerProduct->name }} <br>
                                                                       ( {{ \Carbon\Carbon::parse($order->subscription->start_date)->format('F j, Y') ?? "NA"}} - {{ $order->subscription->new_date ? \Carbon\Carbon::parse($order->subscription->new_date)->format('F j, Y') : \Carbon\Carbon::parse($order->subscription->end_date)->format('F j, Y') }} )
                
                
                                                                    </td>
                                                                    <td>
                                                                        <strong>Address:</strong> {{ $order->address->area ?? "" }}<br>
                                                                        <strong>City:</strong> {{ $order->address->city ?? ""}}<br>
                                                                        <strong>State:</strong> {{ $order->address->state ?? ""}}<br>
                                                                        <strong>Zip Code:</strong> {{ $order->address->pincode ?? "" }}
                                                                    </td>
                                                                    {{-- <td>{{ $order->total_price }}</td> --}}
                                                                    <td>{{ number_format($order->total_price, 2) }}</td>
                
                                                                    <td>
                                                                        <span class="status-badge 
                                                                        {{ $order->subscription->status === 'active' ? 'status-running bg-success' : '' }}
                                                                        {{ $order->subscription->status === 'paused' ? 'status-paused bg-warning' : '' }}
                                                                        {{ $order->subscription->status === 'expired' ? 'status-expired bg-danger' : '' }}">
                                                                        {{ ucfirst($order->subscription->status) }}
                                                                    </span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary">View Details</a>
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
                                </div>

                                <div class="main-content-body tab-pane border-top-0 " id="requestorder">
                                    <div class="border-0">
                                        <div class="main-content-body main-content-body-profile">
                                            <div class="main-profile-body p-0">
                                                <div class="row row-sm">
                                                    <div class="col-12">
                                                        <table id="file-datatable" class="table table-bordered ">
                                                            <thead>
                                                                <tr>
                                                                    <th>Request ID</th>
                                                                   
                                                                    <th>Delivery Date</th>
                                                                  
                                                                    <th>Flower Items</th>
                                                                   
                                                                    <th>Status</th>
                                                                   
                                                                    <th>Actions</th>
                                                                    <th>Address</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($pendingRequests as $request)
                                                                    <tr>
                                                                        <td>{{ $request->request_id }} <br>
                                                                            Name: {{ $request->user->name }} <br>
                                                                            Number : {{ $request->user->mobile_number }}
                                                                        </td>
                                                                        
                                                                        <td>{{ $request->date }} 
                                                                           {{-- ( {{  \Carbon\Carbon::parse($request->date)->format('F j, Y') }} ) --}}
                                                                        </td>
                                                                        <td>
                                                                            <ul>
                                                                                @foreach ($request->flowerRequestItems as $item)
                                                                                    <li>
                                                                                        {{ $item->flower_name }} - {{ $item->flower_quantity }} {{ $item->flower_unit }}
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </td>
                                                                     
                                                                        <td>@if ($request->status == 'pending')
                                                                            <p>Order Placed <br> Update the Price</p>
                                                                            @elseif($request->status == 'approved')
                                                                            <p>Payment Pending</p>
                                                                            @elseif($request->status == 'paid')
                                                                            <p>Payment Completed</p>
                                                                            @endif
                                                                        </td>
                                                                        
                                                                        
                                                                        <td>
                                                                           {{$request->status}}
                                                                        </td>
                                                                        <td>
                                                                            <strong>Address:</strong> {{ $order->address->apartment_flat_plot ?? "" }}, {{ $order->address->locality ?? "" }}<br>
                                                                            <strong>Landmark:</strong> {{ $order->address->landmark ?? "" }}<br>
                    
                                                                            <strong>City:</strong> {{ $order->address->city ?? ""}}<br>
                                                                            <strong>State:</strong> {{ $order->address->state ?? ""}}<br>
                                                                            <strong>Pin Code:</strong> {{ $order->address->pincode ?? "" }}
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
                                </div>
                
                                <div class="main-content-body tab-pane border-top-0" id="address">
                                    <div class="border-0">
                                        <div class="main-content-body main-content-body-profile">
                                            <div class="main-profile-body p-0">
                                                <div class="row row-sm">
                                                  
                                                       

                                                        @foreach ($addressdata as $address)
                                                        <div class="col-3">
                                                            <div class="card mb-3 shadow-sm border-secondary">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h5 class="card-title fw-bold">{{ $address->address_type ?? "N/A" }}</h5>
                                                                        @if ($address->default == 1)
                                                                            <span class="badge bg-success">Default</span>
                                                                        @endif
                                                                    </div>
                                                                    <p class="card-text mb-2">
                                                                        <strong>Address:</strong> {{ $address->area ?? "N/A" }}<br>
                                                                        <strong>City:</strong> {{ $address->city ?? "N/A" }}<br>
                                                                        <strong>State:</strong> {{ $address->state ?? "N/A" }}<br>
                                                                        <strong>Zip Code:</strong> {{ $address->pincode ?? "N/A" }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach

                                                        
                                                       
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    @endsection

    @section('scripts')

        <!-- Internal Select2 js-->
        <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
        <script src="{{asset('assets/js/select2.js')}}"></script>

        <!-- smart photo master js -->
        <script src="{{asset('assets/plugins/SmartPhoto-master/smartphoto.js')}}"></script>
        <script src="{{asset('assets/js/gallery.js')}}"></script>

    @endsection
