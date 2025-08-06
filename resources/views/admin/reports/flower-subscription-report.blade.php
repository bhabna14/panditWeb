 @extends('admin.layouts.apps')

 @section('styles')

     <input type="hidden" name="_token" value="{{ csrf_token() }}">

     <!-- Data table css -->
     <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
     <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
     <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
 @endsection

 @section('content')
     <!-- breadcrumb -->
     <div class="breadcrumb-header justify-content-between">
         <div class="left-content">
             <span class="main-content-title mg-b-0 mg-b-lg-1">Subscription Report</span>
         </div>
         <div class="justify-content-center mt-2">
             <ol class="breadcrumb d-flex justify-content-between align-items-center">
                 <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Report</a></li>
             </ol>
         </div>
     </div>

     <div class="row mb-4">
         <div class="col-md-3">
             <label for="from_date" class="form-label">From Date</label>
             <input type="date" id="from_date" name="from_date" class="form-control">
         </div>
         <div class="col-md-3">
             <label for="to_date" class="form-label">To Date</label>
             <input type="date" id="to_date" name="to_date" class="form-control">
         </div>
         <div class="col-md-3 d-flex align-items-end">
             <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
         </div>
     </div>

     <div class="table-responsive">
         <table id="file-datatable" class="table table-bordered w-100">
             <thead>
                 <tr>
                     <th>Customer Details</th>
                     <th>Purchase Date</th>
                     <th>Duration</th>
                     <th>Price</th>
                     <th>Status</th>
                 </tr>
             </thead>
             <tbody></tbody>
         </table>
     </div>

 @section('scripts')
     <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
     <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#file-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('subscription.report') }}",
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                { data: 'customer_details', name: 'customer_details' },
                { data: 'purchase_date', name: 'start_date' },
                { data: 'duration', name: 'duration' },
                { data: 'price', name: 'price' },
                { data: 'status', name: 'status' },
            ]
        });

        $('#searchBtn').click(function () {
            table.ajax.reload();
        });
    });
</script>
 @endsection
