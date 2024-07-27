@extends('pandit.layouts.app')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">DASHBOARD</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sales</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->
	@if (session('success'))
    <div class="alert alert-success" id ="Message">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" id ="Message">
        {{ session('error') }}
    </div>
@endif

    <!-- row -->
    <div class="row">
        <!-- Bookings Section -->
		<div class="col-xl-7 col-lg-12 col-md-12 col-sm-12">
			<div class="row">
				<div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
					<div class="card">
						<div class="text-center pt-4">
							<h3 style="font-weight: bold; font-family: Copperplate, Papyrus, fantasy; font-size: 30px">
								{{ $today }}
							</h3>
							<h3 style="font-family: 'Trebuchet MS', sans-serif; font-size: 20px;" id="liveTime"></h3>
						</div>
						<div class="card-body">
							<div class="row">
								@foreach ($bookings as $booking)
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4">
										<div class="card text-center p-3">
											<h5 class="text-dark font-weight-semibold mb-2">
												{{ $booking->pooja_name }}
												({{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i') }})
											</h5>
											<div class="d-flex justify-content-center">
												@if ($booking->status)
													@if ($booking->status->start_time && !$booking->status->end_time)
														<!-- If started but not ended, show End button -->
														<form action="{{ route('pooja.end') }}" method="POST">
															@csrf
															<input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
															<input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
															<button type="submit" class="btn btn-success mb-2">End</button>
														</form>
													@elseif (!$booking->status->start_time)
														<!-- If not started, show Start button -->
														<form action="{{ route('pooja.start') }}" method="POST" class="mr-2">
															@csrf
															<input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
															<input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
															<button type="submit" class="btn btn-primary mb-2">Start</button>
														</form>
													@else
														<!-- If started and ended, show Completed button -->
														<button class="btn btn-secondary mb-2" disabled>Pooja Completed</button>
													@endif
												@else
													<!-- If no status record, show both Start and End buttons -->
													<form action="{{ route('pooja.start') }}" method="POST" class="mr-2">
														@csrf
														<input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
														<input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
														<button type="submit" class="btn btn-primary mb-2">Start</button>
													</form>
													<form action="{{ route('pooja.end') }}" method="POST">
														@csrf
														<input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
														<input type="hidden" name="pooja_id" value="{{ $booking->pooja_id }}">
														<button type="submit" class="btn btn-success mb-2" style="margin-left: 10px">End</button>
													</form>
												@endif
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
		
        <!-- Profile Section -->
        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-9 col-lg-7 col-md-6 col-sm-12">
                                    <div class="text-justified align-items-center">
                                        <h3 class="text-dark font-weight-semibold mb-2 mt-0">
                                            Hi, Welcome Back
                                            <span class="text-primary">
                                                {{ $profile->name ?? 'Pandit' }}
                                            </span>!
                                        </h3>
                                        <p class="text-dark tx-14 mb-3 lh-3">
                                            If you want to get request of pooja then you will first complete all profile
                                            details
                                        </p>
                                        <button class="btn btn-primary shadow">Upgrade Now</button>
                                    </div>
                                </div>
                                <div
                                    class="col-xl-3 col-lg-5 col-md-6 col-sm-12 d-flex align-items-center justify-content-center">
                                    <div class="chart-circle float-md-end mt-4 mt-md-0" data-value="0.30" data-thickness="8"
                                        data-color="">
                                        <canvas width="100" height="100"></canvas>
                                        <div class="chart-circle-value circle-style">
                                            <div class="tx-18 font-weight-semibold">35%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- </div> -->
    </div>
    <!-- row closed -->

    <!-- row  -->
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product Summary</h4>
                </div>
                <div class="card-body pt-0 example1-table">
                    <div class="table-responsive">
                        <table class="table  table-bordered text-nowrap mb-0" id="example1">
                            <thead>
                                <tr>
                                    <th>Slno</th>
                                    <th>Pooja Name</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Pooja Duration</th>
                                    <th>Pooja Status</th>
                                </tr>
                            </thead>
							<tbody>
								@foreach ($pooja_status as $index => $status)
								<tr>
									<td>{{ $index + 1 }}</td>
									<td>{{ $status->pooja_name }}</td>
									<td>{{ $status->start_time ? \Carbon\Carbon::parse($status->start_time)->format('Y-m-d H:i:s') : 'Not Started' }}</td>
									<td>{{ $status->end_time ? \Carbon\Carbon::parse($status->end_time)->format('Y-m-d H:i:s') : 'Not Ended' }}</td>
									<td>{{ $status->pooja_duration }}</td>
									<td>{{ $status->pooja_status }}</td>

								</tr>
								@endforeach
							</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /row closed -->
@endsection

@section('scripts')
    <script>
        function updateTime() {
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');
            var formattedTime = hours + ':' + minutes + ':' + seconds;
            document.getElementById('liveTime').innerText = formattedTime;
        }

        setInterval(updateTime, 1000); // Update every second
        updateTime(); // Initial call to set the time immediately
    </script>
    <!-- Internal Chart.Bundle js-->
    <script src="{{ asset('assets/plugins/chartjs/Chart.bundle.min.js') }}"></script>

    <!-- Moment js -->
    <script src="{{ asset('assets/plugins/raphael/raphael.min.js') }}"></script>

    <!-- INTERNAL Apexchart js -->
    <script src="{{ asset('assets/js/apexcharts.js') }}"></script>

    <!--Internal Sparkline js -->
    <script src="{{ asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

    <!--Internal  index js -->
    <script src="{{ asset('assets/js/index.js') }}"></script>

    <!-- Chart-circle js -->
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script>

    <!-- Internal Data tables -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>

	<script>
        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
        setTimeout(function() {
            document.getElementById('Messages').style.display = 'none';
        }, 3000);
    </script>
	
@endsection
