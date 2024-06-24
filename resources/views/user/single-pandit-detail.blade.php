@extends('user.layouts.front')

@section('styles')
@endsection

@section('content')
<section class="pandit-single-profile">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="pandit-profile">
                    <img src="{{ asset($single_pandit->profile_photo) }}" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="pandit-desc">
                    <h5>{{ $single_pandit->name }}</h5>
                    <p>{{ $single_pandit->about_pandit }}</p>
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
                            <div class="col-md-4 pandit-card">
                                <div class="card" data-state="#pooja">
                                    <div class="card-header">
                                        <img class="card-pooja" src="{{ asset('assets/img/'.$pandit_puja->poojalist->pooja_photo) }}" alt="image">
                                    </div>
                                    <div class="pooja-head row">
                                        <div class="col-md-12 col-12">
                                            <h5>{{ $pandit_puja->poojalist->pooja_name }}</h5>
                                            <p class="short-desc">{{ $pandit_puja->poojalist->short_description }}</p>
                                            
                                            <p class="total-fee">Total Fee : ₹{{ $pandit_puja->pooja_fee }}</p>
                                            <p class="total-fee">Advance Fee : ₹{{ $advancefee = $pandit_puja->pooja_fee * 20/100 }}</p>
                                            <p>Total Time : {{ $pandit_puja->pooja_duration }} hrs</p>
                                            <a href="{{ Auth::guard('users')->check() ? route('book.now', ['panditSlug' => $single_pandit->slug, 'poojaSlug' => $pandit_puja->poojalist->slug, 'poojaFee' => $pandit_puja->pooja_fee]) : route('userlogin') }}" class="button -md -blue-1 bg-dark-3 text-white mt-10">
                                                {{ Auth::guard('users')->check() ? 'Book Now' : 'Login to Book' }}
                                            </a>
                                            
                                        </div>
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
</section>
@endsection

@section('scripts')
@endsection
