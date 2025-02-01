@extends('admin.layouts.app')

@section('styles')
    <style>
        /* General Card and Layout Styling */
        .cards {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: #fff;
            margin-bottom: 30px;
        }
       /* Card Header Styling */
.card-header {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); /* Gradient background */
    color: #fff;
    padding: 25px;
    border-radius: 8px 8px 0 0;
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.header-text {
    position: relative;
    z-index: 1;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3); /* Text shadow for a 3D effect */
    letter-spacing: 1px;
}

.card-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 8px 8px 0 0;
    z-index: 0;
}

        .card-body {
            padding: 25px;
        }

        /* Form Section Styling */
        .form-section {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .form-section h4 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #5A9BEF;
            padding-bottom: 10px;
        }

        /* Input and Button Styling */
        .form-control {
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 12px;
            font-size: 15px;
        }
        .form-control:focus {
            border-color: #5A9BEF;
            box-shadow: 0 0 5px rgba(90, 155, 239, 0.5);
        }

        .btn-primary, .btn-success {
            background-color: #5A9BEF;
            border: none;
            color: #fff;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover, .btn-success:hover {
            background-color: #4A8AE0;
            cursor: pointer;
        }

        .btn-secondary {
            background-color: #ddd;
            color: #333;
        }

        /* Modal Footer Styling */
        .modal-footer {
            padding: 10px;
            display: flex;
            justify-content: space-between;
        }

        /* Breadcrumb Styling */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }
        .breadcrumb-item a {
            color: #5A9BEF;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #666;
        }

        /* Responsiveness for smaller screens */
        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .form-section h4 {
                font-size: 18px;
            }
        }
        

    </style>
@endsection

@section('content')


    <div class="cards">

        <div class="card-header">
            {{ $action === 'pause' ? 'Pause Subscription' : 'Resume Subscription' }}
        </div>

        <!-- Success/Error Message -->
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #09fe11; color: rgb(21, 224, 153);text-align: center;color: black">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" style="background-color: #ee1708; color: rgb(255, 159, 159); text-align: center;">
                {{ session('error') }}
            </div>
        @endif
      
        <div class="card-body">
            @if ($action === 'pause')
            <form id="pauseForm" action="{{ route('subscription.pause', $order->order_id) }}" method="POST">
                @csrf
                <div class="form-section">
                    <h4>Pause Details</h4>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="pause_start_date" class="form-label">Pause Start Date</label>
                                <input 
                                    type="date" 
                                    id="pause_start_date" 
                                    name="pause_start_date" 
                                    class="form-control" 
                                    required 
                                    min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" 
                                    onchange="updateEndDate()"
                                >
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="pause_end_date" class="form-label">Pause End Date</label>
                                <input 
                                    type="date" 
                                    id="pause_end_date" 
                                    name="pause_end_date" 
                                    class="form-control" 
                                    required 
                                    min="{{ date('Y-m-d') }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top: 35px;">
                            <button type="submit" class="btn btn-primary">Pause</button>
                        </div>
                    </div>
                </div>
            </form>
            @if (!is_null($order->pause_start_date) && !is_null($order->pause_end_date))
                <div class="alert alert-danger mt-3">
                    <p>
                        <strong>Pause Period:</strong> 
                        {{ \Carbon\Carbon::parse($order->pause_start_date)->format('d M Y') }} 
                        to 
                        {{ \Carbon\Carbon::parse($order->pause_end_date)->format('d M Y') }}
                    </p>
                </div>
            @endif
        @elseif ($action === 'resume')
            <form id="resumeForm" action="{{ route('subscription.resume', $order->id) }}" method="POST">
                @csrf
                <div class="form-section">
                    <h4>Resume Details</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="resume_date" class="form-label">Resume Date</label>
                                <input 
                                    type="date" 
                                    id="resume_date" 
                                    name="resume_date" 
                                    class="form-control" 
                                    required 
                                    min="{{ $order->pause_start_date ?? '' }}" 
                                    max="{{ $order->pause_end_date ?? '' }}"
                                >
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-top: 35px;">
                            <button type="submit" class="btn btn-success">Resume</button>
                        </div>
                    </div>
                </div>
            </form>
            @if (!is_null($order->pause_start_date) && !is_null($order->pause_end_date))
                <div class="alert alert-danger mt-3">
                    <p>
                        <strong>Pause Period:</strong> 
                        {{ \Carbon\Carbon::parse($order->pause_start_date)->format('d M Y') }} 
                        to 
                        {{ \Carbon\Carbon::parse($order->pause_end_date)->format('d M Y') }}
                    </p>
                </div>
            @endif
        @endif
        
        </div>
    </div>

@endsection

@section('scripts')

 
<script>
    // JavaScript to dynamically set the min date to tomorrow and disable previous dates
    document.addEventListener('DOMContentLoaded', function() {
        var pauseStartDateInput = document.getElementById('pause_start_date');
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1); // Set tomorrow's date
        
        // Format the date to yyyy-mm-dd
        var dd = String(tomorrow.getDate()).padStart(2, '0');
        var mm = String(tomorrow.getMonth() + 1).padStart(2, '0'); // January is 0!
        var yyyy = tomorrow.getFullYear();
        var tomorrowFormatted = yyyy + '-' + mm + '-' + dd;

        // Set the min and max date for the input
        pauseStartDateInput.setAttribute('min', tomorrowFormatted);
    });
</script>
    <script>
        function updateEndDate() {
            const startDate = document.getElementById('pause_start_date').value;
            const endDate = document.getElementById('pause_end_date');

            if (startDate) {
                endDate.min = startDate; // Set the minimum date for the end date
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resumeDate = document.getElementById('resume_date');

            // Ensure pause_start_date and pause_end_date are available in the backend
            const pauseStartDate = "{{ $order->pause_start_date ?? '' }}";
            const pauseEndDate = "{{ $order->pause_end_date ?? '' }}";

            if (pauseStartDate) {
                resumeDate.setAttribute('min', pauseStartDate);
            }

            if (pauseEndDate) {
                resumeDate.setAttribute('max', pauseEndDate);
            }
        });
    </script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
