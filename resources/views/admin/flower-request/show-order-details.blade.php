@extends('admin.layouts.app')

@section('styles')
    <!-- Add any required styles -->
    <link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
    <link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .card-header {
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
      <style>
        .subscription-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            /* margin: 20px auto; */
            font-family: Arial, sans-serif;
        }
    
        .card-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            color: #333;
        }
    
        .details {
            line-height: 1.5;
        }
    
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eaeaea;
        }
    
        .info-row:last-child {
            border-bottom: none; /* Remove border for the last row */
        }
    
        .info-label {
            font-weight: bold;
            color: #555;
        }
    
        .info-value {
            color: #333;
        }
    
        .price-row {
            font-size: 1.2em;
            color: #2c3e50;
        }
    
        .divider {
            margin: 15px 0;
            border-top: 1px solid #eaeaea;
        }
    
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9em;
            color: #fff;
            text-align: center;
        }
    
        .status-running {
            background-color: #28a745; /* Green */
        }
    
        .status-expired {
            background-color: #dc3545; /* Red */
        }
        .note-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    padding: 10px;
    border-radius: 5px;
}

.text-warning {
    color: #dc3545 !important;
    font-weight: bold;
}

    </style>
@endsection

@section('content')

<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">Booking Details</span>
    </div>
    <div class="justify-content-center mt-2">
        <ol class="breadcrumb d-flex justify-content-between align-items-center">
            <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Booking Details</li>
        </ol>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-5">
            <div class="subscription-card">
                <div class="card-header">Order & Subscription Summary</div>
                <div class="details">
                    <div class="info-row">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">{{ $order->order_id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Product:</span>
                        <span class="info-value">{{ $order->flowerProduct->name }}</span>
                    </div>
                    <div class="info-row price-row">
                        <span class="info-label">Total Price:</span>
                        <span class="info-value">₹ {{ number_format($order->total_price, 2) }}</span>
                    </div>
            
                    <div class="divider"></div>
            
                    @if($order->subscription)
                        <div class="info-row">
                            <span class="info-label">Start Date:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($order->subscription->start_date)->format('d M, Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">End Date:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($order->subscription->end_date)->format('d M, Y') }}</span>
                        </div>
                          <!-- Check if subscription has been paused and resumed -->
                        @if($order->pauseResumeLogs->count() > 0)
                            <div class="info-row note-warning">
                                <span class="info-label">Note:</span>
                                <span class="info-value text-warning">
                                    You paused or resumed the subscription, so your new extended end date is 
                                    {{ \Carbon\Carbon::parse($order->subscription->new_date)->format('d M, Y') }}.
                                </span>
                            </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="status-badge 
                                {{ $order->subscription->status === 'active' ? 'status-running bg-success' : '' }}
                                {{ $order->subscription->status === 'paused' ? 'status-paused bg-warning' : '' }}
                                {{ $order->subscription->status === 'expired' ? 'status-expired bg-danger' : '' }}">
                                {{ ucfirst($order->subscription->status) }}
                            </span>
                        </div>
                        
                    @else
                        <div class="info-row">
                            <span class="info-label">Subscription:</span>
                            <span class="status-badge status-expired">No active subscription</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <!-- Displaying SubscriptionPauseResumeLog Data in a Table -->
            @if($order->pauseResumeLogs->count() > 0)
                <div class="card">
                    <div class="card-header">Subscription Pause/Resume Logs</div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Pause Start Date</th>
                                <th>Pause End Date</th>
                                <th>Resume Date</th>
                                <th>New End Date</th>
                                <th>Paused Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->pauseResumeLogs as $log)
                                <tr>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->pause_start_date)->format('d M, Y') }}</td>
                                    <td>{{ $log->pause_end_date ? \Carbon\Carbon::parse($log->pause_end_date)->format('d M, Y') : 'N/A'}}</td>
                                    <td>{{ $log->resume_date ? \Carbon\Carbon::parse($log->resume_date)->format('d M, Y') : 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->new_end_date)->format('d M, Y') }}</td>
                                    <td>{{ $log->paused_days }} days</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No pause/resume logs available.</p>
            @endif
        </div>
    </div>
    
    
  

   
</div>
@endsection

@section('scripts')
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
    <script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
@endsection