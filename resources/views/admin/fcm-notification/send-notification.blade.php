@extends('admin.layouts.app')

@section('styles')
    <!-- Internal Select2 css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
    
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">Send Notification</span>
        </div>
        
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
               
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Send Notification</li>
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
                            <a class="nav-link mb-2 mt-2 {{ Request::is('admin/send-notification') ? 'active' : '' }}" href="{{ route('admin.notification.create') }}"
                                onclick="changeColor(this)">App Notification</a>
                            <a class="nav-link mb-2 mt-2" href="{{ route('admin.whatsapp-notification.create') }}"
                                onclick="changeColor(this)">Whatsapp Notification</a>
                           
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger" id="Message">
            {{ session()->get('error') }}
        </div>
    @endif
    
    <form action="{{ route('admin.notification.send') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image (optional)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>

    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifications as $notification)
                <tr>
                    <td>{{ $notification->id }}</td>
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->description }}</td>
                    <td>
                        @if($notification->image)
                            <img src="{{ asset('storage/' . $notification->image) }}" alt="Notification Image" width="50">
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ ucfirst($notification->status) }}</td>
                    <td>{{ $notification->created_at }}</td>
                    <td>
                        <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                        <form id="resend-form-{{ $notification->id }}" action="{{ route('admin.notifications.resend', $notification->id) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <button class="btn btn-primary btn-sm" onclick="resendNotification({{ $notification->id }})">Resend</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
   
    
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

  
    <!-- jQuery UI library for datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- jQuery Timepicker plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function resendNotification(notificationId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to resend this notification?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('resend-form-' + notificationId).submit();
                }
            });
        }
    </script>
    
  
    
@endsection
