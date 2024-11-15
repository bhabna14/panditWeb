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
                                        <table id="file-datatable" class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                <th class="border-bottom-0">#</th>
                                                <th class="border-bottom-0">Booking Id</th>
                                                    <th class="border-bottom-0">Pandit Name</th>
                                                    <th class="border-bottom-0">Booking Date</th>
                                                    <th class="border-bottom-0">Total Payment</th>
                                                    <th class="border-bottom-0">Paid Amount</th>
                                                   
                                                    {{-- <th class="border-bottom-0">Application Status</th> --}}
                                                    <th class="border-bottom-0">Payment Status</th>
                                                    <th class="border-bottom-0">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                           
                                                @foreach ($bookings as $index => $booking)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                      
                                                        <td>   <a href="{{url('admin/booking/'. $booking->id)}}">{{ $booking->booking_id }} </a></td>
                                                        
                                                      
                                                        <td class="tb-col">
                                                            <a href="{{url('admin/booking/'. $booking->id)}}" class="title">
                                                            <div class="media-group">
                                                                <div class="media media-md media-middle media-circle">
                                                                        <img src="{{asset('assets/img/user.jpg') }}" alt="user">
                                                                </div>
                                                                <div class="media-text">
                                                                    <span class="title">{{ $booking->pandit->title }} {{ $booking->pandit->name }}</span>
                                                                    <h6 class="title">{{ $booking->pooja->pooja_name ?? "N/A" }}</h6>
                                                                </div>
                                                            </div>
                                                             </a>
                                                        </td>
                                                      
                                                        
                                                    <td>{{ $booking->booking_date }}</td>
                                                    <td>{{ $booking->pooja_fee }}</td>
                                                    <td>
                                                       @if($booking->payment_status == "paid")
                                                            @if($booking->payment->payment_type == "full")
                                                            <h6 class="title">{{ $booking->payment->paid }} <br>(Full paid with 5% discount)</h6>
                                                            @else
                                                            <h6 class="title">{{ $booking->payment->paid }} <br>(Advanced paid 20%)</h6>
                                                            @endif
                                                        @elseif($booking->payment_status == "refundprocess")
                                                          <h6>Refund On Process</h6>
                                                        @elseif($booking->payment_status == "refundcompleted")
                                                            <h6>Refund On Completed</h6>
                                                        @else
                                                        <h6>Not Yet Paid</h6>
                                                        @endif


                                                    </td>
                                                   
                                                        {{-- <td>
                                                                <span class="badge badge-success">{{ $booking->application_status }}</span> 
                                                        
                                                        </td> --}}
                                                        <td>
                                                            <span class="badge badge-success">{{ $booking->payment_status }}</span> 
                                                    
                                                         </td>
                                                        
                                                        <td>
                                                            <a href="{{url('admin/booking/'. $booking->id)}}"><i class="fas fa-eye"></i></a> | 
                                                           
                                                            <form action="{{ route('admin.booking.delete', $booking->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure to delete?');">
                                                                @csrf
                                                                @method('DELETE') <!-- This will be used to spoof the DELETE method -->
                                                                <button type="submit" style="border: none; background: none; color: red; cursor: pointer;">
                                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                                </button>
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

@endsection
