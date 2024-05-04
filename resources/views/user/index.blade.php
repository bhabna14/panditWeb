@extends('user.layouts.front')

    @section('styles')

		<!-- INTERNAL Select2 css -->
		<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" />

		<!-- INTERNAL Data table css -->
		<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.css')}}" rel="stylesheet" />
		<link href="{{asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css')}}"  rel="stylesheet">
		<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />

    @endsection

    @section('content')

        <!-- Main Section -->
        <main class="main">
            <section class="section banner banner-section">
            <div class="container banner-column">
                <img class="banner-image" src="https://i.ibb.co/vB5LTFG/Headphone.png" alt="banner">
                <div class="banner-inner">
                    <h1 class="heading-xl">Experience Media Like Never Before</h1>
                    <p class="paragraph">
                        Enjoy award-winning stereo beats with wireless listening freedom and sleek,
                        streamlined with premium padded and delivering first-rate playback.
                    </p>
                    <button class="btn btn-darken btn-inline">
                        Our Products<i class="bx bx-right-arrow-alt"></i>
                    </button>
                </div>
                <div class="banner-links">
                    <a href="#" title=""><i class="bx bxl-facebook"></i></a>
                    <a href="#" title=""><i class="bx bxl-instagram"></i></a>
                    <a href="#" title=""><i class="bx bxl-twitter"></i></a>
                    <a href="#" title=""><i class="bx bxl-youtube"></i></a>
                </div>
            </div>
            </section>
        </main>
    @endsection

    @section('scripts')

		<!-- Internal Chart.Bundle js-->
		<script src="{{asset('assets/plugins/chartjs/Chart.bundle.min.js')}}"></script>

		<!-- Moment js -->
		<script src="{{asset('assets/plugins/raphael/raphael.min.js')}}"></script>

		<!-- INTERNAL Apexchart js -->
		<script src="{{asset('assets/js/apexcharts.js')}}"></script>

		<!--Internal Sparkline js -->
		<script src="{{asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>

		<!--Internal  index js -->
		<script src="{{asset('assets/js/index.js')}}"></script>

        <!-- Chart-circle js -->
		<script src="{{asset('assets/js/chart-circle.js')}}"></script>

		<!-- Internal Data tables -->
		<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
		<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
		<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
		<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>

		<!-- INTERNAL Select2 js -->
		<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
		<script src="{{asset('assets/js/select2.js')}}"></script>

    @endsection
