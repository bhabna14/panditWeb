@extends('admin.layouts.app')

@section('styles')

    <!-- Data table css -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    <!-- INTERNAL Select2 css -->
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .timeline {
    border-left: 2px solid #007bff;
    margin: 20px 0;
    padding-left: 20px;
    position: relative;
}

.timeline-item {
    margin-bottom: 20px;
    position: relative;
}

.timeline-item::before {
    content: "";
    background: #007bff;
    border-radius: 50%;
    height: 10px;
    width: 10px;
    position: absolute;
    left: -14px;
    top: 4px;
}

.timeline-date {
    color: #007bff;
    font-weight: bold;
    margin-bottom: 5px;
}

.timeline-content {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 10px;
}

    </style>

@endsection
@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0">Subscriptions Ending Soon</span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card custom-card mt-4">
    <div class="card-body">
        <div class="table-responsive ">
         <table id="file-datatable" class="table table-bordered ">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Product</th>
                    <th>Subscription End Date</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @if($order->subscription)
                    <tr>
                        <td>{{ $order->order_id }} <br>
                            Name: {{ $order->user->name }} <br>
                            Number : {{ $order->user->mobile_number }}</td>
                            <td>{{ $order->flowerProduct->name }} <br>
                                ( {{ \Carbon\Carbon::parse($order->subscription->start_date)->format('F j, Y') }} - {{ $order->subscription->new_date ? \Carbon\Carbon::parse($order->subscription->new_date)->format('F j, Y') : \Carbon\Carbon::parse($order->subscription->end_date)->format('F j, Y') }} )
                             </td>
                        <td>{{ $order->subscription->new_date ? \Carbon\Carbon::parse($order->subscription->new_date)->format('F j, Y') : \Carbon\Carbon::parse($order->subscription->end_date)->format('F j, Y') }} </td>
                        <td>
                            <strong>Address:</strong> {{ $order->address->apartment_flat_plot ?? "" }},{{ $order->address->apartment_name ?? "" }}, {{ $order->address->localityDetails->locality_name ?? "" }}<br>
                            <strong>Landmark:</strong> {{ $order->address->landmark ?? "" }}<br>
                            <strong>City:</strong> {{ $order->address->city ?? ""}}<br>
                            <strong>State:</strong> {{ $order->address->state ?? ""}}<br>
                            <strong>Pin Code:</strong> {{ $order->address->pincode ?? "" }}
                        </td>
                        <td>
                            <!-- Call Button with Phone Icon -->
                            <a href="tel:{{ $order->user->phone }}" class="btn btn-sm btn-success mb-2">
                                <i class="fas fa-phone-alt"></i> Call
                            </a>
                        
                            <!-- WhatsApp Button with WhatsApp Icon -->
                            <a href="https://wa.me/{{ $order->user->mobile_number }}" class="btn btn-sm btn-success mb-2">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        
                            <!-- Mail Button with Email Icon -->
                            <a href="mailto:{{ $order->user->email }}" class="btn btn-sm btn-info mb-2">
                                <i class="fas fa-envelope"></i> Mail
                            </a>
                        
                            <!-- Add Note Button with Note Icon -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#followUpModal-{{ $order->id }}">
                                <i class="fas fa-sticky-note"></i> Add Note
                            </button>
                        
                            <!-- View Notes Button with Eye Icon -->
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#viewNotesModal-{{ $order->id }}">
                                <i class="fas fa-eye"></i> View Notes
                            </button>
                        </td>
                        
                        
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </div> 
    </div>
</div>
<div class="modal fade" id="viewNotesModal-{{ $order->id }}" tabindex="-1" aria-labelledby="viewNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Follow-Up Notes for Order #{{ $order->order_id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($order->marketingFollowUps->isEmpty())
                    <p>No follow-up notes available.</p>
                @else
                    <div class="timeline">
                        @foreach ($order->marketingFollowUps as $followUp)
                            <div class="timeline-item">
                                <span class="timeline-date">{{ \Carbon\Carbon::parse($followUp->followup_date)->format('d M Y') }}</span>
                                <div class="timeline-content">
                                    <strong>Note:</strong> {{ $followUp->note }}
                                    <br>
                                    <small>Added on {{ $followUp->created_at->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@foreach($orders as $order)
@if($order->subscription)
<!-- Follow-Up Modal -->
<div class="modal fade" id="followUpModal-{{ $order->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.saveFollowUp') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Follow-Up Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <input type="hidden" name="subscription_id" value="{{ $order->subscription->subscription_id }}">
                    <input type="hidden" name="user_id" value="{{ $order->user->userid }}">

                    <div class="form-group">
                        <label for="note">Follow-Up Note</label>
                        <textarea name="note" id="note" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach
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
  
    

@endsection
