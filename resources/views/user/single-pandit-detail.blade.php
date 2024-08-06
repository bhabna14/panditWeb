@extends('user.layouts.front')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
        padding-top: 60px;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    </style>
    
@endsection

@section('content')
<section class="pandit-single-profile">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="pandit-profile">
                    <img src="{{ asset($single_pandit->profile_photo) }}" alt="">
                </div>
            </div>
            <div class="col-md-8">
                <div class="pandit-desc">
                    <h5>{{ $single_pandit->name }}</h5>
                    <p>{{ $single_pandit->about_pandit }}</p>
                    <h6 class="pt-10">Languages Known : {{ $single_pandit->language }}</h6>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="tabs -underline-2 pt-20 lg:pt-40 sm:pt-30 js-tabs">
                <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">
                    <div class="col-auto">
                        <button class="tabs__button text-light-1 fw-500 px-5 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">List Of Poojas</button>
                    </div>
                </div>
                <div class="tabs__content js-tabs-content">
                    <div class="tabs__pane -tab-item-1 is-tab-el-active">
                        <div class="row" data-aos="fade-up">
                           

                            @foreach ($pandit_pujas as $pandit_puja)
                            @if($pandit_puja->poojalist)
                            <div class="col-md-4 pandit-card">
                                <div class="card" data-state="#pooja">
                                    <div class="card-header">
                                        <img class="card-pooja" src="{{ $pandit_puja->poojalist->pooja_photo ? asset('assets/img/' . $pandit_puja->poojalist->pooja_photo) : asset('assets/img/default-image.jpg') }}" alt="image">

                                    </div>
                                    <div class="pooja-head row">
                                        <div class="col-md-12 col-12">
                                            <h5>{{ $pandit_puja->poojalist->pooja_name }}</h5>
                                            <p class="short-desc">{{ $pandit_puja->poojalist->short_description }}</p>
                                            
                                            <p class="total-fee">Total Fee : ₹{{ sprintf('%.2f', $pandit_puja->pooja_fee) }}</p>
                                            <p class="total-fee">Advance Fee : ₹{{ sprintf('%.2f',$advancefee = $pandit_puja->pooja_fee * 20/100) }}</p>
                                            <p>Total Time : {{ $pandit_puja->pooja_duration }}</p>
                                            {{-- <a href="{{ Auth::guard('users')->check() ? route('book.now', ['panditSlug' => $single_pandit->slug, 'poojaSlug' => $pandit_puja->poojalist->slug, 'poojaFee' => $pandit_puja->pooja_fee]) : route('userlogin') }}" class="button -md -blue-1 bg-dark-3 text-white mt-10">
                                                {{ Auth::guard('users')->check() ? 'Book Now' : 'Login to Book' }}
                                            </a> --}}
                                            {{-- <a href="{{ Auth::guard('users')->check() ? route('book.now', ['panditSlug' => $single_pandit->slug, 'poojaSlug' => $pandit_puja->poojalist->slug, 'poojaFee' => $pandit_puja->pooja_fee]) : '#' }}" class="button -md -blue-1 bg-dark-3 text-white mt-10" onclick="openModal('{{ Auth::guard('users')->check() ? '' : 'loginModal' }}')">
                                                {{ Auth::guard('users')->check() ? 'Book Now' : 'Login to Book' }}
                                            </a> --}}

                                            <a href="{{ Auth::guard('users')->check() ? route('book.now', ['panditSlug' => $single_pandit->slug, 'poojaSlug' => $pandit_puja->poojalist->slug, 'poojaFee' => $pandit_puja->pooja_fee]) : route('userlogin', ['referer' => urlencode(url()->current())]) }}" class="button -md -blue-1 bg-dark-3 text-white mt-10">
                                                {{ Auth::guard('users')->check() ? 'Book Now' : 'Login to Book' }}
                                            </a>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                        
                            @endif
                            @endforeach
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('loginModal')">&times;</span>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form id="loginForm" action="{{ route('user.login') }}" method="POST">
            @csrf
            <input type="hidden" class="" id="otp" name="otp" value="{{ rand(1000, 9999) }}">
            <input type="hidden" class="form-control" id="userid" name="userid" value="USER{{ rand(9, 99999) }}">
            <div id="step1">
                <div class="form-group">
                    <label for="mobile_no">Phone Number</label>
                    <div style="display: flex; align-items: center;">
                        <input type="text" class="" id="country_code" name="country_code" value="+91" readonly style="background-color: #f1f1f1; width: 60px; text-align: center;">
                        <input type="number" class="form-control" id="phonenumber" name="phonenumber" placeholder="Enter your phone number" style="margin-left: 5px; flex: 1;">
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="Generate OTP">
            </div>
        </form>
    </div>
</div>

<!-- OTP Modal -->
<div id="otpModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('otpModal')">&times;</span>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <form id="otpForm" action="{{ route('check.userotp') }}" method="POST">
            @csrf
            <div id="step1">
                <div class="form-group">
                    <label for="otp">OTP</label>
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter your OTP">
                </div>
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "block";
    }
    
    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }
    
    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        var loginModal = document.getElementById('loginModal');
        var otpModal = document.getElementById('otpModal');
        if (event.target == loginModal) {
            loginModal.style.display = "none";
        }
        if (event.target == otpModal) {
            otpModal.style.display = "none";
        }
    }
    </script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            var form = event.target;
            
            fetch(form.action, {
                method: form.method,
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('loginModal');
                    openModal('otpModal');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
        </script>
        
@endsection
